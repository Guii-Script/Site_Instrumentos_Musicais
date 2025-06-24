<?php
// includes/conexao.php

// Configurações do banco de dados
$host = '127.0.0.1'; // ou 'localhost'
$dbname = 'bd_instrumentos';
$username = 'root'; // substitua pelo seu usuário do MySQL
$password = ''; // substitua pela sua senha do MySQL

try {
    // Cria a conexão PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    
    // Configura o PDO para lançar exceções em caso de erros
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Configura o PDO para retornar arrays associativos por padrão
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
    // Desativa emulações de prepared statements para segurança
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    
    // Mensagem de sucesso (apenas para desenvolvimento)
    // echo "Conexão com o banco de dados estabelecida com sucesso!";
    
} catch(PDOException $e) {
    // Em caso de erro, exibe a mensagem e interrompe a execução
    die("Erro na conexão com o banco de dados: " . $e->getMessage());
}
?>