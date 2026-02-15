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
            background: linear-gradient(to bottom, #87CEEB 0%, #87CEEB 30%, #228B22 30%, #1a6b1a 100%);
            overflow: hidden;
        }
        
        #road {
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            bottom: 0;
            width: 280px;
            height: 100%;
            background: linear-gradient(to bottom, #555 0%, #444 100%);
            clip-path: polygon(0 0, 100% 0, 85% 100%, 15% 100%);
        }
        
        #road-center {
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            width: 12px;
            height: 200%;
            bottom: 0;
            background: repeating-linear-gradient(to bottom, #fff 0px, #fff 50px, transparent 50px, transparent 100px);
            animation: roadMove 0.3s linear infinite;
        }
        @keyframes roadMove {
            from { transform: translateX(-50%) translateY(0); }
            to { transform: translateX(-50%) translateY(100px); }
        }
        
        #road-shoulder {
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 200%;
            bottom: 0;
            background: repeating-linear-gradient(to bottom, #fff 0px, #fff 30px, transparent 30px, transparent 60px);
            animation: shoulderMove 0.3s linear infinite;
        }
        @keyframes shoulderMove {
            from { transform: translateX(-50%) translateY(0); }
            to { transform: translateX(-50%) translateY(60px); }
        }
        
        #side-deco {
            position: absolute;
            width: 100%;
            height: 100%;
            pointer-events: none;
        }
        
        .tree {
            position: absolute;
            font-size: 40px;
            animation: treeMove 0.3s linear infinite;
        }
        @keyframes treeMove {
            from { transform: translateY(0); }
            to { transform: translateY(120px); }
        }
        
        #player {
            position: absolute;
            bottom: 15px;
            width: 50px;
            height: 80px;
            font-size: 45px;
            text-align: center;
            line-height: 80px;
            z-index: 10;
            filter: drop-shadow(0 5px 10px rgba(0,0,0,0.5));
            animation: carVibrate 0.1s linear infinite;
        }
        @keyframes carVibrate {
            0%, 100% { transform: translateX(0); }
            50% { transform: translateX(0.5px); }
        }
        
        .obstacle {
            position: absolute;
            width: 50px;
            height: 80px;
            font-size: 40px;
            z-index: 5;
            filter: drop-shadow(0 3px 5px rgba(0,0,0,0.3));
        }
        
        .coin {
            position: absolute;
            font-size: 32px;
            z-index: 4;
            animation: coinSpin 0.5s linear infinite;
        }
        @keyframes coinSpin {
            0%, 100% { transform: rotateY(0deg); }
            50% { transform: rotateY(180deg); }
        }
        
        #speed-lines {
            position: absolute;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 20;
            opacity: 0.3;
        }
        
        .speed-line {
            position: absolute;
            width: 2px;
            height: 40px;
            background: linear-gradient(to bottom, transparent, #fff);
            animation: speedLine 0.2s linear infinite;
        }
        @keyframes speedLine {
            from { transform: translateY(-50px); opacity: 1; }
            to { transform: translateY(100vh); opacity: 0; }
        }
        
        .controls {
            display: flex;
            gap: 20px;
            padding: 15px;
            justify-content: center;
            background: rgba(0,0,0,0.4);
        }
        .btn {
            width: 90px;
            height: 90px;
            border: none;
            border-radius: 50%;
            font-size: 38px;
            cursor: pointer;
            background: rgba(255,255,255,0.25);
            color: #fff;
            box-shadow: 0 4px 15px rgba(0,0,0,0.4);
            transition: all 0.1s;
        }
        .btn:active { transform: scale(0.9); background: rgba(255,255,255,0.4); }
        
        .msg {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(0,0,0,0.92);
            color: #fff;
            padding: 35px;
            border-radius: 18px;
            font-size: 22px;
            text-align: center;
            z-index: 2000;
            display: none;
            min-width: 280px;
        }
        .msg.show { display: block; animation: popIn 0.2s ease-out; }
        @keyframes popIn {
            from { transform: translate(-50%, -50%) scale(0.8); opacity: 0; }
            to { transform: translate(-50%, -50%) scale(1); opacity: 1; }
        }
        
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
        <div class="info-box">속도: <span class="info-value" id="speed-val">0</span> km/h</div>
    </div>
    
    <div id="game-area">
        <div id="road">
            <div id="road-center"></div>
            <div id="road-shoulder"></div>
        </div>
        <div id="side-deco"></div>
        <div id="speed-lines"></div>
        <div id="player">🏎️</div>
    </div>
    
    <div class="controls">
        <button class="btn" onmousedown="moveLeft()" ontouchstart="moveLeft()">⬅️</button>
        <button class="btn" onmousedown="moveRight()" ontouchstart="moveRight()">➡️</button>
    </div>
    
    <div class="msg" id="msg">
        <div id="msgText"></div>
        <button class="btn" onclick="startGame()" style="margin-top:20px;width:120px;height:50px;font-size:16px;">재시작</button>
    </div>
    
    <script>
    const area = document.getElementById('game-area');
    const player = document.getElementById('player');
    const roadCenter = document.getElementById('road-center');
    const roadShoulder = document.getElementById('road-shoulder');
    const sideDeco = document.getElementById('side-deco');
    const speedLines = document.getElementById('speed-lines');
    
    let playerX = 50;
    let obstacles = [];
    let coinList = [];
    let score = 0, coinCount = 0;
    let speed = 8;
    let running = false;
    let animId = null;
    let level = 1;
    
    // 트리 위치
    const trees = [];
    const treePositions = [5, 15, 25, 75, 85, 95];
    
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
        document.getElementById('msgText').innerHTML = '🏎️ 터보 레이싱<br><br>🏁 빠른 레이싱을 즐겨보세요!';
        document.getElementById('msg').classList.add('show');
    }
    
    function startGame() {
        running = true;
        score = 0;
        coinCount = 0;
        level = 1;
        speed = 8;
        playerX = 50;
        
        // 기존 제거
        document.querySelectorAll('.obstacle').forEach(el => el.remove());
        document.querySelectorAll('.coin').forEach(el => el.remove());
        document.querySelectorAll('.tree').forEach(el => el.remove());
        obstacles = [];
        coinList = [];
        trees.length = 0;
        
        // 스피드 라인 생성
        createSpeedLines();
        
        document.getElementById('score').textContent = score;
        document.getElementById('coins').textContent = coinCount;
        document.getElementById('level').textContent = level;
        document.getElementById('speed-val').textContent = Math.floor(speed * 15);
        document.getElementById('msg').classList.remove('show');
        
        player.style.left = playerX + '%';
        
        if (animId) cancelAnimationFrame(animId);
        gameLoop();
    }
    
    function moveLeft() {
        if (!running) return;
        playerX = Math.max(25, playerX - 8);
        player.style.left = playerX + '%';
    }
    
    function moveRight() {
        if (!running) return;
        playerX = Math.min(75, playerX + 8);
        player.style.left = playerX + '%';
    }
    
    let keyLeft = false, keyRight = false;
    
    function handleKey(e) {
        if (e.key === 'ArrowLeft' || e.key === 'a') { keyLeft = true; moveLeft(); }
        if (e.key === 'ArrowRight' || e.key === 'd') { keyRight = true; moveRight(); }
    }
    
    function handleKeyUp(e) {
        if (e.key === 'ArrowLeft' || e.key === 'a') keyLeft = false;
        if (e.key === 'ArrowRight' || e.key === 'd') keyRight = false;
    }
    
    function handleTouch(e) {
        e.preventDefault();
        if (!running) return;
        
        const rect = area.getBoundingClientRect();
        const x = e.touches[0].clientX - rect.left;
        const mid = rect.width / 2;
        
        if (x < mid - 40) moveLeft();
        else if (x > mid + 40) moveRight();
    }
    
    function createSpeedLines() {
        speedLines.innerHTML = '';
        for (let i = 0; i < 15; i++) {
            const line = document.createElement('div');
            line.className = 'speed-line';
            line.style.left = Math.random() * 100 + '%';
            line.style.animationDuration = (0.15 + Math.random() * 0.1) + 's';
            speedLines.appendChild(line);
        }
    }
    
    function spawnTree() {
        if (trees.length >= 8) return;
        
        const side = Math.random() > 0.5 ? 'left' : 'right';
        const el = document.createElement('div');
        el.className = 'tree';
        el.textContent = '🌲';
        el.style[side] = '-10px';
        el.style.top = '-50px';
        el.dataset.side = side;
        area.appendChild(el);
        trees.push(el);
    }
    
    function spawnObstacle() {
        const types = ['🚗', '🚕', '🚙', '🛻', '🚐', '🏎️'];
        const type = types[Math.floor(Math.random() * types.length)];
        
        const el = document.createElement('div');
        el.className = 'obstacle';
        el.textContent = type;
        el.style.top = '-90px';
        el.style.left = (30 + Math.random() * 40) + '%';
        area.appendChild(el);
        
        obstacles.push({ el: el, x: parseFloat(el.style.left), y: -90 });
    }
    
    function spawnCoin() {
        const el = document.createElement('div');
        el.className = 'coin';
        el.textContent = '🪙';
        el.style.top = '-40px';
        el.style.left = (30 + Math.random() * 40) + '%';
        area.appendChild(el);
        
        coinList.push({ el: el, x: parseFloat(el.style.left), y: -40 });
    }
    
    function gameLoop() {
        if (!running) return;
        
        const w = area.clientWidth;
        const h = area.clientHeight;
        
        // 애니메이션 속도 조정
        const animDuration = Math.max(0.15, 0.35 - level * 0.02);
        roadCenter.style.animationDuration = animDuration + 's';
        roadShoulder.style.animationDuration = animDuration + 's';
        
        // 트리 생성
        if (Math.random() < 0.1) spawnTree();
        
        // 트리 이동
        trees.forEach((tree, idx) => {
            let y = parseFloat(tree.style.top) || -50;
            y += speed * 1.5;
            tree.style.top = y + 'px';
            if (y > h + 50) {
                tree.remove();
                trees.splice(idx, 1);
            }
        });
        
        // 장애물 생성
        if (Math.random() < 0.02 + level * 0.003) {
            spawnObstacle();
        }
        
        // 코인 생성
        if (Math.random() < 0.025) {
            spawnCoin();
        }
        
        // 플레이어 위치
        const pLeft = w * playerX / 100 - 25;
        const pRight = w * playerX / 100 + 25;
        const pTop = h - 95;
        const pBottom = h - 15;
        
        // 장애물 이동
        let newObstacles = [];
        obstacles.forEach(obs => {
            obs.y += speed;
            obs.el.style.top = obs.y + 'px';
            
            const obsLeft = w * obs.x / 100 - 25;
            const obsRight = w * obs.x / 100 + 25;
            
            if (pRight > obsLeft && pLeft < obsRight && pBottom > obs.y && pTop < obs.y + 80) {
                gameOver();
                return;
            }
            
            if (obs.y < h + 100) {
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
            
            const cLeft = w * c.x / 100 - 16;
            const cRight = w * c.x / 100 + 16;
            
            if (pRight > cLeft && pLeft < cRight && pBottom > c.y && pTop < c.y + 32) {
                coinCount++;
                score += 50;
                document.getElementById('coins').textContent = coinCount;
                document.getElementById('score').textContent = score;
                c.el.remove();
                if (navigator.vibrate) navigator.vibrate(10);
            } else if (c.y < h + 100) {
                newCoins.push(c);
            } else {
                c.el.remove();
            }
        });
        coinList = newCoins;
        
        // 점수
        score++;
        document.getElementById('score').textContent = score;
        document.getElementById('speed-val').textContent = Math.floor(speed * 12);
        
        // 레벨
        if (score > level * 300) {
            level++;
            speed += 1;
            document.getElementById('level').textContent = level;
        }
        
        animId = requestAnimationFrame(gameLoop);
    }
    
    function gameOver() {
        running = false;
        cancelAnimationFrame(animId);
        if (navigator.vibrate) navigator.vibrate(150);
        
        document.getElementById('msgText').innerHTML = 
            '💥 게임 오버!<br><br>🏆 점수: ' + score + '<br>🪙 코인: ' + coinCount + '<br>📊 레벨: ' + level;
        document.getElementById('msg').classList.add('show');
    }
    
    init();
    </script>
</body>
</html>
