# Cost to Cost Foods — Feature Flows & User Guide

**Purpose:** Explain every completed module — who logs in, what to do step by step, and what happens next after each action.  
**Status:** Modules 1–7 complete  
**App:** Cost to Cost Foods (Laravel + Vue admin)

---

## Quick login map

| Who | Login email | Password | Where they work |
|-----|-------------|----------|-----------------|
| Admin | `admin@example.com` | `123456` | Full system |
| Restaurant owner | `owner@example.com` | `123456` | Restaurant settings, tables, employees, overview |
| Waiter | `waiter@example.com` | `123456` | `/admin/waiter` |
| Chef | `chef@example.com` | `123456` | `/admin/kitchen` |
| Cashier | `cashier@example.com` | `123456` | `/admin/pos` |
| Manager | `manager@example.com` | `123456` | Management + ops screens they are permitted for |
| Guest (no login) | — | — | Scan table QR → `/t/{token}` |

**Login page:** `/login` (admin SPA after auth)

---

## End-to-end dine-in flow (big picture)

```
Owner/Manager sets up staff + tables + QR
        ↓
Guest scans QR  OR  Waiter opens table
        ↓
Order created (draft / pending)
        ↓
Waiter sends to kitchen  (or guest order reaches kitchen)
        ↓
Chef: Accept → Preparing → Ready
        ↓
Waiter notified → serves food
        ↓
(Optional later) Cashier settles bill / table freed
```

---

# Module 1 — Employees

### Status: ✅ Complete

### Who logs in
- **Owner / Admin / Manager** (users with Employees permission)

### What it does
Create and manage restaurant staff with roles: Waiter, Chef, Cashier, Manager.

### Screen
`/admin/employees`

### Flow

| Step | User action | What happens next |
|------|-------------|-------------------|
| 1 | Open **Employees** | Staff list loads |
| 2 | Click **Add** / Create | Fill name, email, phone, role, password |
| 3 | Save employee | Employee can log in; realtime/inbox may notify ops |
| 4 | Open employee → edit | Update details / role / password / image |
| 5 | Manage addresses (if used) | Address saved on employee profile |
| 6 | Delete / export (if needed) | Staff removed or exported |

### After creating a waiter/chef
1. Give them their email + password  
2. They log in at `/login`  
3. Waiter goes to **Waiter** menu; Chef goes to **Kitchen** menu  

---

# Module 2 — Restaurant tables

### Status: ✅ Complete

### Who logs in
- **Owner / Admin / Manager** (Tables permission)

### What it does
Define the floor: table number, name, capacity, zone, notes, and status.

### Screens
- List: `/admin/tables`  
- Detail: `/admin/tables/show/{id}`

### Table statuses
| Status | Meaning |
|--------|---------|
| Available | Free for seating / new order |
| Occupied | Guests / open order |
| Reserved | Held for booking |
| Cleaning | Being cleared |
| Out of Service | Not usable |
| Inactive | Hidden / disabled |

### Flow

| Step | User action | What happens next |
|------|-------------|-------------------|
| 1 | Open **Tables** | Floor table list |
| 2 | Create table (number, zone, capacity) | Table appears for waiter + QR tools |
| 3 | Change status | Waiter board updates (live if realtime on) |
| 4 | Open table show page | View details / linked QR actions |

### After creating tables
→ Go to **Module 3** and generate QR codes  
→ Waiters will see tables under **Waiter → Tables**

---

# Module 3 — Table QR codes

### Status: ✅ Complete

### Who logs in
- **Owner / Admin / Manager** (to generate/print)  
- **Guest** — no login (scan only)

### What it does
Each table gets a unique QR. Guests scan it to open dine-in ordering for that table.

### Admin screens
- Inside Tables list / table detail (QR modal, generate, download, print)

### Guest entry
`/t/{token}`

### Admin flow

| Step | User action | What happens next |
|------|-------------|-------------------|
| 1 | Open Tables | Select a table |
| 2 | **Generate QR** (or Generate Missing) | Token + QR image created |
| 3 | Download PNG/SVG or print sheet | Print and place on table |
| 4 | Regenerate (if needed) | Old QR token invalidated; print new one |

### Guest flow

| Step | User action | What happens next |
|------|-------------|-------------------|
| 1 | Scan QR on table | Opens `/t/{token}` |
| 2 | App resolves table | Guest enters dine-in ordering for that table |
| 3 | Guest places order | Order goes into restaurant dine-in / kitchen pipeline (per dine-in rules) |

### After printing QR
→ Stick QR on physical table  
→ Test scan with phone  
→ Confirm correct table opens  

---

# Module 4 — Waiter ordering

### Status: ✅ Complete

### Who logs in
- **Waiter** → `waiter@example.com` / `123456`  
- Also Admin/Owner if they have waiter permission

