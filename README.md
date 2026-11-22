# CargoTrace &bull; Global Shipment Visibility & Tracking Platform

[![Web Programming 2 - UAS Final Project](https://img.shields.io/badge/Academic-Web%20Programming%202%20UAS-0284c7?style=for-the-badge)](https://github.com)
[![PHP](https://img.shields.io/badge/PHP-8.3%2B-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![Laravel](https://img.shields.io/badge/Laravel-12%2B-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![Tests Passing](https://img.shields.io/badge/Tests-21%20Passed%20(70%20Assertions)-10b981?style=for-the-badge&logo=checkmarx&logoColor=white)](https://phpunit.de)
[![License](https://img.shields.io/badge/License-MIT-slate?style=for-the-badge)](LICENSE)

> **"Build a software portfolio project—not just a CRUD website."**  
> CargoTrace is an enterprise-grade multimodal logistics visibility and telemetry platform engineered with Laravel, MySQL/SQLite, Leaflet.js, and Open-Meteo meteorological API integration.

---

## 🌐 Live International Cloud Deployment
Platform ini telah dihosting secara internasional dan dapat diakses langsung oleh penguji/dosen:
- **Live Website URL**: **[https://thus-profits-glory-command.trycloudflare.com](https://thus-profits-glory-command.trycloudflare.com)**
- **Akses Global**: Aktif melalui Cloudflare Global Edge Network (HTTPS Terenkripsi)
- **Akun Demo Pengujian**:
  - **Administrator**: `admin@cargotrace.com` / `password123`
  - **Logistics Operator**: `operator@cargotrace.com` / `password123`
  - **Customer / Consignee**: `customer@cargotrace.com` / `password123`

---

## 1. Problem & Business Solution

Modern global supply chains encounter persistent visibility bottlenecks:
- **Disparate Shipment Data**: Consignment status is scattered across legacy carrier databases, leaving consignees uninformed about real-time transit locations.
- **Undetected Exceptions & Bottlenecks**: Port congestion, customs inspections, and weather anomalies frequently cause delivery delays that are not surfaced systematically.
- **Lack of Centralized Operational Triage**: Fleet operators require real-time metrics and configurable delay thresholds to prioritize critical dispatches.

### CargoTrace Solution:
1. **Strict Shipment Lifecycle State Machine**: Enforces sequential milestone progression (`CREATED` &rarr; `BOOKED` &rarr; `PICKED_UP` &rarr; `IN_TRANSIT` &rarr; `DELIVERED`) with backend validation preventing illegal status jumps and locking terminal delivered records.
2. **Interactive Spatial Telemetry**: Interactive Leaflet.js mapping with custom visual pins for origin ports, waypoints, and destination gateways, paired with real-time ambient weather feeds from Open-Meteo.
3. **Deterministic Delay & ETA Engine**: Compares expected arrival SLA against dynamic estimated arrival, categorizing delay severity (`LOW`, `MEDIUM`, `HIGH`) across customizable scenario thresholds (Group A: 12h, Group B: 18h, Group C: 24h).
4. **Exception Incident Lifecycle**: Tracks issues (`CUSTOMS_HOLD`, `WEATHER_DELAY`, `PORT_CONGESTION`, etc.) through `OPEN`, `INVESTIGATING`, and `RESOLVED` states with automated Mean Time To Resolve (MTTR) calculation.
5. **Full RESTful API & Sanctum Authentication**: Headless API endpoints supporting token-based external integration, equipped with a complete Postman collection.

---

## 2. System Architecture & Tech Stack

Following **Slide 15** architecture guidelines, CargoTrace decouples transport concerns from business rules through a dedicated **Service Layer**:

```
Browser / HTTP Request / Postman
            │
            ▼
Routes + Middleware (Auth, Sanctum, RBAC Authorization)
            │
            ▼
Thin Controllers (DashboardController, ShipmentController, ApiController)
            │
            ▼
Business Service Layer
  ├── ShipmentService   : Lifecycle transitions, sequential validation, customer filtering
  ├── TrackingService   : Chronological event validation, current location updates
  ├── EtaService        : Deterministic delay comparison & threshold calculations
  ├── ExceptionService  : Incident lifecycle, customer notifications, resolution duration
  ├── ExternalApiService: Open-Meteo Weather API & OpenStreetMap Geocoding
  └── AuditService      : Comprehensive user and transaction audit trail
            │
            ▼
Eloquent ORM Models (User, Carrier, Location, Shipment, TrackingEvent, etc.)
            │
            ▼
Database (MySQL 8+ / SQLite)
```

### Technology Matrix:
| Component | Technology | Description |
|---|---|---|
| **Backend Framework** | Laravel 12+ / PHP 8.3 | Clean MVC + Service Pattern, Sanctum, Eloquent ORM |
| **Frontend UI** | Blade + Tailwind CSS | Responsive logistics dashboard with dark navy theme |
| **Interactive Maps** | Leaflet.js + OpenStreetMap | Real-time waypoint plotting, route polyline, and popup telemetry |
| **Charts & Metrics** | Chart.js | Visual status distribution bar chart & carrier delay doughnut |
| **External APIs** | Open-Meteo Weather API | Live ambient temperature, conditions, and wind speed |
| **Testing** | PHPUnit 12 | 21 automated feature and unit tests with 70 assertions |
| **Deployment** | Docker & Docker Compose | Containerized Nginx + PHP-FPM + MySQL setup |

---

## 3. Database Schema & ERD

The data model satisfies all requirements from **Slide 11** with 10 relational tables:

```
users (Accounts & Roles: admin, operator, customer)
  ├── shipments (Central consignment record & delay metrics)
  │     ├── shipment_items (Consignment package breakdown & HS codes)
  │     ├── tracking_events (Chronological immutable checkpoint logs)
  │     └── shipment_exceptions (Customs, weather, congestion disruptions)
  ├── carriers (Integrated logistics transport providers)
  ├── locations (Ports, airports, intermodal hubs, and coordinates)
  ├── notifications (In-app customer and operator alert dispatch)
  ├── audit_logs (Immutable activity & transaction log)
  └── system_settings (Dynamic delay thresholds & evaluation parameters)
```

> **Full Documentation**: See [docs/ERD.md](docs/ERD.md) for the detailed Entity-Relationship Diagram and complete Data Dictionary. Also see [docs/ARCHITECTURE.md](docs/ARCHITECTURE.md) for software engineering patterns.

---

## 4. User Roles & Access Control (RBAC)

All endpoints and views enforce server-side authorization:
- **Administrator (`admin`)**: Full system access, carrier/location management, global configurations, audit trail inspection.
- **Logistics Operator (`operator`)**: Dispatch and update shipments, log tracking checkpoints, manage and resolve exceptions.
- **Customer Shipper (`customer`)**: Data isolation policy ensures customers can **only view shipments belonging to their organization**. Unauthorized access returns HTTP 403.

---

## 5. Quick Start & Installation

### Prerequisites:
- PHP 8.2 or 8.3+
- Composer 2.x
- Node.js & npm (optional, Tailwind & Leaflet load via high-speed CDN)
- SQLite (preconfigured default) or MySQL 8+

### Step-by-Step Setup:
```bash
# 1. Clone repository & navigate to directory
cd cargotrace

# 2. Install composer dependencies (if setting up fresh)
composer install

# 3. Configure environment file
cp .env.example .env

# 4. Generate application key
php artisan key:generate

# 5. Run database migrations & seed realistic portfolio data
php artisan migrate:fresh --seed

# 6. Start local development server
php artisan serve
```
Open **`http://127.0.0.1:8000`** in your browser.

---

## 6. Seeded Demo Accounts (Slide 18 & 22)

The database seeder provisions **128 realistic shipments** matching the exact dashboard metrics from the brief, with ready-to-test accounts:

| Role | Name | Email | Password | Access Scope |
|---|---|---|---|---|
| **Administrator** | Sarah Johnson | `admin@cargotrace.com` | `password123` | Full access, audit logs, carrier settings |
| **Logistics Operator** | Alex Rivera | `operator@cargotrace.com` | `password123` | Create shipments, checkpoints, exceptions |
| **Customer (Shipper)** | David Miller | `customer@globalindustries.com` | `password123` | Isolated view for Global Industries Inc. |
| **Customer (Shipper)** | Elena Rostova | `customer@techsolutions.com` | `password123` | Isolated view for Tech Solutions Corp |

*(The login screen also provides a **Quick Demo Switcher** to log in as any role with a single click).*

---

## 7. Automated Testing Suite (Slide 16)

Execute the comprehensive automated test suite with PHPUnit:
```bash
php vendor/phpunit/phpunit/phpunit
```

### Test Coverage Highlights:
- `AuthenticationTest`: Valid/invalid credentials, unauthenticated redirects, API Sanctum token issuance.
- `ShipmentLifecycleTest`: Valid sequential transitions, illegal status skipping prevention, terminal `DELIVERED` status immutability.
- `AuthorizationTest`: Cross-tenant customer isolation (403 forbidden), customer self-service, non-admin audit log protection.
- `ShipmentApiTest`: Full REST API suite (pagination, creation, checkpoint logging, exception patching).
- `EtaServiceTest`: Mathematical delay calculation, severity bands (`LOW`, `MEDIUM`, `HIGH`), and Group scenario threshold overrides.

---

## 8. REST API Reference & Postman Collection

Import the included Postman collection located at:
`docs/CargoTrace_Postman_Collection.json`

### Key Endpoints (Slide 12):
| Method | Endpoint | Description | Access |
|---|---|---|---|
| `POST` | `/api/register` | Register customer account | Public |
| `POST` | `/api/login` | Obtain Sanctum Bearer token | Public |
| `GET` | `/api/user` | Get authenticated user profile | Authenticated |
| `GET` | `/api/shipments` | List shipments (paginated & filterable) | Authenticated |
| `POST` | `/api/shipments` | Register new shipment manifest | Operator/Admin |
| `GET` | `/api/shipments/{id}` | Retrieve consignment details & waypoints | Authenticated |
| `PATCH` | `/api/shipments/{id}` | Update shipment or transition status | Operator/Admin |
| `DELETE`| `/api/shipments/{id}` | Delete shipment | Admin only |
| `GET` | `/api/shipments/{id}/tracking` | List chronological checkpoints | Authenticated |
| `POST` | `/api/shipments/{id}/tracking` | Append tracking checkpoint event | Operator/Admin |
| `GET` | `/api/carriers` | List active carrier fleet | Authenticated |
| `POST` | `/api/carriers` | Register carrier partner | Admin only |
| `GET` | `/api/locations` | List ports and logistics terminals | Authenticated |
| `GET` | `/api/shipments/{id}/exceptions` | List exceptions on shipment | Authenticated |
| `POST` | `/api/shipments/{id}/exceptions` | Flag operational exception | Operator/Admin |
| `PATCH` | `/api/exceptions/{id}` | Update exception status & resolution | Operator/Admin |

---

## 9. Docker Deployment

Deploy with Docker Compose for production evaluation:
```bash
docker compose up -d --build
```
The application will be served at `http://localhost:8000`.

---

## 10. Third-Party Libraries & Code Attribution (Slide 2 & 20)

In compliance with the academic integrity policy, all external libraries and templates are attributed:
- **Tailwind CSS**: Utility-first CSS styling framework ([tailwindcss.com](https://tailwindcss.com)).
- **Leaflet.js**: Mobile-friendly interactive geospatial maps ([leafletjs.com](https://leafletjs.com)).
- **OpenStreetMap**: Map tiles and cartography data ([openstreetmap.org](https://www.openstreetmap.org/copyright)).
- **Open-Meteo**: Free weather forecast API ([open-meteo.com](https://open-meteo.com)).
- **Chart.js**: HTML5 canvas charting library ([chartjs.org](https://www.chartjs.org)).
- **Font Awesome**: User interface vector icons ([fontawesome.com](https://fontawesome.com)).
- **Laravel Framework**: PHP Web Application Framework ([laravel.com](https://laravel.com)).
