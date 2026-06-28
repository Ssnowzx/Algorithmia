# 📲 Operador remoto — Telegram → Claude Code

Ponte que deixa você **operar o Claude Code de qualquer lugar pelo celular**.
Você manda uma mensagem pro bot do Telegram, ela vira um run do `claude -p`
dentro deste projeto, e a resposta volta pro Telegram.

- **Zero dependências** — Node 20+ puro (usa `fetch` nativo). Não tem `npm install`.
- **Sem webhook / sem servidor exposto** — usa *long polling*. Roda no seu Mac,
  só precisa de internet. Nada de ngrok, VPS ou porta aberta.
- **Seguro por padrão** — só obedece os chat IDs da sua *whitelist*. E as
  deny-rules globais (`rm -rf`, `sudo`, …) continuam bloqueando até nos runs headless.

> **Por que Telegram e não WhatsApp?** O Telegram tem Bot API oficial, grátis e
> trivial — sem aprovação da Meta, sem risco de banir número. Se um dia quiser
> WhatsApp, a mesma arquitetura troca só a camada de mensageria (Twilio ou Cloud API).

---

## Setup (≈ 3 minutos)

### 1. Criar o bot no Telegram
1. No Telegram, abra **[@BotFather](https://t.me/BotFather)**.
2. Mande `/newbot`, dê um nome e um @username pro bot.
3. Ele te devolve um **token** tipo `123456789:AAH....`. Guarde.

### 2. Configurar
```bash
cd worker/telegram-bridge
cp .env.example .env
# edite o .env e cole o token em TELEGRAM_BOT_TOKEN (deixe a whitelist vazia por ora)
```

### 3. Descobrir seu chat ID
```bash
./start.sh          # ou: node bridge.mjs
```
No Telegram, mande **qualquer mensagem** pro seu bot. Ele responde com o seu
`chat id`. Copie esse número.

### 4. Travar na sua conta (importante!)
Pare o bridge (`Ctrl+C`), coloque o id no `.env`:
```
TELEGRAM_ALLOWED_CHAT_ID=123456789
```
Suba de novo:
```bash
./start.sh
```
Pronto. Agora só **você** comanda. Mande uma tarefa e veja o Claude trabalhar. ✅

---

## Como usar

Mande qualquer texto — ele vira o prompt do Claude Code no projeto. Ex.:
> *"Liste os arquivos em database/migrations e me diga o que cada um faz"*
> *"Corrija o typo no README da raiz e me mostre o diff"*

Comandos:
| Comando | O quê |
|---|---|
| `/help` | ajuda |
| `/status` | pasta, modo de permissão e sessão atual |
| `/reset` | começa uma conversa nova (esquece o contexto) |

A conversa é **contínua**: o bot mantém uma sessão dedicada do Claude (via
`--session-id` / `--resume`), então ele lembra do que vocês já falaram — até
você dar `/reset`. Essa sessão é separada das suas sessões interativas no
terminal, então **não atropela** outro trabalho aberto.

---

## Níveis de autonomia (`CLAUDE_PERMISSION_MODE`)

| Modo | Comportamento |
|---|---|
| `acceptEdits` *(padrão)* | Edita arquivos sozinho. Comandos de shell fora do allowlist são **negados** (o Claude segue e te avisa). Equilíbrio seguro. |
| `bypassPermissions` | **Autonomia total** — roda qualquer comando. As deny-rules globais (`rm -rf`, `sudo`…) **ainda bloqueiam**. Use só se quiser que ele execute tudo sem freio. |
| `default` | Pede permissão interativa — **não use headless**, trava esperando resposta. |

Troque no `.env` e reinicie.

---

## Segurança — leia

- **O token do bot é a chave do reino.** Quem tiver o token + souber mandar com o
  seu chat id consegue rodar comando na sua máquina. O `.env` está no `.gitignore`
  — **nunca** commite. Se vazar, gere outro no @BotFather (`/revoke`).
- A **whitelist de chat id** é a trava principal. Sem ela, o bot fica em "modo
  descoberta" e **não executa nada** — só revela o id.
- Cada mensagem gasta **cota/tokens** da sua conta Claude Code, igual a uma
  sessão normal.

---

## Deixar sempre ligado (opcional)

Rodando no terminal, fecha quando você fecha o terminal. Para rodar em background:

```bash
# simples: nohup
nohup ./start.sh > bridge.log 2>&1 &

# ou, “de verdade”, com launchd (auto-start no boot) — veja exemplo no fim deste README.
```

<details>
<summary>Exemplo de plist launchd (macOS)</summary>

Salve em `~/Library/LaunchAgents/com.algorithmia.telegram-bridge.plist`,
ajuste os caminhos, e rode `launchctl load ~/Library/LaunchAgents/com.algorithmia.telegram-bridge.plist`:

```xml
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE plist PUBLIC "-//Apple//DTD PLIST 1.0//EN" "http://www.apple.com/DTDs/PropertyList-1.0.dtd">
<plist version="1.0">
<dict>
  <key>Label</key><string>com.algorithmia.telegram-bridge</string>
  <key>ProgramArguments</key>
  <array>
    <string>/opt/homebrew/bin/node</string>
    <string>/Users/snows/AntiGravity/TrabalhoWillen2/worker/telegram-bridge/bridge.mjs</string>
  </array>
  <key>WorkingDirectory</key>
  <string>/Users/snows/AntiGravity/TrabalhoWillen2/worker/telegram-bridge</string>
  <key>RunAtLoad</key><true/>
  <key>KeepAlive</key><true/>
  <key>StandardOutPath</key><string>/tmp/telegram-bridge.log</string>
  <key>StandardErrorPath</key><string>/tmp/telegram-bridge.err.log</string>
</dict>
</plist>
```
</details>

---

## Como funciona (1 parágrafo)

`bridge.mjs` faz long polling no `getUpdates` da Telegram Bot API. Mensagem nova
de um chat autorizado entra numa fila (uma por vez, pra dois runs não mexerem no
repo juntos), o worker chama `claude -p "<sua msg>" --output-format json
--permission-mode <modo> --resume <sessão>` com `cwd` no projeto, lê o campo
`result` do JSON e devolve via `sendMessage` (quebrado em pedaços de 4000 chars).
Estado (offset do polling + id da sessão) fica em `.state.json`.
