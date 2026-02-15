<?php
require_once '../../config.php';
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <title>Turbo Racing - <?= SITE_NAME ?></title>
    <link rel="stylesheet" href="../../css/style.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body { height: 100%; overflow: hidden; }
        body { 
            background: #1a1a2e;
            display: flex;
            flex-direction: column;
            touch-action: manipulation;
            user-select: none;
            font-family: system-ui, sans-serif;
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
        
        .info {
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
            background: linear-gradient(to bottom, #2d5a27 0%, #4a7c42 100%);
            overflow: hidden;
        }
        
        #road {
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            width: 200px;
            height: 100%;
            background: #444;
            border-left: 5px solid #fff;
            border-right: 5px solid #fff;
        }
        
        #road-line {
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            width: 10px;
            height: 200%;
            background: repeating-linear-gradient(to bottom, #fff 0px, #fff 40px, transparent 40px, transparent 80px);
            top: 0;
        }
        
        #player {
            position: absolute;
            bottom: 80px;
            width: 40px;
            height: 70px;
            font-size: 40px;
            text-align: center;
            line-height: 70px;
            z-index: 10;
        }
        
        .obstacle {
            position: absolute;
            width: 40px;
            height: 70px;
            font-size: 35px;
            z-index: 5;
        }
        
        .coin {
            position: absolute;
            font-size: 28px;
            z-index: 4;
        }
        
        .controls {
            display: flex;
            gap: 20px;
            padding: 15px;
            justify-content: center;
            background: rgba(0,0,0,0.3);
        }
        .btn {
            width: 80px;
            height: 80px;
            border: none;
            border-radius: 50%;
            font-size: 32px;
            cursor: pointer;
            background: rgba(255,255,255,0.2);
            color: #fff;
            box-shadow: 0 4px 10px rgba(0,0,0,0.3);
        }
        .btn:active { transform: scale(0.9); background: rgba(255,255,255,0.3); }
        
        .msg {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(0,0,0,0.9);
            color: #fff;
            padding: 30px;
            border-radius: 15px;
            font-size: 20px;
            text-align: center;
            z-index: 2000;
            display: none;
        }
        .msg.show { display: block; }
        
        body.dark-mode { background: #1a1a2e !important; }
        body.dark-mode header { background: #1a1a2e; }
        body.dark-mode .logo { color: #fff; }
        body.dark-mode nav a { color: #ccc; }
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
    
    <div class="info">
        <div class="info-box">점수: <span class="info-value" id="score">0</span></div>
        <div class="info-box">코인: <span class="info-value" id="coins">0</span></div>
        <div class="info-box">레벨: <span class="info-value" id="level">1</span></div>
    </div>
    
    <div id="game-area">
        <div id="road">
            <div id="road-line"></div>
        </div>
        <div id="player">🏎️</div>
    </div>
    
    <div class="controls">
        <button class="btn" onclick="moveLeft()">⬅️</button>
        <button class="btn" onclick="moveRight()">➡️</button>
    </div>
    
    <div class="msg" id="msg">
        <div id="msgText"></div>
        <button class="btn" onclick="startGame()" style="margin-top:15px;">재시작</button>
    </div>
    
    <script>
    const area = document.getElementById('game-area');
    const player = document.getElementById('player');
    const roadLine = document.getElementById('road-line');
    
    let playerX = 50;
    let obstacles = [];
    let coinList = [];
    let score = 0, coinCount = 0;
    let speed = 6;
    let running = false;
    let animId = null;
    let paused = false;
    let level = 1;
    
    function init() {
        document.addEventListener('keydown', handleKey);
        document.addEventListener('keyup', handleKeyUp);
        
        // 터치
        area.addEventListener('touchstart', handleTouch, { passive: false });
        area.addEventListener('touchmove', handleTouch, { passive: false });
        
        if (localStorage.getItem('darkMode') === '1') {
            document.body.classList.add('dark-mode');
        }
        
        showStart();
    }
    
    function showStart() {
        document.getElementById('msgText').innerHTML = '🏎️ 터보 레이싱<br><br>좌우로 이동하세요!';
        document.getElementById('msg').classList.add('show');
    }
    
    function startGame() {
        running = true;
        paused = false;
        score = 0;
        coinCount = 0;
        level = 1;
        speed = 6;
        playerX = 50;
        
        // 기존 제거
        document.querySelectorAll('.obstacle').forEach(el => el.remove());
        document.querySelectorAll('.coin').forEach(el => el.remove());
        obstacles = [];
        coinList = [];
        
        document.getElementById('score').textContent = score;
        document.getElementById('coins').textContent = coinCount;
        document.getElementById('level').textContent = level;
        document.getElementById('msg').classList.remove('show');
        
        player.style.left = playerX + '%';
        
        if (animId) cancelAnimationFrame(animId);
        gameLoop();
    }
    
    function moveLeft() {
        if (!running || paused) return;
        playerX = Math.max(20, playerX - 10);
        player.style.left = playerX + '%';
    }
    
    function moveRight() {
        if (!running || paused) return;
        playerX = Math.min(80, playerX + 10);
        player.style.left = playerX + '%';
    }
    
    let keyLeft = false, keyRight = false;
    
    function handleKey(e) {
        if (e.key === 'ArrowLeft' || e.key === 'a') keyLeft = true;
        if (e.key === 'ArrowRight' || e.key === 'd') keyRight = true;
    }
    
    function handleKeyUp(e) {
        if (e.key === 'ArrowLeft' || e.key === 'a') keyLeft = false;
        if (e.key === 'ArrowRight' || e.key === 'd') keyRight = false;
    }
    
    function handleTouch(e) {
        e.preventDefault();
        if (!running || paused) return;
        
        const rect = area.getBoundingClientRect();
        const x = e.touches[0].clientX - rect.left;
        const mid = rect.width / 2;
        
        if (x < mid - 30) moveLeft();
        else if (x > mid + 30) moveRight();
    }
    
    function spawnObstacle() {
        const types = ['🚗', '🚕', '🚙', '🛻', '🚐'];
        const type = types[Math.floor(Math.random() * types.length)];
        
        const el = document.createElement('div');
        el.className = 'obstacle';
        el.textContent = type;
        el.style.top = '-80px';
        el.style.left = (25 + Math.random() * 50) + '%';
        area.appendChild(el);
        
        obstacles.push({ el: el, x: parseFloat(el.style.left), y: -80 });
    }
    
    function spawnCoin() {
        const el = document.createElement('div');
        el.className = 'coin';
        el.textContent = '🪙';
        el.style.top = '-40px';
        el.style.left = (25 + Math.random() * 50) + '%';
        area.appendChild(el);
        
        coinList.push({ el: el, x: parseFloat(el.style.left), y: -40 });
    }
    
    function gameLoop() {
        if (!running || paused) return;
        
        const w = area.clientWidth;
        const h = area.clientHeight;
        
        // 키보드
        if (keyLeft) moveLeft();
        if (keyRight) moveRight();
        
        // 도로 라인 애니메이션
        roadLine.style.top = (speed * 2) + '%';
        setTimeout(() => { roadLine.style.top = '0'; }, 50);
        
        // 장애물 생성
        if (Math.random() < 0.015 + level * 0.003) {
            spawnObstacle();
        }
        
        // 코인 생성
        if (Math.random() < 0.02) {
            spawnCoin();
        }
        
        // 플레이어 위치
        const pLeft = w * playerX / 100 - 20;
        const pRight = w * playerX / 100 + 20;
        const pTop = h - 150;
        const pBottom = h - 80;
        
        // 장애물 이동
        let newObstacles = [];
        obstacles.forEach(obs => {
            obs.y += speed;
            obs.el.style.top = obs.y + 'px';
            
            // 충돌
            const obsLeft = w * obs.x / 100 - 20;
            const obsRight = w * obs.x / 100 + 20;
            
            if (pRight > obsLeft && pLeft < obsRight && pBottom > obs.y && pTop < obs.y + 70) {
                gameOver();
                return;
            }
            
            // 화면에 있으면 유지
            if (obs.y < h + 50) {
                newObstacles.push(obs);
            } else {
                obs.el.remove();
            }
        });
        obstacles = newObstacles;
        
        // 코인 이동
        let newCoins = [];
        coinList.forEach(c => {
            c.y += speed;
            c.el.style.top = c.y + 'px';
            
            const cLeft = w * c.x / 100 - 15;
            const cRight = w * c.x / 100 + 15;
            
            if (pRight > cLeft && pLeft < cRight && pBottom > c.y && pTop < c.y + 30) {
                coinCount++;
                score += 50;
                document.getElementById('coins').textContent = coinCount;
                document.getElementById('score').textContent = score;
                c.el.remove();
                if (navigator.vibrate) navigator.vibrate(10);
            } else if (c.y < h + 50) {
                newCoins.push(c);
            } else {
                c.el.remove();
            }
        });
        coinList = newCoins;
        
        // 점수
        score++;
        document.getElementById('score').textContent = score;
        
        // 레벨
        if (score > level * 500) {
            level++;
            speed += 0.5;
            document.getElementById('level').textContent = level;
        }
        
        animId = requestAnimationFrame(gameLoop);
    }
    
    function togglePause() {
        if (!running) {
            startGame();
        } else {
            paused = !paused;
            if (paused) {
                document.getElementById('msgText').innerHTML = '⏸️ 일시정지';
                document.getElementById('msg').classList.add('show');
            } else {
                document.getElementById('msg').classList.remove('show');
                gameLoop();
            }
        }
    }
    
    function gameOver() {
        running = false;
        cancelAnimationFrame(animId);
        if (navigator.vibrate) navigator.vibrate(100);
        
        document.getElementById('msgText').innerHTML = '💀 게임 오버!<br>점수: ' + score + '<br>코인: ' + coinCount;
        document.getElementById('msg').classList.add('show');
    }
    
    init();
    </script>
</body>
</html>
