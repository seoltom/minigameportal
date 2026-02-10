<?php
/**
 * Brick Breaker 게임 페이지 - 모바일 최적화
 */
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
    <link href="https://fonts.googleapis.com/css2?family=Pretendard:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        html, body {
            overflow: hidden;
            height: 100%;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
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
        }
        
        .game-info {
            display: flex;
            gap: 30px;
            color: #fff;
            font-size: 16px;
            margin-bottom: 5px;
        }
        
        .info-item {
            background: rgba(0,0,0,0.4);
            padding: 8px 20px;
            border-radius: 20px;
        }
        
        #game-canvas {
            width: 100%;
            max-width: 350px;
            height: 100%;
            max-height: 400px;
            background: #000;
            border-radius: 10px;
            border: 2px solid #333;
            position: relative;
            overflow: hidden;
        }
        
        .paddle {
            position: absolute;
            bottom: 20px;
            height: 12px;
            background: linear-gradient(to bottom, #60a5fa, #3b82f6);
            border-radius: 6px;
        }
        
        .ball {
            position: absolute;
            width: 14px;
            height: 14px;
            background: #fff;
            border-radius: 50%;
        }
        
        .brick {
            position: absolute;
            height: 20px;
            border-radius: 4px;
        }
        
        .brick-row-0 { background: linear-gradient(135deg, #ef4444, #dc2626); }
        .brick-row-1 { background: linear-gradient(135deg, #f97316, #ea580c); }
        .brick-row-2 { background: linear-gradient(135deg, #eab308, #ca8a04); }
        .brick-row-3 { background: linear-gradient(135deg, #22c55e, #16a34a); }
        .brick-row-4 { background: linear-gradient(135deg, #3b82f6, #2563eb); }
        
        .life {
            position: absolute;
            top: 10px;
            right: 10px;
            color: #fff;
            font-size: 14px;
        }
        
        .controls-hint {
            color: #666;
            font-size: 12px;
            margin-top: 10px;
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
    </style>
</head>
<body>
    <header class="game-header-section">
        <a href="../../index.php" class="logo">🧱 Brick Breaker</a>
        <nav>
            <a href="../../index.php">게임</a>
            <a href="../../blog/">블로그</a>
        </nav>
    </header>

    <main class="game-area">
        <div class="game-info">
            <div class="info-item">⭐ <span id="score">0</span></div>
            <div class="info-item">❤️ <span id="lives">3</span></div>
        </div>
        
        <div id="game-canvas">
            <div class="life">❤️ <span id="lives2">3</span></div>
            <div class="paddle" id="paddle"></div>
            <div class="ball" id="ball"></div>
        </div>
        
        <div class="controls-hint">화면을 좌우로 터치하여 패들 이동</div>
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
