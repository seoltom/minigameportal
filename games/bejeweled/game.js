/**
// v=20260210 - cache bust
 * 보석 매칭 게임 로직
 */

// 보석 이모지 (대체용)
const GEMS = ['💎', '🔷', '💚', '⭐', '🔮', '🔶', '💠', '💙'];

// 레벨별 설정
const LEVELS = {
    easy: { rows: 6, cols: 7, target: 300, time: 180 },
    normal: { rows: 7, cols: 8, target: 500, time: 300 },
    hard: { rows: 8, cols: 9, target: 800, time: 420 }
};

let board = [];
let rows = 7;
let cols = 8;
let selectedGem = null;
let score = 0;
let level = 1;
let targetScore = 500;
let timeLeft = 300;
let timerInterval = null;
let gameOver = false;
let gameWon = false;
let isAnimating = false;

// 게임 초기화
function initGame() {
    const diff = document.getElementById('difficulty').value;
    const config = LEVELS[diff];
    rows = config.rows;
    cols = config.cols;
    targetScore = config.target;
    timeLeft = config.time;
    
    // 보드 생성
    board = [];
    for (let i = 0; i < rows; i++) {
        board[i] = [];
        for (let j = 0; j < cols; j++) {
            board[i][j] = getRandomGem(i, j);
        }
    }
    
    // 매칭 제거
    while (findMatches().length > 0) {
        for (let i = 0; i < rows; i++) {
            for (let j = 0; j < cols; j++) {
                board[i][j] = getRandomGem(i, j);
            }
        }
    }
    
    score = 0;
    level = 1;
    selectedGem = null;
    gameOver = false;
    gameWon = false;
    isAnimating = false;
    
    hideMessage();
    renderBoard();
    updateStats();
    startTimer();
    
    showHeader();
}

// 랜덤 보석 (매칭 방지)
function getRandomGem(row, col) {
    let gems = [];
    for (let g = 0; g < 8; g++) {
        // 가로 매칭 방지
        if (col >= 2 && board[row][col-1] === g && board[row][col-2] === g) continue;
        // 세로 매칭 방지
        if (row >= 2 && board[row-1][col] === g && board[row-2][col] === g) continue;
        gems.push(g);
    }
    return gems.length > 0 ? gems[Math.floor(Math.random() * gems.length)] : Math.floor(Math.random() * 8);
}

// 보드 렌더링
function renderBoard() {
    const container = document.getElementById('game-board');
    container.innerHTML = '';
    
    const containerWidth = container.parentElement.clientWidth - 20;
    const containerHeight = container.parentElement.clientHeight - 20;
    
    const gemWidth = Math.floor((containerWidth - (cols - 1) * 3) / cols);
    const gemHeight = Math.floor((containerHeight - (rows - 1) * 3) / rows);
    const gemSize = Math.max(38, Math.min(gemWidth, gemHeight, 55));
    
    container.style.gridTemplateColumns = `repeat(${cols}, ${gemSize}px)`;
    container.style.gap = '3px';
    
    for (let i = 0; i < rows; i++) {
        for (let j = 0; j < cols; j++) {
            const gem = document.createElement('div');
            gem.className = `gem gem-${board[i][j]}`;
            gem.dataset.row = i;
            gem.dataset.col = j;
            gem.textContent = GEMS[board[i][j]];
            gem.style.width = `${gemSize}px`;
            gem.style.height = `${gemSize}px`;
            gem.style.fontSize = `${Math.floor(gemSize * 0.6)}px`;
            
            gem.addEventListener('click', (e) => { e.preventDefault(); handleGemClick(i, j); });
            gem.addEventListener('touchend', (e) => { e.preventDefault(); handleGemClick(i, j); });
            
            container.appendChild(gem);
        }
    }
}

