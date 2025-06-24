<?php include 'includes/cabecalho.php'; ?>
<?php include 'includes/conexao.php'; ?>

<section class="banner">
    <div class="slideshow-container">
        <?php
        // Busca produtos em destaque para o banner
        $stmt = $pdo->query("SELECT * FROM produtos WHERE destaque = true LIMIT 3");
        $banners = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($banners as $banner) {
            echo '<div class="slide">';
            echo '<img src="images/' . htmlspecialchars($banner['imagem']) . '" alt="' . htmlspecialchars($banner['nome']) . '">';
            echo '<div class="slide-text">';
            echo '<h2>' . htmlspecialchars($banner['nome']) . '</h2>';
            echo '<p>A partir de R$ ' . number_format($banner['preco'], 2, ',', '.') . '</p>';
            echo '<a href="detalhe.php?id=' . $banner['id'] . '" class="btn">Ver Detalhes</a>';
            echo '</div></div>';
        }
        ?>
    </div>
</section>

<section class="destaques">
    <h2>Instrumentos em Destaque</h2>
    <div class="produtos-grid">
        <?php
        $stmt = $pdo->query("SELECT * FROM produtos ORDER BY data_cadastro DESC LIMIT 4");
        while ($produto = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo '<div class="produto-card">';
            echo '<img src="images/' . htmlspecialchars($produto['imagem']) . '" alt="' . htmlspecialchars($produto['nome']) . '">';
            echo '<h3>' . htmlspecialchars($produto['nome']) . '</h3>';
            echo '<p class="preco">R$ ' . number_format($produto['preco'], 2, ',', '.') . '</p>';
            echo '<a href="detalhe.php?id=' . $produto['id'] . '" class="btn">Ver Mais</a>';
            echo '</div>';
        }
        ?>
    </div>
</section>

<?php include 'includes/rodape.php'; ?>