/**
// v=20260210 - cache bust
 * 2048 게임 로직
 */

let board = [];
let score = 0;
let bestScore = localStorage.getItem('2048-best') || 0;
let gameOver = false;
let won = false;

// 게임 초기화
function initGame() {
    board = Array(4).fill().map(() => Array(4).fill(0));
    score = 0;
    gameOver = false;
    won = false;
    
    addRandomTile();
    addRandomTile();
    updateDisplay();
    hideMessage();
}

// 랜덤 타일 추가
function addRandomTile() {
    const emptyCells = [];
    for (let i = 0; i < 4; i++) {
        for (let j = 0; j < 4; j++) {
            if (board[i][j] === 0) {
                emptyCells.push({i, j});
            }
        }
    }
    
    if (emptyCells.length > 0) {
        const {i, j} = emptyCells[Math.floor(Math.random() * emptyCells.length)];
        board[i][j] = Math.random() < 0.9 ? 2 : 4;
    }
}

// 화면 업데이트
function updateDisplay() {
    const gameBoard = document.getElementById('game-board');
    gameBoard.innerHTML = '';
    
    for (let i = 0; i < 4; i++) {
        for (let j = 0; j < 4; j++) {
            const cell = document.createElement('div');
            cell.className = 'cell';
            const value = board[i][j];
            cell.textContent = value || '';
            cell.setAttribute('data-value', value || '');
            gameBoard.appendChild(cell);
        }
    }
    
    document.getElementById('score').textContent = score;
    document.getElementById('best-score').textContent = bestScore;
}

// 타일 이동
function move(direction) {
    if (gameOver || won) return;
    
    let moved = false;
    const rotated = rotateBoard(board);
    
    switch(direction) {
        case 'left':
            moved = slide(rotated);
            break;
        case 'right':
            rotated.reverse();
            moved = slide(rotated);
            rotated.reverse();
            break;
        case 'up':
            moved = slide(rotated);
            break;
        case 'down':
            rotated.reverse();
            moved = slide(rotated);
            rotated.reverse();
            break;
    }
    
    if (moved) {
        board = rotateBoard(rotated);
        addRandomTile();
        updateDisplay();
        checkGameState();
    }
}

// 타일 슬라이드
function slide(row) {
    let moved = false;
    const filtered = row.filter(val => val !== 0);
    
    for (let i = 0; i < filtered.length - 1; i++) {
        if (filtered[i] === filtered[i + 1]) {
            filtered[i] *= 2;
            score += filtered[i];
            filtered[i + 1] = 0;
            moved = true;
        }
    }
    
    const newFiltered = filtered.filter(val => val !== 0);
    while (newFiltered.length < 4) {
        newFiltered.push(0);
    }
    
    if (JSON.stringify(row) !== JSON.stringify(newFiltered)) {
        moved = true;
    }
    
    for (let i = 0; i < 4; i++) {
        row[i] = newFiltered[i];
    }
    
    return moved;
}

// 보드 회전
function rotateBoard(matrix) {
    const result = Array(4).fill().map(() => Array(4).fill(0));
    for (let i = 0; i < 4; i++) {
        for (let j = 0; j < 4; j++) {
            result[j][3 - i] = matrix[i][j];
        }
    }
    return result;
}

// 게임 상태 확인
function checkGameState() {
    // 2048 찾음
    for (let i = 0; i < 4; i++) {
        for (let j = 0; j < 4; j++) {
            if (board[i][j] === 2048 && !won) {
                won = true;
                showMessage('🎉 축하합니다! 2048을 만들었습니다!', 'win');
                return;
            }
        }
    }
    
    // 게임 오버 확인
    if (isGameOver()) {
        gameOver = true;
        showMessage('게임 오버! 😢', 'over');
    }
    
    // 최고 점수 업데이트
    if (score > bestScore) {
        bestScore = score;
        localStorage.setItem('2048-best', bestScore);
    }
}

// 게임 오버 체크
function isGameOver() {
    // 빈칸 있으면 안 끝남
    for (let i = 0; i < 4; i++) {
        for (let j = 0; j < 4; j++) {
            if (board[i][j] === 0) return false;
        }
    }
    
    // 가로로 합칠 수 있으면 안 끝남
    for (let i = 0; i < 4; i++) {
        for (let j = 0; j < 3; j++) {
            if (board[i][j] === board[i][j + 1]) return false;
        }
    }
    
    // 세로로 합칠 수 있으면 안 끝남
    for (let i = 0; i < 3; i++) {
        for (let j = 0; j < 4; j++) {
            if (board[i][j] === board[i + 1][j]) return false;
        }
    }
    
    return true;
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

// 키보드 이벤트
document.addEventListener('keydown', (e) => {
    switch(e.key) {
        case 'ArrowLeft':
            e.preventDefault();
            move('left');
            break;
        case 'ArrowRight':
            e.preventDefault();
            move('right');
            break;
        case 'ArrowUp':
            e.preventDefault();
            move('up');
            break;
        case 'ArrowDown':
            e.preventDefault();
            move('down');
            break;
    }
});

// 터치 이벤트 (스와이프) - 게임 보드에서만
let touchStartX = 0;
let touchStartY = 0;
let gameBoard = null;

document.addEventListener('DOMContentLoaded', () => {
    gameBoard = document.getElementById('game-board');
    
    gameBoard.addEventListener('touchstart', (e) => {
        touchStartX = e.touches[0].clientX;
        touchStartY = e.touches[0].clientY;
    });

    gameBoard.addEventListener('touchend', (e) => {
        const touchEndX = e.changedTouches[0].clientX;
        const touchEndY = e.changedTouches[0].clientY;
        
        const dx = touchEndX - touchStartX;
        const dy = touchEndY - touchStartY;
        
        const minSwipe = 40;
        
        if (Math.abs(dx) > Math.abs(dy)) {
            if (dx > minSwipe) move('right');
            else if (dx < -minSwipe) move('left');
        } else {
            if (dy > minSwipe) move('down');
            else if (dy < -minSwipe) move('up');
        }
    });
});

// 게임 시작
initGame();
