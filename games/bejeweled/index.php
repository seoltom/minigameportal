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
    
        html, body { overflow: hidden; height: 100%; margin: 0; background: #2c3e50; }
        body { display: flex; flex-direction: column; height: 100%; touch-action: manipulation; user-select: none; }
        header { background: #fff; box-shadow: 0 2px 8px rgba(0,0,0,0.08); position: sticky; top: 0; z-index: 100; flex-shrink: 0; }
        .header-content { display: flex; justify-content: space-between; align-items: center; padding: 10px 20px; max-width: 1200px; margin: 0 auto; }
        .logo { font-size: 18px; font-weight: bold; color: #4f46e5; }
        nav { display: flex; gap: 20px; }
        nav a { font-size: 14px; color: #666; text-decoration: none; }
        .game-area { flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 10px; }
        .game-info { display: flex; gap: 15px; margin-bottom: 10px; width: 100%; max-width: 400px; }
        .info-box { flex: 1; background: rgba(0,0,0,0.5); color: #fff; padding: 8px; border-radius: 6px; text-align: center; }
        .info-label { font-size: 10px; opacity: 0.8; }
        .info-value { font-size: 18px; font-weight: bold; color: #ffd700; }
        .controls { display: flex; gap: 8px; margin-bottom: 10px; width: 100%; max-width: 400px; }
        .btn { flex: 1; padding: 12px; border: none; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; background: #8f7a66; color: #fff; }
        .btn:active { transform: scale(0.95); }
        .game-board-container { flex: 1; display: flex; align-items: center; justify-content: center; }
        #game-board { background: linear-gradient(135deg, #2c3e50, #34495e); border-radius: 8px; display: grid; gap: 3px; }
        .gem { border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 28px; cursor: pointer; transition: all 0.15s; }
        .gem:active { transform: scale(0.9); }
        .gem.selected { transform: scale(1.1); box-shadow: 0 0 15px rgba(255,255,255,0.8); }
        .gem-0 { background: linear-gradient(135deg, #e74c3c, #c0392b); }
        .gem-1 { background: linear-gradient(135deg, #3498db, #2980b9); }
        .gem-2 { background: linear-gradient(135deg, #2ecc71, #27ae60); }
        .gem-3 { background: linear-gradient(135deg, #f1c40f, #f39c12); }
        .gem-4 { background: linear-gradient(135deg, #9b59b6, #8e44ad); }
        .gem-5 { background: linear-gradient(135deg, #e67e22, #d35400); }
        .gem-6 { background: linear-gradient(135deg, #1abc9c, #16a085); }
        .gem-7 { background: linear-gradient(135deg, #ecf0f1, #bdc3c7); }
        .game-message { position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: rgba(0,0,0,0.9); color: #fff; padding: 25px 35px; border-radius: 12px; font-size: 18px; text-align: center; z-index: 2000; display: none; }
        .game-message.show { display: block; }
        footer { flex-shrink: 0; padding: 5px 20px; font-size: 10px; color: #999; text-align: center; }
        footer a { color: #999; }
    </style>
<?php require_once ../header.php; ?>
<?php require_once '../header.php'; ?>
</head>
<body>
    
        
            <a href="../../index.php" class="logo">🎮 <?= SITE_NAME ?></a>
            <nav><a href="../../index.php">게임</a><a href="../../blog/">블로그</a></nav>
        </div>
    
    <main class="game-area">
        <div class="game-info">
            <div class="info-box"><div class="info-label">SCORE</div><div class="info-value" id="score">0</div></div>
            <div class="info-box"><div class="info-label">LEVEL</div><div class="info-value" id="level">1</div></div>
            <div class="info-box"><div class="info-label">TARGET</div><div class="info-value" id="target">500</div></div>
        </div>
        <div class="controls">
            <button class="btn" onclick="initGame()">새 게임</button>
            <button class="btn" onclick="showHint()">힌트</button>
        </div>
        <div class="game-board-container"><div id="game-board"></div></div>
    </main>
    <div class="game-message" id="gameMessage"><div id="messageText"></div><button onclick="initGame()" style="margin-top:10px;padding:10px 20px;border:none;border-radius:6px;background:#fff;color:#000;font-weight:bold;">다시하기</button></div>
    <footer><p>© <?= date('Y') ?> <a href="https://tomseol.pe.kr/" target="_blank">tomseol.pe.kr</a></p></footer>
    <script src="game.js"></script>
</body>
</html>
