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
            background: linear-gradient(to bottom, #70c5ce 0%, #70c5ce 75%, #ded895 75%, #ded895 100%);
            overflow: hidden;
            cursor: pointer;
        }
        
        #bird {
            position: absolute;
            width: 35px;
            height: 35px;
            line-height: 35px;
            text-align: center;
            font-size: 30px;
            z-index: 10;
            transform-origin: center;
        }
        
        .pipe {
            position: absolute;
            width: 55px;
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
        
        .score {
            position: absolute;
            top: 15%;
            left: 50%;
            transform: translateX(-50%);
            font-size: 55px;
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
            background: rgba(0,0,0,0.85);
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
            font-size: 45px;
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
    
    let birdX = 80;
    let birdY = 200;
    let birdVY = 0;
    let pipes = [], clouds = [];
    let score = 0, best = 0;
    let running = false, animId = null;
    let gravity = 0.4;
    let jumpForce = -7;
    let pipeSpeed = 3;
    let nextPipeTime = 0;
    
    const birdEmojis = ['🐦', '🐤', '🕊️'];
    let currentBirdIdx = 0;
    
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
        for (let i = 0; i < 4; i++) {
            createCloud(Math.random() * area.clientWidth, Math.random() * 50);
        }
        
        showStartScreen();
        
        best = localStorage.getItem('flappyBest') || 0;
        
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
            if (navigator.vibrate) navigator.vibrate(10);
        }
    }
    
    function showStartScreen() {
        birdX = 80;
        birdY = area.clientHeight * 0.4;
        bird.style.left = birdX + 'px';
        bird.style.top = birdY + 'px';
        bird.style.transform = 'rotate(0deg)';
        
        document.getElementById('messageText').innerHTML = '🐦 날개짓<br><br>화면을 터치하여 시작!';
        document.getElementById('gameMessage').classList.add('show');
    }
    
    function startGame() {
        running = true;
        score = 0;
        birdY = area.clientHeight * 0.4;
        birdVY = 0;
        currentBirdIdx = (currentBirdIdx + 1) % birdEmojis.length;
        bird.textContent = birdEmojis[currentBirdIdx];
        
        pipes.forEach(p => { p.topEl.remove(); p.bottomEl.remove(); });
        pipes = [];
        
        document.getElementById('score').textContent = score;
        document.getElementById('gameMessage').classList.remove('show');
        
        nextPipeTime = Date.now() + 1500;
        
        if (animId) cancelAnimationFrame(animId);
        animId = requestAnimationFrame(update);
    }
    
    function createPipe() {
        const gap = 150;
        const areaH = area.clientHeight * 0.75;
        const minPipe = 60;
        const maxPipe = areaH - gap - minPipe;
        
        const topHeight = minPipe + Math.random() * (maxPipe - minPipe);
        const bottomY = topHeight + gap;
        const bottomH = areaH - bottomY;
        
        const pipeX = area.clientWidth + 20;
        const pipeW = 55;
        
        // 위 파이프
        const topEl = document.createElement('div');
        topEl.className = 'pipe pipe-top';
        topEl.style.height = topHeight + 'px';
        topEl.style.left = pipeX + 'px';
        topEl.style.top = '0';
        area.appendChild(topEl);
        
        // 아래 파이프
        const bottomEl = document.createElement('div');
        bottomEl.className = 'pipe pipe-bottom';
        bottomEl.style.height = bottomH + 'px';
        bottomEl.style.left = pipeX + 'px';
        bottomEl.style.top = bottomY + 'px';
        area.appendChild(bottomEl);
        
        pipes.push({ 
            x: pipeX, 
            width: pipeW,
            topHeight: topHeight,
            bottomY: bottomY,
            topEl: topEl,
            bottomEl: bottomEl,
            passed: false 
        });
    }
    
    function createCloud(x, y) {
        const el = document.createElement('div');
        el.className = 'cloud';
        el.textContent = '☁️';
        el.style.left = x + 'px';
        el.style.top = y + '%';
        area.appendChild(el);
        
        clouds.push({ el: el, x: x, speed: 0.3 + Math.random() * 0.3 });
    }
    
    function update() {
        if (!running) return;
        
        const w = area.clientWidth;
        const h = area.clientHeight;
        const groundY = h * 0.75;
        const birdSize = 30;
        
        // 새 이동
        birdVY += gravity;
        birdY += birdVY;
        
        // 새 회전
        const rotation = Math.min(Math.max(birdVY * 2.5, -25), 90);
        bird.style.transform = `rotate(${rotation}deg)`;
        bird.style.top = birdY + 'px';
        
        // 바닥/천장 충돌
        if (birdY < 0 || birdY + birdSize > groundY) {
            gameOver();
            return;
        }
        
        // 파이프 생성
        const now = Date.now();
        if (now > nextPipeTime) {
            createPipe();
            nextPipeTime = now + 1800 - Math.min(score * 15, 700);
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
            p.x -= pipeSpeed;
            p.topEl.style.left = p.x + 'px';
            p.bottomEl.style.left = p.x + 'px';
            
            // 점수
            if (!p.passed && p.x + p.width < birdX) {
                p.passed = true;
                score++;
                document.getElementById('score').textContent = score;
                if (navigator.vibrate) navigator.vibrate(8);
            }
            
            // 충돌 검사
            const birdLeft = birdX + 5;
            const birdRight = birdX + birdSize - 5;
            const birdTop = birdY + 5;
            const birdBottom = birdY + birdSize - 5;
            
            const pipeLeft = p.x;
            const pipeRight = p.x + p.width;
            
            // 새가 파이프 범위 내에 있음
            if (birdRight > pipeLeft && birdLeft < pipeRight) {
                // 위 파이프와 충돌
                if (birdTop < p.topHeight) {
                    gameOver();
                    return;
                }
                // 아래 파이프와 충돌
                if (birdBottom > p.bottomY) {
                    gameOver();
                    return;
                }
            }
            
            // 화면 밖 파이프 제거
            if (p.x < -p.width) {
                p.topEl.remove();
                p.bottomEl.remove();
            }
        });
        
        // 오래된 파이프 정리
        pipes = pipes.filter(p => p.x > -100);
        
        animId = requestAnimationFrame(update);
    }
    
    function gameOver() {
        running = false;
        cancelAnimationFrame(animId);
        
        bird.textContent = '💀';
        if (navigator.vibrate) navigator.vibrate([80, 40, 80]);
        
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
        }, 400);
    }
    
    init();
    </script>
</body>
</html>
