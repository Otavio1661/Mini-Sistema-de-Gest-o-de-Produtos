<?php
require_once __DIR__ . '/classes/Auth.php';
require_once __DIR__ . '/classes/Fornecedor.php';

Auth::exigirLogin();

$fornecedores = Fornecedor::listarTodos();

$paginaTitulo = 'Atualização via AJAX - Gestão de Produtos';
require __DIR__ . '/includes/header.php';
require __DIR__ . '/includes/navbar.php';
?>
<div class="container">
    <h2 class="mb-1">Área de Atualização (AJAX)</h2>
    <p class="text-muted">Cadastre, edite e exclua Produtos, Fornecedores e itens da Cesta sem recarregar a página.</p>

    <div id="alertaAjax"></div>

    <ul class="nav nav-tabs mb-3" id="abasGerenciar">
        <li class="nav-item">
            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tabProdutos" type="button">Produtos</button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tabFornecedores" type="button">Fornecedores</button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tabCesta" type="button">Cesta</button>
        </li>
    </ul>

    <div class="tab-content">
        <!-- PRODUTOS -->
        <div class="tab-pane fade show active" id="tabProdutos">
            <div class="row">
                <div class="col-md-5">
                    <div class="card shadow-sm mb-4">
                        <div class="card-body">
                            <h5 class="card-title">Produto</h5>
                            <form id="formProduto">
                                <input type="hidden" id="produtoId" name="id">
                                <div class="mb-2">
                                    <label class="form-label">Nome</label>
                                    <input type="text" class="form-control" id="produtoNome" required>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label">Descrição</label>
                                    <input type="text" class="form-control" id="produtoDescricao">
                                </div>
                                <div class="mb-2">
                                    <label class="form-label">Preço (R$)</label>
                                    <input type="number" step="0.01" min="0" class="form-control" id="produtoPreco" required>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label">Estoque</label>
                                    <input type="number" min="0" class="form-control" id="produtoEstoque" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Fornecedor</label>
                                    <select class="form-select" id="produtoFornecedor" required>
                                        <option value="">Selecione...</option>
                                        <?php foreach ($fornecedores as $f): ?>
                                            <option value="<?= $f->id ?>"><?= htmlspecialchars($f->nome) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary w-100">Salvar</button>
                                <button type="button" class="btn btn-outline-secondary w-100 mt-2" id="cancelarProduto">Limpar</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-md-7">
                    <table class="table table-striped align-middle">
                        <thead>
                            <tr><th>Nome</th><th>Fornecedor</th><th>Preço</th><th>Estoque</th><th></th></tr>
                        </thead>
                        <tbody id="tabelaProdutos"></tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- FORNECEDORES -->
        <div class="tab-pane fade" id="tabFornecedores">
            <div class="row">
                <div class="col-md-5">
                    <div class="card shadow-sm mb-4">
                        <div class="card-body">
                            <h5 class="card-title">Fornecedor</h5>
                            <form id="formFornecedor">
                                <input type="hidden" id="fornecedorId" name="id">
                                <div class="mb-2">
                                    <label class="form-label">Nome</label>
                                    <input type="text" class="form-control" id="fornecedorNome" required>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label">CNPJ</label>
                                    <input type="text" class="form-control" id="fornecedorCnpj" maxlength="18"
                                           placeholder="00.000.000/0000-00" required>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label">Telefone</label>
                                    <input type="text" class="form-control" id="fornecedorTelefone" maxlength="15"
                                           placeholder="(00) 00000-0000">
                                </div>
                                <div class="mb-2">
                                    <label class="form-label">E-mail</label>
                                    <input type="email" class="form-control" id="fornecedorEmail">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Endereço</label>
                                    <input type="text" class="form-control" id="fornecedorEndereco">
                                </div>
                                <button type="submit" class="btn btn-primary w-100">Salvar</button>
                                <button type="button" class="btn btn-outline-secondary w-100 mt-2" id="cancelarFornecedor">Limpar</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-md-7">
                    <table class="table table-striped align-middle">
                        <thead>
                            <tr><th>Nome</th><th>CNPJ</th><th>Telefone</th><th></th></tr>
                        </thead>
                        <tbody id="tabelaFornecedores"></tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- CESTA -->
        <div class="tab-pane fade" id="tabCesta">
            <p class="text-muted">Remova itens da sua cesta em tempo real.</p>
            <table class="table table-striped align-middle">
                <thead>
                    <tr><th>Produto</th><th>Preço</th><th></th></tr>
                </thead>
                <tbody id="tabelaCesta"></tbody>
                <tfoot>
                    <tr>
                        <th>Total</th>
                        <th id="totalCestaAjax">R$ 0,00</th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
<script src="assets/js/mascaras.js"></script>
<script src="assets/js/gerenciar.js"></script>
<?php require __DIR__ . '/includes/footer.php'; ?>
