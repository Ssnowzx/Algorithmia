#!/usr/bin/env node
/**
 * Telegram <-> Claude Code bridge (zero dependencias).
 *
 * Voce manda uma mensagem pro bot no Telegram -> esta ponte recebe via
 * long polling -> dispara o Claude Code em modo headless (`claude -p`) dentro
 * do projeto ATIVO -> devolve a resposta no Telegram.
 *
 * Multi-projeto: descobre as pastas sob PROJECTS_ROOTS (uma ou mais raizes,
 * ex.: ~/AntiGravity,~/Cursor). Troque pelo chat com /projetos, /projeto <nome>
 * ou /cd <caminho>. Cada projeto tem a sua propria sessao (nao se misturam).
 *
 * Seguranca: so obedece os chat IDs da whitelist (TELEGRAM_ALLOWED_CHAT_ID).
 * As deny-rules globais (rm -rf, sudo, ...) continuam valendo nos runs headless.
 *
 * Requisitos: Node 18+ (usa fetch nativo) e o CLI `claude` no PATH/CLAUDE_BIN.
 */

import { spawn } from 'node:child_process'
import { readFileSync, writeFileSync, existsSync, readdirSync } from 'node:fs'
import { randomUUID } from 'node:crypto'
import { dirname, join, resolve } from 'node:path'
import { fileURLToPath } from 'node:url'

const HERE = dirname(fileURLToPath(import.meta.url))

// ---------------------------------------------------------------------------
// Config (.env opcional + variaveis de ambiente)
// ---------------------------------------------------------------------------

/** Le um .env simples (KEY=VALUE por linha) sem dependencias externas. */
function loadEnv(path) {
  if (!existsSync(path)) return
  const text = readFileSync(path, 'utf8')
  for (const rawLine of text.split('\n')) {
    const line = rawLine.trim()
    if (!line || line.startsWith('#')) continue
    const eq = line.indexOf('=')
    if (eq === -1) continue
    const key = line.slice(0, eq).trim()
    let value = line.slice(eq + 1).trim()
    if (
      (value.startsWith('"') && value.endsWith('"')) ||
      (value.startsWith("'") && value.endsWith("'"))
    ) {
      value = value.slice(1, -1)
    }
    if (!(key in process.env)) process.env[key] = value
  }
}

loadEnv(join(HERE, '.env'))

const HOME = process.env.HOME || ''
const TOKEN = process.env.TELEGRAM_BOT_TOKEN || ''
const ALLOWED = (process.env.TELEGRAM_ALLOWED_CHAT_ID || '')
  .split(',')
  .map((s) => s.trim())
  .filter(Boolean)

// Projeto inicial.
const DEFAULT_PROJECT = resolve(
  process.env.CLAUDE_PROJECT_DIR || join(HERE, '..', '..'),
)
// Uma ou mais raizes onde os projetos sao descobertos (separadas por virgula).
const PROJECT_ROOTS = [
  ...new Set(
    (process.env.PROJECTS_ROOTS || process.env.PROJECTS_ROOT || dirname(DEFAULT_PROJECT))
      .split(',')
      .map((s) => s.trim())
      .filter(Boolean)
      .map((p) => resolve(p.replace(/^~/, HOME))),
  ),
]

const CLAUDE_BIN = process.env.CLAUDE_BIN || 'claude'
// acceptEdits = edita arquivos sozinho mas nega comandos perigosos nao-permitidos.
// Para autonomia total (rodar qualquer bash), use bypassPermissions (veja README).
const PERMISSION_MODE = process.env.CLAUDE_PERMISSION_MODE || 'acceptEdits'
const CLAUDE_MODEL = process.env.CLAUDE_MODEL || '' // vazio = default do CLI
const RUN_TIMEOUT_MS = Number(process.env.CLAUDE_TIMEOUT_MS || 600_000) // 10 min
const STATE_PATH = join(HERE, '.state.json')

if (!TOKEN) {
  console.error('[bridge] Faltou TELEGRAM_BOT_TOKEN. Veja .env.example.')
  process.exit(1)
}

