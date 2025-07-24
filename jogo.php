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

// Pega a música selecionada
$id_musica = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id_musica) die("Música inválida!");

$stmt = $pdo->prepare("SELECT * FROM musicas WHERE id = ?");
$stmt->execute([$id_musica]);
$musica = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$musica) die("Música não encontrada!");
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Jogando: <?php echo htmlspecialchars($musica['titulo']); ?></title>
    <style>
        /* --- CSS ATUALIZADO COM EFEITOS --- */
        body { font-family: sans-serif; background-color: #282c34; color: #fff; text-align: center; }
        #game-board { width: 400px; height: 600px; border: 2px solid #61dafb; margin: auto; position: relative; overflow: hidden; background-color: #20232a; border-radius: 10px; }
        .note { 
            width: 98px; 
            height: 30px; 
            position: absolute; 
            border-radius: 5px; 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            font-size: 1.5em; 
            font-weight: bold; 
            color: #20232a;
            /* Transição suave para os efeitos */
            transition: all 0.2s ease-out;
        }
        .note-A { background-color: #69F0AE; left: 1px; }
        .note-S { background-color: #FF5252; left: 101px; }
        .note-D { background-color: #FFD740; left: 201px; }
        .note-F { background-color: #40C4FF; left: 301px; }
        #hit-zone { 
            position: absolute; 
            bottom: 50px; 
            width: 100%; 
            height: 40px; /* Área maior para referência visual */
            background: linear-gradient(to top, rgba(97, 218, 251, 0.3), rgba(97, 218, 251, 0));
            border-top: 2px solid #61dafb;
        }
        #game-stats { margin: 10px; font-size: 1.2em; }
        
        /* EFEITO DE ACERTO */
        .hit-effect {
            opacity: 0;
            transform: scale(1.5);
            background-color: white !important;
        }
        
        /* EFEITO DE ERRO (quando a nota passa direto) */
        .miss-effect {
            opacity: 0;
            transform: translateY(20px) scale(0.8);
        }
    </style>
</head>
<body>
    <h1><?php echo htmlspecialchars($musica['titulo']); ?></h1>
    <div id="game-stats">
        <span id="score">Pontos: 0</span> | <span id="combo">Combo: 0</span>
    </div>
    <div id="game-board">
        <div id="hit-zone"></div>
    </div>
    <p id="start-message">Pressione [ESPAÇO] para começar!</p>
    <audio id="game-audio" src="<?php echo htmlspecialchars($musica['arquivo_audio']); ?>"></audio>

    <script>
    // --- LÓGICA DO JOGO ATUALIZADA ---

    // 1. Pegar os elementos da página
    const audio = document.getElementById('game-audio');
    const gameBoard = document.getElementById('game-board');
    const scoreDisplay = document.getElementById('score');
    const comboDisplay = document.getElementById('combo');
    const startMessage = document.getElementById('start-message');
    
    // 2. Pegar os dados da música que o PHP enviou
    const musicaId = <?php echo $musica['id']; ?>;

    // 3. Variáveis para controlar o estado do jogo
    let score = 0;
    let combo = 0;
    let noteIndex = 0;
    let activeNotes = [];
    let gameRunning = false;
    const keyPositions = { 'A': 'note-A', 'S': 'note-S', 'D': 'note-D', 'F': 'note-F' };

    // --- NOVO: GERADOR DE MAPA DE NOTAS ---
    // Esta função cria um jogo longo e mais fácil dinamicamente.
    function createDynamicNoteMap() {
        const newMap = [];
        const duration = 60000; // Duração de 60 segundos
        const interval = 850;   // Uma nota a cada 850ms (mais fácil)
        const keys = ['A', 'S', 'D', 'F'];
        
        for (let time = 2000; time < duration; time += interval) {
            const randomKey = keys[Math.floor(Math.random() * keys.length)];
            newMap.push({ time: time, key: randomKey });
        }
        return newMap;
    }
    const noteMap = createDynamicNoteMap();

    // Inicia o jogo ao apertar Espaço
    document.body.onkeyup = function(e) {
        if (e.code === "Space" && !gameRunning) {
            gameRunning = true;
            startMessage.style.display = 'none';
            audio.play();
            requestAnimationFrame(gameLoop);
            audio.onended = endGame;
        }
    }

    // O loop que roda a cada frame do jogo
    function gameLoop() {
        if (!gameRunning) return;

        const elapsedTime = audio.currentTime * 1000;

        // Cria novas notas
        while (noteIndex < noteMap.length && noteMap[noteIndex].time <= elapsedTime + 3000) {
            createNote(noteMap[noteIndex]);
            noteIndex++;
        }

        // Move as notas e checa as que foram perdidas
        for (let i = activeNotes.length - 1; i >= 0; i--) {
            const note = activeNotes[i];
            const noteTime = note.dataset.time;
            const fallDuration = 3000;
            const progress = (elapsedTime - (noteTime - fallDuration)) / fallDuration;
            note.style.top = (progress * 600) + 'px';

            // NOVO: EFEITO DE ERRO se a nota passar da tela
            if (parseFloat(note.style.top) > 600) {
                note.classList.add('miss-effect');
                setTimeout(() => note.remove(), 200); // Remove após o efeito

                activeNotes.splice(i, 1);
                combo = 0;
                comboDisplay.textContent = 'Combo: ' + combo;
            }
        }
        
        requestAnimationFrame(gameLoop);
    }

    function createNote(noteData) {
        const noteEl = document.createElement('div');
        noteEl.className = 'note ' + keyPositions[noteData.key];
        noteEl.dataset.time = noteData.time;
        noteEl.dataset.key = noteData.key;
        noteEl.textContent = noteData.key;
        gameBoard.appendChild(noteEl);
        activeNotes.push(noteEl);
    }

    // Função que escuta as teclas pressionadas
    document.addEventListener('keydown', (e) => {
        if (!gameRunning) return;
        const pressedKey = e.key.toUpperCase();

        // LÓGICA DE ACERTO ATUALIZADA
        const hitZoneCenter = 550; // O "ponto perfeito" em pixels (baseado na altura de 600px do game-board)
        const hitWindow = 50;      // A distância em pixels da nota para o centro para contar como acerto

        for (let i = 0; i < activeNotes.length; i++) {
            const note = activeNotes[i];
            const notePosition = parseFloat(note.style.top);
            
            // Se a tecla é a certa E a nota está na zona de acerto
            if (note.dataset.key === pressedKey && Math.abs(notePosition - hitZoneCenter) < hitWindow) {
                // ACERTOU!
                score += 10;
                combo++;
                scoreDisplay.textContent = 'Pontos: ' + score;
                comboDisplay.textContent = 'Combo: ' + combo;

                // NOVO: Adiciona o efeito de acerto e remove a nota
                note.classList.add('hit-effect');
                setTimeout(() => note.remove(), 200);

                activeNotes.splice(i, 1);
                return; // Acertou, não precisa checar outras notas
            }
        }
        
        // NOVO: Penalidade por apertar a tecla errada (efeito visual vermelho na zona de acerto)
        if (['A', 'S', 'D', 'F'].includes(pressedKey)) {
             const hitZoneEl = document.getElementById('hit-zone');
             hitZoneEl.style.backgroundColor = 'rgba(255, 0, 0, 0.5)';
             setTimeout(() => {
                hitZoneEl.style.backgroundColor = 'transparent'; // Volta ao normal
             }, 100);
        }
    });

    function endGame() {
        gameRunning = false;
        alert(`Fim de Jogo! Sua pontuação final: ${score}`);
        const playerName = prompt("Digite seu nome para o ranking:", "Player1");
        if (playerName) {
            fetch('salvar_pontuacao.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ nome: playerName, pontos: score, musica_id: musicaId })
            }).then(() => {
                window.location.href = `ranking.php?id=${musicaId}`;
            });
        } else {
             window.location.href = `index.php`;
        }
    }
    </script>
</body>
</html>