function mostrarAlerta(mensagem, tipo) {
    const container = document.getElementById('alertaAjax');
    container.innerHTML = `<div class="alert alert-${tipo} alert-dismissible fade show" role="alert">
        ${mensagem}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>`;
}

function formatarPreco(valor) {
    return 'R$ ' + Number(valor).toFixed(2).replace('.', ',');
}

function criarBotao(texto, classe, aoClicar) {
    const btn = document.createElement('button');
    btn.type = 'button';
    btn.className = classe;
    btn.textContent = texto;
    btn.addEventListener('click', aoClicar);
    return btn;
}

function criarCelulaAcoes(...botoes) {
    const td = document.createElement('td');
    td.className = 'text-end';
    botoes.forEach((btn, i) => {
        if (i > 0) td.appendChild(document.createTextNode(' '));
        td.appendChild(btn);
    });
    return td;
}

/* ---------------- PRODUTOS ---------------- */

async function carregarProdutos() {
    const resposta = await fetch('ajax/produtos.php');
    const json = await resposta.json();
    const tbody = document.getElementById('tabelaProdutos');
    tbody.innerHTML = '';

    (json.dados || []).forEach((p) => {
        const tr = document.createElement('tr');

        const tdNome = document.createElement('td');
        tdNome.textContent = p.nome;
        const tdFornecedor = document.createElement('td');
        tdFornecedor.textContent = p.fornecedor_nome;
        const tdPreco = document.createElement('td');
        tdPreco.textContent = formatarPreco(p.preco);
        const tdEstoque = document.createElement('td');
        tdEstoque.textContent = p.quantidade_estoque;

        tr.append(tdNome, tdFornecedor, tdPreco, tdEstoque);
        tr.appendChild(criarCelulaAcoes(
            criarBotao('Editar', 'btn btn-sm btn-outline-primary', () => editarProduto(p)),
            criarBotao('Excluir', 'btn btn-sm btn-outline-danger', () => excluirProduto(p.id)),
        ));

        tbody.appendChild(tr);
    });
}

function editarProduto(p) {
    document.getElementById('produtoId').value = p.id;
    document.getElementById('produtoNome').value = p.nome;
    document.getElementById('produtoDescricao').value = p.descricao || '';
    document.getElementById('produtoPreco').value = p.preco;
    document.getElementById('produtoEstoque').value = p.quantidade_estoque;
    document.getElementById('produtoFornecedor').value = p.fornecedor_id;
}

async function excluirProduto(id) {
    if (!confirm('Excluir este produto?')) return;

    const resposta = await fetch('ajax/produtos.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ acao: 'excluir', id }),
    });
    const json = await resposta.json();

    mostrarAlerta(json.mensagem || json.erro, json.sucesso ? 'success' : 'danger');
    carregarProdutos();
}

document.getElementById('formProduto').addEventListener('submit', async (evento) => {
    evento.preventDefault();

    const preco = parseFloat(document.getElementById('produtoPreco').value);
    const estoque = parseInt(document.getElementById('produtoEstoque').value, 10);

    if (preco < 0 || estoque < 0) {
        mostrarAlerta('Preço e estoque não podem ser negativos.', 'danger');
        return;
    }

    const dados = {
        acao: 'salvar',
        id: document.getElementById('produtoId').value || null,
        nome: document.getElementById('produtoNome').value,
        descricao: document.getElementById('produtoDescricao').value,
        preco,
        quantidade_estoque: estoque,
        fornecedor_id: document.getElementById('produtoFornecedor').value,
    };

    const resposta = await fetch('ajax/produtos.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(dados),
    });
    const json = await resposta.json();

    mostrarAlerta(json.mensagem || json.erro, json.sucesso ? 'success' : 'danger');

    if (json.sucesso) {
        document.getElementById('formProduto').reset();
        document.getElementById('produtoId').value = '';
        carregarProdutos();
    }
});

document.getElementById('cancelarProduto').addEventListener('click', () => {
    document.getElementById('formProduto').reset();
    document.getElementById('produtoId').value = '';
});

/* ---------------- FORNECEDORES ---------------- */

