/**
// v=20260210 - cache bust
 * Mahjong Connect 게임 로직 - 모바일 최적화 v2
 */

// 타일 이모지
const TILES = ['🀄', '🀅', '🀆', '🀇', '🀈', '🀉', '🀊', '🀋', '🀌', '🀍', '🀎', '🀏', '🀐', '🀑', '🀒', '🀓', '🎋', '🎎', '🎏', '🎐', '🎑', '🎒', '🎓', '🌸', '🌺', '🌻', '🌼', '🌽', '🌾', '🌿', '🍀', '🍁'];

// 레벨별 설정
const LEVELS = { easy: { rows: 4, cols: 6, time: 180 }, normal: { rows: 6, cols: 8, time: 300 }, hard: { rows: 8, cols: 10, time: 480 } };

let board = [], rows = 6, cols = 8, selectedTile = null, score = 0, pairsLeft = 0, timeLeft = 300, timerInterval = null, gameOver = false, gameWon = false;

// 게임 초기화
function initGame() {
    const level = document.getElementById('level').value;
    const config = LEVELS[level];
    rows = config.rows;
    cols = config.cols;
    timeLeft = config.time;
    
    const totalTiles = rows * cols, pairCount = totalTiles / 2;
    pairsLeft = pairCount;
    
    const selectedTiles = [];
    for (let i = 0; i < pairCount; i++) {
        const tile = TILES[i % TILES.length];
        selectedTiles.push(tile, tile);
    }
    
    // 셔플
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
    
    // 헤더 다시 보이기
    showHeader();
}

// 보드 렌더링
function renderBoard() {
    const container = document.getElementById('game-board');
    container.innerHTML = '';
    
    // 가용 공간 계산
    const containerWidth = container.parentElement.clientWidth - 20;
    const containerHeight = container.parentElement.clientHeight - 20;
    
    // 타일 크기 계산 (정사각형)
    const maxTileWidth = Math.floor(containerWidth / cols) - 2;
    const maxTileHeight = Math.floor(containerHeight / rows) - 2;
    const tileSize = Math.max(32, Math.min(maxTileWidth, maxTileHeight, 52));
    
    container.style.gridTemplateColumns = `repeat(${cols}, ${tileSize}px)`;
    container.style.gap = '2px';
    
    for (let i = 1; i <= rows; i++) {
        for (let j = 1; j <= cols; j++) {
            const tile = document.createElement('div');
            tile.className = 'tile';
            tile.dataset.row = i;
            tile.dataset.col = j;
            tile.textContent = board[i][j];
            tile.style.width = `${tileSize}px`;
            tile.style.height = `${Math.floor(tileSize * 1.2)}px`;
            tile.style.fontSize = `${Math.floor(tileSize * 0.7)}px`;
            
            if (board[i][j] === 0) tile.classList.add('matched');
            
            tile.addEventListener('click', (e) => { e.preventDefault(); handleTileClick(i, j); });
            tile.addEventListener('touchend', (e) => { e.preventDefault(); handleTileClick(i, j); });
            
            container.appendChild(tile);
        }
    }
}

// 타일 클릭
function handleTileClick(row, col) {
    if (gameOver || gameWon || board[row][col] === 0) return;
    
    const clicked = { row, col, value: board[row][col] };
    
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
            
            // 진동
            if (navigator.vibrate) navigator.vibrate(30);
            
            setTimeout(() => {
                renderBoard();
                updateStats();
                checkWin();
            }, 150);
            
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

// 하이라이트
function highlightTile(row, col, selected) {
    const tile = document.querySelector(`.tile[data-row="${row}"][data-col="${col}"]`);
    if (tile) tile.classList.toggle('selected', selected);
}

// BFS 경로 찾기
function findPath(r1, c1, r2, c2) {
    const directions = [[-1, 0], [1, 0], [0, -1], [0, 1]];
    const queue = [{ row: r1, col: c1, path: [] }];
    const visited = new Set([`${r1},${c1}`]);
    
    while (queue.length > 0) {
        const current = queue.shift();
        
        if (current.row === r2 && current.col === c2) return current.path;
        
        for (const [dr, dc] of directions) {
            const nr = current.row + dr, nc = current.col + dc;
            if (nr < 0 || nr > rows + 1 || nc < 0 || nc > cols + 1) continue;
            if (visited.has(`${nr},${nc}`)) continue;
            if (board[nr][nc] !== 0 && !(nr === r2 && nc === c2)) continue;
            
            visited.add(`${nr},${nc}`);
            queue.push({ row: nr, col: nc, path: [...current.path, { row: nr, col: nc }] });
        }
    }
    return null;
}

// 힌트
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
                        const t1 = document.querySelector(`.tile[data-row="${i}"][data-col="${j}"]`);
                        const t2 = document.querySelector(`.tile[data-row="${ii}"][data-col="${jj}"]`);
                        if (t1) t1.classList.add('hint');
                        if (t2) t2.classList.add('hint');
                        if (navigator.vibrate) navigator.vibrate(20);
                        setTimeout(() => {
                            if (t1) t1.classList.remove('hint');
                            if (t2) t2.classList.remove('hint');
                        }, 800);
                        return;
                    }
                }
            }
        }
    }
}

// 게임 상태
function checkWin() {
    if (pairsLeft === 0) {
        gameWon = true;
        stopTimer();
        score += timeLeft * 10;
        if (navigator.vibrate) navigator.vibrate([100, 50, 100]);
        showMessage(`🎉 클리어!<br>점수: ${score}`);
    } else if (timeLeft <= 0) {
        gameOver = true;
        stopTimer();
        if (navigator.vibrate) navigator.vibrate(200);
        showMessage('😢 시간 초과!');
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

// 통계
function updateStats() {
    document.getElementById('score').textContent = score;
    const m = Math.floor(timeLeft / 60), s = timeLeft % 60;
    document.getElementById('time').textContent = `${m}:${s.toString().padStart(2, '0')}`;
    document.getElementById('pairs').textContent = pairsLeft;
}

// 메시지
function showMessage(text) {
    document.getElementById('messageText').innerHTML = text;
    document.getElementById('gameMessage').classList.add('show');
}

function hideMessage() {
    document.getElementById('gameMessage').classList.remove('show');
}

// 헤더 토글
function toggleHeader() {
    const header = document.getElementById('headerSection');
    const btn = document.getElementById('toggleBtn');
    
    if (header.classList.contains('hidden')) {
        showHeader();
    } else {
        hideHeader();
    }
}

function hideHeader() {
    document.getElementById('headerSection').classList.add('hidden');
    document.getElementById('toggleBtn').classList.add('show');
    document.getElementById('toggleBtn').textContent = '⬇️ 메뉴 보기';
    setTimeout(renderBoard, 300);
}

function showHeader() {
    document.getElementById('headerSection').classList.remove('hidden');
    document.getElementById('toggleBtn').classList.remove('show');
    setTimeout(renderBoard, 300);
}

// 화면 크기 변경
let resizeTimeout;
window.addEventListener('resize', () => {
    clearTimeout(resizeTimeout);
    resizeTimeout = setTimeout(renderBoard, 100);
});

// 게임 시작
initGame();
