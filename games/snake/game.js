/**
 * Snake 게임 로직
 */

const GRID_SIZE = 20;
const CELL_SIZE = 18;
let board = null;
let snake = [];
let food = null;
let direction = 'right';
let nextDirection = 'right';
let score = 0;
let bestScore = localStorage.getItem('snakeBestScore') || 0;
let gameRunning = false;
let gameInterval = null;
let gameSpeed = 150;

function init() {
    board = document.getElementById('game-board');
    
    // 보드 크기 설정
    const gridWidth = 15;
    const gridHeight = 18;
    board.style.width = (gridWidth * CELL_SIZE) + 'px';
    board.style.height = (gridHeight * CELL_SIZE) + 'px';
    
    document.getElementById('best-score').textContent = bestScore;
    
    // 터치/키보드 이벤트
    document.addEventListener('keydown', handleKeydown);
    board.addEventListener('click', () => {
        if (!gameRunning) startGame();
    });
    
    showStartScreen();
}

function showStartScreen() {
    document.getElementById('messageText').innerHTML = '🐍 Snake<br><br>버튼을 눌러 시작!';
    document.getElementById('gameMessage').classList.add('show');
}

function startGame() {
    // 초기화
    snake = [
        { x: 5, y: 8 },
        { x: 4, y: 8 },
        { x: 3, y: 8 }
    ];
    direction = 'right';
    nextDirection = 'right';
    score = 0;
    gameSpeed = 150;
    
    document.getElementById('score').textContent = score;
    document.getElementById('gameMessage').classList.remove('show');
    
    // 음식 생성
    createFood();
    
    // 게임 루프 시작
    gameRunning = true;
    gameLoop();
}

function gameLoop() {
    if (!gameRunning) return;
    
    direction = nextDirection;
    
    // 새 머리 위치 계산
    const head = { ...snake[0] };
    
    switch (direction) {
        case 'up': head.y--; break;
        case 'down': head.y++; break;
        case 'left': head.x--; break;
        case 'right': head.x++; break;
    }
    
    // 충돌 검사
    if (checkCollision(head)) {
        gameOver();
        return;
    }
    
    // 음식 먹음
    if (head.x === food.x && head.y === food.y) {
        score += 10;
        document.getElementById('score').textContent = score;
        
        // 속도 증가
        if (gameSpeed > 80) {
            gameSpeed -= 2;
        }
        
        createFood();
    } else {
        snake.pop(); // 꼬리 제거
    }
    
    snake.unshift(head); // 새 머리 추가
    render();
    
    gameInterval = setTimeout(gameLoop, gameSpeed);
}

function checkCollision(head) {
    // 벽 충돌
    const gridWidth = 15;
    const gridHeight = 18;
    
    if (head.x < 0 || head.x >= gridWidth || head.y < 0 || head.y >= gridHeight) {
        return true;
    }
    
    // 자기 몸 충돌
    for (let i = 0; i < snake.length; i++) {
        if (head.x === snake[i].x && head.y === snake[i].y) {
            return true;
        }
    }
    
    return false;
}

function createFood() {
    const gridWidth = 15;
    const gridHeight = 18;
    
    let newFood;
    do {
        newFood = {
            x: Math.floor(Math.random() * gridWidth),
            y: Math.floor(Math.random() * gridHeight)
        };
    } while (snake.some(segment => segment.x === newFood.x && segment.y === newFood.y));
    
    food = newFood;
}

function render() {
    board.innerHTML = '';
    
    // 뱀 렌더링
    snake.forEach((segment, index) => {
        const el = document.createElement('div');
        el.className = 'snake' + (index === 0 ? ' snake-head' : '');
        el.style.left = (segment.x * CELL_SIZE) + 'px';
        el.style.top = (segment.y * CELL_SIZE) + 'px';
        el.style.width = (CELL_SIZE - 1) + 'px';
        el.style.height = (CELL_SIZE - 1) + 'px';
        board.appendChild(el);
    });
    
    // 음식 렌더링
    const foodEl = document.createElement('div');
    foodEl.className = 'food';
    foodEl.textContent = '🍎';
    foodEl.style.left = (food.x * CELL_SIZE) + 'px';
    foodEl.style.top = (food.y * CELL_SIZE) + 'px';
    board.appendChild(foodEl);
}

function changeDirection(newDir) {
    if (!gameRunning) return;
    
    // 반대 방향으로 이동 방지
    if ((newDir === 'up' && direction !== 'down') ||
        (newDir === 'down' && direction !== 'up') ||
        (newDir === 'left' && direction !== 'right') ||
        (newDir === 'right' && direction !== 'left')) {
        nextDirection = newDir;
    }
}

function handleKeydown(e) {
    if (!gameRunning) return;
    
    switch(e.key) {
        case 'ArrowUp': case 'w': case 'W':
            changeDirection('up');
            break;
        case 'ArrowDown': case 's': case 'S':
            changeDirection('down');
            break;
        case 'ArrowLeft': case 'a': case 'A':
            changeDirection('left');
            break;
        case 'ArrowRight': case 'd': case 'D':
            changeDirection('right');
            break;
    }
}

function gameOver() {
    gameRunning = false;
    clearTimeout(gameInterval);
    
    if (navigator.vibrate) navigator.vibrate([100, 50, 100]);
    
    // 최고 점수 업데이트
    if (score > bestScore) {
        bestScore = score;
        localStorage.setItem('snakeBestScore', bestScore);
        document.getElementById('best-score').textContent = bestScore;
        document.getElementById('messageText').innerHTML = `💀 게임 오버!<br><br>점수: ${score}<br>🎉 새 최고 기록!`;
    } else {
        document.getElementById('messageText').innerHTML = `💀 게임 오버!<br><br>점수: ${score}<br>최고 기록: ${bestScore}`;
    }
    
    document.getElementById('gameMessage').classList.add('show');
}

// 초기화 실행
init();
