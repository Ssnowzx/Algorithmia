@extends('layouts.app')
@section('titulo', 'Gerir Itens')

@section('conteudo')
<div class="cartao">
    <h1>Itens</h1>

    @foreach (['sucesso', 'erro', 'info'] as $tipo)
        @if (session($tipo))
            <div class="flash flash-{{ $tipo }}" role="status">{{ session($tipo) }}</div>
        @endif
    @endforeach

    <a class="botao" href="{{ route('mestre.item.novo') }}">Novo item</a>

    <table class="tabela">
        <thead>
            <tr><th>#</th><th>Nome</th><th>Tipo</th><th>Raridade</th><th>Preço</th><th>Efeito</th><th>Comprável</th><th></th></tr>
        </thead>
        <tbody>
            @foreach ($itens as $item)
                <tr>
                    <td>{{ $item->id }}</td>
                    <td>{{ $item->nome }}</td>
                    <td>{{ $item->tipo }}</td>
                    <td>{{ $item->raridade }}</td>
                    <td>{{ $item->preco }}</td>
                    <td>
                        @forelse ($item->efeito ?? [] as $chave => $valor)
                            <span class="selo">{{ str_replace('_', ' ', $chave) }} +{{ $valor }}</span>
                        @empty
                            —
                        @endforelse
                    </td>
                    <td>{{ $item->compravel ? 'sim' : 'não' }}</td>
                    <td class="acoes">
                        <a class="botao botao-sm" href="{{ route('mestre.item.editar', $item) }}">Editar</a>

                        <form method="POST" action="{{ route('mestre.item.excluir', $item) }}"
                              data-confirmar="Excluir “{{ $item->nome }}”? Ele sairá do inventário de todos os heróis.">
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
