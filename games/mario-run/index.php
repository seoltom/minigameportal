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
            background: linear-gradient(to bottom, #87CEEB 0%, #87CEEB 60%, #228B22 60%, #32CD32 100%);
            overflow: hidden;
            cursor: pointer;
        }
        
        #player {
            position: absolute;
            bottom: 40%;
            left: 50px;
            font-size: 50px;
            z-index: 10;
        }
        #player.jumping {
            animation: jump-anim 0.5s ease-out;
        }
        @keyframes jump-anim {
            0% { bottom: 40%; transform: rotate(0deg); }
            50% { bottom: 65%; transform: rotate(20deg); }
            100% { bottom: 40%; transform: rotate(0deg); }
        }
        
        .obstacle {
            position: absolute;
            bottom: 40%;
            font-size: 45px;
            z-index: 5;
        }
        
        .coin {
            position: absolute;
            font-size: 35px;
            z-index: 5;
        }
        
        .cloud {
            position: absolute;
            font-size: 55px;
            z-index: 1;
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
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: #fff;
            font-size: 18px;
            font-weight: bold;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
            z-index: 20;
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
    let isJumping = false, running = false;
    let obstacles = [], coins = [], clouds = [];
    let speed = 6;
    let animId = null;
    let lastObstacleTime = 0;
    
    function init() {
        // 터치 이벤트
        area.addEventListener('touchstart', (e) => {
            e.preventDefault();
            handleAction();
        }, { passive: false });
        
        // 마우스 클릭
        area.addEventListener('mousedown', (e) => {
            e.preventDefault();
            handleAction();
        });
        
        // 키보드
        document.addEventListener('keydown', (e) => {
            if (e.code === 'Space' || e.code === 'ArrowUp') {
                e.preventDefault();
                handleAction();
            }
        });
        
        // 구름 생성
        for (let i = 0; i < 5; i++) {
            createCloud(Math.random() * window.innerWidth);
        }
        
        if (localStorage.getItem('darkMode') === '1') {
            document.body.classList.add('dark-mode');
            document.querySelector('header').classList.add('dark');
        }
    }
    
    function handleAction() {
        if (!running) {
            startGame();
        } else if (!isJumping) {
            jump();
        }
    }
    
    function jump() {
        isJumping = true;
        player.classList.add('jumping');
        player.textContent = '🐻';
        
        setTimeout(() => {
            isJumping = false;
            player.classList.remove('jumping');
        }, 500);
        
        if (navigator.vibrate) navigator.vibrate(20);
    }
    
    function startGame() {
        running = true;
        score = 0;
        dist = 0;
        speed = 6;
        isJumping = false;
        lastObstacleTime = Date.now();
        
        obstacles.forEach(o => o.el.remove());
        coins.forEach(c => c.el.remove());
        obstacles = [];
        coins = [];
        
        document.getElementById('score').textContent = score;
        document.getElementById('dist').textContent = dist;
        document.getElementById('gameMessage').classList.remove('show');
        document.getElementById('hint').style.display = 'none';
        
        player.style.bottom = '40%';
        player.classList.remove('jumping');
        
        if (animId) cancelAnimationFrame(animId);
        animId = requestAnimationFrame(update);
    }
    
    function createObstacle() {
        const types = ['🐌', '🐸', '🦋', '🐙'];
        const type = types[Math.floor(Math.random() * types.length)];
        
        const el = document.createElement('div');
        el.className = 'obstacle';
        el.textContent = type;
        el.style.left = area.clientWidth + 'px';
        area.appendChild(el);
        
        obstacles.push({ el, x: area.clientWidth });
    }
    
    function createCoin() {
        const el = document.createElement('div');
        el.className = 'coin';
        el.textContent = '⭐';
        el.style.left = area.clientWidth + 'px';
        el.style.bottom = (45 + Math.random() * 25) + '%';
        area.appendChild(el);
        
        coins.push({ el, x: area.clientWidth });
    }
    
    function createCloud(x) {
        const el = document.createElement('div');
        el.className = 'cloud';
        el.textContent = '☁️';
        el.style.left = x + 'px';
        el.style.top = (10 + Math.random() * 30) + '%';
        area.appendChild(el);
        
        clouds.push({ el, x: x });
    }
    
    function update() {
        if (!running) return;
        
        const w = area.clientWidth;
        
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
        
        // 장애물 생성 (더 자주)
        const now = Date.now();
        if (now - lastObstacleTime > 1500 && Math.random() < 0.7) {
            createObstacle();
            lastObstacleTime = now;
        }
        
        // 코인 생성
        if (Math.random() < 0.03) {
            createCoin();
        }
        
        // 구름 이동
        clouds.forEach(c => {
            c.x -= speed * 0.3;
            if (c.x < -60) {
                c.x = w + 60;
                c.el.style.top = (10 + Math.random() * 30) + '%';
            }
            c.el.style.left = c.x + 'px';
        });
        
        // 장애물 이동
        obstacles.forEach(o => {
            o.x += speed;
            o.el.style.left = o.x + 'px';
            
            // 충돌 검사
            const pLeft = 50;
            const pRight = 90;
            const pBottom = 40;
            const pTop = 80;
            
            const oLeft = o.x;
            const oRight = o.x + 40;
            
            if (pRight > oLeft && pLeft < oRight) {
                gameOver();
            }
            
            // 제거
            if (o.x > w + 50) {
                o.el.remove();
                obstacles = obstacles.filter(ob => ob !== o);
            }
        });
        
        // 코인 이동
        coins.forEach(c => {
            c.x += speed;
            c.el.style.left = c.x + 'px';
            
            // 수집
            const pLeft = 50;
            const pRight = 90;
            
            if (pRight > c.x && pLeft < c.x + 30) {
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
