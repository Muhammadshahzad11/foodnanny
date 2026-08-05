# Cost to Cost Foods  
## Complete Feature & Workflow Document  
### What’s built, how it works, and what each person does next

**Prepared for:** Cost to Cost Foods (client review)  
**Product:** Cost to Cost Foods — restaurant ordering & dine-in operations platform  
**Date:** August 2026  
**Delivery status:** Modules 1–8 complete and ready for walkthrough  

---

## A short note before the details

This document is meant for you — the client — so you can see the full picture of what’s sitting in the system today. Not just the big headlines (waiter app, kitchen board, QR), but also the smaller pieces that take real time: status rules, role permissions, install prompts, demo data, notification deep links, print sheets, error messages, dashboard polish, and the “what happens after I click this?” paths for every role.

We started from a solid FoodNanny-style multi-restaurant base (online ordering, POS, menu, reports, settings, etc.) and built a full **dine-in operations layer** on top of it for Cost to Cost Foods. Staff can run the floor and kitchen from the same admin app. Guests can order from the table QR. Everything below is live in the build we demo.

If something below says **Complete**, it means it is implemented and usable — not a mockup.

---

## 1. Big picture — what the product does now

Cost to Cost Foods is no longer “online orders only.” It is a combined system:

1. **Public website / customer ordering** — browse restaurant, place online orders, account area  
2. **Admin & restaurant management** — menu, settings, staff, reports, promos, payments setup  
3. **Counter POS** — cashier takes walk-in / takeaway orders  
4. **Dine-in stack (Modules 1–8)** — employees, tables, QR, waiter pad, kitchen display, live notifications, PWA install for staff phones/tablets  

### The main dine-in story (one sentence)

Owner sets up staff and tables → QR goes on the tables → waiter (or guest) creates an order → kitchen cooks it on a live board → waiter gets notified when food is ready → guest is served.

### The same story as a flow

```
SETUP (Owner / Manager)
  Create employees (waiter, chef, cashier…)
  Create floor tables
  Generate & print QR codes
           │
           ▼
SERVICE
  Guest scans QR  ──or──  Waiter opens table on phone/tablet
           │
           ▼
  Order created (draft → sent to kitchen)
           │
           ▼
KITCHEN
  Pending → Accept → Preparing → Ready
           │
           ▼
FLOOR
  Waiter notified → serves table
           │
           ▼
SETTLEMENT (existing POS / cashier process as needed)
```

---

## 2. Who logs in where

**Staff login page:** `/login`  
**Demo password for all seeded accounts:** `123456`

| Person | Email | Needs login? | Main place they work |
|--------|-------|--------------|----------------------|
| Platform admin | `admin@example.com` | Yes | Full admin — monitoring, settings, everything |
| Restaurant owner | `owner@example.com` | Yes | Restaurant setup, employees, tables, overview |
| Manager | `manager@example.com` | Yes | Ops screens they are permitted for |
| Waiter | `waiter@example.com` | Yes | `/admin/waiter` and tables board |
| Chef | `chef@example.com` | Yes | `/admin/kitchen` and kitchen queue |
| Cashier | `cashier@example.com` | Yes | `/admin/pos` |
| Online customer | their own signup | Yes (for account features) | Website: orders, profile, addresses |
| Dine-in guest at table | — | **No** | Scan QR → `/t/{token}` — no staff password |

Guests at the table never need a staff login. Staff always do.

---

## 3. What’s complete — checklist from major to minor

### A. Major modules delivered for dine-in (custom work)

