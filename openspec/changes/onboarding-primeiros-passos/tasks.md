# Tasks — Onboarding "Primeiros passos"

## 1. Modelo (dados + lógica)
- [x] 1.1 `config/config.php`: `ONBOARDING_NIVEL_MAX`
- [x] 1.2 `Inventario::temEquipado()` (read-only)
- [x] 1.3 `app/services/OnboardingService.php`: `montar(flags, nivel)` pura + `primeirosPassos()` defensivo

## 2. Teste
- [x] 2.1 `tools/verificar_onboarding.php` (CLI): head start (1/4 quando só herói), progressão, `mostrar` (gate de nível + completude)
- [x] 2.2 Rodar — verde

## 3. Integração (apresentação)
- [x] 3.1 `PerfilController`: passa `$onboarding`
- [x] 3.2 `app/views/perfil/index.php`: painel "🌟 Primeiros passos" no topo (só se `mostrar`); checklist + barra X/4
- [x] 3.3 `public/css/style.css`: estilos `.onboarding-*`

## 4. Verificação
- [x] 4.1 Visual no navegador (perfil do demo nível 1 = 1/4; injetar/reverter p/ ver progressão)
- [x] 4.2 `openspec validate onboarding-primeiros-passos` OK
