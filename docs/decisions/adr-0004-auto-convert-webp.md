# ADR-0004: Auto-Convert Upload Gambar ke WebP

- **Status:** Accepted
- **Tanggal:** 2026-11-15
- **Pembuat keputusan:** Webane Indonesia

## Konteks
Pengelola website pesantren/madrasah sering mengunggah foto kegiatan, banner,
dan foto ustadz berukuran besar langsung dari kamera/ponsel dalam format `.jpg`
atau `.png`. Kita membutuhkan mekanisme otomatis di level theme yang
mengonversi setiap gambar `.jpg`/`.jpeg`/`.png` yang diunggah menjadi `.webp`,
memastikan seluruh sub-size (`thumbnail`, `medium`, `large`, `kotak`, `persegi`)
otomatis terbentuk dalam format `.webp`, dan menghapus file mentah `.jpg`/`.png`.

## Keputusan
Konversi dilakukan pada filter `wp_handle_upload` dan `wp_handle_sideload` di
modul `inc/webp-converter.php` (menggantikan `inc/media.php` lama) dengan
kualitas default `82` (dapat diubah lewat filter `makhadane_webp_quality`),
disertai penanganan khusus PNG 8-bit palette (`imagepalettetotruecolor`) dan
fallback aman jika server tidak mendukung WebP.

## Konsekuensi
- **Positif:** Semua file gambar baru di server pelanggan (baik ukuran original
  maupun seluruh ukuran potongan theme) 100% berformat `.webp`, hemat ukuran file
  60–80%, dan 1× kerja CPU saat upload.
- **Negatif / Trade-off:** Gambar lama yang sudah diunggah sebelum fitur ini
  aktif tetap berformat lama agar tidak merusak URL gambar lama di konten post.
