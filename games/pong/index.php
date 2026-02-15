<?php
require_once '../../config.php';
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <title>Pong - <?= SITE_NAME ?></title>
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
        
        .score-board {
            display: flex;
            gap: 40px;
            padding: 10px 20px;
            justify-content: center;
            background: rgba(0,0,0,0.3);
        }
        .score-item {
            text-align: center;
            color: #fff;
        }
        .score-label { font-size: 12px; color: #888; }
        .score-value { font-size: 36px; font-weight: bold; }
        
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
        
        .paddle-btn {
            position: absolute;
            width: 60px;
            height: 60px;
            background: rgba(255,255,255,0.15);
            border: 2px solid rgba(255,255,255,0.3);
            border-radius: 50%;
            color: #fff;
            font-size: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10;
            touch-action: manipulation;
        }
        .paddle-btn:active { background: rgba(255,255,255,0.3); }
        #btn-left { bottom: 30px; left: calc(50% - 80px); }
        #btn-right { bottom: 30px; left: calc(50% + 20px); }
        
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
    
    <div class="score-board">
        <div class="score-item">
            <div class="score-label">나</div>
            <div class="score-value" id="player-score">0</div>
        </div>
        <div class="score-item">
            <div class="score-label">컴퓨터</div>
            <div class="score-value" id="cpu-score">0</div>
        </div>
    </div>
    
    <div id="game-area">
        <canvas id="canvas"></canvas>
    </div>
    
    <button class="paddle-btn" id="btn-left">⬅️</button>
    <button class="paddle-btn" id="btn-right">➡️</button>
    
    <div class="game-message" id="gameMessage">
        <div id="messageText"></div>
        <button class="btn" onclick="startGame()" style="margin-top:15px;">재시작</button>
    </div>
    
    <script>
    const canvas = document.getElementById('canvas');
    const ctx = canvas.getContext('2d');
    const area = document.getElementById('game-area');
    
    let width = 0, height = 0;
    let paddleH = 0, paddleW = 0;
    let ballR = 0;
    
    // 게임 상태
    let playerY = 0, cpuY = 0;
    let ballX = 0, ballY = 0, ballVX = 0, ballVY = 0;
    let playerScore = 0, cpuScore = 0;
    let running = false;
    let animId = null;
    let cpuSpeed = 4;
    let playerMovingUp = false;
    let playerMovingDown = false;
    
    function init() {
        resizeCanvas();
        window.addEventListener('resize', resizeCanvas);
        
        // 터치/마우스 패들 이동
        area.addEventListener('touchstart', handleTouch, { passive: false });
        area.addEventListener('touchmove', handleTouch, { passive: false });
        area.addEventListener('touchend', () => { playerMovingUp = false; playerMovingDown = false; });
        area.addEventListener('mousedown', handleTouch);
        area.addEventListener('mousemove', handleTouch);
        area.addEventListener('mouseup', () => { playerMovingUp = false; playerMovingDown = false; });
        
        // 버튼
        document.getElementById('btn-left').addEventListener('touchstart', (e) => { e.preventDefault(); playerMovingUp = true; });
        document.getElementById('btn-left').addEventListener('touchend', () => { playerMovingUp = false; });
        document.getElementById('btn-left').addEventListener('mousedown', () => { playerMovingUp = true; });
        document.getElementById('btn-left').addEventListener('mouseup', () => { playerMovingUp = false; });
        
        document.getElementById('btn-right').addEventListener('touchstart', (e) => { e.preventDefault(); playerMovingDown = true; });
        document.getElementById('btn-right').addEventListener('touchend', () => { playerMovingDown = false; });
        document.getElementById('btn-right').addEventListener('mousedown', () => { playerMovingDown = true; });
        document.getElementById('btn-right').addEventListener('mouseup', () => { playerMovingDown = false; });
        
        // 키보드
        document.addEventListener('keydown', (e) => {
            if (e.key === 'ArrowUp' || e.key === 'w') playerMovingUp = true;
            if (e.key === 'ArrowDown' || e.key === 's') playerMovingDown = true;
        });
        document.addEventListener('keyup', (e) => {
            if (e.key === 'ArrowUp' || e.key === 'w') playerMovingUp = false;
            if (e.key === 'ArrowDown' || e.key === 's') playerMovingDown = false;
        });
        
        if (localStorage.getItem('darkMode') === '1') {
            document.body.classList.add('dark-mode');
            document.querySelector('header').classList.add('dark');
        }
        
        showStartScreen();
    }
    
    function resizeCanvas() {
        width = area.clientWidth;
        height = area.clientHeight;
        canvas.width = width;
        canvas.height = height;
        
        paddleW = Math.min(15, width * 0.025);
        paddleH = Math.max(60, height * 0.2);
        ballR = Math.min(10, width * 0.02);
        
        if (!running) {
            playerY = (height - paddleH) / 2;
            cpuY = (height - paddleH) / 2;
            render();
        }
    }
    
    function handleTouch(e) {
        if (e.type === 'touchstart') e.preventDefault();
        if (!running) return;
        
        const rect = area.getBoundingClientRect();
        const clientY = e.touches ? e.touches[0].clientY : e.clientY;
        const touchY = clientY - rect.top;
        
        // 패들 중앙을 터치 위치로
        playerY = touchY - paddleH / 2;
        
        // 경계
        playerY = Math.max(0, Math.min(height - paddleH, playerY));
    }
    
    function showStartScreen() {
        document.getElementById('messageText').innerHTML = '🏓 Pong 탁구<br><br>화면을 터치하거나<br>버튼으로 움직이세요!';
        document.getElementById('gameMessage').classList.add('show');
        render();
    }
    
    function startGame() {
        running = true;
        playerScore = 0;
        cpuScore = 0;
        updateScore();
        
        document.getElementById('gameMessage').classList.remove('show');
        
        resetBall();
        
        if (animId) cancelAnimationFrame(animId);
        gameLoop();
    }
    
    function resetBall() {
        ballX = width / 2;
        ballY = height / 2;
        
        const angle = (Math.random() * Math.PI / 4) - (Math.PI / 8);
        const speed = width * 0.008;
        const dir = Math.random() < 0.5 ? 1 : -1;
        
        ballVX = Math.cos(angle) * speed * dir;
        ballVY = Math.sin(angle) * speed;
    }
    
    function updateScore() {
        document.getElementById('player-score').textContent = playerScore;
        document.getElementById('cpu-score').textContent = cpuScore;
    }
    
    function gameLoop() {
        if (!running) return;
        
        // 플레이어 이동
        if (playerMovingUp) playerY -= width * 0.012;
        if (playerMovingDown) playerY += width * 0.012;
        playerY = Math.max(0, Math.min(height - paddleH, playerY));
        
        // CPU 이동 (공 따라감)
        const cpuCenter = cpuY + paddleH / 2;
        if (cpuCenter < ballY - 20) {
            cpuY += cpuSpeed;
        } else if (cpuCenter > ballY + 20) {
            cpuY -= cpuSpeed;
        }
        cpuY = Math.max(0, Math.min(height - paddleH, cpuY));
        
        // 공 이동
        ballX += ballVX;
        ballY += ballVY;
        
        // 상하 벽 충돌
        if (ballY - ballR <= 0 || ballY + ballR >= height) {
            ballVY = -ballVY;
            if (navigator.vibrate) navigator.vibrate(10);
        }
        
        // 플레이어 패들 충돌
        if (ballX - ballR <= paddleW && ballY >= playerY && ballY <= playerY + paddleH) {
            ballX = paddleW + ballR;
            const hitPos = (ballY - playerY) / paddleH;
            const angle = (hitPos - 0.5) * Math.PI / 3;
            const speed = Math.sqrt(ballVX*ballVX + ballVY*ballVY) * 1.05;
            ballVX = Math.cos(angle) * speed;
            ballVY = Math.sin(angle) * speed;
            if (navigator.vibrate) navigator.vibrate(20);
        }
        
        // CPU 패들 충돌
        if (ballX + ballR >= width - paddleW && ballY >= cpuY && ballY <= cpuY + paddleH) {
            ballX = width - paddleW - ballR;
            const hitPos = (ballY - cpuY) / paddleH;
            const angle = (hitPos - 0.5) * Math.PI / 3;
            const speed = Math.sqrt(ballVX*ballVX + ballVY*ballVY) * 1.05;
            ballVX = -Math.cos(angle) * speed;
            ballVY = Math.sin(angle) * speed;
            if (navigator.vibrate) navigator.vibrate(20);
        }
        
        // 점수
        if (ballX < 0) {
            cpuScore++;
            updateScore();
            if (cpuScore >= 10) {
                gameOver('💀 패배!');
                return;
            }
            resetBall();
        } else if (ballX > width) {
            playerScore++;
            updateScore();
            if (playerScore >= 10) {
                gameOver('🎉 승리!');
                return;
            }
            resetBall();
        }
        
        // 속도 증가
        cpuSpeed = Math.min(8, 4 + playerScore * 0.2);
        
        render();
        animId = requestAnimationFrame(gameLoop);
    }
    
    function render() {
        // 배경
        ctx.fillStyle = '#0f0f1a';
        ctx.fillRect(0, 0, width, height);
        
        // 중앙선
        ctx.setLineDash([10, 10]);
        ctx.strokeStyle = 'rgba(255,255,255,0.2)';
        ctx.lineWidth = 2;
        ctx.beginPath();
        ctx.moveTo(width / 2, 0);
        ctx.lineTo(width / 2, height);
        ctx.stroke();
        ctx.setLineDash([]);
        
        // 플레이어 패들
        ctx.fillStyle = '#4ade80';
        ctx.fillRect(0, playerY, paddleW, paddleH);
        
        // CPU 패들
        ctx.fillStyle = '#f87171';
        ctx.fillRect(width - paddleW, cpuY, paddleW, paddleH);
        
        // 공
        ctx.beginPath();
        ctx.arc(ballX, ballY, ballR, 0, Math.PI * 2);
        ctx.fillStyle = '#fff';
        ctx.fill();
    }
    
    function gameOver(msg) {
        running = false;
        cancelAnimationFrame(animId);
        
        document.getElementById('messageText').innerHTML = msg + '<br><br>나: ' + playerScore + '<br>컴퓨터: ' + cpuScore;
        document.getElementById('gameMessage').classList.add('show');
    }
    
    init();
    </script>
</body>
</html>
