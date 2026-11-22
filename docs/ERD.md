# CargoTrace - Entity Relationship Diagram & Data Dictionary

This document details the database architecture, table structures, relationships, cardinality, and design choices for the **CargoTrace Global Shipment Visibility Platform** (Web Programming 2 Final Project).

---

## 1. Entity-Relationship Diagram (ERD)

```mermaid
erDiagram
    USERS ||--o{ SHIPMENTS : "customer places"
    USERS ||--o{ NOTIFICATIONS : "receives"
    USERS ||--o{ AUDIT_LOGS : "acts in"
    USERS ||--o{ TRACKING_EVENTS : "creates"
    USERS ||--o{ SHIPMENT_EXCEPTIONS : "resolves"

    CARRIERS ||--o{ SHIPMENTS : "delivers"

    LOCATIONS ||--o{ SHIPMENTS : "origin"
    LOCATIONS ||--o{ SHIPMENTS : "destination"
    LOCATIONS ||--o{ SHIPMENTS : "current location"
    LOCATIONS ||--o{ TRACKING_EVENTS : "checkpoint at"

    SHIPMENTS ||--o{ SHIPMENT_ITEMS : "contains"
    SHIPMENTS ||--o{ TRACKING_EVENTS : "logs travel"
    SHIPMENTS ||--o{ SHIPMENT_EXCEPTIONS : "encounters"

    USERS {
        bigint id PK
        string name
        string email UK
        string password
        enum role "admin, operator, customer"
        string company_name
        string phone
        timestamps created_at
    }

    CARRIERS {
        bigint id PK
        string code UK "3-10 char unique code"
        string name
        string contact_email
        string phone
        string service_type "Ocean, Air, Road, Rail"
        string website
        boolean is_active
        timestamps created_at
    }

    LOCATIONS {
        bigint id PK
        string code UK "e.g. USLAX, USORD"
        string name
        string type "Port, Airport, Warehouse, Hub, Customer Site"
        string city
        string state
        string country
        decimal latitude "10,7"
        decimal longitude "10,7"
        timestamps created_at
    }

    SHIPMENTS {
        bigint id PK
        string shipment_number UK "e.g. SHP-2024-001284"
        bigint customer_id FK "users.id"
        bigint carrier_id FK "carriers.id"
        bigint origin_id FK "locations.id"
        bigint destination_id FK "locations.id"
        bigint current_location_id FK "locations.id"
        string cargo_type
        decimal total_weight "10,2 kg"
        decimal total_volume "10,2 cbm"
        datetime expected_departure
        datetime actual_departure
        datetime expected_arrival
        datetime estimated_arrival
        datetime actual_arrival
        enum current_status "CREATED, BOOKED, PICKED_UP, IN_TRANSIT, DELIVERED"
        boolean is_delayed
        decimal delay_hours "8,2"
        enum delay_severity "NONE, LOW, MEDIUM, HIGH"
        string group_scenario "Group A, Group B, Group C"
        text notes
        timestamps created_at
    }

    SHIPMENT_ITEMS {
        bigint id PK
        bigint shipment_id FK "shipments.id (CASCADE)"
        string item_name
        string sku
        integer quantity
        decimal weight "10,2 kg"
        decimal volume "10,2 cbm"
        string package_type "Box, Pallet, Container, Crate"
        string hs_code
        timestamps created_at
    }

    TRACKING_EVENTS {
        bigint id PK
        bigint shipment_id FK "shipments.id (CASCADE)"
        bigint location_id FK "locations.id (NULLABLE)"
        string event_type "MILESTONE, STATUS_CHANGE, CHECKPOINT, DEPARTURE, ARRIVAL, EXCEPTION"
        enum status "CREATED, BOOKED, PICKED_UP, IN_TRANSIT, DELIVERED"
        decimal latitude "10,7"
        decimal longitude "10,7"
        string location_name
        text description
        datetime event_time
        bigint created_by FK "users.id"
        boolean is_exception
        timestamps created_at
    }

    SHIPMENT_EXCEPTIONS {
        bigint id PK
        bigint shipment_id FK "shipments.id (CASCADE)"
        enum exception_type "CUSTOMS_HOLD, WEATHER_DELAY, PORT_CONGESTION, DAMAGED, MISROUTED, DOCUMENTATION_ISSUE"
        enum severity "LOW, MEDIUM, HIGH, CRITICAL"
        enum status "OPEN, INVESTIGATING, RESOLVED"
        text description
        datetime detected_at
        datetime resolved_at
        bigint resolved_by FK "users.id (NULLABLE)"
        text resolution_notes
        timestamps created_at
    }

    NOTIFICATIONS {
        bigint id PK
        bigint user_id FK "users.id (CASCADE)"
        string title
        text message
        string type "info, warning, danger, success"
        boolean is_read
        datetime read_at
        string link
        timestamps created_at
    }

    AUDIT_LOGS {
        bigint id PK
        bigint user_id FK "users.id (NULLABLE)"
        string action "CREATE_SHIPMENT, STATUS_TRANSITION, etc."
        string entity_type "Shipment, TrackingEvent, etc."
        bigint entity_id
        json old_values
        json new_values
        string ip_address
        text user_agent
        timestamps created_at
    }

    SYSTEM_SETTINGS {
        bigint id PK
        string key UK
        text value
        string description
        timestamps created_at
    }
```

