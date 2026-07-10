{{-- Splash: vitrine para o visitante. Porte de app/views/home/index.php.
     Página autossuficiente, sem o layout do app. --}}
@php use App\Support\Arte; @endphp
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Algorithmia — A Lenda dos Cinco Mestres</title>
    <link rel="stylesheet" href="{{ asset('css/tokens.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/cena.css') }}">
</head>
<body class="pagina-home">

<section class="home-entrada">
    <img class="home-entrada-bg" src="{{ Arte::src('ui/splash-cena') }}" alt="" aria-hidden="true" decoding="async">
    <div class="home-entrada-overlay"></div>
    <div class="splash-particulas" aria-hidden="true"></div>

    <div class="home-entrada-conteudo">
        @include('componentes.marca', ['variante' => 'hero'])

        <p class="home-entrada-sub">
            A Lenda dos Cinco Mestres — onde <strong>programar é magia</strong> e aprender é na marra.
        </p>

        <a class="home-entrada-botao" href="{{ route('registro') }}">
            @if ($botao = Arte::src('ui/botoes/botao-entrar-mundo'))
                <img src="{{ $botao }}" alt="Entrar no mundo" loading="eager">
            @else
                <span class="botao">Entrar no mundo</span>
            @endif
        </a>

        <a class="home-login-link" href="{{ route('login') }}">Já vendi minha alma aqui</a>
        <a class="seta-rolar" href="#saibaMais" aria-label="Rolar para saber mais">▾</a>
    </div>
</section>

<div class="conteudo" id="saibaMais">
    <div class="secao-historia">
        <h2>O Reino de Algorithmia</h2>
        <p>Houve um tempo em que uma <strong>IA Ancestral</strong> dava todas as respostas. Maravilhoso, até os
        programadores esquecerem como pensar. Aí ela travou no <em>Grande Timeout</em>, o mundo quase virou um
        <code>500 Internal Server Error</code> e os Cinco Mestres tiveram que selar a coitada no Abismo do
        <code>/dev/null</code> — e fundar um culto à indentação, porque é claro que fundaram.</p>

        <p>Agora os Fragmentos da IA reapareceram (de novo) e os bugs voltaram a escapar das fendas (de novo). Você,
        mais um aprendiz genérico da Vila Hello World, herda um Fragmento e vai treinar com os Cinco Mestres. A cada
        cola que der, mais perto fica do destino sombrio de <strong>Lorde Segfault</strong>. Ou da redenção. Sem pressão.</p>
    </div>

    <h2 class="titulo-secao" style="text-align:center">Os Cinco Mestres</h2>
    <div class="mestres-grid">
        @foreach ($mestres as $mestre)
            <div class="mestre-card">
                <div class="retrato">
                    <img src="{{ Arte::srcOu("mestres/{$mestre->svg_slug}", 'ui/logos/logo-header') }}" alt="">
                </div>
                <h3>{{ $mestre->nome }}</h3>
                <div class="titulo-m">{{ $mestre->titulo }}</div>
                <div class="disc">{{ $mestre->disciplina }}</div>
                <div class="disc">📍 {{ $mestre->regiao }}</div>
            </div>
        @endforeach
    </div>

    <h2 class="titulo-secao" style="text-align:center;margin-top:2.4rem">…e Aquele que Caiu</h2>
    <div class="mestres-grid" style="max-width:330px;margin:0 auto">
        <div class="mestre-card">
            <div class="retrato">
                <img src="{{ Arte::srcOu('mapas/fase-lorde-segfault', 'inimigos/inimigo-segfault') }}" alt="">
            </div>
            <h3>Márcio</h3>
            <div class="titulo-m">O Lorde Segfault</div>
            <div class="disc">Soberano do /dev/null — outrora Zero, o primeiro aluno</div>
            <div class="disc">📍 O Abismo do /dev/null</div>
        </div>
    </div>

    <div style="text-align:center;margin-top:2.5rem">
        <a class="botao" href="{{ route('registro') }}">Criar meu herói agora</a>
        <a class="botao botao-fantasma" href="{{ route('lore') }}">Ler a história completa</a>
    </div>
</div>

<footer class="rodape">
    <div class="rodape-conteudo">
        <span class="rodape-marca">@include('componentes.marca', ['variante' => 'rodape'])</span>
        <span class="rodape-sep">·</span>
        <span>A Lenda dos Cinco Mestres</span>
    </div>
</footer>

<script src="{{ asset('js/som.js') }}"></script>
<script src="{{ asset('js/ui.js') }}"></script>
<script nonce="{{ $cspNonce }}">
    // Só o feedback sonoro dos botões de entrada — sem trilha de fundo.
    document.querySelectorAll('.home-entrada-botao, .home-login-link').forEach(function (botao) {
        botao.addEventListener('mouseenter', function () { window.SOM?.clique(); });
        botao.addEventListener('click', function () { window.SOM?.pressStart(); });
    });
</script>
</body>
</html>