// 보석 클릭
function handleGemClick(row, col) {
    if (gameOver || gameWon || isAnimating) return;
    
    const clicked = { row, col };
    
    if (!selectedGem) {
        selectedGem = clicked;
        highlightGem(row, col, true);
        return;
    }
    
    // 같은 보석 클릭 -> 선택 취소
    if (selectedGem.row === row && selectedGem.col === col) {
        highlightGem(row, col, false);
        selectedGem = null;
        return;
    }
    
    // 인접한 보석인지 확인
    const rowDiff = Math.abs(selectedGem.row - row);
    const colDiff = Math.abs(selectedGem.col - col);
    
    if (rowDiff + colDiff === 1) {
        // 교환 시도
        swapGems(selectedGem.row, selectedGem.col, row, col);
        selectedGem = null;
    } else {
        // 다른 보석 선택
        highlightGem(selectedGem.row, selectedGem.col, false);
        selectedGem = clicked;
        highlightGem(row, col, true);
    }
}

// 보석 교환
async function swapGems(r1, c1, r2, c2) {
    isAnimating = true;
    
    // 교환
    [board[r1][c1], board[r2][c2]] = [board[r2][c2], board[r1][c1]];
    renderBoard();
    
    // 진동
    if (navigator.vibrate) navigator.vibrate(20);
    
    await sleep(100);
    
    // 매칭 확인
    const matches = findMatches();
    
    if (matches.length > 0) {
        // 매칭 있음 -> 제거 애니메이션
        await removeMatches(matches);
    } else {
        // 매칭 없음 -> 다시 교환
        [board[r1][c1], board[r2][c2]] = [board[r2][c2], board[r1][c1]];
        renderBoard();
        
        // 흔들림 효과
        const gem1 = document.querySelector(`.gem[data-row="${r1}"][data-col="${c1}"]`);
        const gem2 = document.querySelector(`.gem[data-row="${r2}"][data-col="${c2}"]`);
        if (gem1) gem1.style.animation = 'shake 0.3s';
        if (gem2) gem2.style.animation = 'shake 0.3s';
        
        await sleep(300);
        renderBoard();
    }
    
    isAnimating = false;
}

// 매칭 찾기
function findMatches() {
    const matches = new Set();
    
    // 가로 매칭
    for (let i = 0; i < rows; i++) {
        for (let j = 0; j < cols - 2; j++) {
            const g = board[i][j];
            if (g !== null && board[i][j+1] === g && board[i][j+2] === g) {
                let k = j;
                while (k < cols && board[i][k] === g) {
                    matches.add(`${i},${k}`);
                    k++;
                }
            }
        }
    }
    
    // 세로 매칭
    for (let j = 0; j < cols; j++) {
        for (let i = 0; i < rows - 2; i++) {
            const g = board[i][j];
            if (g !== null && board[i+1][j] === g && board[i+2][j] === g) {
                let k = i;
                while (k < rows && board[k][j] === g) {
                    matches.add(`${k},${j}`);
                    k++;
                }
            }
        }
    }
    
    return Array.from(matches).map(s => {
        const [r, c] = s.split(',').map(Number);
        return { row: r, col: c, gem: board[r][c] };
    });
}

// 매칭 제거
async function removeMatches(matches) {
    // 점수 계산 (매칭 수 * 10 + 보너스)
    const points = matches.length * 10 + (matches.length > 3 ? (matches.length - 3) * 20 : 0);
    score += points;
    
    // 애니메이션
    matches.forEach(m => {
        const gem = document.querySelector(`.gem[data-row="${m.row}"][data-col="${m.col}"]`);
        if (gem) gem.classList.add('matched');
    });
    
    if (navigator.vibrate) navigator.vibrate(50);
    
    await sleep(300);
    
    // 보석 제거
    matches.forEach(m => {
        board[m.row][m.col] = null;
    });
    
    // 보석 떨어뜨리기
    await dropGems();
    
    // 새로운 보석 추가
    await fillBoard();
    
    // 재귀적으로 매칭 확인
    const newMatches = findMatches();
    if (newMatches.length > 0) {
        await sleep(200);
        await removeMatches(newMatches);
    }
    
    updateStats();
    
    // 레벨업 확인
    if (score >= targetScore) {
        level++;
        targetScore = Math.floor(targetScore * 1.5);
        timeLeft += 60;
        if (navigator.vibrate) navigator.vibrate([100, 50, 100]);
        showMessage(`🎉 레벨 ${level}!<br>+60초 추가!`);
        await sleep(1500);
        hideMessage();
    }
}

