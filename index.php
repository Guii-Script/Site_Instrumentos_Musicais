<?php
// Arquivo: index.php (Tela de Listagem de Músicas)
require_once 'conexao.php';

// Busca todas as músicas cadastradas para exibi-las na lista.
$stmt = $pdo->query("SELECT id, titulo, artista FROM musicas ORDER BY titulo ASC");
$musicas = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>RockBand Hero - Escolha sua Música</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>RockBand Hero</h1>
        <h2>Escolha uma música para começar</h2>

        <?php if (count($musicas) > 0): ?>
            <ul class="song-list">
                <?php foreach ($musicas as $musica): ?>
                    <li class="song-item">
                        <div class="song-info">
                            <strong><?php echo htmlspecialchars($musica['titulo']); ?></strong>
                            <span><?php echo htmlspecialchars($musica['artista']); ?></span>
                        </div>
                        <a href="detalhes.php?id=<?php echo $musica['id']; ?>">Ver Detalhes</a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p class="aviso">Nenhuma música encontrada no banco de dados. Adicione algumas para começar!</p>
        <?php endif; ?>
    </div>
</body>
</html>