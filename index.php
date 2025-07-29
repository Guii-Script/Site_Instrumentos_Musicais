<?php
// Arquivo: index.php (Tela de Listagem de Músicas)
require_once 'conexao.php';

// Esta parte continua igual, pois as músicas vêm do banco de dados.
$stmt_all = $pdo->query("SELECT id, titulo, artista, imagem_capa FROM musicas ORDER BY titulo ASC");
$musicas = $stmt_all->fetchAll();

// O array de imagens do banner foi REMOVIDO daqui.
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RockBand Hero - Escolha sua Música</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>RockBand Hero</h1>

        <div id="heroBanner" class="carousel slide carousel-fade mb-4" data-bs-ride="carousel">
            <div class="carousel-indicators">
                <button type="button" data-bs-target="#heroBanner" data-bs-slide-to="0" class="active" aria-current="true"></button>
                <button type="button" data-bs-target="#heroBanner" data-bs-slide-to="1"></button>
                <button type="button" data-bs-target="#heroBanner" data-bs-slide-to="2"></button>
            </div>

            <div class="carousel-inner">
                <div class="carousel-item active">
                    <img src="images/banner1.png" class="d-block w-100 carousel-image-custom" alt="Banner Promocional 1">
                </div>
                <div class="carousel-item">
                    <img src="images/banner2.jpg" class="d-block w-100 carousel-image-custom" alt="Banner Promocional 2">
                </div>
                <div class="carousel-item">
                    <img src="images/banner3.jpg" class="d-block w-100 carousel-image-custom" alt="Banner Promocional 3">
                </div>
            </div>

            <button class="carousel-control-prev" type="button" data-bs-target="#heroBanner" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#heroBanner" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>
        <h2>Escolha uma música para começar</h2>

        <?php if (count($musicas) > 0): ?>
            <ul class="song-list">
                <?php foreach ($musicas as $musica): ?>
                    <li class="song-item">
                        <img src="<?php echo htmlspecialchars($musica['imagem_capa']); ?>" alt="Capa de <?php echo htmlspecialchars($musica['titulo']); ?>" class="song-cover-thumbnail">
                        
                        <div class="song-info">
                            <strong><?php echo htmlspecialchars($musica['titulo']); ?></strong>
                            <span><?php echo htmlspecialchars($musica['artista']); ?></span>
                        </div>
                        <a href="detalhes.php?id=<?php echo $musica['id']; ?>">Ver Detalhes</a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p class="aviso">Nenhuma música encontrada no banco de dados.</p>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>