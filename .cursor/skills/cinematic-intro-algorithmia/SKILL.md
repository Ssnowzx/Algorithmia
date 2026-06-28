---
name: cinematic-intro-algorithmia
description: >-
  Gera cinemáticas do Algorithmia (keyframes, narração PT-BR neural e vídeo MP4)
  com identidade visual consistente. Use quando o usuário pedir intro, vídeo
  narrado, cinemática, trailer, keyframes ou narração para fases/regiões do jogo.
---

# Cinemáticas Algorithmia

Pipeline padronizado: **preset YAML → keyframes → narração Kokoro → MP4**.

## Formato padrão

- **Vídeo:** horizontal **16:9** (1920×1080) — padrão atual do projeto
- **Narração:** Kokoro-82M PT-BR (`pm_santa` = narrador grave/sombrio; `pm_alex` = neutro)
- **Estilo:** `tools/cinematicas/cinematic_intro/marca_visual.py` (paleta navy/roxo/ouro/ciano, magia+código)
- **Saída:** `docs/cinematics/<slug>/` (fonte) **+** cópia publicada em `public/video/cinematics/<slug>.mp4` (servida pela web)

## Integração com o mapa do jogo (intro por região)

O MP4 final é **publicado automaticamente** em `public/video/cinematics/<slug>.mp4`
(o jogo só serve assets de `public/`; `docs/` é bloqueado pelo `.htaccess`). A tela
do mapa (`app/views/mapa/index.php` + helper `videoIntroRegiao()`) então mostra
**sozinha** um selo ▶ no ícone da região e abre o player ao clicar — **sem código novo**,
basta o arquivo existir com o slug certo:

| Região do mapa | Slug do preset/arquivo |
|---|---|
| Terras de Hello World (início) | `terras-hello-world` |
| Região de um mestre | nome do mestre = `svg_slug` sem o prefixo `mestre-` (ex.: `mestre-willen` → `willen`) |
| O Abismo (fim) | `abismo-devnull` |

> Testar vídeo **localmente**: o `php -S` não faz streaming de MP4 (sem Range/206).
> Rode com o router de dev: `php -S 127.0.0.1:8000 tools/deploy/dev-server-router.php`
> (em produção o Apache já trata Range). Se o vídeo travar após uma tentativa
> falha, limpe o cache do navegador para aquela URL.

## Workflow do agente

### 1. Novo cinematic

1. Copie `tools/cinematicas/cinematic_intro/presets/_template.yaml` → `tools/cinematicas/cinematic_intro/presets/<slug>.yaml`
2. Preencha `narracao`, `cenas[]` (id, arquivo, duracao, prompt) e `referencias`
3. Gere prompts completos:
   ```bash
   python3 tools/cinematicas/gerar_cinematic_intro.py <slug> --prompts
   ```
4. Gere as imagens com `GenerateImage` (uma por cena), usando:
   - prompt do passo 3
   - `reference_image_paths`: refs do preset + frames anteriores para consistência
   - Salve em `docs/cinematics/<slug>/keyframes/<arquivo>`
5. Execute o pipeline:
   ```bash
   python3 tools/cinematicas/gerar_cinematic_intro.py <slug>
   ```

### 2. Cinemática existente (só remontar)

Se os PNGs já existem em `docs/cinematics/<slug>/keyframes/`:

```bash
python3 tools/cinematicas/gerar_cinematic_intro.py <slug> --somente-video
```

### 3. Importar keyframes de outro lugar

```bash
python3 tools/cinematicas/gerar_cinematic_intro.py terras-hello-world \
  --importar /caminho/dos/pngs
python3 tools/cinematicas/gerar_cinematic_intro.py terras-hello-world
```

## Comandos úteis

| Comando | Ação |
|---------|------|
| `--listar` | Lista presets |
| `--prompts` | Imprime prompts (marca inclusa) |
| `--prompts-sora` | Imprime blocos Sora (bíblia + cena) |
| `--somente-narracao` | Só gera `narracao.mp3` |
| `--somente-video` | Só monta MP4 (requer keyframes + narração) |
| `--keyframes-dir PATH` | Usa outra pasta de PNGs |

## Regras de consistência

1. **Nunca** usar `say` do macOS nem Edge TTS com pitch shift — soa robótico
2. **Narração:** Kokoro `pm_santa` + `velocidade_voz: 0.92`; só `loudnorm` leve
3. **Imagens:** full-bleed no aspecto do preset (cover crop, sem letterbox)
4. **Sem texto legível** nas imagens — só glifos decorativos
5. Anexe `public/img/mapas/regiao-*.png` como referência de cenário
6. Para tom ácido perfeito: oferecer faixa sem narração para gravação humana

## Bíblia de estilo

Texto canônico em `tools/cinematicas/cinematic_intro/marca_visual.py` (`BIBLIA_ESTILO`).

| Comando | Uso |
|---------|-----|
| `--prompts` | Pipeline interno / GenerateImage — marca já inclusa, **não** duplicar bíblia |
| `--prompts-sora` | Sora/ChatGPT — bíblia + `---` + descrição da cena em cada bloco |

## Presets disponíveis

| Slug | Região / mestre |
|------|-----------------|
| `terras-hello-world` | Intro — Terras de Hello World (início) |
| `willen` | Porto da Sintaxe — Willen, o Arquiteto |
| `clayton` | Cidadela dos Objetos — Clayton, o Moldador |
| `marcelo` | Floresta das Estruturas — Marcelo, o Andarilho |
| `cesar` | Montanha do Cálculo — César, o Oráculo |
| `cassandro` | Torre das Conexões — Cassandro, o Mensageiro |
| `abismo-devnull` | O Abismo — O Lorde do Vazio (fim) |

> As 7 cinemáticas estão concluídas e publicadas em `public/video/cinematics/`. O mapa
> mostra o selo ▶ em cada região automaticamente (ver tabela de slugs acima).

```bash
python3 tools/cinematicas/gerar_cinematic_intro.py willen --prompts-sora
python3 tools/cinematicas/gerar_cinematic_intro.py clayton
```

## Preset legado: Terras de Hello World

```bash
python3 tools/cinematicas/gerar_cinematic_intro.py terras-hello-world
```

## Dependências (auto-instaladas no venv)

- `ffmpeg`, `ffprobe` (sistema)
- `espeak-ng` — `brew install espeak-ng` (macOS)
- Venv em `tools/.venvs/cinematic/` (kokoro, soundfile, numpy, pillow)

## Estrutura de saída

```
docs/cinematics/<slug>/
├── keyframes/          # PNGs fonte (gerados pelo agente)
├── frames/             # PNGs normalizados 1920×1080
├── narracao-raw.wav
├── narracao.mp3
├── <slug>.mp4
└── manifest.json
```

## Referência

- Presets: [tools/cinematicas/cinematic_intro/presets/](../../../tools/cinematicas/cinematic_intro/presets/)
- Marca visual: [tools/cinematicas/cinematic_intro/marca_visual.py](../../../tools/cinematicas/cinematic_intro/marca_visual.py)
