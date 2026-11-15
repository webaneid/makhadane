#!/usr/bin/env bash
# Pemindai keamanan otomatis theme WordPress Webane:
# 1. Deteksi file PHP/script mencurigakan di dalam folder aset (anti-webshell drop)
# 2. Deteksi fungsi berbahaya khas webshell/backdoor (eval, base64_decode, shell_exec, dll)
# 3. Deteksi secret/API key yang ter-hardcode (Google API Key, GitHub PAT, AWS Key)
# 4. Pastikan setiap file PHP theme memiliki guard ABSPATH (anti direct-access)
set -euo pipefail

FAILED=0

echo "🔍 [1/4] Memeriksa folder aset (css, js, img, fonts) dari penyusupan file PHP/executable..."
SUSPICIOUS_ASSETS=$(find css js img fonts -type f \( -iname '*.php*' -o -iname '*.phtml' -o -iname '*.phar' -o -iname '*.sh' -o -iname '*.cgi' -o -iname '*.pl' \) 2>/dev/null || true)
if [ -n "$SUSPICIOUS_ASSETS" ]; then
  echo "❌ BAHAYA: Ditemukan file executable di dalam folder aset:"
  echo "$SUSPICIOUS_ASSETS"
  FAILED=1
fi

echo "🔍 [2/4] Memeriksa fungsi berbahaya/obfuscation khas webshell (eval, base64_decode, shell_exec, dll)..."
DANGEROUS_CALLS=$(grep -rnE '\b(eval|base64_decode|gzinflate|gzuncompress|str_rot13|shell_exec|exec|passthru|system|proc_open|popen|create_function|move_uploaded_file)\s*\(' --include='*.php' . | grep -vE '^\./(vendor|\.phpstan-cache)/' || true)
if [ -n "$DANGEROUS_CALLS" ]; then
  echo "❌ BAHAYA: Ditemukan pemanggilan fungsi berisiko tinggi:"
  echo "$DANGEROUS_CALLS"
  FAILED=1
fi

echo "🔍 [3/4] Memeriksa hardcoded API key / token rahasia..."
HARDCODED_SECRETS=$(grep -rnE '(AIza[0-9A-Za-z_-]{35}|ghp_[0-9A-Za-z]{36}|github_pat_[0-9A-Za-z_]{25,}|AKIA[0-9A-Z]{16})' --include='*.php' --include='*.js' . | grep -v '^./vendor/' || true)
if [ -n "$HARDCODED_SECRETS" ]; then
  echo "❌ BAHAYA: Ditemukan hardcoded API key/token di kode:"
  echo "$HARDCODED_SECRETS"
  FAILED=1
fi

echo "🔍 [4/4] Memeriksa guard ABSPATH (anti direct-access) di seluruh file PHP..."
MISSING_ABSPATH=""
while IFS= read -r -d '' phpfile; do
  if ! grep -q "ABSPATH" "$phpfile"; then
    MISSING_ABSPATH="${MISSING_ABSPATH}  - ${phpfile}\n"
  fi
done < <(find . -name '*.php' -not -path './vendor/*' -not -path './node_modules/*' -not -path './dist/*' -not -path './.phpstan-cache/*' -not -path './languages/*' -print0)

if [ -n "$MISSING_ABSPATH" ]; then
  echo "❌ PERINGATAN: File PHP berikut belum memiliki guard defined( 'ABSPATH' ) || exit;:"
  printf "%b" "$MISSING_ABSPATH"
  FAILED=1
fi

if [ "$FAILED" -ne 0 ]; then
  echo "⛔ Security scan GAGAL. Perbaiki temuan di atas sebelum lanjut."
  exit 1
fi

echo "✅ Security scan LOLOS: Tidak ada indikasi webshell, hardcoded secret, atau file PHP tanpa guard ABSPATH."
