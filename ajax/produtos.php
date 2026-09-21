<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../classes/Auth.php';
require_once __DIR__ . '/../classes/Produto.php';
require_once __DIR__ . '/../classes/Fornecedor.php';

Auth::iniciarSessao();

if (!Auth::usuarioLogado()) {
    http_response_code(401);
    echo json_encode(['erro' => 'Não autenticado.']);
    exit;
}

$metodo = $_SERVER['REQUEST_METHOD'];

try {
    if ($metodo === 'GET') {
        $produtos = Produto::listarComFornecedor();
        echo json_encode(['dados' => $produtos]);
        exit;
    }

    $entrada = json_decode(file_get_contents('php://input'), true) ?? [];
    $acao = $entrada['acao'] ?? '';

    if ($acao === 'salvar') {
        $nome = trim($entrada['nome'] ?? '');
        $descricao = trim($entrada['descricao'] ?? '') ?: null;
        $preco = (float) ($entrada['preco'] ?? 0);
        $quantidade = (int) ($entrada['quantidade_estoque'] ?? 0);
        $fornecedorId = (int) ($entrada['fornecedor_id'] ?? 0);
        $id = !empty($entrada['id']) ? (int) $entrada['id'] : null;

        if ($nome === '' || $fornecedorId <= 0) {
            http_response_code(422);
            echo json_encode(['erro' => 'Nome e fornecedor são obrigatórios.']);
            exit;
        }

        $produto = new Produto($nome, $descricao, $preco, $quantidade, $fornecedorId, $id);
        $produto->salvar();

        echo json_encode(['sucesso' => true, 'mensagem' => 'Produto salvo com sucesso.', 'id' => $produto->id]);
        exit;
    }

    if ($acao === 'excluir') {
        Produto::excluir((int) ($entrada['id'] ?? 0));
        echo json_encode(['sucesso' => true, 'mensagem' => 'Produto excluído com sucesso.']);
        exit;
    }

    http_response_code(400);
    echo json_encode(['erro' => 'Ação inválida.']);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['erro' => 'Erro ao processar: ' . $e->getMessage()]);
}