| # | Module | Status | In plain English |
|---|--------|--------|------------------|
| 1 | Employees | **Complete** | Hire and manage Waiter / Chef / Cashier / Manager under the restaurant |
| 2 | Restaurant tables | **Complete** | Floor plan data: number, zone, capacity, status lifecycle |
| 3 | Table QR codes | **Complete** | Generate, regenerate, download, bulk, print; guest entry link |
| 4 | Waiter ordering | **Complete** | Dashboard, live tables, order pad, send to kitchen, draft cancel |
| 5 | Kitchen / KDS | **Complete** | Live queue, Accept → Preparing → Ready, reject/cancel, priorities, stations |
| 6 | Demo seed & branding | **Complete** | Cost to Cost Foods branding + demo restaurant, menu, tables, sample orders, test logins |
| 7 | Realtime & inbox | **Complete** | Live boards + notification bell + mark read + deep links |
| 8 | PWA (installable app) | **Complete** | Install prompts, offline shell, update flow, admin PWA settings, shortcuts |

### B. Platform foundation already in the product (base system you also get)

These were part of the underlying platform and remain available under Cost to Cost Foods branding:

- Admin dashboard (sales / orders / balance style overview)  
- Multi-restaurant management  
- Full menu / items (categories, attributes, variations, extras where configured)  
- Restaurant settings (order setup, time slots, taxes, etc.)  
- Online orders handling  
- POS and POS order history  
- Delivery boy / rider side (available & active orders, delivery boy users & settings)  
- Order tracker, returns, refunds, reviews  
- Users: administrators, customers, restaurant owners, delivery boys, employees  
- Roles & permissions  
- Accounts: transactions, payouts, collections, cashouts  
- Promotions: vouchers, coupons, offers, campaigns  
- Communications: push notifications, messages, subscribers  
- Reports: sales, items, credit balance, collection  
- System settings: company, site, mail, Pusher, storage, OTP, theme, languages, payment/SMS gateways, CMS pages, analytics, cookies, and more  
- Customer website: home, restaurant pages, checkout, offers, static pages  
- Customer account: my orders, profile, favorites, addresses, password  

That base is a large product on its own. The dine-in modules sit on top of it so one system covers **online + counter + floor + kitchen**.

### C. Smaller / polish items (easy to overlook, still done)

These are the “glue” pieces clients often don’t list as modules, but they are what make demos feel finished:

- Waiter dashboard redesigned with status tiles and icons  
- Kitchen dashboard matched to the same style  
- Waiter tables board: clear status colors, clickable order numbers  
- Toast alerts moved under the header so they don’t clash with the top bar  
- Longer, readable toast duration  
- Clearer API error text (less generic “Something wrong”)  
- Header icon fixes (notification bell / menus)  
- Optimistic locking on waiter drafts so two waiters don’t silently overwrite each other  
- Table occupancy updates when orders open/close  
- Kitchen priorities: Normal, High, Urgent, VIP  
- Kitchen stations support  
- KOT / ticket print payload for kitchen  
- QR print sheet + PNG/SVG download + generate-missing  
- Guest QR guard rules so table ordering stays consistent with staff flow  
- In-app notification inbox (list, unread count, mark one / mark all)  
- Role-based live channels (kitchen, waiter, owner/admin ops)  
- PWA install popup: Install / Maybe Later / Never Show Again  
- Install App entry points in navbar, sidebar, profile, and settings  
- Branded offline page when the network drops  
- PWA update prompt when a new service worker is waiting  
- Admin PWA settings: name, colors, orientation, display mode, cache strategy, popup timing, force update, icons/splashes  
- Manifest shortcuts for Orders, Tables, Kitchen, Waiter, POS, Notifications  
- Same-origin relative manifest/icons so install works across local ports  
- Demo reseed command for a clean walkthrough anytime  

---

## 4. Module-by-module — how it works and what to do next

### Module 1 — Employees  
**Status: Complete** · **Login:** Owner / Admin / Manager  

**Screen:** `/admin/employees`

**What it is**  
Staff directory for the restaurant. You create people, assign roles (Waiter, Chef, Cashier, Manager), set passwords, optional photo, and keep everything scoped to the restaurant.

**Typical flow**

