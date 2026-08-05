# Module 8 — Enterprise PWA Architecture Plan

**Status:** Awaiting approval (no implementation yet)  
**Rule:** Reuse existing PWA; do not replace; improve and extend.

---

## 1. Current PWA Architecture

```
Browser
  │
  ├─ master.blade.php → @laravelPWA
  │     └─ vendor/laravelpwa/meta.blade.php
  │           ├─ <link rel="manifest" href="/manifest.json">
  │           ├─ Apple splash + theme meta
  │           └─ registers /serviceworker.js
  │
  ├─ ManifestController@show  (dynamic JSON from config/laravelpwa.php + .env icon/splash URLs)
  │
  ├─ Admin System Setting → PwaComponent
  │     └─ PwaService updates icon/splash media → EnvEditor writes D_* env keys
  │
  ├─ Frontend only: FrontendPWAComponent (beforeinstallprompt)
  │     └─ mounted in DefaultComponent when theme === 'frontend'
  │
  └─ Separate: public/firebase-messaging-sw.js (existing FCM SW — leave alone; Module 8 will not replace Firebase)
```

### Gaps vs Module 8 goals
| Area | Current | Gap |
|------|---------|-----|
| Install UI | Frontend customer only | Missing for Admin / Owner / Manager / Waiter / Chef / Cashier |
| Admin PWA settings | Icon + splash upload only | No name, colors, orientation, display, offline, popup, cache, auto-update |
| Manifest | Basic fields + 1 shortcut | No maskable icons, categories, screenshots, role shortcuts, description, protocol handlers |
| Service worker | Naive cache-first; wrong asset paths (`/css/app.css`, `/js/app.js`, `/images/icons/*`) | Breaks Vite builds; no strategies; no update UX; weak offline |
| Offline page | Minimal Blade text | Not branded / not SPA-aware |
| Update flow | None | No “New version available” |
| Install detection | Partial | No `display-mode` / `getInstalledRelatedApps` handling in admin |
| Background sync | None | Architecture only (Module 8) |
| Push | Firebase SW exists | Prepare hooks only — **no new Firebase work** |

---

## 2. Existing Files (reuse)

| File | Role |
|------|------|
| `composer.json` → `silviolleite/laravelpwa` | Package retained |
| `config/laravelpwa.php` | Base manifest config |
| `public/serviceworker.js` | Extend (do not delete) |
| `resources/views/vendor/laravelpwa/meta.blade.php` | Improve meta / SW registration |
| `resources/views/vendor/laravelpwa/offline.blade.php` | Redesign offline fallback |
| `app/Http/Controllers/Frontend/ManifestController.php` | Enrich manifest JSON |
| `app/Models/Pwa.php` | Extend fillable + media conversions |
| `app/Services/PwaService.php` | Extend settings persistence |
| `app/Http/Controllers/Admin/PwaController.php` | Extend API |
| `app/Http/Requests/PwaRequest.php` | Extend validation |
| `resources/js/components/admin/systemSetting/Pwa/PwaComponent.vue` | Expand to full PWA Settings UI |
| `resources/js/stores/pwa.js` | Extend store |
| `resources/js/components/layouts/frontend/FrontendPWAComponent.vue` | Evolve into shared install UX or thin wrapper |
| `public/images/default/pwa/icons/*` | Keep defaults (72–512) |
| `public/images/default/pwa/splashes/*` | Keep Apple splash set |
| `routes/web.php` → `laravelpwa.manifest` | Keep route |
| `routes/api.php` → `admin/pwa` | Extend endpoints |
| Inbox / Echo / NotificationBell | Reuse as-is in standalone |

---

## 3. Files to Modify