// 보석 떨어뜨리기
async function dropGems() {
    for (let j = 0; j < cols; j++) {
        let empty = rows - 1;
        for (let i = rows - 1; i >= 0; i--) {
            if (board[i][j] !== null) {
                if (i !== empty) {
                    board[empty][j] = board[i][j];
                    board[i][j] = null;
                }
                empty--;
            }
        }
    }
    renderBoard();
    await sleep(200);
}

// 보드 채우기
async function fillBoard() {
    const container = document.getElementById('game-board');
    
    for (let j = 0; j < cols; j++) {
        for (let i = rows - 1; i >= 0; i--) {
            if (board[i][j] === null) {
                board[i][j] = Math.floor(Math.random() * 8);
                renderBoard();
                
                const gem = document.querySelector(`.gem[data-row="${i}"][data-col="${j}"]`);
                if (gem) gem.classList.add('falling');
            }
        }
    }
    
    // 새 매칭 확인
    const newMatches = findMatches();
    if (newMatches.length > 0) {
        await sleep(200);
        await removeMatches(newMatches);
    }
}

// 하이라이트
function highlightGem(row, col, selected) {
    const gem = document.querySelector(`.gem[data-row="${row}"][data-col="${col}"]`);
    if (gem) gem.classList.toggle('selected', selected);
}

// 힌트
function showHint() {
    if (gameOver || gameWon || isAnimating) return;
    
    // 가능한 움직임 찾기
    for (let i = 0; i < rows; i++) {
        for (let j = 0; j < cols; j++) {
            // 오른쪽과 교환
            if (j < cols - 1) {
                [board[i][j], board[i][j+1]] = [board[i][j+1], board[i][j]];
                if (findMatches().length > 0) {
                    highlightGem(i, j, true);
                    highlightGem(i, j+1, true);
                    [board[i][j], board[i][j+1]] = [board[i][j+1], board[i][j]];
                    setTimeout(() => {
                        renderBoard();
                    }, 1000);
                    return;
                }
                [board[i][j], board[i][j+1]] = [board[i][j+1], board[i][j]];
            }
            
            // 아래와 교환
            if (i < rows - 1) {
                [board[i][j], board[i+1][j]] = [board[i+1][j], board[i][j]];
                if (findMatches().length > 0) {
                    highlightGem(i, j, true);
                    highlightGem(i+1, j, true);
                    [board[i][j], board[i+1][j]] = [board[i+1][j], board[i][j]];
                    setTimeout(() => {
                        renderBoard();
                    }, 1000);
                    return;
                }
                [board[i][j], board[i+1][j]] = [board[i+1][j], board[i][j]];
            }
        }
    }
}

// 게임 상태
function checkGameState() {
    if (timeLeft <= 0) {
        gameOver = true;
        stopTimer();
        if (navigator.vibrate) navigator.vibrate(200);
        showMessage(`😢 게임 오버!<br>최종 점수: ${score}`);
    }
}

// 타이머
function startTimer() {
    stopTimer();
    timerInterval = setInterval(() => {
        timeLeft--;
        updateStats();
        checkGameState();
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
    document.getElementById('level').textContent = level;
    document.getElementById('target').textContent = targetScore;
    
    const m = Math.floor(timeLeft / 60), s = timeLeft % 60;
    document.getElementById('time').textContent = `${m}:${s.toString().padStart(2, '0')}`;
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

// 유틸리티
function sleep(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}

// 화면 크기 변경
let resizeTimeout;
window.addEventListener('resize', () => {
    clearTimeout(resizeTimeout);
    resizeTimeout = setTimeout(renderBoard, 100);
});

// CSS에 shake 애니메이션 추가
const style = document.createElement('style');
style.textContent = `
@keyframes shake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-5px); }
    75% { transform: translateX(5px); }
}
`;
document.head.appendChild(style);

// 게임 시작
initGame();
