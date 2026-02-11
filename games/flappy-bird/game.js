/**
// v=20260210 - cache bust
 * Flappy Bird 게임 로직
 */

let canvas = null;
let bird = null;
let score = 0;
let gameRunning = false;
let gameStarted = false;
let birdY = 0;
let birdVelocity = 0;
let pipes = [];
let gameSpeed = 3;
let animationFrame = null;
let gravity = 0.5;

function init() {
    canvas = document.getElementById('game-canvas');
    bird = document.getElementById('bird');
    
    // 터치 이벤트
    canvas.addEventListener('click', jump);
    canvas.addEventListener('touchend', (e) => {
        e.preventDefault();
        jump();
    });
    
    showStartScreen();
}

function showStartScreen() {
    birdY = canvas.clientHeight * 0.4;
    bird.style.top = birdY + 'px';
    bird.style.left = '50px';
    document.getElementById('hint').style.display = 'block';
    document.getElementById('gameMessage').classList.add('show');
    document.getElementById('messageText').innerHTML = '🐦 Flappy Bird<br><br>👆 탭하여 시작!';
}

function startGame() {
    gameRunning = true;
    gameStarted = true;
    score = 0;
    birdVelocity = 0;
    birdY = canvas.clientHeight * 0.4;
    pipes = [];
    
    document.getElementById('score').textContent = score;
    document.getElementById('gameMessage').classList.remove('show');
    document.getElementById('hint').style.display = 'none';
    
    // 기존 파이프 제거
    document.querySelectorAll('.pipe').forEach(p => p.remove());
    
    gameLoop();
}

function jump() {
    if (!gameStarted) {
        startGame();
        return;
    }
    
    if (gameRunning) {
        birdVelocity = -8;
        bird.classList.add('jump');
        setTimeout(() => bird.classList.remove('jump'), 300);
        
        if (navigator.vibrate) navigator.vibrate(20);
    }
}

function gameLoop() {
    if (!gameRunning) return;
    
    // 중력 적용
    birdVelocity += gravity;
    birdY += birdVelocity;
    
    // 파이프 생성
    if (Math.random() < 0.015) {
        createPipe();
    }
    
    // 파이프 이동
    movePipes();
    
    // 파이프 충돌 검사
    checkCollisions();
    
    // 새벽 위치 업데이트
    bird.style.top = birdY + 'px';
    bird.style.transform = `rotate(${Math.min(Math.max(birdVelocity * 3, -30), 90)}deg)`;
    
    animationFrame = requestAnimationFrame(gameLoop);
}

function createPipe() {
    const gap = 140;
    const minPipeHeight = 50;
    const canvasHeight = canvas.clientHeight;
    const pipeHeight = Math.floor(Math.random() * (canvasHeight - gap - minPipeHeight * 2)) + minPipeHeight;
    
    const topPipe = document.createElement('div');
    topPipe.className = 'pipe pipe-top';
    topPipe.style.height = pipeHeight + 'px';
    topPipe.style.right = '-60px';
    topPipe.style.top = '0';
    
    const bottomPipe = document.createElement('div');
    bottomPipe.className = 'pipe pipe-bottom';
    bottomPipe.style.height = (canvasHeight * 0.6 - pipeHeight - gap + 20) + 'px';
    bottomPipe.style.right = '-60px';
    bottomPipe.style.bottom = '0';
    
    canvas.appendChild(topPipe);
    canvas.appendChild(bottomPipe);
    
    pipes.push({ top: topPipe, bottom: bottomPipe, passed: false });
}

function movePipes() {
    const canvasWidth = canvas.clientWidth;
    
    pipes.forEach((pipe, index) => {
        const currentRight = parseFloat(pipe.top.style.right) || 0;
        const newRight = currentRight + gameSpeed;
        
        pipe.top.style.right = newRight + 'px';
        pipe.bottom.style.right = newRight + 'px';
        
        // 점수 증가
        if (!pipe.passed && newRight > 100) {
            pipe.passed = true;
            score++;
            document.getElementById('score').textContent = score;
            
            if (navigator.vibrate) navigator.vibrate(15);
        }
        
        // 화면 밖으로 나가면 제거
        if (newRight > canvasWidth + 100) {
            pipe.top.remove();
            pipe.bottom.remove();
            pipes.splice(index, 1);
        }
    });
}

function checkCollisions() {
    const birdRect = bird.getBoundingClientRect();
    const canvasRect = canvas.getBoundingClientRect();
    
    // 새벽이 화면 밖으로 나가면
    if (birdY <= 0 || birdY >= canvas.clientHeight - 40) {
        gameOver();
        return;
    }
    
    // 파이프 충돌
    pipes.forEach(pipe => {
        const topRect = pipe.top.getBoundingClientRect();
        const bottomRect = pipe.bottom.getBoundingClientRect();
        
        // 위 파이프 충돌
        if (birdRect.right > topRect.left + 5 && birdRect.left < topRect.right - 5) {
            if (birdRect.top < topRect.bottom) {
                gameOver();
            }
        }
        
        // 아래 파이프 충돌
        if (birdRect.right > bottomRect.left + 5 && birdRect.left < bottomRect.right - 5) {
            if (birdRect.bottom > bottomRect.top) {
                gameOver();
            }
        }
    });
}

function gameOver() {
    gameRunning = false;
    cancelAnimationFrame(animationFrame);
    
    if (navigator.vibrate) navigator.vibrate([100, 50, 100]);
    
    bird.textContent = '😵';
    
    setTimeout(() => {
        const bestScore = localStorage.getItem('flappyBestScore') || 0;
        if (score > bestScore) {
            localStorage.setItem('flappyBestScore', score);
            document.getElementById('messageText').innerHTML = `💀 게임 오버!<br><br>점수: ${score}<br>🎉 새 최고 기록!`;
        } else {
            document.getElementById('messageText').innerHTML = `💀 게임 오버!<br><br>점수: ${score}<br>최고 기록: ${bestScore}`;
        }
        document.getElementById('gameMessage').classList.add('show');
        bird.textContent = '🐤';
    }, 500);
}

// 초기화 실행
init();
