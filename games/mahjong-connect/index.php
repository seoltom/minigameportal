<?php
/**
 * 마작 연결 게임 페이지 - 모바일 최적화 v3
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
    <title>마작 연결 - <?= SITE_NAME ?></title>
    <link rel="stylesheet" href="../../css/style.css">
    <style>
        header { background: #fff; box-shadow: 0 2px 8px rgba(0,0,0,0.08); position: sticky; top: 0; z-index: 100; }
        html, body {
            overflow: hidden;
            height: 100%;
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
        
        .game-header-section.hidden {
            transform: translateY(-100%);
            position: absolute;
            width: 100%;
        }
        
        .game-area {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            overflow: hidden;
            padding: 10px;
        }
        
        .game-board-container {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
        }
        
        #game-board {
            background: #faf8ef;
            border-radius: 8px;
            padding: 8px;
            display: grid;
            gap: 2px;
        }
        
        .tile {
            background: #c9b99a;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            cursor: pointer;
            transition: all 0.1s;
            user-select: none;
            -webkit-tap-highlight-color: transparent;
        }
        
        .tile:active {
            transform: scale(0.92);
        }
        
        .tile.selected {
            border: 2px solid #f59563;
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
            50% { transform: scale(1.08); }
        }
        
        footer {
            flex-shrink: 0;
            padding: 5px 20px;
            font-size: 11px;
            margin-top: auto;
            color: #999;
            text-align: center;
        }
        
        footer a {
            color: #999;
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
        
        .game-message {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(0,0,0,0.9);
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
        <a href="../../index.php" class="logo">🎮 Mahjong</a>
        <nav>
            <a href="../../index.php">게임</a>
            <a href="../../blog/">블로그</a>
        </nav>
    </header>

    <main class="game-area">
        <div style="display: flex; justify-content: space-between; align-items: center; padding: 8px 10px; background: #fff; border-radius: 8px; margin-bottom: 10px;">
            <div style="display: flex; gap: 15px;">
                <div style="text-align: center;">
                    <div style="font-size: 10px; color: #888;">SCORE</div>
                    <div style="font-size: 18px; font-weight: bold;" id="score">0</div>
                </div>
                <div style="text-align: center;">
                    <div style="font-size: 10px; color: #888;">TIME</div>
                    <div style="font-size: 18px; font-weight: bold;" id="time">0:00</div>
                </div>
                <div style="text-align: center;">
                    <div style="font-size: 10px; color: #888;">남음</div>
                    <div style="font-size: 18px; font-weight: bold;" id="pairs">0</div>
                </div>
            </div>
            
            <div style="display: flex; gap: 8px;">
                <select id="level" onchange="initGame()" style="padding: 8px; border-radius: 6px; border: 1px solid #ddd;">
                    <option value="easy">쉬움</option>
                    <option value="normal" selected>보통</option>
                    <option value="hard">어려움</option>
                </select>
            </div>
        </div>
        
        <div style="display: flex; gap: 8px; margin-bottom: 10px;">
            <button onclick="initGame()" style="flex:1; padding: 12px; background: #8f7a66; color: #fff; border: none; border-radius: 8px; font-size: 14px; font-weight: 600;">🔄 새 게임</button>
            <button onclick="showHint()" style="flex:1; padding: 12px; background: #8f7a66; color: #fff; border: none; border-radius: 8px; font-size: 14px; font-weight: 600;">💡 힌트</button>
            <button onclick="toggleHeader()" style="padding: 12px 16px; background: #f5f5f5; border: none; border-radius: 8px; font-size: 14px;">⬆️</button>
        </div>
        
        <div class="game-board-container">
            <div id="game-board"></div>
        </div>
    </main>

    <button class="toggle-header-btn" id="toggleBtn" onclick="toggleHeader()">⬇️ 메뉴 보기</button>

    <div class="game-message" id="gameMessage">
        <div id="messageText"></div>
        <button class="btn btn-primary" onclick="initGame()">다시하기</button>
    </div>

    <footer>
        <p>© <?= date('Y') ?> <a href="https://tomseol.pe.kr/" target="_blank">tomseol.pe.kr</a></p>
    </footer>

    <script src="game.js"></script>
</body>
</html>
