<?php

function inicializarBancoDados(): PDO {
    $host = 'localhost';
    $user = 'root';
    $pass = '';
    $dbname = 'the_farmer_edu';

    try {
        // Tenta conectar ao banco existente
        $pdo = new PDO(
            "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
            $user,
            $pass,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]
        );
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), '1049') !== false) {
            // Banco não existe, cria ele
            $pdo = new PDO(
                "mysql:host=$host;charset=utf8mb4",
                $user,
                $pass,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );

            // Cria o banco
            $pdo->exec("CREATE DATABASE IF NOT EXISTS $dbname CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            $pdo->exec("USE $dbname");
        } else {
            throw $e;
        }
    }
    
    // Verifica se a tabela usuarios já existe
    try {
        $stmt = $pdo->query("SELECT 1 FROM usuarios LIMIT 1");
        $stmt->fetch();
        return $pdo; // Tabela já existe
    } catch (PDOException $e) {
        // Tabela não existe, precisa criar
    }

    // Lê e executa o schema.sql
    $schemaPath = __DIR__ . '/../../database/schema.sql';
    if (!file_exists($schemaPath)) {
        throw new Exception("schema.sql não encontrado em: $schemaPath");
    }

    $sql = file_get_contents($schemaPath);
    
    // Remove a criação do banco do schema se existir
    $sql = preg_replace('/CREATE DATABASE.*?;/is', '', $sql);
    $sql = preg_replace('/USE\s+the_farmer_edu;/i', '', $sql);
    
    $statements = array_filter(array_map('trim', explode(';', $sql)));

    foreach ($statements as $statement) {
        if (!empty($statement)) {
            try {
                $pdo->exec($statement);
            } catch (PDOException $e) {
                // Ignora erros de "tabela já existe"
                if (strpos($e->getMessage(), '1050') === false) {
                    throw $e;
                }
            }
        }
    }

    return $pdo;
}

