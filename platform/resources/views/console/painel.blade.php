@extends('layouts.console')

@section('titulo', 'Instituições')

@section('conteudo')
    <h1>Instituições</h1>

    {{-- As desligadas aparecem primeiro na cabeça de quem opera: são as que ele acabou de
         provisionar e ainda não pôs no ar. Um painel que só mostra o que está funcionando
         não ajuda quem está pondo algo para funcionar. --}}
    <p>
        Provisionar uma escola é trabalho de terminal — só o <code>algorithmia:importar</code>
        sabe enchê-la. Aqui se liga, desliga e libera funcionalidade.
    </p>

    @foreach ($instituicoes as $instituicao)
        @php($tenant = $instituicao['tenant'])
        @php($m = $instituicao['metricas'])

        <section class="cartao">
            <h2>
                {{ $tenant->nome }}
                <small>{{ $tenant->slug }} — {{ $tenant->ativo ? 'no ar' : 'desligada' }}</small>
            </h2>

            <p>
                @if ($instituicao['host'])
                    <code>{{ $instituicao['host'] }}</code>
                @else
                    {{-- Ativa e sem domínio: no ar, e ninguém a alcança. O smoke reprova. --}}
                    <strong>sem domínio — ninguém a alcança</strong>
                @endif
            </p>

            <table>
                <thead>
                    <tr>
                        <th>contas</th>
                        {{-- Ativação: de cada cem contas, quantas criaram herói. Trezentas
                             contas e quarenta heróis não é problema de adoção — é problema
                             na tela de criação de personagem. --}}
                        <th>heróis (ativação)</th>
                        <th>fases concluídas</th>
                        <th>estrelas</th>
                        <th>respostas</th>
                        <th>precisão</th>
                        <th>última atividade</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ $m['contas'] }}</td>
                        <td>{{ $m['herois'] }} @if ($m['ativacao'] !== null) ({{ $m['ativacao'] }}%) @endif</td>
                        <td>{{ $m['fases_concluidas'] }}</td>
                        <td>{{ $m['estrelas'] }}</td>
                        <td>{{ $m['respostas'] }}</td>
                        <td>{{ $m['precisao'] === null ? '—' : $m['precisao'].'%' }}</td>
                        <td>{{ $m['ultima_atividade'] ?? '—' }}</td>
                    </tr>
                </tbody>
            </table>

            <h3>Funcionalidades</h3>

            <table>
                <tbody>
                    @foreach ($instituicao['flags'] as $chave => $estado)
                        <tr>
                            <td><code>{{ $chave }}</code></td>
                            <td>{{ $estado['descricao'] }}</td>
                            <td>
                                {{ $estado['ativa'] ? 'ligada' : 'desligada' }}
                                {{-- A distinção que importa numa auditoria: a escola pediu
                                     isto, ou apenas herdou o padrão de quem escreveu o código? --}}
                                <small>{{ $estado['sobrescrita'] ? 'a escola escolheu' : 'padrão do código' }}</small>
                            </td>
                            <td>
                                <form method="POST" action="{{ route('console.flag', $tenant) }}">
                                    @csrf
                                    <input type="hidden" name="chave" value="{{ $chave }}">
                                    <button type="submit" name="estado" value="{{ $estado['ativa'] ? 'desligar' : 'ligar' }}" class="botao botao-sm">
                                        {{ $estado['ativa'] ? 'desligar' : 'ligar' }}
                                    </button>
                                    @if ($estado['sobrescrita'])
                                        {{-- `padrao` REMOVE a opinião da escola. Gravar o valor
                                             do padrão a congelaria no valor de hoje. --}}
                                        <button type="submit" name="estado" value="padrao" class="botao botao-sm botao-fantasma">
                                            voltar ao padrão
                                        </button>
                                    @endif
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            @if ($tenant->ativo)
                <form method="POST" action="{{ route('console.desativar', $tenant) }}"
                      data-confirmar="Desligar {{ $tenant->slug }}? Os alunos dela receberão 404 imediatamente.">
                    @csrf
                    <button type="submit" class="botao botao-perigo">Desligar</button>
                </form>
            @else
                {{-- Ativar roda o smoke daquela instituição e recusa uma escola injogável.
                     Uma escola ativa e vazia reprova o próximo deploy inteiro. --}}
                <form method="POST" action="{{ route('console.ativar', $tenant) }}">
                    @csrf
                    <button type="submit" class="botao">Ligar (roda o smoke antes)</button>
                </form>
            @endif
        </section>
    @endforeach
@endsection
