<?php
/**
 * 2048 게임 페이지
 */
require_once '../config.php';
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>2048 - <?= SITE_NAME ?></title>
    <link rel="stylesheet" href="../css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Pretendard:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .game-container {
            max-width: 500px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .game-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .game-title {
            font-size: 48px;
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
            min-width: 80px;
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
        
        #game-board {
            background: #bbada0;
            border-radius: 8px;
            padding: 15px;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .cell {
            background: rgba(238, 228, 218, 0.35);
            border-radius: 6px;
            aspect-ratio: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 48px;
            font-weight: bold;
            color: #776e65;
            transition: all 0.15s ease;
        }
        
        .cell[data-value="2"] { background: #eee4da; }
        .cell[data-value="4"] { background: #ede0c8; }
        .cell[data-value="8"] { background: #f2b179; color: #f9f6f2; }
        .cell[data-value="16"] { background: #f59563; color: #f9f6f2; }
        .cell[data-value="32"] { background: #f67c5f; color: #f9f6f2; }
        .cell[data-value="64"] { background: #f65e3b; color: #f9f6f2; }
        .cell[data-value="128"] { background: #edcf72; color: #f9f6f2; font-size: 36px; }
        .cell[data-value="256"] { background: #edcc61; color: #f9f6f2; font-size: 36px; }
        .cell[data-value="512"] { background: #edc850; color: #f9f6f2; font-size: 36px; }
        .cell[data-value="1024"] { background: #edc53f; color: #f9f6f2; font-size: 28px; }
        .cell[data-value="2048"] { background: #edc22e; color: #f9f6f2; font-size: 28px; }
        
        .game-controls {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
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
        
        .game-message {
            text-align: center;
            padding: 15px;
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
    </style>
</head>
<body>
    <header>
        <div class="header-content">
            <a href="../index.php" class="logo">🎮 <?= SITE_NAME ?></a>
            <nav>
                <a href="../index.php">미니게임</a>
                <a href="../blog/">블로그</a>
            </nav>
        </div>
    </header>

    <main class="container">
        <div class="game-container">
            <div class="game-header">
                <h1 class="game-title">2048</h1>
                <div class="score-container">
                    <div class="score-box">
                        <div class="score-label">Score</div>
                        <div class="score-value" id="score">0</div>
                    </div>
                    <div class="score-box">
                        <div class="score-label">Best</div>
                        <div class="score-value" id="best-score">0</div>
                    </div>
                </div>
            </div>
            
            <div class="game-controls">
                <button class="btn btn-primary" onclick="initGame()">새 게임</button>
            </div>
            
            <div id="game-message" class="game-message"></div>
            
            <div id="game-board"></div>
            
            <div class="instructions">
                <h3>🎮 게임 방법</h3>
                <p>화살표 키 (← ↑ → ↓) 또는 스와이프로 타일을 이동하세요.</p>
                <p>같은 숫자의 타기가 만나면 합쳐집니다. 2048을 만들어 보세요!</p>
            </div>
        </div>
    </main>

    <footer>
        <p>© <?= date('Y') ?> <a href="https://tomseol.pe.kr/" target="_blank">tomseol.pe.kr</a>에서 제작한 <?= SITE_NAME ?></p>
    </footer>

    <script src="game.js"></script>
</body>
</html>
