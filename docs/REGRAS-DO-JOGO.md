# 📜 Regras do Jogo — Algorithmia: A Lenda dos Cinco Mestres

Documento de referência das regras e do balanceamento. Os valores numéricos
ficam centralizados em `config/config.php` (constantes) e a lógica em
`app/services/` (regras de negócio).

---

## 1. Objetivo
Percorrer o mapa de fases, derrotar os bugs de cada região e os Cinco Mestres,
até o confronto final contra **Lorde Segfault** e a **IA Ancestral**. A escolha
final + a reputação acumulada determinam um dos três desfechos.

## 2. Classes jogáveis
Definidas em `config/config.php` (`CLASSES`). Cada uma altera os atributos-base:

| Classe | HP | MP | Ataque | Defesa | Estilo |
|---|---|---|---|---|---|
| 🧙 Mago do Backend | 80 | 60 | 12 | 4 | Muita mana, pouca vida |
| 🛡️ Guerreiro do Frontend | 130 | 25 | 10 | 9 | Muita vida e defesa |
| 🏹 Ranger Fullstack | 100 | 40 | 11 | 6 | Equilibrado, +faro p/ ouro |

## 3. Progressão de fases
- Cada fase tem um `requisito_fase_id`. **A fase só libera quando a fase-requisito
  é concluída** (`ProgressaoService::faseLiberada`).
- Fase sem requisito (`NULL`) já nasce liberada (ex.: o Prólogo).
- O mapa é dividido em regiões: **Início (Hello World) → Porto da Sintaxe →
  Cidadela dos Objetos → Floresta das Estruturas → Montanha do Cálculo →
  Torre das Conexões → Fim da Jornada**.

## 4. Batalha (a regra principal)
A cada desafio respondido, é um turno (`BatalhaService::responder`):

- ✅ **Acerto** → causa dano no inimigo. Acertos seguidos formam **combo**
  (`+25%` de dano por acerto, teto de **4x** — `COMBO_BONUS`, `COMBO_MAX`).
- ❌ **Erro** → zera o combo e o herói **leva dano** do inimigo.

**Condições de término (nesta ordem):**
1. **HP do inimigo = 0 → 🏆 VITÓRIA.**
2. **HP do herói = 0 → 💀 DERROTA.**
3. **Acabaram os desafios e o inimigo ainda tem vida → 💀 DERROTA.**

> ⚠️ **Regra de ouro:** só se vence **derrotando o inimigo**. Sobreviver sem
> derrubá-lo (ou errar tudo) é **derrota** — não basta "chegar ao fim".

**Consequências:**
- **Vitória** → concede XP, ouro, possível item, estrelas e **registra o
  progresso** (libera a próxima fase). `BatalhaController::concederRecompensas`.
- **Derrota** → **não** registra progresso, **não** libera a próxima fase,
  **não** dá recompensa. O jogador pode **"↻ Tentar de novo"**.

**Ações de combate:**
- **✦ Especial** — gasta `15` de mana (`CUSTO_MP_ESPECIAL`) e **dobra** o dano
  do próximo acerto (`MULTIPLICADOR_ESPECIAL = 2.0`).
- **🧪 Poção** — consome um item do inventário para recuperar HP/MP.
- **🏃 Fugir** — abandona a batalha sem concluir a fase (sem progresso).

## 5. Estrelas (1 a 3)
Calculadas em `ProgressaoService::calcularEstrelas` com base em **erros** e
**uso da IA**. Menos erros e sem usar atalho da IA → mais estrelas. Refazer uma
fase **mantém o melhor desempenho**.

## 6. Reputação e o Fragmento da IA
- O eixo de **Reputação** vai de `-100` (IA) a `+100` (Disciplina), começando em `0`.
- Usar o **Fragmento da IA Ancestral** em batalha acerta o desafio
  automaticamente, **mas** custa `-10` de reputação (`REPUTACAO_USO_IA`) e deixa
  uma marca permanente na fase. É o "atalho que corrói a alma".

## 7. Os três desfechos
Determinados pela escolha final + reputação (`ReputacaoService::finalDeterminado`):

| Final | Como chegar | Resumo |
|---|---|---|
| 👑 **O Sexto Mestre** | Destruir a IA com reputação alta (`≥ 40`) | O esforço vence o atalho |
| 🤖 **A Singularidade** | Fundir-se à IA (ou reputação `≤ -40`) | Poder absoluto, alma vazia |
| ✨ **O Copiloto** | Reescrever a IA / equilíbrio | A IA vira ferramenta, não muleta |

---

## 8. Onde cada regra vive (mapa para o código)
| Regra | Arquivo |
|---|---|
| Constantes de balanceamento, classes, XP | `config/config.php` |
| Liberação de fases, estrelas, capítulos | `app/services/ProgressaoService.php` |
| Turnos, vitória/derrota, dano, combo, especial | `app/services/BatalhaService.php` |
| Reputação, Fragmento, final | `app/services/ReputacaoService.php` |
| Recompensas e registro de progresso | `app/controllers/BatalhaController.php` |

## 9. Regras de desenvolvimento (OBRIGATÓRIO)
- **Toda mudança relevante segue o fluxo OpenSpec** (propor → revisar spec →
  implementar → arquivar). Não fazer grandes alterações direto no código sem uma
  proposta em `openspec/changes/`.
- **Stack fixa:** PHP puro + **MVC** + MySQL. Sem framework, sem dependências de
  runtime. Regra/negócio em `app/services`, dados em `app/models`, fluxo em
  `app/controllers`, apresentação em `app/views`/`public`.
- **Padrões:** PHPDoc nas funções públicas; nomes de domínio em português;
  `declare(strict_types=1)` no PHP.
- **Dois colaboradores, duas IAs:** o fluxo completo (Claude Code **e** Codex)
  está em **[`docs/FLUXO-OPENSPEC.md`](FLUXO-OPENSPEC.md)**.
