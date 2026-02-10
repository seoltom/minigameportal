/**
 * Mahjong Connect 게임 로직
 */

// 타일 이모지 (쌍으로 사용)
const TILES = [
    '🀄', '🀅', '🀆', '🀇', '🀈', '🀉', '🀊', '🀋',
    '🀌', '🀍', '🀎', '🀏', '🀐', '🀑', '🀒', '🀓',
    '🎋', '🎎', '🎏', '🎐', '🎑', '🎒', '🎓', '🌸',
    '🌺', '🌻', '🌼', '🌽', '🌾', '🌿', '🍀', '🍁',
    '🍂', '🍃', '🍇', '🍈', '🍉', '🍊', '🍋', '🍌'
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
    
    // 보드 생성
    const totalTiles = rows * cols;
    const pairCount = totalTiles / 2;
    pairsLeft = pairCount;
    
    // 타일 선택 (쌍으로)
    const selectedTiles = [];
    for (let i = 0; i < pairCount; i++) {
        const tile = TILES[i % TILES.length];
        selectedTiles.push(tile, tile);
    }
    
    // 타일 섞기
    for (let i = selectedTiles.length - 1; i > 0; i--) {
        const j = Math.floor(Math.random() * (i + 1));
        [selectedTiles[i], selectedTiles[j]] = [selectedTiles[j], selectedTiles[i]];
    }
    
    // 보드에 배치 (0 = 빈칸, 실제 타일은 1부터 시작)
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
    gameBoard.style.gridTemplateColumns = `repeat(${cols}, 50px)`;
    
    for (let i = 1; i <= rows; i++) {
        for (let j = 1; j <= cols; j++) {
            const tile = document.createElement('div');
            tile.className = 'tile';
            tile.dataset.row = i;
            tile.dataset.col = j;
            tile.textContent = board[i][j];
            
            if (board[i][j] === 0) {
                tile.classList.add('matched');
            }
            
            tile.addEventListener('click', () => handleTileClick(i, j));
            gameBoard.appendChild(tile);
        }
    }
}

// 타일 클릭 처리
function handleTileClick(row, col) {
    if (gameOver || gameWon || board[row][col] === 0) return;
    
    const clicked = { row, col, value: board[row][col] };
    
    // 첫 번째 타일 선택
    if (!selectedTile) {
        selectedTile = clicked;
        highlightTile(row, col, true);
        return;
    }
    
    // 같은 타일 클릭 -> 선택 취소
    if (selectedTile.row === row && selectedTile.col === col) {
        highlightTile(row, col, false);
        selectedTile = null;
        return;
    }
    
    // 다른 타일 선택 -> 매칭 확인
    if (selectedTile.value === clicked.value) {
        // 경로 찾기
        const path = findPath(selectedTile.row, selectedTile.col, row, col);
        
        if (path) {
            // 매칭 성공
            score += 100;
            pairsLeft--;
            
            // 타일 제거
            board[selectedTile.row][selectedTile.col] = 0;
            board[row][col] = 0;
            
            // 경로 시각화 (선택사항)
            drawPath(path);
            
            // 타일 다시 렌더링
            setTimeout(() => {
                renderBoard();
                updateStats();
                checkWin();
            }, 300);
            
            selectedTile = null;
        } else {
            // 매칭 실패
            highlightTile(selectedTile.row, selectedTile.col, false);
            selectedTile = clicked;
            highlightTile(row, col, true);
        }
    } else {
        // 다른 그림
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
    // 0: 상, 1: 하, 2: 좌, 3: 우
    const directions = [[-1, 0], [1, 0], [0, -1], [0, 1]];
    
    // BFS
    const queue = [];
    const visited = new Set();
    const parent = new Map();
    
    queue.push({ row: r1, col: c1, turns: 0, path: [] });
    visited.add(`${r1},${c1}`);
    
    while (queue.length > 0) {
        const current = queue.shift();
        
        // 목적지 도착
        if (current.row === r2 && current.col === c2) {
            return current.path;
        }
        
        // 방향 탐색
        for (let i = 0; i < directions.length; i++) {
            const [dr, dc] = directions[i];
            let newRow = current.row + dr;
            let newCol = current.col + dc;
            const newTurns = current.turns + (current.path.length > 0 && current.path.length % 2 === 0 ? 1 : 0);
            
            // 범위 체크 (패딩 포함)
            if (newRow < 0 || newRow > rows + 1 || newCol < 0 || newCol > cols + 1) {
                continue;
            }
            
            // 이미 방문
            const key = `${newRow},${newCol}`;
            if (visited.has(key)) {
                continue;
            }
            
            // 빈칸이거나 목적지
            if (board[newRow][newCol] === 0 || (newRow === r2 && newCol === c2)) {
                visited.add(key);
                const newPath = [...current.path, { row: newRow, col: newCol }];
                queue.push({
                    row: newRow,
                    col: newCol,
                    turns: newTurns,
                    path: newPath
                });
            }
        }
    }
    
    return null;
}

// 경로 그리기 (시각적 효과)
function drawPath(path) {
    // 간단한 효과 - 선택된 타일들을 잠시 보여줌
    if (selectedTile) {
        highlightTile(selectedTile.row, selectedTile.col, true);
    }
}

// 힌트 표시
function showHint() {
    if (gameOver || gameWon) return;
    
    // 힌트 타일 찾기
    for (let i = 1; i <= rows; i++) {
        for (let j = 1; j <= cols; j++) {
            if (board[i][j] === 0) continue;
            
            for (let ii = i; ii <= rows; ii++) {
                for (let jj = 1; jj <= cols; jj++) {
                    if (i === ii && j === jj) continue;
                    if (board[ii][jj] === 0) continue;
                    if (board[i][j] !== board[ii][jj]) continue;
                    
                    if (findPath(i, j, ii, jj)) {
                        // 힌트 표시
                        const tile1 = document.querySelector(`.tile[data-row="${i}"][data-col="${j}"]`);
                        const tile2 = document.querySelector(`.tile[data-row="${ii}"][data-col="${jj}"]`);
                        
                        if (tile1) tile1.classList.add('hint');
                        if (tile2) tile2.classList.add('hint');
                        
                        // 2초 후 제거
                        setTimeout(() => {
                            if (tile1) tile1.classList.remove('hint');
                            if (tile2) tile2.classList.remove('hint');
                        }, 2000);
                        
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
        score += timeLeft * 10; // 시간 보너스
        showMessage(`🎉 축하합니다! 클리어! 점수: ${score}`, 'win');
    } else if (timeLeft <= 0) {
        gameOver = true;
        stopTimer();
        showMessage('😢 시간 초과! 게임 오버!', 'over');
    }
}

// 타이머 시작
function startTimer() {
    stopTimer();
    timerInterval = setInterval(() => {
        timeLeft--;
        updateStats();
        checkWin();
    }, 1000);
}

// 타이머 중지
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

// 메시지 표시
function showMessage(text, type) {
    const msg = document.getElementById('game-message');
    msg.textContent = text;
    msg.className = 'game-message ' + type;
}

// 메시지 숨기기
function hideMessage() {
    const msg = document.getElementById('game-message');
    msg.style.display = 'none';
}

// 키보드 이벤트 (선택적)
document.addEventListener('keydown', (e) => {
    if (e.key === 'h' || e.key === 'H') {
        showHint();
    }
    if (e.key === 'n' || e.key === 'N') {
        initGame();
    }
});

// 게임 시작
initGame();
