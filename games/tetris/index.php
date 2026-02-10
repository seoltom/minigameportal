<?php
/**
 * Tetris 게임 페이지 - 모바일 최적화
 */
require_once '../../config.php';
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Tetris - <?= SITE_NAME ?></title>
    <link rel="stylesheet" href="../../css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Pretendard:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        html, body {
            overflow: hidden;
            height: 100%;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
        }
        
        body {
            display: flex;
            flex-direction: column;
            height: 100%;
        }
        
        .game-header-section {
            flex-shrink: 0;
            transition: transform 0.3s ease;
        }
        
        .game-header-section.hidden {
            transform: translateY(-100%);
            position: absolute;
            width: 100%;
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
        
        .tetris-container {
            display: flex;
            gap: 15px;
            align-items: flex-start;
        }
        
        #game-board {
            background: #000;
            border: 3px solid #333;
            border-radius: 4px;
            display: grid;
            gap: 1px;
        }
        
        .cell {
            width: 28px;
            height: 28px;
            background: #111;
            border-radius: 2px;
        }
        
        .cell.filled {
            border-radius: 3px;
            box-shadow: inset 0 0 10px rgba(255,255,255,0.3);
        }
        
        /* 테트로미노 색상 */
        .I { background: linear-gradient(135deg, #00f5ff, #00a8b5); }
        .O { background: linear-gradient(135deg, #ffd700, #ffaa00); }
        .T { background: linear-gradient(135deg, #a855f7, #7c3aed); }
        .S { background: linear-gradient(135deg, #22c55e, #16a34a); }
        .Z { background: linear-gradient(135deg, #ef4444, #dc2626); }
        .J { background: linear-gradient(135deg, #3b82f6, #2563eb); }
        .L { background: linear-gradient(135deg, #f97316, #ea580c); }
        
        /* 사이드 패널 */
        .side-panel {
            display: flex;
            flex-direction: column;
            gap: 10px;
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
            margin-bottom: 5px;
        }
        
        #next-board {
            display: grid;
            gap: 1px;
        }
        
        .stats {
            background: rgba(0,0,0,0.5);
            border-radius: 8px;
            padding: 10px;
            color: #fff;
        }
        
        .stat-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
            font-size: 12px;
        }
        
        .stat-value {
            font-weight: bold;
            color: #ffd700;
        }
        
        /* 컨트롤 버튼 */
        .controls {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 8px;
            width: 100%;
            max-width: 250px;
        }
        
        .control-btn {
            padding: 15px;
            border: none;
            border-radius: 8px;
            font-size: 20px;
            cursor: pointer;
            user-select: none;
            -webkit-tap-highlight-color: transparent;
            touch-action: manipulation;
        }
        
        .control-btn:active {
            transform: scale(0.95);
        }
        
        .btn-left, .btn-right {
            background: rgba(255,255,255,0.2);
            color: #fff;
        }
        
        .btn-down {
            background: rgba(255,193,7,0.8);
            color: #000;
        }
        
        .btn-rotate {
            background: rgba(156,39,176,0.8);
            color: #fff;
        }
        
        .btn-drop {
            background: rgba(244,67,54,0.8);
            color: #fff;
            grid-column: span 2;
        }
        
        footer {
            flex-shrink: 0;
            padding: 5px 20px;
            font-size: 11px;
            color: #888;
        }
        
        footer a {
            color: #888;
        }
        
        /* 게임 오버레이 */
        .game-message {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(0,0,0,0.95);
            color: #fff;
            padding: 30px 40px;
            border-radius: 16px;
            font-size: 20px;
            font-weight: bold;
            text-align: center;
            z-index: 2000;
            display: none;
        }
        
        .game-message.show {
            display: block;
        }
        
        .toggle-header-btn {
            position: fixed;
            top: 10px;
            right: 10px;
            z-index: 1000;
            background: rgba(255,255,255,0.9);
            border: none;
            border-radius: 20px;
            padding: 8px 12px;
            font-size: 12px;
            cursor: pointer;
            display: none;
        }
        
        .toggle-header-btn.show {
            display: block;
        }
    </style>
</head>
<body>
    <header class="game-header-section" id="headerSection">
        <div class="header-content">
            <a href="../../index.php" class="logo">🎮 <?= SITE_NAME ?></a>
            <nav>
                <a href="../../index.php">미니게임</a>
                <a href="../../blog/">블로그</a>
            </nav>
        </div>
    </header>

    <main class="game-area">
        <div class="tetris-container">
            <!-- 게임 보드 -->
            <div id="game-board"></div>
            
            <!-- 사이드 패널 -->
            <div class="side-panel">
                <!-- 다음 조각 -->
                <div class="next-piece">
                    <div class="next-piece-label">NEXT</div>
                    <div id="next-board"></div>
                </div>
                
                <!-- 통계 -->
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
        
        <!-- 모바일 컨트롤 -->
        <div class="controls">
            <button class="control-btn btn-rotate" onclick="rotate()">↻</button>
            <button class="control-btn btn-left" onclick="moveLeft()">⬅️</button>
            <button class="control-btn btn-right" onclick="moveRight()">➡️</button>
            <button class="control-btn btn-down" onclick="moveDown()">⬇️</button>
            <button class="control-btn btn-drop" onclick="hardDrop()">⬇️⬇️ DROP</button>
        </div>
    </main>

    <button class="toggle-header-btn" id="toggleBtn" onclick="toggleHeader()">⬇️ 메뉴</button>

    <div class="game-message" id="gameMessage">
        <div id="messageText"></div>
        <button class="btn btn-primary" onclick="startGame()">🔄 다시하기</button>
    </div>

    <footer>
        <p>© <?= date('Y') ?> <a href="https://tomseol.pe.kr/" target="_blank">tomseol.pe.kr</a></p>
    </footer>

    <script src="game.js"></script>
</body>
</html>
