/**
 * Tetris 게임 로직
 */

// 보드 설정
const COLS = 10;
const ROWS = 20;
const CELL_SIZE = 28;

// 테트로미노 정의 (I, J, L, O, S, T, Z)
const TETROMINOES = {
    I: {
        shape: [[0,0,0,0], [1,1,1,1], [0,0,0,0], [0,0,0,0]],
        color: 'I'
    },
    O: {
        shape: [[1,1], [1,1]],
        color: 'O'
    },
    T: {
        shape: [[0,1,0], [1,1,1], [0,0,0]],
        color: 'T'
    },
    S: {
        shape: [[0,1,1], [1,1,0], [0,0,0]],
        color: 'S'
    },
    Z: {
        shape: [[1,1,0], [0,1,1], [0,0,0]],
        color: 'Z'
    },
    J: {
        shape: [[1,0,0], [1,1,1], [0,0,0]],
        color: 'J'
    },
    L: {
        shape: [[0,0,1], [1,1,1], [0,0,0]],
        color: 'L'
    }
};

const PIECE_NAMES = ['I', 'O', 'T', 'S', 'Z', 'J', 'L'];

// 게임 상태
let board = [];
let currentPiece = null;
let nextPiece = null;
let score = 0;
let level = 1;
let lines = 0;
let gameOver = false;
let gameRunning = false;
let dropInterval = null;
let dropTime = 1000;

// DOM 요소
let gameBoardEl = null;
let nextBoardEl = null;

// 초기화
function init() {
    gameBoardEl = document.getElementById('game-board');
    nextBoardEl = document.getElementById('next-board');
    
    // 보드 생성
    createBoard();
    createNextBoard();
    
    // 이벤트 리스너
    document.addEventListener('keydown', handleKeydown);
    
    showStartScreen();
}

// 보드 생성
function createBoard() {
    gameBoardEl.innerHTML = '';
    gameBoardEl.style.gridTemplateColumns = `repeat(${COLS}, ${CELL_SIZE}px)`;
    
    for (let r = 0; r < ROWS; r++) {
        for (let c = 0; c < COLS; c++) {
            const cell = document.createElement('div');
            cell.className = 'cell';
            cell.id = `cell-${r}-${c}`;
            gameBoardEl.appendChild(cell);
        }
    }
}

// 다음 조각 보드 생성
function createNextBoard() {
    nextBoardEl.innerHTML = '';
    nextBoardEl.style.gridTemplateColumns = 'repeat(4, 20px)';
    
    for (let r = 0; r < 4; r++) {
        for (let c = 0; c < 4; c++) {
            const cell = document.createElement('div');
            cell.className = 'cell';
            cell.style.width = '20px';
            cell.style.height = '20px';
            nextBoardEl.appendChild(cell);
        }
    }
}

// 시작 화면
function showStartScreen() {
    document.getElementById('messageText').innerHTML = '🧱 Tetris<br><br>버튼을 눌러 시작!';
    document.getElementById('gameMessage').classList.add('show');
}

// 게임 시작
function startGame() {
    // 보드 초기화
    board = Array(ROWS).fill().map(() => Array(COLS).fill(0));
    
    // 상태 초기화
    score = 0;
    level = 1;
    lines = 0;
    gameOver = false;
    gameRunning = true;
    dropTime = 1000;
    
    // 새 조각
    nextPiece = createPiece();
    spawnPiece();
    
    // UI 업데이트
    updateStats();
    hideMessage();
    
    // 드롭 타이머 시작
    startDropTimer();
}

// 조각 생성
function createPiece() {
    const name = PIECE_NAMES[Math.floor(Math.random() * PIECE_NAMES.length)];
    return {
        shape: TETROMINOES[name].shape.map(row => [...row]),
        color: TETROMINOES[name].color,
        x: Math.floor(COLS / 2) - 2,
        y: 0
    };
}

// 조각 생성
function spawnPiece() {
    currentPiece = nextPiece;
    currentPiece.x = Math.floor(COLS / 2) - Math.floor(currentPiece.shape[0].length / 2);
    currentPiece.y = 0;
    
    nextPiece = createPiece();
    renderNextPiece();
    
    // 충돌 검사
    if (checkCollision()) {
        gameOver = true;
        gameRunning = false;
        stopDropTimer();
        
        // 최고 점수 저장
        const bestScore = localStorage.getItem('tetrisBestScore') || 0;
        if (score > bestScore) {
            localStorage.setItem('tetrisBestScore', score);
        }
        
        showGameOver();
    }
}

