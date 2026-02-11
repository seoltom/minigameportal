<?php
require_once '../../config.php';
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Mario Run - <?= SITE_NAME ?></title>
    <link rel="stylesheet" href="../../css/style.css">
    <style>
        html, body { overflow: hidden; height: 100%; margin: 0; background: linear-gradient(87deg, #5BC0DE, #D6AE01); }
        body { display: flex; flex-direction: column; height: 100%; touch-action: manipulation; user-select: none; }
        header { background: rgba(0,0,0,0.3); position: sticky; top: 0; z-index: 100; flex-shrink: 0; }
        .header-content { display: flex; justify-content: space-between; align-items: center; padding: 10px 20px; max-width: 1200px; margin: 0 auto; }
        .logo { font-size: 18px; font-weight: bold; color: #fff; }
        nav { display: flex; gap: 20px; }
        nav a { font-size: 14px; color: #fff; text-decoration: none; }
        .game-area { flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 10px; position: relative; }
        #game-canvas { width: 100%; max-width: 400px; height: 100%; max-height: 350px; background: linear-gradient(to bottom, #5c94fc 60%, #8b4513 60%); border-radius: 8px; position: relative; overflow: hidden; }
        .ground { position: absolute; bottom: 0; left: 0; right: 0; height: 25%; background: linear-gradient(to bottom, #8b4513, #654321); border-top: 4px solid #4a3728; }
        #mario { position: absolute; bottom: 25%; left: 50px; font-size: 45px; transition: bottom 0.1s; z-index: 20; }
        .pipe { position: absolute; bottom: 25%; width: 50px; background: linear-gradient(to right, #228b22, #32cd32, #228b22); border-radius: 5px 5px 0 0; border: 2px solid #1a6b1a; }
        .pipe::before { content:''; position: absolute; top: -10px; left: -5px; width: 60px; height: 15px; background: linear-gradient(to right, #228b22, #32cd32, #228b22); border-radius: 5px; border: 2px solid #1a6b1a; }
        .score-display { position: absolute; top: 10px; right: 15px; background: rgba(0,0,0,0.7); color: #fff; padding: 8px 15px; border-radius: 20px; font-size: 16px; font-weight: bold; z-index: 100; }
        .controls-hint { position: absolute; bottom: 30%; left: 50%; transform: translateX(-50%); background: rgba(0,0,0,0.6); color: #fff; padding: 8px 16px; border-radius: 20px; font-size: 12px; z-index: 100; }
        footer { flex-shrink: 0; padding: 5px 20px; font-size: 10px; color: #fff; background: rgba(0,0,0,0.3); text-align: center; }
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
        <div id="game-canvas">
            <div class="score-display"><span>⭐ <span id="score">0</span></span> <span style="margin-left:15px;">🏃 <span id="distance">0</span>m</span></div>
            <div class="controls-hint" id="hint">👆 탭하여 점프!</div>
            <div id="mario">🍄</div>
            <div class="ground"></div>
        </div>
    </main>
    <footer><p>© <?= date('Y') ?> <a href="https://tomseol.pe.kr/" target="_blank">tomseol.pe.kr</a></p></footer>
    <script src="game.js"></script>
</body>
</html>
