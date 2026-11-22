<?php

namespace Tests\Feature;

use App\Models\Carrier;
use App\Models\Location;
use App\Models\Shipment;
use App\Models\ShipmentException;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ShipmentApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $operator;
    protected User $customer;
    protected Carrier $carrier;
    protected Location $origin;
    protected Location $destination;

    protected function setUp(): void
    {
        parent::setUp();

        $this->operator = User::factory()->create(['role' => 'operator']);
        $this->customer = User::factory()->create(['role' => 'customer']);

        $this->carrier = Carrier::create([
            'code' => 'FST',
            'name' => 'FastShip',
            'service_type' => 'Ocean & Air',
        ]);

        $this->origin = Location::create([
            'code' => 'USLAX',
            'name' => 'Port of Los Angeles',
            'city' => 'Los Angeles',
            'country' => 'USA',
            'latitude' => 33.74,
            'longitude' => -118.26,
        ]);

        $this->destination = Location::create([
            'code' => 'USORD',
            'name' => 'Chicago Hub',
            'city' => 'Chicago',
            'country' => 'USA',
            'latitude' => 41.87,
            'longitude' => -87.62,
        ]);
    }

    public function test_api_can_list_shipments_with_pagination(): void
    {
        Sanctum::actingAs($this->operator);

        Shipment::create([
            'shipment_number' => 'SHP-API-001',
            'customer_id' => $this->customer->id,
            'carrier_id' => $this->carrier->id,
            'origin_id' => $this->origin->id,
            'destination_id' => $this->destination->id,
            'cargo_type' => 'Goods',
            'total_weight' => 1000,
            'total_volume' => 10,
            'expected_departure' => Carbon::now(),
            'expected_arrival' => Carbon::now()->addDays(3),
            'estimated_arrival' => Carbon::now()->addDays(3),
            'current_status' => 'IN_TRANSIT',
        ]);

        $response = $this->getJson('/api/shipments');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data',
                'meta' => ['current_page', 'last_page', 'per_page', 'total'],
            ]);
    }

    public function test_api_can_create_shipment_by_operator(): void
    {
        Sanctum::actingAs($this->operator);

        $payload = [
            'customer_id' => $this->customer->id,
            'carrier_id' => $this->carrier->id,
            'origin_id' => $this->origin->id,
            'destination_id' => $this->destination->id,
            'cargo_type' => 'Consumer Tech Gear',
            'total_weight' => 2500.50,
            'total_volume' => 12.00,
            'expected_departure' => Carbon::now()->addDay()->toDateTimeString(),
            'expected_arrival' => Carbon::now()->addDays(5)->toDateTimeString(),
            'estimated_arrival' => Carbon::now()->addDays(5)->toDateTimeString(),
        ];

        $response = $this->postJson('/api/shipments', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.cargo_type', 'Consumer Tech Gear');
    }

    public function test_api_can_add_tracking_event(): void
    {
        Sanctum::actingAs($this->operator);

        $shipment = Shipment::create([
            'shipment_number' => 'SHP-API-TRK',
            'customer_id' => $this->customer->id,
            'carrier_id' => $this->carrier->id,
            'origin_id' => $this->origin->id,
            'destination_id' => $this->destination->id,
            'cargo_type' => 'Freight',
            'total_weight' => 500,
            'total_volume' => 5,
            'expected_departure' => Carbon::now(),
            'expected_arrival' => Carbon::now()->addDays(3),
            'estimated_arrival' => Carbon::now()->addDays(3),
            'current_status' => 'IN_TRANSIT',
        ]);

        $response = $this->postJson("/api/shipments/{$shipment->id}/tracking", [
            'event_type' => 'CHECKPOINT',
            'status' => 'IN_TRANSIT',
            'latitude' => 35.12,
            'longitude' => -105.44,
            'description' => 'Container scanned at intermodal junction depot.',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.description', 'Container scanned at intermodal junction depot.');
    }

    public function test_api_can_create_and_patch_exception(): void
    {
        Sanctum::actingAs($this->operator);

        $shipment = Shipment::create([
            'shipment_number' => 'SHP-API-EX',
            'customer_id' => $this->customer->id,
            'carrier_id' => $this->carrier->id,
            'origin_id' => $this->origin->id,
            'destination_id' => $this->destination->id,
            'cargo_type' => 'Goods',
            'total_weight' => 500,
            'total_volume' => 5,
            'expected_departure' => Carbon::now(),
            'expected_arrival' => Carbon::now()->addDays(3),
            'estimated_arrival' => Carbon::now()->addDays(3),
            'current_status' => 'IN_TRANSIT',
        ]);

        // Create exception
        $createRes = $this->postJson("/api/shipments/{$shipment->id}/exceptions", [
            'exception_type' => 'CUSTOMS_HOLD',
            'severity' => 'HIGH',
            'description' => 'Regulatory documents awaiting review.',
        ]);

        $createRes->assertStatus(201)
            ->assertJsonPath('data.exception_type', 'CUSTOMS_HOLD');

        $exceptionId = $createRes->json('data.id');

        // Patch exception status to RESOLVED
        $patchRes = $this->patchJson("/api/exceptions/{$exceptionId}", [
            'status' => 'RESOLVED',
            'resolution_notes' => 'Customs release verified by duty officer.',
        ]);

        $patchRes->assertStatus(200)
            ->assertJsonPath('data.status', 'RESOLVED')
            ->assertJsonPath('data.resolution_notes', 'Customs release verified by duty officer.');
    }
}
