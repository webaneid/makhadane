# ADR-0005: Tracking Berbasis ID (Bukan Script Mentah)

- **Status:** Accepted
- **Tanggal:** 2026-11-19
- **Pembuat keputusan:** Webane Indonesia

## Konteks
Tab **Script** di `ane-general-setting` berupa textarea tempat admin menempel
snippet lengkap (GTM header/footer, Search Console, Meta Pixel, Meta SDK).
Isinya dicetak mentah (`echo $script`) di semua halaman publik. Akibatnya:
- Akun admin pelanggan yang disusupi (atau jalur lain yang bisa menulis option)
  dapat menyuntik JavaScript arbitrer ke seluruh pengunjung (lihat GAP-29).
- Di multisite, admin per-situs tanpa `unfiltered_html` bisa melewati batasan WP.
- Snippet sering tertempel dobel (GTM + gtag GA4), membebani PageSpeed.
- Field `ane_metasdk_body` punya `default_value` berisi App ID Facebook tertanam,
  sehingga situs yang tidak pernah mengisinya ikut memuat SDK Facebook.

Theme lain Webane (`jalaversity`, `sekolahane`, `newsane`, `jalawarta`) sudah
memakai pola berbasis ID.

## Keputusan
Admin hanya mengisi **ID/kode**. Theme memvalidasi dengan regex ketat dan
mencetak snippet resmi **hanya bila ID terisi** (`inc/tracking-scripts.php`):
GTM, GA4, Meta Pixel, TikTok Pixel, Yandex Metrica, serta meta verifikasi
Google/Bing/Yandex/Meta. Nilai disanitasi saat disimpan (`acf/update_value`)
dan dibersihkan lagi saat dibaca.

Field baru (prefix `makhadane_`): `makhadane_gtm_id`, `makhadane_ga4_id`,
`makhadane_gsc_verification`, `makhadane_bing_verification`,
`makhadane_yandex_verification`, `makhadane_yandex_metrica_id`,
`makhadane_fb_pixel_id`, `makhadane_fb_domain_verify`, `makhadane_tiktok_pixel_id`.

**Migrasi (sekali jalan, flag `makhadane_tracking_migrated`):** ID diekstrak dari
isian lama (`ane_ga_header`, `ane_ga_footer`, `ane_sc_header`,
`ane_metapixel_header`) ke field baru tanpa menimpa nilai baru yang sudah ada,
lalu **isian lama dihapus dari database**, termasuk `ane_metasdk_body`.

## Alternatif yang Dipertimbangkan
- Mempertahankan textarea lama sebagai "Script kustom (lanjutan)" — celah XSS
  tetap ada; ditolak.
- Mencetak textarea lama lewat `wp_kses` — merusak snippet GTM/Pixel; ditolak.

## Konsekuensi
- **Positif:** tidak ada JS arbitrer dari option; snippet seragam & benar; tidak
  ada lagi App ID Facebook bawaan yang ikut termuat; mudah diaudit.
- **Trade-off:** script kustom di luar GTM/Pixel/GSC yang pernah ditempel
  pelanggan **hilang** saat migrasi (hanya ID yang dipertahankan). Pelanggan yang
  membutuhkannya harus memasangnya lewat Google Tag Manager.
- **Kompatibilitas:** nama field lama (`ane_ga_header`, dst.) dihapus; ini
  disengaja dan didokumentasikan di sini sebagai pengecualian aturan
  "jangan ubah nama field ACF tanpa migrasi" (migrasi disediakan).
- Meta SDK untuk komentar Facebook tetap dipasang oleh `inc/admin/facebook-comments.php`
  (field `ane_facebook_app_id`), tidak terpengaruh.
- Rilis berupa `feat:` → versi minor ke semua situs pelanggan.
