<?php
/**
 * Minesweeper 게임 페이지 - 모바일 최적화
 */
require_once '../../config.php';
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <title>지뢰 찾기 - <?= SITE_NAME ?></title>
    <link rel="stylesheet" href="../../css/style.css">
    <style>
        header { background: #fff; box-shadow: 0 2px 8px rgba(0,0,0,0.08); position: sticky; top: 0; z-index: 100; }
        html, body {
            overflow: hidden;
            height: 100%;
            background: linear-gradient(135deg, #2c3e50 0%, #1a252f 100%);
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
            color: #fff;
        }
        
        .game-header-section nav {
            display: flex;
            gap: 15px;
        }
        
        .game-header-section nav a {
            font-size: 13px;
            color: #888;
            text-decoration: none;
        }
        
        .game-area {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 10px;
            gap: 10px;
        }
        
        .game-info {
            display: flex;
            gap: 20px;
            color: #fff;
            font-size: 14px;
            margin-bottom: 5px;
        }
        
        .info-item {
            background: rgba(0,0,0,0.4);
            padding: 8px 15px;
            border-radius: 8px;
        }
        
        #game-board {
            display: grid;
            gap: 2px;
            background: #333;
            padding: 5px;
            border-radius: 8px;
        }
        
        .cell {
            width: 32px;
            height: 32px;
            background: #6b7280;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
            user-select: none;
        }
        
        .cell:active {
            transform: scale(0.95);
        }
        
        .cell.revealed {
            background: #d1d5db;
            color: #1f2937;
        }
        
        .cell.flagged {
            background: #f59e0b;
        }
        
        .cell.mine {
            background: #ef4444 !important;
        }
        
        .cell[data-num="1"] { color: #3b82f6; }
        .cell[data-num="2"] { color: #22c55e; }
        .cell[data-num="3"] { color: #ef4444; }
        .cell[data-num="4"] { color: #8b5cf6; }
        .cell[data-num="5"] { color: #f97316; }
        .cell[data-num="6"] { color: #06b6d4; }
        .cell[data-num="7"] { color: #1f2937; }
        .cell[data-num="8"] { color: #6b7280; }
        
        .controls {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }
        
        .btn {
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
        }
        
        .btn-reset {
            background: #f87171;
            color: #fff;
        }
        
        .btn-difficulty {
            background: #60a5fa;
            color: #fff;
        }
        
        .game-message {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(0,0,0,0.9);
            color: #fff;
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
        
        footer {
            flex-shrink: 0;
            padding: 5px 20px;
            font-size: 10px;
            color: #666;
            text-align: center;
        }
    </style>
</head>
<body>
    <header>
    <div class="header-content">
        <a href="../index.php" class="logo">🎮 <?= SITE_NAME</a>
        <nav>
            <a href="../index.php">미니게임</a>
            <a href="../blog/">블로그</a>
        </nav>
    </div>
</header>
        <a href="../../index.php" class="logo">💣 Minesweeper</a>
        <nav>
            <a href="../../index.php">게임</a>
            <a href="../../blog/">블로그</a>
        </nav>
    </header>

    <main class="game-area">
        <div class="game-info">
            <div class="info-item">💣 <span id="mines">10</span></div>
            <div class="info-item">⏱️ <span id="timer">0</span></div>
        </div>
        
        <div id="game-board"></div>
        
        <div class="controls">
            <button class="btn btn-difficulty" onclick="toggleDifficulty()">
                <span id="diffLabel">보통</span>
            </button>
            <button class="btn btn-reset" onclick="initGame()">🔄 새로하기</button>
        </div>
    </main>

    <div class="game-message" id="gameMessage">
        <div id="messageText"></div>
        <button onclick="initGame()" style="margin-top:15px;padding:12px 25px;border:none;border-radius:8px;background:#4ade80;color:#000;font-weight:bold;font-size:16px;">다시하기</button>
    </div>

    <footer>
        <p>© <?= date('Y') ?> <a href="https://tomseol.pe.kr/" target="_blank">tomseol.pe.kr</a></p>
    </footer>

    <script src="game.js"></script>
</body>
</html>