### Backend
- `database/migrations/..._extend_pwas_table.php` *(new migration)* — settings columns on `pwas`
- `app/Models/Pwa.php` — settings casts; maskable conversions if needed
- `app/Services/PwaService.php` — CRUD settings + regenerate env/config outputs
- `app/Http/Controllers/Admin/PwaController.php` — full settings API
- `app/Http/Requests/PwaRequest.php` — new fields
- `app/Http/Resources/PwaResource.php` — expose settings
- `app/Http/Controllers/Frontend/ManifestController.php` — full Web Manifest
- `config/laravelpwa.php` — defaults aligned with Cost to Cost Foods; richer shortcuts
- `resources/views/vendor/laravelpwa/meta.blade.php` — safer SW register + apple icons
- `resources/views/vendor/laravelpwa/offline.blade.php` — branded offline shell
- `public/serviceworker.js` — strategies, versioning, offline, message API (no Workbox CDN dependency if we can extend vanilla cleanly; optional light Workbox later)
- `routes/web.php` — ensure `/offline` route if missing
- `routes/api.php` — PWA settings routes (public read for install config if needed)
- `database/seeders/PwaTableSeeder.php` / Module seed — default Module 8 settings
- `resources/js/languages/en.json` (+ other langs as needed) — install/update copy

### Frontend (Vue)
- `resources/js/components/DefaultComponent.vue` — mount shared PWA install + update for **both** frontend & admin themes
- `resources/js/components/layouts/frontend/FrontendPWAComponent.vue` — refactor to use shared composable (keep file; avoid duplicate)
- `resources/js/components/layouts/backend/BackendNavbarComponent.vue` — Install App button
- `resources/js/components/layouts/backend/BackendMenuComponent.vue` — Install entry (or footer)
- Profile menu / settings — Install App when eligible
- `resources/js/components/admin/systemSetting/Pwa/PwaComponent.vue` — full admin PWA Settings
- `resources/js/stores/pwa.js` — settings + public install config
- `resources/css/app.css` — standalone / display-mode tweaks; popup animations

---

## 4. New Files

| New file | Purpose |
|----------|---------|
| `resources/js/composables/usePwaInstall.js` | Single source: `beforeinstallprompt`, installed detection, prompt(), dismiss, never again |
| `resources/js/composables/usePwaUpdate.js` | SW updatefound → prompt → skipWaiting → reload |
| `resources/js/components/common/PwaInstallPromptComponent.vue` | Beautiful install popup (all roles/themes) |
| `resources/js/components/common/PwaUpdatePromptComponent.vue` | “New version available” |
| `resources/js/components/common/PwaInstallButtonComponent.vue` | Reusable Install button (navbar/sidebar/profile/settings) |
| `app/Services/PwaManifestBuilder.php` | Build manifest DTO from DB + config (SOLID) |
| `app/Enums/PwaDisplayMode.php`, `PwaOrientation.php`, `PwaCacheStrategy.php` | Typed settings |
| `resources/js/services/pwaBackgroundSync.js` | **Architecture stub** — request queue interface (no full offline orders yet) |
| `resources/js/services/pwaPushReady.js` | **Architecture stub** — subscription hook points (no Firebase changes) |
| `MODULE-8-PWA-IMPLEMENTATION-REPORT.md` | Post-build report (after coding) |

**Not creating:** a second service worker, duplicate manifest, or new Firebase integration.

---

## 5. Manifest Plan

`ManifestController` will emit production Web App Manifest:

```json
{
  "id": "/",
  "name": "Cost to Cost Foods",
  "short_name": "CTC Foods",
  "description": "...",
  "start_url": "/?source=pwa",
  "scope": "/",
  "display": "standalone",
  "orientation": "any",
  "theme_color": "#148A3C",
  "background_color": "#ffffff",
  "categories": ["food", "business", "lifestyle"],
  "icons": [ /* 72–512 any + maskable */ ],
  "shortcuts": [
    { "name": "Orders", "url": "/admin/online-orders" },
    { "name": "Tables", "url": "/admin/tables" },
    { "name": "Kitchen", "url": "/admin/kitchen" },
    { "name": "Waiter", "url": "/admin/waiter" },
    { "name": "POS", "url": "/admin/pos" },
    { "name": "Notifications", "url": "/admin/dashboard" }
  ],
  "screenshots": [ /* optional marketing shots if assets exist */ ],
  "protocol_handlers": [ /* future-ready stub if browsers allow; otherwise omit until ready */ ],
  "prefer_related_applications": false
}
```

