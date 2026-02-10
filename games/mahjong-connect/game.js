/**
 * Mahjong Connect 게임 로직 - 모바일 최적화
 */

// 타일 이모지 (쌍으로 사용)
const TILES = [
    '🀄', '🀅', '🀆', '🀇', '🀈', '🀉', '🀊', '🀋',
    '🀌', '🀍', '🀎', '🀏', '🀐', '🀑', '🀒', '🀓',
    '🎋', '🎎', '🎏', '🎐', '🎑', '🎒', '🎓', '🌸',
    '🌺', '🌻', '🌼', '🌽', '🌾', '🌿', '🍀', '🍁'
];

// 레벨별 설정
const LEVELS = {
    easy: { rows: 4, cols: 6, time: 180 },
    normal: { rows: 6, cols: 8, time: 300 },
    hard: { rows: 8, cols: 10, time: 480 }
};

let board = [];
let rows = 6;
let cols = 8;
let selectedTile = null;
let score = 0;
let pairsLeft = 0;
let timeLeft = 300;
let timerInterval = null;
let gameOver = false;
let gameWon = false;

// 게임 초기화
function initGame() {
    const level = document.getElementById('level').value;
    const config = LEVELS[level];
    rows = config.rows;
    cols = config.cols;
    timeLeft = config.time;
    
    const totalTiles = rows * cols;
    const pairCount = totalTiles / 2;
    pairsLeft = pairCount;
    
    const selectedTiles = [];
    for (let i = 0; i < pairCount; i++) {
        const tile = TILES[i % TILES.length];
        selectedTiles.push(tile, tile);
    }
    
    // Fisher-Yates 셔플
    for (let i = selectedTiles.length - 1; i > 0; i--) {
        const j = Math.floor(Math.random() * (i + 1));
        [selectedTiles[i], selectedTiles[j]] = [selectedTiles[j], selectedTiles[i]];
    }
    
    board = Array(rows + 2).fill().map(() => Array(cols + 2).fill(0));
    let idx = 0;
    for (let i = 1; i <= rows; i++) {
        for (let j = 1; j <= cols; j++) {
            board[i][j] = selectedTiles[idx++];
        }
    }
    
    score = 0;
    selectedTile = null;
    gameOver = false;
    gameWon = false;
    
    hideMessage();
    renderBoard();
    updateStats();
    startTimer();
}

// 보드 렌더링
function renderBoard() {
    const gameBoard = document.getElementById('game-board');
    gameBoard.innerHTML = '';
    
    // 화면 너비에 따라 타일 크기 계산
    const boardWidth = Math.min(window.innerWidth - 40, 500);
    const tileSize = Math.floor((boardWidth - (cols - 1) * 3) / cols);
    const clampedSize = Math.max(32, Math.min(tileSize, 50));
    
    gameBoard.style.gridTemplateColumns = `repeat(${cols}, ${clampedSize}px)`;
    gameBoard.style.gap = '3px';
    
    for (let i = 1; i <= rows; i++) {
        for (let j = 1; j <= cols; j++) {
            const tile = document.createElement('div');
            tile.className = 'tile';
            tile.dataset.row = i;
            tile.dataset.col = j;
            tile.textContent = board[i][j];
            tile.style.height = `${Math.floor(clampedSize * 1.15)}px`;
            tile.style.fontSize = `${Math.floor(clampedSize * 0.5)}px`;
            
            if (board[i][j] === 0) {
                tile.classList.add('matched');
            }
            
            // 터치 이벤트
            tile.addEventListener('click', (e) => {
                e.preventDefault();
                handleTileClick(i, j);
            });
            
            // 더블탭 방지
            tile.addEventListener('touchend', (e) => {
                e.preventDefault();
                handleTileClick(i, j);
            });
            
            gameBoard.appendChild(tile);
        }
    }
}

// 타일 클릭 처리
function handleTileClick(row, col) {
    if (gameOver || gameWon || board[row][col] === 0) return;
    
    const clicked = { row, col, value: board[row][j] };
    
    if (!selectedTile) {
        selectedTile = clicked;
        highlightTile(row, col, true);
        return;
    }
    
    if (selectedTile.row === row && selectedTile.col === col) {
        highlightTile(row, col, false);
        selectedTile = null;
        return;
    }
    
    if (selectedTile.value === clicked.value) {
        const path = findPath(selectedTile.row, selectedTile.col, row, col);
        
        if (path) {
            score += 100;
            pairsLeft--;
            
            board[selectedTile.row][selectedTile.col] = 0;
            board[row][col] = 0;
            
            // 진동 피드백 (모바일)
            if (navigator.vibrate) {
                navigator.vibrate(50);
            }
            
            setTimeout(() => {
                renderBoard();
                updateStats();
                checkWin();
            }, 200);
            
            selectedTile = null;
        } else {
            highlightTile(selectedTile.row, selectedTile.col, false);
            selectedTile = clicked;
            highlightTile(row, col, true);
        }
    } else {
        highlightTile(selectedTile.row, selectedTile.col, false);
        selectedTile = clicked;
        highlightTile(row, col, true);
    }
}