---

## 2. Data Dictionary

### Table: `users`
Stores system actors with role-based access control.
- `id`: Primary key, auto-incrementing 64-bit integer.
- `name`: Full name of user or primary representative.
- `email`: Unique login email address.
- `password`: Bcrypt / Argon2 hashed password.
- `role`: Role enum (`admin`, `operator`, `customer`).
- `company_name`: Associated corporate shipper name.
- `phone`: Contact telephone.

### Table: `carriers`
Stores integrated logistics transport carriers (e.g. FastShip, CargoPro, Maersk).
- `id`: Primary key.
- `code`: Unique uppercase carrier identifier code.
- `name`: Full legal company name.
- `service_type`: Primary transport mode (Ocean, Air, Intermodal).
- `is_active`: Operational status flag.

### Table: `locations`
Stores ports, airports, intermodal rail hubs, and regional warehouses.
- `id`: Primary key.
- `code`: UN/LOCODE or airport code (e.g. `USLAX`, `USORD`).
- `name`: Facility title.
- `type`: Category (`Port`, `Airport`, `Warehouse`, `Hub`).
- `latitude` / `longitude`: Decimal coordinates (precision 10, scale 7) used for Leaflet.js mapping and Open-Meteo meteorological queries.

### Table: `shipments`
Central aggregate entity managing life-cycle and scheduling.
- `shipment_number`: Unique business tracking code (e.g. `SHP-2024-001284`).
- `customer_id`: Foreign key to `users.id` representing owner.
- `carrier_id`: Assigned carrier.
- `origin_id` / `destination_id`: Routing terminals.
- `current_location_id`: Dynamic location pointer.
- `current_status`: Finite state machine (`CREATED`, `BOOKED`, `PICKED_UP`, `IN_TRANSIT`, `DELIVERED`).
- `is_delayed`: Computed boolean flag based on ETA vs SLA arrival.
- `delay_hours`: Numeric delay duration in hours.
- `delay_severity`: Classification (`LOW`, `MEDIUM`, `HIGH`, `NONE`).
- `group_scenario`: Identifier for unique course scenario (`Group A`, `Group B`, `Group C`).

### Table: `tracking_events`
Immutable historical checkpoints and waypoint telemetry.
- `shipment_id`: Target consignment.
- `event_time`: Verified chronological timestamp.
- `is_exception`: Flag distinguishing operational bottlenecks from standard milestones.

### Table: `shipment_exceptions`
Disruption records requiring manual or automated resolution.
- `exception_type`: Specific cause (`CUSTOMS_HOLD`, `WEATHER_DELAY`, `PORT_CONGESTION`, `DAMAGED`, `MISROUTED`, `DOCUMENTATION_ISSUE`).
- `severity`: Impact scale (`LOW`, `MEDIUM`, `HIGH`, `CRITICAL`).
- `status`: Incident workflow (`OPEN`, `INVESTIGATING`, `RESOLVED`).
- `detected_at` / `resolved_at`: Timestamps used to calculate Mean Time To Resolve (MTTR).

---

## 3. Cardinality & Design Decisions

1. **Why `Shipment -> TrackingEvents` is 1-to-Many:**
   A shipment moves across multiple spatial checkpoints (origin terminal, vessel departure, waterway corridor, customs gate, rail yard, and final consignee). Each checkpoint is recorded as an immutable row to preserve chronological auditability and route history.
2. **Why `Shipment -> ShipmentExceptions` is 1-to-Many:**
   A long-haul international shipment can encounter multiple distinct disruptions throughout its voyage (e.g. weather delay at sea followed later by documentation issues at customs).
3. **Database Driver Agnosticism:**
   All migrations, relationships, and queries are 100% compliant with both SQLite (for zero-setup dev & test) and MySQL 8+ (for production enterprise hosting).
