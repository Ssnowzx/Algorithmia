# QA e Testes — Algorithmia

[← Inicio](README.md)

Hoje o projeto nao tem testes automatizados. Esta pagina documenta as checagens
manuais atuais e a proposta de cobertura com PHPUnit.

## Checagens manuais por area

### Autenticacao e sessao

- [ ] Cadastro com email duplicado exibe erro correto.
- [ ] Login com senha errada nao autentica.
- [ ] Logout expira o cookie de sessao (verificar em DevTools > Application > Cookies).
- [ ] Sessao orfã (banco recriado) redireciona para login com mensagem de info.
- [ ] URL direta de area logada (`?url=mapa`) sem sessao redireciona para login.
- [ ] URL de area de mestre (`?url=mestre`) com conta de aluno retorna 403.

### Criacao de personagem

- [ ] Todas as 6 classes aparecem na tela de criacao.
- [ ] Apos criar, redirecionamento correto para a home/mapa.
- [ ] HP/MP iniciais batem com os valores em `config/config.php (CLASSES)`.

### Batalha

- [ ] Questoes de multipla escolha funcionam (clicar opcao, enviar, ver resultado).
- [ ] Questoes V/F, completar, ordenar funcionam.
- [ ] Acerto aumenta o combo e exibe animacao de dano.
- [ ] Erro aplica dano ao heroi e zera o combo.
- [ ] MP e consumido ao usar ataque especial.
- [ ] Pocao de HP cura corretamente.
- [ ] Fugir encerra a batalha sem conceder recompensas.
- [ ] Ao vencer, XP, ouro e progresso sao registrados.
- [ ] Refazer a mesma fase raramente repete as mesmas perguntas (anti-repeticao).
- [ ] Gabarito NAO aparece no DevTools > Network > resposta JSON de batalha.

### Loja e inventario

- [ ] Comprar item debita ouro e adiciona ao inventario.
- [ ] Comprar sem ouro suficiente exibe erro e nao debita.
- [ ] Vender item credita ouro e remove do inventario.
- [ ] Equipar item aplica bonus de ataque/defesa na batalha.
- [ ] Descartar item remove permanentemente (sem desfazer).

### Mapa

- [ ] 35 fases aparecem distribuidas nas 6 regioes.
- [ ] Icones de fases concluidas mostram estrelas.
- [ ] Fase bloqueada nao permite acesso direto via URL.

### Painel do mestre

- [ ] Criar fase aparece no banco e no mapa.
- [ ] Editar desafio atualiza a pergunta.
- [ ] Excluir fase remove em cascade os desafios.
- [ ] Conta de aluno nao acessa `?url=mestre`.

## Proposta PHPUnit — services criticos

Os services de logica de jogo sao o alvo principal. Nao tem dependencia de
HTTP ou sessao — testavel com PDO em memoria (SQLite) ou MySQL de teste.

### Prioridade 1: BatalhaService

```
BatalhaServiceTest
  - sortearDesafios_priorizaIneditos()
  - verificar_respostaCorreta_retornaComboBonusAtualizado()
  - verificar_respostaErrada_aplicaDanoAoHeroi()
  - verificar_gabarito_naoPresente_em_estadoPublico()
  - concederRecompensas_comErro_rollbackTransaction()
```

### Prioridade 2: ProgressaoService

```
ProgressaoServiceTest
  - ganharXp_atingeNivel_atualizaHpMpMax()
  - ganharXp_abaixoDoLimite_naoAvancaNivel()
  - xpParaNivel_calculaCorretamente()
```

### Prioridade 3: ReputacaoService

```
ReputacaoServiceTest
  - ajustar_naoUltrapassaReputacaoMaxima()
  - ajustar_naoUltrapassaReputacaoMinima()
  - ajustar_atomico_semToctou()
```

### Prioridade 4: ConquistaService

```
ConquistaServiceTest
  - avaliarAposFase_concede_conquista_mestre_ao_completar_regiao()
  - avaliarAposFase_puroDeCoracao_concede_se_sem_ia()
  - avaliarAposFase_nao_duplica_conquista_ja_concedida()
```

## O que validar por area antes de fazer merge

| Area | O que checar |
|---|---|
| Models | `create/update/delete` com dados validos e invalidos |
| Auth | Login/logout/exigirLogin/exigirMestre |
| Router | Metodo protected nao e roteado; 404 para controller inexistente |
| Formularios | Token CSRF valido e invalido |
| Saida HTML | Nenhum dado do banco sai sem `e()` |
| Banco | `migrate.php` roda sem erro em banco vazio e em banco existente |
| Assets | Slugs novos sao resolvidos por `srcImagem()` sem retornar null |

## Quando houver CI

Sugestao de pipeline basico:

1. `php -l app/**/*.php` — lint de sintaxe PHP.
2. `php database/migrate.php --schema` — schema valido.
3. `php database/seed-banco-questoes.php` — seeder roda sem erro.
4. `vendor/bin/phpunit` — suite de testes.