const API = `https://api.telegram.org/bot${TOKEN}`

// ---------------------------------------------------------------------------
// Estado persistido (offset do polling + projeto ativo + sessoes por projeto)
// ---------------------------------------------------------------------------

function loadState() {
  let s = {}
  if (existsSync(STATE_PATH)) {
    try {
      s = JSON.parse(readFileSync(STATE_PATH, 'utf8'))
    } catch {
      /* arquivo corrompido -> recomeca */
    }
  }
  if (typeof s.offset !== 'number') s.offset = 0
  if (!s.projectDir) s.projectDir = DEFAULT_PROJECT
  // Migracao do formato antigo (sessionId/sessionStarted global -> por projeto).
  if (!s.sessions) {
    s.sessions = {}
    if (s.sessionId) {
      s.sessions[s.projectDir] = { id: s.sessionId, started: !!s.sessionStarted }
    }
  }
  delete s.sessionId
  delete s.sessionStarted
  return s
}

let state = loadState()
function saveState() {
  writeFileSync(STATE_PATH, JSON.stringify(state, null, 2))
}

/** Sessao de contexto dedicada de um projeto (cria se nao existir). */
function sessionFor(dir) {
  if (!state.sessions[dir]) state.sessions[dir] = { id: randomUUID(), started: false }
  return state.sessions[dir]
}

// ---------------------------------------------------------------------------
// Projetos
// ---------------------------------------------------------------------------

/** Subpastas de todas as raizes (ignora ocultas) -> [{name, root, path}]. */
function listProjects() {
  const out = []
  for (const root of PROJECT_ROOTS) {
    let entries = []
    try {
      entries = readdirSync(root, { withFileTypes: true })
    } catch {
      continue
    }
    for (const d of entries) {
      if (d.isDirectory() && !d.name.startsWith('.')) {
        out.push({ name: d.name, root, path: join(root, d.name) })
      }
    }
  }
  return out.sort((a, b) => a.name.localeCompare(b.name))
}

/** Resolve um argumento (caminho absoluto, ~ ou nome do projeto) -> pasta. */
function resolveProject(arg) {
  const a = arg.trim()
  if (a.startsWith('/') || a.startsWith('~')) {
    const p = resolve(a.replace(/^~/, HOME))
    return existsSync(p) ? p : null
  }
  const dirs = listProjects()
  const exact = dirs.find((d) => d.name.toLowerCase() === a.toLowerCase())
  const partial = dirs.find((d) => d.name.toLowerCase().includes(a.toLowerCase()))
  const hit = exact || partial
  return hit ? hit.path : null
}

// ---------------------------------------------------------------------------
// Telegram helpers
// ---------------------------------------------------------------------------

async function tg(method, params) {
  const res = await fetch(`${API}/${method}`, {
    method: 'POST',
    headers: { 'content-type': 'application/json' },
    body: JSON.stringify(params),
  })
  return res.json()
}

const TG_LIMIT = 4000 // limite real e 4096; folga p/ seguranca

async function sendMessage(chatId, text) {
  const body = String(text ?? '').trim() || '(resposta vazia)'
  for (let i = 0; i < body.length; i += TG_LIMIT) {
    const chunk = body.slice(i, i + TG_LIMIT)
    await tg('sendMessage', { chat_id: chatId, text: chunk })
  }
}

const typing = (chatId) =>
  tg('sendChatAction', { chat_id: chatId, action: 'typing' }).catch(() => {})

// ---------------------------------------------------------------------------
// Claude Code headless
// ---------------------------------------------------------------------------

