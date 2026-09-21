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

/* ---------------- PRODUTOS ---------------- */

async function carregarProdutos() {
    const resposta = await fetch('ajax/produtos.php');
    const json = await resposta.json();
    const tbody = document.getElementById('tabelaProdutos');
    tbody.innerHTML = '';

    (json.dados || []).forEach((p) => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${p.nome}</td>
            <td>${p.fornecedor_nome}</td>
            <td>${formatarPreco(p.preco)}</td>
            <td>${p.quantidade_estoque}</td>
            <td class="text-end">
                <button class="btn btn-sm btn-outline-primary" onclick="editarProduto(${p.id}, '${p.nome.replace(/'/g, "\\'")}', '${(p.descricao || '').replace(/'/g, "\\'")}', ${p.preco}, ${p.quantidade_estoque}, ${p.fornecedor_id})">Editar</button>
                <button class="btn btn-sm btn-outline-danger" onclick="excluirProduto(${p.id})">Excluir</button>
            </td>`;
        tbody.appendChild(tr);
    });
}

function editarProduto(id, nome, descricao, preco, estoque, fornecedorId) {
    document.getElementById('produtoId').value = id;
    document.getElementById('produtoNome').value = nome;
    document.getElementById('produtoDescricao').value = descricao;
    document.getElementById('produtoPreco').value = preco;
    document.getElementById('produtoEstoque').value = estoque;
    document.getElementById('produtoFornecedor').value = fornecedorId;
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

    const dados = {
        acao: 'salvar',
        id: document.getElementById('produtoId').value || null,
        nome: document.getElementById('produtoNome').value,
        descricao: document.getElementById('produtoDescricao').value,
        preco: document.getElementById('produtoPreco').value,
        quantidade_estoque: document.getElementById('produtoEstoque').value,
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
        tr.innerHTML = `
            <td>${f.nome}</td>
            <td>${f.cnpj}</td>
            <td>${f.telefone || '-'}</td>
            <td class="text-end">
                <button class="btn btn-sm btn-outline-primary" onclick='editarFornecedor(${JSON.stringify(f)})'>Editar</button>
                <button class="btn btn-sm btn-outline-danger" onclick="excluirFornecedor(${f.id})">Excluir</button>
            </td>`;
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
        tr.innerHTML = `
            <td>${item.nome}</td>
            <td>${formatarPreco(item.preco)}</td>
            <td class="text-end">
                <button class="btn btn-sm btn-outline-danger" onclick="removerDaCestaAjax(${item.id})">Remover</button>
            </td>`;
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
