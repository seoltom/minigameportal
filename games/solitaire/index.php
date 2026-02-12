<?php
require_once '../../config.php';
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Solitaire - <?= SITE_NAME ?></title>
    <link rel="stylesheet" href="../../css/style.css">
    <style>
        html, body.dark-mode { background: #1a1a2e !important; color: #fff !important; }
        body { overflow: hidden; height: 100%; margin: 0; background: linear-gradient(135deg, #1a5f2a, #0d3d1a); }
        body.dark-mode { background: #1a1a2e !important; color: #fff !important; }
        body { display: flex; flex-direction: column; height: 100%; touch-action: manipulation; user-select: none; }
        header { background: rgba(255,255,255,0.9); box-shadow: 0 2px 8px rgba(0,0,0,0.08); position: sticky; top: 0; z-index: 100; flex-shrink: 0; }
        .header-content { display: flex; justify-content: space-between; align-items: center; padding: 10px 20px; max-width: 1200px; margin: 0 auto; }
        .logo { font-size: 18px; font-weight: bold; color: #ffd700; }
        nav { display: flex; gap: 20px; }
        nav a { font-size: 14px; color: #666; text-decoration: none; }
        .game-area { flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: flex-start; padding: 10px; gap: 10px; }
        .top-row { display: flex; gap: 8px; width: 100%; justify-content: space-between; }
        .stock, .waste, .foundation { width: 45px; height: 60px; background: rgba(0,0,0,0.3); border-radius: 6px; border: 2px dashed rgba(255,255,255,0.3); display: flex; align-items: center; justify-content: center; font-size: 24px; }
        #tableau { display: flex; gap: 6px; width: 100%; justify-content: center; }
        .column { width: 45px; min-height: 60px; background: rgba(0,0,0,0.2); border-radius: 6px; position: relative; }
        .card { width: 45px; height: 60px; background: #fff; border-radius: 4px; display: flex; align-items: center; justify-content: center; font-size: 22px; font-weight: bold; position: absolute; box-shadow: 0 2px 4px rgba(0,0,0,0.3); cursor: pointer; }
        .card.red { color: #dc2626; }
        .card.black { color: #1f2937; }
        .card.face-down { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
        .controls { display: flex; gap: 10px; margin-top: 10px; }
        .score-display { color: #ffd700; font-size: 14px; }
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
        <div class="top-row">
            <div class="stock" id="stock" onclick="drawCard()">🃏</div>
            <div class="waste" id="waste"></div>
            <div style="display: flex; gap: 4px;">
                <div class="foundation" id="foundation-0">♥</div>
                <div class="foundation" id="foundation-1">♦</div>
                <div class="foundation" id="foundation-2">♣</div>
                <div class="foundation" id="foundation-3">♠</div>
            </div>
        </div>
        <div id="tableau">
            <div class="column" id="col-0"></div>
            <div class="column" id="col-1"></div>
            <div class="column" id="col-2"></div>
            <div class="column" id="col-3"></div>
            <div class="column" id="col-4"></div>
            <div class="column" id="col-5"></div>
            <div class="column" id="col-6"></div>
        </div>
        <div class="controls">
            <div class="score-display">점수: <span id="score">0</span></div>
            <button class="btn" onclick="initGame()">새 게임</button>
        </div>
    </main>
    <footer><p>© <?= date('Y') ?> <a href="https://tomseol.pe.kr/" target="_blank">tomseol.pe.kr</a></p></footer>
    <script src="game.js"></script>
    <script>
    if (localStorage.getItem('darkMode') === '1') {
        document.body.classList.add('dark-mode');
        document.querySelector('header').classList.add('dark');
    }
    </script>
</body>
</html>
