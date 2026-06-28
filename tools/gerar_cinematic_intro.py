#!/usr/bin/env python3
"""
Gera cinemática Algorithmia: keyframes + narração Kokoro + vídeo MP4.

Uso:
  python3 tools/gerar_cinematic_intro.py terras-hello-world
  python3 tools/gerar_cinematic_intro.py --listar
  python3 tools/gerar_cinematic_intro.py terras-hello-world --somente-narracao
  python3 tools/gerar_cinematic_intro.py terras-hello-world --prompts
  python3 tools/gerar_cinematic_intro.py willen --prompts-sora
  python3 tools/gerar_cinematic_intro.py meu-slug --keyframes-dir /caminho/pngs

Saída padrão: docs/cinematics/<slug>/
"""

from __future__ import annotations

import argparse
import shutil
import sys
from pathlib import Path

RAIZ = Path(__file__).resolve().parents[1]
sys.path.insert(0, str(RAIZ))

from cinematic_intro.imagens import preparar_keyframes
from cinematic_intro.marca_visual import prompt_cena, prompt_sora_cena
from cinematic_intro.narracao import gerar_narracao, masterizar_audio
from cinematic_intro.presets_loader import carregar_preset, listar_presets
from cinematic_intro.video import montar_video, salvar_manifest

SAIDA_BASE = RAIZ / "docs" / "cinematics"
# O jogo só serve assets de public/ (docs/ é bloqueado pelo .htaccess); por isso
# o MP4 final também é copiado para cá, de onde a tela do mapa o carrega.
PUBLICO_VIDEO = RAIZ / "public" / "video" / "cinematics"


def _dir_saida(slug: str) -> Path:
    return SAIDA_BASE / slug


def _publicar_web(mp4: Path, slug: str) -> Path:
    """Publica o MP4 em public/video/cinematics/<slug>.mp4 (servido pela web)."""
    PUBLICO_VIDEO.mkdir(parents=True, exist_ok=True)
    destino = PUBLICO_VIDEO / f"{slug}.mp4"
    shutil.copy2(mp4, destino)
    print(f"  ✓ publicado em {destino.relative_to(RAIZ)}")
    return destino


def _dir_keyframes(slug: str, override: Path | None) -> Path:
    if override:
        return override
    return _dir_saida(slug) / "keyframes"


def cmd_prompts_sora(preset: dict) -> None:
    """Prompts para Sora: bíblia de estilo + cena (um bloco por cena)."""
    print(f"# Prompts Sora — {preset.get('titulo', preset['slug'])}\n")
    print("Referências sugeridas (anexar no ChatGPT/Sora):")
    for ref in preset.get("referencias", []):
        print(f"  - {RAIZ / ref}")
    print()
    for i, cena in enumerate(preset["cenas"], 1):
        print(f"## Cena {i} — {cena['id']} → {cena['arquivo']}\n")
        print(prompt_sora_cena(cena["prompt"]))
        print("\n---\n")


def cmd_prompts(preset: dict) -> None:
    aspecto = preset.get("aspecto", "16:9")
    print(f"# Prompts — {preset.get('titulo', preset['slug'])} ({aspecto})\n")
    print("Referências sugeridas:")
    for ref in preset.get("referencias", []):
        print(f"  - {RAIZ / ref}")
    print()
    for cena in preset["cenas"]:
        print(f"## {cena['id']} → {cena['arquivo']}\n")
        print(prompt_cena(cena["prompt"], aspecto))
        print("\n---\n")


def cmd_narracao(preset: dict, saida: Path) -> tuple[Path, Path]:
    wav = saida / "narracao-raw.wav"
    mp3 = saida / "narracao.mp3"
    print(f"→ Narração ({preset.get('voz', 'pm_santa')})…")
    gerar_narracao(
        preset["narracao"],
        wav,
        voz=preset.get("voz", "pm_santa"),
        lang_code=preset.get("lang_code", "p"),
        velocidade=float(preset.get("velocidade_voz", 0.92)),
    )
    masterizar_audio(wav, mp3)
    print(f"  ✓ {mp3}")
    return wav, mp3


