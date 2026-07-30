# FoodNanny — Architecture Review (Read-Only)

This is a **multi-restaurant food delivery + admin POS** product (branded FoodNanny in config). The app is a **Vue 3 SPA** on a **single Blade shell**, backed by a **JSON API** (`routes/api.php`) with **Laravel Sanctum** and **Spatie Permission**. There is **no dedicated in-venue POS module** (tables, QR, kitchen KOT, waiter flow) yet—only a **counter POS** and **online order** flows.

---

## Step 1 — Tech Stack

| Area | Finding |
|------|---------|
| **Laravel** | **12.x** (`laravel/framework: ^12.0`) |
| **PHP** | **^8.2** (`composer.json`) |
| **Database** | MySQL-style schema via migrations (SQLite used in composer post-install script); **78 migrations**, core tables for users, restaurants, menu/items, orders, payments, permissions, jobs, cache, media |
| **Frontend** | **Vue 3** + **Vue Router** + **Pinia** (with persisted state) + **Vite 6**; **no Livewire/React** in app code |
| **CSS** | **Tailwind CSS 3/4** (Vite + PostCSS); custom theme CSS under `public/themes/default/` |
| **Auth** | **Sanctum** (`auth:sanctum`), web guard for credential check, API token on login; email/phone login; OTP signup flows |
| **Roles & permissions** | **spatie/laravel-permission** (`guard_name: sanctum`) |
| **Queue** | Default **`database`** driver; `jobs` table; dev script runs `queue:listen` |
| **Events** | Many domain events + listeners under `app/Events`, `app/Listeners`; **broadcasting** configured (Pusher/Reverb) but default **`BROADCAST_CONNECTION=null`**; only **`NewChatMessage`** implements `ShouldBroadcastNow` |
| **Broadcasting** | `pusher/pusher-php-server`, `laravel-echo`, `pusher-js`; admin Pusher settings UI |
| **PWA** | **silviolleite/laravelpwa**; manifest route, `@laravelPWA` in `master.blade.php`, `public/serviceworker.js` |
| **QR codes** | **No QR package** in `composer.json` or app references |
| **POS-related packages** | No dedicated POS SDK; POS is **custom** (controllers/services/Vue). Payments: Stripe, PayPal, Razorpay, Mollie, etc. |
| **Other notable packages** | Spatie Media Library, Maatwebsite Excel, Firebase (push), OpenAI client, GeoScope, many payment gateways |
| **Dev quality tools** | Debugbar, Query Detector (N+1 hints in dev) |

---

## Step 2 — Project Structure

### High-level layout

```
app/
  Enums/           # Role, OrderStatus, OrderType, permissions, payments, etc.
  Events/          # Order, OTP, restaurant notifications, chat broadcast
  Listeners/       # SMS/email/push handlers (see Step 8 — wiring concern)
  Libraries/       # AppLibrary helpers
  Models/          # Eloquent + many Frontend* aliases on same tables
  Models/Scopes/   # RestaurantScope (multi-tenant filter)
  Observers/       # Restaurant-scoped models + OrderObserver
  Services/        # Primary business layer (~114 classes)
  Http/
    Controllers/   # Admin, Frontend, Auth, Installer (~133)
    Middleware/    # installed, apiKey, localization, permission, verify email
    Requests/      # ~102 Form requests
    Resources/     # ~153 API transformers
  Exports/         # Excel exports
  Traits/          # Translatable, seeders, DefaultAccess, etc.

database/migrations, seeders, factories
resources/js/      # Vue SPA (components, stores, router, enums)
resources/views/   # master.blade.php SPA shell, installer, emails, payment blades
routes/api.php     # Almost all application API
routes/web.php     # SPA catch-all, installer, payment callbacks, manifest
public/            # themes, build, serviceworker.js, firebase-messaging-sw.js
```

### Pattern inventory

| Layer | Present? | Notes |
|--------|----------|--------|
| **Models** | Yes | 84 under `app/Models`; duplicate **Frontend\*** models map to same tables (e.g. `FrontendOrder` → `orders`) |
| **Controllers** | Yes | Thin; delegate to services |
| **Middleware** | Yes | Spatie `permission`, custom `apiKey`, `installed` |
| **Services** | Yes | **Main business layer**; no repository folder |
| **Repositories** | **No** | |
| **Helpers** | `App\Libraries\AppLibrary` | |
| **Traits** | Yes | Translatable, DefaultAccess, HasModelMeta, seeder traits |
| **Events** | Yes | Order lifecycle, OTP, chat |
| **Jobs** | **No** `app/Jobs` | Queue used via event/listener patterns if wired |
| **Policies** | **No** `app/Policies` | Authorization = Spatie permissions on controllers |
| **Requests** | Yes | Strong validation on API |
| **Resources** | Yes | Large API surface |
| **Components** | **Vue components**, not Laravel Blade components | 340+ `.vue` files |
| **Blade** | Minimal | Shell + installer + payment + emails |

