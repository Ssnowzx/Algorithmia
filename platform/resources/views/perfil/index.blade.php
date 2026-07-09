@extends('layouts.app')
@section('titulo', 'Perfil')

@section('conteudo')
@php use App\Support\{Arte, Reputacao}; @endphp

<div class="cartao">
    <img src="{{ Arte::srcOu('herois/'.config("jogo.classes.{$heroi->classe}.svg"), 'herois/heroi-ranger') }}" alt="">

    <h1>{{ $heroi->nome }}</h1>
    <p class="sutil">
        {{ config("jogo.classes.{$heroi->classe}.nome") }} · Nível {{ $heroi->nivel }}
        @if ($tituloLenda !== '')
            · <strong class="selo">👑 {{ $tituloLenda }}</strong>
        @endif
    </p>

    <dl class="atributos">
        <dt>XP</dt><dd>{{ $heroi->xp }} / {{ $xpProximoNivel }}</dd>
        <dt>Vida</dt><dd>{{ $heroi->hp_atual }} / {{ $heroi->hp_max }}</dd>
        <dt>Mana</dt><dd>{{ $heroi->mp_atual }} / {{ $heroi->mp_max }}</dd>
        <dt>Ouro</dt><dd>{{ $heroi->ouro }}</dd>
        <dt>Fases concluídas</dt><dd>{{ $fasesConcluidas }}</dd>
        <dt>Estrelas</dt><dd>{{ $estrelas }}</dd>
        <dt>Reputação</dt>
        <dd title="Cai a cada uso do Fragmento da IA; sobe ao vencer sem ele">
            {{ Reputacao::rotulo($heroi->reputacao) }} ({{ $heroi->reputacao }})
        </dd>
    </dl>
</div>

{{-- Primeiros passos: o primeiro item já nasce feito. Uma jornada que começou
     tem muito mais chance de ser terminada do que uma que ainda não. --}}
@if ($onboarding['mostrar'])
    <div class="cartao painel-onboarding">
        <h2>Primeiros passos</h2>
        <p class="sutil">{{ $onboarding['completos'] }} de {{ $onboarding['total'] }} — você já começou.</p>

        <ul class="checklist">
            @foreach ($onboarding['passos'] as $passo)
                <li class="{{ $passo['feito'] ? 'feito' : 'pendente' }}">
                    {{ $passo['feito'] ? '✅' : '⬜' }} {{ $passo['label'] }}
                </li>
            @endforeach
        </ul>
    </div>
@endif

{{-- Missões da semana: derivadas da atividade da semana ISO. Sem cron, sem
     recompensa, sem persistência. --}}
@if ($missoes !== [])
    <div class="cartao">
        <h2>Missões da semana</h2>
        <p class="sutil">{{ $missoesCompletas }} de {{ count($missoes) }} concluídas. Zeram na segunda-feira.</p>

        <ul class="missoes">
            @foreach ($missoes as $missao)
                <li class="missao {{ $missao['completa'] ? 'completa' : '' }}">
                    <strong>{{ $missao['icone'] }} {{ $missao['titulo'] }}</strong>
                    <p class="sutil">{{ $missao['desc'] }}</p>

                    <div class="barra" role="progressbar"
                         aria-valuenow="{{ $missao['pct'] }}" aria-valuemin="0" aria-valuemax="100">
                        <div class="barra-fill" style="width: {{ $missao['pct'] }}%"></div>
                    </div>

                    <span class="sutil">
                        {{ $missao['atual'] }}/{{ $missao['alvo'] }}{{ $missao['unidade'] }}
                        @if ($missao['nota'] !== '') · {{ $missao['nota'] }} @endif
                        @if ($missao['completa']) · ✅ @endif
                    </span>
                </li>
            @endforeach
        </ul>
    </div>
@endif

{{-- Maestria por matéria: volume de acertos + precisão sustentada. A barra mede o
     fator mais atrasado dos dois, para nunca encher enganando. --}}
