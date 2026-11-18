# Lessons Learned

> Format: tanggal, masalah, root cause, fix, pencegahan. Entri terbaru di atas.
> Wajib diisi setelah debugging signifikan atau hotfix production.

---

## 2026-11-19 — Spam Komentar Judol Walau Menu Komentar "Dimatikan"
**Masalah:** Situs pelanggan tetap menerima spam komentar (judi online) padahal menu Komentar tidak terlihat dan theme tidak menampilkan form komentar.
**Root cause:** `codesnippets_disable_comments` hanya menyembunyikan menu & dukungan editor di wp-admin. Status `comment_status = open` pada post tetap, sehingga `wp-comments-post.php`, REST API, XML-RPC, dan trackback tetap menerima komentar dari bot tanpa membuka halaman situs.
**Fix:** Saklar ACF `makhadane_disable_comments` (default dimatikan) di `inc/comments-control.php` yang menutup semua jalur pengiriman (lihat ADR-0006).
**Pencegahan:** "Menyembunyikan UI" bukan "menonaktifkan fitur". Untuk fitur yang bisa diserang, tutup di sisi server (filter `comments_open`/`pings_open`, blokir endpoint) dan uji dengan permintaan langsung (`curl` ke `wp-comments-post.php`, REST, XML-RPC), bukan hanya lewat tampilan.

---

## 2026-11-19 — Audit Security Theme: Token Updater, AJAX Publik, SRI, Schema Output
**Masalah:** Audit manual menemukan lima celah kecil–menengah: (1) token GitHub updater bisa terkirim ke host non-GitHub; (2) AJAX `nopriv` klik Linktree menulis ke DB tanpa whitelist/batas laju; (3) Chart.js dimuat dari CDN tanpa SRI; (4) dedupe view membuat 1 transient per IP × post; (5) `$schema_type` dari post meta dicetak ke komentar HTML tanpa validasi (bisa keluar komentar dengan `-->`).
**Root cause:** (1) host URL dicocokkan dengan `strpos()` pada seluruh string URL, bukan di-parse; (2) nonce publik ikut ter-cache sehingga tidak bisa jadi proteksi, dan tidak ada validasi nilai; (3) aset CDN admin tidak melewati aturan §7 security; (4) key transient mengandung post ID; (5) mengira nilai meta pasti berasal dari dropdown ACF.
**Fix:** `wp_parse_url()` + cek host/path persis (updater); whitelist + batas panjang + rate limit 30/menit per IP (Linktree); `integrity` + `crossorigin` via `script_loader_tag`; satu transient per IP berisi daftar post (maks 100); whitelist `$schema_type` saat output. Lihat `GAP-23`–`GAP-27` di `docs/RETROFIT-GAPS.md`.
**Pencegahan:** (a) Jangan pernah memvalidasi URL/host dengan `strpos()`/`str_contains()` — selalu `wp_parse_url()` lalu bandingkan host persis. (b) Data meta/option dianggap tidak tepercaya walau field ACF-nya `select`: validasi dengan whitelist saat output. (c) Endpoint `nopriv` yang menulis ke DB wajib punya whitelist nilai, batas panjang, dan rate limit. (d) Jangan buat transient dengan key yang bergantung pada input yang bisa divariasikan penyerang (post ID × IP). (e) Naikkan versi library CDN → hitung ulang hash SRI.

---

## 2026-11-15 — Sanitasi Judul Post/CPT Anti-HTML & Anti-Copy-Paste Microsoft Word (Fase 03)
**Masalah:** Judul post/page/CPT yang di-copy-paste langsung dari Microsoft Word ke editor WordPress membawa tag HTML (`<span style="mso-...">`, `<font>`, `<strong>`, `<o:p>`, komentar kondisional `<!--[if gte mso 9]>`, serta `&nbsp;` / zero-width spaces) yang merusak tipografi/layout desain dan berpotensi menimbulkan celah XSS jika template memanggil `the_title()` tanpa escaping, atau menampilkan kode tag mentah di layar jika di-escape dengan `esc_html()`.
**Root cause:** WordPress mengizinkan tag HTML inline di `post_title` bagi user dengan capability `unfiltered_html` (Admin/Editor) maupun tag format dasar bagi Author.
**Fix:** Memasang fungsi `makhadane_clean_title_string()` di `inc/post.php` pada dua lapis: (1) filter `title_save_pre` saat post disimpan ke database, dan (2) filter `the_title` prioritas `5` (sebelum `wptexturize`) untuk membersihkan post lama di database pelanggan secara otomatis saat ditampilkan, ditambah penggunaan `esc_html( get_the_title() )` / `esc_attr( get_the_title() )` di seluruh template.
**Pencegahan:** Jangan pernah mengizinkan HTML di `post_title` dan selalu gunakan `esc_html( get_the_title() )` atau `ane_get_title()` saat mencetak judul.

---

## 2026-11-15 — Inisialisasi Retrofit Makhad Ane (`v4.1.3`)
**Masalah:** Theme `makhadane` memiliki banyak file `.md` terpisah di root, bergantung pada kompilasi SCSS manual tanpa dead-CSS audit, serta belum memiliki quality gate otomatis (`PHPCS` WordPress Coding Standards, `PHPStan`, `security-scan.sh`, `check-dist.sh`).
**Root cause:** Theme dikembangkan sebelum standar `master-wordpress` (Sekolahane/Newsane/Jalawarta) ditetapkan.
**Fix:** Penerapan Fase 00 (Fondasi: `AGENTS.md`, `docs/SOP.md`, `composer.json` + WPCS + PHPStan level 3 + baseline, `bin/` scripts) dan Fase 01 (Retrofit Audit).
**Pencegahan:** Setiap perubahan kode wajib mengikuti alur 6 langkah `docs/SOP.md` dan lulus `composer lint:changed`, `composer analyse`, `composer security:scan`, dan `composer css:check`.
