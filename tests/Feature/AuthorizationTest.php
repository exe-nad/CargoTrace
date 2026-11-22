<?php

namespace Tests\Feature;

use App\Models\Carrier;
use App\Models\Location;
use App\Models\Shipment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_cannot_view_another_customers_shipment(): void
    {
        $customerA = User::factory()->create(['role' => 'customer']);
        $customerB = User::factory()->create(['role' => 'customer']);

        $carrier = Carrier::create(['code' => 'CAR1', 'name' => 'FastShip']);
        $origin = Location::create(['code' => 'L1', 'name' => 'Port A', 'city' => 'City A', 'country' => 'USA', 'latitude' => 10, 'longitude' => 20]);
        $dest = Location::create(['code' => 'L2', 'name' => 'Port B', 'city' => 'City B', 'country' => 'USA', 'latitude' => 30, 'longitude' => 40]);

        $shipmentB = Shipment::create([
            'shipment_number' => 'SHP-CUST-B',
            'customer_id' => $customerB->id,
            'carrier_id' => $carrier->id,
            'origin_id' => $origin->id,
            'destination_id' => $dest->id,
            'cargo_type' => 'Goods',
            'total_weight' => 100,
            'total_volume' => 1,
            'expected_departure' => Carbon::now(),
            'expected_arrival' => Carbon::now()->addDays(2),
            'estimated_arrival' => Carbon::now()->addDays(2),
            'current_status' => 'IN_TRANSIT',
        ]);

        // Customer A attempts to view Customer B's shipment
        $response = $this->actingAs($customerA)->get('/shipments/' . $shipmentB->id);
        $response->assertStatus(403);
    }

    public function test_customer_can_view_own_shipment(): void
    {
        $customerA = User::factory()->create(['role' => 'customer']);
        $carrier = Carrier::create(['code' => 'CAR2', 'name' => 'CargoPro']);
        $origin = Location::create(['code' => 'L3', 'name' => 'Port C', 'city' => 'City C', 'country' => 'USA', 'latitude' => 10, 'longitude' => 20]);
        $dest = Location::create(['code' => 'L4', 'name' => 'Port D', 'city' => 'City D', 'country' => 'USA', 'latitude' => 30, 'longitude' => 40]);

        $shipmentA = Shipment::create([
            'shipment_number' => 'SHP-CUST-A',
            'customer_id' => $customerA->id,
            'carrier_id' => $carrier->id,
            'origin_id' => $origin->id,
            'destination_id' => $dest->id,
            'cargo_type' => 'Goods',
            'total_weight' => 100,
            'total_volume' => 1,
            'expected_departure' => Carbon::now(),
            'expected_arrival' => Carbon::now()->addDays(2),
            'estimated_arrival' => Carbon::now()->addDays(2),
            'current_status' => 'IN_TRANSIT',
        ]);

        $response = $this->actingAs($customerA)->get('/shipments/' . $shipmentA->id);
        $response->assertStatus(200);
        $response->assertSee('SHP-CUST-A');
    }

    public function test_non_admin_cannot_access_audit_logs(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $operator = User::factory()->create(['role' => 'operator']);

        $this->actingAs($customer)->get('/audit-logs')->assertStatus(403);
        $this->actingAs($operator)->get('/audit-logs')->assertStatus(403);
    }
}
