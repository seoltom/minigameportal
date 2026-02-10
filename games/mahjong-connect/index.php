<?php
/**
 * 마작 연결 게임 페이지
 */
require_once '../../config.php';
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mahjong Connect - <?= SITE_NAME ?></title>
    <link rel="stylesheet" href="../../css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Pretendard:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .game-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .game-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 10px;
        }
        
        .game-title {
            font-size: 36px;
            font-weight: bold;
            color: #776e65;
        }
        
        .score-container {
            display: flex;
            gap: 10px;
        }
        
        .score-box {
            background: #bbada0;
            color: #fff;
            padding: 10px 20px;
            border-radius: 6px;
            text-align: center;
        }
        
        .score-label {
            font-size: 12px;
            text-transform: uppercase;
            opacity: 0.8;
        }
        
        .score-value {
            font-size: 24px;
            font-weight: bold;
        }
        
        .game-controls {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        
        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .btn-primary {
            background: #8f7a66;
            color: #f9f6f2;
        }
        
        .btn-primary:hover {
            background: #776e65;
        }
        
        #game-board {
            background: #faf8ef;
            border-radius: 8px;
            padding: 10px;
            display: grid;
            gap: 4px;
            margin-bottom: 20px;
            justify-content: center;
        }
        
        .tile {
            width: 50px;
            height: 60px;
            background: #c9b99a;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            cursor: pointer;
            transition: all 0.15s;
            user-select: none;
            border: 2px solid transparent;
        }
        
        .tile:hover {
            background: #d4c4a8;
        }
        
        .tile.selected {
            border-color: #f59563;
            background: #f2b179;
            transform: scale(1.05);
        }
        
        .tile.matched {
            visibility: hidden;
            opacity: 0;
            pointer-events: none;
        }
        
        .tile.hint {
            animation: pulse 0.5s infinite;
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }
        
        .game-message {
            text-align: center;
            padding: 20px;
            border-radius: 6px;
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 20px;
            display: none;
        }
        
        .game-message.win {
            background: #edc22e;
            color: #f9f6f2;
            display: block;
        }
        
        .game-message.over {
            background: #776e65;
            color: #f9f6f2;
            display: block;
        }
        
        .level-select {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        
        .level-select select {
            padding: 10px 15px;
            border-radius: 6px;
            border: 2px solid #bbada0;
            font-size: 16px;
            background: #fff;
            cursor: pointer;
        }
        
        .instructions {
            background: #eee4da;
            padding: 15px;
            border-radius: 6px;
            color: #776e65;
            font-size: 14px;
            line-height: 1.8;
        }
        
        .instructions h3 {
            margin-bottom: 10px;
        }
        
        /* 연결선 */
        .connection-line {
            position: fixed;
            pointer-events: none;
            z-index: 1000;
        }
    </style>
</head>
<body>
    <header>
        <div class="header-content">
            <a href="../../index.php" class="logo">🎮 <?= SITE_NAME ?></a>
            <nav>
                <a href="../../index.php">미니게임</a>
                <a href="../../blog/">블로그</a>
            </nav>
        </div>
    </header>

    <main class="container">
        <div class="game-container">
            <div class="game-header">
                <h1 class="game-title">🀄 Mahjong Connect</h1>
                <div class="score-container">
                    <div class="score-box">
                        <div class="score-label">Score</div>
                        <div class="score-value" id="score">0</div>
                    </div>
                    <div class="score-box">
                        <div class="score-label">Time</div>
                        <div class="score-value" id="time">0:00</div>
                    </div>
                    <div class="score-box">
                        <div class="score-label">Pairs</div>
                        <div class="score-value" id="pairs">0</div>
                    </div>
                </div>
            </div>
            
            <div class="game-controls">
                <div class="level-select">
                    <label>레벨:</label>
                    <select id="level" onchange="initGame()">
                        <option value="easy">쉬움 (6x4)</option>
                        <option value="normal" selected>보통 (8x6)</option>
                        <option value="hard">어려움 (10x8)</option>
                    </select>
                </div>
                <button class="btn btn-primary" onclick="initGame()">새 게임</button>
                <button class="btn btn-primary" onclick="showHint()">힌트</button>
            </div>
            
            <div id="game-message" class="game-message"></div>
            
            <div id="game-board"></div>
            
            <div class="instructions">
                <h3>🎮 게임 방법</h3>
                <p>1. 같은 그림의 타일 2개를 클릭하여 선택하세요.</p>
                <p>2. 타일 사이의 연결 경로가 2번 이하의 방향 전환으로 연결되어야 합니다.</p>
                <p>3. 모든 타일을 제거하면 클리어!</p>
            </div>
        </div>
    </main>

    <footer>
        <p>© <?= date('Y') ?> <a href="https://tomseol.pe.kr/" target="_blank">tomseol.pe.kr</a>에서 제작한 <?= SITE_NAME ?></p>
    </footer>

    <script src="game.js"></script>
</body>
</html>
