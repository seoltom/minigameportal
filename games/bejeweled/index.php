<?php
require_once '../../config.php';
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Bejeweled - <?= SITE_NAME ?></title>
    <link rel="stylesheet" href="../../css/style.css">
    <style>
        html, body.dark-mode { background: #1a1a2e !important; color: #fff !important; }
        body { overflow: hidden; height: 100%; margin: 0; background: #2c3e50; }
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
        .game-info { display: flex; gap: 15px; margin-bottom: 10px; }
        .info-box { background: rgba(0,0,0,0.5); color: #fff; padding: 8px 15px; border-radius: 6px; text-align: center; }
        .info-value { font-size: 18px; font-weight: bold; color: #ffd700; }
        .controls { display: flex; gap: 8px; margin-bottom: 10px; }
        .btn { padding: 12px 20px; border: none; border-radius: 8px; font-size: 14px; cursor: pointer; background: #8f7a66; color: #fff; }
        .game-board-container { flex: 1; display: flex; align-items: center; justify-content: center; }
        #game-board { background: linear-gradient(135deg, #2c3e50, #34495e); border-radius: 8px; display: grid; gap: 3px; }
        .gem { border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 28px; cursor: pointer; }
        .gem.selected { transform: scale(1.1); box-shadow: 0 0 15px rgba(255,255,255,0.8); }
        .gem-0 { background: linear-gradient(135deg, #e74c3c, #c0392b); }
        .gem-1 { background: linear-gradient(135deg, #3498db, #2980b9); }
        .gem-2 { background: linear-gradient(135deg, #2ecc71, #27ae60); }
        .gem-3 { background: linear-gradient(135deg, #f1c40f, #f39c12); }
        .gem-4 { background: linear-gradient(135deg, #9b59b6, #8e44ad); }
        .gem-5 { background: linear-gradient(135deg, #e67e22, #d35400); }
        .game-message { position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: rgba(0,0,0,0.9); color: #fff; padding: 25px 35px; border-radius: 12px; font-size: 18px; text-align: center; z-index: 2000; display: none; }
        .game-message.show { display: block; }
        footer { flex-shrink: 0; padding: 5px 20px; font-size: 10px; color: #999; text-align: center; }
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
            <div class="info-box">SCORE: <span class="info-value" id="score">0</span></div>
            <div class="info-box">LEVEL: <span class="info-value" id="level">1</span></div>
        </div>
        <div class="controls">
            <button class="btn" onclick="initGame()">새 게임</button>
        </div>
        <div class="game-board-container"><div id="game-board"></div></div>
    </main>
    <div class="game-message" id="gameMessage"><button onclick="initGame()" style="margin-top:10px;padding:10px 20px;border:none;border-radius:6px;background:#fff;color:#000;font-weight:bold;">다시하기</button></div>
    <footer><p>© <?= date('Y') ?> <a href="https://tomseol.pe.kr/" target="_blank">tomseol.pe.kr</a></p></footer>
    <script src="game.js"></script>
    <script>
    if (localStorage.getItem('darkMode') === '1') {
        document.body.classList.add('dark-mode');
    }
    </script>
</body>
</html>
