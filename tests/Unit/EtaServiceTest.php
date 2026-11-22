<?php

namespace Tests\Unit;

use App\Services\EtaService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EtaServiceTest extends TestCase
{
    use RefreshDatabase;
    public function test_evaluates_on_schedule_when_eta_is_before_expected_arrival(): void
    {
        $service = new EtaService();
        $expected = Carbon::now()->addDays(5);
        $estimated = Carbon::now()->addDays(4);

        $result = $service->evaluateDelay($expected, $estimated);

        $this->assertFalse($result['is_delayed']);
        $this->assertEquals(0.0, $result['delay_hours']);
        $this->assertEquals('NONE', $result['delay_severity']);
    }

    public function test_calculates_low_severity_delay_under_six_hours(): void
    {
        $service = new EtaService();
        $expected = Carbon::now();
        $estimated = Carbon::now()->addHours(4);

        $result = $service->evaluateDelay($expected, $estimated);

        $this->assertTrue($result['is_delayed']);
        $this->assertEquals(4.0, $result['delay_hours']);
        $this->assertEquals('LOW', $result['delay_severity']);
    }

    public function test_calculates_medium_severity_delay_between_six_and_twenty_four_hours(): void
    {
        $service = new EtaService();
        $expected = Carbon::now();
        $estimated = Carbon::now()->addHours(14);

        $result = $service->evaluateDelay($expected, $estimated);

        $this->assertTrue($result['is_delayed']);
        $this->assertEquals(14.0, $result['delay_hours']);
        $this->assertEquals('MEDIUM', $result['delay_severity']);
    }

    public function test_calculates_high_severity_delay_over_twenty_four_hours(): void
    {
        $service = new EtaService();
        $expected = Carbon::now();
        $estimated = Carbon::now()->addHours(36);

        $result = $service->evaluateDelay($expected, $estimated);

        $this->assertTrue($result['is_delayed']);
        $this->assertEquals(36.0, $result['delay_hours']);
        $this->assertEquals('HIGH', $result['delay_severity']);
    }

    public function test_group_a_scenario_threshold(): void
    {
        $service = new EtaService();
        $expected = Carbon::now();
        // In Group A, threshold is 12h: >12h is HIGH
        $estimated = Carbon::now()->addHours(13);

        $result = $service->evaluateDelay($expected, $estimated, 'Group A');

        $this->assertTrue($result['is_delayed']);
        $this->assertEquals('HIGH', $result['delay_severity']);
    }
}
