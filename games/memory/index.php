<?php
/**
 * Memory 게임 페이지 - 모바일 최적화
 */
require_once '../../config.php';
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <title>Memory - <?= SITE_NAME ?></title>
    <link rel="stylesheet" href="../../css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Pretendard:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        html, body {
            overflow: hidden;
            height: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
        }
        
        .game-header-section nav {
            display: flex;
            gap: 15px;
        }
        
        .game-header-section nav a {
            font-size: 13px;
            color: rgba(255,255,255,0.8);
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
        
        .game-info {
            display: flex;
            gap: 20px;
            color: #fff;
            font-size: 16px;
        }
        
        .info-item {
            background: rgba(0,0,0,0.3);
            padding: 8px 20px;
            border-radius: 20px;
        }
        
        #game-board {
            display: grid;
            gap: 8px;
            padding: 10px;
            background: rgba(0,0,0,0.2);
            border-radius: 12px;
        }
        
        .card {
            width: 55px;
            height: 65px;
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            cursor: pointer;
            transition: all 0.3s;
            transform-style: preserve-3d;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        
        .card:active {
            transform: scale(0.95);
        }
        
        .card.flipped {
            background: #fff;
            transform: rotateY(180deg);
        }
        
        .card.matched {
            background: #4ade80 !important;
            opacity: 0.7;
            transform: rotateY(180deg) scale(0.95);
        }
        
        .card .back {
            display: none;
        }
        
        .card.flipped .back,
        .card.matched .back {
            display: flex;
        }
        
        .card .front {
            display: flex;
        }
        
        .card.flipped .front,
        .card.matched .front {
            display: none;
        }
        
        .controls {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }
        
        .btn {
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
        }
        
        .btn-reset {
            background: #f87171;
            color: #fff;
        }
        
        .btn-difficulty {
            background: #60a5fa;
            color: #fff;
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
            color: rgba(255,255,255,0.5);
            text-align: center;
        }
    </style>
</head>
<body>
    <header class="game-header-section">
        <a href="../../index.php" class="logo">🧠 Memory</a>
        <nav>
            <a href="../../index.php">게임</a>
            <a href="../../blog/">블로그</a>
        </nav>
    </header>

    <main class="game-area">
        <div class="game-info">
            <div class="info-item">🎯 <span id="moves">0</span>번</div>
            <div class="info-item">⭐ <span id="pairs">0</span>/8</div>
        </div>
        
        <div id="game-board"></div>
        
        <div class="controls">
            <button class="btn btn-difficulty" onclick="toggleDifficulty()">
                <span id="diffLabel">4x4</span>
            </button>
            <button class="btn btn-reset" onclick="initGame()">🔄 새로하기</button>
        </div>
    </main>

    <div class="game-message" id="gameMessage">
        <div id="messageText"></div>
        <button onclick="initGame()" style="margin-top:15px;padding:12px 25px;border:none;border-radius:8px;background:#4ade80;color:#000;font-weight:bold;font-size:16px;">다시하기</button>
    </div>

    <footer>
        <p>© <?= date('Y') ?> <a href="https://tomseol.pe.kr/" target="_blank">tomseol.pe.kr</a></p>
    </footer>

    <script src="game.js"></script>
</body>
</html>
