# Lessons Learned

> Format: tanggal, masalah, root cause, fix, pencegahan. Entri terbaru di atas.
> Wajib diisi setelah debugging signifikan atau hotfix production.

---

## 2026-11-15 — Inisialisasi Retrofit Makhad Ane (`v4.1.3`)
**Masalah:** Theme `makhadane` memiliki banyak file `.md` terpisah di root, bergantung pada kompilasi SCSS manual tanpa dead-CSS audit, serta belum memiliki quality gate otomatis (`PHPCS` WordPress Coding Standards, `PHPStan`, `security-scan.sh`, `check-dist.sh`).
**Root cause:** Theme dikembangkan sebelum standar `master-wordpress` (Sekolahane/Newsane/Jalawarta) ditetapkan.
**Fix:** Penerapan Fase 00 (Fondasi: `AGENTS.md`, `docs/SOP.md`, `composer.json` + WPCS + PHPStan level 3 + baseline, `bin/` scripts) dan Fase 01 (Retrofit Audit).
**Pencegahan:** Setiap perubahan kode wajib mengikuti alur 6 langkah `docs/SOP.md` dan lulus `composer lint:changed`, `composer analyse`, `composer security:scan`, dan `composer css:check`.

---

## [Tanggal] — [Judul]
**Masalah:**
**Root cause:**
**Fix:**
**Pencegahan:**