// 보드 렌더링
function renderBoard() {
    // 보드 초기화
    for (let r = 0; r < ROWS; r++) {
        for (let c = 0; c < COLS; c++) {
            const cell = document.getElementById(`cell-${r}-${c}`);
            cell.className = 'cell' + (board[r][c] ? ` filled ${board[r][c]}` : '');
        }
    }
    
    // 현재 조각 렌더링
    if (currentPiece) {
        for (let r = 0; r < currentPiece.shape.length; r++) {
            for (let c = 0; c < currentPiece.shape[r].length; c++) {
                if (currentPiece.shape[r][c]) {
                    const boardY = currentPiece.y + r;
                    const boardX = currentPiece.x + c;
                    
                    if (boardY >= 0 && boardY < ROWS && boardX >= 0 && boardX < COLS) {
                        const cell = document.getElementById(`cell-${boardY}-${boardX}`);
                        if (cell) cell.classList.add('filled', currentPiece.color);
                    }
                }
            }
        }
    }
}

// 다음 조각 렌더링
function renderNextPiece() {
    // 초기화
    const cells = nextBoardEl.querySelectorAll('.cell');
    cells.forEach(cell => {
        cell.className = 'cell';
        cell.style.background = '#111';
    });
    
    // 다음 조각 렌더링
    const offsetX = Math.floor((4 - nextPiece.shape[0].length) / 2);
    const offsetY = Math.floor((4 - nextPiece.shape.length) / 2);
    
    for (let r = 0; r < nextPiece.shape.length; r++) {
        for (let c = 0; c < nextPiece.shape[r].length; c++) {
            if (nextPiece.shape[r][c]) {
                const cell = nextBoardEl.querySelectorAll('.cell')[(offsetY + r) * 4 + (offsetX + c)];
                if (cell) {
                    cell.classList.add('filled', nextPiece.color);
                }
            }
        }
    }
}

// 충돌 검사
function checkCollision(offsetX = 0, offsetY = 0, shape = null) {
    const piece = shape || currentPiece.shape;
    const px = (currentPiece ? currentPiece.x : 0) + offsetX;
    const py = (currentPiece ? currentPiece.y : 0) + offsetY;
    
    for (let r = 0; r < piece.length; r++) {
        for (let c = 0; c < piece[r].length; c++) {
            if (piece[r][c]) {
                const boardY = py + r;
                const boardX = px + c;
                
                // 경계 초과
                if (boardX < 0 || boardX >= COLS || boardY >= ROWS) {
                    return true;
                }
                
                // 보드와 충돌
                if (boardY >= 0 && board[boardY][boardX]) {
                    return true;
                }
            }
        }
    }
    return false;
}

// 조각 고정
function lockPiece() {
    for (let r = 0; r < currentPiece.shape.length; r++) {
        for (let c = 0; c < currentPiece.shape[r].length; c++) {
            if (currentPiece.shape[r][c]) {
                const boardY = currentPiece.y + r;
                const boardX = currentPiece.x + c;
                
                if (boardY >= 0 && boardY < ROWS && boardX >= 0 && boardX < COLS) {
                    board[boardY][boardX] = currentPiece.color;
                }
            }
        }
    }
    
    // 라인 제거
    clearLines();
    
    // 새 조각
    spawnPiece();
}

// 라인 제거
function clearLines() {
    let linesCleared = 0;
    
    for (let r = ROWS - 1; r >= 0; r--) {
        if (board[r].every(cell => cell !== 0)) {
            // 라인 제거
            board.splice(r, 1);
            board.unshift(Array(COLS).fill(0));
            linesCleared++;
            r++; // 같은 라인 다시 검사
        }
    }
    
    if (linesCleared > 0) {
        // 점수 계산
        const points = [0, 100, 300, 500, 800];
        score += points[linesCleared] * level;
        lines += linesCleared;
        
        // 레벨업
        const newLevel = Math.floor(lines / 10) + 1;
        if (newLevel > level) {
            level = newLevel;
            dropTime = Math.max(100, 1000 - (level - 1) * 100);
            restartDropTimer();
        }
        
        updateStats();
        
        // 진동
        if (navigator.vibrate) navigator.vibrate(50);
    }
}

