<?php
/**
 * Tetris 게임 페이지 - 모바일 최적화 v3 (컴팩트)
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
    <title>테트리스 - <?= SITE_NAME ?></title>
    <link rel="stylesheet" href="../../css/style.css">
    <style>
        header { background: #fff; box-shadow: 0 2px 8px rgba(0,0,0,0.08); position: sticky; top: 0; z-index: 100; }
        html, body {
            overflow: hidden;
            height: 100%;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
        }
        
        body {
            display: flex;
            flex-direction: column;
            height: 100%;
        }
        
        .game-header-section {
            flex-shrink: 0;
            padding: 5px 15px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .game-header-section .logo {
            font-size: 14px;
        }
        
        .game-header-section nav a {
            font-size: 12px;
            margin-left: 10px;
        }
        
        .game-area {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 5px;
            overflow: hidden;
            touch-action: manipulation;
            -webkit-touch-callout: none;
            -webkit-user-select: none;
            user-select: none;
        }
        
        .main-content {
            display: flex;
            gap: 15px;
            align-items: flex-start;
            touch-action: manipulation;
        }
        
        #game-board {
            background: #000;
            border: 2px solid #444;
            border-radius: 4px;
            display: grid;
            gap: 0px;
            touch-action: manipulation;
        }
        
        .cell {
            width: 22px;
            height: 22px;
            background: #1a1a1a;
        }
        
        .cell.filled {
            border-radius: 2px;
            box-shadow: inset 0 0 4px rgba(255,255,255,0.2);
        }
        
        .I { background: linear-gradient(135deg, #00f5ff, #00a8b5); }
        .O { background: linear-gradient(135deg, #ffd700, #ffaa00); }
        .T { background: linear-gradient(135deg, #a855f7, #7c3aed); }
        .S { background: linear-gradient(135deg, #22c55e, #16a34a); }
        .Z { background: linear-gradient(135deg, #ef4444, #dc2626); }
        .J { background: linear-gradient(135deg, #3b82f6, #2563eb); }
        .L { background: linear-gradient(135deg, #f97316, #ea580c); }
        
        .side-panel {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        
        .next-piece {
            background: #000;
            border: 2px solid #333;
            border-radius: 4px;
            padding: 5px;
        }
        
        .next-piece-label {
            color: #888;
            font-size: 10px;
            text-align: center;
            margin-bottom: 3px;
        }
        
        #next-board {
            display: grid;
            gap: 0px;
        }
        
        #next-board .cell {
            width: 16px;
            height: 16px;
        }
        
        .stats {
            background: rgba(0,0,0,0.6);
            border-radius: 6px;
            padding: 8px;
            color: #fff;
        }
        
        .stat-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
            font-size: 11px;
        }
        
        .stat-value {
            font-weight: bold;
            color: #ffd700;
        }
        
        /* 컨트롤 - 한줄로 */
        .controls {
            display: flex;
            gap: 6px;
            margin-top: 8px;
            width: 100%;
            justify-content: center;
            touch-action: manipulation;
        }
        
        .control-btn {
            padding: 10px 14px;
            border: none;
            border-radius: 6px;
            font-size: 18px;
            cursor: pointer;
            user-select: none;
            -webkit-tap-highlight-color: transparent;
            -webkit-touch-callout: none;
            touch-action: manipulation;
        }
        
        .control-btn:active {
            transform: scale(0.9);
        }
        
        .btn-move {
            background: rgba(255,255,255,0.2);
            color: #fff;
        }
        
        .btn-down {
            background: #ffc107;
            color: #000;
        }
        
        .btn-rotate {
            background: #9c27b0;
            color: #fff;
        }
        
        .btn-drop {
            background: #f44336;
            color: #fff;
            font-weight: bold;
        }
        
        footer {
            flex-shrink: 0;
            padding: 3px 20px;
            font-size: 10px;
            color: #666;
        }
        
        footer a {
            color: #666;
        }
        
        .game-message {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(0,0,0,0.95);
            color: #fff;
            padding: 25px 30px;
            border-radius: 12px;
            font-size: 18px;
            text-align: center;
            z-index: 2000;
            display: none;
        }
        
        .game-message.show {
            display: block;
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
        <a href="../../index.php" class="logo">🎮 <?= SITE_NAME ?></a>
        <nav>
            <a href="../../index.php">게임</a>
            <a href="../../blog/">블로그</a>
        </nav>
    </header>

    <main class="game-area">
        <div class="main-content">
            <div id="game-board"></div>
            
            <div class="side-panel">
                <div class="next-piece">
                    <div class="next-piece-label">NEXT</div>
                    <div id="next-board"></div>
                </div>
                
                <div class="stats">
                    <div class="stat-item">
                        <span>SCORE</span>
                        <span class="stat-value" id="score">0</span>
                    </div>
                    <div class="stat-item">
                        <span>LEVEL</span>
                        <span class="stat-value" id="level">1</span>
                    </div>
                    <div class="stat-item">
                        <span>LINES</span>
                        <span class="stat-value" id="lines">0</span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="controls">
            <button class="control-btn btn-drop" onclick="hardDrop()">DROP</button>
            <button class="control-btn btn-move" onclick="moveLeft()">⬅️</button>
            <button class="control-btn btn-down" onclick="moveDown()">⬇️</button>
            <button class="control-btn btn-move" onclick="moveRight()">➡️</button>
            <button class="control-btn btn-rotate" onclick="rotate()">↻</button>
        </div>
    </main>

    <div class="game-message" id="gameMessage">
        <div id="messageText"></div>
        <button class="btn btn-primary" onclick="startGame()" style="margin-top:10px;padding:8px 20px;">🔄 다시하기</button>
    </div>

    <footer>
        <p>© <?= date('Y') ?> <a href="https://tomseol.pe.kr/" target="_blank">tomseol.pe.kr</a></p>
    </footer>

    <script src="game.js"></script>
</body>
</html>
