# CargoTrace - Software Architecture & Engineering Documentation

This document describes the software architecture, design patterns, security mechanisms, and service layer implementations of **CargoTrace** in strict accordance with the Web Programming 2 UAS guidelines.

---

## 1. Architectural Pattern (Layered Architecture)

In adherence to **Slide 15**, CargoTrace cleanly separates HTTP transport concerns from core business logic:

```
[ Client / Web Browser / REST Client ]
                 │
                 ▼
[ Routes & HTTP Middleware ]
  ├── Web Session Auth & CSRF Protection
  ├── API Sanctum Token Authentication
  └── Role-Based Access Control (RBAC)
                 │
                 ▼
[ Controllers ] (Thin Controllers)
  ├── AuthController / ApiAuthController
  ├── DashboardController
  ├── ShipmentController / ApiShipmentController
  ├── TrackingController
  ├── ExceptionController
  ├── CarrierController
  └── ReportController
                 │
                 ▼
[ Business Service Layer ] (Fat Services)
  ├── ShipmentService    : Lifecycle transitions, sequential validation, search/filtering
  ├── TrackingService    : Chronological event validation, current location updates
  ├── EtaService         : Deterministic delay comparison & group threshold calculation
  ├── ExceptionService   : Incident triage, customer notification, resolution metrics
  ├── ExternalApiService : Open-Meteo Weather API & OpenStreetMap Geocoding (with fallbacks)
  └── AuditService       : Immutable event auditing
                 │
                 ▼
[ Domain Models & Eloquent ORM ]
  └── Query Scopes, Relationships, Accessors, Casts
                 │
                 ▼
[ Persistence & Data Stores ]
  ├── Relational DB (MySQL 8+ / SQLite)
  └── Cache Driver (File / Database / Redis)
```

---

## 2. Business Services Specification

### A. `ShipmentService`
- **Unique Identification**: Generates standardized identifiers in the format `SHP-YYYY-XXXXXX` (e.g., `SHP-2024-001284`).
- **State Machine Enforcement**:
  $$\text{CREATED} \longrightarrow \text{BOOKED} \longrightarrow \text{PICKED\_UP} \longrightarrow \text{IN\_TRANSIT} \longrightarrow \text{DELIVERED}$$
  Disallows illegal state jumps (e.g. `CREATED` $\rightarrow$ `DELIVERED`). Terminal status `DELIVERED` is strictly locked.
- **Customer Isolation**: Automatically filters queries so customer accounts can only view consignments belonging to their company.

### B. `EtaService`
- **Deterministic Delay Rule**:
  Calculates difference between SLA expected arrival ($T_{\text{expected}}$) and current estimated arrival ($T_{\text{estimated}}$):
  $$\Delta t = \max\left(0, \frac{T_{\text{estimated}} - T_{\text{expected}}}{3600}\right) \text{ hours}$$
- **Thresholds**:
  - $0 < \Delta t \le 6\text{h}$: **LOW**
  - $6 < \Delta t \le 24\text{h}$: **MEDIUM**
  - $\Delta t > 24\text{h}$: **HIGH**
- **Group Scenarios**:
  - Group A: 12-hour high threshold.
  - Group B: 18-hour high threshold.
  - Group C: 24-hour high threshold.

### C. `TrackingService`
- Validates chronological monotonicity ($t_{\text{new}} \ge t_{\text{latest}}$).
- Automatically updates the shipment's `current_location_id` and latest waypoint.
- Dispatches in-app notifications to consignees.

### D. `ExternalApiService`
- Interfaces with the **Open-Meteo Weather Forecast API** without requiring paid keys.
- Implements 30-minute caching to optimize performance and prevent rate limiting.
- Handles network timeouts and returns deterministic fallback weather telemetry if external networks are unreachable.

---

## 3. Security Hardening (Slide 16)

1. **Authentication**:
   - Session-based cookie auth with CSRF tokens for web interactions.
   - Laravel Sanctum Bearer tokens for headless REST API clients.
2. **Authorization**:
   - Server-side role validation on every route and API endpoint.
   - Backend prevents customer access to other customers' data regardless of URL tampering.
3. **Input Validation**:
   - Strict `validate()` rules on all incoming payloads; returns HTTP 422 with structured JSON error fields.
4. **Injection Prevention**:
   - Eloquent PDO parameter binding completely mitigates SQL injection.
   - Blade `{{ $variable }}` auto-escapes outputs to prevent Cross-Site Scripting (XSS).
5. **No Secrets in Repository**:
   - All environment variables are stored in `.env`, ignored by `.gitignore`.
