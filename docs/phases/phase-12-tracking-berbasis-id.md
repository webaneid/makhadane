# Fase 12 — Tracking Berbasis ID

- **Status:** `Done`
- **Prasyarat:** Fase 11 (`Done`)
- **Keputusan:** [ADR-0005](../decisions/adr-0005-tracking-berbasis-id.md)

## Tujuan
Mengganti textarea script mentah di `ane-general-setting` (tab Script) dengan
isian ID/kode saja. Theme mencetak snippet resmi hanya bila ID terisi.

## Perubahan
1. `inc/tracking-scripts.php` ditulis ulang: ekstraksi + validasi regex per field,
   render `wp_head` (prioritas 2) & `wp_body_open`, sanitasi `acf/update_value`.
2. `inc/acf-json/field-groups.json` (grup **Script**): 5 textarea lama diganti 9
   field teks (GTM, GA4, GSC, Yandex verifikasi + Metrica, Bing, Meta Pixel,
   Meta domain, TikTok) dalam tab Google / Yandex & Bing / Meta / TikTok.
   Tersinkron ke situs pelanggan lewat hash `acf-importer`.
3. Migrasi sekali jalan `makhadane_migrate_legacy_tracking()` (`init` 20): ekstrak
   ID dari isian lama → field baru, hapus isian lama, set flag
   `makhadane_tracking_migrated`.

## Verifikasi
- Uji skrip PHP terisolasi: ekstraksi GTM/GA4/GSC/Bing/Pixel dari snippet nyata,
  idempotent, tidak menimpa nilai baru, input XSS dibersihkan.
- `composer lint:changed`, `composer analyse`, `composer security:scan`, `composer css:check`.
- Cek manual di situs lokal: isi tiap ID → snippet muncul di `<head>`/`<body>`; kosong → tidak ada output.
