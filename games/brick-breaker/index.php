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
        
        #game-area {
            flex: 1;
            position: relative;
            background: #0f0f23;
            overflow: hidden;
        }
        
        #paddle {
            position: absolute;
            bottom: 20px;
            height: 14px;
            background: linear-gradient(to top, #667eea, #764ba2);
            border-radius: 7px;
            touch-action: none;
        }
        
        #ball {
            position: absolute;
            width: 16px;
            height: 16px;
            background: #fff;
            border-radius: 50%;
            box-shadow: 0 0 15px rgba(255,255,255,0.9);
        }
        
        .brick {
            position: absolute;
            height: 20px;
            border-radius: 4px;
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
    
    <div id="game-area">
        <div id="paddle"></div>
        <div id="ball"></div>
    </div>
    
    <div class="game-message" id="gameMessage">
        <div id="messageText"></div>
        <button class="btn" onclick="startGame()" style="margin-top:15px;">재시작</button>
    </div>
    
    <script>
    const area = document.getElementById('game-area');
    const paddle = document.getElementById('paddle');
    const ball = document.getElementById('ball');
    
    let paddleX = 0, paddleW = 100, paddleH = 14;
    let ballX = 0, ballY = 0, ballR = 8, ballVX = 0, ballVY = 0;
    let bricks = [];
    let score = 0, lives = 3;
    let running = false, animId = null;
    
    const brickColors = ['#f56565', '#ed8936', '#ecc94b', '#48bb78', '#4299e1'];
    
    function init() {
        window.addEventListener('resize', resize);
        
        area.addEventListener('touchmove', movePaddle, { passive: false });
        area.addEventListener('touchstart', movePaddle, { passive: false });
        area.addEventListener('mousemove', movePaddle);
        
        showStartScreen();
        
        if (localStorage.getItem('darkMode') === '1') {
            document.body.classList.add('dark-mode');
            document.querySelector('header').classList.add('dark');
        }
    }
    
    function resize() {
        if (!running) {
            paddleW = Math.min(area.clientWidth * 0.3, 120);
            paddleX = (area.clientWidth - paddleW) / 2;
            paddle.style.width = paddleW + 'px';
            paddle.style.left = paddleX + 'px';
            paddle.style.bottom = '20px';
        }
    }
    
    function movePaddle(e) {
        if (!running) return;
        e.preventDefault();
        
        const rect = area.getBoundingClientRect();
        let clientX = e.clientX || (e.touches && e.touches[0].clientX);
        
        paddleX = clientX - rect.left - paddleW / 2;
        paddleX = Math.max(0, Math.min(area.clientWidth - paddleW, paddleX));
        paddle.style.left = paddleX + 'px';
    }
    
    function showStartScreen() {
        document.getElementById('messageText').innerHTML = '🧱 벽돌깨기<br><br>화면을 터치하여 시작!';
        document.getElementById('gameMessage').classList.add('show');
        
        resize();
        ball.style.display = 'block';
        ball.style.left = (area.clientWidth / 2 - ballR) + 'px';
        ball.style.top = (area.clientHeight - 80) + 'px';
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
        
        animId = requestAnimationFrame(update);
    }
    
    function createBricks() {
        document.querySelectorAll('.brick').forEach(b => b.remove());
        bricks = [];
        
        const w = area.clientWidth;
        const cols = 6;
        const gap = 5;
        const brickW = (w - (cols + 1) * gap) / cols;
        const brickH = 22;
        const offsetTop = 50;
        
        for (let r = 0; r < 5; r++) {
            for (let c = 0; c < cols; c++) {
                const el = document.createElement('div');
                el.className = 'brick';
                el.style.width = brickW + 'px';
                el.style.height = brickH + 'px';
                el.style.left = (gap + c * (brickW + gap)) + 'px';
                el.style.top = (offsetTop + r * (brickH + gap)) + 'px';
                el.style.background = brickColors[r];
                area.appendChild(el);
                
                bricks.push({
                    el: el,
                    x: gap + c * (brickW + gap),
                    y: offsetTop + r * (brickH + gap),
                    w: brickW,
                    h: brickH,
                    active: true
                });
            }
        }
    }
    
    function resetBall() {
        const w = area.clientWidth;
        const h = area.clientHeight;
        
        ballX = w / 2;
        ballY = h - 80;
        
        const angle = (Math.random() * 0.8 - 0.4) * Math.PI;
        const speed = Math.min(w * 0.01, 5) + 2;
        ballVX = Math.cos(angle) * speed;
        ballVY = -speed;
        
        ball.style.left = (ballX - ballR) + 'px';
        ball.style.top = (ballY - ballR) + 'px';
    }
    
    function update() {
        if (!running) return;
        
        const w = area.clientWidth;
        const h = area.clientHeight;
        
        // 공 이동
        ballX += ballVX;
        ballY += ballVY;
        
        // 벽 충돌
        if (ballX - ballR <= 0 || ballX + ballR >= w) {
            ballVX = -ballVX;
            ballX = Math.max(ballR, Math.min(w - ballR, ballX));
        }
        if (ballY - ballR <= 0) {
            ballVY = -ballVY;
            ballY = ballR;
        }
        
        // 패들 충돌
        const paddleTop = h - 20 - paddleH;
        if (ballY + ballR >= paddleTop && ballY - ballR <= paddleTop + paddleH && ballVY > 0) {
            if (ballX >= paddleX && ballX <= paddleX + paddleW) {
                ballVY = -Math.abs(ballVY * 1.05);
                const hitPos = (ballX - paddleX) / paddleW;
                ballVX = (hitPos - 0.5) * 8;
                ballY = paddleTop - ballR;
            }
        }
        
        // 공 아래로
        if (ballY + ballR >= h) {
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
                b.el.style.display = 'none';
                
                // 충돌 방향
                const overlapLeft = ballX + ballR - b.x;
                const overlapRight = b.x + b.w - ballX + ballR;
                const overlapTop = ballY + ballR - b.y;
                const overlapBottom = b.y + b.h - ballY + ballR;
                
                const minX = Math.min(overlapLeft, overlapRight);
                const minY = Math.min(overlapTop, overlapBottom);
                
                if (minX < minY) {
                    ballVX = -ballVX;
                } else {
                    ballVY = -ballVY;
                }
                
                score += 10;
                document.getElementById('score').textContent = score;
                
                // 승리
                if (bricks.filter(bb => bb.active).length === 0) {
                    gameWon();
                }
            }
        });
        
        // 렌더링
        ball.style.left = (ballX - ballR) + 'px';
        ball.style.top = (ballY - ballR) + 'px';
        
        animId = requestAnimationFrame(update);
    }
    
    function gameOver() {
        running = false;
        cancelAnimationFrame(animId);
        document.getElementById('messageText').innerHTML = '💀 게임 오버!<br>점수: ' + score;
        document.getElementById('gameMessage').classList.add('show');
    }
    
    function gameWon() {
        running = false;
        cancelAnimationFrame(animId);
        score += lives * 100;
        document.getElementById('messageText').innerHTML = '🎉 클리어!<br>점수: ' + score;
        document.getElementById('gameMessage').classList.add('show');
    }
    
    init();
    </script>
</body>
</html>
