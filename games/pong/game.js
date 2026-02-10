/**
 * Pong 게임 로직
 */

let canvas = null;
let playerPaddle = null;
let cpuPaddle = null;
let ball = null;
let playerY = 0;
let cpuY = 0;
let ballX = 0;
let ballY = 0;
let ballVX = 0;
let ballVY = 0;
let playerScore = 0;
let cpuScore = 0;
let gameRunning = false;
let animationFrame = null;

const PADDLE_HEIGHT = 60;
const PADDLE_WIDTH = 8;
const BALL_SIZE = 16;
const CPU_SPEED = 4;

function init() {
    canvas = document.getElementById('game-canvas');
    playerPaddle = document.getElementById('playerPaddle');
    cpuPaddle = document.getElementById('cpuPaddle');
    ball = document.getElementById('ball');
    
    // 터치 이벤트
    canvas.addEventListener('touchmove', handleTouchMove);
    canvas.addEventListener('touchstart', handleTouchMove);
    
    // 클릭 이벤트
    canvas.addEventListener('click', () => {
        if (!gameRunning) startGame();
    });
    
    showStartScreen();
}

function showStartScreen() {
    document.getElementById('messageText').innerHTML = '🏓 Pong<br><br>화면을 터치하여 시작!';
    document.getElementById('gameMessage').classList.add('show');
    
    // 초기 위치 설정
    const canvasRect = canvas.getBoundingClientRect();
    playerY = canvasRect.height / 2 - PADDLE_HEIGHT / 2;
    cpuY = canvasRect.height / 2 - PADDLE_HEIGHT / 2;
    
    updatePaddles();
}

function startGame() {
    playerScore = 0;
    cpuScore = 0;
    gameRunning = true;
    
    document.getElementById('playerScore').textContent = 0;
    document.getElementById('cpuScore').textContent = 0;
    document.getElementById('gameMessage').classList.remove('show');
    
    resetBall();
    gameLoop();
}

function handleTouchMove(e) {
    if (!gameRunning) return;
    
    e.preventDefault();
    
    const touch = e.touches[0] || e.changedTouches[0];
    const canvasRect = canvas.getBoundingClientRect();
    
    // 터치 위치에 따라 플레이어 라켓 이동
    let touchY = touch.clientY - canvasRect.top;
    playerY = touchY - PADDLE_HEIGHT / 2;
    
    // 경계 제한
    const maxY = canvasRect.height - PADDLE_HEIGHT;
    playerY = Math.max(0, Math.min(maxY, playerY));
    
    updatePaddles();
}

function updatePaddles() {
    const canvasRect = canvas.getBoundingClientRect();
    
    playerPaddle.style.top = playerY + 'px';
    cpuPaddle.style.top = cpuY + 'px';
}

function resetBall() {
    const canvasRect = canvas.getBoundingClientRect();
    
    ballX = canvasRect.width / 2 - BALL_SIZE / 2;
    ballY = canvasRect.height / 2 - BALL_SIZE / 2;
    
    // 공 방향 랜덤
    const angle = (Math.random() * Math.PI / 2) - (Math.PI / 4);
    const speed = 5;
    ballVX = Math.cos(angle) * speed * (Math.random() < 0.5 ? 1 : -1);
    ballVY = Math.sin(angle) * speed;
    
    ball.style.left = ballX + 'px';
    ball.style.top = ballY + 'px';
}

function gameLoop() {
    if (!gameRunning) return;
    
    const canvasRect = canvas.getBoundingClientRect();
    
    // 공 이동
    ballX += ballVX;
    ballY += ballVY;
    
    // 상하 벽 충돌
    if (ballY <= 0 || ballY >= canvasRect.height - BALL_SIZE) {
        ballVY = -ballVY;
        if (navigator.vibrate) navigator.vibrate(10);
    }
    
    // 플레이어 라켓 충돌
    if (ballX <= PADDLE_WIDTH + 10 && ballVX < 0) {
        const paddleTop = playerY;
        const paddleBottom = playerY + PADDLE_HEIGHT;
        
        if (ballY + BALL_SIZE >= paddleTop && ballY <= paddleBottom) {
            ballVX = -ballVX * 1.05; // 속도 증가
            ballX = PADDLE_WIDTH + 12;
            
            // 튕긴 위치에 따라 각도 변경
            const hitPos = (ballY + BALL_SIZE/2 - paddleTop) / PADDLE_HEIGHT;
            ballVY = (hitPos - 0.5) * 8;
            
            if (navigator.vibrate) navigator.vibrate(20);
        }
    }
    
    // CPU 라켓 충돌
    if (ballX >= canvasRect.width - PADDLE_WIDTH - BALL_SIZE - 10 && ballVX > 0) {
        const paddleTop = cpuY;
        const paddleBottom = cpuY + PADDLE_HEIGHT;
        
        if (ballY + BALL_SIZE >= paddleTop && ballY <= paddleBottom) {
            ballVX = -ballVX * 1.05;
            ballX = canvasRect.width - PADDLE_WIDTH - BALL_SIZE - 12;
            
            const hitPos = (ballY + BALL_SIZE/2 - paddleTop) / PADDLE_HEIGHT;
            ballVY = (hitPos - 0.5) * 8;
            
            if (navigator.vibrate) navigator.vibrate(20);
        }
    }
    
    // CPU AI
    const paddleCenter = cpuY + PADDLE_HEIGHT / 2;
    const targetY = ballY + BALL_SIZE / 2;
    
    if (paddleCenter < targetY - 20) {
        cpuY += CPU_SPEED;
    } else if (paddleCenter > targetY + 20) {
        cpuY -= CPU_SPEED;
    }
    
    // CPU 경계 제한
    cpuY = Math.max(0, Math.min(canvasRect.height - PADDLE_HEIGHT, cpuY));
    
    // 점수
    if (ballX < -BALL_SIZE) {
        cpuScore++;
        document.getElementById('cpuScore').textContent = cpuScore;
        checkGameEnd();
        if (gameRunning) resetBall();
    } else if (ballX > canvasRect.width) {
        playerScore++;
        document.getElementById('playerScore').textContent = playerScore;
        checkGameEnd();
        if (gameRunning) resetBall();
    }
    
    // 렌더링
    ball.style.left = ballX + 'px';
    ball.style.top = ballY + 'px';
    updatePaddles();
    
    animationFrame = requestAnimationFrame(gameLoop);
}

function checkGameEnd() {
    // 5점先勝
    if (playerScore >= 5 || cpuScore >= 5) {
        gameOver();
    }
}

function gameOver() {
    gameRunning = false;
    cancelAnimationFrame(animationFrame);
    
    if (navigator.vibrate) navigator.vibrate([100, 50, 100]);
    
    const winner = playerScore >= 5 ? '🎉 승리!' : '💀 패배...';
    
    setTimeout(() => {
        document.getElementById('messageText').innerHTML = `${winner}<br><br>${playerScore} - ${cpuScore}`;
        document.getElementById('gameMessage').classList.add('show');
    }, 300);
}

// 초기화 실행
init();
