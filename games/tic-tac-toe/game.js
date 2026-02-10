/**
 * Tic-Tac-Toe 게임 로직
 */

let board = [];
let currentPlayer = 'X';
let gameOver = false;
let gameActive = true;
let difficulty = 'normal'; // easy, normal, hard
let playerScore = 0;
let cpuScore = 0;
let draws = 0;

const WIN_COMBINATIONS = [
    [0, 1, 2], [3, 4, 5], [6, 7, 8], // 가로
    [0, 3, 6], [1, 4, 7], [2, 5, 8], // 세로
    [0, 4, 8], [2, 4, 6]              // 대각선
];

function init() {
    createBoard();
    
    document.addEventListener('keydown', (e) => {
        if (e.key === ' ' || e.key === 'Enter') {
            if (gameOver) resetGame();
        }
    });
}

function createBoard() {
    const gameBoard = document.getElementById('game-board');
    gameBoard.innerHTML = '';
    board = Array(9).fill(null);
    
    for (let i = 0; i < 9; i++) {
        const cell = document.createElement('div');
        cell.className = 'cell';
        cell.dataset.index = i;
        cell.addEventListener('click', () => handleCellClick(i));
        gameBoard.appendChild(cell);
    }
}

function handleCellClick(index) {
    if (gameOver || !gameActive || board[index]) return;
    
    // 플레이어(X) 움직임
    makeMove(index, 'X');
    
    if (!gameOver && gameActive) {
        // CPU(O) 움직임 - 약간의 딜레이
        setTimeout(() => {
            cpuMove();
        }, 300);
    }
}

function makeMove(index, player) {
    board[index] = player;
    
    const cell = document.querySelector(`.cell[data-index="${index}"]`);
    cell.textContent = player === 'X' ? '❌' : '⭕';
    cell.classList.add(player.toLowerCase());
    
    // 진동
    if (navigator.vibrate) navigator.vibrate(20);
    
    // 승리 검사
    if (checkWin(player)) {
        gameOver = true;
        gameActive = false;
        
        if (player === 'X') {
            playerScore++;
            showMessage('🎉 승리! ❌');
        } else {
            cpuScore++;
            showMessage('😢 패배... ⭕');
        }
        
        highlightWinner(player);
        return;
    }
    
    // 무승부 검사
    if (!board.includes(null)) {
        gameOver = true;
        gameActive = false;
        draws++;
        showMessage('🤝 무승부!');
        return;
    }
    
    // 플레이어 전환
    currentPlayer = player === 'X' ? 'O' : 'X';
    updatePlayerInfo();
}

function cpuMove() {
    if (gameOver || !gameActive) return;
    
    let move;
    
    switch (difficulty) {
        case 'easy':
            move = getRandomMove();
            break;
        case 'hard':
            move = getBestMove();
            break;
        default: // normal
            move = Math.random() < 0.4 ? getBestMove() : getRandomMove();
    }
    
    if (move !== null) {
        makeMove(move, 'O');
    }
}

function getRandomMove() {
    const emptyCells = board.map((v, i) => v === null ? i : null).filter(v => v !== null);
    if (emptyCells.length === 0) return null;
    return emptyCells[Math.floor(Math.random() * emptyCells.length)];
}

function getBestMove() {
    // 먼저 승리 상황 확인
    for (let combo of WIN_COMBINATIONS) {
        const [a, b, c] = combo;
        if (board[a] === 'O' && board[b] === 'O' && board[c] === null) return c;
        if (board[a] === 'O' && board[c] === 'O' && board[b] === null) return b;
        if (board[b] === 'O' && board[c] === 'O' && board[a] === null) return a;
    }
    
    // 플레이어의 승리 차단
    for (let combo of WIN_COMBINATIONS) {
        const [a, b, c] = combo;
        if (board[a] === 'X' && board[b] === 'X' && board[c] === null) return c;
        if (board[a] === 'X' && board[c] === 'X' && board[b] === null) return b;
        if (board[b] === 'X' && board[c] === 'X' && board[a] === null) return a;
    }
    
    // 중앙 점유
    if (board[4] === null) return 4;
    
    // 모서리/가운데 선택
    const corners = [0, 2, 6, 8];
    const emptyCorners = corners.filter(i => board[i] === null);
    if (emptyCorners.length > 0) {
        return emptyCorners[Math.floor(Math.random() * emptyCorners.length)];
    }
    
    return getRandomMove();
}

function checkWin(player) {
    return WIN_COMBINATIONS.some(combo => {
        return combo.every(index => board[index] === player);
    });
}

function highlightWinner(player) {
    for (let combo of WIN_COMBINATIONS) {
        if (combo.every(index => board[index] === player)) {
            combo.forEach(index => {
                document.querySelector(`.cell[data-index="${index}"]`).classList.add('winner');
            });
            break;
        }
    }
}

function updatePlayerInfo() {
    const playerX = document.getElementById('playerX');
    const playerO = document.getElementById('playerO');
    
    if (currentPlayer === 'X') {
        playerX.classList.add('active');
        playerX.classList.remove('loser');
        playerO.classList.remove('active');
    } else {
        playerO.classList.add('active');
        playerX.classList.add('loser');
        playerO.classList.remove('loser');
    }
}

function showMessage(text) {
    document.getElementById('messageText').innerHTML = text + `<br><br>❌ ${playerScore} - ${draws} - ${cpuScore} ⭕`;
    document.getElementById('gameMessage').classList.add('show');
}

function resetGame() {
    gameOver = false;
    gameActive = true;
    currentPlayer = 'X';
    
    createBoard();
    updatePlayerInfo();
    document.getElementById('gameMessage').classList.remove('show');
    
    if (navigator.vibrate) navigator.vibrate(30);
}

function toggleDifficulty() {
    const diffs = ['easy', 'normal', 'hard'];
    const labels = { easy: '쉬움', normal: '보통', hard: '어려움' };
    
    const currentIndex = diffs.indexOf(difficulty);
    difficulty = diffs[(currentIndex + 1) % diffs.length];
    
    document.getElementById('diffLabel').textContent = labels[difficulty];
    
    // 점수 리셋
    playerScore = 0;
    cpuScore = 0;
    draws = 0;
    
    resetGame();
}

// 초기화 실행
init();
