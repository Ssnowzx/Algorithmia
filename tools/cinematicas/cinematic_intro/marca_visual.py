"""Fragmentos de estilo compartilhados — identidade Algorithmia para cinemáticas."""

PALETA = {
    "navy": "#0b0c1d",
    "roxo": "#7c5cff",
    "roxo_claro": "#9d83ff",
    "ouro": "#ffce47",
    "ciano": "#8ce6ff",
}

# Bíblia de estilo — colar ANTES de cada cena no Sora/ChatGPT.
# No pipeline (--prompts), marca_visual.prompt_cena() já incorpora isto.
BIBLIA_ESTILO = """\
Ilustração digital pintada à mão, alta resolução, fantasia épica fundida com tecnologia \
arcana (magia + código). Composição horizontal 16:9, paisagem cinematográfica full-bleed, \
sem bordas. Paleta: navy #0b0c1d, roxo #7c5cff/#9d83ff, ouro #ffce47, runas ciano #8ce6ff. \
Iluminação dramática, brilho volumétrico, névoa leve, partículas de luz. Glifos rúnicos de \
código (</> { } ; 0 1) flutuando como vaga-lumes. Protagonista: aprendiz encapuzado, rosto \
na sombra (genérico). EVITAR: qualquer texto/letra legível na tela, logos, marca d'água, \
pixel art, 8-bit, rosto realista de pessoa específica, HUD/interface de jogo."""

ESTILO_BASE = BIBLIA_ESTILO

PALETA_TEXTO = ""  # já incluso na bíblia

EVITAR = ""  # já incluso na bíblia

REFERENCIAS_PADRAO = [
    "public/img/mapas/regiao-hello-world.png",
    "public/img/ui/logos/logo-marca-ilustrado.png",
]


def biblia_estilo() -> str:
    return BIBLIA_ESTILO


def prompt_cena(descricao: str, aspecto: str = "16:9") -> str:
    """Prompt completo para GenerateImage / pipeline interno."""
    if aspecto != "16:9":
        extra = (
            "Composição vertical 9:16, retrato cinematográfico full-bleed, "
            "preenchendo todo o quadro sem bordas."
        )
        return f"{descricao.strip()}\n\n{BIBLIA_ESTILO}\n{extra}"
    return f"{descricao.strip()}\n\n{BIBLIA_ESTILO}"


def prompt_sora_cena(descricao: str) -> str:
    """Prompt para Sora: bíblia + cena (cole como um bloco único)."""
    return f"{BIBLIA_ESTILO}\n\n---\n\n{descricao.strip()}"