---

## Step 3 — Database Review

### Core relationship diagram (text)

```
users ──────────────┬──< orders (user_id, delivery_boy_id)
  │                 │       ├──< order_items >── items
  │                 │       ├── order_addresses (1:1)
  │                 │       ├── order_coupons (1:1)
  │                 │       ├── order_pos_details (1:1, POS payments)
  │                 │       └── transactions (1:1)
  │                 │
  ├── roles (spatie model_has_roles)
  ├── addresses
  └── delivery_locations (delivery boys)

restaurants ──────┬── user_id (primary owner user)
                  ├──< items >── item_categories
                  │     ├── item_variations, item_extras, item_addons
                  │     └── taxes
                  ├──< orders
                  ├── restaurant_cuisines >── cuisines
                  ├── order_setups, time_slots
                  └── reviews (morph)

roles / permissions (spatie tables)
menus, menu_sections, menu_templates  → admin sidebar (not food menu)
settings, media, translations, …
```

### Domain checklist

| Concept | In DB? | Notes |
|---------|--------|--------|
| **Restaurants** | Yes | `restaurants` |
| **Users** | Yes | `users` + `restaurant_id` for scoping |
| **Roles** | Yes | Spatie `roles`; seeded: Admin, Restaurant Owner, Delivery Boy, Customer, **Staff** |
| **Orders** | Yes | `orders` |
| **Order items** | Yes | `order_items` |
| **Products / menu** | Yes | `items`, `item_categories`, variations/extras/addons |
| **Categories** | Yes | `item_categories` |
| **Tables (dining)** | **No** | `OrderType::DINING_TABLE = 20` exists in code only |
| **Kitchen / KOT** | **No** | No `kitchen_orders`, `kot`, or station tables |
| **Waiters** | **No** | Closest: **Staff** role + **Employee** admin module |
| **Payments** | Yes | `transactions`, gateways, `order_pos_details` for POS |
| **POS** | Partial | Orders with `order_type = POS`, `order_pos_details` |
| **Inventory** | **No** | No stock/ingredient tables |

### Missing tables for a full restaurant POS (dine-in)

Suggested additions (conceptual):

- `restaurant_tables` (name, capacity, zone, status, `restaurant_id`)
- `table_qr_codes` or QR payload on tables (`uuid` / signed URL)
- `dining_sessions` or link `orders.table_id`, `waiter_id`, `session_token`
- `kitchen_tickets` / `kot_items` (order_id, status, station, timestamps)
- Optional: `order_status_logs`, `order_item_kitchen_status`
- Indexes: `(restaurant_id, status)`, `(table_id, active)`, `(order_id)` on KOT

---

## Step 4 — Existing POS System

### Where it lives

| Layer | Files |
|--------|--------|
| API | `PosController`, `PosOrderController`, `PosCategoryController`, `PosOfferController` |
| Service | `OrderService::posOrderStore`, `changeStatus`, `list` |
| Request | `PosOrderRequest` + `ValidJsonOrder` |
| Vue | `resources/js/components/admin/pos/PosComponent.vue`, `posCart` store, `PaymentComponent`, `PosReceiptComponent` |
| Orders UI | `posOrders/*`, status flow on `PosOrderShowComponent` |

### How orders are created

1. Staff/owner opens **`/admin/pos`** (permission `pos`).
2. Cart is **client-side** (`usePosCartStore`, Pinia **persisted**).
3. Checkout opens payment modal; POST **`/api/admin/pos`** → `OrderService::posOrderStore`.
4. Server creates `Order` with `order_type = POS`, `source = POS`, status **`ACCEPT`**, **`payment_status = PAID`**, inserts `order_items` from JSON, creates **`order_pos_details`**, sets `order_serial_no`.

**Reuse candidates:** `PosOrderRequest` validation, item JSON shape, `OrderItem` insert logic, tax/totals from Vue, `OrderPosDetail`, `changeStatus` for kitchen-like states, `PosReceiptComponent` / print, `StatementCalculationService::restaurantPOS` on delivered, `RestaurantScope` + `DefaultAccess`.

