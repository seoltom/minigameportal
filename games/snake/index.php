<?php
require_once '../../config.php';
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Snake - <?= SITE_NAME ?></title>
    <link rel="stylesheet" href="../../css/style.css">
    <style>
        html, body { overflow: hidden; height: 100%; margin: 0; background: #1a1a1a; }
        body { display: flex; flex-direction: column; height: 100%; touch-action: manipulation; user-select: none; }
        header { background: #2a2a2a; box-shadow: 0 2px 8px rgba(0,0,0,0.08); position: sticky; top: 0; z-index: 100; flex-shrink: 0; }
        .header-content { display: flex; justify-content: space-between; align-items: center; padding: 10px 20px; max-width: 1200px; margin: 0 auto; }
        .logo { font-size: 18px; font-weight: bold; color: #4ade80; }
        nav { display: flex; gap: 20px; }
        nav a { font-size: 14px; color: #ccc; text-decoration: none; }
        .game-area { flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 10px; gap: 10px; }
        .game-info { display: flex; gap: 30px; color: #fff; font-size: 16px; }
        .info-value { font-size: 20px; font-weight: bold; color: #4ade80; }
        #game-board { background: #000; border: 3px solid #333; border-radius: 8px; position: relative; }
        .snake { position: absolute; background: #4ade80; border-radius: 4px; }
        .snake-head { background: #22c55e; }
        .food { position: absolute; font-size: 20px; animation: pulse 0.5s infinite; }
        @keyframes pulse { 0%, 100% { transform: scale(1); } 50% { transform: scale(1.2); } }
        .controls { display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px; margin-top: 10px; }
        .control-btn { padding: 15px; border: none; border-radius: 10px; font-size: 24px; background: #333; color: #fff; cursor: pointer; }
        .control-btn:active { background: #444; transform: scale(0.95); }
        .btn-empty { background: transparent; }
        footer { flex-shrink: 0; padding: 5px 20px; font-size: 10px; color: #666; text-align: center; }
    </style>
</head>
<body>
    <header>
        <div class="header-content">
            <a href="http://tomseol.pe.kr/" class="logo">🐍 <?= SITE_NAME ?></a>
            <nav>
                <a href="http://tomseol.pe.kr/">미니게임</a>
                <a href="http://tomseol.pe.kr/blog/">블로그</a>
            </nav>
        </div>
    </header>
    <main class="game-area">
        <div class="game-info">
            <div>SCORE: <span class="info-value" id="score">0</span></div>
            <div>BEST: <span class="info-value" id="best-score">0</span></div>
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
    <footer><p>© <?= date('Y') ?> <a href="https://tomseol.pe.kr/" target="_blank">tomseol.pe.kr</a></p></footer>
    <script src="game.js"></script>
</body>
</html>
