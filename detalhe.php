<?php include 'includes/cabecalho.php'; ?>
<?php include 'includes/conexao.php'; ?>

<?php
if (!isset($_GET['id'])) {
    header('Location: listagem.php');
    exit;
}

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM produtos WHERE id = ?");
$stmt->execute([$id]);
$produto = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$produto) {
    header('Location: listagem.php');
    exit;
}
?>

<section class="detalhe-produto">
    <div class="produto-imagem">
        <img src="images/<?= htmlspecialchars($produto['imagem']) ?>" alt="<?= htmlspecialchars($produto['nome']) ?>">
    </div>
    <div class="produto-info">
        <h1><?= htmlspecialchars($produto['nome']) ?></h1>
        <p class="marca-modelo"><?= htmlspecialchars($produto['marca']) ?> <?= htmlspecialchars($produto['modelo']) ?></p>
        <p class="preco">R$ <?= number_format($produto['preco'], 2, ',', '.') ?></p>
        
        <div class="estoque">
            <?php if ($produto['estoque'] > 0): ?>
                <p class="disponivel">Disponível (<?= $produto['estoque'] ?> em estoque)</p>
            <?php else: ?>
                <p class="indisponivel">Indisponível no momento</p>
            <?php endif; ?>
        </div>
        
        <div class="descricao">
            <h3>Descrição</h3>
            <p><?= nl2br(htmlspecialchars($produto['descricao'])) ?></p>
        </div>
        
        <div class="especificacoes">
            <h3>Especificações</h3>
            <ul>
                <li><strong>Categoria:</strong> <?= htmlspecialchars($produto['categoria']) ?></li>
                <li><strong>Marca:</strong> <?= htmlspecialchars($produto['marca']) ?></li>
                <li><strong>Modelo:</strong> <?= htmlspecialchars($produto['modelo']) ?></li>
            </ul>
        </div>
        
        <button class="btn-comprar">Adicionar ao Carrinho</button>
    </div>
</section>

<?php include 'includes/rodape.php'; ?>