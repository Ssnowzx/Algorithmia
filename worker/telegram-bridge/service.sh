#!/usr/bin/env bash
#
# Gerenciador do bridge como servico do macOS (launchd / LaunchAgent).
# Auto-start no boot, reinicio automatico, sem sudo. Blindado contra busy-loop.
#
# Uso:
#   ./service.sh install     # gera o plist e liga o servico (auto-start)
#   ./service.sh uninstall   # para e remove o servico
#   ./service.sh start        # liga
#   ./service.sh stop         # desliga
#   ./service.sh restart      # reinicia
#   ./service.sh status       # mostra se esta rodando + CPU acumulada
#   ./service.sh logs         # acompanha os logs (Ctrl+C pra sair)
#
set -euo pipefail

LABEL="com.algorithmia.telegram-bridge"
DIR="$(cd "$(dirname "$0")" && pwd)"
PLIST="$HOME/Library/LaunchAgents/${LABEL}.plist"
UID_NUM="$(id -u)"
DOMAIN="gui/${UID_NUM}"
OUT_LOG="${DIR}/bridge.out.log"
ERR_LOG="${DIR}/bridge.err.log"

# Resolve o node REAL (estavel), nao o symlink efemero do fnm.
resolve_node() {
  if ! command -v node >/dev/null 2>&1; then
    echo "ERRO: node nao encontrado no PATH." >&2
    exit 1
  fi
  node -e "console.log(process.execPath)"
}

gen_plist() {
  local node_bin node_dir claude_bin
  node_bin="$(resolve_node)"
  node_dir="$(dirname "$node_bin")"
  claude_bin="$(command -v claude || echo /opt/homebrew/bin/claude)"

  mkdir -p "$HOME/Library/LaunchAgents"
  cat > "$PLIST" <<PLIST_EOF
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE plist PUBLIC "-//Apple//DTD PLIST 1.0//EN" "http://www.apple.com/DTDs/PropertyList-1.0.dtd">
<plist version="1.0">
<dict>
  <key>Label</key><string>${LABEL}</string>
  <key>ProgramArguments</key>
  <array>
    <string>${node_bin}</string>
    <string>${DIR}/bridge.mjs</string>
  </array>
  <key>WorkingDirectory</key><string>${DIR}</string>
  <key>EnvironmentVariables</key>
  <dict>
    <key>PATH</key>
    <string>/opt/homebrew/bin:${node_dir}:/usr/local/bin:/usr/bin:/bin:/usr/sbin:/sbin</string>
    <key>CLAUDE_BIN</key><string>${claude_bin}</string>
  </dict>
  <key>RunAtLoad</key><true/>
  <key>KeepAlive</key><true/>
  <!-- Trava anti-busy-loop: no maximo 1 (re)inicio a cada 30s. -->
  <key>ThrottleInterval</key><integer>30</integer>
  <!-- Prioridade baixa: economiza bateria. -->
  <key>ProcessType</key><string>Background</string>
  <key>StandardOutPath</key><string>${OUT_LOG}</string>
  <key>StandardErrorPath</key><string>${ERR_LOG}</string>
</dict>
</plist>
PLIST_EOF
  echo "plist gerado em: ${PLIST}"
  echo "  node:   ${node_bin}"
  echo "  claude: ${claude_bin}"
}

load_service() {
  launchctl bootout "${DOMAIN}" "$PLIST" 2>/dev/null || launchctl unload "$PLIST" 2>/dev/null || true
  launchctl bootstrap "${DOMAIN}" "$PLIST" 2>/dev/null || launchctl load -w "$PLIST"
}

unload_service() {
  launchctl bootout "${DOMAIN}" "$PLIST" 2>/dev/null || launchctl unload "$PLIST" 2>/dev/null || true
}

case "${1:-}" in
  install)
    if [ ! -f "${DIR}/.env" ]; then
      echo "ERRO: falta o .env (cp .env.example .env e preencha o token)." >&2
      exit 1
    fi
    gen_plist
    load_service
    echo "✅ servico instalado e ligado (auto-start no boot)."
    ;;
  uninstall)
    unload_service
    rm -f "$PLIST"
    echo "🧹 servico removido."
    ;;
  start)   load_service; echo "▶️  ligado." ;;
  stop)    unload_service; echo "⏹  desligado." ;;
  restart) launchctl kickstart -k "${DOMAIN}/${LABEL}" 2>/dev/null || { unload_service; load_service; }; echo "🔁 reiniciado." ;;
  status)
    if launchctl print "${DOMAIN}/${LABEL}" >/dev/null 2>&1; then
      echo "✅ servico carregado no launchd:"
      launchctl print "${DOMAIN}/${LABEL}" 2>/dev/null \
        | grep -iE 'state =|pid =|runs =|last exit' | head
      echo ""
      ps -Ao pid,pcpu,etime,time,command | grep 'bridge\.mjs' | grep -v grep \
        || echo "(processo node ainda subindo ou parado)"
    else
      echo "Servico NAO esta carregado. Use: ./service.sh install"
    fi
    ;;
  logs)
    echo "== ${ERR_LOG} / ${OUT_LOG} (Ctrl+C pra sair) =="
    touch "$OUT_LOG" "$ERR_LOG"
    tail -n 30 -f "$OUT_LOG" "$ERR_LOG"
    ;;
  *)
    echo "uso: ./service.sh {install|uninstall|start|stop|restart|status|logs}"
    exit 1
    ;;
esac
