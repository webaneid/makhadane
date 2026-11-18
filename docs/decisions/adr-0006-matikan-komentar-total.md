# ADR-0006: Saklar "Matikan Komentar WordPress" (Total)

- **Status:** Accepted
- **Tanggal:** 2026-11-19
- **Pembuat keputusan:** Webane Indonesia

## Konteks
Komentar WordPress sering dipakai untuk spam (mis. judi online) dan hampir tidak
dipakai pelanggan. Theme ini **tidak punya `comments.php`** sehingga tidak
menampilkan form maupun daftar komentar, tetapi sebelumnya hanya menyembunyikan
menu Komentar di admin (`codesnippets_disable_comments`). Endpoint pengiriman
(`wp-comments-post.php`, REST `/wp/v2/comments`, XML-RPC, trackback/pingback)
tetap menerima komentar untuk post berstatus `open`, jadi bot tetap bisa
mengirim spam tanpa membuka halaman situs.

## Keputusan
Tambah saklar ACF `makhadane_disable_comments` (ACF Options `ane-general-setting`,
grup "Komentar WordPress"). Bila aktif, `inc/comments-control.php`:
- menutup `comments_open`/`pings_open`, menyembunyikan komentar lama, `get_comments_number` = 0;
- memblokir `wp-comments-post.php` (403), pembuatan komentar dari kode mana pun
  (`preprocess_comment`), endpoint REST komentar, metode XML-RPC komentar/pingback,
  feed komentar, dan widget Komentar Terbaru;
- mencabut dukungan `comments`/`trackbacks` dari semua post type dan menetapkan
  status bawaan konten baru `closed`;
- menyembunyikan menu Komentar, submenu Diskusi, widget dashboard, ikon admin bar,
  serta mengalihkan akses langsung ke halaman admin komentar/diskusi.

**Default: DIMATIKAN** (nilai belum pernah disimpan dianggap aktif-dimatikan).
Tidak ada tulis ke DB per-request dan tidak ada migrasi data; status `comment_status`
pada post lama tidak diubah (ditimpa lewat filter).

## Alternatif yang Dipertimbangkan
- Default "komentar aktif" — pelanggan baru tetap rentan spam sampai saklar disentuh;
  ditolak karena theme memang tidak menyediakan UI komentar.
- Menutup semua post lewat query UPDATE sekali jalan — menulis massal ke DB dan
  tidak melindungi post baru; filter sudah cukup.
- Plugin "Disable Comments" — menambah dependensi per situs.

## Konsekuensi
- **Positif:** spam komentar tidak bisa masuk lewat jalur apa pun; satu tombol untuk pelanggan.
- **Trade-off:** situs yang sebelumnya memakai komentar WordPress lewat plugin/kode
  sendiri harus mematikan saklar ini. Komentar yang sudah ada tetap di database
  (tidak dihapus) dan muncul kembali bila saklar dimatikan.
- Komentar Facebook (`ane_facebook_comments_enable`) tidak terpengaruh.
- Fungsi lama `codesnippets_disable_comments` (`inc/handle.php`) dihapus.
