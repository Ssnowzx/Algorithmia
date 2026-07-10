# Desenvolvimento — Algorithmia

Documentacao tecnica para quem contribui com o jogo. Cada doc e especifico ao
projeto: cita caminhos reais, patterns reais, decisoes ja tomadas.

## Indice

| Documento | O que cobre |
|---|---|
| [arquitetura.md](arquitetura.md) | Fluxo MVC artesanal, bootstrap, PDO, Auth, responsabilidades por pasta |
| [padroes-de-codigo.md](padroes-de-codigo.md) | PHP (prepared statements, `e()`, services), JS/CSS |
| [convencoes-nomenclatura.md](convencoes-nomenclatura.md) | Arquivos, classes, slugs de assets, tabelas, branches, commits |
| [fluxo-de-desenvolvimento.md](fluxo-de-desenvolvimento.md) | Branch -> codigo -> commit -> migrations -> push |
| [criacao-de-conteudo.md](criacao-de-conteudo.md) | Fases, desafios, questoes, bestiario/lore |
| [criacao-de-assets.md](criacao-de-assets.md) | Pipeline de arte: briefing -> IA -> rembg -> webp -> snapshot |
| [cinematicas.md](cinematicas.md) | Intros em video das fases: preset -> keyframes -> narracao -> FFmpeg; integracao no mapa |
| [qa-e-testes.md](qa-e-testes.md) | Checagens manuais hoje, proposta PHPUnit, o que validar |
| [releases-e-versionamento.md](releases-e-versionamento.md) | Versionamento, fluxo de release, checklist de publicacao |
| [checklists.md](checklists.md) | Checklists acionaveis para features, bugs, reviews, publicacao, conteudo |
| [timeline-evolucao.md](timeline-evolucao.md) | Linha do tempo visual e tecnica do jogo |

## Contexto rapido

- **Stack:** PHP 8 (sem framework), MySQL/PDO, JS/CSS vanilla, Python (PDFs, rembg).
- **Ponto de entrada:** `index.php` — unico front controller.
- **Banco de dados:** `php database/migrate.php` cria e atualiza; idempotente por design.
- **Assets:** `public/img/` (servidos); historico de arte em `docs/evolucao-visual/`.
- **Conta de desenvolvimento:** `masterboss@boss.com` (papel: mestre). A senha é
  sorteada e impressa quando a conta é criada. Para escolhê-la:
  `DEMO_SENHA=qwe123 php database/migrate.php`. Uma instalação nova não nasce mais com
  senha publicada neste repositório.
- **Auditoria tecnica completa:** `docs/auditoria/README.md`.
