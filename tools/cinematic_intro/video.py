"""Montagem do MP4 com ffmpeg."""

from __future__ import annotations

import json
import subprocess
from pathlib import Path


def _duracao(path: Path) -> float:
    out = subprocess.run(
        [
            "ffprobe", "-v", "error",
            "-show_entries", "format=duration",
            "-of", "default=noprint_wrappers=1:nokey=1",
            str(path),
        ],
        check=True,
        capture_output=True,
        text=True,
    )
    return float(out.stdout.strip())


def montar_video(
    frames: list[Path],
    duracoes: list[float],
    audio_mp3: Path,
    destino_mp4: Path,
    fps: int = 30,
    crf: int = 18,
    largura: int = 1920,
    altura: int = 1080,
) -> Path:
    if len(frames) != len(duracoes):
        raise ValueError("frames e duracoes devem ter o mesmo tamanho")

    destino_mp4.parent.mkdir(parents=True, exist_ok=True)
    audio_dur = _duracao(audio_mp3)
    video_dur = sum(duracoes)
    if video_dur < audio_dur + 0.5:
        extra = audio_dur + 1.5 - video_dur
        duracoes = duracoes.copy()
        duracoes[-1] += extra
        video_dur = sum(duracoes)

    inputs: list[str] = []
    for frame, dur in zip(frames, duracoes):
        inputs.extend(["-loop", "1", "-t", f"{dur:.3f}", "-i", str(frame)])
    inputs.extend(["-i", str(audio_mp3)])

    n = len(frames)
    filters = []
    for i in range(n):
        fade_out = max(0.1, duracoes[i] - 0.7)
        sw = largura + 80
        sh = altura + 45
        filters.append(
            f"[{i}:v]fps={fps},scale={sw}:{sh},"
            f"crop={largura}:{altura}:'(iw-ow)/2':'(ih-oh)/2+12*sin(2*PI*t/{18 + i*4})',"
            f"setsar=1,fade=t=in:st=0:d=0.6,fade=t=out:st={fade_out:.2f}:d=0.7[v{i}]"
        )

    concat_in = "".join(f"[v{i}]" for i in range(n))
    fade_audio = max(0.1, video_dur - 1.2)
    filters.append(f"{concat_in}concat=n={n}:v=1:a=0,format=yuv420p[v]")
    filters.append(
        f"[{n}:a]apad,atrim=0:{video_dur:.3f},"
        f"afade=t=in:st=0:d=0.15,afade=t=out:st={fade_audio:.2f}:d=1.2[a]"
    )

    cmd = [
        "ffmpeg", "-y",
        *inputs,
        "-filter_complex", ";".join(filters),
        "-map", "[v]", "-map", "[a]",
        "-c:v", "libx264", "-crf", str(crf), "-preset", "medium",
        "-c:a", "aac", "-b:a", "192k",
        "-movflags", "+faststart",
        str(destino_mp4),
    ]
    subprocess.run(cmd, check=True)
    return destino_mp4


def salvar_manifest(saida_dir: Path, preset: dict, artefatos: dict) -> Path:
    manifest = {
        "slug": preset["slug"],
        "titulo": preset.get("titulo"),
        "aspecto": preset.get("aspecto", "16:9"),
        "voz": preset.get("voz"),
        **artefatos,
    }
    path = saida_dir / "manifest.json"
    path.write_text(json.dumps(manifest, indent=2, ensure_ascii=False), encoding="utf-8")
    return path
