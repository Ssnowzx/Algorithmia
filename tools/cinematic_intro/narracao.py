"""Narração neural PT-BR via Kokoro-82M."""

from __future__ import annotations

import asyncio
import os
import subprocess
import sys
from pathlib import Path

RAIZ = Path(__file__).resolve().parents[2]
VENV_DIR = RAIZ / "tools" / ".venvs" / "cinematic"
VENV_PYTHON = VENV_DIR / "bin" / "python3.11"
if not VENV_PYTHON.is_file():
    VENV_PYTHON = VENV_DIR / "bin" / "python3"


def garantir_venv() -> Path:
    """Cria venv com kokoro + soundfile se necessário."""
    if VENV_PYTHON.is_file():
        try:
            subprocess.run(
                [str(VENV_PYTHON), "-c", "import kokoro, soundfile"],
                check=True,
                capture_output=True,
            )
            return VENV_PYTHON
        except subprocess.CalledProcessError:
            pass

    VENV_DIR.parent.mkdir(parents=True, exist_ok=True)
    py = "/opt/homebrew/bin/python3.11"
    if not Path(py).is_file():
        py = sys.executable

    subprocess.run([py, "-m", "venv", str(VENV_DIR)], check=True)
    pip = VENV_DIR / "bin" / "pip"
    subprocess.run(
        [str(pip), "install", "kokoro>=0.9.4", "soundfile", "numpy", "pillow"],
        check=True,
    )

    # espeak-ng necessário para PT-BR no Kokoro (macOS)
    if subprocess.run(["which", "espeak-ng"], capture_output=True).returncode != 0:
        print(
            "Aviso: instale espeak-ng para PT-BR — brew install espeak-ng",
            file=sys.stderr,
        )

    return VENV_PYTHON


def gerar_narracao(
    texto: str,
    destino_wav: Path,
    voz: str = "pm_santa",
    lang_code: str = "p",
    velocidade: float = 0.92,
) -> Path:
    destino_wav.parent.mkdir(parents=True, exist_ok=True)
    python = garantir_venv()

    script = f'''
import asyncio
import numpy as np
import soundfile as sf
from kokoro import KPipeline

TEXT = {texto!r}

async def main():
    pipe = KPipeline(lang_code={lang_code!r}, repo_id="hexgrad/Kokoro-82M")
    chunks = []
    for _, _, audio in pipe(TEXT, voice={voz!r}, speed={velocidade}, split_pattern=r"\\n+"):
        chunks.append(audio)
    sf.write({str(destino_wav)!r}, np.concatenate(chunks), 24000)

asyncio.run(main())
'''
    subprocess.run([str(python), "-c", script], check=True, env=os.environ.copy())
    return destino_wav


def masterizar_audio(origem_wav: Path, destino_mp3: Path) -> Path:
    """Normalização leve — sem pitch shift nem efeitos que soam robóticos."""
    destino_mp3.parent.mkdir(parents=True, exist_ok=True)
    subprocess.run(
        [
            "ffmpeg", "-y",
            "-i", str(origem_wav),
            "-af", "highpass=f=60,loudnorm=I=-16:TP=-1.5:LRA=11",
            str(destino_mp3),
        ],
        check=True,
        capture_output=True,
    )
    return destino_mp3