// 타일 하이라이트
function highlightTile(row, col, selected) {
    const tile = document.querySelector(`.tile[data-row="${row}"][data-col="${col}"]`);
    if (tile) {
        if (selected) {
            tile.classList.add('selected');
        } else {
            tile.classList.remove('selected');
        }
    }
}

// 경로 찾기 (BFS)
function findPath(r1, c1, r2, c2) {
    const directions = [[-1, 0], [1, 0], [0, -1], [0, 1]];
    
    const queue = [];
    const visited = new Set();
    const parent = new Map();
    
    queue.push({ row: r1, col: c1, turns: 0, path: [] });
    visited.add(`${r1},${c1}`);
    
    while (queue.length > 0) {
        const current = queue.shift();
        
        if (current.row === r2 && current.col === c2) {
            return current.path;
        }
        
        for (let i = 0; i < directions.length; i++) {
            const [dr, dc] = directions[i];
            let newRow = current.row + dr;
            let newCol = current.col + dc;
            
            if (newRow < 0 || newRow > rows + 1 || newCol < 0 || newCol > cols + 1) {
                continue;
            }
            
            const key = `${newRow},${newCol}`;
            if (visited.has(key)) {
                continue;
            }
            
            if (board[newRow][newCol] === 0 || (newRow === r2 && newCol === c2)) {
                visited.add(key);
                queue.push({
                    row: newRow,
                    col: newCol,
                    path: [...current.path, { row: newRow, col: newCol }]
                });
            }
        }
    }
    
    return null;
}

// 힌트 표시
function showHint() {
    if (gameOver || gameWon) return;
    
    for (let i = 1; i <= rows; i++) {
        for (let j = 1; j <= cols; j++) {
            if (board[i][j] === 0) continue;
            
            for (let ii = 1; ii <= rows; ii++) {
                for (let jj = 1; jj <= cols; jj++) {
                    if (i === ii && j === jj) continue;
                    if (board[ii][jj] === 0) continue;
                    if (board[i][j] !== board[ii][jj]) continue;
                    
                    if (findPath(i, j, ii, jj)) {
                        const tile1 = document.querySelector(`.tile[data-row="${i}"][data-col="${j}"]`);
                        const tile2 = document.querySelector(`.tile[data-row="${ii}"][data-col="${jj}"]`);
                        
                        if (tile1) tile1.classList.add('hint');
                        if (tile2) tile2.classList.add('hint');
                        
                        // 진동 피드백
                        if (navigator.vibrate) {
                            navigator.vibrate(30);
                        }
                        
                        setTimeout(() => {
                            if (tile1) tile1.classList.remove('hint');
                            if (tile2) tile2.classList.remove('hint');
                        }, 1000);
                        
                        return;
                    }
                }
            }
        }
    }
}

// 게임 상태 확인
function checkWin() {
    if (pairsLeft === 0) {
        gameWon = true;
        stopTimer();
        score += timeLeft * 10;
        
        // 승리 진동
        if (navigator.vibrate) {
            navigator.vibrate([100, 50, 100]);
        }
        
        showMessage(`🎉 클리어! 점수: ${score}`, 'win');
    } else if (timeLeft <= 0) {
        gameOver = true;
        stopTimer();
        
        if (navigator.vibrate) {
            navigator.vibrate(200);
        }
        
        showMessage('😢 시간 초과!', 'over');
    }
}

// 타이머
function startTimer() {
    stopTimer();
    timerInterval = setInterval(() => {
        timeLeft--;
        updateStats();
        checkWin();
    }, 1000);
}

function stopTimer() {
    if (timerInterval) {
        clearInterval(timerInterval);
        timerInterval = null;
    }
}

// 통계 업데이트
function updateStats() {
    document.getElementById('score').textContent = score;
    
    const minutes = Math.floor(timeLeft / 60);
    const seconds = timeLeft % 60;
    document.getElementById('time').textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
    document.getElementById('pairs').textContent = pairsLeft;
}

// 메시지
function showMessage(text, type) {
    const msg = document.getElementById('game-message');
    msg.textContent = text;
    msg.className = 'game-message ' + type;
}

function hideMessage() {
    const msg = document.getElementById('game-message');
    msg.style.display = 'none';
}

// 화면 크기 변경 시 다시 렌더링
window.addEventListener('resize', () => {
    renderBoard();
});

// 게임 시작
initGame();
