"""Normalização de keyframes para o aspecto do preset."""

from __future__ import annotations

from pathlib import Path

from PIL import Image


def encaixar_cover(
    origem: Path,
    destino: Path,
    largura: int,
    altura: int,
    foco_x: float = 0.5,
    foco_y: float = 0.5,
) -> None:
    """Recorta e redimensiona em modo cover (preenche o quadro inteiro)."""
    im = Image.open(origem).convert("RGB")
    iw, ih = im.size
    target_ratio = largura / altura
    src_ratio = iw / ih

    if src_ratio > target_ratio:
        new_w = int(ih * target_ratio)
        new_h = ih
    else:
        new_w = iw
        new_h = int(iw / target_ratio)

    left = int((iw - new_w) * foco_x)
    top = int((ih - new_h) * foco_y)
    left = max(0, min(left, iw - new_w))
    top = max(0, min(top, ih - new_h))

    cropped = im.crop((left, top, left + new_w, top + new_h))
    cropped.resize((largura, altura), Image.Resampling.LANCZOS).save(destino, quality=95)


def preparar_keyframes(
    preset: dict,
    keyframes_dir: Path,
    saida_dir: Path,
) -> list[Path]:
    """Gera PNGs normalizados em saida_dir/frames/."""
    frames_dir = saida_dir / "frames"
    frames_dir.mkdir(parents=True, exist_ok=True)

    largura = int(preset.get("largura", 1920))
    altura = int(preset.get("altura", 1080))
    focos = preset.get("focos", {})

    saidas: list[Path] = []
    for cena in preset["cenas"]:
        cid = cena["id"]
        arquivo = cena["arquivo"]
        origem = keyframes_dir / arquivo
        if not origem.is_file():
            raise FileNotFoundError(f"Keyframe ausente: {origem}")

        foco = focos.get(cid, {})
        fx = float(foco.get("x", 0.5))
        fy = float(foco.get("y", 0.5))

        destino = frames_dir / arquivo
        encaixar_cover(origem, destino, largura, altura, fx, fy)
        saidas.append(destino)

    return saidas
