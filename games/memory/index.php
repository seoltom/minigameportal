<?php
require_once '../../config.php';
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Memory - <?= SITE_NAME ?></title>
    <link rel="stylesheet" href="../../css/style.css">
    <style>
        html, body.dark-mode { background: #1a1a2e !important; color: #fff !important; }
        body { overflow: hidden; height: 100%; margin: 0; background: linear-gradient(135deg, #667eea, #764ba2); }
        body.dark-mode { background: #1a1a2e !important; color: #fff !important; }
        body { display: flex; flex-direction: column; height: 100%; touch-action: manipulation; user-select: none; }
        header { background: rgba(255,255,255,0.9); box-shadow: 0 2px 8px rgba(0,0,0,0.08); position: sticky; top: 0; z-index: 100; flex-shrink: 0; }
        .header-content { display: flex; justify-content: space-between; align-items: center; padding: 10px 20px; max-width: 1200px; margin: 0 auto; }
        .logo { font-size: 18px; font-weight: bold; color: #4f46e5; }
        nav { display: flex; gap: 20px; }
        nav a { font-size: 14px; color: #666; text-decoration: none; }
        .game-area { flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 10px; }
        .game-info { display: flex; gap: 20px; color: #fff; font-size: 16px; margin-bottom: 10px; }
        .info-item { background: rgba(0,0,0,0.3); padding: 8px 20px; border-radius: 20px; }
        #game-board { display: grid; gap: 8px; padding: 10px; background: rgba(0,0,0,0.2); border-radius: 12px; }
        .card { width: 55px; height: 65px; background: linear-gradient(135deg, #4facfe, #00f2fe); border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 28px; cursor: pointer; }
        .card.flipped { background: #fff; transform: rotateY(180deg); }
        .card.matched { background: #4ade80 !important; opacity: 0.7; }
        .controls { display: flex; gap: 10px; margin-top: 10px; }
        .btn { padding: 12px 25px; border: none; border-radius: 8px; font-size: 14px; cursor: pointer; background: #f87171; color: #fff; }
        footer { flex-shrink: 0; padding: 5px 20px; font-size: 10px; color: rgba(255,255,255,0.5); text-align: center; }
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
    <main class="game-area">
        <div class="game-info">
            <div class="info-item">🎯 <span id="moves">0</span>번</div>
            <div class="info-item">⭐ <span id="pairs">0</span>/8</div>
        </div>
        <div id="game-board"></div>
        <div class="controls">
            <button class="btn" onclick="initGame()">새 게임</button>
        </div>
    </main>
    <footer><p>© <?= date('Y') ?> <a href="https://tomseol.pe.kr/" target="_blank">tomseol.pe.kr</a></p></footer>
    <script src="game.js"></script>
    <script>
    if (localStorage.getItem('darkMode') === '1') {
        document.body.classList.add('dark-mode');
    }
    </script>
</body>
</html>
