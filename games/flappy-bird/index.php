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
    
        html, body { overflow: hidden; height: 100%; margin: 0; background: linear-gradient(to bottom, #70c5ce 60%, #ded895 60%); }
        body { display: flex; flex-direction: column; height: 100%; touch-action: manipulation; user-select: none; }
        header { background: rgba(0,0,0,0.3); position: sticky; top: 0; z-index: 100; flex-shrink: 0; }
        .header-content { display: flex; justify-content: space-between; align-items: center; padding: 10px 20px; max-width: 1200px; margin: 0 auto; }
        .logo { font-size: 18px; font-weight: bold; color: #fff; text-shadow: 2px 2px 0 #000; }
        nav { display: flex; gap: 20px; }
        nav a { font-size: 14px; color: #fff; text-shadow: 1px 1px 0 #000; text-decoration: none; }
        .game-area { flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 10px; }
        #game-canvas { width: 100%; max-width: 400px; height: 100%; max-height: 450px; background: linear-gradient(to bottom, #70c5ce, #ded895); border-radius: 10px; border: 3px solid #4a3728; position: relative; overflow: hidden; }
        #bird { position: absolute; font-size: 35px; z-index: 10; transition: transform 0.1s; }
        .score-display { position: absolute; top: 20px; left: 50%; transform: translateX(-50%); font-size: 50px; font-weight: bold; color: #fff; text-shadow: 3px 3px 0 #000; z-index: 100; }
        .pipe { position: absolute; width: 50px; background: linear-gradient(to right, #73bf2e, #9ce659, #73bf2e); border-radius: 5px; border: 2px solid #558c22; z-index: 5; }
        .pipe::before { content:''; position: absolute; left: 50%; transform: translateX(-50%); width: 60px; height: 25px; background: linear-gradient(to right, #73bf2e, #9ce659, #73bf2e); border-radius: 5px; border: 2px solid #558c22; }
        .pipe-top::before { top: -12px; }
        .pipe-bottom::before { bottom: -12px; }
        .controls-hint { position: absolute; bottom: 20%; left: 50%; transform: translateX(-50%); background: rgba(0,0,0,0.6); color: #fff; padding: 10px 20px; border-radius: 20px; font-size: 14px; z-index: 100; }
        .game-message { position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: rgba(0,0,0,0.9); color: #fff; padding: 25px 35px; border-radius: 12px; font-size: 18px; text-align: center; z-index: 2000; display: none; }
        .game-message.show { display: block; }
        footer { flex-shrink: 0; padding: 5px 20px; font-size: 10px; color: #555; text-align: center; }
        footer a { color: #555; }
    </style>
<?php require_once ../header.php; ?>
<?php require_once '../header.php'; ?>
</head>
<body>
    
        
            <a href="../../index.php" class="logo">🐦 <?= SITE_NAME ?></a>
            <nav><a href="../../index.php">게임</a><a href="../../blog/">블로그</a></nav>
        </div>
    
    <main class="game-area">
        <div id="game-canvas">
            <div class="score-display" id="score">0</div>
            <div class="controls-hint" id="hint">👆 탭하여 점프!</div>
            <div id="bird">🐤</div>
        </div>
    </main>
    <div class="game-message" id="gameMessage"><div id="messageText"></div><button onclick="startGame()" style="margin-top:15px;padding:12px 25px;border:none;border-radius:8px;background:#73bf2e;color:#fff;font-weight:bold;">다시하기</button></div>
    <footer><p>© <?= date('Y') ?> <a href="https://tomseol.pe.kr/" target="_blank">tomseol.pe.kr</a></p></footer>
    <script src="game.js"></script>
</body>
</html>
