<?php
require_once '../../config.php';
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Tic-Tac-Toe - <?= SITE_NAME ?></title>
    <link rel="stylesheet" href="../../css/style.css">
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
        .logo { font-size: 18px; font-weight: bold; color: #fcd34d; }
        nav { display: flex; gap: 20px; }
        nav a { font-size: 14px; color: #666; text-decoration: none; }
        .game-area { flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 10px; gap: 15px; }
        .game-info { display: flex; gap: 20px; color: #fff; font-size: 14px; }
        .player-info { display: flex; align-items: center; gap: 8px; padding: 8px 15px; background: rgba(255,255,255,0.1); border-radius: 20px; }
        .player-info.active { background: rgba(74, 222, 128, 0.3); border: 2px solid #4ade80; }
        #game-board { display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px; background: #333; padding: 8px; border-radius: 12px; }
        .cell { width: 70px; height: 70px; background: #1a1a1a; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 40px; font-weight: bold; cursor: pointer; }
        .cell.x { color: #f87171; }
        .cell.o { color: #60a5fa; }
        .cell.winner { background: #4ade80 !important; }
        .controls { display: flex; gap: 10px; margin-top: 10px; }
        .btn { padding: 12px 25px; border: none; border-radius: 8px; font-size: 14px; cursor: pointer; background: #f87171; color: #fff; }
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
            <div class="player-info active" id="playerX"><span>❌</span><span>당신</span></div>
            <div class="player-info" id="playerO"><span>⭕</span><span>CPU</span></div>
        </div>
        <div id="game-board"></div>
        <div class="controls">
            <button class="btn" onclick="resetGame()">새 게임</button>
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