| Step | You do this | Then this happens | What you do next |
|------|-------------|-------------------|------------------|
| 1 | Open Employees | List of staff loads | Click Add |
| 2 | Fill name, email, phone, role, password | Record is validated | Save |
| 3 | Save | Employee exists and can log in | Send them their email + password |
| 4 | They open `/login` | They land in admin with their permissions | Waiter opens Waiter menu; Chef opens Kitchen; Cashier opens POS |
| 5 | Edit later if needed | Role / password / details update | They re-login if permissions changed |

**After creating a waiter:** don’t stop at “saved.” Give them credentials and confirm they can open `/admin/waiter`.

---

### Module 2 — Restaurant tables  
**Status: Complete** · **Login:** Owner / Admin / Manager  

**Screens:** `/admin/tables` · `/admin/tables/show/{id}`

**What it is**  
Your digital floor. Each table has number, name, capacity, zone, notes, and a live status.

**Statuses you can use**

| Status | Meaning on the floor |
|--------|----------------------|
| Available | Free — good for new seating / new waiter order |
| Occupied | Guests seated / open order |
| Reserved | Held for a booking |
| Cleaning | Being cleared |
| Out of Service | Broken / blocked |
| Inactive | Not shown for normal service |

**Typical flow**

| Step | You do this | Then this happens | What you do next |
|------|-------------|-------------------|------------------|
| 1 | Create tables (number, zone, capacity) | Tables appear for waiters | Generate QR (Module 3) |
| 2 | Change status | Waiter board updates (live if realtime is on) | Tell floor staff if needed |
| 3 | Open table detail | See info + QR tools | Print QR or adjust status |

**After creating tables:** go straight to QR generation. Tables without QR still work for waiter ordering, but guests can’t self-order until QR is printed and placed.

---

### Module 3 — Table QR codes  
**Status: Complete** · **Login:** Staff to generate/print · **Guest:** no login  

**Staff:** on Tables screens  
**Guest entry:** `/t/{token}`

**What it is**  
Each table gets a unique token and QR image. Guest scans → phone opens that table’s dine-in path. Staff can regenerate (old code dies), download PNG/SVG, bulk download, print a sheet, or generate all missing codes in one go.

**Staff flow**

| Step | You do this | Then this happens | What you do next |
|------|-------------|-------------------|------------------|
| 1 | Generate QR on a table | Token + image created | Download or print |
| 2 | Print sheet / stick on table | Physical table is linked | Test with your phone |
| 3 | Scan test | `/t/{token}` opens correct table | Ready for guests |
| 4 | Regenerate if lost/stolen | Old token stops working | Reprint and replace sticker |

**Guest flow**

| Step | Guest does this | Then this happens | Staff does next |
|------|-----------------|-------------------|-----------------|
| 1 | Scans QR | Opens table ordering | — |
| 2 | Places order | Order enters dine-in / kitchen pipeline | Kitchen/waiter handle like other dine-in orders |

**After printing:** physically place codes, then do one real phone scan per zone so nothing points to the wrong table.

---

### Module 4 — Waiter ordering  
**Status: Complete** · **Login:** Waiter (or anyone with waiter permission)  

**Screens**

| Screen | URL |
|--------|-----|
| Waiter hub | `/admin/waiter` |
| Tables board | `/admin/waiter/tables` |
| Order pad | `/admin/waiter/tables/{id}` |
| Order view | `/admin/waiter/orders/{id}` |

**What it is**  
The waiter’s daily tool. See the floor, open a table, build a draft, send it to the kitchen, cancel a draft if the guest changes their mind, and get pinged when food is ready.

**A) Start of shift**

| Step | Waiter does | Then | Next |
|------|-------------|------|------|
| 1 | Login → Waiter | Dashboard counts load | Check Ready first |
| 2 | Open Tables | Color-coded floor | Pick Available table for new guests |

**B) Take an order (main path)**