<div class="cartao">
    <h2>Maestria por matéria</h2>
    <p class="sutil">{{ $materiasDominadas }}/{{ $totalMaterias }} dominadas. Domínio é acerto consistente, não sorte com três respostas.</p>

    <ul class="maestria">
        @foreach ($maestria as $materia)
            @php $faixa = $materia['faixa']; @endphp

            <li class="materia maestria-{{ $faixa['cor'] }}">
                <strong>{{ $faixa['icone'] }} {{ $materia['rotulo'] }}</strong>
                <span class="selo">{{ $faixa['rotulo'] }}</span>

                @if ($faixa['total'] > 0)
                    <span class="sutil">{{ $faixa['acertos'] }}/{{ $faixa['total'] }} · {{ $faixa['precisao'] }}% de precisão</span>
                @else
                    <span class="sutil">nenhuma resposta ainda</span>
                @endif

                @if ($faixa['proximo'])
                    <div class="barra" role="progressbar"
                         aria-valuenow="{{ $faixa['proximo']['pct'] }}" aria-valuemin="0" aria-valuemax="100">
                        <div class="barra-fill" style="width: {{ $faixa['proximo']['pct'] }}%"></div>
                    </div>
                    <span class="sutil">{{ $faixa['proximo']['dica'] }}</span>
                @else
                    <span class="sutil">Maestria máxima 👑</span>
                @endif
            </li>
        @endforeach
    </ul>
</div>

{{-- Domínio das regiões: "Dominada" exige 3 estrelas em tudo, o que só acontece
     sem um erro e sem tocar no Fragmento. --}}
@if ($regioes !== [])
    <div class="cartao">
        <h2>Domínio das regiões</h2>
        <p class="sutil">{{ $regioesDominadas }}/{{ count($regioes) }} dominadas. Dominar exige perfeição: 3 estrelas em cada fase.</p>

        <ul class="regioes">
            @foreach ($regioes as $regiao)
                @php $faixa = $regiao['faixa']; @endphp

                <li class="regiao regiao-{{ $faixa['cor'] }}" style="--cor-regiao: {{ $regiao['cor_tema'] }}">
                    <img src="{{ Arte::srcOu("mestres/{$regiao['svg_slug']}", 'ui/logos/logo-header') }}" alt="">
                    <strong>{{ $regiao['regiao'] }}</strong>
                    <span class="selo">{{ $faixa['rotulo'] }}</span>

                    <div class="barra" role="progressbar"
                         aria-valuenow="{{ $faixa['pct'] }}" aria-valuemin="0" aria-valuemax="100">
                        <div class="barra-fill" style="width: {{ $faixa['pct'] }}%"></div>
                    </div>

                    <span class="sutil">
                        {{ $faixa['estrelas'] }}/{{ $faixa['max_estrelas'] }} ⭐ · {{ $faixa['dica'] }}
                    </span>
                </li>
            @endforeach
        </ul>
    </div>
@endif

<div class="cartao">
    <h2>Conquistas</h2>

    <ul class="grade-conquistas">
        @foreach ($conquistas as $conquista)
            @php $obtida = in_array($conquista->id, $conquistasObtidas, true); @endphp

            {{-- Uma conquista secreta não revela seu nome antes de ser obtida:
                 spoiler é o oposto de recompensa. --}}
            <li class="conquista {{ $obtida ? 'obtida' : 'trancada' }}">
                @if ($obtida || ! $conquista->secreta)
                    <img src="{{ Arte::srcOu("ui/conquistas/{$conquista->svg_slug}", 'ui/logos/logo-header') }}" alt="">
                    <strong>{{ $conquista->nome }}</strong>
                    <span class="sutil">{{ $conquista->descricao }}</span>
                @else
                    <span class="selo">🔒</span>
                    <strong>Conquista secreta</strong>
                    <span class="sutil">Descubra por conta própria.</span>
                @endif
            </li>
        @endforeach
    </ul>
</div>
@endsection
