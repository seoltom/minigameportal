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
        
        .controls {
            display: flex;
            gap: 8px;
            padding: 8px 12px;
            justify-content: center;
            flex-wrap: wrap;
        }
        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            font-size: 12px;
            cursor: pointer;
            background: #8f7a66;
            color: #fff;
        }
        
        #game-area {
            flex: 1;
            position: relative;
            background: #0a0a15;
            overflow: hidden;
        }
        
        #snake-canvas {
            display: block;
        }
        
        .direction-pad {
            display: grid;
            grid-template-columns: repeat(3, 50px);
            grid-template-rows: repeat(3, 50px);
            gap: 5px;
            padding: 10px;
            justify-content: center;
        }
        .dir-btn {
            background: rgba(255,255,255,0.1);
            border: 2px solid rgba(255,255,255,0.2);
            border-radius: 8px;
            color: #fff;
            font-size: 20px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .dir-btn:active { background: rgba(255,255,255,0.2); }
        
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
        <canvas id="snake-canvas"></canvas>
    </div>
    
    <div class="direction-pad">
        <div></div>
        <button class="dir-btn" onclick="setDir('up')">⬆️</button>
        <div></div>
        <button class="dir-btn" onclick="setDir('left')">⬅️</button>
        <button class="dir-btn" onclick="togglePause()">⏸️</button>
        <button class="dir-btn" onclick="setDir('right')">➡️</button>
        <div></div>
        <button class="dir-btn" onclick="setDir('down')">⬇️</button>
        <div></div>
    </div>
    
    <div class="game-message" id="gameMessage">
        <div id="messageText"></div>
        <button class="btn" onclick="startGame()" style="margin-top:15px;">재시작</button>
    </div>
    
    <script>
    const canvas = document.getElementById('snake-canvas');
    const ctx = canvas.getContext('2d');
    const area = document.getElementById('game-area');
    
    let gridSize = 20;
    let cols = 20;
    let rows = 20;
    let snake = [];
    let food = {};
    let dir = 'right';
    let nextDir = 'right';
    let score = 0;
    let best = 0;
    let running = false;
    let paused = false;
    let animId = null;
    let speed = 100;
    
    const foodEmojis = ['🍎', '🍊', '🍇', '🍓', '🍒', '🥝', '🍑', '🍌'];
    
    function init() {
        resizeCanvas();
        window.addEventListener('resize', resizeCanvas);
        
        // 터치/키보드 방향 전환
        document.addEventListener('keydown', (e) => {
            switch(e.key) {
                case 'ArrowUp': case 'w': case 'W': setDir('up'); break;
                case 'ArrowDown': case 's': case 'S': setDir('down'); break;
                case 'ArrowLeft': case 'a': case 'A': setDir('left'); break;
                case 'ArrowRight': case 'd': case 'D': setDir('right'); break;
                case ' ': togglePause(); break;
            }
        });
        
        best = localStorage.getItem('snakeBest') || 0;
        document.getElementById('best').textContent = best;
        
        if (localStorage.getItem('darkMode') === '1') {
            document.body.classList.add('dark-mode');
            document.querySelector('header').classList.add('dark');
        }
        
        showStartScreen();
    }
    
    function resizeCanvas() {
        const w = area.clientWidth;
        const h = area.clientHeight - 10;
        
        cols = Math.floor(w / gridSize);
        rows = Math.floor(h / gridSize);
        
        canvas.width = cols * gridSize;
        canvas.height = rows * gridSize;
        
        if (!running) {
            render();
        }
    }
    
    function setDir(newDir) {
        if (paused) return;
        
        const opposites = { up: 'down', down: 'up', left: 'right', right: 'left' };
        if (newDir !== opposites[dir]) {
            nextDir = newDir;
        }
    }
    
    function togglePause() {
        if (!running) {
            startGame();
        } else {
            paused = !paused;
            if (!paused) {
                document.getElementById('gameMessage').classList.remove('show');
                gameLoop();
            } else {
                document.getElementById('messageText').innerHTML = '⏸️ 일시정지';
                document.getElementById('gameMessage').classList.add('show');
            }
        }
    }
    
    function showStartScreen() {
        document.getElementById('messageText').innerHTML = '🐍 뱀먹기<br><br>화면을 터치하거나<br>방향 버튼을 누르세요!';
        document.getElementById('gameMessage').classList.add('show');
        render();
    }
    
    function startGame() {
        running = true;
        paused = false;
        score = 0;
        dir = 'right';
        nextDir = 'right';
        speed = Math.max(60, 100 - Math.floor(score / 5) * 5);
        
        // 뱀 초기화 (가운데)
        snake = [];
        const startX = Math.floor(cols / 2);
        const startY = Math.floor(rows / 2);
        for (let i = 2; i >= 0; i--) {
            snake.push({ x: startX - i, y: startY });
        }
        
        spawnFood();
        
        document.getElementById('score').textContent = score;
        document.getElementById('gameMessage').classList.remove('show');
        
        if (animId) cancelAnimationFrame(animId);
        gameLoop();
    }
    
    function spawnFood() {
        let valid = false;
        while (!valid) {
            food = {
                x: Math.floor(Math.random() * cols),
                y: Math.floor(Math.random() * rows),
                emoji: foodEmojis[Math.floor(Math.random() * foodEmojis.length)]
            };
            
            // 뱀 몸통에 겹치지 않게
            valid = !snake.some(s => s.x === food.x && s.y === food.y);
        }
    }
    
    function gameLoop() {
        if (!running || paused) return;
        
        dir = nextDir;
        
        // 이동
        const head = { x: snake[0].x, y: snake[0].y };
        
        switch(dir) {
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
        if (snake.some(s => s.x === head.x && s.y === head.y)) {
            gameOver();
            return;
        }
        
        snake.unshift(head);
        
        // 음식 먹음
        if (head.x === food.x && head.y === food.y) {
            score += 10;
            document.getElementById('score').textContent = score;
            
            // 속도 증가
            speed = Math.max(60, 100 - Math.floor(score / 10) * 3);
            
            spawnFood();
            
            if (navigator.vibrate) navigator.vibrate(20);
        } else {
            snake.pop();
        }
        
        render();
        
        setTimeout(() => {
            animId = requestAnimationFrame(gameLoop);
        }, speed);
    }
    
    function render() {
        const w = canvas.width;
        const h = canvas.height;
        
        // 배경
        ctx.fillStyle = '#0a0a15';
        ctx.fillRect(0, 0, w, h);
        
        // 그리드 (얕게)
        ctx.strokeStyle = 'rgba(255,255,255,0.02)';
        ctx.lineWidth = 1;
        for (let x = 0; x <= cols; x++) {
            ctx.beginPath();
            ctx.moveTo(x * gridSize, 0);
            ctx.lineTo(x * gridSize, h);
            ctx.stroke();
        }
        for (let y = 0; y <= rows; y++) {
            ctx.beginPath();
            ctx.moveTo(0, y * gridSize);
            ctx.lineTo(w, y * gridSize);
            ctx.stroke();
        }
        
        // 음식
        ctx.font = `${gridSize * 0.8}px Arial`;
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';
        ctx.fillText(food.emoji, food.x * gridSize + gridSize/2, food.y * gridSize + gridSize/2);
        
        // 뱀
        snake.forEach((segment, i) => {
            if (i === 0) {
                // 머리
                ctx.fillStyle = '#4ade80';
            } else {
                // 몸통 (그라데이션)
                const alpha = 1 - (i / snake.length) * 0.5;
                ctx.fillStyle = `rgba(74, 222, 128, ${alpha})`;
            }
            
            ctx.beginPath();
            ctx.roundRect(
                segment.x * gridSize + 1,
                segment.y * gridSize + 1,
                gridSize - 2,
                gridSize - 2,
                4
            );
            ctx.fill();
            
            // 눈 (머리에만)
            if (i === 0) {
                ctx.fillStyle = '#fff';
                const eyeSize = 3;
                const cx = segment.x * gridSize + gridSize/2;
                const cy = segment.y * gridSize + gridSize/2;
                
                switch(dir) {
                    case 'right':
                        ctx.fillRect(cx + 2, cy - 4, eyeSize, eyeSize);
                        ctx.fillRect(cx + 2, cy + 2, eyeSize, eyeSize);
                        break;
                    case 'left':
                        ctx.fillRect(cx - 5, cy - 4, eyeSize, eyeSize);
                        ctx.fillRect(cx - 5, cy + 2, eyeSize, eyeSize);
                        break;
                    case 'up':
                        ctx.fillRect(cx - 4, cy - 5, eyeSize, eyeSize);
                        ctx.fillRect(cx + 2, cy - 5, eyeSize, eyeSize);
                        break;
                    case 'down':
                        ctx.fillRect(cx - 4, cy + 2, eyeSize, eyeSize);
                        ctx.fillRect(cx + 2, cy + 2, eyeSize, eyeSize);
                        break;
                }
            }
        });
    }
    
    function gameOver() {
        running = false;
        cancelAnimationFrame(animId);
        
        if (navigator.vibrate) navigator.vibrate([100, 50, 100]);
        
        if (score > best) {
            best = score;
            localStorage.setItem('snakeBest', best);
            document.getElementById('best').textContent = best;
        }
        
        let msg = '💀 게임 오버!<br>점수: ' + score;
        msg += '<br><br>최고: ' + best;
        
        document.getElementById('messageText').innerHTML = msg;
        document.getElementById('gameMessage').classList.add('show');
    }
    
    init();
    </script>
</body>
</html>
