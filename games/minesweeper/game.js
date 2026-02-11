/**
// v=20260210 - cache bust
 * Minesweeper 게임 로직
 */

const LEVELS = {
    easy: { rows: 8, cols: 10, mines: 10 },
    normal: { rows: 10, cols: 12, mines: 20 },
    hard: { rows: 12, cols: 14, mines: 35 }
};

let board = [];
let revealed = [];
let flagged = [];
let rows = 10;
let cols = 12;
let mines = 20;
let gameOver = false;
let gameWon = false;
let timer = 0;
let timerInterval = null;
let firstClick = true;

function init() {
    toggleDifficulty();
}

function toggleDifficulty() {
    const diffs = ['easy', 'normal', 'hard'];
    const labels = { easy: '쉬움', normal: '보통', hard: '어려움' };
    const currentDiff = document.getElementById('diffLabel').textContent;
    
    let newDiff;
    if (currentDiff === '쉬움') newDiff = 'normal';
    else if (currentDiff === '보통') newDiff = 'hard';
    else newDiff = 'easy';
    
    document.getElementById('diffLabel').textContent = labels[newDiff];
    
    const config = LEVELS[newDiff];
    rows = config.rows;
    cols = config.cols;
    mines = config.mines;
    
    initGame();
}

function initGame() {
    board = [];
    revealed = [];
    flagged = [];
    gameOver = false;
    gameWon = false;
    firstClick = true;
    timer = 0;
    
    if (timerInterval) clearInterval(timerInterval);
    document.getElementById('timer').textContent = '0';
    document.getElementById('mines').textContent = mines;
    
    createBoard();
    renderBoard();
    hideMessage();
}

function createBoard() {
    for (let r = 0; r < rows; r++) {
        board[r] = [];
        revealed[r] = [];
        flagged[r] = [];
        for (let c = 0; c < cols; c++) {
            board[r][c] = 0;
            revealed[r][c] = false;
            flagged[r][c] = false;
        }
    }
}

function placeMines(excludeR, excludeC) {
    let placed = 0;
    while (placed < mines) {
        const r = Math.floor(Math.random() * rows);
        const c = Math.floor(Math.random() * cols);
        
        // 첫 클릭 주변은 지뢰 제외
        if (excludeR !== undefined) {
            if (Math.abs(r - excludeR) <= 1 && Math.abs(c - excludeC) <= 1) continue;
        }
        
        if (board[r][c] !== -1) {
            board[r][c] = -1;
            placed++;
        }
    }
    
    // 숫자 계산
    for (let r = 0; r < rows; r++) {
        for (let c = 0; c < cols; c++) {
            if (board[r][c] === -1) continue;
            
            let count = 0;
            for (let dr = -1; dr <= 1; dr++) {
                for (let dc = -1; dc <= 1; dc++) {
                    const nr = r + dr;
                    const nc = c + dc;
                    if (nr >= 0 && nr < rows && nc >= 0 && nc < cols && board[nr][nc] === -1) {
                        count++;
                    }
                }
            }
            board[r][c] = count;
        }
    }
}

function renderBoard() {
    const gameBoard = document.getElementById('game-board');
    gameBoard.innerHTML = '';
    
    gameBoard.style.gridTemplateColumns = `repeat(${cols}, 32px)`;
    
    for (let r = 0; r < rows; r++) {
        for (let c = 0; c < cols; c++) {
            const cell = document.createElement('div');
            cell.className = 'cell';
            cell.dataset.row = r;
            cell.dataset.col = c;
            
            if (revealed[r][c]) {
                cell.classList.add('revealed');
                if (board[r][c] === -1) {
                    cell.classList.add('mine');
                    cell.textContent = '💣';
                } else if (board[r][c] > 0) {
                    cell.textContent = board[r][c];
                    cell.dataset.num = board[r][c];
                }
            } else if (flagged[r][c]) {
                cell.classList.add('flagged');
                cell.textContent = '🚩';
            }
            
            cell.addEventListener('click', () => handleClick(r, c));
            cell.addEventListener('contextmenu', (e) => {
                e.preventDefault();
                toggleFlag(r, c);
            });
            
            gameBoard.appendChild(cell);
        }
    }
}

function handleClick(r, c) {
    if (gameOver || revealed[r][c] || flagged[r][c]) return;
    
    // 첫 클릭 시 지뢰 배치
    if (firstClick) {
        firstClick = false;
        placeMines(r, c);
        startTimer();
    }
    
    // 지뢰 클릭
    if (board[r][c] === -1) {
        revealAll();
        gameOver = true;
        showMessage('💥 게임 오버!');
        return;
    }
    
    // 빈칸 공개
    reveal(r, c);
    renderBoard();
    
    // 승리 검사
    checkWin();
}

function toggleFlag(r, c) {
    if (gameOver || revealed[r][c]) return;
    
    flagged[r][c] = !flagged[r][c];
    renderBoard();
    
    const flagCount = flagged.flat().filter(f => f).length;
    document.getElementById('mines').textContent = mines - flagCount;
}

function reveal(r, c) {
    if (r < 0 || r >= rows || c < 0 || c >= cols) return;
    if (revealed[r][c] || flagged[r][c]) return;
    
    revealed[r][c] = true;
    
    if (board[r][c] === 0) {
        for (let dr = -1; dr <= 1; dr++) {
            for (let dc = -1; dc <= 1; dc++) {
                reveal(r + dr, c + dc);
            }
        }
    }
}

function revealAll() {
    for (let r = 0; r < rows; r++) {
        for (let c = 0; c < cols; c++) {
            if (board[r][c] === -1) {
                revealed[r][c] = true;
            }
        }
    }
    renderBoard();
}

function checkWin() {
    let unrevealed = 0;
    for (let r = 0; r < rows; r++) {
        for (let c = 0; c < cols; c++) {
            if (!revealed[r][c]) unrevealed++;
        }
    }
    
    if (unrevealed === mines) {
        gameWon = true;
        gameOver = true;
        stopTimer();
        showMessage('🎉 클리어! 💣');
        
        // 모든 지뢰 표시
        for (let r = 0; r < rows; r++) {
            for (let c = 0; c < cols; c++) {
                if (board[r][c] === -1) {
                    flagged[r][c] = true;
                }
            }
        }
        renderBoard();
    }
}

function startTimer() {
    timerInterval = setInterval(() => {
        timer++;
        document.getElementById('timer').textContent = timer;
    }, 1000);
}

function stopTimer() {
    if (timerInterval) {
        clearInterval(timerInterval);
        timerInterval = null;
    }
}

function showMessage(text) {
    document.getElementById('messageText').innerHTML = text;
    document.getElementById('gameMessage').classList.add('show');
}

function hideMessage() {
    document.getElementById('gameMessage').classList.remove('show');
}

// 초기화 실행
init();
