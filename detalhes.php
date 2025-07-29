<?php
// Arquivo: detalhes.php (Tela de Detalhes da Fase)
require_once 'conexao.php';

// Valida o ID da música recebido pela URL. É uma medida de segurança.
$id_musica = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id_musica) {
    header("Location: index.php"); // Se o ID for inválido, volta ao menu
    exit;
}

// Busca os detalhes completos da música selecionada no banco.
$stmt = $pdo->prepare("SELECT * FROM musicas WHERE id = ?");
$stmt->execute([$id_musica]);
$musica = $stmt->fetch();

// Se não encontrou uma música com aquele ID, volta ao menu.
if (!$musica) {
    header("Location: index.php");
    exit;
}

// Decodifica o mapa de notas (que está em JSON) para um array PHP.
$mapa_notas = json_decode($musica['mapa_notas'], true);
$total_notas = is_array($mapa_notas) ? count($mapa_notas) : 0;
// Calcula a pontuação máxima. Assumindo que cada nota vale 10 pontos.
$pontuacao_maxima = $total_notas * 10;

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Detalhes: <?php echo htmlspecialchars($musica['titulo']); ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1><?php echo htmlspecialchars($musica['titulo']); ?></h1>
        <h2 class="subtitulo-artista">por <?php echo htmlspecialchars($musica['artista']); ?></h2>

        <div class="details-card">
            <h3>Informações da Fase</h3>
            <p><strong>Total de Notas a Acertar:</strong> <?php echo $total_notas; ?></p>
            <p><strong>Pontuação Máxima Possível:</strong> <?php echo $pontuacao_maxima; ?> pontos</p>
        </div>

        <div class="actions">
            <a href="jogo.php?id=<?php echo $musica['id']; ?>" class="btn-play">Tocar Agora!</a>
            <a href="index.php" class="btn-back">Voltar ao Menu</a>
        </div>
    </div>
</body>
</html>