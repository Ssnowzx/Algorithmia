{{-- Casca comum. O CSS é o mesmo do jogo em PHP puro: o port não é uma
     oportunidade para redesenhar a identidade visual. --}}
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('titulo', 'Algorithmia') — A Lenda dos Cinco Mestres</title>

    <link rel="stylesheet" href="{{ asset('css/tokens.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/shell.css') }}">
    @stack('estilos')
</head>
<body>
    @auth
        <header class="topo">
            <a href="{{ route('mapa') }}" class="marca">⚔ Algorithmia</a>
            <nav>
                <a href="{{ route('mapa') }}">Mapa</a>
                <a href="{{ route('loja') }}">Loja</a>
                <a href="{{ route('inventario') }}">Inventário</a>
                <a href="{{ route('ranking') }}">Ranking</a>
                <a href="{{ route('perfil') }}">Perfil</a>
                <a href="{{ route('lore') }}">História</a>

                @if (auth()->user()?->ehMestre())
                    <a href="{{ route('mestre.painel') }}">Painel do Mestre</a>
                @endif

                {{-- Sair é POST: um <img src="/sair"> não deve derrubar a sessão. --}}
                <form method="POST" action="{{ route('sair') }}" style="display:inline">
                    @csrf
                    <button type="submit" class="botao botao-sm botao-fantasma">Sair</button>
                </form>
            </nav>
        </header>
    @endauth

    @if (session('erro'))
        <div class="flash flash-erro" role="alert">{{ session('erro') }}</div>
    @endif

    <main class="conteudo">
        @yield('conteudo')
    </main>

    {{-- Os mesmos scripts do jogo em PHP puro, na mesma ordem: som.js expõe
         window.SOM, do qual ui.js e app.js dependem. --}}
    <script src="{{ asset('js/som.js') }}"></script>
    <script src="{{ asset('js/ui.js') }}"></script>
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/celebracao.js') }}"></script>

    @stack('scripts')
</body>
</html>
