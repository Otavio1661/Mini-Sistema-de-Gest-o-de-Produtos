const formLoja = document.getElementById('formLoja');

if (formLoja) {
    formLoja.addEventListener('submit', async (evento) => {
        evento.preventDefault();

        const selecionados = Array.from(document.querySelectorAll('.produto-checkbox:checked'))
            .map((input) => parseInt(input.value, 10));

        const alerta = document.getElementById('alertaLoja');

        if (selecionados.length === 0) {
            alerta.innerHTML = `<div class="alert alert-warning">Selecione ao menos um produto antes de adicionar à cesta.</div>`;
            return;
        }

        const resposta = await fetch('ajax/cesta.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ acao: 'adicionar', produto_ids: selecionados }),
        });
        const json = await resposta.json();

        if (json.sucesso) {
            window.location.href = 'cesta.php';
            return;
        }

        alerta.innerHTML = `<div class="alert alert-danger">${json.erro || 'Não foi possível adicionar os produtos.'}</div>`;
    });
}
