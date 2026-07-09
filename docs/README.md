# 📚 docs/ — Documentação do Algorithmia

Índice das pastas de documentação. Cada subpasta agrupa um tema; nada fica solto
na raiz de `docs/` (exceto o PDF gerado).

| Pasta | Conteúdo |
|-------|----------|
| [`arte/`](arte/) | Pipeline e briefings de arte: design system (`PROMPT-EVOLUCAO-VISUAL.md`), arquitetura de imagens (`ARQUITETURA-IMAGENS.md`), prompts e briefings (`PROMPT-MESTRE-SVG-JOGO-COMPLETO.md`, `briefing-arte-v2/v3.md`) |
| [`canon/`](canon/) | Cânone do jogo: roteiro narrativo (`ROTEIRO-NARRATIVO.md`) e regras/balanceamento (`REGRAS-DO-JOGO.md`) |
| [`migracao/`](migracao/) | O port para Laravel 13 + PostgreSQL: plano (`PLANO.md`), inventário do domínio legado (`INVENTARIO.md`) e o roteiro original arquivado (`roteiro-v1.html`) |
| [`operacao/`](operacao/) | Runbook de produção do port (`RUNBOOK.md`): deploy, rollback, backup, coexistência e corte |
| [`processo/`](processo/) | Operação e processo: deploy (`DEPLOY.md`), fluxo spec-driven (`FLUXO-OPENSPEC.md`), handoffs de sessão (`HANDOFF-SESSAO.md`), status de UI (`STATUS-UI-ATUAL.md`) |
| [`desenvolvimento/`](desenvolvimento/) | Guia técnico de quem contribui: arquitetura MVC, padrões, convenções, assets, QA, cinemáticas, releases |
| [`codex/`](codex/) | Cânone consolidado: jornada das 35 fases, mestres e classes, bestiário |
| [`cinematics/`](cinematics/) | Galeria das 7 intros em vídeo (fonte: keyframes + narração) |
| [`evolucao-visual/`](evolucao-visual/) | Histórico **versionado** da arte (v1 pixel-art, v2 ilustrações) |
| [`auditoria/`](auditoria/) | Auditoria técnica multi-agente (backend, segurança, performance, game design). ⚠️ **Retrato de 2026-06-18; os débitos críticos já foram corrigidos** |
| [`auditoria-ux/`](auditoria-ux/) | Auditoria de UX (navegação, motion, acessibilidade, retenção) |
| [`galeria/`](galeria/) | Imagens de galeria usadas no README |
| [`materias/`](materias/) | Material de referência das matérias ensinadas |
| [`produto/`](produto/) | Visão de produto |
| [`prototipo/`](prototipo/) | Protótipos |

> 📄 `Fases-e-Topicos-por-Mestre.pdf` na raiz de `docs/` é um **deliverable gerado**
> por `tools/pdf/gerar_pdf_fases.py` (por isso fica fora das subpastas).
