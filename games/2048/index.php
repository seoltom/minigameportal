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
    <link rel="stylesheet" href="../css/style.css">
    <?php require_once '../../header.php'; ?>
    <style>
        html, body {
            overflow: hidden;
            height: 100%;
            margin: 0;
            padding: 0;
            background: #faf8ef;
        }
        
        body {
            display: flex;
            flex-direction: column;
            height: 100%;
            touch-action: manipulation;
            -webkit-touch-callout: none;
            -webkit-user-select: none;
            user-select: none;
        }
        
        .game-header-section {
            flex-shrink: 0;
            padding: 8px 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            box-sizing: border-box;
        }
        
        .game-header-section .logo {
            font-size: 16px;
            font-weight: bold;
            color: #776e65;
        }
        
        .game-header-section nav {
            display: flex;
            gap: 15px;
        }
        
        .game-header-section nav a {
            font-size: 13px;
            color: #776e65;
            text-decoration: none;
        }
        
        .game-header-section nav a:hover {
            text-decoration: underline;
        }
        
        .game-area {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 10px;
            overflow: hidden;
        }
        
        .score-bar {
            display: flex;
            gap: 10px;
            margin-bottom: 10px;
            width: 100%;
            max-width: 320px;
        }
        
        .score-box {
            flex: 1;
            background: #bbada0;
            color: #fff;
            padding: 8px;
            border-radius: 6px;
            text-align: center;
        }
        
        .score-label {
            font-size: 10px;
            text-transform: uppercase;
            opacity: 0.8;
        }
        
        .score-value {
            font-size: 18px;
            font-weight: bold;
        }
        
        #game-board {
            background: #bbada0;
            border-radius: 8px;
            padding: 8px;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 6px;
            width: 100%;
            max-width: 320px;
            touch-action: manipulation;
        }
        
        .cell {
            background: rgba(238, 228, 218, 0.35);
            border-radius: 4px;
            aspect-ratio: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            font-weight: bold;
            color: #776e65;
            transition: all 0.1s ease;
        }
        
        .cell[data-value="2"] { background: #eee4da; }
        .cell[data-value="4"] { background: #ede0c8; }
        .cell[data-value="8"] { background: #f2b179; color: #f9f6f2; }
        .cell[data-value="16"] { background: #f59563; color: #f9f6f2; }
        .cell[data-value="32"] { background: #f67c5f; color: #f9f6f2; }
        .cell[data-value="64"] { background: #f65e3b; color: #f9f6f2; }
        .cell[data-value="128"] { background: #edcf72; color: #f9f6f2; font-size: 22px; }
        .cell[data-value="256"] { background: #edcc61; color: #f9f6f2; font-size: 22px; }
        .cell[data-value="512"] { background: #edc850; color: #f9f6f2; font-size: 22px; }
        .cell[data-value="1024"] { background: #edc53f; color: #f9f6f2; font-size: 18px; }
        .cell[data-value="2048"] { background: #edc22e; color: #f9f6f2; font-size: 18px; }
        
        .cell.new {
            animation: pop 0.2s ease;
        }
        
        @keyframes pop {
            0% { transform: scale(0); }
            50% { transform: scale(1.2); }
            100% { transform: scale(1); }
        }
        
        .cell.merged {
            animation: merge 0.15s ease;
        }
        
        @keyframes merge {
            0% { transform: scale(1); }
            50% { transform: scale(1.15); }
            100% { transform: scale(1); }
        }
        
        .controls {
            display: flex;
            gap: 6px;
            margin-top: 12px;
            width: 100%;
            max-width: 320px;
            justify-content: center;
            touch-action: manipulation;
        }
        
        .control-btn {
            padding: 12px 16px;
            border: none;
            border-radius: 8px;
            font-size: 20px;
            cursor: pointer;
            -webkit-tap-highlight-color: transparent;
            -webkit-touch-callout: none;
        }
        
        .control-btn:active {
            transform: scale(0.92);
        }
        
        .btn-move {
            background: #8f7a66;
            color: #fff;
        }
        
        .btn-reset {
            background: #f65e3b;
            color: #fff;
            font-size: 14px;
        }
        
        .game-message {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(237, 194, 46, 0.95);
            color: #f9f6f2;
            padding: 25px 35px;
            border-radius: 12px;
            font-size: 20px;
            font-weight: bold;
            text-align: center;
            z-index: 2000;
            display: none;
        }
        
        .game-message.show {
            display: block;
        }
        
        .game-message.over {
            background: rgba(119, 110, 101, 0.95);
        }
        
        footer {
            flex-shrink: 0;
            padding: 3px 20px;
            font-size: 10px;
            color: #999;
            text-align: center;
        }
        
        footer a {
            color: #999;
        }
    </style>
</head>
<body>
    <header class="game-header-section">
        <a href="../index.php" class="logo">🎮 2048</a>
        <nav>
            <a href="../index.php">게임</a>
            <a href="../blog/">블로그</a>
        </nav>
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
        <button onclick="initGame()" style="margin-top:10px;padding:10px 20px;border:none;border-radius:6px;background:#fff;color:#776e65;font-weight:bold;">다시하기</button>
    </div>

    <footer>
        <p>© <?= date('Y') ?> <a href="https://tomseol.pe.kr/" target="_blank">tomseol.pe.kr</a></p>
    </footer>

    <script src="game.js"></script>
</body>
</html>