| Step | Waiter does | Then | Next |
|------|-------------|------|------|
| 1 | Tap Available table | Order pad opens | Add items |
| 2 | Add items / qty / notes | Draft holds the order; table tends toward occupied | Review with guest |
| 3 | **Send to kitchen** | Kitchen sees Pending (live) | Stay aware of notifications |
| 4 | Guest asks for more later | Re-open table / follow restaurant rules for add-ons | Send again as needed |

**C) Draft cancel**

| Step | Waiter does | Then | Next |
|------|-------------|------|------|
| 1 | Cancel draft | Draft gone | Seat someone else if table frees |

**D) When kitchen marks Ready**

| Step | Waiter does | Then | Next |
|------|-------------|------|------|
| 1 | See toast / bell / Ready count | Know which table | Pick up and serve |
| 2 | Serve guest | Food delivered | Continue floor; settlement via cashier/POS as your house rules say |

**Demo tip:** some seeded demo tickets are already **Prepared** and locked. For a clean live demo, always start from an **Available** table.

**After Send to kitchen:** your job pauses on cooking — the chef owns Accept → Preparing → Ready. You come back when Ready hits.

---

### Module 5 — Kitchen / KDS  
**Status: Complete** · **Login:** Chef (or kitchen permission)  

**Screens**

| Screen | URL |
|--------|-----|
| Kitchen hub | `/admin/kitchen` |
| Queue board | `/admin/kitchen/queue` |
| Order detail | `/admin/kitchen/orders/{id}` |

**Pipeline**

```
Pending → Accepted → Preparing → Ready
              ↓           ↓         ↓
          Reject     Reject/Cancel  Cancel (when allowed)
```

**Cook flow**

| Step | Chef does | System | Who acts next |
|------|-----------|--------|---------------|
| 1 | Open Kitchen Queue | Tickets stream in live | — |
| 2 | **Accept** on Pending | Status → Accepted | Start prep when ready |
| 3 | **Preparing** | Status → Preparing | Cook |
| 4 | **Ready** | Status → Ready | **Waiter serves** |
| 5 | Print ticket (optional) | KOT payload | Physical ticket if you use printers |
| 6 | Reject / Cancel with reason | Order stops; floor informed | Waiter talks to guest |

Priorities (Normal / High / Urgent / VIP) help the line decide what jumps the queue. Stations support splitting work conceptually across prep areas.

**After Ready:** kitchen’s part for that ticket is done. Waiter must pick up. Don’t leave Ready tickets hanging without floor coverage.

---

### Module 6 — Demo seed & Cost to Cost Foods branding  
**Status: Complete** · **Who runs it:** developer / ops on the server (not daily floor staff)

```bash
php artisan db:seed --class=ModuleTestDataSeeder
```

**What you get after seeding**

- Branding as **Cost to Cost Foods**  
- Demo restaurant, menu items, kitchen stations  
- About 12 tables with zones and QR tokens  
- Sample kitchen scenarios (`DEMO-KDS`)  
- Sample waiter draft (`DEMO-WAITER`)  
- Sample POS → kitchen order (`DEMO-POS`)  
- All role accounts with password `123456`

**After seeding:** log in as waiter and chef in two browsers and run the live path once so you trust the demo before showing it.

---

### Module 7 — Realtime & notifications  
**Status: Complete** · **Login:** any staff role that sees the admin header  

**What it is**  
The “no shouting across the restaurant” layer.

- Kitchen queue and waiter tables refresh when events fire  
- Navbar bell + inbox  
- Unread count, mark read, mark all read  
- Click a notification → jump to the related order/table  
- Role channels so chefs aren’t flooded with unrelated waiter noise (and the reverse)

**Needs:** Pusher (or your configured broadcast driver) correctly set in the environment. Without it, screens still work — they just won’t feel instant.

**After turning realtime on:** open waiter + chef side by side and send one order. If the chef screen updates without refresh, you’re good.

---

