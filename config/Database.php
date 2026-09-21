<?php

class Database
{
    private static ?PDO $connection = null;

    private const HOST = '127.0.0.1';
    private const PORT = '3306';
    private const DB_NAME = 'gestao_produtos';
    private const USER = 'root';
    private const PASS = '';

    public static function getConnection(): PDO
    {
        if (self::$connection === null) {
            self::createDatabaseIfNotExists();

            $dsn = 'mysql:host=' . self::HOST . ';port=' . self::PORT . ';dbname=' . self::DB_NAME . ';charset=utf8mb4';
            self::$connection = new PDO($dsn, self::USER, self::PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);

            self::createTablesIfNotExist(self::$connection);
        }

        return self::$connection;
    }

    private static function createDatabaseIfNotExists(): void
    {
        $dsn = 'mysql:host=' . self::HOST . ';port=' . self::PORT . ';charset=utf8mb4';
        $pdo = new PDO($dsn, self::USER, self::PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ]);

        $pdo->exec('CREATE DATABASE IF NOT EXISTS `' . self::DB_NAME . '` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
    }

    private static function createTablesIfNotExist(PDO $pdo): void
    {
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS usuarios (
                id INT AUTO_INCREMENT PRIMARY KEY,
                nome VARCHAR(120) NOT NULL,
                email VARCHAR(150) NOT NULL UNIQUE,
                senha_hash CHAR(64) NOT NULL,
                criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");

        $pdo->exec("
            CREATE TABLE IF NOT EXISTS fornecedores (
                id INT AUTO_INCREMENT PRIMARY KEY,
                nome VARCHAR(150) NOT NULL,
                cnpj VARCHAR(18) NOT NULL,
                telefone VARCHAR(20) NULL,
                email VARCHAR(150) NULL,
                endereco VARCHAR(255) NULL,
                criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");

        $pdo->exec("
            CREATE TABLE IF NOT EXISTS produtos (
                id INT AUTO_INCREMENT PRIMARY KEY,
                nome VARCHAR(150) NOT NULL,
                descricao VARCHAR(255) NULL,
                preco DECIMAL(10,2) NOT NULL DEFAULT 0,
                quantidade_estoque INT NOT NULL DEFAULT 0,
                fornecedor_id INT NOT NULL,
                criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                CONSTRAINT fk_produto_fornecedor FOREIGN KEY (fornecedor_id)
                    REFERENCES fornecedores(id) ON DELETE RESTRICT ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");

        $pdo->exec("
            CREATE TABLE IF NOT EXISTS cestas (
                id INT AUTO_INCREMENT PRIMARY KEY,
                usuario_id INT NOT NULL,
                status ENUM('aberta','finalizada') NOT NULL DEFAULT 'aberta',
                criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                CONSTRAINT fk_cesta_usuario FOREIGN KEY (usuario_id)
                    REFERENCES usuarios(id) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");

        $pdo->exec("
            CREATE TABLE IF NOT EXISTS cesta_itens (
                id INT AUTO_INCREMENT PRIMARY KEY,
                cesta_id INT NOT NULL,
                produto_id INT NOT NULL,
                criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                CONSTRAINT fk_item_cesta FOREIGN KEY (cesta_id)
                    REFERENCES cestas(id) ON DELETE CASCADE ON UPDATE CASCADE,
                CONSTRAINT fk_item_produto FOREIGN KEY (produto_id)
                    REFERENCES produtos(id) ON DELETE CASCADE ON UPDATE CASCADE,
                UNIQUE KEY uk_cesta_produto (cesta_id, produto_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
    }
}
