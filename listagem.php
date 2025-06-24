<?php include 'includes/cabecalho.php'; ?>
<?php include 'includes/conexao.php'; ?>

<section class="listagem">
    <h2>Todos os Produtos</h2>
    
    <div class="filtros">
        <form method="get">
            <select name="categoria">
                <option value="">Todas Categorias</option>
                <?php
                $stmt = $pdo->query("SELECT DISTINCT categoria FROM produtos WHERE categoria IS NOT NULL");
                while ($categoria = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $selected = (isset($_GET['categoria']) && $_GET['categoria'] == $categoria['categoria']) ? 'selected' : '';
                    echo '<option value="' . htmlspecialchars($categoria['categoria']) . '" ' . $selected . '>' . 
                         htmlspecialchars($categoria['categoria']) . '</option>';
                }
                ?>
            </select>
            <input type="submit" value="Filtrar">
        </form>
    </div>
    
    <div class="produtos-grid">
        <?php
        $sql = "SELECT * FROM produtos";
        if (isset($_GET['categoria']) && !empty($_GET['categoria'])) {
            $sql .= " WHERE categoria = :categoria";
        }
        $sql .= " ORDER BY nome";
        
        $stmt = $pdo->prepare($sql);
        if (isset($_GET['categoria']) && !empty($_GET['categoria'])) {
            $stmt->bindValue(':categoria', $_GET['categoria']);
        }
        $stmt->execute();
        
        while ($produto = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo '<div class="produto-card">';
            echo '<img src="images/' . htmlspecialchars($produto['imagem']) . '" alt="' . htmlspecialchars($produto['nome']) . '">';
            echo '<h3>' . htmlspecialchars($produto['nome']) . '</h3>';
            echo '<p class="preco">R$ ' . number_format($produto['preco'], 2, ',', '.') . '</p>';
            echo '<a href="detalhe.php?id=' . $produto['id'] . '" class="btn">Ver Detalhes</a>';
            echo '</div>';
        }
        ?>
    </div>
</section>

<?php include 'includes/rodape.php'; ?>