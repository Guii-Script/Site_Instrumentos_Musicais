<?php

$host = 'localhost';
$db   = 'rockband_db';
$user = 'root'; // Usuário padrão do XAMPP/WAMP
$pass = '';     // Senha padrão do XAMPP/WAMP
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

$options = [
    // Lança exceções em caso de erros, para um controle mais robusto.
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    // Define o modo de busca padrão para arrays associativos.
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    // Garante o uso de prepared statements nativos do driver do banco.
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
     $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
     // Em um ambiente real, logue o erro em um arquivo em vez de exibi-lo na tela.
     error_log($e->getMessage());
     die("Ocorreu um erro ao conectar com o banco de dados. Tente novamente mais tarde.");
}
?>