### Screens
| Screen | URL |
|--------|-----|
| Dashboard | `/admin/waiter` |
| Tables board | `/admin/waiter/tables` |
| Order pad | `/admin/waiter/tables/{id}` |
| Order view | `/admin/waiter/orders/{id}` |

### A) Waiter dashboard flow

| Step | User action | What happens next |
|------|-------------|-------------------|
| 1 | Login as waiter | Lands in admin; open **Waiter** |
| 2 | View overview cards | See available / occupied / kitchen / ready counts |
| 3 | Click **Tables** (or a card) | Opens floor board |
| 4 | If Ready Orders > 0 | Serve those tables first |

### B) Take an order (main waiter flow)

| Step | User action | What happens next |
|------|-------------|-------------------|
| 1 | Open **Waiter → Tables** | Color-coded floor tiles load (live) |
| 2 | Tap an **Available** table | Order pad opens for that table |
| 3 | Add menu items / quantities / notes | Items sit in draft order |
| 4 | Save draft (if prompted / auto) | Order stays editable; table becomes occupied |
| 5 | Click **Send to kitchen** | Order status → Pending for kitchen; chef sees it live |
| 6 | Stay on tables board | Watch status / notifications |

### C) Edit / cancel draft

| Step | User action | What happens next |
|------|-------------|-------------------|
| 1 | Re-open table with draft | Order pad loads existing draft |
| 2 | Change items → save | Draft updated (optimistic lock prevents overwrite conflicts) |
| 3 | **Cancel draft** | Draft removed; table can return to available if no open order |

### D) When food is ready

| Step | User action | What happens next |
|------|-------------|-------------------|
| 1 | Receive notification / see Ready count | Toast + bell + dashboard update |
| 2 | Open table / order | Confirm items ready |
| 3 | Serve guest | (Settlement may be cashier/POS depending on restaurant process) |

### Important demo tip
Do **not** edit seeded orders already marked **Prepared**. Use free/available tables for a clean demo.

### After send to kitchen
→ **Chef** must act in Module 5  
→ Waiter waits for Accept / Preparing / Ready updates  

---

# Module 5 — Kitchen / KDS

### Status: ✅ Complete

### Who logs in
- **Chef** → `chef@example.com` / `123456`  
- Admin/Owner with kitchen permission

### Screens
| Screen | URL |
|--------|-----|
| Dashboard | `/admin/kitchen` |
| Queue (KDS board) | `/admin/kitchen/queue` |
| Order detail | `/admin/kitchen/orders/{id}` |

### Kitchen status pipeline

```
Pending  →  Accepted  →  Preparing  →  Ready
                ↓            ↓           ↓
            Reject       Reject/Cancel  Cancel (if allowed)
```

### A) Kitchen dashboard

| Step | User action | What happens next |
|------|-------------|-------------------|
| 1 | Login as chef → **Kitchen** | See today’s counts by status |
| 2 | Click a status card | Opens queue filtered to that status |
| 3 | Click **Kitchen Queue** | Full board |
| 4 | Check performance strip | Avg ready time + preparation queue |

### B) Cook an order (main chef flow)

| Step | User action | System result | Who is notified / what next |
|------|-------------|---------------|-----------------------------|
| 1 | Open **Kitchen Queue** | Tickets appear (live) | — |
| 2 | Find new **Pending** ticket | Shows table, items, priority, timer | — |
| 3 | Click **Accept** | Status → Accepted | Waiter/ops notified; next click **Preparing** |
| 4 | Click **Preparing** | Status → Preparing | Cooking in progress |
| 5 | Click **Ready** | Status → Ready / Prepared | **Waiter must pick up & serve** |
| 6 | Optional: Print ticket | KOT/print sheet data | Physical kitchen ticket |

### C) Reject / cancel

| Step | User action | What happens next |
|------|-------------|-------------------|
| 1 | Reject (while pending/accepted/preparing) | Enter reason → order rejected; waiter informed |
| 2 | Cancel (allowed statuses) | Enter reason → order cancelled; floor/waiter update |

### D) Priority
Orders can be Normal / High / Urgent / VIP — chefs should handle higher priority first.

### After marking Ready
→ Waiter serves the table  
→ Ready count drops when order moves on / is handled  
→ Table remains occupied until dining/settlement finishes  

---

# Module 6 — Demo seed & branding

### Status: ✅ Complete

### Who runs it
- Developer / Admin on server (Artisan), not a daily staff action

### Command
```bash
php artisan db:seed --class=ModuleTestDataSeeder
```

### What it creates
- Branding: **Cost to Cost Foods**  
- Demo restaurant, menu items, kitchen stations  
- ~12 tables (zones + QR tokens)  
- Sample KDS orders (`DEMO-KDS`)  
- Waiter draft (`DEMO-WAITER`)  
- POS sample kitchen order (`DEMO-POS`)  
- All role accounts (password `123456`)

