# Conventions

## PHP
- Ikuti **WordPress Coding Standards** (dicek otomatis oleh PHPCS):
  tab untuk indentasi, `snake_case` untuk fungsi/variabel, spasi di dalam
  kurung `foo( $bar )`, Yoda condition tidak diwajibkan.
- Prefix baru: fungsi/hook/option/transient `makhadane_`, konstanta
  `MAKHADANE_`, class `Makhadane_`.
- Kompatibilitas: PHP **8.0+**, WordPress **6.0+**. Fitur PHP 8.0 (`match`,
  named args, `str_contains`, nullsafe) boleh dipakai; fitur 8.1+ jangan.
- Satu file `inc/` = satu area tanggung jawab (`inc/makhadane.php`,
  `inc/updater.php`, `inc/custompost.php`, `inc/webp-converter.php`, ...).
- Komentar boleh Bahasa Indonesia; docblock fungsi publik minimal berisi
  satu kalimat tujuan + `@param`/`@return`.

## Template
- Template hierarchy WordPress di root; potongan reusable di `tp/`.
- Template part tidak boleh query/logika berat — taruh di fungsi `inc/`,
  template cukup memanggil dan meng-escape output.
- Nama file template part: `tp/{kelompok}/{nama}.php`, kebab-case.

## Aset
- CSS sumber → `css/main.css`, hasil minify `css/main.min.css`.
- Library pihak ketiga: utamakan disimpan lokal (bukan CDN) atau, kalau CDN,
  versi dipin + atribut SRI. Keputusan per library dicatat di ADR.

## Branch
- `main` — production. Setiap merge berpotensi rilis ke semua pelanggan.
- `feat/{nama}`, `fix/{nama}`, `docs/{nama}`, `chore/{nama}` — dari `main`,
  di-merge kembali lewat PR.

## Commit Message (Conventional Commits)
Format: `type(scope): deskripsi singkat` — deskripsi Bahasa Indonesia boleh.

| Type | Kapan | Memicu rilis? |
|---|---|---|
| `feat` | fitur baru yang terlihat pengguna | ✅ minor |
| `fix` | perbaikan bug | ✅ patch |
| `perf` | peningkatan performa | ✅ patch |
| `refactor` | ubah struktur tanpa ubah perilaku | ❌ |
| `style` | format kode (bukan CSS tampilan!) | ❌ |
| `docs` | dokumentasi | ❌ |
| `chore` | tooling, config, dependensi dev | ❌ |
| `test` | test | ❌ |

- Perubahan tampilan CSS yang terlihat pengguna = `fix` atau `feat`, bukan `style`.
- Scope contoh: `updater`, `ustadz`, `header`, `security`, `enqueue`, `docs`.
- Breaking: tambahkan footer `BREAKING CHANGE: ...` (jarang — lihat SOP).

## Versi
- Sumber kebenaran: `Version:` di `style.css`, dibaca ke konstanta
  `MAKHADANE_VERSION`. Dinaikkan otomatis oleh CI, jangan manual.
