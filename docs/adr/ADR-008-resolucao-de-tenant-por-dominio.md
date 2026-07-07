# ADR-008: Resolução de tenant por domínio

## Contexto

A plataforma precisa distinguir rotas globais, rotas de plataforma e rotas tenant-aware
sem inferir tenant pelo usuário autenticado. O host da requisição é a entrada de resolução
mais segura para o modelo de multitenancy escolhido.

## Decisão

Resolver tenant por host confiável, normalizado e validado antes de qualquer consulta
tenant-aware. O `TenantResolver` produzirá um `CurrentTenant` imutável por requisição.

## Alternativas rejeitadas

- Inferir tenant pelo usuário: mistura identidade com escopo de dados.
- Usar parâmetro na URL como fonte principal: abre espaço para manipulação indevida.
- Permitir fallback para tenant padrão: violaria falha fechada.

## Consequências

- Hosts com protocolo, caminho, query string ou porta devem ser rejeitados.
- Domínios ativos devem ser globalmente únicos.
- `/healthz` e outras rotas globais permanecem fora da resolução.

## Riscos

- Configuração incorreta de proxy confiável pode permitir `Host` ou `X-Forwarded-Host` adulterados.
- Ambientes de desenvolvimento e staging precisam registrar explicitamente hosts válidos.

## Estratégia de reversibilidade

Se a resolução por domínio precisar ser ajustada, a reversão deve preservar as rotas globais
e manter a plataforma funcional sem tenant-aware routes até o ajuste da configuração.

## Impactos na migração do legado

Nenhum impacto direto no legado PHP/MySQL. O contrato é exclusivo da nova plataforma.

## Critérios de aceite

- Host inválido ou inexistente falha de modo fechado.
- `CurrentTenant` existe apenas após resolução bem-sucedida.
- `/healthz` não depende de tenant.
