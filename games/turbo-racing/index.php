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
        
        #center-line {
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            width: 10px;
            height: 100%;
            background: repeating-linear-gradient(
                to bottom,
                #fff 0px,
                #fff 40px,
                transparent 40px,
                transparent 80px
            );
            animation: roadMove 0.5s linear infinite;
        }
        @keyframes roadMove {
            from { background-position: 0 0; }
            to { background-position: 0 80px; }
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
            transition: left 0.1s;
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
            width: 30px;
            height: 30px;
            font-size: 25px;
            z-index: 4;
        }
        
        .road-mark {
            position: absolute;
            width: 20px;
            height: 4px;
            background: #fff;
            z-index: 3;
        }
        
        .controls {
            display: flex;
            gap: 10px;
            padding: 10px;
            justify-content: center;
            background: rgba(0,0,0,0.2);
        }
        .btn {
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            cursor: pointer;
            background: #8f7a66;
            color: #fff;
        }
        .btn:active { transform: scale(0.95); }
        
        .game-message {
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
        .game-message.show { display: block; }
        
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
            <div id="center-line"></div>
        </div>
        <div id="player">🏎️</div>
    </div>
    
    <div class="controls">
        <button class="btn" onclick="moveLeft()">⬅️ 왼쪽</button>
        <button class="btn" onclick="moveRight()">➡️ 오른쪽</button>
        <button class="btn" onclick="togglePause()">⏸️</button>
    </div>
    
    <div class="game-message" id="gameMessage">
        <div id="messageText"></div>
        <button class="btn" onclick="startGame()" style="margin-top:15px;">재시작</button>
    </div>
    
    <script>
    const gameArea = document.getElementById('game-area');
    const player = document.getElementById('player');
    const road = document.getElementById('road');
    
    let playerX = 50; // %
    let obstacles = [], coins = [], roadMarks = [];
    let score = 0, coinCount = 0;
    let gameSpeed = 5;
    let running = false;
    let animId = null;
    let isPaused = false;
    let level = 1;
    
    function init() {
        // 터치/키보드
        document.addEventListener('keydown', handleKey);
        document.addEventListener('keyup', handleKeyUp);
        
        // 터치 슬라이드
        let startX = 0;
        gameArea.addEventListener('touchstart', (e) => {
            startX = e.touches[0].clientX;
        });
        gameArea.addEventListener('touchmove', (e) => {
            const diff = e.touches[0].clientX - startX;
            if (Math.abs(diff) > 10) {
                if (diff > 0) moveRight();
                else moveLeft();
                startX = e.touches[0].clientX;
            }
        });
        
        if (localStorage.getItem('darkMode') === '1') {
            document.body.classList.add('dark-mode');
        }
        
        showStartScreen();
    }
    
    function showStartScreen() {
        document.getElementById('messageText').innerHTML = '🏎️ 터보 레이싱<br><br>화면을 터치하거나<br>버튼으로 이동하세요!';
        document.getElementById('gameMessage').classList.add('show');
    }
    
    function startGame() {
        running = true;
        score = 0;
        coinCount = 0;
        gameSpeed = 5;
        level = 1;
        playerX = 50;
        obstacles = [];
        coins = [];
        
        document.getElementById('score').textContent = score;
        document.getElementById('coins').textContent = coinCount;
        document.getElementById('level').textContent = level;
        document.getElementById('gameMessage').classList.remove('show');
        
        player.style.left = '50%';
        
        // 도로 마크 생성
        createRoadMarks();
        
        if (animId) cancelAnimationFrame(animId);
        gameLoop();
    }
    
    function moveLeft() {
        if (!running || isPaused) return;
        playerX = Math.max(20, playerX - 8);
        player.style.left = playerX + '%';
    }
    
    function moveRight() {
        if (!running || isPaused) return;
        playerX = Math.min(80, playerX + 8);
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
    
    function createRoadMarks() {
        document.querySelectorAll('.road-mark').forEach(el => el.remove());
        roadMarks = [];
        
        for (let i = 0; i < 10; i++) {
            const mark = document.createElement('div');
            mark.className = 'road-mark';
            mark.style.top = (i * 80) + 'px';
            mark.style.left = '35%';
            gameArea.appendChild(mark);
            roadMarks.push(mark);
        }
    }
    
    function spawnObstacle() {
        const types = ['🚗', '🚕', '🚙', '🛻', '🚐'];
        const type = types[Math.floor(Math.random() * types.length)];
        
        const el = document.createElement('div');
        el.className = 'obstacle';
        el.textContent = type;
        el.style.top = '-80px';
        el.style.left = (25 + Math.random() * 50) + '%';
        gameArea.appendChild(el);
        
        obstacles.push({ el, x: parseFloat(el.style.left), y: -80 });
    }
    
    function spawnCoin() {
        const el = document.createElement('div');
        el.className = 'coin';
        el.textContent = '🪙';
        el.style.top = '-40px';
        el.style.left = (25 + Math.random() * 50) + '%';
        gameArea.appendChild(el);
        
        coins.push({ el, x: parseFloat(el.style.left), y: -40 });
    }
    
    function gameLoop() {
        if (!running || isPaused) return;
        
        const h = gameArea.clientHeight;
        const w = gameArea.clientWidth;
        const roadLeft = w * 0.25;
        const roadWidth = w * 0.5;
        
        // 키보드 입력
        if (keyLeft) moveLeft();
        if (keyRight) moveRight();
        
        // 도로 마크 이동
        roadMarks.forEach(mark => {
            let y = parseFloat(mark.style.top);
            y += gameSpeed * 1.5;
            if (y > h) y = -20;
            mark.style.top = y + 'px';
        });
        
        // 장애물 생성
        if (Math.random() < 0.02 + level * 0.005) {
            spawnObstacle();
        }
        
        // 코인 생성
        if (Math.random() < 0.03) {
            spawnCoin();
        }
        
        // 장애물 이동 및 충돌
        obstacles.forEach((obs, idx) => {
            obs.y += gameSpeed;
            obs.el.style.top = obs.y + 'px';
            
            // 충돌 검사
            const playerRect = {
                left: w * playerX / 100 - 20,
                right: w * playerX / 100 + 20,
                top: h - 150,
                bottom: h - 80
            };
            const obsLeft = roadLeft + obs.x / 100 * roadWidth - 20;
            const obsRight = roadLeft + obs.x / 100 * roadWidth + 20;
            
            if (playerRect.right > obsLeft && playerRect.left < obsRight &&
                playerRect.bottom > obs.y && playerRect.top < obs.y + 70) {
                gameOver();
                return;
            }
            
            // 화면 밖
            if (obs.y > h) {
                obs.el.remove();
                obstacles.splice(idx, 1);
            }
        });
        
        // 코인 수집
        coins.forEach((coin, idx) => {
            coin.y += gameSpeed;
            coin.el.style.top = coin.y + 'px';
            
            const playerRect = {
                left: w * playerX / 100 - 15,
                right: w * playerX / 100 + 15,
                top: h - 150,
                bottom: h - 80
            };
            const coinLeft = roadLeft + coin.x / 100 * roadWidth - 15;
            const coinRight = roadLeft + coin.x / 100 * roadWidth + 15;
            
            if (playerRect.right > coinLeft && playerRect.left < coinRight &&
                playerRect.bottom > coin.y && playerRect.top < coin.y + 30) {
                coinCount++;
                score += 50;
                document.getElementById('coins').textContent = coinCount;
                document.getElementById('score').textContent = score;
                
                coin.el.remove();
                coins.splice(idx, 1);
                
                if (navigator.vibrate) navigator.vibrate(10);
            }
            
            if (coin.y > h) {
                coin.el.remove();
                coins.splice(idx, 1);
            }
        });
        
        // 점수 증가
        if (Math.random() < 0.1) {
            score++;
            document.getElementById('score').textContent = score;
        }
        
        // 레벨 증가
        if (score > level * 500) {
            level++;
            gameSpeed += 0.5;
            document.getElementById('level').textContent = level;
        }
        
        animId = requestAnimationFrame(gameLoop);
    }
    
    function togglePause() {
        if (!running) {
            startGame();
        } else {
            isPaused = !isPaused;
            if (isPaused) {
                document.getElementById('messageText').innerHTML = '⏸️ 일시정지';
                document.getElementById('gameMessage').classList.add('show');
            } else {
                document.getElementById('gameMessage').classList.remove('show');
                gameLoop();
            }
        }
    }
    
    function gameOver() {
        running = false;
        cancelAnimationFrame(animId);
        
        if (navigator.vibrate) navigator.vibrate(100);
        
        document.getElementById('messageText').innerHTML = 
            '💀 게임 오버!<br><br>점수: ' + score + '<br>코인: ' + coinCount + '<br>레벨: ' + level;
        document.getElementById('gameMessage').classList.add('show');
    }
    
    init();
    </script>
</body>
</html>