- Values come from **Admin PWA Settings** (DB) with fallback to `config/laravelpwa.php` / company settings.
- Icons: reuse generated Spatie conversions + add **maskable** purpose entries (safe padding conversion or dedicated upload later).
- `start_url` remains `/` so all roles enter the SPA; router then sends them by auth/permission.

---

## 6. Service Worker Plan

**Keep** `public/serviceworker.js` path and registration from `meta.blade.php`.

### Extend to
1. **Versioned caches**  
   - `ctc-shell-v{VERSION}`  
   - `ctc-runtime-v{VERSION}`  
   - `ctc-images-v{VERSION}`  
   - `ctc-fonts-v{VERSION}`  
2. **Precache (install)**  
   - `/offline`  
   - Default PWA icons under `/images/default/pwa/icons/*`  
   - Critical shell paths that actually exist (not broken Vite legacy `/css/app.css`)  
3. **Message API**  
   - `SKIP_WAITING` for update popup  
   - Optional `CLEAR_CACHES` for Force Update admin action  
4. **Do not hijack** authenticated API JSON with stale cache  
5. **Leave** `firebase-messaging-sw.js` untouched (separate SW file for FCM)

### Registration improvement
- Register with `updateViaCache: 'none'`
- Listen for `controllerchange` → soft reload after user confirms update

---

## 7. Cache Strategy

| Resource | Strategy |
|----------|----------|
| App shell / offline page / icons | **Cache First** |
| Vite hashed `/build/assets/*` | **Cache First** (immutable) + version purge |
| HTML navigations (SPA) | **Network First** → fallback `/offline` |
| API `/api/*` | **Network Only** (never cache auth/order payloads) |
| Images (media, item photos) | **Stale While Revalidate** (size-capped) |
| Fonts (Iconly, Rubik, etc.) | **Cache First** |
| External CDNs (if any) | Network First / opaque careful |

**Security:** No tokens, cart secrets, or PII in Cache Storage. Only static/public assets.

**Cleanup:** On activate, delete old `ctc-*-v*` caches not matching current version. Admin **Force Update** bumps version / posts clear message.

---

## 8. Install Flow

```
App load (frontend OR admin theme)
  → usePwaInstall.init()
  → if display-mode standalone OR iOS navigator.standalone → mark installed; hide all CTAs
  → else listen beforeinstallprompt (capture & preventDefault)
  → if settings.enable_install_popup
        AND not never_again
        AND frequency/delay OK
        AND prompt event available (or iOS instruction mode)
     → show PwaInstallPromptComponent after delay
```

### Popup content
- Logo (theme / PWA icon)  
- App name  
- Short benefits (faster access, home screen, offline-ready shell)  
- **Install** → `deferredPrompt.prompt()`  
- **Maybe Later** → dismiss for `popup_frequency` window  
- **Never Show Again** → permanent local flag  

### Install buttons
Shown only when `canInstall === true` and not installed:
- Backend navbar  
- Backend sidebar  
- Admin PWA Settings page  
- User profile dropdown  
- Install popup  

### After install (`appinstalled` / standalone)
- Hide popup + buttons  
- Toast: **Installed Successfully**  
- Persist installed flag  

### iOS note
No `beforeinstallprompt` — show polite “Add to Home Screen” instructions instead of fake Install.

---

## 9. Update Flow

```
SW install (new version) → waiting worker
  → usePwaUpdate detects updatefound / waiting
  → if settings.auto_update OR user clicks Update Now
        postMessage SKIP_WAITING → controllerchange → reload
  → else show PwaUpdatePromptComponent (Update Now | Later)
```

Admin **Force Update**: bump SW cache version string from settings + instruct clients to clear caches on next load.

---

## 10. Lighthouse Improvement Plan

| Category | Actions |
|----------|---------|
| **PWA** | Valid manifest, SW controlling, installability, offline fallback, maskable icons, HTTPS |
| **Performance** | Fix SW not caching wrong assets; don’t cache API; reduce install-time precache; keep Vite code-split |
| **Best Practices** | HTTPS-only assumptions; no insecure SW; console-clean registration |
| **Accessibility** | Popup focus trap, labels, contrast on install/update modals |
| **SEO** | Manifest + theme-color + apple meta already partially present; ensure description |

