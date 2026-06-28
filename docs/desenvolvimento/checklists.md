# Checklists — Algorithmia

[← Inicio](README.md)

Checklists acionaveis. Marque com [x] conforme avanca.

---

## Nova feature

```
Planejamento
[ ] Entender o que muda (schema? assets? logica?)
[ ] Verificar se ha debito tecnico relacionado na auditoria (docs/auditoria/)

Branch
[ ] git checkout main && git pull origin main
[ ] git checkout -b feature/nome-da-feature

Desenvolvimento
[ ] Toda saida HTML passa por e()
[ ] SQL apenas em Models e Services
[ ] Formularios POST com csrf_field() e exigirCsrf()
[ ] Operacoes de escrita multipla em beginTransaction
[ ] Sem console.log em public/js/
[ ] Sem var_dump / print_r

Schema (se necessario)
[ ] Migration criada em database/migrations/AAAAMMDD-descricao.sql
[ ] Migration e idempotente (IF NOT EXISTS / ALTER IF COLUMN NOT EXISTS)
[ ] schema.sql atualizado para refletir o estado final
[ ] php database/migrate.php rodou sem erro localmente

Assets (se necessario)
[ ] Slug segue convencao (prefixo-descricao, lowercase-com-hifen)
[ ] WebP gerado alem do PNG
[ ] srcImagem() resolve o slug corretamente (nao retorna null)

Commit
[ ] Formato: tipo: descricao no imperativo
[ ] 1 commit = 1 razao logica

Teste manual
[ ] Fluxo principal da feature funciona
[ ] Casos de erro exibem mensagem correta
[ ] Sem regressoes nas areas adjacentes

PR
[ ] Descricao do PR explica o que muda e como testar
```

---

## Correcao de bug

```
[ ] Reproducao confirmada localmente
[ ] Causa identificada (arquivo, linha)
[ ] Branch: git checkout -b fix/descricao-do-bug
[ ] Correcao aplicada no local certo (nao um workaround na view)
[ ] Sem efeitos colaterais na area corrigida
[ ] Teste manual confirma correcao
[ ] Commit: fix: descricao do bug corrigido
[ ] PR referenciando o problema (fix: corrige X (#N))
```

---

## Revisao de codigo (code review)

```
Seguranca
[ ] Toda saida de dados de usuario passa por e()
[ ] Nenhum dado via $_GET/$_POST interpola em SQL direto
[ ] Formularios POST tem CSRF
[ ] Endpoints de escrita sao POST (nao GET)
[ ] Nenhuma credencial ou .env no diff

Arquitetura
[ ] SQL esta em Model ou Service (nao no controller, nao na view)
[ ] Logica de negocio esta em Service (nao no controller)
[ ] Nenhum ID de fase/tipo hardcoded introduzido

Qualidade
[ ] Sem console.log / var_dump / print_r
[ ] Sem codigo comentado (deletar, nao comentar)
[ ] Nomes seguem convencoes do projeto
[ ] Funcoes com mais de 50 linhas identificadas para refator

Banco
[ ] Migrations novas sao idempotentes
[ ] schema.sql atualizado se migration foi adicionada
[ ] Nenhum UPDATE sem WHERE
```

---

## Publicacao de versao

```
[ ] PR aprovado e mergeado em main
[ ] Testes manuais das areas alteradas (ver qa-e-testes.md)
[ ] git tag vX.Y.Z -m "descricao"
[ ] Deploy: git pull + php database/migrate.php + apache reload
[ ] Verificacao em producao no navegador
[ ] Se mudanca visual relevante: snapshot em docs/evolucao-visual/
[ ] HANDOFF-SESSAO.md atualizado
```

---

## Criacao de conteudo (fases/questoes)

```
[ ] Fase ID definido e consistente com a sequencia de ordem_global
[ ] Perguntas adicionadas no arquivo .php da materia correta (database/banco-questoes/)
[ ] Campos obrigatorios preenchidos: fase, tipo, assunto, pergunta, resposta, explicacao, dif
[ ] Gabarito correto verificado (tipo multipla: indice base 0)
[ ] Explicacao tecnicamente correta
[ ] Tom da pergunta alinhado ao sabor do projeto (acido, nao ofensivo)
[ ] php database/seed-banco-questoes.php rodou sem erro
[ ] Questao aparece no combate (verificar contagem no relatorio do migrate.php)
[ ] Se nova fase: icone em public/img/mapas/ e mapeamento em iconeFaseMapa()
[ ] Se novo inimigo: entrada em config/bestiario.php com conceito correto
```

---

## Pipeline de assets

```
[ ] Briefing consultado (docs/briefing-arte-v2.md ou v3.md)
[ ] Slug planejado segue convencao (prefixo-descricao, lowercase-com-hifen)
[ ] Para sprites (inimigos/herois/icones): recorte com tools/fundo/recortar_rembg.py
[ ] WebP gerado (cwebp -q 90 entrada.png -o saida.webp)
[ ] PNG original mantido em public/img/
[ ] srcImagem('slug/caminho') retorna URL (nao null) — testar no browser
[ ] Sem texto/letras na imagem (exceto glifos runicos decorativos)
[ ] Paleta dentro do padrao do projeto
[ ] Se marco visual: snapshot em docs/evolucao-visual/ + atualizar README.md da pasta
```

---

## Auditoria futura (referencia)

Itens do debito tecnico a validar em auditorias periodicas:

```
Seguranca
[ ] CSRF em endpoints GET de escrita (loja/comprar, inventario/descartar)
[ ] CSRF em endpoints AJAX de batalha (responder/fragmento/pocao)
[ ] Rate limit no login (sem limite de tentativas atualmente)
[ ] CSP (Content-Security-Policy ausente em index.php)
[ ] XSS potencial em batalha.js (rec.redirect_final em href sem escape)

Integridade
[ ] beginTransaction em concederRecompensas (BatalhaController)
[ ] UPDATE atomico no ouro da loja (TOCTOU)
[ ] INSERT ON DUPLICATE KEY para Inventario::adicionar
[ ] UNIQUE(personagem_id, codigo) em tabela escolhas

Balanceamento
[ ] HP/def do Elfo vs chefes (F27/F33/F35)
[ ] Especial Guerreiro vs Mago (1x vs 4x de dano)
[ ] 10 conquistas sem trigger (mestre_*, puro_de_coracao, finais narrativos)
[ ] Reputacao invisivel ao jogador (decide o final)

Conteudo
[ ] SQL subrepresentado (apenas 7 questoes; faltam JOIN/subconsulta/DDL)

Performance
[ ] 48+ PNGs sem WebP (principalmente itens pendentes de redesign)
[ ] CSS por rota (batalha.css/cena.css carregados em todas as paginas)
[ ] Indice composto (personagem_id, desafio_id) em respostas_log
[ ] Google Fonts carregado 2x (link + @import)

Frontend
[ ] color-mix() sem fallback (47 ocorrencias, quebra Safari <=15)
[ ] Labels sem for/id (acessibilidade)
[ ] Dois sistemas de card (.item-card vs .carta-loja) sem tokens compartilhados

Arquitetura
[ ] IDs de fase hardcoded em HistoriaController e ConquistaService
[ ] Desafio::daFase() morta (duplicata de poolDaFase)
[ ] Model::update sem guard para $data vazio
[ ] VALUES() depreciado no MySQL 8.0.20+ em ProgressoFase::registrar
```
