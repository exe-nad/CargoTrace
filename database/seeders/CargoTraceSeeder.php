<?php

namespace Database\Seeders;

use App\Models\Carrier;
use App\Models\Location;
use App\Models\Notification;
use App\Models\Shipment;
use App\Models\ShipmentException;
use App\Models\ShipmentItem;
use App\Models\SystemSetting;
use App\Models\TrackingEvent;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CargoTraceSeeder extends Seeder
{
    public function run(): void
    {
        // 1. System Settings
        SystemSetting::set('delay_threshold_low', 6.0, 'Low severity delay threshold in hours');
        SystemSetting::set('delay_threshold_medium', 24.0, 'Medium severity delay threshold in hours');
        SystemSetting::set('default_group_scenario', 'Group A', 'Current active group scenario (Group A: 12h, Group B: 18h, Group C: 24h)');

        // 2. Users (Admin, Operator, Customers)
        $admin = User::firstOrCreate(
            ['email' => 'admin@cargotrace.com'],
            [
                'name' => 'Sarah Johnson',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'company_name' => 'CargoTrace Global Logistics Ltd',
                'phone' => '+1 (555) 019-2834',
            ]
        );

        $operator = User::firstOrCreate(
            ['email' => 'operator@cargotrace.com'],
            [
                'name' => 'Alex Rivera',
                'password' => Hash::make('password123'),
                'role' => 'operator',
                'company_name' => 'CargoTrace Operations Center',
                'phone' => '+1 (555) 018-9921',
            ]
        );

        $customerUsers = [
            'Global Industries' => User::firstOrCreate(
                ['email' => 'customer@globalindustries.com'],
                ['name' => 'David Miller', 'password' => Hash::make('password123'), 'role' => 'customer', 'company_name' => 'Global Industries Inc.', 'phone' => '+1 (555) 102-3948']
            ),
            'Tech Solutions' => User::firstOrCreate(
                ['email' => 'customer@techsolutions.com'],
                ['name' => 'Elena Rostova', 'password' => Hash::make('password123'), 'role' => 'customer', 'company_name' => 'Tech Solutions Corp', 'phone' => '+1 (555) 203-4958']
            ),
            'Retail Hub' => User::firstOrCreate(
                ['email' => 'customer@retailhub.com'],
                ['name' => 'Marcus Vance', 'password' => Hash::make('password123'), 'role' => 'customer', 'company_name' => 'Retail Hub International', 'phone' => '+1 (555) 304-5968']
            ),
            'Alpha Manufacturing' => User::firstOrCreate(
                ['email' => 'customer@alphamanuf.com'],
                ['name' => 'Chen Wei', 'password' => Hash::make('password123'), 'role' => 'customer', 'company_name' => 'Alpha Manufacturing Ltd', 'phone' => '+1 (555) 405-6978']
            ),
            'Omega Retail' => User::firstOrCreate(
                ['email' => 'customer@omegaretail.com'],
                ['name' => 'Sophie Laurent', 'password' => Hash::make('password123'), 'role' => 'customer', 'company_name' => 'Omega Retail Partners', 'phone' => '+1 (555) 506-7988']
            ),
        ];

        // 3. Carriers (Slide 10/Mockup: FastShip, CargoPro, Logistics Partners, etc.)
        $fastShip = Carrier::firstOrCreate(['code' => 'FST'], ['name' => 'FastShip', 'contact_email' => 'support@fastship.com', 'phone' => '+1 (800) 555-0101', 'service_type' => 'Express Ocean & Air']);
        $cargoPro = Carrier::firstOrCreate(['code' => 'CPR'], ['name' => 'CargoPro', 'contact_email' => 'dispatch@cargopro.net', 'phone' => '+1 (800) 555-0102', 'service_type' => 'Heavy Freight & Rail']);
        $logisticsPartners = Carrier::firstOrCreate(['code' => 'LGP'], ['name' => 'Logistics Partners', 'contact_email' => 'ops@logpartners.org', 'phone' => '+1 (800) 555-0103', 'service_type' => 'Intermodal Freight']);
        $maersk = Carrier::firstOrCreate(['code' => 'MSK'], ['name' => 'Maersk Global', 'contact_email' => 'support@maersk.com', 'phone' => '+1 (800) 555-0104', 'service_type' => 'Ocean Container Lines']);
        $dhl = Carrier::firstOrCreate(['code' => 'DHL'], ['name' => 'DHL Global Forwarding', 'contact_email' => 'freight@dhl.com', 'phone' => '+1 (800) 555-0105', 'service_type' => 'Air & Overland Express']);

        $carriers = [$fastShip, $cargoPro, $logisticsPartners, $maersk, $dhl];

        // 4. Locations (Realistic ports, hubs with coordinates)
        $locLA = Location::firstOrCreate(['code' => 'USLAX'], ['name' => 'Port of Los Angeles', 'type' => 'Port', 'city' => 'Los Angeles', 'state' => 'CA', 'country' => 'United States', 'latitude' => 33.74318, 'longitude' => -118.26725]);
        $locCHI = Location::firstOrCreate(['code' => 'USORD'], ['name' => 'Chicago Intermodal Gateway', 'type' => 'Hub', 'city' => 'Chicago', 'state' => 'IL', 'country' => 'United States', 'latitude' => 41.87811, 'longitude' => -87.62980]);
        $locSF = Location::firstOrCreate(['code' => 'USSFO'], ['name' => 'San Francisco Bay Terminal', 'type' => 'Warehouse', 'city' => 'San Francisco', 'state' => 'CA', 'country' => 'United States', 'latitude' => 37.77493, 'longitude' => -122.41942]);
        $locDAL = Location::firstOrCreate(['code' => 'USDFW'], ['name' => 'Dallas Freight Depot', 'type' => 'Hub', 'city' => 'Dallas', 'state' => 'TX', 'country' => 'United States', 'latitude' => 32.77666, 'longitude' => -96.79699]);
        $locNYC = Location::firstOrCreate(['code' => 'USNYC'], ['name' => 'Port of New York & New Jersey', 'type' => 'Port', 'city' => 'New York', 'state' => 'NY', 'country' => 'United States', 'latitude' => 40.71278, 'longitude' => -74.00594]);
        $locMIA = Location::firstOrCreate(['code' => 'USMIA'], ['name' => 'Miami Maritime Terminal', 'type' => 'Port', 'city' => 'Miami', 'state' => 'FL', 'country' => 'United States', 'latitude' => 25.76168, 'longitude' => -80.19179]);
        $locDET = Location::firstOrCreate(['code' => 'USDTW'], ['name' => 'Detroit Logistics Center', 'type' => 'Warehouse', 'city' => 'Detroit', 'state' => 'MI', 'country' => 'United States', 'latitude' => 42.33143, 'longitude' => -83.04575]);
        $locSEA = Location::firstOrCreate(['code' => 'USSEA'], ['name' => 'Port of Seattle', 'type' => 'Port', 'city' => 'Seattle', 'state' => 'WA', 'country' => 'United States', 'latitude' => 47.60621, 'longitude' => -122.33207]);
        $locAUS = Location::firstOrCreate(['code' => 'USAUS'], ['name' => 'Austin Regional Hub', 'type' => 'Hub', 'city' => 'Austin', 'state' => 'TX', 'country' => 'United States', 'latitude' => 30.26715, 'longitude' => -97.74306]);
        $locDEN = Location::firstOrCreate(['code' => 'USDEN'], ['name' => 'Denver Overland Depot', 'type' => 'Warehouse', 'city' => 'Denver', 'state' => 'CO', 'country' => 'United States', 'latitude' => 39.73924, 'longitude' => -104.99025]);
        $locSIN = Location::firstOrCreate(['code' => 'SGSIN'], ['name' => 'PSA Port of Singapore', 'type' => 'Port', 'city' => 'Singapore', 'state' => 'Central', 'country' => 'Singapore', 'latitude' => 1.29027, 'longitude' => 103.85196]);
        $locRTM = Location::firstOrCreate(['code' => 'NLRTM'], ['name' => 'Port of Rotterdam Europort', 'type' => 'Port', 'city' => 'Rotterdam', 'state' => 'South Holland', 'country' => 'Netherlands', 'latitude' => 51.92442, 'longitude' => 4.47773]);

        $locations = [$locLA, $locCHI, $locSF, $locDAL, $locNYC, $locMIA, $locDET, $locSEA, $locAUS, $locDEN, $locSIN, $locRTM];

        // 5. Create Key Mockup Shipments (Exact Match from User's Screenshot)
        // Mockup 1: SHP-2024-001284 | Global Industries | FastShip | Los Angeles, CA | Chicago, IL | 2024-05-24 | DELAYED
        $s1 = Shipment::firstOrCreate(
            ['shipment_number' => 'SHP-2024-001284'],
            [
                'customer_id' => $customerUsers['Global Industries']->id,
                'carrier_id' => $fastShip->id,
                'origin_id' => $locLA->id,
                'destination_id' => $locCHI->id,
                'current_location_id' => $locLA->id,
                'cargo_type' => 'Electronics & Semiconductor Modules',
                'total_weight' => 14250.00,
                'total_volume' => 42.50,
                'expected_departure' => Carbon::now()->subDays(5),
                'actual_departure' => Carbon::now()->subDays(5)->addHours(4),
                'expected_arrival' => Carbon::now()->subHours(12),
                'estimated_arrival' => Carbon::now()->addHours(18), // Delayed by 30 hours
                'current_status' => 'IN_TRANSIT',
                'is_delayed' => true,
                'delay_hours' => 30.00,
                'delay_severity' => 'HIGH',
                'group_scenario' => 'Group A',
                'notes' => 'Priority maritime cargo with customs pre-clearance required.',
            ]
        );

        // Add exception to s1
        ShipmentException::firstOrCreate(
            ['shipment_id' => $s1->id, 'exception_type' => 'CUSTOMS_HOLD'],
            [
                'severity' => 'HIGH',
                'status' => 'OPEN',
                'description' => 'Documentation discrepancy during customs clearance audit at gateway checkpoint.',
                'detected_at' => Carbon::now()->subHours(18),
            ]
        );

        // Mockup 2: SHP-2024-00123 | Tech Solutions | CargoPro | San Francisco, CA | Dallas, TX | 2024-05-23 | IN TRANSIT
        $s2 = Shipment::firstOrCreate(
            ['shipment_number' => 'SHP-2024-00123'],
            [
                'customer_id' => $customerUsers['Tech Solutions']->id,
                'carrier_id' => $cargoPro->id,
                'origin_id' => $locSF->id,
                'destination_id' => $locDAL->id,
                'current_location_id' => $locSF->id,
                'cargo_type' => 'Data Center Server Racks',
                'total_weight' => 8400.00,
                'total_volume' => 28.00,
                'expected_departure' => Carbon::now()->subDays(2),
                'actual_departure' => Carbon::now()->subDays(2)->addHours(2),
                'expected_arrival' => Carbon::now()->addDays(2),
                'estimated_arrival' => Carbon::now()->addDays(2),
                'current_status' => 'IN_TRANSIT',
                'is_delayed' => false,
                'delay_hours' => 0.00,
                'delay_severity' => 'NONE',
                'group_scenario' => 'Group B',
                'notes' => 'Temperature-controlled shock-dampened transport.',
            ]
        );

        // Mockup 3: SHP-2024-00122 | Retail Hub | Logistics Partners | New York, NY | Miami, FL | 2024-05-22 | IN TRANSIT
        $s3 = Shipment::firstOrCreate(
            ['shipment_number' => 'SHP-2024-00122'],
            [
                'customer_id' => $customerUsers['Retail Hub']->id,
                'carrier_id' => $logisticsPartners->id,
                'origin_id' => $locNYC->id,
                'destination_id' => $locMIA->id,
                'current_location_id' => $locNYC->id,
                'cargo_type' => 'Apparel & Fast Fashion Garments',
                'total_weight' => 6100.00,
                'total_volume' => 35.00,
                'expected_departure' => Carbon::now()->subDays(3),
                'actual_departure' => Carbon::now()->subDays(3)->addHours(1),
                'expected_arrival' => Carbon::now()->addDays(1),
                'estimated_arrival' => Carbon::now()->addDays(1),
                'current_status' => 'IN_TRANSIT',
                'is_delayed' => false,
                'delay_hours' => 0.00,
                'delay_severity' => 'NONE',
                'group_scenario' => 'Group C',
                'notes' => 'Seasonal inventory delivery to coastal regional distribution center.',
            ]
        );

        // Mockup 4: SHP-2024-001281 | Alpha Manufacturing | FastShip | Detroit, MI | Seattle, WA | 2024-05-21 | IN TRANSIT
        $s4 = Shipment::firstOrCreate(
            ['shipment_number' => 'SHP-2024-001281'],
            [
                'customer_id' => $customerUsers['Alpha Manufacturing']->id,
                'carrier_id' => $fastShip->id,
                'origin_id' => $locDET->id,
                'destination_id' => $locSEA->id,
                'current_location_id' => $locDET->id,
                'cargo_type' => 'Automotive Precision Engine Blocks',
                'total_weight' => 21000.00,
                'total_volume' => 55.00,
                'expected_departure' => Carbon::now()->subDays(4),
                'actual_departure' => Carbon::now()->subDays(4)->addHours(3),
                'expected_arrival' => Carbon::now()->addDays(3),
                'estimated_arrival' => Carbon::now()->addDays(3),
                'current_status' => 'IN_TRANSIT',
                'is_delayed' => false,
                'delay_hours' => 0.00,
                'delay_severity' => 'NONE',
                'group_scenario' => 'Group A',
                'notes' => 'Heavy industrial machinery requiring flatbed multimodal rail.',
            ]
        );

        // Mockup 5: SHP-2024-001280 | Omega Retail | CargoPro | Austin, TX | Denver, CO | 2024-05-20 | IN TRANSIT
        $s5 = Shipment::firstOrCreate(
            ['shipment_number' => 'SHP-2024-001280'],
            [
                'customer_id' => $customerUsers['Omega Retail']->id,
                'carrier_id' => $cargoPro->id,
                'origin_id' => $locAUS->id,
                'destination_id' => $locDEN->id,
                'current_location_id' => $locAUS->id,
                'cargo_type' => 'Consumer Smart Electronics & IoT Gear',
                'total_weight' => 5200.00,
                'total_volume' => 19.50,
                'expected_departure' => Carbon::now()->subDays(2),
                'actual_departure' => Carbon::now()->subDays(2)->addHours(1),
                'expected_arrival' => Carbon::now()->addDays(2),
                'estimated_arrival' => Carbon::now()->addDays(2),
                'current_status' => 'IN_TRANSIT',
                'is_delayed' => false,
                'delay_hours' => 0.00,
                'delay_severity' => 'NONE',
                'group_scenario' => 'Group B',
                'notes' => 'High-value secured transit.',
            ]
        );

        // Add Items for s1..s5
        $namedShipments = [$s1, $s2, $s3, $s4, $s5];
        foreach ($namedShipments as $ns) {
            ShipmentItem::create([
                'shipment_id' => $ns->id,
                'item_name' => $ns->cargo_type . ' (Primary Lot)',
                'sku' => 'SKU-' . strtoupper(substr(md5($ns->shipment_number), 0, 8)),
                'quantity' => 150,
                'weight' => $ns->total_weight,
                'volume' => $ns->total_volume,
                'package_type' => 'Container 40ft',
                'hs_code' => '8471.50',
            ]);

            // Add Origin Tracking Event
            TrackingEvent::create([
                'shipment_id' => $ns->id,
                'location_id' => $ns->origin_id,
                'event_type' => 'MILESTONE',
                'status' => 'CREATED',
                'location_name' => $ns->origin->name,
                'latitude' => $ns->origin->latitude,
                'longitude' => $ns->origin->longitude,
                'description' => 'Cargo booking confirmed and manifest generated.',
                'event_time' => Carbon::parse($ns->expected_departure)->subHours(8),
                'created_by' => $admin->id,
            ]);

            TrackingEvent::create([
                'shipment_id' => $ns->id,
                'location_id' => $ns->origin_id,
                'event_type' => 'DEPARTURE',
                'status' => 'IN_TRANSIT',
                'location_name' => $ns->origin->name,
                'latitude' => $ns->origin->latitude,
                'longitude' => $ns->origin->longitude,
                'description' => 'Vessel departed terminal. En route to ' . $ns->destination->city . '.',
                'event_time' => Carbon::parse($ns->actual_departure ?? now()->subDays(1)),
                'created_by' => $operator->id,
            ]);
        }

        // Add additional waypoint events to s1 (Los Angeles to Chicago)
        TrackingEvent::create([
            'shipment_id' => $s1->id,
            'location_id' => $locDEN->id,
            'event_type' => 'CHECKPOINT',
            'status' => 'IN_TRANSIT',
            'location_name' => 'Overland Transit Checkpoint (Rocky Mountains Depot)',
            'latitude' => 39.73924,
            'longitude' => -104.99025,
            'description' => 'Container scanned at rail interchange checkpoint.',
            'event_time' => Carbon::now()->subDays(2),
            'created_by' => $operator->id,
        ]);

        TrackingEvent::create([
            'shipment_id' => $s1->id,
            'location_id' => null,
            'event_type' => 'EXCEPTION',
            'status' => 'IN_TRANSIT',
            'location_name' => 'Customs Intermodal Staging Yard',
            'latitude' => 38.50000,
            'longitude' => -95.00000,
            'description' => 'Customs hold flag initiated. Additional manifest verification in progress.',
            'event_time' => Carbon::now()->subHours(18),
            'created_by' => $operator->id,
            'is_exception' => true,
        ]);

        // 6. Generate remaining shipments to match EXACT stats from Mockup:
        // Mockup Target Numbers:
        // Total Shipments: 128
        // In Transit: 46 (We already have 5, need 41 more)
        // Delivered: 61 (Need 61 delivered)
        // Delayed: 12 (We already have 1, need 11 more)
        // Open Exceptions: 9 (We already have 1, need 8 more)
        // Remainder: 128 - 46 (in transit) - 61 (delivered) = 21 (Created / Booked / Picked up)

        $customersList = array_values($customerUsers);
        $exceptionTypes = [
            'CUSTOMS_HOLD',
            'WEATHER_DELAY',
            'PORT_CONGESTION',
            'DAMAGED',
            'MISROUTED',
            'DOCUMENTATION_ISSUE',
        ];

        $currentCount = Shipment::count();
        $targetTotal = 128;
        $inTransitNeeded = 46 - 5;
        $deliveredNeeded = 61;
        $delayedRemaining = 12 - 1;
        $openExceptionsRemaining = 9 - 1;

        for ($i = $currentCount + 1; $i <= $targetTotal; $i++) {
            $customer = $customersList[$i % count($customersList)];
            $carrier = $carriers[$i % count($carriers)];
            $orig = $locations[$i % count($locations)];
            $dest = $locations[($i + 3) % count($locations)];

            // Determine status
            if ($inTransitNeeded > 0) {
                $status = 'IN_TRANSIT';
                $inTransitNeeded--;
            } elseif ($deliveredNeeded > 0) {
                $status = 'DELIVERED';
                $deliveredNeeded--;
            } elseif ($i % 3 === 0) {
                $status = 'PICKED_UP';
            } elseif ($i % 3 === 1) {
                $status = 'BOOKED';
            } else {
                $status = 'CREATED';
            }

            // Determine if delayed
            $isDelayed = false;
            $delayHours = 0.00;
            $severity = 'NONE';
            $expectedArrival = Carbon::now()->addDays(($i % 10) - 4);
            $estimatedArrival = clone $expectedArrival;

            if ($delayedRemaining > 0 && ($status === 'IN_TRANSIT' || $status === 'PICKED_UP')) {
                $isDelayed = true;
                $delayHours = ($i % 48) + 8.5; // between 8.5 and 56.5 hours
                $severity = $delayHours > 24 ? 'HIGH' : ($delayHours > 6 ? 'MEDIUM' : 'LOW');
                $estimatedArrival->addHours((int) $delayHours);
                $delayedRemaining--;
            }

            $shipmentNumber = sprintf('SHP-2024-%06d', 1000 + $i);

            $shipment = Shipment::create([
                'shipment_number' => $shipmentNumber,
                'customer_id' => $customer->id,
                'carrier_id' => $carrier->id,
                'origin_id' => $orig->id,
                'destination_id' => $dest->id,
                'current_location_id' => $status === 'DELIVERED' ? $dest->id : $orig->id,
                'cargo_type' => ['Medical Supplies', 'Automotive Parts', 'Consumer Goods', 'Chemical Reagents', 'Industrial Pumps'][$i % 5],
                'total_weight' => rand(1500, 32000),
                'total_volume' => rand(10, 85),
                'expected_departure' => Carbon::now()->subDays(rand(3, 15)),
                'actual_departure' => Carbon::now()->subDays(rand(3, 15))->addHours(rand(1, 6)),
                'expected_arrival' => $expectedArrival,
                'estimated_arrival' => $estimatedArrival,
                'actual_arrival' => $status === 'DELIVERED' ? Carbon::now()->subDays(rand(1, 5)) : null,
                'current_status' => $status,
                'is_delayed' => $isDelayed,
                'delay_hours' => $delayHours,
                'delay_severity' => $severity,
                'group_scenario' => ['Group A', 'Group B', 'Group C'][$i % 3],
                'notes' => 'Commercial shipment under CargoTrace visibility SLA.',
            ]);

            // Add standard cargo item
            ShipmentItem::create([
                'shipment_id' => $shipment->id,
                'item_name' => $shipment->cargo_type . ' Pack',
                'sku' => 'SKU-' . strtoupper(substr(md5($shipment->shipment_number), 0, 8)),
                'quantity' => rand(50, 600),
                'weight' => $shipment->total_weight,
                'volume' => $shipment->total_volume,
                'package_type' => ['Standard Box', 'Pallet', 'Crate', 'Container'][$i % 4],
                'hs_code' => '8471.' . rand(10, 90),
            ]);

            // Add tracking events
            TrackingEvent::create([
                'shipment_id' => $shipment->id,
                'location_id' => $orig->id,
                'event_type' => 'MILESTONE',
                'status' => 'CREATED',
                'location_name' => $orig->name,
                'latitude' => $orig->latitude,
                'longitude' => $orig->longitude,
                'description' => 'Shipment booked and initial customs declaration filed.',
                'event_time' => Carbon::parse($shipment->expected_departure)->subHours(12),
                'created_by' => $admin->id,
            ]);

            if (in_array($status, ['PICKED_UP', 'IN_TRANSIT', 'DELIVERED'])) {
                TrackingEvent::create([
                    'shipment_id' => $shipment->id,
                    'location_id' => $orig->id,
                    'event_type' => 'DEPARTURE',
                    'status' => 'IN_TRANSIT',
                    'location_name' => $orig->name,
                    'latitude' => $orig->latitude,
                    'longitude' => $orig->longitude,
                    'description' => 'Cargo picked up and dispatched from origin gateway.',
                    'event_time' => Carbon::parse($shipment->actual_departure),
                    'created_by' => $operator->id,
                ]);
            }

            if ($status === 'DELIVERED') {
                TrackingEvent::create([
                    'shipment_id' => $shipment->id,
                    'location_id' => $dest->id,
                    'event_type' => 'ARRIVAL',
                    'status' => 'DELIVERED',
                    'location_name' => $dest->name,
                    'latitude' => $dest->latitude,
                    'longitude' => $dest->longitude,
                    'description' => 'Shipment arrived at final destination and signed off by consignee.',
                    'event_time' => Carbon::parse($shipment->actual_arrival),
                    'created_by' => $operator->id,
                ]);
            }

            // Create open exceptions if still needed
            if ($openExceptionsRemaining > 0 && ($isDelayed || $status === 'IN_TRANSIT')) {
                $exType = $exceptionTypes[$openExceptionsRemaining % count($exceptionTypes)];
                ShipmentException::create([
                    'shipment_id' => $shipment->id,
                    'exception_type' => $exType,
                    'severity' => ['MEDIUM', 'HIGH', 'CRITICAL'][$openExceptionsRemaining % 3],
                    'status' => $openExceptionsRemaining % 2 === 0 ? 'OPEN' : 'INVESTIGATING',
                    'description' => 'Operational exception noted: ' . str_replace('_', ' ', $exType) . ' during transport stage.',
                    'detected_at' => Carbon::now()->subHours(rand(4, 36)),
                ]);
                $openExceptionsRemaining--;
            }
        }

        // 7. Seed Initial Notifications
        Notification::create([
            'user_id' => $admin->id,
            'title' => 'System Alert: Port Congestion',
            'message' => 'Multiple inbound shipments to Port of Los Angeles experiencing delays due to berth congestion.',
            'type' => 'warning',
            'link' => '/exceptions',
        ]);

        Notification::create([
            'user_id' => $admin->id,
            'title' => 'High Delay Warning: SHP-2024-001284',
            'message' => 'Shipment SHP-2024-001284 has exceeded standard delay threshold by 30.0 hours.',
            'type' => 'danger',
            'link' => '/tracking/SHP-2024-001284',
        ]);

        Notification::create([
            'user_id' => $admin->id,
            'title' => 'Customs Clearance Completed',
            'message' => 'Shipment SHP-2024-001281 passed automated inspection successfully.',
            'type' => 'success',
            'link' => '/tracking/SHP-2024-001281',
        ]);
    }
}
