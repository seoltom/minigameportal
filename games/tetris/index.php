<?php
require_once '../../config.php';
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Tetris - <?= SITE_NAME ?></title>
    <link rel="stylesheet" href="../../css/style.css">
    <style>
        html, body { overflow: hidden; height: 100%; margin: 0; padding: 0; background: #1a1a2e; }
        body { display: flex; flex-direction: column; height: 100%; touch-action: manipulation; user-select: none; }
        header { background: #fff; box-shadow: 0 2px 8px rgba(0,0,0,0.08); position: sticky; top: 0; z-index: 100; flex-shrink: 0; }
        .header-content { display: flex; justify-content: space-between; align-items: center; padding: 10px 20px; max-width: 1200px; margin: 0 auto; }
        .logo { font-size: 18px; font-weight: bold; color: #4f46e5; }
        nav { display: flex; gap: 20px; }
        nav a { font-size: 14px; color: #666; text-decoration: none; }
        .game-area { flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 10px; }
        .game-container { display: flex; gap: 15px; align-items: flex-start; }
        #game-board { background: #000; border: 2px solid #333; border-radius: 4px; display: grid; gap: 1px; }
        .cell { width: 28px; height: 28px; background: #111; }
        .cell.filled { border-radius: 3px; }
        .I { background: linear-gradient(135deg, #00f5ff, #00a8b5); }
        .O { background: linear-gradient(135deg, #ffd700, #ffaa00); }
        .T { background: linear-gradient(135deg, #a855f7, #7c3aed); }
        .S { background: linear-gradient(135deg, #22c55e, #16a34a); }
        .Z { background: linear-gradient(135deg, #ef4444, #dc2626); }
        .J { background: linear-gradient(135deg, #3b82f6, #2563eb); }
        .L { background: linear-gradient(135deg, #f97316, #ea580c); }
        .side-panel { display: flex; flex-direction: column; gap: 10px; }
        .next-piece { background: #000; border: 2px solid #333; border-radius: 4px; padding: 5px; }
        .next-piece-label { color: #888; font-size: 10px; text-align: center; margin-bottom: 5px; }
        #next-board { display: grid; gap: 1px; }
        #next-board .cell { width: 20px; height: 20px; }
        .stats { background: rgba(0,0,0,0.5); border-radius: 8px; padding: 10px; }
        .stat-item { display: flex; justify-content: space-between; margin-bottom: 5px; font-size: 12px; color: #fff; }
        .stat-value { font-weight: bold; color: #ffd700; }
        .controls { display: flex; gap: 6px; margin-top: 10px; width: 100%; max-width: 280px; justify-content: center; }
        .control-btn { padding: 12px; border: none; border-radius: 8px; font-size: 18px; cursor: pointer; }
        .control-btn:active { transform: scale(0.92); }
        .btn-move { background: rgba(255,255,255,0.2); color: #fff; }
        .btn-down { background: #ffc107; color: #000; }
        .btn-rotate { background: #9c27b0; color: #fff; }
        .btn-drop { background: #f44336; color: #fff; font-size: 14px; font-weight: bold; }
        .game-message { position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: rgba(0,0,0,0.9); color: #fff; padding: 25px 35px; border-radius: 12px; font-size: 18px; text-align: center; z-index: 2000; display: none; }
        .game-message.show { display: block; }
        footer { flex-shrink: 0; padding: 5px 20px; font-size: 10px; color: #666; text-align: center; }
        footer a { color: #666; }
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
        <div class="game-container">
            <div id="game-board"></div>
            <div class="side-panel">
                <div class="next-piece">
                    <div class="next-piece-label">NEXT</div>
                    <div id="next-board"></div>
                </div>
                <div class="stats">
                    <div class="stat-item"><span>SCORE</span><span class="stat-value" id="score">0</span></div>
                    <div class="stat-item"><span>LEVEL</span><span class="stat-value" id="level">1</span></div>
                    <div class="stat-item"><span>LINES</span><span class="stat-value" id="lines">0</span></div>
                </div>
            </div>
        </div>
        <div class="controls">
            <button class="control-btn btn-rotate" onclick="rotate()">↻</button>
            <button class="control-btn btn-move" onclick="moveLeft()">⬅️</button>
            <button class="control-btn btn-move" onclick="moveRight()">➡️</button>
            <button class="control-btn btn-down" onclick="moveDown()">⬇️</button>
            <button class="control-btn btn-drop" onclick="hardDrop()">DROP</button>
        </div>
    </main>
    <div class="game-message" id="gameMessage">
        <div id="messageText"></div>
        <button onclick="startGame()" style="margin-top:10px;padding:10px 20px;border:none;border-radius:6px;background:#4ade80;color:#000;font-weight:bold;">다시하기</button>
    </div>
    <footer><p>© <?= date('Y') ?> <a href="https://tomseol.pe.kr/" target="_blank">tomseol.pe.kr</a></p></footer>
    <script src="game.js"></script>
</body>
</html>