/** Roda `claude -p` no projeto ativo e devolve { ok, text }. */
function runClaude(prompt) {
  return new Promise((done) => {
    const dir = state.projectDir
    const sess = sessionFor(dir)
    const args = ['-p', prompt, '--output-format', 'json', '--permission-mode', PERMISSION_MODE]
    if (sess.started) args.push('--resume', sess.id)
    else args.push('--session-id', sess.id)
    if (CLAUDE_MODEL) args.push('--model', CLAUDE_MODEL)

    const child = spawn(CLAUDE_BIN, args, { cwd: dir, env: process.env })

    let stdout = ''
    let stderr = ''
    child.stdout.on('data', (d) => (stdout += d))
    child.stderr.on('data', (d) => (stderr += d))

    const killer = setTimeout(() => child.kill('SIGKILL'), RUN_TIMEOUT_MS)

    child.on('error', (err) => {
      clearTimeout(killer)
      done({ ok: false, text: `Erro ao iniciar o claude: ${err.message}` })
    })

    child.on('close', (code) => {
      clearTimeout(killer)
      // A primeira execucao "cria" a sessao; as proximas usam --resume.
      sess.started = true
      saveState()

      try {
        const json = JSON.parse(stdout)
        const out = json.result || json.error || stdout
        done({ ok: !json.is_error, text: out })
      } catch {
        const text = stdout.trim() || stderr.trim() || `claude saiu com codigo ${code}`
        done({ ok: code === 0, text })
      }
    })
  })
}

// ---------------------------------------------------------------------------
// Fila: processa uma mensagem por vez (evita 2 runs mexendo no repo juntos)
// ---------------------------------------------------------------------------

let busy = false
const queue = []

function enqueue(job) {
  queue.push(job)
  drain()
}

async function drain() {
  if (busy) return
  const job = queue.shift()
  if (!job) return
  busy = true
  try {
    await job()
  } catch (err) {
    console.error('[bridge] erro no job:', err)
  } finally {
    busy = false
    drain()
  }
}

// ---------------------------------------------------------------------------
// Comandos e roteamento de mensagens
// ---------------------------------------------------------------------------

const HELP = [
  'Algorithmia — Operador remoto via Claude Code',
  '',
  'Manda qualquer mensagem que eu repasso pro Claude Code no projeto ATIVO.',
  '',
  'Projetos:',
  '/projetos — lista os projetos e mostra o ativo',
  '/projeto <nome> — troca de projeto (ex.: /projeto Hermes)',
  '/cd <caminho> — aponta pra qualquer pasta (ex.: /cd /Users/snows/Cursor/Tars)',
  '',
  'Sessao:',
  '/status — pasta, modo e sessao atuais',
  '/reset — zera o contexto SO do projeto atual',
  '/help — esta ajuda',
].join('\n')

async function handleText(chatId, text) {
  const cmd = text.trim()
  const lower = cmd.toLowerCase()

  if (lower === '/start' || lower === '/help') {
    return sendMessage(chatId, HELP)
  }

  if (lower === '/projetos' || lower === '/projects') {
    const dirs = listProjects()
    const blocks = PROJECT_ROOTS.map((root) => {
      const items = dirs
        .filter((d) => d.root === root)
        .map((d) => (d.path === state.projectDir ? `• ${d.name}  ⬅️ ativo` : `• ${d.name}`))
      return `📁 ${root}\n${items.join('\n') || '(vazio)'}`
    })
    return sendMessage(
      chatId,
      [
        'Projetos disponiveis:',
        '',
        blocks.join('\n\n'),
        '',
        `Ativo agora: ${state.projectDir}`,
        'Trocar: /projeto <nome>  ou  /cd <caminho>',
      ].join('\n'),
    )
  }

  if (lower === '/projeto' || lower === '/cd') {
    return sendMessage(chatId, 'Uso: /projeto <nome>  ou  /cd <caminho>. Veja /projetos.')
  }

  if (lower.startsWith('/projeto ') || lower.startsWith('/cd ')) {
    const arg = cmd.slice(cmd.indexOf(' ') + 1).trim()
    const dir = resolveProject(arg)
    if (!dir) {
      return sendMessage(
        chatId,
        `❌ Nao achei "${arg}". Veja /projetos ou passe um caminho absoluto.`,
      )
    }
    state.projectDir = dir
    saveState()
    const sess = sessionFor(dir)
    return sendMessage(
      chatId,
      [
        `✅ Projeto ativo: ${dir}`,
        `Sessao: ${sess.id.slice(0, 8)} (${sess.started ? 'continuando' : 'nova'})`,
        'Manda a tarefa que eu rodo o Claude ai dentro.',
      ].join('\n'),
    )
  }

  if (lower === '/status') {
    const sess = sessionFor(state.projectDir)
    return sendMessage(
      chatId,
      [
        `Pasta: ${state.projectDir}`,
        `Permissao: ${PERMISSION_MODE}`,
        `Modelo: ${CLAUDE_MODEL || '(default)'}`,
        `Sessao: ${sess.id.slice(0, 8)} (${sess.started ? 'ativa' : 'nova'})`,
        `Na fila: ${queue.length}`,
      ].join('\n'),
    )
  }

  if (lower === '/reset') {
    const sess = sessionFor(state.projectDir)
    sess.id = randomUUID()
    sess.started = false
    saveState()
    return sendMessage(chatId, 'Conversa deste projeto zerada. Comecando do zero. 🧹')
  }

  // Mensagem normal -> Claude Code no projeto ativo
  enqueue(async () => {
    await typing(chatId)
    const heartbeat = setInterval(() => typing(chatId), 5000)
    const { ok, text: reply } = await runClaude(text)
    clearInterval(heartbeat)
    await sendMessage(chatId, (ok ? '' : '⚠️ ') + reply)
  })
}

