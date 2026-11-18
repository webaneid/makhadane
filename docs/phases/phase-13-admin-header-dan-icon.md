# Fase 13 — Admin: Header Bawaan WP & Icon Outline

- **Status:** `Done`
- **Prasyarat:** Fase 12 (`Done`)

## Tujuan
Menyederhanakan tampilan admin: memakai admin bar bawaan WordPress dan
menyeragamkan icon menu (semua outline).

## Perubahan
1. **Icon (`style(admin)`):** 4 menu CPT (Asatidz, Ekstrakurikuler, Testimoni, FAQ)
   yang masih memakai dashicons solid diganti SVG outline (stroke 1,5) dengan pola
   yang sama seperti `#menu-users` di `css/admin.min.css`.
2. **Header bawaan WP:**
   - `inc/admin/header.php`: dihapus `ane_customize_admin_bar` (tombol New Post/New Page,
     link Dashboard, penghapusan node bawaan), `ane_replace_howdy`, `ane_admin_bar_class`,
     dan JS inline panel hamburger/user mobile. Tersisa `ane_hide_admin_bar_frontend`.
   - `css/admin.min.css`: dihapus 109 aturan `#wpadminbar`/`.ane-mobile-*`/`.ane-action-btn`/
     `.ane-btn-icon` dan 2 aturan mobile `body.admin-bar{padding-top:46px|56px}` yang
     menggandakan offset bawaan WP (126 KB → ~104 KB). Sidebar `#adminmenu` sudah
     diposisikan `top:32px` sehingga cocok dengan bar standar.

## Catatan
- `css/admin.min.css` tidak punya file sumber; perubahan dilakukan langsung pada file minified.
- Menu mobile kini memakai menu responsif bawaan WP (`.wp-responsive-open`).

## Verifikasi
- Parser CSS: round-trip file identik sebelum diedit, hasil edit seimbang & dapat di-parse ulang.
- `composer lint:changed`, `composer analyse`, `composer security:scan`, `composer dist:check`.
- Cek manual: wp-admin desktop & mobile (bar, sidebar rail, panel submenu, menu mobile).
