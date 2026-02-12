<?php
require_once '../../config.php';
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <title>Brick Breaker - <?= SITE_NAME ?></title>
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
        
        #game-container {
            flex: 1;
            position: relative;
            background: #0f0f23;
            overflow: hidden;
        }
        #game-canvas {
            width: 100%;
            height: 100%;
            display: block;
        }
        #paddle {
            position: absolute;
            bottom: 20px;
            height: 12px;
            background: linear-gradient(to top, #667eea, #764ba2);
            border-radius: 6px;
            touch-action: none;
        }
        #ball {
            position: absolute;
            width: 14px;
            height: 14px;
            background: #fff;
            border-radius: 50%;
            box-shadow: 0 0 10px rgba(255,255,255,0.8);
        }
        .brick {
            position: absolute;
            height: 18px;
            border-radius: 3px;
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
        <div class="info-box">목숨: <span class="info-value" id="lives">3</span></div>
    </div>
    
    <div id="game-container">
        <canvas id="game-canvas"></canvas>
        <div id="paddle"></div>
        <div id="ball"></div>
    </div>
    
    <div class="game-message" id="gameMessage">
        <div id="messageText"></div>
        <button class="btn" onclick="startGame()" style="margin-top:15px;">재시작</button>
    </div>
    
    <script>
    const canvas = document.getElementById('game-canvas');
    const container = document.getElementById('game-container');
    const paddle = document.getElementById('paddle');
    const ball = document.getElementById('ball');
    
    let ctx = null;
    let width = 0, height = 0;
    let paddleX = 0, paddleW = 80, paddleH = 12;
    let ballX = 0, ballY = 0, ballR = 7, ballVX = 0, ballVY = 0;
    let bricks = [];
    let brickRows = 5, brickCols = 7;
    let score = 0, lives = 3;
    let running = false, animId = null;
    let lastTime = 0;
    
    const brickColors = ['#f56565', '#ed8936', '#ecc94b', '#48bb78', '#4299e1'];
    
    function init() {
        ctx = canvas.getContext('2d');
        resize();
        window.addEventListener('resize', resize);
        
        // 터치/마우스 이벤트
        container.addEventListener('touchmove', movePaddle, { passive: false });
        container.addEventListener('touchstart', movePaddle, { passive: false });
        container.addEventListener('mousemove', movePaddle);
        
        showStartScreen();
        
        if (localStorage.getItem('darkMode') === '1') {
            document.body.classList.add('dark-mode');
            document.querySelector('header').classList.add('dark');
        }
    }
    
    function resize() {
        width = container.clientWidth;
        height = container.clientHeight;
        canvas.width = width;
        canvas.height = height;
        
        if (!running) {
            paddleX = (width - paddleW) / 2;
            paddle.style.left = paddleX + 'px';
            paddle.style.width = paddleW + 'px';
            paddle.style.height = paddleH + 'px';
        }
    }
    
    function movePaddle(e) {
        if (!running) return;
        e.preventDefault();
        
        const rect = container.getBoundingClientRect();
        let clientX = e.clientX;
        
        if (e.touches && e.touches.length > 0) {
            clientX = e.touches[0].clientX;
        }
        
        paddleX = clientX - rect.left - paddleW / 2;
        paddleX = Math.max(0, Math.min(width - paddleW, paddleX));
        paddle.style.left = paddleX + 'px';
    }
    
    function showStartScreen() {
        document.getElementById('messageText').innerHTML = '🧱 벽돌깨기<br><br>화면을 터치하여 시작!';
        document.getElementById('gameMessage').classList.add('show');
        
        paddleX = (width - paddleW) / 2;
        paddle.style.left = paddleX + 'px';
        paddle.style.bottom = '20px';
        
        ballX = width / 2;
        ballY = height - 60;
        ball.style.left = ballX - ballR + 'px';
        ball.style.top = ballY - ballR + 'px';
    }
    
    function startGame() {
        score = 0;
        lives = 3;
        running = true;
        
        document.getElementById('score').textContent = score;
        document.getElementById('lives').textContent = lives;
        document.getElementById('gameMessage').classList.remove('show');
        
        createBricks();
        resetBall();
        
        paddle.style.width = Math.min(width * 0.25, 100) + 'px';
        paddleW = parseInt(paddle.style.width);
        paddleX = (width - paddleW) / 2;
        paddle.style.left = paddleX + 'px';
        
        lastTime = performance.now();
        animId = requestAnimationFrame(update);
    }
    
    function createBricks() {
        bricks = [];
        document.querySelectorAll('.brick').forEach(b => b.remove());
        
        const brickW = (width - (brickCols + 1) * 4) / brickCols;
        const brickH = 18;
        const offsetTop = 60;
        
        for (let r = 0; r < brickRows; r++) {
            for (let c = 0; c < brickCols; c++) {
                const el = document.createElement('div');
                el.className = 'brick';
                el.style.width = brickW + 'px';
                el.style.height = brickH + 'px';
                el.style.left = (4 + c * (brickW + 4)) + 'px';
                el.style.top = (offsetTop + r * (brickH + 4)) + 'px';
                el.style.background = brickColors[r % brickColors.length];
                container.appendChild(el);
                
                bricks.push({
                    x: 4 + c * (brickW + 4),
                    y: offsetTop + r * (brickH + 4),
                    w: brickW,
                    h: brickH,
                    active: true
                });
            }
        }
    }
    
    function resetBall() {
        ballX = width / 2;
        ballY = height - 60;
        
        const angle = (Math.random() * Math.PI / 3) - (Math.PI / 6);
        const speed = Math.min(width * 0.012, 6);
        ballVX = Math.cos(angle) * speed;
        ballVY = -speed;
        
        ball.style.left = ballX - ballR + 'px';
        ball.style.top = ballY - ballR + 'px';
    }
    
    function update(now) {
        if (!running) return;
        
        const dt = Math.min((now - lastTime) / 16, 2);
        lastTime = now;
        
        // 공 이동
        ballX += ballVX * dt;
        ballY += ballVY * dt;
        
        // 벽 충돌
        if (ballX - ballR <= 0 || ballX + ballR >= width) {
            ballVX = -ballVX;
            ballX = Math.max(ballR, Math.min(width - ballR, ballX));
            if (navigator.vibrate) navigator.vibrate(10);
        }
        if (ballY - ballR <= 0) {
            ballVY = -ballVY;
            ballY = ballR;
            if (navigator.vibrate) navigator.vibrate(10);
        }
        
        // 패들 충돌
        const paddleTop = height - 20 - paddleH;
        if (ballY + ballR >= paddleTop && ballY - ballR <= paddleTop + paddleH && ballVY > 0) {
            if (ballX >= paddleX && ballX <= paddleX + paddleW) {
                ballVY = -Math.abs(ballVY * 1.03);
                const hitPos = (ballX - paddleX) / paddleW;
                ballVX = (hitPos - 0.5) * 10;
                ballY = paddleTop - ballR;
                if (navigator.vibrate) navigator.vibrate(20);
            }
        }
        
        // 공落下
        if (ballY + ballR >= height) {
            lives--;
            document.getElementById('lives').textContent = lives;
            
            if (lives <= 0) {
                gameOver();
                return;
            } else {
                resetBall();
            }
        }
        
        // 벽돌 충돌
        bricks.forEach(b => {
            if (!b.active) return;
            
            if (ballX + ballR > b.x && ballX - ballR < b.x + b.w &&
                ballY + ballR > b.y && ballY - ballR < b.y + b.h) {
                
                b.active = false;
                document.querySelector(`.brick[style*="left: ${b.x}px"]`)?.remove();
                
                const overlapLeft = ballX + ballR - b.x;
                const overlapRight = b.x + b.w - ballX + ballR;
                const overlapTop = ballY + ballR - b.y;
                const overlapBottom = b.y + b.h - ballY + ballR;
                
                if (overlapLeft < overlapRight && overlapLeft < overlapTop && overlapLeft < overlapBottom) {
                    ballVX = -Math.abs(ballVX);
                } else if (overlapRight < overlapTop && overlapRight < overlapBottom) {
                    ballVX = Math.abs(ballVX);
                } else if (overlapTop < overlapBottom) {
                    ballVY = -Math.abs(ballVY);
                } else {
                    ballVY = Math.abs(ballVY);
                }
                
                score += 10;
                document.getElementById('score').textContent = score;
                if (navigator.vibrate) navigator.vibrate(15);
                
                // 승리 체크
                if (bricks.filter(bb => bb.active).length === 0) {
                    gameWon();
                    return;
                }
            }
        });
        
        // 렌더링
        ball.style.left = ballX - ballR + 'px';
        ball.style.top = ballY - ballR + 'px';
        
        animId = requestAnimationFrame(update);
    }
    
    function gameOver() {
        running = false;
        cancelAnimationFrame(animId);
        if (navigator.vibrate) navigator.vibrate([100, 50, 100]);
        
        setTimeout(() => {
            document.getElementById('messageText').innerHTML = `💀 게임 오버!<br>점수: ${score}`;
            document.getElementById('gameMessage').classList.add('show');
        }, 300);
    }
    
    function gameWon() {
        running = false;
        cancelAnimationFrame(animId);
        score += lives * 100;
        if (navigator.vibrate) navigator.vibrate([100, 50, 100]);
        
        setTimeout(() => {
            document.getElementById('messageText').innerHTML = `🎉 클리어!<br>점수: ${score}`;
            document.getElementById('gameMessage').classList.add('show');
        }, 300);
    }
    
    init();
    </script>
</body>
</html>
