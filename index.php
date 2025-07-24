<?php
// --- Conexão com o Banco de Dados (integrada) ---
$host = 'localhost';
$db   = 'rockband_db';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
try {
     $pdo = new PDO($dsn, $user, $pass);
} catch (\PDOException $e) {
     die("Erro ao conectar ao banco de dados: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>RockBand Hero - Simplificado</title>
    <style>
        /* --- CSS Básico (integrado) --- */
        body { font-family: sans-serif; background-color: #333; color: #fff; text-align: center; }
        .container { max-width: 800px; margin: auto; background-color: #444; padding: 20px; border-radius: 8px; }
        h1 { color: #ffc107; }
        .song-list { list-style: none; padding: 0; }
        .song-item { background: #555; margin: 10px 0; padding: 15px; display: flex; justify-content: space-between; align-items: center; }
        .song-item a { text-decoration: none; background-color: #00bcd4; color: #fff; padding: 10px 15px; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>RockBand Hero</h1>
        <h2>Escolha uma música para tocar!</h2>
        <ul class="song-list">
            <?php
            // Busca e exibe as músicas
            $stmt = $pdo->query("SELECT id, titulo, artista FROM musicas");
            while ($musica = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<li class='song-item'>";
                echo "<span><strong>" . htmlspecialchars($musica['titulo']) . "</strong> - " . htmlspecialchars($musica['artista']) . "</span>";
                echo "<a href='jogo.php?id=" . $musica['id'] . "'>Tocar!</a>";
                echo "</li>";
            }
            ?>
        </ul>
    </div>
</body>
</html>