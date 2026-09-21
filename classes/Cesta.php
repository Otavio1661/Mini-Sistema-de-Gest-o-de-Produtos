<?php

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/Produto.php';

class Cesta
{
    public int $id;
    public int $usuarioId;
    public string $status;

    private function __construct(int $id, int $usuarioId, string $status)
    {
        $this->id = $id;
        $this->usuarioId = $usuarioId;
        $this->status = $status;
    }

    /**
     * Garante que o usuário sempre tenha uma cesta "aberta" (cria se necessário).
     */
    public static function obterOuCriarAberta(int $usuarioId): self
    {
        $pdo = Database::getConnection();

        $stmt = $pdo->prepare("SELECT * FROM cestas WHERE usuario_id = :uid AND status = 'aberta' LIMIT 1");
        $stmt->execute([':uid' => $usuarioId]);
        $row = $stmt->fetch();

        if ($row) {
            return new self((int) $row['id'], (int) $row['usuario_id'], $row['status']);
        }

        $stmt = $pdo->prepare("INSERT INTO cestas (usuario_id, status) VALUES (:uid, 'aberta')");
        $stmt->execute([':uid' => $usuarioId]);

        return new self((int) $pdo->lastInsertId(), $usuarioId, 'aberta');
    }

    /**
     * Adiciona produtos (por id) à cesta, ignorando duplicados já existentes.
     *
     * @param int[] $produtoIds
     */
    public function adicionarProdutos(array $produtoIds): int
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare(
            'INSERT IGNORE INTO cesta_itens (cesta_id, produto_id) VALUES (:cesta_id, :produto_id)'
        );

        $adicionados = 0;
        foreach ($produtoIds as $produtoId) {
            $stmt->execute([
                ':cesta_id' => $this->id,
                ':produto_id' => (int) $produtoId,
            ]);
            $adicionados += $stmt->rowCount();
        }

        return $adicionados;
    }

    public function removerProduto(int $produtoId): bool
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('DELETE FROM cesta_itens WHERE cesta_id = :cesta_id AND produto_id = :produto_id');

        return $stmt->execute([':cesta_id' => $this->id, ':produto_id' => $produtoId]);
    }

    /**
     * Retorna os produtos da cesta (relacionamento N:N via cesta_itens).
     *
     * @return Produto[]
     */
    public function getItens(): array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare(
            'SELECT p.* FROM cesta_itens ci
             INNER JOIN produtos p ON p.id = ci.produto_id
             WHERE ci.cesta_id = :cesta_id
             ORDER BY p.nome ASC'
        );
        $stmt->execute([':cesta_id' => $this->id]);

        return array_map(fn (array $row) => Produto::fromArray($row), $stmt->fetchAll());
    }

    public function getTotal(): float
    {
        $total = 0.0;
        foreach ($this->getItens() as $produto) {
            $total += $produto->preco;
        }

        return $total;
    }

    public function getQuantidadeItens(): int
    {
        return count($this->getItens());
    }
}
