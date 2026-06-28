"""Carrega presets YAML das cinemáticas."""

from __future__ import annotations

import re
from pathlib import Path

PRESETS_DIR = Path(__file__).resolve().parent / "presets"


def listar_presets() -> list[str]:
    return sorted(
        p.stem
        for p in PRESETS_DIR.glob("*.yaml")
        if not p.name.startswith("_")
    )


def _parse_simple_yaml(text: str) -> dict:
    """Parser mínimo para o formato de preset do projeto (sem dependência PyYAML)."""
    data: dict = {}
    lines = text.splitlines()
    i = 0

    def read_scalar(key: str) -> None:
        nonlocal i
        m = re.match(rf"^{re.escape(key)}:\s*(.*)$", lines[i])
        if not m:
            return
        val = m.group(1).strip().strip('"').strip("'")
        if val in ("", "|", ">"):
            i += 1
            block: list[str] = []
            while i < len(lines):
                if lines[i] and not lines[i].startswith(" ") and not lines[i].startswith("\t"):
                    break
                block.append(re.sub(r"^  ?", "", lines[i]))
                i += 1
            data[key] = "\n".join(block).strip("\n")
            return
        data[key] = val
        i += 1

    scalar_keys = [
        "slug", "titulo", "aspecto", "voz", "lang_code",
    ]
    numeric_keys = ["largura", "altura", "fps", "velocidade_voz"]

    while i < len(lines):
        line = lines[i]
        if not line.strip() or line.strip().startswith("#"):
            i += 1
            continue

        if line.startswith("referencias:"):
            i += 1
            refs: list[str] = []
            while i < len(lines) and lines[i].startswith("  - "):
                refs.append(lines[i][4:].strip())
                i += 1
            data["referencias"] = refs
            continue

        if line.startswith("narracao:"):
            data["narracao"] = ""
            i += 1
            block = []
            while i < len(lines):
                if lines[i] and not lines[i].startswith("  ") and lines[i] != "":
                    if not lines[i].startswith("\t"):
                        break
                if lines[i].startswith("  "):
                    block.append(lines[i][2:])
                elif lines[i] == "":
                    block.append("")
                else:
                    break
                i += 1
            data["narracao"] = "\n".join(block).strip("\n")
            continue

        if line.startswith("cenas:"):
            i += 1
            cenas: list[dict] = []
            while i < len(lines):
                if lines[i].startswith("  - id:"):
                    cena: dict = {"id": lines[i].split(":", 1)[1].strip()}
                    i += 1
                    while i < len(lines) and lines[i].startswith("    "):
                        sub = lines[i].strip()
                        if sub.startswith("prompt:"):
                            i += 1
                            prompt_lines = []
                            while i < len(lines) and (
                                lines[i].startswith("      ") or lines[i] == ""
                            ):
                                if lines[i].startswith("      "):
                                    prompt_lines.append(lines[i][6:])
                                elif lines[i] == "" and prompt_lines:
                                    prompt_lines.append("")
                                else:
                                    break
                                i += 1
                            cena["prompt"] = "\n".join(prompt_lines).strip("\n")
                            continue
                        key, _, val = sub.partition(":")
                        cena[key.strip()] = val.strip()
                        i += 1
                    cenas.append(cena)
                    continue
                if lines[i].strip() and not lines[i].startswith("  "):
                    break
                i += 1
            data["cenas"] = cenas
            continue

        for key in scalar_keys:
            if line.startswith(f"{key}:"):
                read_scalar(key)
                break
        else:
            for key in numeric_keys:
                if line.startswith(f"{key}:"):
                    read_scalar(key)
                    data[key] = float(data[key]) if key == "velocidade_voz" else int(data[key])
                    break
            else:
                i += 1

    return data


def carregar_preset(slug: str) -> dict:
    path = PRESETS_DIR / f"{slug}.yaml"
    if not path.is_file():
        raise FileNotFoundError(f"Preset não encontrado: {slug} ({path})")

    text = path.read_text(encoding="utf-8")
    try:
        import yaml  # type: ignore

        return yaml.safe_load(text)
    except ImportError:
        return _parse_simple_yaml(text)
