<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../classes/Auth.php';
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
        $fornecedores = array_map(
            fn (Fornecedor $f) => [
                'id' => $f->id,
                'nome' => $f->nome,
                'cnpj' => $f->cnpj,
                'telefone' => $f->telefone,
                'email' => $f->email,
                'endereco' => $f->endereco,
            ],
            Fornecedor::listarTodos()
        );
        echo json_encode(['dados' => $fornecedores]);
        exit;
    }

    $entrada = json_decode(file_get_contents('php://input'), true) ?? [];
    $acao = $entrada['acao'] ?? '';

    if ($acao === 'salvar') {
        $nome = trim($entrada['nome'] ?? '');
        $cnpj = trim($entrada['cnpj'] ?? '');
        $telefone = trim($entrada['telefone'] ?? '') ?: null;
        $email = trim($entrada['email'] ?? '') ?: null;
        $endereco = trim($entrada['endereco'] ?? '') ?: null;
        $id = !empty($entrada['id']) ? (int) $entrada['id'] : null;

        if ($nome === '' || $cnpj === '') {
            http_response_code(422);
            echo json_encode(['erro' => 'Nome e CNPJ são obrigatórios.']);
            exit;
        }

        if (!Fornecedor::cnpjValido($cnpj)) {
            http_response_code(422);
            echo json_encode(['erro' => 'CNPJ inválido. Informe os 14 dígitos do CNPJ.']);
            exit;
        }

        $fornecedor = new Fornecedor($nome, $cnpj, $telefone, $email, $endereco, $id);
        $fornecedor->salvar();

        echo json_encode(['sucesso' => true, 'mensagem' => 'Fornecedor salvo com sucesso.', 'id' => $fornecedor->id]);
        exit;
    }

    if ($acao === 'excluir') {
        $idExcluir = (int) ($entrada['id'] ?? 0);

        if (Fornecedor::possuiProdutos($idExcluir)) {
            http_response_code(409);
            echo json_encode(['erro' => 'Não é possível excluir: existem produtos cadastrados para este fornecedor.']);
            exit;
        }

        Fornecedor::excluir($idExcluir);
        echo json_encode(['sucesso' => true, 'mensagem' => 'Fornecedor excluído com sucesso.']);
        exit;
    }

    http_response_code(400);
    echo json_encode(['erro' => 'Ação inválida.']);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['erro' => 'Erro ao processar: ' . $e->getMessage()]);
}
