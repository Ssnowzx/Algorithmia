#!/usr/bin/env bash
# ============================================================
#  Algorithmia — gera WebP otimizado das ILUSTRAÇÕES.
# ============================================================
# Os PNG continuam sendo a fonte editável (e o fallback). Este script gera um
# .webp ao lado de cada ilustração; o helper svg() serve o .webp quando existe.
#
# A PIXEL ART (herois/heroi-*, inimigos/, itens/) NÃO é tocada: é minúscula e
# borraria com compressão com perda.
#
# Requisitos: cwebp (libwebp) e sips (macOS) ou ImageMagick.
# Uso: bash tools/otimizar-imagens.sh
set -euo pipefail

BASE="$(cd "$(dirname "$0")/../public/img" && pwd)"
MAXDIM=1280   # maior lado; só reduz se exceder
Q=80          # qualidade WebP com perda

command -v cwebp >/dev/null || { echo "Erro: cwebp não encontrado (brew install webp)"; exit 1; }

redimensionar() { # $1=entrada $2=saida — reduz para MAXDIM no maior lado
    if command -v sips >/dev/null; then
        sips -Z "$MAXDIM" "$1" --out "$2" >/dev/null 2>&1
    elif command -v magick >/dev/null; then
        magick "$1" -resize "${MAXDIM}x${MAXDIM}>" "$2" >/dev/null 2>&1
    else
        cp "$1" "$2"
    fi
}

otimizar() {
    local png="$1"
    local webp="${png%.png}.webp"
    local tmp="${TMPDIR:-/tmp}/otim-$$.png"
    redimensionar "$png" "$tmp"
    cwebp -quiet -q "$Q" "$tmp" -o "$webp" >/dev/null 2>&1
    rm -f "$tmp"
    echo "  ${png#"$BASE"/} -> $(du -h "$webp" | cut -f1)"
}

echo "Otimizando ilustrações em $BASE ..."
find "$BASE/fundos" "$BASE/mapas" "$BASE/mestres" "$BASE/ui" "$BASE/atores" \
    -type f -name '*.png' 2>/dev/null | while read -r f; do otimizar "$f"; done
find "$BASE/herois" -type f -name 'hud-*.png' 2>/dev/null | while read -r f; do otimizar "$f"; done

echo "Pronto. Total WebP: $(find "$BASE" -name '*.webp' | wc -l | tr -d ' ') arquivos."
