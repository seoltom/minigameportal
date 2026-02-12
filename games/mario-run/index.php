<?php
require_once '../../config.php';
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <title>Mario Run - <?= SITE_NAME ?></title>
    <link rel="stylesheet" href="../../css/style.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body { height: 100%; overflow: hidden; }
        body { 
            background: #5c94fc;
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
        
        .game-info {
            display: flex;
            gap: 10px;
            padding: 8px 12px;
            justify-content: center;
            background: rgba(0,0,0,0.3);
        }
        .info-box {
            background: rgba(255,255,255,0.1);
            color: #fff;
            padding: 6px 15px;
            border-radius: 6px;
            text-align: center;
            font-size: 12px;
        }
        .info-value { font-size: 16px; font-weight: bold; color: #ffd700; }
        
        #game-area {
            flex: 1;
            position: relative;
            background: linear-gradient(to bottom, #5c94fc 0%, #5c94fc 60%, #8B4513 60%, #8B4513 100%);
            overflow: hidden;
        }
        
        #ground {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 40%;
            background: linear-gradient(to bottom, #654321 0%, #8B4513 100%);
        }
        
        #ground::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 15px;
            background: linear-gradient(to bottom, #228B22 0%, #32CD32 100%);
        }
        
        #mario {
            position: absolute;
            bottom: 40%;
            left: 50px;
            font-size: 40px;
            z-index: 10;
            transition: bottom 0.1s;
        }
        #mario.jumping { bottom: 45% !important; }
        
        .obstacle {
            position: absolute;
            bottom: 40%;
            font-size: 35px;
            z-index: 5;
        }
        
        .coin {
            position: absolute;
            font-size: 25px;
            z-index: 5;
            animation: spin 0.5s infinite;
        }
        @keyframes spin {
            0%, 100% { transform: rotateY(0deg); }
            50% { transform: rotateY(180deg); }
        }
        
        .cloud {
            position: absolute;
            font-size: 40px;
            opacity: 0.8;
        }
        
        .game-message {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(0,0,0,0.9);
            color: #fff;
            padding: 30px;
            border-radius: 12px;
            font-size: 20px;
            text-align: center;
            z-index: 2000;
            display: none;
        }
        .game-message.show { display: block; }
        
        body.dark-mode { background: #1a1a2e !important; color: #fff !important; }
        body.dark-mode header { background: #1a1a2e !important; }
        body.dark-mode .logo { color: #fff !important; }
        body.dark-mode nav a { color: #ccc !important; }
        
        #controls-hint {
            position: absolute;
            bottom: 20%;
            left: 50%;
            transform: translateX(-50%);
            color: #fff;
            font-size: 14px;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
            z-index: 5;
        }
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
    
    <div class="game-info">
        <div class="info-box">점수: <span class="info-value" id="score">0</span></div>
        <div class="info-box">거리: <span class="info-value" id="dist">0</span>m</div>
    </div>
    
    <div id="game-area">
        <div id="ground"></div>
        <div id="mario">🏃</div>
        <div id="controls-hint">화면을 터치하여 점프!</div>
    </div>
    
    <div class="game-message" id="gameMessage">
        <div id="messageText"></div>
        <button class="btn" onclick="startGame()" style="margin-top:15px;">재시작</button>
    </div>
    
    <script>
    const area = document.getElementById('game-area');
    const mario = document.getElementById('mario');
    
    let score = 0, dist = 0;
    let isJumping = false, jumpHeight = 0;
    let running = false;
    let obstacles = [], coins = [], clouds = [];
    let speed = 6;
    let animId = null;
    let jumpTimer = null;
    
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
        for (let i = 0; i < 3; i++) {
            createCloud(Math.random() * area.clientWidth);
        }
        
        if (localStorage.getItem('darkMode') === '1') {
            document.body.classList.add('dark-mode');
            document.querySelector('header').classList.add('dark');
        }
    }
    
    function handleJump(e) {
        if (e) e.preventDefault();
        
        if (!running) {
            startGame();
            return;
        }
        
        if (!isJumping) {
            isJumping = true;
            mario.classList.add('jumping');
            mario.textContent = '🦘';
            
            let jumpProgress = 0;
            const jumpDuration = 400;
            const startTime = Date.now();
            
            jumpTimer = setInterval(() => {
                const elapsed = Date.now() - startTime;
                const progress = elapsed / jumpDuration;
                
                if (progress >= 1) {
                    clearInterval(jumpTimer);
                    isJumping = false;
                    mario.classList.remove('jumping');
                    mario.textContent = '🏃';
                    mario.style.bottom = '40%';
                } else {
                    // 포물선 점프
                    const jumpArc = Math.sin(progress * Math.PI) * 100;
                    mario.style.bottom = (40 + jumpArc / area.clientHeight * 100) + '%';
                }
            }, 16);
            
            if (navigator.vibrate) navigator.vibrate(30);
        }
    }
    
    function startGame() {
        running = true;
        score = 0;
        dist = 0;
        speed = 6;
        
        obstacles.forEach(o => o.el.remove());
        coins.forEach(c => c.el.remove());
        obstacles = [];
        coins = [];
        
        document.getElementById('score').textContent = score;
        document.getElementById('dist').textContent = dist;
        document.getElementById('gameMessage').classList.remove('show');
        document.getElementById('controls-hint').style.display = 'none';
        
        mario.style.bottom = '40%';
        mario.textContent = '🏃';
        
        animId = requestAnimationFrame(update);
    }
    
    function createObstacle() {
        const types = ['🌵', '🪨', '👺'];
        const type = types[Math.floor(Math.random() * types.length)];
        
        const el = document.createElement('div');
        el.className = 'obstacle';
        el.textContent = type;
        el.style.right = '-50px';
        area.appendChild(el);
        
        obstacles.push({ el, x: area.clientWidth + 50 });
    }
    
    function createCoin() {
        const el = document.createElement('div');
        el.className = 'coin';
        el.textContent = '🪙';
        el.style.right = '-30px';
        el.style.bottom = (45 + Math.random() * 30) + '%';
        area.appendChild(el);
        
        coins.push({ el, x: area.clientWidth + 30 });
    }
    
    function createCloud(startX) {
        const el = document.createElement('div');
        el.className = 'cloud';
        el.textContent = '☁️';
        el.style.left = startX + 'px';
        el.style.top = (20 + Math.random() * 30) + '%';
        area.appendChild(el);
        
        clouds.push({ el, x: startX });
    }
    
    function update() {
        if (!running) return;
        
        const w = area.clientWidth;
        const h = area.clientHeight;
        
        // 거리 증가
        dist++;
        document.getElementById('dist').textContent = dist;
        
        // 점수 증가
        if (dist % 5 === 0) {
            score++;
            document.getElementById('score').textContent = score;
        }
        
        // 속도 증가
        if (dist % 200 === 0 && speed < 15) {
            speed += 0.5;
        }
        
        // 장애물 생성
        if (Math.random() < 0.015) {
            createObstacle();
        }
        
        // 코인 생성
        if (Math.random() < 0.02) {
            createCoin();
        }
        
        // 구름 이동
        clouds.forEach(c => {
            c.x -= speed * 0.3;
            if (c.x < -50) {
                c.x = w + 50;
                c.el.style.top = (20 + Math.random() * 30) + '%';
            }
            c.el.style.left = c.x + 'px';
        });
        
        // 장애물 이동
        obstacles.forEach(o => {
            o.x += speed;
            o.el.style.right = (w - o.x) + 'px';
            
            // 충돌 검사
            const marioRect = {
                left: 50,
                right: 90,
                bottom: getMarioBottom(),
                top: getMarioBottom() + 40
            };
            
            const obsLeft = o.x - 35;
            const obsRight = o.x;
            const obsBottom = h * 0.4;
            const obsTop = obsBottom + 35;
            
            if (marioRect.right > obsLeft && marioRect.left < obsRight &&
                marioRect.bottom < obsTop && marioRect.top > obsBottom) {
                gameOver();
            }
            
            // 제거
            if (o.x > w + 100) {
                o.el.remove();
                obstacles = obstacles.filter(ob => ob !== o);
            }
        });
        
        // 코인 이동
        coins.forEach(c => {
            c.x += speed;
            c.el.style.right = (w - c.x) + 'px';
            
            // 충돌 검사
            const marioRect = {
                left: 50,
                right: 90,
                bottom: getMarioBottom(),
                top: getMarioBottom() + 40
            };
            
            const coinLeft = c.x - 20;
            const coinRight = c.x + 10;
            const coinBottom = parseFloat(c.el.style.bottom) / 100 * h;
            const coinTop = coinBottom + 25;
            
            if (marioRect.right > coinLeft && marioRect.left < coinRight &&
                marioRect.bottom < coinTop && marioRect.top > coinBottom) {
                score += 50;
                document.getElementById('score').textContent = score;
                c.el.remove();
                coins = coins.filter(co => co !== c);
                if (navigator.vibrate) navigator.vibrate(20);
            }
            
            if (c.x > w + 100) {
                c.el.remove();
                coins = coins.filter(co => co !== c);
            }
        });
        
        animId = requestAnimationFrame(update);
    }
    
    function getMarioBottom() {
        return h * 0.4;
    }
    
    function gameOver() {
        running = false;
        cancelAnimationFrame(animId);
        clearInterval(jumpTimer);
        
        mario.textContent = '😵';
        if (navigator.vibrate) navigator.vibrate([100, 50, 100]);
        
        const best = localStorage.getItem('marioBest') || 0;
        let msg = `💀 게임 오버!<br>점수: ${score}<br>거리: ${dist}m`;
        if (score > best) {
            localStorage.setItem('marioBest', score);
            msg += '<br>🎉 최고 점수!';
        }
        
        document.getElementById('messageText').innerHTML = msg;
        document.getElementById('gameMessage').classList.add('show');
    }
    
    init();
    </script>
</body>
</html>