Post-implementation: run Lighthouse on production build (not Vite HMR), fix, re-audit.

---

## 11. Testing Strategy

| Test | How |
|------|-----|
| Install Android Chrome | beforeinstallprompt + popup + navbar button |
| Install Desktop Chrome/Edge | same |
| iPhone Safari | A2HS instructions; apple-touch-icon; standalone |
| Offline | DevTools offline → `/offline` shell; no broken blank API cache |
| Online | SPA + Echo + NotificationBell still work |
| Cache | New deploy → update popup → reload → old cache gone |
| Standalone | CSS safe-areas; install CTAs hidden |
| Roles | Waiter/Chef/Cashier/Owner/Admin/Customer each see install when eligible |
| Realtime | Pusher/Echo in standalone |
| Notifications | Bell + badge unchanged |
| Lighthouse | CI or local report attached to Module 8 report |
| Regression | Existing Admin PWA icon/splash upload still works |

---

## Admin PWA Settings (UI fields)

Extend current Icon/Splash screen to include:

| Field | Storage |
|-------|---------|
| Application Name | `pwas.name` |
| Short Name | `pwas.short_name` |
| Description | `pwas.description` |
| Theme Color | `pwas.theme_color` |
| Background Color | `pwas.background_color` |
| App Icon | existing media `pwa_icon` |
| Splash Screen | existing media `pwa_splash` |
| Orientation | enum |
| Display Mode | enum (`standalone`, `fullscreen`, `minimal-ui`, `browser`) |
| Offline Mode | boolean |
| Auto Update | boolean |
| Cache Strategy | enum / profile key |
| Enable Install Popup | boolean |
| Popup Delay (sec) | int |
| Popup Frequency (hours) | int |
| Force Update | action button (bumps version) |

Public install config endpoint (cached): delay/frequency/enable for the Vue composable.

---

## Architecture stubs (explicitly not full features)

### Push-ready
- `pwaPushReady.js` exports `isPushSupported()`, `getRegistration()`, placeholder `subscribe()` that throws `NOT_IMPLEMENTED` until product chooses provider.  
- Do **not** modify Firebase config or implement FCM in Module 8.

### Background sync / offline orders
- IndexedDB queue interface: `enqueue(request)`, `flush()`, `onOnline`.  
- No waiter/kitchen order mutation offline in Module 8 — only scaffolding + docs.

---

## Implementation phases (after approval)

1. **DB + Admin Settings API/UI** (extend existing PWA setting)  
2. **ManifestBuilder + ManifestController enrichment**  
3. **Service worker strategies + offline page**  
4. **Shared install/update Vue composables + components** (admin + frontend)  
5. **Navbar/sidebar/profile Install buttons**  
6. **Shortcuts + maskable icons**  
7. **Stubs for push + background sync**  
8. **Lighthouse + manual install matrix + Module 8 report**

---

## Risks & decisions needing approval

1. **Workbox vs extended vanilla SW** — Recommend **extend existing vanilla SW** first (no new heavy dependency); add Workbox only if needed for maintainability.  
2. **Manifest shortcuts** — Role-specific URLs may 403 if wrong role installs; still OK (SPA will redirect). Confirm shortcut list.  
3. **EnvEditor vs DB** — Today icons write to `.env`. Plan: **settings live in `pwas` table**; ManifestBuilder reads DB first, env as fallback for generated icon URLs (keep current upload pipeline).  
4. **Scope** — Module 8 will not rebuild Firebase push or full offline ordering.

---

## Approval checkpoint

Please confirm to proceed with implementation, or adjust:

- [ ] Approve vanilla SW extension (no Workbox package)  
- [ ] Approve shortcut list (Orders / Tables / Kitchen / Waiter / POS / Notifications)  
- [ ] Approve install UI on **both** frontend + admin for all roles  
- [ ] Approve DB-backed PWA settings extending current Icon/Splash page  
- [ ] Approve push + background-sync as **stubs only**

Reply **Approve Module 8** (with any tweaks) and implementation will start.
