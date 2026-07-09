{{-- Logo do jogo. Porte de `marcaHtml()` de app/core/helpers.php: a variante
     escolhe classe e arquivo, e o resto é o mesmo <img>. --}}
@php
    use App\Support\Arte;

    $classes = [
        'header' => 'logo-marca logo-marca-header',
        'auth' => 'logo-marca logo-marca-auth',
        'rodape' => 'logo-marca logo-marca-rodape',
        'splash' => 'logo-marca logo-marca-splash splash-marca',
        'hero' => 'logo-marca logo-marca-hero',
    ];
    $arquivos = [
        'header' => 'logo-header',
        'rodape' => 'logo-header',
        'auth' => 'logo-marca-ilustrado',
        'splash' => 'logo-marca-ilustrado',
        'hero' => 'logo-marca-ilustrado',
    ];

    $classe = $classes[$variante ?? 'header'] ?? 'logo-marca';
    $src = Arte::src('ui/logos/'.($arquivos[$variante ?? 'header'] ?? 'logo-header'));
@endphp

@if ($src)
    <img src="{{ $src }}" class="{{ $classe }}" alt="Algorithmia"
         loading="{{ ($variante ?? '') === 'splash' ? 'eager' : 'lazy' }}">
@else
    <span class="svg-faltando" title="logo ausente">▢</span>
@endif