**Caution:** `posOrderStore` hardcodes `'user_id' => 2` (walk-in customer placeholder)—should be parameterized for waiter/table flows.

### Payments

- POS payment methods: `App\Enums\PosPaymentMethod` (cash, card, mobile banking, other).
- Stored in **`order_pos_details`** (method, note, received_amount).
- Order row also gets payment fields; POS creation sets paid immediately.

### Cart logic

- **Frontend only** until submit; merge logic for variations/extras in `posCart.js`.
- Discount (fixed/percent) applied in Vue before API call.

### Order status flow (POS)

Enum: `PENDING → ACCEPT → PREPARING → PREPARED → … → DELIVERED` (POS uses **ACCEPT** on create, then UI: **Preparing → Prepared → Delivered**).

`OrderService::changeStatus` allows POS/TAKEAWAY to reach **DELIVERED**; triggers email/SMS/push **dispatch** (listener wiring issue below).

This is **admin POS order detail**, not a separate kitchen display.

### Invoice / receipt

- **`PosReceiptComponent`** + `vue3-print-nb` on POS order show.

### Kitchen integration

- **None.** Online orders use same status enum; no KOT entity or kitchen-only UI.

---

## Step 5 — Authentication

### Login system

- **Email + password** or **phone + country_code + password** → `LoginController`.
- **Sanctum** personal access token returned with **menus + permissions** split (admin vs restaurant).
- **`DefaultAccessService`** stores selected `restaurant_id` for multi-restaurant users.
- API routes use **`installed`**, **`x-api-key`** (`VITE_API_KEY`), **`auth:sanctum`**, Spatie **`permission:`** on controllers.
- Signup: customer, restaurant owner, delivery boy, guest (OTP).

### Current roles (seeded)

| ID | Name | Enum constant |
|----|------|----------------|
| 1 | Admin | `Role::ADMIN` |
| 2 | Restaurant Owner | `Role::RESTAURANT_OWNER` |
| 3 | Delivery Boy | `Role::DELIVERY_BOY` |
| 4 | Customer | `Role::CUSTOMER` |
| 5 | Staff | `Role::STAFF` |

### Required roles — existence

| Role | Exists? |
|------|---------|
| Restaurant Owner | **Yes** |
| Customer | **Yes** |
| Waiter | **No** (use **Staff** or new role) |
| Chef | **No** |
| Kitchen | **No** (role or station) |

### Adding roles safely

1. Add constant to `app/Enums/Role.php` (IDs must stay aligned with seeder order if using numeric `Role::find()`).
2. Seed in `RoleTableSeeder`.
3. Add permissions in `PermissionTableSeeder` + assign in `RolePermissionTableSeeder`.
4. Extend `RoleService::$roleArray` only if role should be hidden from custom role UI.
5. Add menus in menu seeders / `MenuService` if needed.
6. Vue: routes + `permissionUrl` + stores.
7. **`EmployeeService`** blocks assigning Admin, Owner, Delivery Boy, Customer—new waiter/chef roles can be assignable via Employee UI if permitted.

**Staff** already has POS + online orders + items (read)—closest to **waiter**, but not labeled or scoped to table service.

---

## Step 6 — Restaurant Module

| Feature | Implementation |
|---------|----------------|
| **Restaurant CRUD** | `RestaurantController` + `RestaurantService`; admin permissions `restaurants_*` |
| **Link owner user** | `RestaurantController::userStore` → creates/updates user, assigns **Restaurant Owner**, sets `restaurants.user_id` |
| **Restaurant owners module** | `RestaurantOwnerController` (separate admin CRUD for owner accounts) |
| **Restaurant settings (owner)** | `MyRestaurantController` + `MyRestaurantService`; permission `restaurant-settings` |
| **Restaurant switch** | `RestaurantSwitchController` for users with multiple restaurants |
| **Dashboard** | `DashboardController`: `restaurantOwnerOverview`, popular items, customer stats |
| **Restaurant users** | Owner via `userStore`; **Staff** via **Employees** (admin `employees_*`, not granted to owner in seeder) |
| **Permissions** | Spatie; owner gets POS, online orders, items, coupons, reports, etc. (see `RolePermissionTableSeeder`) |
| **Frontend discovery** | `Frontend\RestaurantController`, menus, checkout, reviews |

**Gap:** Owner **cannot** manage waiter accounts in-app unless you grant `employees_*` to owners or add a **restaurant-scoped staff** module.

---

## Step 7 — Performance Review

