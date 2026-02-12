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
            background: #87CEEB;
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
            background: rgba(0,0,0,0.2);
        }
        .info-box {
            background: rgba(255,255,255,0.2);
            color: #fff;
            padding: 6px 15px;
            border-radius: 6px;
            text-align: center;
            font-size: 12px;
        }
        .info-value { font-size: 16px; font-weight: bold; color: #fff; }
        
        #game-area {
            flex: 1;
            position: relative;
            background: linear-gradient(to bottom, #87CEEB 0%, #87CEEB 65%, #228B22 65%, #006400 100%);
            overflow: hidden;
        }
        
        #ground {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 35%;
        }
        
        #player {
            position: absolute;
            bottom: 35%;
            left: 60px;
            font-size: 45px;
            z-index: 10;
            transition: none;
            animation: run 0.3s infinite alternate;
        }
        #player.jumping {
            animation: jump-anim 0.6s ease-out;
        }
        @keyframes run {
            from { transform: translateY(0); }
            to { transform: translateY(-3px); }
        }
        @keyframes jump-anim {
            0% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-120px) rotate(15deg); }
            100% { transform: translateY(0) rotate(0deg); }
        }
        
        .obstacle {
            position: absolute;
            bottom: 35%;
            font-size: 40px;
            z-index: 5;
        }
        
        .coin {
            position: absolute;
            font-size: 30px;
            z-index: 5;
            animation: spin 0.4s infinite alternate;
        }
        @keyframes spin {
            from { transform: scaleX(1); }
            to { transform: scaleX(0.7); }
        }
        
        .cloud {
            position: absolute;
            font-size: 50px;
            opacity: 0.9;
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
        
        body.dark-mode { background: #1a1a2e !important; color: #fff !important; }
        body.dark-mode header { background: #1a1a2e !important; }
        body.dark-mode .logo { color: #fff !important; }
        body.dark-mode nav a { color: #ccc !important; }
        
        #hint {
            position: absolute;
            bottom: 45%;
            left: 50%;
            transform: translateX(-50%);
            color: #fff;
            font-size: 16px;
            font-weight: bold;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
            z-index: 20;
            animation: pulse 1s infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.6; }
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
        <div id="player">🐻</div>
        <div id="hint">화면을 터치하여 시작!</div>
    </div>
    
    <div class="game-message" id="gameMessage">
        <div id="messageText"></div>
        <button class="btn" onclick="startGame()" style="margin-top:15px;">재시작</button>
    </div>
    
    <script>
    const area = document.getElementById('game-area');
    const player = document.getElementById('player');
    
    let score = 0, dist = 0;
    let isJumping = false, isRunning = false;
    let playerY = 0; // 0 = ground, 1 = jumping
    let velocityY = 0;
    let running = false;
    let obstacles = [], coins = [], clouds = [];
    let speed = 5;
    let animId = null;
    const GRAVITY = 0.8;
    const JUMP_FORCE = -15;
    const GROUND_Y = 35;
    
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
            velocityY = JUMP_FORCE;
            player.classList.add('jumping');
            player.textContent = '🐻';
            if (navigator.vibrate) navigator.vibrate(20);
        }
    }
    
    function startGame() {
        running = true;
        isRunning = true;
        score = 0;
        dist = 0;
        speed = 5;
        isJumping = false;
        playerY = 0;
        velocityY = 0;
        
        obstacles.forEach(o => o.el.remove());
        coins.forEach(c => c.el.remove());
        obstacles = [];
        coins = [];
        
        document.getElementById('score').textContent = score;
        document.getElementById('dist').textContent = dist;
        document.getElementById('gameMessage').classList.remove('show');
        document.getElementById('hint').style.display = 'none';
        
        player.style.bottom = GROUND_Y + '%';
        player.classList.remove('jumping');
        player.textContent = '🐻';
        
        if (animId) cancelAnimationFrame(animId);
        animId = requestAnimationFrame(update);
    }
    
    function createObstacle() {
        const types = ['🐌', '🐸', '🦋', '🐙'];
        const type = types[Math.floor(Math.random() * types.length)];
        
        const el = document.createElement('div');
        el.className = 'obstacle';
        el.textContent = type;
        area.appendChild(el);
        
        obstacles.push({ el, x: area.clientWidth + 50 });
    }
    
    function createCoin() {
        const el = document.createElement('div');
        el.className = 'coin';
        el.textContent = '⭐';
        area.appendChild(el);
        
        coins.push({ el, x: area.clientWidth + 30, y: 40 + Math.random() * 25 });
    }
    
    function createCloud(startX) {
        const el = document.createElement('div');
        el.className = 'cloud';
        el.textContent = '☁️';
        el.style.left = startX + 'px';
        el.style.top = (10 + Math.random() * 25) + '%';
        area.appendChild(el);
        
        clouds.push({ el, x: startX });
    }
    
    function update() {
        if (!running) return;
        
        const w = area.clientWidth;
        const h = area.clientHeight;
        
        // 중력 적용
        if (isJumping) {
            playerY += velocityY;
            velocityY += GRAVITY;
            
            // 착지
            if (playerY >= 0) {
                playerY = 0;
                isJumping = false;
                velocityY = 0;
                player.classList.remove('jumping');
            }
            
            player.style.bottom = (GROUND_Y - Math.abs(playerY)) + '%';
        }
        
        // 거리 증가
        dist++;
        document.getElementById('dist').textContent = dist;
        
        // 점수 증가
        if (dist % 3 === 0) {
            score++;
            document.getElementById('score').textContent = score;
        }
        
        // 속도 증가
        if (dist % 300 === 0 && speed < 12) {
            speed += 0.5;
        }
        
        // 장애물 생성
        if (Math.random() < 0.012 + (speed * 0.0005)) {
            createObstacle();
        }
        
        // 코인 생성
        if (Math.random() < 0.018) {
            createCoin();
        }
        
        // 구름 이동
        clouds.forEach(c => {
            c.x -= speed * 0.2;
            if (c.x < -60) {
                c.x = w + 60;
            }
            c.el.style.left = c.x + 'px';
        });
        
        // 장애물 이동 및 충돌
        obstacles.forEach(o => {
            o.x += speed;
            o.el.style.left = o.x + 'px';
            
            // 충돌 검사
            const playerLeft = 60;
            const playerRight = 100;
            const playerBottom = GROUND_Y + (isJumping ? playerY : 0);
            const playerTop = playerBottom + 40;
            
            const obsLeft = o.x;
            const obsRight = o.x + 40;
            const obsBottom = GROUND_Y;
            const obsTop = GROUND_Y + 40;
            
            if (playerRight > obsLeft && playerLeft < obsRight &&
                playerBottom < obsTop && playerTop > obsBottom) {
                gameOver();
            }
            
            // 제거
            if (o.x > w + 60) {
                o.el.remove();
                obstacles = obstacles.filter(ob => ob !== o);
            }
        });
        
        // 코인 이동 및 수집
        coins.forEach(c => {
            c.x += speed;
            c.el.style.left = c.x + 'px';
            c.el.style.bottom = c.y + '%';
            
            // 충돌
            const playerLeft = 60;
            const playerRight = 100;
            const playerBottom = GROUND_Y + (isJumping ? playerY : 0);
            const playerTop = playerBottom + 40;
            
            const coinLeft = c.x;
            const coinRight = c.x + 30;
            const coinBottom = c.y;
            const coinTop = coinBottom + 30;
            
            if (playerRight > coinLeft && playerLeft < coinRight &&
                playerBottom < coinTop && playerTop > coinBottom) {
                score += 50;
                document.getElementById('score').textContent = score;
                c.el.remove();
                coins = coins.filter(co => co !== c);
                if (navigator.vibrate) navigator.vibrate(15);
            }
            
            if (c.x > w + 40) {
                c.el.remove();
                coins = coins.filter(co => co !== c);
            }
        });
        
        animId = requestAnimationFrame(update);
    }
    
    function gameOver() {
        running = false;
        cancelAnimationFrame(animId);
        
        player.textContent = '😵';
        if (navigator.vibrate) navigator.vibrate([100, 50, 100]);
        
        const best = localStorage.getItem('marioBest') || 0;
        let msg = `💀 게임 오버!<br>점수: ${score}<br>거리: ${dist}m`;
        if (score > best) {
            localStorage.setItem('marioBest', score);
            msg += '<br>🏆 최고 점수!';
        }
        
        document.getElementById('messageText').innerHTML = msg;
        document.getElementById('gameMessage').classList.add('show');
    }
    
    init();
    </script>
</body>
</html>