// 이동
function moveLeft() {
    if (!gameRunning || gameOver) return;
    if (!checkCollision(-1, 0)) {
        currentPiece.x--;
        renderBoard();
    }
}

function moveRight() {
    if (!gameRunning || gameOver) return;
    if (!checkCollision(1, 0)) {
        currentPiece.x++;
        renderBoard();
    }
}

function moveDown() {
    if (!gameRunning || gameOver) return;
    if (!checkCollision(0, 1)) {
        currentPiece.y++;
        score += 1;
        updateStats();
        renderBoard();
        return true;
    }
    return false;
}

// 하드 드롭
function hardDrop() {
    if (!gameRunning || gameOver) return;
    
    let dropDistance = 0;
    while (!checkCollision(0, dropDistance + 1)) {
        dropDistance++;
    }
    
    currentPiece.y += dropDistance;
    score += dropDistance * 2;
    lockPiece();
    renderBoard();
    
    if (navigator.vibrate) navigator.vibrate(30);
}

// 회전
function rotate() {
    if (!gameRunning || gameOver) return;
    
    // 회전된 형태 계산
    const rotated = [];
    const rows = currentPiece.shape.length;
    const cols = currentPiece.shape[0].length;
    
    for (let c = 0; c < cols; c++) {
        rotated[c] = [];
        for (let r = rows - 1; r >= 0; r--) {
            rotated[c].push(currentPiece.shape[r][c]);
        }
    }
    
    // 충돌 검사
    if (!checkCollision(0, 0, rotated)) {
        currentPiece.shape = rotated;
        renderBoard();
        
        if (navigator.vibrate) navigator.vibrate(20);
    } else {
        // 벽 킥 시도
        if (!checkCollision(-1, 0, rotated)) {
            currentPiece.x--;
            currentPiece.shape = rotated;
            renderBoard();
            return;
        }
        if (!checkCollision(1, 0, rotated)) {
            currentPiece.x++;
            currentPiece.shape = rotated;
            renderBoard();
            return;
        }
        if (!checkCollision(-2, 0, rotated)) {
            currentPiece.x -= 2;
            currentPiece.shape = rotated;
            renderBoard();
            return;
        }
    }
}

// 드롭 타이머
function startDropTimer() {
    stopDropTimer();
    dropInterval = setInterval(dropPiece, dropTime);
}

function restartDropTimer() {
    startDropTimer();
}

function stopDropTimer() {
    if (dropInterval) {
        clearInterval(dropInterval);
        dropInterval = null;
    }
}

function dropPiece() {
    if (!gameRunning || gameOver) return;
    
    if (!checkCollision(0, 1)) {
        currentPiece.y++;
        renderBoard();
    } else {
        lockPiece();
        renderBoard();
    }
}

// 키보드 이벤트
function handleKeydown(e) {
    if (!gameRunning || gameOver) return;
    
    switch(e.code) {
        case 'ArrowLeft':
        case 'KeyA':
            e.preventDefault();
            moveLeft();
            break;
        case 'ArrowRight':
        case 'KeyD':
            e.preventDefault();
            moveRight();
            break;
        case 'ArrowDown':
        case 'KeyS':
            e.preventDefault();
            moveDown();
            break;
        case 'ArrowUp':
        case 'KeyW':
            e.preventDefault();
            rotate();
            break;
        case 'Space':
            e.preventDefault();
            hardDrop();
            break;
    }
}

// 통계 업데이트
function updateStats() {
    document.getElementById('score').textContent = score;
    document.getElementById('level').textContent = level;
    document.getElementById('lines').textContent = lines;
}

// 게임 오버
function showGameOver() {
    const bestScore = localStorage.getItem('tetrisBestScore') || 0;
    document.getElementById('messageText').innerHTML = 
        `💀 게임 오버!<br><br>점수: ${score}<br>최고 점수: ${bestScore}<br>레벨: ${level}`;
    document.getElementById('gameMessage').classList.add('show');
    
    if (navigator.vibrate) navigator.vibrate([100, 50, 100]);
}

// 메시지
function hideMessage() {
    document.getElementById('gameMessage').classList.remove('show');
}

// 초기화 실행
init();
