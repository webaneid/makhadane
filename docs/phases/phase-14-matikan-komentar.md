# Fase 14 — Saklar Matikan Komentar WordPress

- **Status:** `Done`
- **Prasyarat:** Fase 13 (`Done`)
- **Keputusan:** [ADR-0006](../decisions/adr-0006-matikan-komentar-total.md)

## Perubahan
1. `inc/comments-control.php` (baru): saklar ACF + seluruh penutup komentar (lihat ADR-0006).
2. `inc/handle.php`: hapus `codesnippets_disable_comments` (hanya menyembunyikan UI).

## Verifikasi (situs lokal, default = dimatikan)
- `wp-comments-post.php` → 403, jumlah komentar tidak bertambah (14 → 14).
- REST `GET/POST /wp/v2/comments` → 404 (sebelumnya GET 200); `/wp/v2/posts` tetap 200.
- XML-RPC `wp.newComment` & `pingback.ping` → `-32601` (metode tidak ada).
- `/comments/feed/` → 404; `/feed/` tetap 200.
- `comments_open`/`pings_open` = false, `get_comments_number` = 0, `default_comment_status` = `closed`.
- Saklar dimatikan (`options_makhadane_disable_comments`=0) → REST komentar 200 & feed komentar 200 (perilaku WP normal); opsi dipulihkan.
- `composer lint:changed`, `composer analyse`, `composer security:scan`, `composer dist:check`.
