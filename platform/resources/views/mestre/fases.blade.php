@extends('layouts.app')
@section('titulo', 'Gerir Fases')

@section('conteudo')
<div class="cartao">
    <h1>Fases</h1>

    @foreach (['sucesso', 'erro', 'info'] as $tipo)
        @if (session($tipo))
            <div class="flash flash-{{ $tipo }}" role="status">{{ session($tipo) }}</div>
        @endif
    @endforeach

    <a class="botao" href="{{ route('mestre.fase.nova') }}">Nova fase</a>

    <table class="tabela">
        <thead>
            <tr><th>#</th><th>Ordem</th><th>Nome</th><th>Tipo</th><th>Mestre</th><th>Inimigo</th><th></th></tr>
        </thead>
        <tbody>
            @foreach ($fases as $fase)
                <tr>
                    <td>{{ $fase->id }}</td>
                    <td>{{ $fase->ordem_global }}</td>
                    <td>{{ $fase->nome }}</td>
                    <td>{{ $fase->tipo }}</td>
                    <td>{{ $fase->mestre?->nome ?? '—' }}</td>
                    <td>{{ $fase->inimigo_nome ?? '—' }}</td>
                    <td class="acoes">
                        <a class="botao botao-sm" href="{{ route('mestre.fase.editar', $fase) }}">Editar</a>

                        {{-- Apaga a fase E seus desafios em cascata. Confirma. --}}
                        <form method="POST" action="{{ route('mestre.fase.excluir', $fase) }}"
                              onsubmit="return confirm('Excluir a fase “{{ $fase->nome }}” e TODOS os seus desafios?')">
                            @csrf
                            <button type="submit" class="botao botao-sm botao-fantasma">Excluir</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
