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
    
        html, body { overflow: hidden; height: 100%; margin: 0; background: linear-gradient(135deg, #1a1a2e, #16213e); }
        body { display: flex; flex-direction: column; height: 100%; touch-action: manipulation; user-select: none; }
        header { background: #fff; box-shadow: 0 2px 8px rgba(0,0,0,0.08); position: sticky; top: 0; z-index: 100; flex-shrink: 0; }
        .header-content { display: flex; justify-content: space-between; align-items: center; padding: 10px 20px; max-width: 1200px; margin: 0 auto; }
        .logo { font-size: 18px; font-weight: bold; color: #fcd34d; }
        nav { display: flex; gap: 20px; }
        nav a { font-size: 14px; color: #666; text-decoration: none; }
        .game-area { flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 10px; gap: 15px; }
        .game-info { display: flex; gap: 20px; color: #fff; font-size: 14px; }
        .player-info { display: flex; align-items: center; gap: 8px; padding: 8px 15px; background: rgba(255,255,255,0.1); border-radius: 20px; }
        .player-info.active { background: rgba(74, 222, 128, 0.3); border: 2px solid #4ade80; }
        .player-info.loser { opacity: 0.5; }
        #game-board { display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px; background: #333; padding: 8px; border-radius: 12px; }
        .cell { width: 70px; height: 70px; background: #1a1a1a; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 40px; font-weight: bold; cursor: pointer; }
        .cell:active { transform: scale(0.95); }
        .cell.x { color: #f87171; }
        .cell.o { color: #60a5fa; }
        .cell.winner { background: #4ade80 !important; animation: winPulse 0.5s ease; }
        @keyframes winPulse { 0%, 100% { transform: scale(1); } 50% { transform: scale(1.1); } }
        .controls { display: flex; gap: 10px; margin-top: 10px; }
        .btn { padding: 12px 25px; border: none; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; }
        .btn-reset { background: #f87171; color: #fff; }
        .btn-difficulty { background: #60a5fa; color: #fff; }
        .btn:active { transform: scale(0.95); }
        .game-message { position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: rgba(0,0,0,0.9); color: #fff; padding: 25px 35px; border-radius: 12px; font-size: 18px; text-align: center; z-index: 2000; display: none; }
        .game-message.show { display: block; }
        footer { flex-shrink: 0; padding: 5px 20px; font-size: 10px; color: #666; text-align: center; }
        footer a { color: #666; }
    </style>
<?php require_once ../header.php; ?>
<?php require_once '../header.php'; ?>
</head>
<body>
    
        
            <a href="../../index.php" class="logo">⭕ <?= SITE_NAME ?></a>
            <nav><a href="../../index.php">게임</a><a href="../../blog/">블로그</a></nav>
        </div>
    
    <main class="game-area">
        <div class="game-info">
            <div class="player-info active" id="playerX"><span>❌</span><span>당신</span></div>
            <div class="player-info" id="playerO"><span>⭕</span><span>CPU</span></div>
        </div>
        <div id="game-board"></div>
        <div class="controls">
            <button class="btn btn-difficulty" onclick="toggleDifficulty()"><span id="diffLabel">보통</span></button>
            <button class="btn btn-reset" onclick="resetGame()">새 게임</button>
        </div>
    </main>
    <div class="game-message" id="gameMessage"><div id="messageText"></div><button onclick="resetGame()" style="margin-top:15px;padding:12px 25px;border:none;border-radius:8px;background:#4ade80;color:#000;font-weight:bold;">다시하기</button></div>
    <footer><p>© <?= date('Y') ?> <a href="https://tomseol.pe.kr/" target="_blank">tomseol.pe.kr</a></p></footer>
    <script src="game.js"></script>
</body>
</html>
