{{-- A casca do console do operador. Deliberadamente sóbria: quem está aqui não veio
     jogar, veio pôr uma escola no ar ou tirá-la. --}}
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('titulo', 'Console') — Algorithmia</title>

    <link rel="stylesheet" href="{{ asset('css/fontes.css') }}">
    <link rel="stylesheet" href="{{ asset('css/tokens.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/shell.css') }}">
</head>
<body>
    @auth('operador')
        <header class="topo">
            <a href="{{ route('console.painel') }}" class="marca">⚙ Console</a>
            <nav>
                <span>{{ auth('operador')->user()?->email }}</span>
                <form method="POST" action="{{ route('console.sair') }}" style="display:inline">
                    @csrf
                    <button type="submit" class="botao botao-sm botao-fantasma">Sair</button>
                </form>
            </nav>
        </header>
    @endauth

    @if (session('erro'))
        <div class="flash flash-erro" role="alert">{{ session('erro') }}</div>
    @endif

    @if (session('sucesso'))
        <div class="flash" role="status">{{ session('sucesso') }}</div>
    @endif

    <main class="conteudo">
        @yield('conteudo')
    </main>

    {{-- Só este. Desligar uma escola é destrutivo, e o `data-confirmar` pergunta antes.
         Nenhum `onsubmit=` inline: o `nonce` do CSP autoriza `<script>`, nunca atributos. --}}
    <script src="{{ asset('js/confirmar.js') }}"></script>
</body>
</html>
