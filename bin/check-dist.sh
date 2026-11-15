#!/usr/bin/env bash
# Simulasi pembuatan paket distribusi rilis dan verifikasi bahwa TIDAK ADA
# file .md, folder docs/, folder AI (.claude/.agents/dll), atau file tooling
# yang ikut masuk ke paket customer.
set -euo pipefail

TMP_DIST="$(mktemp -d)"
trap 'rm -rf "$TMP_DIST"' EXIT

rsync -a --exclude-from=.distignore ./ "$TMP_DIST/makhadane/"

# Cari file/folder terlarang di dalam paket hasil build
FORBIDDEN=$(
  find "$TMP_DIST/makhadane" \( \
    -iname '*.md' \
    -o -name '.*' \
    -o -name 'docs' \
    -o -name 'blueprints' \
    -o -name 'bin' \
    -o -name 'vendor' \
    -o -name 'node_modules' \
    -o -name 'composer.*' \
    -o -name 'package*.json' \
    -o -name 'phpcs*' \
    -o -name 'phpstan*' \
    -o -name '*.map' \
  \)
)

if [ -n "$FORBIDDEN" ]; then
  echo "❌ GAGAL: Ditemukan file/folder internal yang ikut masuk ke paket rilis:"
  echo "$FORBIDDEN"
  exit 1
fi

if [ -d "$TMP_DIST/makhadane/docs" ]; then
  echo "❌ GAGAL: Folder docs/ masih ikut masuk ke paket rilis!"
  exit 1
fi

echo "✅ Paket rilis bersih. Tidak ada file .md, folder docs/AI, atau tooling dev."
echo "Ringkasan isi root paket rilis (makhadane/):"
ls -la "$TMP_DIST/makhadane"
