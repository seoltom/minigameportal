<?php
/**
 * Flappy Bird 게임 페이지 - 모바일 최적화
 */
require_once '../../config.php';
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="mobile-web-app-capable" content="yes">
    <title>Flappy Bird - <?= SITE_NAME ?></title>
    <link rel="stylesheet" href="../../css/style.css">
    <style>
        html, body {
            overflow: hidden;
            height: 100%;
            background: linear-gradient(to bottom, #70c5ce 0%, #70c5ce 50%, #ded895 50%, #ded895 100%);
        }
        
        body {
            display: flex;
            flex-direction: column;
            height: 100%;
            touch-action: manipulation;
            -webkit-touch-callout: none;
            -webkit-user-select: none;
            user-select: none;
        }
        
        .game-header-section {
            flex-shrink: 0;
            padding: 8px 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            box-sizing: border-box;
        }
        
        .game-header-section .logo {
            font-size: 16px;
            font-weight: bold;
            color: #fff;
            text-shadow: 2px 2px 0 #000;
        }
        
        .game-header-section nav {
            display: flex;
            gap: 15px;
        }
        
        .game-header-section nav a {
            font-size: 13px;
            color: #fff;
            text-shadow: 1px 1px 0 #000;
            text-decoration: none;
        }
        
        .game-area {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 10px;
            overflow: hidden;
            position: relative;
        }
        
        #game-canvas {
            width: 100%;
            max-width: 400px;
            height: 100%;
            max-height: 500px;
            background: linear-gradient(to bottom, #70c5ce 0%, #70c5ce 60%, #ded895 60%, #ded895 100%);
            border-radius: 10px;
            border: 3px solid #4a3728;
            position: relative;
            overflow: hidden;
        }
        
        .bird {
            position: absolute;
            font-size: 35px;
            z-index: 10;
            transition: transform 0.1s;
        }
        
        .bird.jump {
            animation: birdJump 0.3s ease;
        }
        
        @keyframes birdJump {
            0%, 100% { transform: rotate(0deg); }
            50% { transform: rotate(-30deg); }
        }
        
        .pipe {
            position: absolute;
            width: 50px;
            z-index: 5;
        }
        
        .pipe-top {
            background: linear-gradient(to right, #73bf2e 0%, #9ce659 50%, #73bf2e 100%);
            border-radius: 5px 5px 0 0;
            border: 2px solid #558c22;
        }
        
        .pipe-bottom {
            background: linear-gradient(to right, #73bf2e 0%, #9ce659 50%, #73bf2e 100%);
            border-radius: 0 0 5px 5px;
            border: 2px solid #558c22;
        }
        
        .pipe::before {
            content: '';
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 25px;
            background: linear-gradient(to right, #73bf2e 0%, #9ce659 50%, #73bf2e 100%);
            border-radius: 5px;
            border: 2px solid #558c22;
        }
        
        .pipe-top::before {
            top: -12px;
        }
        
        .pipe-bottom::before {
            bottom: -12px;
        }
        
        .score-display {
            position: absolute;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 50px;
            font-weight: bold;
            color: #fff;
            text-shadow: 3px 3px 0 #000;
            z-index: 100;
        }
        
        .controls-hint {
            position: absolute;
            bottom: 20%;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(0,0,0,0.6);
            color: #fff;
            padding: 10px 20px;
            border-radius: 20px;
            font-size: 14px;
            z-index: 100;
        }
        
        .game-message {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(0,0,0,0.9);
            color: #fff;
            padding: 25px 35px;
            border-radius: 12px;
            font-size: 20px;
            font-weight: bold;
            text-align: center;
            z-index: 2000;
            display: none;
        }
        
        .game-message.show {
            display: block;
        }
        
        footer {
            flex-shrink: 0;
            padding: 5px 20px;
            font-size: 10px;
            color: #555;
            text-align: center;
        }
        
        footer a {
            color: #555;
        }
    </style>
</head>
<body>
    <header class="game-header-section">
        <a href="../../index.php" class="logo">🐦 Flappy</a>
        <nav>
            <a href="../../index.php">게임</a>
            <a href="../../blog/">블로그</a>
        </nav>
    </header>

    <main class="game-area">
        <div id="game-canvas">
            <div class="score-display" id="score">0</div>
            <div class="controls-hint" id="hint">👆 탭하여 점프!</div>
            <div class="bird" id="bird">🐤</div>
        </div>
    </main>

    <div class="game-message" id="gameMessage">
        <div id="messageText"></div>
        <button onclick="startGame()" style="margin-top:15px;padding:12px 25px;border:none;border-radius:8px;background:#73bf2e;color:#fff;font-weight:bold;font-size:16px;">🔄 다시하기</button>
    </div>

    <footer>
        <p>© <?= date('Y') ?> <a href="https://tomseol.pe.kr/" target="_blank">tomseol.pe.kr</a></p>
    </footer>

    <script src="game.js"></script>
</body>
</html>
