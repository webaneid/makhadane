# ADR-0001: Standar Kerja Tidak Terikat Satu AI Tool

**Status:** Accepted
**Tanggal:** 2026-11-15

## Context
Theme Webane dikerjakan bergantian dengan beberapa AI agent (Claude Code,
Antigravity, Codex) dan manusia. Menyimpan aturan ganda di `AGENTS.md` dan
`CLAUDE.md` pasti menyebabkan drift.

## Decision
1. `AGENTS.md` adalah sumber aturan tunggal. `CLAUDE.md` hanya berisi
   `@AGENTS.md` (import). Tool lain membaca `AGENTS.md` langsung.
2. Proses dan checklist ditulis sebagai markdown biasa di `docs/` (SOP,
   security checklist), bukan sebagai fitur khusus satu tool.
3. Enforcement nyata ada di **Composer scripts + CI GitHub Actions** —
   berlaku siapa pun yang menulis kode.

## Alternatif yang Dipertimbangkan
- Pakai hanya `.claude/hooks` — ditolak: mengunci ke satu vendor.
- Duplikasi isi ke `AGENTS.md` dan `CLAUDE.md` — ditolak: pasti drift.

## Konsekuensi
- Ganti/tambah AI tool tidak mengubah standar.
- Aturan yang benar-benar wajib harus bisa dicek mesin (PHPCS/PHPStan/CI).
