# Lessons Learned

> Format: tanggal, masalah, root cause, fix, pencegahan. Entri terbaru di atas.
> Wajib diisi setelah debugging signifikan atau hotfix production.

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