| Issue | Evidence / risk |
|-------|------------------|
| **N+1** | Many lists use `with()` (e.g. orders + transaction + orderItems); not universal—audit hot lists (restaurant index, item lists). **Query Detector** in dev helps. |
| **Heavy queries** | `Restaurant::scopeWithReviewRating` uses correlated subqueries; geo distance queries |
| **Duplicate models** | `Order` vs `FrontendOrder`, many `Frontend*` duplicates—confusing, double maintenance |
| **Duplicate logic** | POS cart vs frontend cart (`FrontendCartComponent`)—similar item merge rules |
| **Large Vue files** | `PosComponent.vue` ~560 lines; several admin show components are large |
| **Service size** | `OrderService`, `RestaurantService`, `StatementCalculationService` are large monoliths |
| **Global scopes** | `RestaurantScope` on Order/Item/etc.—good for tenancy; watch admin cross-restaurant reports |
| **Missing indexes** | Orders: consider `(restaurant_id, status, order_datetime)`; items: `(restaurant_id, item_category_id, status)` |
| **Events not wired** | `EventServiceProvider` only registers `Registered`; **`OrderPlaced*` listeners not mapped**—dispatches may no-op for email/SMS/push (verify in runtime) |
| **POS hardcoded user_id** | Extra DB/API noise |
| **PWA cache list** | SW caches `/css/app.css`, `/js/app.js` but app uses **Vite build** paths—stale/offline strategy weak |
| **Unused/dead** | No `app/Jobs`; `DINING_TABLE` barely referenced; policies unused |

**Improvements (later):** Eager-load standards, extract POS/order commands, index migrations, wire or remove dead listeners, cache settings/menus, chunk Vue POS, queue heavy notifications explicitly, fix SW asset list for Vite.

---

## Step 8 — Code Quality

| Topic | Assessment |
|-------|------------|
| **SOLID** | Controllers thin; **services are god-classes**; enums help |
| **Laravel practices** | Form requests, API resources, route middleware attributes, observers for restaurant_id |
| **Security** | API key on all API calls; rate limits in `AppServiceProvider`; permissions on controllers; **PosOrderRequest::authorize() always true**; **mass assignment** on models with fillable—OK if requests validate; **shared API key in frontend env** is weak for public clients |
| **Authorization** | Permission-based, not policies; restaurant scope via middleware + default access |
| **Transactions** | POS order uses `DB::transaction` (note: outer `rollBack` on inner closure errors—review pattern) |
| **Service layer** | **Yes**, primary pattern |
| **Repository** | **No** |
| **Standards** | Laravel Pint in dev; consistent Dipokhalder-style structure |

---

## Step 9 — PWA

| Capability | Status |
|------------|--------|
| **Manifest** | Yes — `config/laravelpwa.php`, dynamic `ManifestController`, route `manifest.json` |
| **Service worker** | Yes — `public/serviceworker.js`, registered in `laravelpwa/meta.blade.php` |
| **Install prompt** | Yes — `FrontendPWAComponent.vue` (`beforeinstallprompt`) |
| **Offline** | Partial — `/offline` route; fetch fallback; **limited** for Vue SPA/API |
| **Caching** | Basic static list; **not aligned with Vite hashed assets** |
| **Admin PWA settings** | `pwas` table + seeder + admin UI |
| **Firebase SW** | `public/firebase-messaging-sw.js` (push, not app shell) |

**To be fully PWA-ready for POS/waiter/kitchen:** extend SW for API caching strategy (network-first for mutations), precache Vite manifest, offline queue for orders, HTTPS, icons from admin PWA settings, optional background sync—not just install banner.

---

## Step 10 — Requirements vs Existing

| # | Requirement | Status | Notes |
|---|-------------|--------|--------|
| 1 | Owner manages waiter accounts | **Partially** | Admin **Employees** → **Staff** role; **not** owner-scoped |
| 2 | Kitchen/Chef login | **Missing** | No role/UI |
| 3 | Waiter login | **Partially** | **Staff** + POS permissions |
| 4 | Table management | **Missing** | No tables |
| 5 | QR per table | **Missing** | No package/table |
| 6 | Customer scans QR, views menu | **Partially** | Frontend restaurant/menu exists; **not** QR/table session |
| 7 | Waiter creates order | **Partially** | POS create; not waiter/table-specific |
| 8 | Waiter edits order | **Missing** | POS is create-only; no edit API |
| 9 | Waiter sends to kitchen | **Missing** | No KOT/send action |
| 10 | Kitchen receives KOT | **Missing** | |
| 11 | Kitchen accepts | **Missing** | |
| 12 | Kitchen Preparing | **Partially** | `OrderStatus::PREPARING` on **online/POS admin** flow, not kitchen app |
| 13 | Kitchen Ready | **Partially** | `PREPARED` exists |
| 14 | Waiter status update | **Missing** | No realtime waiter channel |
| 15 | Waiter marks Served | **Partially** | POS uses **Delivered** as final step |
| 16 | Reuse POS logic | **Partially** | Strong reuse for cart, items, payments, receipt |
| 17 | Compatible with updates | **Partially** | Extend services/enums; avoid forking core POS |
| 18 | Improve performance | **Partially** | Baseline works; indexes/N+1/SW needed |
| 19 | Whole project PWA-ready | **Partially** | Frontend install exists; admin/kitchen need SW strategy |

