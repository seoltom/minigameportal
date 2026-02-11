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
    
        html, body { overflow: hidden; height: 100%; margin: 0; background: #1a1a1a; }
        body { display: flex; flex-direction: column; height: 100%; touch-action: manipulation; user-select: none; }
        header { background: #2a2a2a; box-shadow: 0 2px 8px rgba(0,0,0,0.08); position: sticky; top: 0; z-index: 100; flex-shrink: 0; }
        .header-content { display: flex; justify-content: space-between; align-items: center; padding: 10px 20px; max-width: 1200px; margin: 0 auto; }
        .logo { font-size: 18px; font-weight: bold; color: #4ade80; }
        nav { display: flex; gap: 20px; }
        nav a { font-size: 14px; color: #ccc; text-decoration: none; }
        .game-area { flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 10px; gap: 10px; }
        .game-info { display: flex; gap: 30px; color: #fff; font-size: 16px; }
        .info-item { text-align: center; }
        .info-label { font-size: 10px; color: #888; }
        .info-value { font-size: 20px; font-weight: bold; color: #4ade80; }
        #game-board { background: #000; border: 3px solid #333; border-radius: 8px; position: relative; }
        .snake { position: absolute; background: #4ade80; border-radius: 4px; }
        .snake-head { background: #22c55e; border-radius: 6px; }
        .food { position: absolute; font-size: 20px; animation: pulse 0.5s infinite; }
        @keyframes pulse { 0%, 100% { transform: scale(1); } 50% { transform: scale(1.2); } }
        .controls { display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px; margin-top: 10px; }
        .control-btn { padding: 15px; border: none; border-radius: 10px; font-size: 24px; background: #333; color: #fff; cursor: pointer; }
        .control-btn:active { background: #444; transform: scale(0.95); }
        .btn-empty { background: transparent; }
        .game-message { position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: rgba(0,0,0,0.9); color: #fff; padding: 25px 35px; border-radius: 12px; font-size: 18px; text-align: center; z-index: 2000; display: none; }
        .game-message.show { display: block; }
        footer { flex-shrink: 0; padding: 5px 20px; font-size: 10px; color: #666; text-align: center; }
        footer a { color: #666; }
    </style>
<?php require_once ../header.php; ?>
<?php require_once '../header.php'; ?>
</head>
<body>
    
        
            <a href="../../index.php" class="logo">🐍 <?= SITE_NAME ?></a>
            <nav><a href="../../index.php">게임</a><a href="../../blog/">블로그</a></nav>
        </div>
    
    <main class="game-area">
        <div class="game-info">
            <div class="info-item"><div class="info-label">SCORE</div><div class="info-value" id="score">0</div></div>
            <div class="info-item"><div class="info-label">BEST</div><div class="info-value" id="best-score">0</div></div>
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
    <div class="game-message" id="gameMessage"><div id="messageText"></div><button onclick="startGame()" style="margin-top:15px;padding:12px 25px;border:none;border-radius:8px;background:#4ade80;color:#000;font-weight:bold;">다시하기</button></div>
    <footer><p>© <?= date('Y') ?> <a href="https://tomseol.pe.kr/" target="_blank">tomseol.pe.kr</a></p></footer>
    <script src="game.js"></script>
</body>
</html>
