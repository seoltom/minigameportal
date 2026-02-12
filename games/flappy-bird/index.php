<?php
require_once '../../config.php';
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Flappy Bird - <?= SITE_NAME ?></title>
    <link rel="stylesheet" href="../../css/style.css">
    <style>
        html, body.dark-mode { background: #1a1a2e !important; color: #fff !important; }
        body { overflow: hidden; height: 100%; margin: 0; background: linear-gradient(to bottom, #70c5ce 60%, #ded895 60%); }
        body.dark-mode { background: #1a1a2e !important; color: #fff !important; }
        body { display: flex; flex-direction: column; height: 100%; touch-action: manipulation; user-select: none; }
        header { background: rgba(0,0,0,0.3); position: sticky; top: 0; z-index: 100; flex-shrink: 0; }
        .header-content { display: flex; justify-content: space-between; align-items: center; padding: 10px 20px; max-width: 1200px; margin: 0 auto; }
        .logo { font-size: 18px; font-weight: bold; color: #fff; text-shadow: 2px 2px 0 #000; }
        nav { display: flex; gap: 20px; }
        nav a { font-size: 14px; color: #fff; text-shadow: 1px 1px 0 #000; text-decoration: none; }
        .game-area { flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 10px; }
        #game-canvas { width: 100%; max-width: 400px; height: 100%; max-height: 450px; background: linear-gradient(to bottom, #70c5ce, #ded895); border-radius: 10px; border: 3px solid #4a3728; position: relative; overflow: hidden; }
        .score-display { position: absolute; top: 20px; left: 50%; transform: translateX(-50%); font-size: 50px; font-weight: bold; color: #fff; text-shadow: 3px 3px 0 #000; z-index: 100; }
        #bird { position: absolute; font-size: 35px; z-index: 10; transition: transform 0.1s; }
        .pipe { position: absolute; width: 50px; background: linear-gradient(to right, #73bf2e, #9ce659, #73bf2e); border-radius: 5px; border: 2px solid #558c22; z-index: 5; }
        .pipe::before { content:''; position: absolute; left: 50%; transform: translateX(-50%); width: 60px; height: 25px; background: linear-gradient(to right, #73bf2e, #9ce659, #73bf2e); border-radius: 5px; border: 2px solid #558c22; }
        .pipe-top::before { top: -12px; }
        .pipe-bottom::before { bottom: -12px; }
        .controls-hint { position: absolute; bottom: 20%; left: 50%; transform: translateX(-50%); background: rgba(0,0,0,0.6); color: #fff; padding: 10px 20px; border-radius: 20px; font-size: 14px; z-index: 100; }
        footer { flex-shrink: 0; padding: 5px 20px; font-size: 10px; color: #555; text-align: center; }
    </style>
</head>
<body>
    <header>
        <div class="header-content">
            <a href="http://tomseol.pe.kr/" class="logo">🐦 <?= SITE_NAME ?></a>
            <nav>
                <a href="http://tomseol.pe.kr/">미니게임</a>
                <a href="http://tomseol.pe.kr/blog/">블로그</a>
            </nav>
        </div>
    </header>
    <main class="game-area">
        <div id="game-canvas">
            <div class="score-display" id="score">0</div>
            <div class="controls-hint" id="hint">👆 탭하여 점프!</div>
            <div id="bird">🐤</div>
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
