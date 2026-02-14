<?php
require_once '../../config.php';
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <title>Snake - <?= SITE_NAME ?></title>
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
            background: #0f0f1a;
            overflow: hidden;
        }
        
        canvas {
            display: block;
            width: 100%;
            height: 100%;
        }
        
        .direction-pad {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 8px;
            padding: 12px;
            background: rgba(0,0,0,0.2);
        }
        .dir-btn {
            background: rgba(255,255,255,0.15);
            border: 2px solid rgba(255,255,255,0.25);
            border-radius: 10px;
            color: #fff;
            font-size: 24px;
            padding: 15px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .dir-btn:active { background: rgba(255,255,255,0.3); }
        
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
    
    <div class="game-info">
        <div class="info-box">점수: <span class="info-value" id="score">0</span></div>
        <div class="info-box">최고: <span class="info-value" id="best">0</span></div>
    </div>
    
    <div id="game-area">
        <canvas id="canvas"></canvas>
    </div>
    
    <div class="direction-pad">
        <div></div>
        <button class="dir-btn" onclick="changeDir('up')">⬆️</button>
        <div></div>
        <button class="dir-btn" onclick="changeDir('left')">⬅️</button>
        <button class="dir-btn" onclick="startGame()">▶️</button>
        <button class="dir-btn" onclick="changeDir('right')">➡️</button>
        <div></div>
        <button class="dir-btn" onclick="changeDir('down')">⬇️</button>
        <div></div>
    </div>
    
    <div class="game-message" id="gameMessage">
        <div id="messageText"></div>
    </div>
    
    <script>
    const canvas = document.getElementById('canvas');
    const ctx = canvas.getContext('2d');
    
    let cellSize = 20;
    let cols = 15;
    let rows = 15;
    let snake = [];
    let food = {x: 0, y: 0, emoji: '🍎'};
    let direction = 'right';
    let nextDirection = 'right';
    let score = 0;
    let best = 0;
    let gameRunning = false;
    let gameLoopId = null;
    
    const foodTypes = ['🍎', '🍊', '🍇', '🍓', '🍒'];
    
    function init() {
        resizeCanvas();
        window.addEventListener('resize', resizeCanvas);
        
        document.addEventListener('keydown', (e) => {
            switch(e.key) {
                case 'ArrowUp': case 'w': case 'W': changeDir('up'); break;
                case 'ArrowDown': case 's': case 'S': changeDir('down'); break;
                case 'ArrowLeft': case 'a': case 'A': changeDir('left'); break;
                case 'ArrowRight': case 'd': case 'D': changeDir('right'); break;
            }
        });
        
        best = parseInt(localStorage.getItem('snakeBest') || '0');
        document.getElementById('best').textContent = best;
        
        if (localStorage.getItem('darkMode') === '1') {
            document.body.classList.add('dark-mode');
            document.querySelector('header').classList.add('dark');
        }
        
        showStartScreen();
    }
    
    function resizeCanvas() {
        const container = document.getElementById('game-area');
        const w = container.clientWidth;
        const h = container.clientHeight;
        
        canvas.width = w;
        canvas.height = h;
        
        cols = Math.floor(w / cellSize);
        rows = Math.floor(h / cellSize);
        
        if (!gameRunning) {
            drawEmpty();
        }
    }
    
    function drawEmpty() {
        ctx.fillStyle = '#0f0f1a';
        ctx.fillRect(0, 0, canvas.width, canvas.height);
        
        ctx.fillStyle = '#333';
        ctx.font = '16px Arial';
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';
        ctx.fillText('▶️ 버튼을 눌러 시작하세요', canvas.width/2, canvas.height/2);
    }
    
    function showStartScreen() {
        drawEmpty();
        document.getElementById('messageText').innerHTML = '🐍 뱀먹기<br><br>▶️ 시작 버튼을 눌러주세요!';
        document.getElementById('gameMessage').classList.add('show');
    }
    
    function changeDir(dir) {
        if (!gameRunning) return;
        
        const opposites = {up:'down', down:'up', left:'right', right:'left'};
        if (dir !== opposites[direction]) {
            nextDirection = dir;
        }
    }
    
    function startGame() {
        // 게임Running 중이면 리턴
        if (gameRunning) return;
        
        gameRunning = true;
        score = 0;
        direction = 'right';
        nextDirection = 'right';
        
        document.getElementById('score').textContent = score;
        document.getElementById('gameMessage').classList.remove('show');
        
        // 뱀 초기화 (중앙)
        snake = [];
        const startX = Math.floor(cols/2);
        const startY = Math.floor(rows/2);
        for (let i = 0; i < 4; i++) {
            snake.push({x: startX - i, y: startY});
        }
        
        spawnFood();
        draw();
        gameLoop();
    }
    
    function spawnFood() {
        food.emoji = foodTypes[Math.floor(Math.random() * foodTypes.length)];
        
        let valid = false;
        while (!valid) {
            food.x = Math.floor(Math.random() * cols);
            food.y = Math.floor(Math.random() * rows);
            
            valid = true;
            for (let s of snake) {
                if (s.x === food.x && s.y === food.y) {
                    valid = false;
                    break;
                }
            }
        }
    }
    
    function gameLoop() {
        if (!gameRunning) return;
        
        direction = nextDirection;
        
        // 새 머리 계산
        const head = {x: snake[0].x, y: snake[0].y};
        
        switch(direction) {
            case 'up': head.y--; break;
            case 'down': head.y++; break;
            case 'left': head.x--; break;
            case 'right': head.x++; break;
        }
        
        // 벽 충돌
        if (head.x < 0 || head.x >= cols || head.y < 0 || head.y >= rows) {
            gameOver();
            return;
        }
        
        // 자기 충돌
        for (let s of snake) {
            if (s.x === head.x && s.y === head.y) {
                gameOver();
                return;
            }
        }
        
        snake.unshift(head);
        
        // 음식 먹음
        if (head.x === food.x && head.y === food.y) {
            score += 10;
            document.getElementById('score').textContent = score;
            spawnFood();
            if (navigator.vibrate) navigator.vibrate(15);
        } else {
            snake.pop();
        }
        
        draw();
        
        // 속도 (점수 따라 빨라짐)
        const speed = Math.max(80, 150 - score);
        gameLoopId = setTimeout(gameLoop, speed);
    }
    
    function draw() {
        // 배경
        ctx.fillStyle = '#0f0f1a';
        ctx.fillRect(0, 0, canvas.width, canvas.height);
        
        // 음식
        ctx.font = (cellSize - 4) + 'px Arial';
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';
        ctx.fillText(food.emoji, food.x * cellSize + cellSize/2, food.y * cellSize + cellSize/2);
        
        // 뱀
        snake.forEach((s, i) => {
            // 색상 (머리는 밝게, 꼬리는 어둡게)
            const green = i === 0 ? 220 : 180 - i * 5;
            ctx.fillStyle = `rgb(50, ${green}, 80)`;
            
            // 둥근 사각형
            const x = s.x * cellSize + 1;
            const y = s.y * cellSize + 1;
            const size = cellSize - 2;
            
            ctx.beginPath();
            ctx.roundRect(x, y, size, size, 4);
            ctx.fill();
        });
    }
    
    function gameOver() {
        gameRunning = false;
        clearTimeout(gameLoopId);
        
        if (navigator.vibrate) navigator.vibrate([100, 50, 100]);
        
        if (score > best) {
            best = score;
            localStorage.setItem('snakeBest', best);
            document.getElementById('best').textContent = best;
        }
        
        document.getElementById('messageText').innerHTML = '💀 게임 오버!<br>점수: ' + score + '<br><br>최고: ' + best + '<br><br>▶️ 다시 시작';
        document.getElementById('gameMessage').classList.add('show');
    }
    
    init();
    </script>
</body>
</html>