### After seeding
1. Login with role accounts above  
2. Walk Module 4 → Module 5 demo path  
3. Use available tables for new orders  

---

# Module 7 — Realtime & notifications

### Status: ✅ Complete

### Who logs in
All staff roles (bell appears in admin header when permitted)

### What it does
- Live refresh of waiter tables / dashboards and kitchen queue / dashboard  
- In-app inbox + navbar bell  
- Toasts for important status changes  

### Channels (conceptually)
- Kitchen channel — chefs  
- Waiter channel — waiters  
- Owner / admin ops — managers/admins  

### Flow

| Step | Trigger | What user sees / does next |
|------|---------|----------------------------|
| 1 | Waiter sends order | Chef queue updates live |
| 2 | Chef accepts / prepares / ready | Waiter gets notification + board updates |
| 3 | Table status changes | Waiter tables board refreshes |
| 4 | Click bell | Open inbox list |
| 5 | Click notification / order # | Jump to related order/table screen |
| 6 | Mark read / mark all | Unread badge clears |

### Requirement
Pusher (or configured broadcast driver) must be working in `.env` for live updates.

### After enabling realtime
→ Open waiter + chef in two browsers and confirm live sync during Module 4→5 demo  

---

# Related existing feature — Counter POS

### Status: ✅ Existing platform feature (integrated with kitchen demo)

### Who logs in
- **Cashier** → `cashier@example.com` / `123456`

### Screen
`/admin/pos`

### Typical flow
1. Login as cashier  
2. Open POS  
3. Create takeaway/counter order  
4. Order can appear on kitchen board (as with `DEMO-POS`)  
5. Chef processes like any kitchen ticket  

---

# Role-by-role daily checklist

### Owner / Manager (setup)
1. Login  
2. Create employees (Module 1)  
3. Create tables (Module 2)  
4. Generate & print QR (Module 3)  
5. Confirm waiter/chef can log in  

### Waiter (service)
1. Login as waiter  
2. Check dashboard for Ready orders  
3. Seat guest → open Available table  
4. Add items → Send to kitchen  
5. Watch notifications → serve when Ready  

### Chef (kitchen)
1. Login as chef  
2. Open Kitchen Queue  
3. Accept → Preparing → Ready  
4. Print ticket if needed  
5. Reject/cancel only with reason when required  

### Guest
1. Scan table QR  
2. Order from phone  
3. No staff login  

### Cashier
1. Login  
2. Use POS for counter/takeaway  
3. Coordinate with kitchen board  

---

# Complete modules checklist

| # | Module | Complete | Login needed |
|---|--------|----------|--------------|
| 1 | Employees | ✅ | Owner/Admin/Manager |
| 2 | Restaurant tables | ✅ | Owner/Admin/Manager |
| 3 | Table QR | ✅ | Staff to generate; Guest to scan |
| 4 | Waiter ordering | ✅ | Waiter |
| 5 | Kitchen / KDS | ✅ | Chef |
| 6 | Demo seed + branding | ✅ | Developer/Admin (seed) |
| 7 | Realtime + inbox notifications | ✅ | All staff |

---

# Recommended client demo script (15 minutes)

1. **Owner** — show Employees + Tables + Generate QR  
2. **Waiter** (browser A) — open available table → add items → Send to kitchen  
3. **Chef** (browser B) — see ticket appear → Accept → Preparing → Ready  
4. **Waiter** — show notification / ready count → open table  
5. **Guest phone** — scan QR on a test table (`/t/{token}`)  
6. Mention realtime: both screens update without refresh  

---

# Common “what do I do next?” answers

| Just finished… | Do this next… |
|----------------|---------------|
| Created employee | Give login; they open their role menu |
| Created table | Generate QR; tell waiter table is ready |
| Generated QR | Print & place on table; test scan |
| Guest scanned QR | Guest orders; kitchen/waiter handle as dine-in |
| Waiter added items | Send to kitchen |
| Waiter sent to kitchen | Chef accepts on KDS |
| Chef accepted | Start cooking → Preparing |
| Chef preparing | When done → Ready |
| Chef marked ready | Waiter serves guest |
| Order rejected | Waiter informs guest / re-order |
| Draft cancelled | Table free again if no other open order |
| Saw notification | Click it → handle the order/table |

---

# Notes & limitations (honest)

- Seeded **Prepared** demo tickets are not editable — use free tables for live ordering demos.  
- Full bill settlement / table close may still follow POS/cashier process depending on restaurant policy.  
- Live sync needs broadcast/Pusher configured.  
- Permissions control exactly which buttons each role sees.

---

*Use this document with clients or ChatGPT to explain workflows, training, or UAT scripts.*
