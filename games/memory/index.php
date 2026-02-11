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
    
        html, body { overflow: hidden; height: 100%; margin: 0; background: linear-gradient(135deg, #667eea, #764ba2); }
        body { display: flex; flex-direction: column; height: 100%; touch-action: manipulation; user-select: none; }
        header { background: rgba(255,255,255,0.9); box-shadow: 0 2px 8px rgba(0,0,0,0.08); position: sticky; top: 0; z-index: 100; flex-shrink: 0; }
        .header-content { display: flex; justify-content: space-between; align-items: center; padding: 10px 20px; max-width: 1200px; margin: 0 auto; }
        .logo { font-size: 18px; font-weight: bold; color: #4f46e5; }
        nav { display: flex; gap: 20px; }
        nav a { font-size: 14px; color: #666; text-decoration: none; }
        .game-area { flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 10px; gap: 10px; }
        .game-info { display: flex; gap: 20px; color: #fff; font-size: 16px; }
        .info-item { background: rgba(0,0,0,0.3); padding: 8px 20px; border-radius: 20px; }
        #game-board { display: grid; gap: 8px; padding: 10px; background: rgba(0,0,0,0.2); border-radius: 12px; }
        .card { width: 55px; height: 65px; background: linear-gradient(135deg, #4facfe, #00f2fe); border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 28px; cursor: pointer; transition: all 0.3s; box-shadow: 0 4px 8px rgba(0,0,0,0.2); }
        .card:active { transform: scale(0.95); }
        .card.flipped { background: #fff; transform: rotateY(180deg); }
        .card.matched { background: #4ade80 !important; opacity: 0.7; transform: rotateY(180deg); }
        .card .back { display: none; }
        .card.flipped .back, .card.matched .back { display: flex; }
        .card .front { display: flex; }
        .card.flipped .front, .card.matched .front { display: none; }
        .controls { display: flex; gap: 10px; margin-top: 10px; }
        .btn { padding: 12px 25px; border: none; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; }
        .btn-reset { background: #f87171; color: #fff; }
        .btn-difficulty { background: #60a5fa; color: #fff; }
        .btn:active { transform: scale(0.95); }
        .game-message { position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: rgba(0,0,0,0.9); color: #fff; padding: 25px 35px; border-radius: 12px; font-size: 18px; text-align: center; z-index: 2000; display: none; }
        .game-message.show { display: block; }
        footer { flex-shrink: 0; padding: 5px 20px; font-size: 10px; color: rgba(255,255,255,0.5); text-align: center; }
        footer a { color: rgba(255,255,255,0.5); }
    </style>
<?php require_once ../header.php; ?>
<?php require_once '../header.php'; ?>
</head>
<body>
    
        
            <a href="../../index.php" class="logo">🧠 <?= SITE_NAME ?></a>
            <nav><a href="../../index.php">게임</a><a href="../../blog/">블로그</a></nav>
        </div>
    
    <main class="game-area">
        <div class="game-info">
            <div class="info-item">🎯 <span id="moves">0</span>번</div>
            <div class="info-item">⭐ <span id="pairs">0</span>/8</div>
        </div>
        <div id="game-board"></div>
        <div class="controls">
            <button class="btn btn-difficulty" onclick="toggleDifficulty()"><span id="diffLabel">4x4</span></button>
            <button class="btn btn-reset" onclick="initGame()">새 게임</button>
        </div>
    </main>
    <div class="game-message" id="gameMessage"><div id="messageText"></div><button onclick="initGame()" style="margin-top:15px;padding:12px 25px;border:none;border-radius:8px;background:#4ade80;color:#000;font-weight:bold;">다시하기</button></div>
    <footer><p>© <?= date('Y') ?> <a href="https://tomseol.pe.kr/" target="_blank">tomseol.pe.kr</a></p></footer>
    <script src="game.js"></script>
</body>
</html>
