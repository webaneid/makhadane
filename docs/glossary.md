# Glossary

| Istilah | Arti |
|---|---|
| **Ustadz** | Custom post type `ustadz` — profil ustadz/tenaga pengajar pesantren/ma'had |
| **Ekstrakurikuler** | Custom post type `ekstrakurikuler` — kegiatan ekstrakurikuler santri/siswa |
| **Testimoni** | Custom post type `testimoni` — testimoni alumni/wali santri |
| **FAQ** | Custom post type `faq` — pertanyaan yang sering diajukan (Frequently Asked Questions) |
| **Kelas**, **Pelajaran** | Custom taxonomy untuk CPT `ustadz` (`inc/custompost.php`). ⚠️ Slug taxonomy Kelas terdaftar dengan huruf kapital `'Kelas'` (rewrite `'kelas'`) — jangan diubah ke lowercase karena data term pelanggan terikat ke `'Kelas'`. |
| **Webane Settings** | Halaman option ACF milik theme (`webane-theme-general-settings`: info umum, banner, customer care, mobile menu, warna, script) |
| **Fase** | Satu unit kerja menurut `SOP.md` |
| **ADR** | Architecture Decision Record — catatan keputusan besar (`docs/decisions/`) |
| **Rilis** | Versi baru yang dibuat CI saat merge ke `main`; otomatis ditawarkan ke situs pelanggan |
| **Situs pelanggan** | Situs customer Webane yang memakai theme ini |