### Missing work — indicative touchpoints

| Feature | Files / layers | Tables | Complexity |
|---------|----------------|--------|------------|
| Waiter management (owner) | New `RestaurantStaffController` or extend `EmployeeService` with owner permissions; Vue owner settings | Maybe none | **M** |
| Waiter/Chef/Kitchen roles | `Role.php`, seeders, `RolePermissionTableSeeder`, menus, routes | — | **S–M** |
| Tables + QR | Migration, `RestaurantTable` model, admin CRUD, QR lib (`simplesoftwareio/simple-qrcode` or similar) | `restaurant_tables` | **M** |
| QR menu view | `Frontend` route (public), controller, Vue menu by table token | `table_id` on session/order | **M** |
| Waiter order CRUD | Extend `OrderService` or `PosOrderService`; PATCH items; link `waiter_id`, `table_id` | `orders` columns | **L** |
| KOT | `KitchenTicketController`, service, Vue KDS | `kitchen_tickets` | **L** |
| Realtime | Broadcast `OrderStatusUpdated`, Echo channels per restaurant | — | **M** |
| PWA hardening | `serviceworker.js`, Vite integration, optional workbox | — | **M** |

---

## Step 11 — Development Plan (Phased Roadmap)

### Phase 1 — Foundation & roles
- Add **Waiter**, **Chef** (or **Kitchen**) roles + permissions; decide **Staff** vs new roles.
- Owner-scoped **staff CRUD** (restaurant_id, role assignment).
- Document status mapping: KOT vs existing `OrderStatus`.

### Phase 2 — Data model for dine-in
- Migrations: **tables**, optional **sessions**, `orders.table_id`, `orders.waiter_id`, `order_type = DINING_TABLE`.
- Fix POS **`user_id`** placeholder; auth audit for new roles.

### Phase 3 — Table management & QR
- Admin/owner table CRUD.
- Generate QR → public URL (table + restaurant slug/uuid).
- Customer **read-only menu** page (reuse frontend item APIs).

### Phase 4 — Waiter app (reuse POS)
- Waiter login + slim UI (mobile-first).
- Reuse **`posCart`**, **`PosOrderRequest`**, item APIs; add **edit order**, **table binding**, **send to kitchen** flag.

### Phase 5 — Kitchen / KOT
- KOT creation on send; kitchen list/filter by restaurant.
- Status: **Accepted → Preparing → Ready** (map to or extend `OrderStatus` / item-level status).
- Print KOT (reuse print patterns).

### Phase 6 — Realtime
- Enable Pusher/Reverb; broadcast status changes; Echo on waiter + kitchen UIs.
- Optional: reuse **`NewChatMessage`** pattern for private channels.

### Phase 7 — Payments & statements
- Reuse **`order_pos_details`** and **`StatementCalculationService`** for dine-in/POS variants.
- Owner reporting by table/waiter.

### Phase 8 — PWA
- Fix SW asset caching for Vite; offline shell for waiter/kitchen routes; install prompts on those apps.
- Keep Firebase for push where needed.

### Phase 9 — Optimization & quality
- DB indexes; eager loads; split large services; wire **event listeners** or remove dead dispatches.
- Security review (API key, policies for table tokens).
- Tests for order/KOT state machine.

---

## Summary

FoodNanny is a **mature Laravel 12 + Vue 3 delivery platform** with a **working counter POS** (cart, payment, receipt, order list, status through Prepared/Delivered). It does **not** yet implement **dine-in tables, QR, kitchen KOT, waiter lifecycle, or owner-managed waiters**. The best reuse path is **`OrderService` / POS Vue cart / `order_items` / `order_pos_details` / permissions / restaurant scoping**, extended with new tables and roles rather than replacing the stack.

No code was modified. When you approve, we can start with a phase you prefer (typically **Phase 1 + 2**, then **4–6** for core POS dining flow).
