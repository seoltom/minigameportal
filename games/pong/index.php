<?php
/**
 * Pong 게임 페이지 - 모바일 최적화
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
    <title>Pong - <?= SITE_NAME ?></title>
    <link rel="stylesheet" href="../../css/style.css">
    <style>
        html, body {
            overflow: hidden;
            height: 100%;
            background: #000;
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
            background: #111;
        }
        
        .game-header-section .logo {
            font-size: 16px;
            font-weight: bold;
            color: #fff;
        }
        
        .game-header-section nav {
            display: flex;
            gap: 15px;
        }
        
        .game-header-section nav a {
            font-size: 13px;
            color: #888;
            text-decoration: none;
        }
        
        .game-area {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 10px;
            gap: 10px;
        }
        
        #game-canvas {
            width: 100%;
            max-width: 350px;
            height: 100%;
            max-height: 400px;
            background: #000;
            border: 2px solid #333;
            border-radius: 8px;
            position: relative;
        }
        
        .paddle {
            position: absolute;
            width: 8px;
            height: 60px;
            background: #fff;
            border-radius: 4px;
        }
        
        .paddle.player {
            left: 10px;
        }
        
        .paddle.cpu {
            right: 10px;
        }
        
        .ball {
            position: absolute;
            width: 16px;
            height: 16px;
            background: #fff;
            border-radius: 50%;
            transition: none;
        }
        
        .center-line {
            position: absolute;
            left: 50%;
            top: 0;
            bottom: 0;
            width: 2px;
            background: repeating-linear-gradient(
                to bottom,
                #333 0px,
                #333 15px,
                transparent 15px,
                transparent 30px
            );
            transform: translateX(-50%);
        }
        
        .score-display {
            display: flex;
            justify-content: center;
            gap: 60px;
            color: #fff;
            font-size: 40px;
            font-weight: bold;
            position: absolute;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 10;
        }
        
        .controls-hint {
            color: #666;
            font-size: 12px;
            text-align: center;
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
            color: #444;
            text-align: center;
        }
        
        footer a {
            color: #444;
        }
    </style>
</head>
<body>
    <header class="game-header-section">
        <a href="../../index.php" class="logo">🏓 Pong</a>
        <nav>
            <a href="../../index.php">게임</a>
            <a href="../../blog/">블로그</a>
        </nav>
    </header>

    <main class="game-area">
        <div id="game-canvas">
            <div class="score-display">
                <span id="playerScore">0</span>
                <span id="cpuScore">0</span>
            </div>
            <div class="center-line"></div>
            <div class="paddle player" id="playerPaddle"></div>
            <div class="paddle cpu" id="cpuPaddle"></div>
            <div class="ball" id="ball"></div>
        </div>
        
        <div class="controls-hint">화면을 위아래로 터치하여 라켓 이동</div>
    </main>

    <div class="game-message" id="gameMessage">
        <div id="messageText"></div>
        <button onclick="startGame()" style="margin-top:15px;padding:12px 25px;border:none;border-radius:8px;background:#fff;color:#000;font-weight:bold;font-size:16px;">🔄 시작</button>
    </div>

    <footer>
        <p>© <?= date('Y') ?> <a href="https://tomseol.pe.kr/" target="_blank">tomseol.pe.kr</a></p>
    </footer>

    <script src="game.js"></script>
</body>
</html>
