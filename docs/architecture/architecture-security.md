# Architecture — Security (WordPress Theme)

> Padanan `architecture-security.md` + skill `security-review` dari
> master-typescript, diterjemahkan ke dunia WordPress. Bagian akhir file ini
> adalah **checklist Security Review** yang wajib dijalankan di SOP Langkah 4.

## Prinsip
1. **Data dari mana pun tidak dipercaya** — termasuk database (ACF, option,
   post meta, nama kategori). Editor/admin situs pelanggan bisa saja disusupi.
2. **Sanitize saat masuk, escape saat keluar.** Dua-duanya, bukan salah satu.
3. **Escape selambat mungkin** (tepat di `echo`), sesuai konteks HTML.
4. **Setiap aksi yang mengubah state** butuh: siapa (capability) + niat (nonce).
5. **Theme tidak menyimpan secret.** Kode theme tersebar ke banyak situs.

## 1. Output Escaping
| Konteks | Fungsi |
|---|---|
| Teks di dalam HTML | `esc_html()` / `esc_html_e()` / `esc_html__()` |
| Nilai atribut HTML | `esc_attr()` |
| URL (`href`, `src`, `action`) | `esc_url()` |
| HTML terbatas dari editor/ACF WYSIWYG | `wp_kses_post()` |
| Di dalam `<script>` / data JS | `wp_json_encode()` / `wp_add_inline_script()` / `wp_localize_script()` |
| Teks terjemahan dengan placeholder | `printf( esc_html__( '... %s', 'makhadane' ), esc_html( $x ) )` |

```php
// ❌ Salah
<a href="<?php echo $link['url']; ?>"><?php echo $link['title']; ?></a>

// ✅ Benar
<a href="<?php echo esc_url( $link['url'] ); ?>"><?php echo esc_html( $link['title'] ); ?></a>
```
- Fungsi WP `the_*()` (mis. `the_title()`, `the_permalink()`) sudah aman
  dipakai langsung. Versi `get_the_*()` yang di-`echo` WAJIB di-escape.
- `the_field()` ACF = `echo get_field()` tanpa escape → hindari, pakai
  `echo esc_html( get_field( ... ) )`.

## 2. Input Sanitization
```php
$post_id = isset( $_GET['postviews_id'] ) ? absint( $_GET['postviews_id'] ) : 0;
$keyword = isset( $_POST['q'] ) ? sanitize_text_field( wp_unslash( $_POST['q'] ) ) : '';
```
| Jenis data | Fungsi |
|---|---|
| Angka/ID | `absint()` / `intval()` |
| Teks satu baris | `sanitize_text_field()` |
| Teks multi-baris | `sanitize_textarea_field()` |
| Email | `sanitize_email()` + `is_email()` |
| URL | `esc_url_raw()` |
| Slug/key | `sanitize_key()` / `sanitize_title()` |
| Warna hex | `sanitize_hex_color()` |
| Pilihan terbatas | whitelist: `in_array( $v, $allowed, true )` |

## 3. Nonce & Capability
- Form admin: `wp_nonce_field()` → `check_admin_referer()`.
- AJAX yang mengubah data milik user/admin: `check_ajax_referer()` +
  `current_user_can()`.
- Link aksi (mis. "Cek Update Sekarang"): `wp_nonce_url()` → `check_admin_referer()`.
- AJAX publik (`wp_ajax_nopriv_*`) seperti penghitung view: nonce tidak bisa
  jadi proteksi utama (cache halaman) → validasi ketat input + batasi efek
  (mis. hanya `absint`, hanya post yang `publish`, debounce/rate limit).

## 4. Database
- Utamakan `WP_Query`, `get_posts()`, `get_post_meta()`, dst.
- `$wpdb` dengan variabel → WAJIB `$wpdb->prepare()`. Tanpa variabel → boleh.
- Query mahal → cache dengan transient/object cache.

## 5. Secret & Konfigurasi
- Urutan sumber nilai rahasia: konstanta di `wp-config.php` → ACF option →
  kosong (fitur nonaktif). Tidak pernah hardcode di theme.
- Contoh: Google Maps API key, token updater, App ID pihak ketiga.
- Token/secret tidak pernah ditampilkan ulang utuh di admin (tampilkan
  `Yes (40 karakter)` atau 4 karakter terakhir).

## 6. File & Include
- Semua file `inc/*.php` diawali `defined( 'ABSPATH' ) || exit;`.
- Tidak ada `include` dengan path dari input user.
- Upload (kalau ada fitur upload): `wp_handle_upload()` +
  `wp_check_filetype_and_ext()` + batas ukuran.

## 7. Aset & Pihak Ketiga
- Semua JS/CSS lewat enqueue. Script pihak ketiga dipin versinya; kalau dari
  CDN, tambahkan SRI (`integrity` + `crossorigin`) via filter
  `script_loader_tag`/`style_loader_tag`.
- Library PHP pihak ketiga dikelola lewat Composer (bukan copy-paste file).

## 8. Updater (lihat ADR-0003)
- Request ke GitHub hanya untuk URL repo theme ini; header Authorization
  tidak boleh terkirim ke host lain.
- Paket hanya diterima dari asset rilis `.zip` milik repo yang dikonfigurasi.

---

## ✅ Checklist Security Review (SOP Langkah 4)

Jalankan untuk **setiap file yang diubah/dibuat** di fase ini. Laporkan
dengan format di bagian bawah.

**Output**
- [ ] Setiap `echo`/`print`/`printf` variabel sudah di-escape sesuai konteks
- [ ] Tidak ada `the_field()` / `echo get_field()` tanpa escape
- [ ] Data untuk JS lewat `wp_json_encode`/`wp_localize_script`, bukan concat string

**Input**
- [ ] Setiap superglobal lewat `isset` + `wp_unslash` + `sanitize_*`/`absint`
- [ ] Nilai pilihan divalidasi dengan whitelist

**Aksi & akses**
- [ ] Aksi yang mengubah data punya nonce + `current_user_can()`
- [ ] Halaman/handler admin mengecek capability
- [ ] AJAX `nopriv` dibatasi efeknya dan inputnya divalidasi

**Database**
- [ ] `$wpdb` dengan variabel memakai `prepare()`
- [ ] Tidak ada tulis DB di setiap request

**Secret & config**
- [ ] Tidak ada API key/token/password di kode
- [ ] Secret tidak bocor ke HTML/JS publik atau ke log

**File & aset**
- [ ] Guard `ABSPATH` ada di file `inc/` baru
- [ ] Aset baru lewat enqueue dengan versi
- [ ] Tidak ada debug tertinggal (`var_dump`, `print_r`, `die`)

**Format laporan**
```
## Security Review — [fase/fitur]
✅ Lolos: ...
⚠️ Perlu diperbaiki: [file:baris] masalah → saran fix  (Critical/High/Medium/Low)
❓ Perlu dicek manual: ...
```
Temuan Critical/High → perbaiki sebelum fase ditutup. Medium/Low yang ditunda
→ catat di `docs/RETROFIT-GAPS.md`.

### Panduan tingkat keparahan
| Level | Contoh |
|---|---|
| Critical | secret bocor, SQL injection, eksekusi kode, bypass auth |
| High | XSS dari data yang bisa diisi non-admin, aksi admin tanpa nonce/capability |
| Medium | XSS yang hanya bisa diisi admin, AJAX publik tanpa batas, aset CDN tanpa SRI |
| Low | hardening (guard ABSPATH), info leak versi, debug tertinggal di jalur admin |
