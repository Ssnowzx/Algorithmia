@extends('layouts.app')
@section('titulo', 'Gerir Desafios')

@section('conteudo')
<div class="cartao">
    <h1>Desafios</h1>

    @foreach (['sucesso', 'erro', 'info'] as $tipo)
        @if (session($tipo))
            <div class="flash flash-{{ $tipo }}" role="status">{{ session($tipo) }}</div>
        @endif
    @endforeach

    <a class="botao" href="{{ route('mestre.desafio.novo') }}">Novo desafio</a>

    <table class="tabela">
        <thead>
            <tr><th>#</th><th>Fase</th><th>Tipo</th><th>Assunto</th><th>Pergunta</th><th>Dif.</th><th></th></tr>
        </thead>
        <tbody>
            @foreach ($desafios as $desafio)
                <tr>
                    <td>{{ $desafio->id }}</td>
                    <td>{{ $desafio->fase->nome }}</td>
                    <td>{{ $desafio->tipo }}</td>
                    <td>{{ config("jogo.assuntos_rotulos.{$desafio->assunto}") }}</td>
                    <td>{{ Str::limit($desafio->pergunta, 60) }}</td>
                    <td>{{ $desafio->dificuldade }}</td>
                    <td class="acoes">
                        <a class="botao botao-sm" href="{{ route('mestre.desafio.editar', $desafio) }}">Editar</a>

                        {{-- Excluir é POST e confirma: no legado bastava um GET. --}}
                        <form method="POST" action="{{ route('mestre.desafio.excluir', $desafio) }}"
                              data-confirmar="Excluir o desafio {{ $desafio->id }}?">
                            @csrf
                            <button type="submit" class="botao botao-sm botao-fantasma">Excluir</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{ $desafios->links() }}
</div>
@endsection
