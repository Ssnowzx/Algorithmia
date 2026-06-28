# Cinemáticas — Intros em vídeo das fases

Cada região do mapa pode ter uma **intro cinematográfica** (vídeo narrado 16:9) que
abre num player ao clicar no ícone da região. Pipeline reutilizável: **preset YAML →
keyframes ilustrados → narração PT-BR (Kokoro) → montagem FFmpeg**.

> Workflow detalhado para agentes: `.cursor/skills/cinematic-intro-algorithmia/SKILL.md`.
> Este doc é o resumo para humanos. **Galeria visual das 7 intros:**
> [`docs/cinematics/README.md`](../cinematics/README.md).

## Onde mora o quê

| Caminho | Papel |
|---|---|
| `tools/gerar_cinematic_intro.py` | CLI do pipeline |
| `tools/cinematic_intro/` | módulos (marca visual, narração, imagens, vídeo) |
| `tools/cinematic_intro/presets/<slug>.yaml` | um preset por cinemática (narração, cenas, durações, refs) |
| `docs/cinematics/<slug>/` | saída-fonte: `keyframes/` (PNGs fonte), `narracao.mp3`, `manifest.json` |
| `public/video/cinematics/<slug>.mp4` | **vídeo servido pela web** (é o que o jogo carrega) |
| `tools/dev-server-router.php` | router de dev p/ testar vídeo no `php -S` (suporte a Range) |

## As 7 cinemáticas

| Slug | Região | Mestre |
|---|---|---|
| `terras-hello-world` | Terras de Hello World (início) | — |
| `willen` | Porto da Sintaxe | Willen, o Arquiteto |
| `clayton` | Cidadela dos Objetos | Clayton, o Moldador |
| `marcelo` | Floresta das Estruturas | Marcelo, o Andarilho |
| `cesar` | Montanha do Cálculo | César, o Oráculo |
| `cassandro` | Torre das Conexões | Cassandro, o Mensageiro |
| `abismo-devnull` | O Abismo (fim) | O Lorde do Vazio |

## Como o mapa liga o vídeo (sem código novo)

O helper `videoIntroRegiao()` (`app/core/helpers.php`) resolve o vídeo de cada região
por **convenção de slug**, e `app/views/mapa/index.php` mostra o selo ▶ **só quando o
arquivo existe**:

- início → `terras-hello-world`
- região de mestre → `svg_slug` sem o prefixo `mestre-` (`mestre-willen` → `willen`)
- fim → `abismo-devnull`

Ou seja: basta publicar `public/video/cinematics/<slug>.mp4` que o ▶ aparece sozinho.

## Gerar / regerar uma cinemática

```bash
python3 tools/gerar_cinematic_intro.py --listar          # lista presets
python3 tools/gerar_cinematic_intro.py <slug> --prompts-sora   # prompts p/ Sora/ChatGPT
python3 tools/gerar_cinematic_intro.py <slug>            # pipeline completo (narração+vídeo)
python3 tools/gerar_cinematic_intro.py <slug> --somente-video  # só remonta (keyframes já existem)
```

O pipeline **publica automaticamente** o MP4 em `public/video/cinematics/<slug>.mp4`.
Dependências: `ffmpeg` + `espeak-ng` (`brew install espeak-ng`); o venv Kokoro em
`tools/.venvs/cinematic/` é criado na 1ª execução.

### Decisões fixas (não reverter)
- **Narração:** Kokoro `pm_santa`, `velocidade_voz: 0.92`, `lang_code: p`. **Sem termos
  em inglês/siglas na fala** — a voz erra a pronúncia; jargão técnico fica na imagem/texto,
  não na narração. (Ex.: o preset usa "César" com acento de propósito.)
- **Formato:** horizontal 16:9 full-bleed (1920×1080, 30fps).
- **Estilo:** ilustração pintada à mão, magia+código; paleta navy/roxo/ouro/ciano; sem
  texto legível, HUD ou pixel art.

## Testar localmente

O servidor embutido `php -S` **não faz streaming de MP4** (sem Range/206). Para testar:

```bash
php -S 127.0.0.1:8000 tools/dev-server-router.php
```

Em produção (Apache) o Range é nativo. Se um vídeo travar após uma tentativa falha,
limpe o cache do navegador para aquela URL.

## Versionamento / deploy

- **Versionado:** presets, pipeline, `keyframes/` (fonte), `narracao.mp3`, `manifest.json`
  e os MP4 servidos em `public/video/cinematics/`.
- **Ignorado** (regenerável — ver `.gitignore`): `docs/cinematics/*/frames/`,
  `*/narracao-raw.wav`, o `.mp4` duplicado dentro de `docs/`, e `tools/.venvs/`.
- **Deploy (VPS):** `git pull` + restart do Apache. Os vídeos seguem no `public/`, sem
  migrations.