def cmd_video(preset: dict, saida: Path, keyframes_dir: Path) -> Path:
    print("→ Normalizando keyframes…")
    frames = preparar_keyframes(preset, keyframes_dir, saida)
    for f in frames:
        print(f"  ✓ {f.name}")

    duracoes = [float(c["duracao"]) for c in preset["cenas"]]
    mp3 = saida / "narracao.mp3"
    if not mp3.is_file():
        cmd_narracao(preset, saida)

    destino = saida / f"{preset['slug']}.mp4"
    print("→ Montando vídeo…")
    montar_video(
        frames, duracoes, mp3, destino,
        fps=int(preset.get("fps", 30)),
        largura=int(preset.get("largura", 1920)),
        altura=int(preset.get("altura", 1080)),
    )
    print(f"  ✓ {destino}")
    _publicar_web(destino, preset["slug"])
    return destino


def main() -> int:
    parser = argparse.ArgumentParser(description="Gera cinemática Algorithmia")
    parser.add_argument("slug", nargs="?", help="Slug do preset (ex.: terras-hello-world)")
    parser.add_argument("--listar", action="store_true", help="Lista presets disponíveis")
    parser.add_argument("--prompts", action="store_true", help="Imprime prompts para GenerateImage (marca já inclusa)")
    parser.add_argument("--prompts-sora", action="store_true", help="Imprime prompts Sora (bíblia + cena por bloco)")
    parser.add_argument("--somente-narracao", action="store_true")
    parser.add_argument("--somente-video", action="store_true")
    parser.add_argument("--keyframes-dir", type=Path, help="Pasta com PNGs das cenas")
    parser.add_argument("--importar", type=Path, help="Copia PNGs de outra pasta para keyframes/")
    args = parser.parse_args()

    if args.listar:
        for slug in listar_presets():
            p = carregar_preset(slug)
            print(f"  {slug:30} {p.get('titulo', '')}")
        return 0

    if not args.slug:
        parser.print_help()
        return 1

    preset = carregar_preset(args.slug)
    saida = _dir_saida(preset["slug"])
    saida.mkdir(parents=True, exist_ok=True)
    keyframes = _dir_keyframes(preset["slug"], args.keyframes_dir)

    if args.importar:
        keyframes.mkdir(parents=True, exist_ok=True)
        for cena in preset["cenas"]:
            src = args.importar / cena["arquivo"]
            if not src.is_file():
                # tenta por nome parcial
                matches = list(args.importar.glob(f"*{cena['id']}*"))
                src = matches[0] if matches else src
            if src.is_file():
                shutil.copy2(src, keyframes / cena["arquivo"])
                print(f"  importado {src.name} → {cena['arquivo']}")

    if args.prompts_sora:
        cmd_prompts_sora(preset)
        return 0

    if args.prompts:
        cmd_prompts(preset)
        return 0

    artefatos: dict = {}

    if args.somente_narracao:
        _, mp3 = cmd_narracao(preset, saida)
        artefatos["narracao"] = str(mp3.relative_to(RAIZ))
        salvar_manifest(saida, preset, artefatos)
        return 0

    if args.somente_video:
        mp4 = cmd_video(preset, saida, keyframes)
        artefatos["video"] = str(mp4.relative_to(RAIZ))
        artefatos["video_publico"] = f"public/video/cinematics/{preset['slug']}.mp4"
        salvar_manifest(saida, preset, artefatos)
        return 0

    # Pipeline completo
    _, mp3 = cmd_narracao(preset, saida)
    mp4 = cmd_video(preset, saida, keyframes)
    artefatos["narracao"] = str(mp3.relative_to(RAIZ))
    artefatos["video"] = str(mp4.relative_to(RAIZ))
    artefatos["video_publico"] = f"public/video/cinematics/{preset['slug']}.mp4"
    salvar_manifest(saida, preset, artefatos)
    print(f"\nConcluído: {mp4}")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
