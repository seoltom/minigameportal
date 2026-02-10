<?php
/**
 * Snake 게임 페이지 - 모바일 최적화
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
    <title>Snake - <?= SITE_NAME ?></title>
    <link rel="stylesheet" href="../../css/style.css">
    <style>
        html, body {
            overflow: hidden;
            height: 100%;
            background: #1a1a1a;
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
            background: #2a2a2a;
        }
        
        .game-header-section .logo {
            font-size: 16px;
            font-weight: bold;
            color: #4ade80;
        }
        
        .game-header-section nav {
            display: flex;
            gap: 15px;
        }
        
        .game-header-section nav a {
            font-size: 13px;
            color: #ccc;
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
        
        #game-board {
            background: #000;
            border: 3px solid #333;
            border-radius: 8px;
            position: relative;
        }
        
        .snake {
            position: absolute;
            background: #4ade80;
            border-radius: 4px;
            transition: all 0.1s;
        }
        
        .snake-head {
            background: #22c55e;
            border-radius: 6px;
        }
        
        .food {
            position: absolute;
            font-size: 20px;
            animation: foodPulse 0.5s infinite;
        }
        
        @keyframes foodPulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.2); }
        }
        
        .score-display {
            display: flex;
            gap: 30px;
            color: #fff;
            font-size: 16px;
        }
        
        .score-item {
            text-align: center;
        }
        
        .score-label {
            font-size: 10px;
            color: #888;
        }
        
        .score-value {
            font-size: 20px;
            font-weight: bold;
            color: #4ade80;
        }
        
        /* 모바일 컨트롤 - 방향키 */
        .controls {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 8px;
            margin-top: 10px;
        }
        
        .control-btn {
            padding: 15px 20px;
            border: none;
            border-radius: 10px;
            font-size: 24px;
            cursor: pointer;
            -webkit-tap-highlight-color: transparent;
            background: #333;
            color: #fff;
        }
        
        .control-btn:active {
            background: #444;
            transform: scale(0.95);
        }
        
        .btn-empty {
            background: transparent;
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
            color: #666;
            text-align: center;
        }
        
        footer a {
            color: #666;
        }
    </style>
</head>
<body>
    <header class="game-header-section">
        <a href="../../index.php" class="logo">🐍 Snake</a>
        <nav>
            <a href="../../index.php">게임</a>
            <a href="../../blog/">블로그</a>
        </nav>
    </header>

    <main class="game-area">
        <div class="score-display">
            <div class="score-item">
                <div class="score-label">SCORE</div>
                <div class="score-value" id="score">0</div>
            </div>
            <div class="score-item">
                <div class="score-label">BEST</div>
                <div class="score-value" id="best-score">0</div>
            </div>
        </div>
        
        <div id="game-board"></div>
        
        <div class="controls">
            <div class="control-btn btn-empty"></div>
            <button class="control-btn" onclick="changeDirection('up')">⬆️</button>
            <div class="control-btn btn-empty"></div>
            <button class="control-btn" onclick="changeDirection('left')">⬅️</button>
            <button class="control-btn" onclick="changeDirection('down')">⬇️</button>
            <button class="control-btn" onclick="changeDirection('right')">➡️</button>
        </div>
    </main>

    <div class="game-message" id="gameMessage">
        <div id="messageText"></div>
        <button onclick="startGame()" style="margin-top:15px;padding:12px 25px;border:none;border-radius:8px;background:#4ade80;color:#000;font-weight:bold;font-size:16px;">🔄 다시하기</button>
    </div>

    <footer>
        <p>© <?= date('Y') ?> <a href="https://tomseol.pe.kr/" target="_blank">tomseol.pe.kr</a></p>
    </footer>

    <script src="game.js"></script>
</body>
</html>