### Module 8 — Progressive Web App (install on phone / tablet / desktop)  
**Status: Complete** · **Login:** not required to see install UI on the site; staff login still required for admin screens  

**What it is**  
Staff (and the site) can install Cost to Cost Foods like an app: home screen icon, standalone window, offline shell if the network drops, update prompt when we ship a new service worker.

**Includes**

- Install popup (Install / Maybe Later / Never Show Again)  
- Install App buttons in admin chrome and settings  
- `/manifest.json` with icons, theme, shortcuts (Orders, Tables, Kitchen, Waiter, POS, Notifications)  
- Service worker with cache strategies (API traffic not cached — live data stays live)  
- Branded `/offline` page  
- Admin → System Settings → Progressive Web App for name, colors, display, orientation, cache, popup timing, force update  
- Push / background-sync left as ready stubs (architecture hooks, not a full Firebase rewrite)

**Staff flow**

| Step | User does | Then | Next |
|------|-----------|------|------|
| 1 | Browse site/admin in Chrome or Edge | Install popup may appear | Install, Later, or Never |
| 2 | Click Install App | Browser install dialog (when Chrome allows it) | Confirm install |
| 3 | Open from home screen / app icon | Standalone app shell | Login as usual for admin work |
| 4 | Go offline briefly | Offline page / cached shell | Back online → continue |

**Note for demos:** Chrome only offers true one-click install when the page is installable (service worker + manifest OK). Use Chrome/Edge. iPhone uses Share → Add to Home Screen. Stay on one host/port while testing locally.

**After install:** treat it like the normal admin URL — login still required for waiter/kitchen. Install does not bypass security.

---

### Related: Counter POS (existing, wired into the kitchen story)  
**Status: Available** · **Login:** Cashier  

**Screen:** `/admin/pos`

Cashier builds a counter/takeaway order → it can show on the kitchen board (demo includes `DEMO-POS`) → chef runs the same Accept → Preparing → Ready path.

**After POS send:** kitchen owns cooking; cashier owns payment/handoff at the counter.

---

## 5. Role-by-role daily picture

### Owner / Manager — morning setup
1. Login  
2. Check employees (anyone new? passwords shared?)  
3. Check tables statuses (anything Out of Service?)  
4. Confirm QRs still on tables  
5. Spot-check kitchen queue and waiter board  

### Waiter — service
1. Login → Waiter dashboard  
2. Clear Ready tickets first  
3. Seat guest → Available table → order pad  
4. Send to kitchen  
5. Watch bell / Ready count → serve  

### Chef — line
1. Login → Kitchen Queue  
2. Work Pending by priority  
3. Accept → Preparing → Ready  
4. Reject only with a clear reason  

### Cashier — counter
1. Login → POS  
2. Take walk-in / takeaway  
3. Coordinate with kitchen board  

### Guest — table
1. Scan QR  
2. Order on phone  
3. No login  

---

## 6. “I just finished X — what now?” cheat sheet

| Just finished… | Do this next… |
|----------------|---------------|
| Created employee | Give login; confirm they open the right menu |
| Created table | Generate QR |
| Generated QR | Print, stick on table, test scan |
| Guest scanned QR | Let them order; kitchen/waiter handle ticket |
| Waiter added items | **Send to kitchen** (draft alone doesn’t cook) |
| Waiter sent to kitchen | Chef Accepts on KDS |
| Chef Accepted | Move to Preparing when cooking starts |
| Chef Preparing | When plated → Ready |
| Chef Ready | Waiter serves |
| Order rejected | Waiter informs guest / reorder |
| Draft cancelled | Table can free up for next guests |
| Got a notification | Open it → handle that table/order |
| Installed PWA | Login and use Waiter/Kitchen as normal |
| Seeded demo data | Run waiter→chef path on an Available table |

---

## 7. Main screens map (bookmark this)

