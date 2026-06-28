# Padroes de Codigo — Algorithmia

[← Inicio](README.md)

## PHP

### `declare(strict_types=1)`

Obrigatorio em `index.php`, `config/bestiario.php`, `database/migrate.php` e
scripts CLI. Models e controllers nao tem por limitacao historica — adicionar
em arquivos novos.

### Saida segura: sempre `e()`

```php
// CORRETO
echo e($usuario['nome']);
echo '<a href="' . e(url('perfil')) . '">Perfil</a>';

// ERRADO — nunca
echo $usuario['nome'];
echo $_GET['q'];
```

A funcao `e()` em `app/core/helpers.php` chama `htmlspecialchars(..., ENT_QUOTES, 'UTF-8')`.

### Prepared statements via Model

Nunca escrever SQL nos controllers ou nas views. O Model base fornece os metodos
comuns; para queries especificas, adicione metodos no Model correspondente:

```php
// CORRETO — no model
public function rankingGeral(): array {
    return $this->db->query(
        'SELECT p.nome, p.nivel FROM personagens p ORDER BY p.nivel DESC LIMIT 10'
    )->fetchAll();
}

// ERRADO — SQL no controller
$ranking = $this->db->query('SELECT ...');
```

Quando o nome de coluna precisa ser interpolado (nao pode ser parametrizado),
use `colunaSegura()` do Model base:

```php
$col = $this->colunaSegura($coluna); // valida /^[a-zA-Z_][a-zA-Z0-9_]*$/
$stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$col} = :v");
```

### Services para logica de negocio

Regra: se a operacao toca mais de um model OU tem logica condicional relevante,
vai para um service, nao para o controller.

```php
// CORRETO
class BatalhaController extends Controller {
    public function responder(): void {
        // ...
        $resultado = (new BatalhaService())->verificar($estado, $resposta);
        $this->json($resultado);
    }
}

// ERRADO — logica de batalha no controller
public function responder(): void {
    $dano = $estado['heroi_ataque'] * (1 + $combo * COMBO_BONUS); // nao aqui
}
```

### CSRF

- Formularios POST: incluir `<?= csrf_field() ?>` e chamar `$this->exigirCsrf()` no controller.
- Endpoints AJAX de batalha: o token deve ser incluido no cabecalho ou corpo JSON.
- Rotas de escrita (loja, inventario, mestre) devem ser POST — nao GET.

### Flash messages

```php
// Controller
$this->flash('sucesso', 'Item comprado!');
$this->redirect('loja');

// View (layout/header.php ja renderiza)
// Nao exibir manualmente — o layout cuida.
```

### Sem SQL na view

Views so recebem dados pre-processados via `$data`. Se a view precisar de dado
extra, o controller deve busca-lo e passar no array.

### Operacoes em transacao

Multiplas escritas relacionadas devem usar `beginTransaction`:

```php
$pdo = getConnection();
$pdo->beginTransaction();
try {
    (new Personagem())->update($id, ['xp' => $novoXp]);
    (new ProgressoFase())->registrar($personId, $faseId, $estrelas, $acertos);
    $pdo->commit();
} catch (PDOException $e) {
    $pdo->rollBack();
    throw $e;
}
```

### Atualizacoes atomicas (TOCTOU)

Para ouro, reputacao e quantidades de inventario, usar UPDATE atomico em vez
de SELECT + calculo + UPDATE:

```php
// CORRETO — atomico
$stmt = $pdo->prepare(
    'UPDATE personagens SET ouro = ouro - :preco WHERE id = :id AND ouro >= :preco'
);
$stmt->execute(['preco' => $item['preco'], 'id' => $personId]);
if ($stmt->rowCount() === 0) { /* saldo insuficiente */ }

// ERRADO — TOCTOU
$heroi = (new Personagem())->findById($id);
(new Personagem())->update($id, ['ouro' => $heroi['ouro'] - $item['preco']]);
```

---

## JavaScript (`public/js/`)

- Vanilla JS — sem frameworks, sem bundler.
- Nao usar `innerHTML` com dados do servidor sem sanitizacao.
- Dados do servidor chegam via JSON em endpoints como `batalha/estado`.
- O gabarito nunca esta no JSON publico — `BatalhaService::estadoPublico()` filtra antes de enviar.
- Sem `console.log` em commits.

---

## CSS (`public/css/`)

- `style.css` — global: variaveis CSS (`:root`), layout, componentes reutilizaveis.
- `batalha.css`, `cena.css`, `mapa.css` — estilos de area especifica.
- Paleta definida nas variaveis de `:root` em `style.css` — nunca hardcodar cores nos templates.
- `color-mix()` requer fallback para Safari <= 15 (48 ocorrencias pendentes de correcao).
- Tokens planejados: `public/css/tokens.css` (ver auditoria `docs/auditoria/README.md`).

---

## Python (`tools/`)

- Scripts de suporte (PDFs, recorte de imagem) — nao fazem parte do runtime do jogo.
- Kit de marca: `tools/pdf/marca_pdf.py` — todo PDF novo deve importa-lo.
- Ambiente isolado para rembg: `tools/.venv-rembg/` (nao versionar o venv).
- Fontes cacheadas em `tools/.cache/` (nao versionar).