async function carregarFornecedores() {
    const resposta = await fetch('ajax/fornecedores.php');
    const json = await resposta.json();
    const tbody = document.getElementById('tabelaFornecedores');
    tbody.innerHTML = '';

    (json.dados || []).forEach((f) => {
        const tr = document.createElement('tr');

        const tdNome = document.createElement('td');
        tdNome.textContent = f.nome;
        const tdCnpj = document.createElement('td');
        tdCnpj.textContent = f.cnpj;
        const tdTelefone = document.createElement('td');
        tdTelefone.textContent = f.telefone || '-';

        tr.append(tdNome, tdCnpj, tdTelefone);
        tr.appendChild(criarCelulaAcoes(
            criarBotao('Editar', 'btn btn-sm btn-outline-primary', () => editarFornecedor(f)),
            criarBotao('Excluir', 'btn btn-sm btn-outline-danger', () => excluirFornecedor(f.id)),
        ));

        tbody.appendChild(tr);
    });
}

function editarFornecedor(f) {
    document.getElementById('fornecedorId').value = f.id;
    document.getElementById('fornecedorNome').value = f.nome;
    document.getElementById('fornecedorCnpj').value = f.cnpj;
    document.getElementById('fornecedorTelefone').value = f.telefone || '';
    document.getElementById('fornecedorEmail').value = f.email || '';
    document.getElementById('fornecedorEndereco').value = f.endereco || '';
}

async function excluirFornecedor(id) {
    if (!confirm('Excluir este fornecedor?')) return;

    const resposta = await fetch('ajax/fornecedores.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ acao: 'excluir', id }),
    });
    const json = await resposta.json();

    mostrarAlerta(json.mensagem || json.erro, json.sucesso ? 'success' : 'danger');
    carregarFornecedores();
    carregarProdutos();
}

document.getElementById('formFornecedor').addEventListener('submit', async (evento) => {
    evento.preventDefault();

    const dados = {
        acao: 'salvar',
        id: document.getElementById('fornecedorId').value || null,
        nome: document.getElementById('fornecedorNome').value,
        cnpj: document.getElementById('fornecedorCnpj').value,
        telefone: document.getElementById('fornecedorTelefone').value,
        email: document.getElementById('fornecedorEmail').value,
        endereco: document.getElementById('fornecedorEndereco').value,
    };

    const resposta = await fetch('ajax/fornecedores.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(dados),
    });
    const json = await resposta.json();

    mostrarAlerta(json.mensagem || json.erro, json.sucesso ? 'success' : 'danger');

    if (json.sucesso) {
        document.getElementById('formFornecedor').reset();
        document.getElementById('fornecedorId').value = '';
        carregarFornecedores();
    }
});

document.getElementById('cancelarFornecedor').addEventListener('click', () => {
    document.getElementById('formFornecedor').reset();
    document.getElementById('fornecedorId').value = '';
});

/* ---------------- CESTA ---------------- */

async function carregarCestaAjax() {
    const resposta = await fetch('ajax/cesta.php');
    const json = await resposta.json();
    const tbody = document.getElementById('tabelaCesta');
    tbody.innerHTML = '';

    (json.dados || []).forEach((item) => {
        const tr = document.createElement('tr');

        const tdNome = document.createElement('td');
        tdNome.textContent = item.nome;
        const tdPreco = document.createElement('td');
        tdPreco.textContent = formatarPreco(item.preco);

        tr.append(tdNome, tdPreco);
        tr.appendChild(criarCelulaAcoes(
            criarBotao('Remover', 'btn btn-sm btn-outline-danger', () => removerDaCestaAjax(item.id)),
        ));

        tbody.appendChild(tr);
    });

    document.getElementById('totalCestaAjax').textContent = formatarPreco(json.total || 0);
}

async function removerDaCestaAjax(produtoId) {
    const resposta = await fetch('ajax/cesta.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ acao: 'remover', produto_id: produtoId }),
    });
    const json = await resposta.json();

    mostrarAlerta(json.mensagem || json.erro, json.sucesso ? 'success' : 'danger');
    carregarCestaAjax();
}

carregarProdutos();
carregarFornecedores();
carregarCestaAjax();
