/**
 * Mario Run 게임 로직
 */

let mario = null;
let canvas = null;
let score = 0;
let distance = 0;
let isJumping = false;
let jumpHeight = 0;
let gameRunning = false;
let obstacles = [];
let collectibles = [];
let gameSpeed = 5;
let lastObstacleTime = 0;
let animationFrame = null;

// 초기화
function init() {
    mario = document.getElementById('mario');
    canvas = document.getElementById('game-canvas');
    
    // 터치 이벤트
    canvas.addEventListener('click', jump);
    canvas.addEventListener('touchend', (e) => {
        e.preventDefault();
        jump();
    });
    
    // 키보드 이벤트
    document.addEventListener('keydown', (e) => {
        if (e.code === 'Space' || e.code === 'ArrowUp') {
            e.preventDefault();
            jump();
        }
    });
    
    showStartScreen();
}

// 시작 화면
function showStartScreen() {
    document.getElementById('messageTitle').textContent = '🏃 Mario Run';
    document.getElementById('messageScore').textContent = '탭하여 시작!';
    document.getElementById('gameMessage').classList.add('show');
}

// 게임 시작
function startGame() {
    gameRunning = true;
    score = 0;
    distance = 0;
    isJumping = false;
    jumpHeight = 0;
    gameSpeed = 5;
    obstacles = [];
    collectibles = [];
    
    // 기존 장애물 제거
    document.querySelectorAll('.pipe, .goblin, .mushroom, .star').forEach(el => el.remove());
    
    hideMessage();
    document.getElementById('controlsHint').style.display = 'block';
    
    // 2초 후 힌트 숨김
    setTimeout(() => {
        document.getElementById('controlsHint').style.display = 'none';
    }, 2000);
    
    // 게임 루프 시작
    gameLoop();
}

// 점프
function jump() {
    if (!gameRunning) {
        startGame();
        return;
    }
    
    if (!isJumping) {
        isJumping = true;
        jumpHeight = 0;
        mario.classList.add('jumping');
        
        // 점프 애니메이션
        const jumpDuration = 500;
        const jumpStart = Date.now();
        
        const jumpAnimation = () => {
            const elapsed = Date.now() - jumpStart;
            const progress = elapsed / jumpDuration;
            
            if (progress < 0.5) {
                // 상승
                jumpHeight = Math.sin(progress * Math.PI) * 120;
            } else {
                // 하강
                jumpHeight = Math.sin(progress * Math.PI) * 120;
            }
            
            mario.style.bottom = `calc(25% + ${jumpHeight}px)`;
            
            if (progress < 1) {
                requestAnimationFrame(jumpAnimation);
            } else {
                jumpHeight = 0;
                mario.style.bottom = '25%';
                isJumping = false;
                mario.classList.remove('jumping');
            }
        };
        
        requestAnimationFrame(jumpAnimation);
        
        // 진동
        if (navigator.vibrate) navigator.vibrate(30);
    }
}

// 게임 루프
function gameLoop() {
    if (!gameRunning) return;
    
    // 거리 증가
    distance += 1;
    document.getElementById('distance').textContent = distance;
    
    // 점수 증가 (장애물 회피/폭발당 +10)
    if (distance % 10 === 0) {
        score += 1;
        document.getElementById('score').textContent = score;
    }
    
    // 속도 증가 (100m마다)
    if (distance % 100 === 0 && gameSpeed < 15) {
        gameSpeed += 0.5;
    }
    
    // 장애물 생성
    const now = Date.now();
    const obstacleInterval = Math.max(1500, 3000 - distance * 5);
    
    if (now - lastObstacleTime > obstacleInterval && Math.random() < 0.3) {
        createObstacle();
        lastObstacleTime = now;
    }
    
    // 수집물 생성
    if (Math.random() < 0.02) {
        createCollectible();
    }
    
    // 장애물 이동
    moveObstacles();
    
    // 수집물 이동
    moveCollectibles();
    
    // 충돌 검사
    checkCollisions();
    
    animationFrame = requestAnimationFrame(gameLoop);
}

