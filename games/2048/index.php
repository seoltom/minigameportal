<?php
/**
 * 2048 게임 페이지 - 모바일 최적화
 */
require_once '../../config.php';
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="mobile-web-app-capable" content="yes">
    <title>2048 - <?= SITE_NAME ?></title>
    <link rel="stylesheet" href="../../css/style.css">
    <style>
        html, body.dark-mode { background: #1a1a2e !important; color: #fff !important; }
        body { overflow: hidden; height: 100%; margin: 0; padding: 0; background: #faf8ef; }
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
        .score-bar { display: flex; gap: 15px; margin-bottom: 15px; }
        .score-box { background: #bbada0; padding: 10px 25px; border-radius: 6px; text-align: center; }
        .score-label { font-size: 11px; color: #eee4da; font-weight: 600; text-transform: uppercase; }
        .score-value { font-size: 24px; font-weight: bold; color: #fff; }
        #game-board { background: #bbada0; border-radius: 8px; padding: 10px; display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px; }
        .tile { width: 65px; height: 65px; border-radius: 6px; display: flex; align-items: center; justify-content: center; font-size: 28px; font-weight: bold; }
        .tile-2 { background: #eee4da; color: #776e65; }
        .tile-4 { background: #ede0c8; color: #776e65; }
        .tile-8 { background: #f2b179; color: #f9f6f2; }
        .tile-16 { background: #f59563; color: #f9f6f2; }
        .tile-32 { background: #f67c5f; color: #f9f6f2; }
        .tile-64 { background: #f65e3b; color: #f9f6f2; }
        .tile-128 { background: #edcf72; color: #f9f6f2; font-size: 24px; }
        .tile-256 { background: #edcc61; color: #f9f6f2; font-size: 24px; }
        .tile-512 { background: #edc850; color: #f9f6f2; font-size: 24px; }
        .tile-1024 { background: #edc53f; color: #f9f6f2; font-size: 20px; }
        .tile-2048 { background: #edc22e; color: #f9f6f2; font-size: 20px; }
        .tile-new { animation: appear 0.2s ease-in-out; }
        .tile-merged { animation: pop 0.2s ease-in-out; }
        @keyframes appear { 0% { opacity: 0; transform: scale(0); } 100% { opacity: 1; transform: scale(1); } }
        @keyframes pop { 0% { transform: scale(1); } 50% { transform: scale(1.2); } 100% { transform: scale(1); } }
        .controls { display: flex; gap: 8px; margin-top: 15px; width: 100%; max-width: 300px; justify-content: center; }
        .control-btn { padding: 15px; border: none; border-radius: 8px; font-size: 20px; cursor: pointer; background: #8f7a66; color: #fff; }
        .control-btn:active { transform: scale(0.92); }
        .game-message { position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: rgba(237, 194, 46, 0.95); color: #fff; padding: 30px 50px; border-radius: 12px; font-size: 24px; font-weight: bold; text-align: center; z-index: 2000; display: none; }
        .game-message.show { display: block; }
        .game-message.game-won { background: rgba(237, 194, 46, 0.95); }
        footer { flex-shrink: 0; padding: 5px 20px; font-size: 10px; color: #999; text-align: center; }
        footer a { color: #999; }
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
        <div class="score-bar">
            <div class="score-box">
                <div class="score-label">SCORE</div>
                <div class="score-value" id="score">0</div>
            </div>
            <div class="score-box">
                <div class="score-label">BEST</div>
                <div class="score-value" id="best-score">0</div>
            </div>
        </div>
        <div id="game-board"></div>
        <div class="controls">
            <button class="control-btn btn-reset" onclick="initGame()">🔄</button>
            <button class="control-btn btn-move" onclick="move('up')">⬆️</button>
            <button class="control-btn btn-move" onclick="move('down')">⬇️</button>
            <button class="control-btn btn-move" onclick="move('left')">⬅️</button>
            <button class="control-btn btn-move" onclick="move('right')">➡️</button>
        </div>
    </main>
    <div class="game-message" id="gameMessage">
        <div id="messageText"></div>
        <button onclick="initGame()" style="margin-top:15px;padding:12px 25px;border:none;border-radius:8px;background:#fff;color:#776e65;font-weight:bold;font-size:16px;">다시하기</button>
    </div>
    <footer><p>© <?= date('Y') ?> <a href="https://tomseol.pe.kr/" target="_blank">tomseol.pe.kr</a></p></footer>
    <script src="game.js"></script>
    <script>
    if (localStorage.getItem('darkMode') === '1') {
        document.body.classList.add('dark-mode');
    }
    </script>
</body>
</html>
