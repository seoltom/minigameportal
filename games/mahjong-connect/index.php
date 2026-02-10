<?php
/**
 * 마작 연결 게임 페이지 - 모바일 최적화
 */
require_once '../../config.php';
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Mahjong Connect - <?= SITE_NAME ?></title>
    <link rel="stylesheet" href="../../css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Pretendard:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .game-container {
            max-width: 100%;
            margin: 0 auto;
            padding: 10px;
        }
        
        .game-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            flex-wrap: wrap;
            gap: 8px;
        }
        
        .game-title {
            font-size: 24px;
            font-weight: bold;
            color: #776e65;
        }
        
        .score-container {
            display: flex;
            gap: 8px;
        }
        
        .score-box {
            background: #bbada0;
            color: #fff;
            padding: 8px 12px;
            border-radius: 6px;
            text-align: center;
            min-width: 60px;
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
        
        .game-controls {
            display: flex;
            gap: 8px;
            margin-bottom: 15px;
            flex-wrap: wrap;
            align-items: center;
        }
        
        .level-select {
            display: flex;
            align-items: center;
            gap: 8px;
            flex: 1;
        }
        
        .level-select label {
            font-size: 14px;
            white-space: nowrap;
        }
        
        .level-select select {
            flex: 1;
            padding: 10px;
            border-radius: 6px;
            border: 2px solid #bbada0;
            font-size: 14px;
            background: #fff;
            cursor: pointer;
        }
        
        .btn {
            padding: 10px 16px;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            white-space: nowrap;
        }
        
        .btn-primary {
            background: #8f7a66;
            color: #f9f6f2;
        }
        
        .btn-primary:active {
            transform: scale(0.95);
        }
        
        #game-board {
            background: #faf8ef;
            border-radius: 8px;
            padding: 8px;
            display: grid;
            gap: 3px;
            margin-bottom: 15px;
            justify-content: center;
            overflow-x: auto;
        }
        
        .tile {
            min-width: 40px;
            height: 45px;
            background: #c9b99a;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            cursor: pointer;
            transition: all 0.1s;
            user-select: none;
            -webkit-tap-highlight-color: transparent;
        }
        
        .tile:active {
            transform: scale(0.95);
        }
        
        .tile.selected {
            border: 3px solid #f59563;
            background: #f2b179;
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
            padding: 15px;
            border-radius: 6px;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 15px;
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
        
        .instructions {
            background: #eee4da;
            padding: 12px;
            border-radius: 6px;
            color: #776e65;
            font-size: 13px;
            line-height: 1.6;
        }
        
        .instructions h3 {
            margin-bottom: 8px;
            font-size: 14px;
        }
        
        .instructions p {
            margin-bottom: 5px;
        }
        
        /* 모바일 최적화 */
        @media (max-width: 480px) {
            .game-title {
                font-size: 20px;
            }
            
            .score-box {
                padding: 6px 10px;
                min-width: 50px;
            }
            
            .score-value {
                font-size: 16px;
            }
            
            .tile {
                min-width: 32px;
                height: 38px;
                font-size: 18px;
            }
            
            .btn {
                padding: 8px 12px;
                font-size: 13px;
            }
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
                        <div class="score-label">남음</div>
                        <div class="score-value" id="pairs">0</div>
                    </div>
                </div>
            </div>
            
            <div class="game-controls">
                <div class="level-select">
                    <label>레벨:</label>
                    <select id="level" onchange="initGame()">
                        <option value="easy">쉬움</option>
                        <option value="normal" selected>보통</option>
                        <option value="hard">어려움</option>
                    </select>
                </div>
            </div>
            
            <div class="game-controls">
                <button class="btn btn-primary" style="flex:1" onclick="initGame()">🔄 새 게임</button>
                <button class="btn btn-primary" style="flex:1" onclick="showHint()">💡 힌트</button>
            </div>
            
            <div id="game-message" class="game-message"></div>
            
            <div id="game-board"></div>
            
            <div class="instructions">
                <h3>🎮 게임 방법</h3>
                <p>1. 같은 타일 2개를 터치해서 선택하세요.</p>
                <p>2. 경로가 2번 이하로 꺾여야 매칭됩니다.</p>
                <p>3. 모든 타일을 제거하면 클리어!</p>
            </div>
        </div>
    </main>

    <footer>
        <p>© <?= date('Y') ?> <a href="https://tomseol.pe.kr/" target="_blank">tomseol.pe.kr</a></p>
    </footer>

    <script src="game.js"></script>
</body>
</html>
