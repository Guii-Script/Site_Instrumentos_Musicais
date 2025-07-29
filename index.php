<?php
// Arquivo: index.php (Tela de Listagem de Músicas)
require_once 'conexao.php';

// Busca todas as músicas, incluindo a nova imagem_capa
$stmt_all = $pdo->query("SELECT id, titulo, artista, imagem_capa FROM musicas ORDER BY titulo ASC");
$musicas = $stmt_all->fetchAll();

// As imagens para o banner continuam sendo definidas aqui
$banner_images = [
    'images/banner1.png',
    'images/banner2.jpg',
    'images/banner3.jpg',
];

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

        <?php if (!empty($banner_images)): ?>
        <div id="heroBanner" class="carousel slide carousel-fade mb-4" data-bs-ride="carousel">
            <div class="carousel-indicators">
                <?php foreach ($banner_images as $key => $image_path): ?>
                    <button type="button" data-bs-target="#heroBanner" data-bs-slide-to="<?php echo $key; ?>" class="<?php echo ($key == 0) ? 'active' : ''; ?>" aria-current="<?php echo ($key == 0) ? 'true' : 'false'; ?>"></button>
                <?php endforeach; ?>
            </div>

            <div class="carousel-inner">
                <?php foreach ($banner_images as $key => $image_path): ?>
                    <div class="carousel-item <?php echo ($key == 0) ? 'active' : ''; ?>">
                        <img src="<?php echo htmlspecialchars($image_path); ?>" class="d-block w-100 carousel-image-custom" alt="Banner Promocional <?php echo $key + 1; ?>">
                    </div>
                <?php endforeach; ?>
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
        <?php endif; ?>
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