// 장애물 생성
function createObstacle() {
    const type = Math.random() < 0.5 ? 'pipe' : 'goblin';
    const obstacle = document.createElement('div');
    obstacle.className = type;
    
    if (type === 'pipe') {
        const height = 80 + Math.random() * 60;
        obstacle.style.height = `${height}px`;
        obstacle.style.right = '-60px';
        obstacle.dataset.bottom = '25%';
    } else {
        obstacle.textContent = '👺';
        obstacle.style.right = '-50px';
        obstacle.dataset.bottom = '25%';
    }
    
    canvas.appendChild(obstacle);
    obstacles.push(obstacle);
}

// 장애물 이동
function moveObstacles() {
    obstacles.forEach(obstacle => {
        const currentRight = parseFloat(obstacle.style.right) || 0;
        obstacle.style.right = `${currentRight + gameSpeed}px`;
        
        // 화면 밖으로 나가면 제거
        if (currentRight > canvas.clientWidth + 100) {
            obstacle.remove();
            obstacles = obstacles.filter(o => o !== obstacle);
        }
    });
}

// 수집물 생성
function createCollectible() {
    const type = Math.random() < 0.7 ? 'mushroom' : 'star';
    const collectible = document.createElement('div');
    collectible.className = type;
    
    collectible.textContent = type === 'mushroom' ? '🍄' : '⭐';
    
    // 랜덤 높이
    const height = 25 + Math.random() * 100;
    collectible.style.bottom = `${height}%`;
    collectible.style.right = '-40px';
    collectible.dataset.height = height;
    
    canvas.appendChild(collectible);
    collectibles.push(collectible);
}

// 수집물 이동
function moveCollectibles() {
    collectibles.forEach(c => {
        const currentRight = parseFloat(c.style.right) || 0;
        c.style.right = `${currentRight + gameSpeed}px`;
        
        if (currentRight > canvas.clientWidth + 100) {
            c.remove();
            collectibles = collectibles.filter(item => item !== c);
        }
    });
}

// 충돌 검사
function checkCollisions() {
    const marioRect = mario.getBoundingClientRect();
    
    // 장애물 충돌
    obstacles.forEach(obstacle => {
        const obsRect = obstacle.getBoundingClientRect();
        
        if (isColliding(marioRect, obsRect)) {
            gameOver();
        }
    });
    
    // 수집물 충돌
    collectibles.forEach(c => {
        const colRect = c.getBoundingClientRect();
        
        if (isColliding(marioRect, colRect)) {
            // 수집!
            if (c.classList.contains('mushroom')) {
                score += 50;
            } else {
                score += 100;
            }
            
            // 수집 효과
            c.style.transform = 'scale(1.5)';
            c.style.opacity = '0';
            
            setTimeout(() => {
                c.remove();
            }, 200);
            
            collectibles = collectibles.filter(item => item !== c);
            
            document.getElementById('score').textContent = score;
            
            // 진동
            if (navigator.vibrate) navigator.vibrate(20);
        }
    });
}

// 충돌 확인
function isColliding(rect1, rect2) {
    const padding = 10;
    return !(rect1.right - padding < rect2.left + padding ||
             rect1.left + padding > rect2.right - padding ||
             rect1.bottom - padding < rect2.top + padding ||
             rect1.top + padding > rect2.bottom - padding);
}

// 게임 오버
function gameOver() {
    gameRunning = false;
    cancelAnimationFrame(animationFrame);
    
    // 진동
    if (navigator.vibrate) navigator.vibrate([100, 50, 100]);
    
    // 마리오 뿅!
    mario.textContent = '😵';
    
    setTimeout(() => {
        document.getElementById('messageTitle').textContent = '💀 게임 오버!';
        document.getElementById('messageScore').textContent = `점수: ${score} | 거리: ${distance}m`;
        document.getElementById('gameMessage').classList.add('show');
        
        // 로컬 스토리지에 최고 점수 저장
        const bestScore = localStorage.getItem('marioBestScore') || 0;
        if (score > bestScore) {
            localStorage.setItem('marioBestScore', score);
            document.getElementById('messageScore').innerHTML += '<br>🎉 새 최고 점수!';
        }
    }, 500);
}

// 메시지 숨김
function hideMessage() {
    document.getElementById('gameMessage').classList.remove('show');
}

// 초기화 실행
init();
