/**
 * Brick Breaker 게임 로직
 */

let canvas = null;
let paddle = null;
let ball = null;
let paddleX = 0;
let ballX = 0;
let ballY = 0;
let ballVX = 0;
let ballVY = 0;
let score = 0;
let lives = 3;
let bricks = [];
let gameRunning = false;
let animationFrame = null;

const BRICK_ROWS = 5;
const BRICK_COLS = 7;
const BRICK_GAP = 4;

function init() {
    canvas = document.getElementById('game-canvas');
    paddle = document.getElementById('paddle');
    ball = document.getElementById('ball');
    
    // 터치 이벤트
    canvas.addEventListener('touchmove', handleTouchMove);
    canvas.addEventListener('touchstart', handleTouchMove);
    
    showStartScreen();
}

function showStartScreen() {
    document.getElementById('messageText').innerHTML = '🧱 Brick Breaker<br><br>화면을 터치하여 시작!';
    document.getElementById('gameMessage').classList.add('show');
    
    resetPositions();
}

function startGame() {
    score = 0;
    lives = 3;
    gameRunning = true;
    
    document.getElementById('score').textContent = score;
    document.getElementById('lives').textContent = lives;
    document.getElementById('lives2').textContent = lives;
    document.getElementById('gameMessage').classList.remove('show');
    
    createBricks();
    resetBall();
    
    // 패들 크기 설정
    const paddleWidth = 80;
    paddle.style.width = paddleWidth + 'px';
    paddleX = (canvas.clientWidth - paddleWidth) / 2;
    paddle.style.left = paddleX + 'px';
    
    gameLoop();
}

function resetPositions() {
    const canvasRect = canvas.getBoundingClientRect();
    paddleX = (canvasRect.width - 80) / 2;
    paddle.style.left = paddleX + 'px';
    paddle.style.bottom = '20px';
}

function createBricks() {
    bricks = [];
    document.querySelectorAll('.brick').forEach(b => b.remove());
    
    const canvasRect = canvas.getBoundingClientRect();
    const brickWidth = (canvasRect.width - (BRICK_COLS + 1) * BRICK_GAP) / BRICK_COLS;
    const brickHeight = 20;
    
    for (let r = 0; r < BRICK_ROWS; r++) {
        for (let c = 0; c < BRICK_COLS; c++) {
            const brick = document.createElement('div');
            brick.className = `brick brick-row-${r}`;
            brick.style.width = brickWidth + 'px';
            brick.style.height = brickHeight + 'px';
            brick.style.left = (BRICK_GAP + c * (brickWidth + BRICK_GAP)) + 'px';
            brick.style.top = (BRICK_GAP + r * (brickHeight + BRICK_GAP)) + 'px';
            
            canvas.appendChild(brick);
            bricks.push({
                element: brick,
                x: BRICK_GAP + c * (brickWidth + BRICK_GAP),
                y: BRICK_GAP + r * (brickHeight + BRICK_GAP),
                width: brickWidth,
                height: brickHeight,
                active: true
            });
        }
    }
}

function resetBall() {
    const canvasRect = canvas.getBoundingClientRect();
    ballX = canvasRect.width / 2 - 7;
    ballY = canvasRect.height - 50;
    
    const angle = (Math.random() * Math.PI / 3) - (Math.PI / 6);
    const speed = 5;
    ballVX = Math.cos(angle) * speed;
    ballVY = -speed;
    
    ball.style.left = ballX + 'px';
    ball.style.top = ballY + 'px';
}

function handleTouchMove(e) {
    if (!gameRunning) return;
    
    e.preventDefault();
    
    const touch = e.touches[0] || e.changedTouches[0];
    const canvasRect = canvas.getBoundingClientRect();
    
    // 터치 위치에 따라 패들 이동
    let touchX = touch.clientX - canvasRect.left;
    paddleX = touchX - paddle.clientWidth / 2;
    
    // 경계 제한
    paddleX = Math.max(0, Math.min(canvasRect.width - paddle.clientWidth, paddleX));
    paddle.style.left = paddleX + 'px';
}

