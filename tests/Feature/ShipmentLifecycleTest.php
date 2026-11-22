<?php

namespace Tests\Feature;

use App\Models\Carrier;
use App\Models\Location;
use App\Models\Shipment;
use App\Models\User;
use App\Services\ShipmentService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class ShipmentLifecycleTest extends TestCase
{
    use RefreshDatabase;

    protected User $operator;
    protected Carrier $carrier;
    protected Location $origin;
    protected Location $destination;

    protected function setUp(): void
    {
        parent::setUp();

        $this->operator = User::factory()->create(['role' => 'operator']);
        $this->carrier = Carrier::create(['code' => 'FST', 'name' => 'FastShip', 'service_type' => 'Ocean & Air']);
        $this->origin = Location::create(['code' => 'USLAX', 'name' => 'Los Angeles', 'city' => 'Los Angeles', 'country' => 'USA', 'latitude' => 33.74, 'longitude' => -118.26]);
        $this->destination = Location::create(['code' => 'USORD', 'name' => 'Chicago', 'city' => 'Chicago', 'country' => 'USA', 'latitude' => 41.87, 'longitude' => -87.62]);
    }

    public function test_shipment_progresses_through_valid_lifecycle_stages(): void
    {
        $service = app(ShipmentService::class);

        $shipment = $service->createShipment([
            'customer_id' => $this->operator->id,
            'carrier_id' => $this->carrier->id,
            'origin_id' => $this->origin->id,
            'destination_id' => $this->destination->id,
            'cargo_type' => 'Industrial Motors',
            'total_weight' => 2000,
            'total_volume' => 10,
            'expected_departure' => Carbon::now()->addDays(1),
            'expected_arrival' => Carbon::now()->addDays(5),
            'estimated_arrival' => Carbon::now()->addDays(5),
        ], $this->operator);

        $this->assertEquals('CREATED', $shipment->current_status);

        // Advance CREATED -> BOOKED
        $shipment = $service->transitionStatus($shipment, 'BOOKED', $this->operator);
        $this->assertEquals('BOOKED', $shipment->current_status);

        // Advance BOOKED -> PICKED_UP
        $shipment = $service->transitionStatus($shipment, 'PICKED_UP', $this->operator);
        $this->assertEquals('PICKED_UP', $shipment->current_status);
        $this->assertNotNull($shipment->actual_departure);

        // Advance PICKED_UP -> IN_TRANSIT
        $shipment = $service->transitionStatus($shipment, 'IN_TRANSIT', $this->operator);
        $this->assertEquals('IN_TRANSIT', $shipment->current_status);

        // Advance IN_TRANSIT -> DELIVERED (Terminal)
        $shipment = $service->transitionStatus($shipment, 'DELIVERED', $this->operator);
        $this->assertEquals('DELIVERED', $shipment->current_status);
        $this->assertNotNull($shipment->actual_arrival);
    }

    public function test_illegal_status_jump_is_rejected_by_business_rules(): void
    {
        $this->expectException(ValidationException::class);

        $service = app(ShipmentService::class);
        $shipment = Shipment::create([
            'shipment_number' => 'SHP-TEST-001',
            'customer_id' => $this->operator->id,
            'carrier_id' => $this->carrier->id,
            'origin_id' => $this->origin->id,
            'destination_id' => $this->destination->id,
            'cargo_type' => 'Apparel',
            'total_weight' => 500,
            'total_volume' => 5,
            'expected_departure' => Carbon::now(),
            'expected_arrival' => Carbon::now()->addDays(3),
            'estimated_arrival' => Carbon::now()->addDays(3),
            'current_status' => 'CREATED',
        ]);

        // Trying to skip directly from CREATED to DELIVERED must fail
        $service->transitionStatus($shipment, 'DELIVERED', $this->operator);
    }

    public function test_delivered_terminal_status_cannot_revert_backwards(): void
    {
        $this->expectException(ValidationException::class);

        $service = app(ShipmentService::class);
        $shipment = Shipment::create([
            'shipment_number' => 'SHP-TEST-002',
            'customer_id' => $this->operator->id,
            'carrier_id' => $this->carrier->id,
            'origin_id' => $this->origin->id,
            'destination_id' => $this->destination->id,
            'cargo_type' => 'Apparel',
            'total_weight' => 500,
            'total_volume' => 5,
            'expected_departure' => Carbon::now()->subDays(5),
            'expected_arrival' => Carbon::now()->subDays(1),
            'estimated_arrival' => Carbon::now()->subDays(1),
            'current_status' => 'DELIVERED',
        ]);

        // Terminal status reversal must fail
        $service->transitionStatus($shipment, 'IN_TRANSIT', $this->operator);
    }
}
