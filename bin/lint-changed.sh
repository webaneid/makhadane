#!/usr/bin/env bash
# Jalankan PHPCS hanya pada file PHP yang berubah dibanding branch dasar
# (default: main), termasuk perubahan yang belum di-commit dan file baru.
# Pemakaian: composer lint:changed            (dasar: main)
#            BASE=origin/main composer lint:changed
set -euo pipefail

BASE="${BASE:-main}"

FILES=$(
  {
    git diff --name-only --diff-filter=ACMR "$BASE" -- '*.php' 2>/dev/null || true
    git ls-files --others --exclude-standard -- '*.php'
  } | { grep -v '^vendor/' || true; } | sort -u
)

if [ -z "$FILES" ]; then
  echo "Tidak ada file PHP yang berubah dibanding $BASE."
  exit 0
fi

echo "File yang dicek:"
echo "$FILES" | sed 's/^/  - /'
echo

# shellcheck disable=SC2086
vendor/bin/phpcs $FILES