| Area | Path |
|------|------|
| Login | `/login` |
| Dashboard | `/admin/dashboard` |
| Employees | `/admin/employees` |
| Tables + QR | `/admin/tables` |
| Waiter hub | `/admin/waiter` |
| Waiter tables | `/admin/waiter/tables` |
| Waiter order pad | `/admin/waiter/tables/{id}` |
| Kitchen hub | `/admin/kitchen` |
| Kitchen queue | `/admin/kitchen/queue` |
| POS | `/admin/pos` |
| Online orders | `/admin/online-orders` |
| PWA settings | `/admin/system-settings/pwa` (path may match your System Settings menu label) |
| Guest QR entry | `/t/{token}` |
| Offline page | `/offline` |
| Manifest | `/manifest.json` |

Customer site pieces (home, restaurant page, checkout, my orders, etc.) remain available as part of the full platform.

---

## 8. Suggested 15-minute client demo

1. **Owner** — Employees list → Tables → Generate/print QR (2–3 min)  
2. **Waiter** (browser A) — Available table → add 2–3 items → Send to kitchen  
3. **Chef** (browser B) — ticket appears → Accept → Preparing → Ready  
4. **Waiter** — notification / Ready count → open table  
5. **Phone** — scan a test QR, show guest entry  
6. Optional — show Install App / PWA settings; mention offline shell  

Two browsers (or phone + laptop) make the realtime moment obvious.

---

## 9. Honest notes (we’d rather say this upfront)

- Seeded **Prepared** demo tickets are not for re-editing — use free/Available tables for live demos.  
- Full bill close / table release may still follow your POS and house payment process; the dine-in modules focus on seating → order → kitchen → ready.  
- Live sync needs broadcast/Pusher configured in the environment.  
- Permissions decide exactly which menus each login sees.  
- PWA one-click install depends on the browser (Chrome/Edge best). iOS uses Add to Home Screen.  
- Push notification *delivery* beyond the in-app inbox, and full background-sync queues, are stubbed for the next phase — Module 8 delivered install, offline shell, updates, and admin control.

None of that reduces what’s already shipped in Modules 1–8; it’s just clear boundary-setting.

---

## 10. Scope snapshot — why this is a large delivery

Putting it in one place so the size is visible:

**Custom dine-in build (Modules 1–8)**  
Employees · Tables · QR lifecycle · Waiter hub/board/pad · Kitchen hub/queue/detail · Status pipelines · Priorities & stations · Optimistic locking · Occupancy rules · Demo seed & branding · Pusher live updates · Notification inbox & deep links · PWA install/update/offline/manifest/admin settings · UI polish across waiter/kitchen/toasts/errors/icons  

**Plus the full underlying commerce platform**  
Dashboard · Restaurants · Menu · Online orders · POS · Riders · Returns/refunds/reviews · Users & roles · Money movement · Promos · Communications · Reports · Deep system settings · Customer website & account  

That’s not a single screen or a theme pass. It’s an operations product: setup → service → kitchen → notify → install on devices — with training-style flows documented above so your team can actually use it.

---

## 11. One-line status you can quote

> **Cost to Cost Foods is delivery-ready for dine-in operations (Modules 1–8): staff, tables, QR, waiter ordering, kitchen KDS, live notifications, and installable PWA — on top of the full online/POS restaurant platform — with seeded demo accounts for walkthrough.**

---

## 12. Test logins (again, for convenience)

| Role | Email | Password |
|------|-------|----------|
| Admin | `admin@example.com` | `123456` |
| Owner | `owner@example.com` | `123456` |
| Waiter | `waiter@example.com` | `123456` |
| Chef | `chef@example.com` | `123456` |
| Cashier | `cashier@example.com` | `123456` |
| Manager | `manager@example.com` | `123456` |

---

*Document prepared for client sharing and UAT. Safe to attach to email, WhatsApp, or a proposal follow-up. If you want a shorter one-pager or a staff training PDF cut from this, say which audience and we’ll trim it.*
