<?php

require_once 'conexao.php';

// 2. Pega o ID da música que foi enviado pela URL.
// Por exemplo, em "jogo.php?id=3", o ID é 3.
// Usamos filter_input para garantir que estamos recebendo um número inteiro, é mais seguro!
$id_musica = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

// 3. Se o ID não for um número válido, não podemos continuar.
// Então, redirecionamos o usuário de volta para a página inicial.
if (!$id_musica) {
    header("Location: index.php");
    exit; // 'exit' para o script imediatamente.
}

// 4. Prepara e executa uma consulta SQL para buscar a música com o ID que recebemos.
// Usar '?' (prepared statements) protege nosso banco contra ataques de SQL Injection.
$stmt = $pdo->prepare("SELECT * FROM tb_musicas WHERE id = ?");
$stmt->execute([$id_musica]);

// 5. Pega os dados da música e guarda na variável $musica.
$musica = $stmt->fetch();

// 6. Se a consulta não retornar nenhuma música (ou seja, não existe música com esse ID),
// também redirecionamos para a página inicial.
if (!$musica) {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Jogando: <?php echo htmlspecialchars($musica['titulo']); ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div id="game-container">
        <a href="detalhes.php?id=<?php echo htmlspecialchars($musica['id']); ?>" class="btn-back-game">&larr; Voltar</a>

        <h1><?php echo htmlspecialchars($musica['titulo']); ?></h1>

        <div id="game-stats">
            <span id="score">Pontos: 0</span>
            <span id="combo">Combo: 0</span>
        </div>

        <div id="game-board">
            <div id="hit-zone"></div>
        </div>

        <p id="start-message">Pressione [ESPAÇO] para começar!</p>

        <audio id="game-audio" src="<?php echo htmlspecialchars($musica['arquivo_audio']); ?>"></audio>
    </div>

    <script>
    // Agora começa a parte mais divertida: a lógica do jogo em si!

    // --- Seção A: Preparando as Ferramentas ---

    // Pegamos os elementos HTML que vamos precisar manipular durante o jogo.
    // É como pegar as peças de um quebra-cabeça antes de começar a montar.
    const audio = document.getElementById('game-audio');
    const gameBoard = document.getElementById('game-board');
    const scoreDisplay = document.getElementById('score');
    const comboDisplay = document.getElementById('combo');
    const startMessage = document.getElementById('start-message');

    // Pegamos os dados da música que o PHP nos enviou.
    // O PHP "imprime" o valor diretamente no nosso código JavaScript.
    const musicaId = <?php echo $musica['id']; ?>;
    const noteMap = <?php echo $musica['mapa_notas']; ?>; // Este é o mapa de notas vindo do banco!

    // Variáveis que vão controlar o estado do nosso jogo.
    let score = 0;              // Guarda a pontuação atual.
    let combo = 0;              // Guarda a sequência de acertos.
    let noteIndex = 0;          // Controla qual a próxima nota a ser criada do `noteMap`.
    let activeNotes = [];       // Uma lista para guardar as notas que estão visíveis na tela.
    let gameRunning = false;    // Diz se o jogo está rolando ou não.
    
    // Um "mapa" para associar cada tecla (A, S, D, F) a um estilo CSS.
    const keyPositions = { 'A': 'note-A', 'S': 'note-S', 'D': 'note-D', 'F': 'note-F' };


    // --- Seção B: O Início e o Fim do Jogo ---

    // Fica "escutando" o teclado. Quando o jogador aperta e solta uma tecla...
    document.body.onkeyup = function(e) {
        // ...verificamos se foi a barra de espaço e se o jogo ainda não começou.
        if (e.code === "Space" && !gameRunning) {
            startGame(); // Se sim, chamamos a função para começar o jogo!
        }
    }

    // Função que realmente começa o jogo.
    function startGame() {
        gameRunning = true; // Avisa o resto do código que o jogo começou.
        startMessage.style.display = 'none'; // Esconde a mensagem "Pressione Espaço".
        audio.play(); // Toca a música.
        
        // Inicia o "game loop", a função que vai rodar repetidamente.
        requestAnimationFrame(gameLoop);
        
        // Define o que acontece quando a música acabar.
        audio.onended = endGame;
    }

    // Função que é chamada quando a música termina.
    function endGame() {
        gameRunning = false; // Avisa que o jogo acabou.
        alert(`Fim de Jogo!\nSua pontuação final foi: ${score}`);
        
        // Leva o jogador de volta para a tela de detalhes da música.
        window.location.href = `detalhes.php?id=${musicaId}`;
    }


    // --- Seção C: O Coração do Jogo (O Game Loop) ---

    // Esta é a função mais importante! Ela é chamada dezenas de vezes por segundo.
    // `requestAnimationFrame` é a forma moderna e eficiente de criar animações no navegador.
    function gameLoop() {
        // Se o jogo não estiver rodando (ex: pausado ou finalizado), a gente para o loop.
        if (!gameRunning) return;

        // Pega o tempo atual da música em milissegundos.
        const elapsedTime = audio.currentTime * 1000;

        // **Lógica para criar novas notas:**
        // Verificamos se já está na hora de criar a próxima nota do nosso mapa (`noteMap`).
        // O `+ 3000` faz com que a nota seja criada 3 segundos ANTES de chegar na zona de acerto.
        while (noteIndex < noteMap.length && noteMap[noteIndex].time <= elapsedTime + 3000) {
            createNoteElement(noteMap[noteIndex]);
            noteIndex++; // Avança para a próxima nota do mapa.
        }

        // **Lógica para mover as notas que já estão na tela:**
        // Passamos por cada nota na lista `activeNotes`.
        activeNotes.forEach((note, index) => {
            const noteTime = note.dataset.time;
            const fallDuration = 3000; // O tempo em milissegundos que a nota leva para cair.

            // Calcula o progresso da queda da nota (de 0 a 1).
            const progress = (elapsedTime - (noteTime - fallDuration)) / fallDuration;
            
            // Atualiza a posição 'top' da nota na tela.
            // A posição é baseada no progresso da queda e na altura da pista.
            note.style.top = (progress * (gameBoard.clientHeight - 30)) + 'px';

            // Se a nota passou do fim da tela, o jogador errou.
            if (parseFloat(note.style.top) > gameBoard.clientHeight) {
                handleMiss(note, index);
            }
        });
        
        // Pede ao navegador para chamar a função `gameLoop` de novo no próximo "quadro".
        // É isso que cria o movimento contínuo.
        requestAnimationFrame(gameLoop);
    }


    // --- Seção D: Funções de Ação e Reação ---

    // Função que cria o elemento HTML de uma nota e o coloca na tela.
    function createNoteElement(noteData) {
        const noteEl = document.createElement('div'); // Cria uma <div>
        noteEl.className = 'note ' + keyPositions[noteData.key]; // Define a classe CSS dela
        
        // Guarda informações importantes na própria nota usando `dataset`.
        noteEl.dataset.time = noteData.time;
        noteEl.dataset.key = noteData.key;
        
        noteEl.textContent = noteData.key; // Escreve a letra dentro da nota.
        
        gameBoard.appendChild(noteEl); // Adiciona a nota na pista.
        activeNotes.push(noteEl);      // Adiciona a nota na nossa lista de notas ativas.
    }

    // Fica "escutando" as teclas que o jogador pressiona.
    document.addEventListener('keydown', (e) => {
        if (!gameRunning) return; // Se o jogo não começou, não faz nada.

        const pressedKey = e.key.toUpperCase(); // Pega a tecla pressionada (ex: 'a' vira 'A').
        
        // Ignora qualquer tecla que não seja A, S, D ou F.
        if (!['A', 'S', 'D', 'F'].includes(pressedKey)) return;

        const hitZoneCenterY = gameBoard.clientHeight - 50; // Ponto ideal do acerto.
        const hitWindow = 50; // A "margem de erro" para cima e para baixo.
        
        let hit = false; // Variável para saber se o jogador acertou alguma nota.

        // Procura a nota certa para acertar.
        // Contamos de trás para frente porque podemos remover uma nota da lista.
        // Se contássemos para frente, poderíamos pular a próxima nota sem querer ao remover uma!
        for (let i = activeNotes.length - 1; i >= 0; i--) {
            const note = activeNotes[i];
            const notePosition = parseFloat(note.style.top) + (note.clientHeight / 2);

            // Verifica se a tecla pressionada é a mesma da nota E se a nota está na zona de acerto.
            if (note.dataset.key === pressedKey && Math.abs(notePosition - hitZoneCenterY) < hitWindow) {
                handleHit(note, i); // Se sim, chama a função de acerto.
                hit = true;
                break; // Acertou, então não precisa procurar mais.
            }
        }
    });
    
    // O que acontece quando o jogador ACERTA.
    function handleHit(note, index) {
        score += (10 + combo); // Aumenta a pontuação (com bônus de combo).
        combo++;               // Aumenta o combo.
        updateDisplays();      // Atualiza os textos de score e combo na tela.

        note.classList.add('hit-effect');        // Adiciona um efeito visual de acerto.
        setTimeout(() => note.remove(), 200);    // Remove a nota da tela após o efeito.
        activeNotes.splice(index, 1);            // Remove a nota da nossa lista de notas ativas.
    }

    // O que acontece quando o jogador ERRA.
    function handleMiss(note, index) {
        combo = 0;             // Zera o combo.
        updateDisplays();      // Atualiza a tela.

        note.classList.add('miss-effect');       // Adiciona um efeito visual de erro.
        setTimeout(() => note.remove(), 200);    // Remove a nota da tela após o efeito.
        activeNotes.splice(index, 1);            // Remove a nota da lista.
    }
    
    // Uma pequena função para manter a tela sempre atualizada com o score e combo.
    function updateDisplays() {
        scoreDisplay.textContent = 'Pontos: ' + score;
        comboDisplay.textContent = 'Combo: ' + combo;
    }

    </script>
</body>
</html>