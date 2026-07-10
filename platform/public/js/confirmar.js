/**
 * Confirmação de ações destrutivas.
 *
 * Isto vivia em `onsubmit="return confirm(...)"` dentro do HTML. Um `nonce` de CSP
 * autoriza elementos `<script>`, nunca atributos de evento — mantê-los exigiria
 * `script-src 'unsafe-inline'`, que é justamente o que o CSP existe para proibir.
 *
 * Sem esta ligação, os formulários de exclusão continuariam funcionando: eles apenas
 * enviariam SEM perguntar. Por isso o teste cobre o atributo, e não o diálogo.
 */
document.addEventListener('submit', function (evento) {
    var formulario = evento.target;

    if (!(formulario instanceof HTMLFormElement)) {
        return;
    }

    var pergunta = formulario.dataset.confirmar;

    if (pergunta && !window.confirm(pergunta)) {
        evento.preventDefault();
    }
});