function gameLoop() {
    if (!gameRunning) return;
    
    const canvasRect = canvas.getBoundingClientRect();
    
    // 공 이동
    ballX += ballVX;
    ballY += ballVY;
    
    // 좌우 벽 충돌
    if (ballX <= 0 || ballX >= canvasRect.width - 14) {
        ballVX = -ballVX;
        if (navigator.vibrate) navigator.vibrate(10);
    }
    
    // 위쪽 벽 충돌
    if (ballY <= 0) {
        ballVY = -ballVY;
        if (navigator.vibrate) navigator.vibrate(10);
    }
    
    // 패들 충돌
    const paddleTop = canvasRect.height - 20 - 12;
    if (ballY + 14 >= paddleTop && ballY <= paddleTop + 12 && ballVY > 0) {
        if (ballX + 7 >= paddleX && ballX <= paddleX + paddle.clientWidth) {
            ballVY = -ballVY * 1.02;
            
            // 튕긴 위치에 따라 각도 변경
            const hitPos = (ballX + 7 - paddleX) / paddle.clientWidth;
            ballVX = (hitPos - 0.5) * 10;
            
            ballY = paddleTop - 14;
            
            if (navigator.vibrate) navigator.vibrate(20);
        }
    }
    
    // 공이 아래로 나가면
    if (ballY >= canvasRect.height) {
        lives--;
        document.getElementById('lives').textContent = lives;
        document.getElementById('lives2').textContent = lives;
        
        if (lives <= 0) {
            gameOver();
            return;
        } else {
            resetBall();
        }
    }
    
    // 벽돌 충돌
    bricks.forEach(brick => {
        if (!brick.active) return;
        
        if (ballX + 14 > brick.x && ballX < brick.x + brick.width &&
            ballY + 14 > brick.y && ballY < brick.y + brick.height) {
            
            brick.active = false;
            brick.element.remove();
            
            // 충돌 방향 결정
            const overlapLeft = ballX + 14 - brick.x;
            const overlapRight = brick.x + brick.width - ballX;
            const overlapTop = ballY + 14 - brick.y;
            const overlapBottom = brick.y + brick.height - ballY;
            
            const minOverlapX = Math.min(overlapLeft, overlapRight);
            const minOverlapY = Math.min(overlapTop, overlapBottom);
            
            if (minOverlapX < minOverlapY) {
                ballVX = -ballVX;
            } else {
                ballVY = -ballVY;
            }
            
            score += 10;
            document.getElementById('score').textContent = score;
            
            if (navigator.vibrate) navigator.vibrate(15);
            
            // 모든 벽돌 깨면 승리
            const activeBricks = bricks.filter(b => b.active);
            if (activeBricks.length === 0) {
                gameWon();
                return;
            }
        }
    });
    
    // 렌더링
    ball.style.left = ballX + 'px';
    ball.style.top = ballY + 'px';
    
    animationFrame = requestAnimationFrame(gameLoop);
}

function gameOver() {
    gameRunning = false;
    cancelAnimationFrame(animationFrame);
    
    if (navigator.vibrate) navigator.vibrate([100, 50, 100]);
    
    setTimeout(() => {
        document.getElementById('messageText').innerHTML = `💀 게임 오버!<br><br>점수: ${score}`;
        document.getElementById('gameMessage').classList.add('show');
    }, 300);
}

function gameWon() {
    gameRunning = false;
    cancelAnimationFrame(animationFrame);
    
    if (navigator.vibrate) navigator.vibrate([100, 50, 100]);
    
    setTimeout(() => {
        document.getElementById('messageText').innerHTML = `🎉 클리어!<br><br>점수: ${score}`;
        document.getElementById('gameMessage').classList.add('show');
    }, 300);
}

// 초기화 실행
init();
