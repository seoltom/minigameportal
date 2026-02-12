<?php
require_once '../../config.php';
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Brick Breaker - <?= SITE_NAME ?></title>
    <link rel="stylesheet" href="../../css/style.css?v=20260212">
    <style>
        html, body.dark-mode { background: #1a1a2e !important; color: #fff !important; }
        body { overflow: hidden; height: 100%; margin: 0; background: linear-gradient(135deg, #1a1a2e, #16213e); }
        body.dark-mode { background: #1a1a2e !important; color: #fff !important; }
        body { display: flex; flex-direction: column; height: 100%; touch-action: manipulation; user-select: none; }
        body.dark-mode header { background: #1a1a2e !important; }
        body.dark-mode .logo { color: #fff !important; }
        body.dark-mode nav a { color: #ccc !important; }
        header { background: #fff; box-shadow: 0 2px 8px rgba(0,0,0,0.08); position: sticky; top: 0; z-index: 100; flex-shrink: 0; }
        .header-content { display: flex; justify-content: space-between; align-items: center; padding: 10px 20px; max-width: 1200px; margin: 0 auto; }
        .logo { font-size: 18px; font-weight: bold; color: #4f46e5; }
        nav { display: flex; gap: 20px; }
        nav a { font-size: 14px; color: #666; text-decoration: none; }
        .game-area { flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 10px; }
        .game-info { display: flex; gap: 30px; color: #fff; font-size: 16px; margin-bottom: 5px; }
        .info-item { background: rgba(0,0,0,0.4); padding: 8px 20px; border-radius: 20px; }
        #game-canvas { width: 100%; max-width: 350px; height: 100%; max-height: 380px; background: #000; border-radius: 10px; border: 2px solid #333; position: relative; overflow: hidden; }
        .paddle { position: absolute; bottom: 20px; height: 12px; background: linear-gradient(to bottom, #60a5fa, #3b82f6); border-radius: 6px; }
        .ball { position: absolute; width: 14px; height: 14px; background: #fff; border-radius: 50%; }
        .brick { position: absolute; height: 20px; border-radius: 4px; }
        .controls-hint { color: #666; font-size: 12px; margin-top: 10px; text-align: center; }
        footer { flex-shrink: 0; padding: 5px 20px; font-size: 10px; color: #666; text-align: center; }
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
            <div class="info-item">⭐ <span id="score">0</span></div>
            <div class="info-item">❤️ <span id="lives">3</span></div>
        </div>
        <div id="game-canvas">
            <div class="paddle" id="paddle"></div>
            <div class="ball" id="ball"></div>
        </div>
        <div class="controls-hint">화면을 좌우로 터치하여 패들 이동</div>
    </main>
    <footer><p>© <?= date('Y') ?> <a href="https://tomseol.pe.kr/" target="_blank">tomseol.pe.kr</a></p></footer>
    <script src="game.js?v=20260212"></script>
    <script>
    if (localStorage.getItem('darkMode') === '1') {
        document.body.classList.add('dark-mode');
        document.querySelector('header').classList.add('dark');
    }
    </script>
</body>
</html>
