# 🧭 Fluxo de Desenvolvimento — OpenSpec (Claude + Codex)

Este projeto segue **desenvolvimento orientado a especificação (spec-driven)** com o
**OpenSpec**. A regra vale para os dois colaboradores e para as duas IDEs de IA:
**Claude Code** e **Codex**.

> **Regra de ouro:** toda mudança relevante (nova fase, regra, tela, refator) nasce
> como uma **proposta** no OpenSpec, é revisada como **spec**, só então
> **implementada** e, por fim, **arquivada**. Nada de grandes mudanças "direto no
> código" sem passar por uma proposta.

---

## 0. Instalar o OpenSpec
**Repositório oficial:** https://github.com/Fission-AI/OpenSpec/

O CLI é pré-requisito para os dois colaboradores (as skills do Codex e os comandos
do Claude dependem dele).

```bash
# Opção A — npm (global):
npm install -g @fission-ai/openspec

# Opção B — Homebrew (macOS):
brew install openspec

# Verificar a instalação:
openspec --version
```

> Consulte sempre o README do repositório para a instalação mais atual:
> https://github.com/Fission-AI/OpenSpec/

---

## 1. Por que OpenSpec
- **Consistência** entre dois colaboradores usando IAs diferentes (Claude e Codex).
- As decisões ficam registradas em texto (specs), não só no código.
- Funciona como "contrato": antes de codar, combina-se *o quê* e *como*.

## 2. Estrutura (não apagar)
```
openspec/
├── config.yaml        # contexto do projeto + regras por artefato
├── specs/             # especificações vigentes (o que o sistema FAZ hoje)
└── changes/           # propostas de mudança em andamento
    └── archive/       # mudanças já concluídas e arquivadas
```
Tanto `.claude/` (comandos do Claude) quanto `.codex/` (skills do Codex) ficam
versionados — ao clonar o repositório, cada um já tem as ferramentas da sua IDE.

## 3. O ciclo (igual para os dois)
1. **Propor** — descreve a ideia; gera `proposal.md` (o quê/porquê),
   `design.md` (como) e `tasks.md` (passos).
2. **Revisar** — leem-se os artefatos da proposta e ajusta-se antes de codar.
3. **Aplicar** — implementa seguindo as `tasks.md`.
4. **Arquivar** — conclui a mudança e atualiza as specs vigentes.

---

## 4. Para o usuário do **Claude Code** (comandos `/opsx`)
| Etapa | Comando |
|---|---|
| Propor mudança | `/opsx:propose "adicionar fase de Recursão na Montanha"` |
| Explorar specs | `/opsx:explore` |
| Implementar a proposta | `/opsx:apply` |
| Arquivar concluída | `/opsx:archive` |
| Sincronizar specs | `/opsx:sync` |

> Os comandos vivem em `.claude/commands/opsx/`. Se não aparecerem, reinicie o Claude Code.

## 5. Para o colaborador do **Codex** (skills do OpenSpec)
O Codex já vem com as skills do OpenSpec em `.codex/skills/`. Elas exigem o
**CLI do OpenSpec instalado** (veja a seção 0 — repositório:
https://github.com/Fission-AI/OpenSpec/).

No Codex, basta pedir a ação que ele usa a skill correspondente, por exemplo:
- *"Use a skill **openspec-propose** para propor: adicionar fase de Recursão."*
- *"Use a skill **openspec-apply-change** para implementar a proposta atual."*
- *"Use a skill **openspec-archive-change** para arquivar a mudança concluída."*
- Skills disponíveis: `openspec-propose`, `openspec-explore`,
  `openspec-apply-change`, `openspec-archive-change`, `openspec-sync-specs`.

## 6. Comandos do CLI (funcionam para QUALQUER um, sem IA)
Úteis para conferir o estado, independente de Claude ou Codex:
```bash
openspec list            # mudanças (propostas) em andamento
openspec list --specs    # especificações vigentes
openspec view            # painel interativo de specs e mudanças
openspec validate        # valida specs e propostas
openspec archive <nome>  # arquiva uma mudança concluída
```

## 7. Regras de convivência (2 pessoas, 2 IAs)
- **Um de cada vez por proposta:** cada mudança tem sua pasta em
  `openspec/changes/<nome>` — evita pisar no trabalho do outro.
- **Commitem o `openspec/`** (specs e propostas) junto com o código.
- Antes de começar algo grande, rode `openspec list` para ver se já não há
  uma proposta aberta sobre o mesmo assunto.
- Padrões de código do projeto continuam valendo: **PHP puro + MVC + MySQL**,
  PHPDoc nas funções, nomes em português no domínio. Veja `docs/REGRAS-DO-JOGO.md`.
