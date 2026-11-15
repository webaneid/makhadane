# Fase 04 — Pembersihan Kode Mati/Duplikat, Sinkronisasi Taxonomy `kelas`, Text Domain `makhadane`, & Perapihan WPCS

- **Status:** Done
- **Prasyarat:** Fase 03 (Security Fix & PHPStan 0 Error)
- **Branch:** `main`

## Tujuan

1. **Sinkronisasi Template Taxonomy `'kelas'` vs `'Kelas'` (`GAP-11`):**
   - Menambahkan filter `makhadane_resolve_kelas_taxonomy_template()` pada `taxonomy_template` di `inc/custompost.php` agar `taxonomy-Kelas.php` selalu terpanggil untuk taxonomy `'kelas'` maupun `'Kelas'` di server Linux yang case-sensitive.
2. **Penyeragaman Text Domain ke `'makhadane'` & Perbaikan Path Logo (`GAP-15`):**
   - Mengubah seluruh text domain `'elemenane'` di `inc/admin/facebook-comments.php` menjadi `'makhadane'`.
   - Mengubah `@package elemenane` di `inc/tracking-scripts.php` menjadi `@package makhadane`.
   - Memperbaiki fallback logo default di `tp/content-linktree.php` dari `/img/logo-newsane.png` (tidak ada) menjadi `/img/logo-makhadane.png`.
3. **Penghapusan Template Part Duplikat/Mati & Aset Sampel Tidak Terpakai (`GAP-16`):**
   - Menghapus 4 file template part yang tidak pernah dipanggil: `tp/single.php`, `tp/single-ustadz.php`, `tp/content-sliders.php`, dan `tp/home-categories.php`.
   - Menghapus pemanggilan `get_template_part( 'tp/news', 'featured-mobile' )` dan `get_template_part( 'tp/mobile', 'news-populer' )` (file tidak ada) di `tp/mobile/kategori.php`.
   - Menghapus 2 file gambar tidak terpakai di `img/`: `img/logo-sekolahane.png` dan `img/outdoor-basketball-court-school-kids-play.jpg` (menghemat 8,27 MB ukuran paket tema).
4. **Perapihan Format Kode sesuai WordPress Coding Standards (`GAP-22`):**
   - Menjalankan `composer lint:fix` (`phpcbf`) dan merapikan 2.537 pelanggaran format kode PHP pada 64 file.

## File yang Disentuh

- `inc/custompost.php` (ubah)
- `inc/admin/facebook-comments.php` (ubah)
- `inc/tracking-scripts.php` (ubah)
- `tp/content-linktree.php` (ubah)
- `tp/mobile/kategori.php` (ubah)
- `tp/single.php` (hapus)
- `tp/single-ustadz.php` (hapus)
- `tp/content-sliders.php` (hapus)
- `tp/home-categories.php` (hapus)
- `img/logo-sekolahane.png` (hapus)
- `img/outdoor-basketball-court-school-kids-play.jpg` (hapus)
- 64 file PHP yang dirapikan otomatis oleh `composer lint:fix`
- `docs/RETROFIT-GAPS.md` (perbarui status `GAP-11`, `GAP-15`, `GAP-16`, `GAP-22`)
- `docs/PROGRESS.md` (perbarui status Fase 04)

## Langkah Detail

- [x] 1. Tambahkan `makhadane_resolve_kelas_taxonomy_template()` di `inc/custompost.php`.
- [x] 2. Seragamkan text domain di `inc/admin/facebook-comments.php` dan docblock di `inc/tracking-scripts.php`.
- [x] 3. Perbaiki fallback logo di `tp/content-linktree.php` dan bersihkan `tp/mobile/kategori.php`.
- [x] 4. Hapus 4 file `tp/` yang tidak terpakai dan 2 file `img/` yang tidak terpakai.
- [x] 5. Jalankan `composer lint:fix` (`phpcbf`) pada codebase.
- [x] 6. Verifikasi dengan `composer security:scan`, `composer lint:syntax`, `composer analyse`, dan `composer dist:check`.

## Kriteria Selesai (Acceptance Criteria)

- [x] `composer security:scan` lolos 100%.
- [x] `composer lint:syntax` lolos 0 error.
- [x] `composer analyse` lolos 0 error (0 baseline).
- [x] `composer dist:check` lolos.
- [x] `GAP-11`, `GAP-15`, `GAP-16`, dan `GAP-22` berstatus `Fixed` di `docs/RETROFIT-GAPS.md`.

## Security Review (Step 4 SOP)

| # | Area | Status | Catatan |
|---|---|---|---|
| 1 | Pengurangan Permukaan Serangan (Attack Surface) | **PASS** | Menghapus 4 file PHP template part mati (`tp/single.php`, `tp/single-ustadz.php`, `tp/content-sliders.php`, `tp/home-categories.php`) dan 2 file gambar tidak terpakai (8,27 MB). |
| 2 | Guard `ABSPATH` & Anti-Webshell | **PASS** | `composer security:scan` lolos pada seluruh 98 file PHP aktif. |
| 3 | Output Escaping & Query Loop | **PASS** | `tp/mobile/kategori.php` menggunakan loop standar `while ( $mobile_query->have_posts() ) : $mobile_query->the_post();` + `wp_reset_postdata()`. |