function authorize(chatId) {
  // Sem whitelist configurada -> modo descoberta: revela o chat id e nao executa.
  if (ALLOWED.length === 0) {
    sendMessage(
      chatId,
      [
        '👋 Bot no ar, mas SEM whitelist configurada.',
        '',
        `Seu chat id e: ${chatId}`,
        '',
        'Coloque ele em TELEGRAM_ALLOWED_CHAT_ID no .env e reinicie o bridge.',
        'Enquanto isso, por seguranca, nao executo nada.',
      ].join('\n'),
    )
    return false
  }
  if (!ALLOWED.includes(String(chatId))) {
    console.warn(`[bridge] chat ${chatId} nao autorizado — ignorado.`)
    return false
  }
  return true
}

// ---------------------------------------------------------------------------
// Loop principal (long polling)
// ---------------------------------------------------------------------------

const sleep = (ms) => new Promise((r) => setTimeout(r, ms))

async function poll() {
  try {
    const res = await fetch(
      `${API}/getUpdates?timeout=30&offset=${state.offset}`,
      { signal: AbortSignal.timeout(40_000) },
    )
    const data = await res.json()
    if (!data.ok) {
      console.error('[bridge] getUpdates falhou:', data.description)
      await sleep(3000)
      return
    }
    for (const update of data.result) {
      state.offset = update.update_id + 1
      saveState()
      const msg = update.message || update.edited_message
      if (!msg || !msg.text) continue
      const chatId = msg.chat.id
      if (!authorize(chatId)) continue
      handleText(chatId, msg.text)
    }
  } catch (err) {
    if (err.name !== 'TimeoutError') console.error('[bridge] poll:', err.message)
    await sleep(2000)
  }
}

async function main() {
  console.log('[bridge] iniciando…')
  console.log(`[bridge] projeto ativo: ${state.projectDir}`)
  console.log(`[bridge] raizes de projetos: ${PROJECT_ROOTS.join(', ')}`)
  console.log(`[bridge] permissao: ${PERMISSION_MODE}`)
  console.log(
    `[bridge] whitelist: ${ALLOWED.length ? ALLOWED.join(', ') : '(vazia — modo descoberta)'}`,
  )
  const me = await tg('getMe', {})
  if (!me.ok) {
    console.error('[bridge] token invalido:', me.description)
    process.exit(1)
  }
  console.log(`[bridge] conectado como @${me.result.username}. Ouvindo…`)

  // eslint-disable-next-line no-constant-condition
  while (true) await poll()
}

main().catch((err) => {
  console.error('[bridge] fatal:', err)
  process.exit(1)
})
