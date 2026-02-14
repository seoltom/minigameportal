<?php
require_once '../../config.php';
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <title>Flappy Bird - <?= SITE_NAME ?></title>
    <link rel="stylesheet" href="../../css/style.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body { height: 100%; overflow: hidden; }
        body { 
            background: #70c5ce;
            display: flex;
            flex-direction: column;
            touch-action: manipulation;
            user-select: none;
        }
        header { 
            background: #fff; 
            box-shadow: 0 2px 8px rgba(0,0,0,0.08); 
            position: sticky; 
            top: 0; 
            z-index: 100; 
            flex-shrink: 0;
        }
        .header-content { 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            padding: 8px 12px; 
            max-width: 1200px; 
            margin: 0 auto; 
        }
        .logo { font-size: 15px; font-weight: bold; color: #4f46e5; }
        nav { display: flex; gap: 10px; }
        nav a { font-size: 12px; color: #666; text-decoration: none; }
        
        #game-area {
            flex: 1;
            position: relative;
            background: linear-gradient(to bottom, #70c5ce 0%, #70c5ce 80%, #ded895 80%, #ded895 100%);
            overflow: hidden;
            cursor: pointer;
        }
        
        #bird {
            position: absolute;
            font-size: 35px;
            z-index: 10;
            transition: transform 0.1s;
        }
        
        .pipe {
            position: absolute;
            width: 50px;
            z-index: 5;
        }
        .pipe-top {
            background: linear-gradient(to right, #73bf2e 0%, #9ace5c 100%);
            border-radius: 5px 5px 0 0;
        }
        .pipe-bottom {
            background: linear-gradient(to right, #73bf2e 0%, #9ace5c 100%);
            border-radius: 0 0 5px 5px;
        }
        .pipe-cap {
            width: 60px;
            background: #73bf2e;
            border-radius: 5px;
            position: absolute;
            left: -5px;
        }
        
        .score {
            position: absolute;
            top: 20%;
            left: 50%;
            transform: translateX(-50%);
            font-size: 50px;
            font-weight: bold;
            color: #fff;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
            z-index: 20;
        }
        
        .game-message {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(0,0,0,0.8);
            color: #fff;
            padding: 30px;
            border-radius: 15px;
            font-size: 20px;
            text-align: center;
            z-index: 2000;
            display: none;
        }
        .game-message.show { display: block; }
        
        .cloud {
            position: absolute;
            font-size: 40px;
            opacity: 0.8;
            z-index: 1;
        }
        
        body.dark-mode { background: #1a1a2e !important; color: #fff !important; }
        body.dark-mode header { background: #1a1a2e !important; }
        body.dark-mode .logo { color: #fff !important; }
        body.dark-mode nav a { color: #ccc !important; }
    </style>
</head>
<body>
    <header>
        <div class="header-content">
            <a href="http://tomseol.pe.kr/" class="logo">🎮 <?= SITE_NAME ?></a>
            <nav>
                <a href="http://tomseol.pe.kr/">미니게임</a>
                <a href="http://tomseol.pe.kr/blog/">블로그</a>
            </nav>
        </div>
    </header>
    
    <div id="game-area">
        <div id="bird">🐦</div>
        <div class="score" id="score">0</div>
    </div>
    
    <div class="game-message" id="gameMessage">
        <div id="messageText"></div>
        <button class="btn" onclick="startGame()" style="margin-top:15px;">재시작</button>
    </div>
    
    <script>
    const area = document.getElementById('game-area');
    const bird = document.getElementById('bird');
    
    let birdY = 0, birdVY = 0;
    let pipes = [], clouds = [];
    let score = 0, best = 0;
    let running = false, animId = null;
    let gravity = 0.5, jumpForce = -8;
    let nextPipeTime = 0;
    
    const birds = ['🐦', '🐤', '🕊️'];
    let currentBird = 0;
    
    function init() {
        area.addEventListener('touchstart', handleJump, { passive: false });
        area.addEventListener('mousedown', handleJump);
        document.addEventListener('keydown', (e) => {
            if (e.code === 'Space' || e.code === 'ArrowUp') {
                e.preventDefault();
                handleJump();
            }
        });
        
        // 구름 생성
        for (let i = 0; i < 5; i++) {
            createCloud(Math.random() * area.clientWidth, Math.random() * 60);
        }
        
        showStartScreen();
        
        if (localStorage.getItem('darkMode') === '1') {
            document.body.classList.add('dark-mode');
            document.querySelector('header').classList.add('dark');
        }
    }
    
    function handleJump(e) {
        if (e) e.preventDefault();
        
        if (!running) {
            startGame();
        } else {
            birdVY = jumpForce;
            if (navigator.vibrate) navigator.vibrate(15);
        }
    }
    
    function showStartScreen() {
        document.getElementById('messageText').innerHTML = '🐦 날개짓<br><br>화면을 터치하여 시작!<br><br><small>스페이스 또는 터치</small>';
        document.getElementById('gameMessage').classList.add('show');
        
        best = localStorage.getItem('flappyBest') || 0;
    }
    
    function startGame() {
        running = true;
        score = 0;
        birdY = area.clientHeight * 0.4;
        birdVY = 0;
        currentBird = (currentBird + 1) % birds.length;
        bird.textContent = birds[currentBird];
        
        pipes.forEach(p => { p.top.remove(); p.bottom.remove(); });
        pipes = [];
        
        document.getElementById('score').textContent = score;
        document.getElementById('gameMessage').classList.remove('show');
        
        nextPipeTime = Date.now() + 1500;
        
        if (animId) cancelAnimationFrame(animId);
        animId = requestAnimationFrame(update);
    }
    
    function createPipe() {
        const gap = 140;
        const minHeight = 80;
        const areaH = area.clientHeight * 0.8;
        const topHeight = minHeight + Math.random() * (areaH - gap - minHeight * 2);
        const bottomY = topHeight + gap;
        const bottomH = areaH - bottomY;
        
        const pipeW = 50;
        const x = area.clientWidth + 30;
        
        // 위 파이프
        const topEl = document.createElement('div');
        topEl.className = 'pipe pipe-top';
        topEl.style.width = pipeW + 'px';
        topEl.style.height = topHeight + 'px';
        topEl.style.left = x + 'px';
        topEl.style.top = '0';
        
        // 위 캡
        const topCap = document.createElement('div');
        topCap.className = 'pipe-cap';
        topCap.style.top = (topHeight - 25) + 'px';
        topEl.appendChild(topCap);
        
        area.appendChild(topEl);
        
        // 아래 파이프
        const bottomEl = document.createElement('div');
        bottomEl.className = 'pipe pipe-bottom';
        bottomEl.style.width = pipeW + 'px';
        bottomEl.style.height = bottomH + 'px';
        bottomEl.style.left = x + 'px';
        bottomEl.style.top = bottomY + 'px';
        
        // 아래 캡
        const bottomCap = document.createElement('div');
        bottomCap.className = 'pipe-cap';
        bottomCap.style.bottom = (bottomH - 25) + 'px';
        bottomEl.appendChild(bottomCap);
        
        area.appendChild(bottomEl);
        
        pipes.push({ top: topEl, bottom: bottomEl, x: x, passed: false });
    }
    
    function createCloud(x, y) {
        const el = document.createElement('div');
        el.className = 'cloud';
        el.textContent = '☁️';
        el.style.left = x + 'px';
        el.style.top = y + '%';
        area.appendChild(el);
        
        clouds.push({ el, x: x, speed: 0.5 + Math.random() * 0.5 });
    }
    
    function update() {
        if (!running) return;
        
        const w = area.clientWidth;
        const h = area.clientHeight;
        const groundY = h * 0.8;
        
        // 새 위치
        birdVY += gravity;
        birdY += birdVY;
        
        // 회전
        const rotation = Math.min(Math.max(birdVY * 3, -30), 90);
        bird.style.transform = `rotate(${rotation}deg)`;
        bird.style.top = birdY + 'px';
        
        // 충돌 - 바닥/천장
        if (birdY < 0 || birdY > groundY - 30) {
            gameOver();
            return;
        }
        
        // 파이프 생성
        const now = Date.now();
        if (now > nextPipeTime) {
            createPipe();
            nextPipeTime = now + 2000 - Math.min(score * 10, 800);
        }
        
        // 구름 이동
        clouds.forEach(c => {
            c.x -= c.speed;
            if (c.x < -60) {
                c.x = w + 60;
            }
            c.el.style.left = c.x + 'px';
        });
        
        // 파이프 이동 및 충돌
        pipes.forEach(p => {
            p.x -= 3;
            p.top.style.left = p.x + 'px';
            p.bottom.style.left = p.x + 'px';
            
            // 점수
            if (!p.passed && p.x < 50) {
                p.passed = true;
                score++;
                document.getElementById('score').textContent = score;
                if (navigator.vibrate) navigator.vibrate(10);
            }
            
            // 충돌 검사
            const birdLeft = 50;
            const birdRight = 85;
            const birdTop = birdY;
            const birdBottom = birdY + 30;
            
            const pipeLeft = p.x;
            const pipeRight = p.x + 50;
            const topHeight = parseInt(p.top.style.height);
            
            // 위 파이프 충돌
            if (birdRight > pipeLeft && birdLeft < pipeRight) {
                if (birdTop < topHeight) gameOver();
            }
            
            // 아래 파이프 충돌
            const bottomY = topHeight + 140;
            if (birdRight > pipeLeft && birdLeft < pipeRight) {
                if (birdBottom > bottomY) gameOver();
            }
            
            // 제거
            if (p.x < -60) {
                p.top.remove();
                p.bottom.remove();
            }
        });
        
        pipes = pipes.filter(p => p.x > -60);
        
        animId = requestAnimationFrame(update);
    }
    
    function gameOver() {
        running = false;
        cancelAnimationFrame(animId);
        
        bird.textContent = '💀';
        if (navigator.vibrate) navigator.vibrate([100, 50, 100]);
        
        let msg = '💀 게임 오버!<br>점수: ' + score;
        if (score > best) {
            best = score;
            localStorage.setItem('flappyBest', best);
            msg += '<br>🏆 최고 점수!';
        }
        msg += '<br><br>최고: ' + best;
        
        setTimeout(() => {
            document.getElementById('messageText').innerHTML = msg;
            document.getElementById('gameMessage').classList.add('show');
        }, 500);
    }
    
    init();
    </script>
</body>
</html>
