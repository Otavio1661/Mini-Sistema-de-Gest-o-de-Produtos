<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../classes/Auth.php';
require_once __DIR__ . '/../classes/Cesta.php';

Auth::iniciarSessao();

if (!Auth::usuarioLogado()) {
    http_response_code(401);
    echo json_encode(['erro' => 'Não autenticado.']);
    exit;
}

$usuarioId = Auth::usuarioId();
$cesta = Cesta::obterOuCriarAberta($usuarioId);
$metodo = $_SERVER['REQUEST_METHOD'];

try {
    if ($metodo === 'GET') {
        $itens = array_map(
            fn ($p) => [
                'id' => $p->id,
                'nome' => $p->nome,
                'preco' => $p->preco,
            ],
            $cesta->getItens()
        );

        echo json_encode([
            'dados' => $itens,
            'total' => $cesta->getTotal(),
            'quantidade' => $cesta->getQuantidadeItens(),
        ]);
        exit;
    }

    $entrada = json_decode(file_get_contents('php://input'), true) ?? [];
    $acao = $entrada['acao'] ?? '';

    if ($acao === 'adicionar') {
        $produtoIds = array_map('intval', $entrada['produto_ids'] ?? []);

        if (!$produtoIds) {
            http_response_code(422);
            echo json_encode(['erro' => 'Selecione ao menos um produto.']);
            exit;
        }

        $cesta->adicionarProdutos($produtoIds);
        echo json_encode([
            'sucesso' => true,
            'mensagem' => 'Produtos adicionados à cesta.',
            'quantidade' => $cesta->getQuantidadeItens(),
            'total' => $cesta->getTotal(),
        ]);
        exit;
    }

    if ($acao === 'remover') {
        $cesta->removerProduto((int) ($entrada['produto_id'] ?? 0));
        echo json_encode([
            'sucesso' => true,
            'mensagem' => 'Produto removido da cesta.',
            'quantidade' => $cesta->getQuantidadeItens(),
            'total' => $cesta->getTotal(),
        ]);
        exit;
    }

    http_response_code(400);
    echo json_encode(['erro' => 'Ação inválida.']);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['erro' => 'Erro ao processar: ' . $e->getMessage()]);
}
