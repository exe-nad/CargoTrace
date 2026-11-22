<?php

namespace App\Services;

use App\Models\Shipment;
use App\Models\SystemSetting;
use Carbon\Carbon;

class EtaService
{
    /**
     * Calculate delay hours and severity based on expected arrival and estimated arrival.
     *
     * @return array{is_delayed: bool, delay_hours: float, delay_severity: string, delay_reason: string}
     */
    public function evaluateDelay(Carbon|string $expectedArrival, Carbon|string $estimatedArrival, ?string $groupScenario = null): array
    {
        $expected = $expectedArrival instanceof Carbon ? $expectedArrival : Carbon::parse($expectedArrival);
        $estimated = $estimatedArrival instanceof Carbon ? $estimatedArrival : Carbon::parse($estimatedArrival);

        if ($estimated->lessThanOrEqualTo($expected)) {
            return [
                'is_delayed' => false,
                'delay_hours' => 0.0,
                'delay_severity' => 'NONE',
                'delay_reason' => 'Shipment is on track or early.',
            ];
        }

        // Difference in hours as a float
        $diffMinutes = $expected->diffInMinutes($estimated, false);
        $delayHours = round(max(0, $diffMinutes / 60), 2);

        // Fetch thresholds from settings or fallback to standard rules
        $thresholdLow = (float) SystemSetting::get('delay_threshold_low', 6.0);
        $thresholdMedium = (float) SystemSetting::get('delay_threshold_medium', 24.0);

        // Group-specific scenarios as required by Slide 8, 20 & 23
        if ($groupScenario === 'Group A') {
            // Group A threshold: 12h
            $severity = $delayHours > 12.0 ? 'HIGH' : ($delayHours > 4.0 ? 'MEDIUM' : 'LOW');
        } elseif ($groupScenario === 'Group B') {
            // Group B threshold: 18h
            $severity = $delayHours > 18.0 ? 'HIGH' : ($delayHours > 6.0 ? 'MEDIUM' : 'LOW');
        } elseif ($groupScenario === 'Group C') {
            // Group C threshold: 24h
            $severity = $delayHours > 24.0 ? 'HIGH' : ($delayHours > 8.0 ? 'MEDIUM' : 'LOW');
        } else {
            // Standard rule: 0-6h LOW, >6-24h MEDIUM, >24h HIGH
            if ($delayHours <= $thresholdLow) {
                $severity = 'LOW';
            } elseif ($delayHours <= $thresholdMedium) {
                $severity = 'MEDIUM';
            } else {
                $severity = 'HIGH';
            }
        }

        return [
            'is_delayed' => true,
            'delay_hours' => $delayHours,
            'delay_severity' => $severity,
            'delay_reason' => sprintf('ETA exceeds expected arrival by %.1f hours (Severity: %s).', $delayHours, $severity),
        ];
    }

    /**
     * Recalculate and update delay attributes for a shipment.
     */
    public function recalculateShipmentDelay(Shipment $shipment): Shipment
    {
        $evaluation = $this->evaluateDelay(
            $shipment->expected_arrival,
            $shipment->estimated_arrival,
            $shipment->group_scenario
        );

        $shipment->is_delayed = $evaluation['is_delayed'];
        $shipment->delay_hours = $evaluation['delay_hours'];
        $shipment->delay_severity = $evaluation['delay_severity'];
        $shipment->save();

        return $shipment;
    }
}
