<?php
/**
 * 보석 매칭 게임 페이지 - 모바일 최적화
 */
require_once '../../config.php';
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <title>Bejeweled - <?= SITE_NAME ?></title>
    <link rel="stylesheet" href="../../css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Pretendard:wght@400;500;600;700&amp;display=swap" rel="stylesheet">
    <style>
        html, body { overflow: hidden; height: 100%; }
        body { display: flex; flex-direction: column; height: 100%; touch-action: manipulation; user-select: none; }
        .game-header-section { flex-shrink: 0; padding: 8px 15px; display: flex; justify-content: space-between; align-items: center; width: 100%; box-sizing: border-box; }
        .game-header-section .logo { font-size: 16px; font-weight: bold; color: #776e65; }
        .game-header-section nav { display: flex; gap: 15px; }
        .game-header-section nav a { font-size: 13px; color: #776e65; text-decoration: none; }
        .game-header-section.hidden { transform: translateY(-100%); position: absolute; width: 100%; }
        .game-area { flex: 1; display: flex; flex-direction: column; justify-content: center; overflow: hidden; padding: 10px; }
        .game-board-container { flex: 1; display: flex; justify-content: center; align-items: center; overflow: hidden; }
        #game-board { background: linear-gradient(135deg, #2c3e50, #34495e); border-radius: 8px; padding: 6px; display: grid; gap: 3px; }
        .gem { border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 28px; cursor: pointer; transition: all 0.15s; user-select: none; }
        .gem:active { transform: scale(0.9); }
        .gem.selected { transform: scale(1.1); box-shadow: 0 0 15px rgba(255,255,255,0.8); z-index: 10; }
        .gem.hint { animation: hintPulse 0.5s ease-in-out infinite; }
        @keyframes hintPulse { 0%, 100% { transform: scale(1); } 50% { transform: scale(1.15); } }
        .gem.matched { animation: matchedPop 0.3s ease-out forwards; }
        @keyframes matchedPop { 0% { transform: scale(1); opacity: 1; } 50% { transform: scale(1.3); opacity: 0.8; } 100% { transform: scale(0); opacity: 0; } }
        .gem.falling { animation: fallIn 0.3s ease-out; }
        @keyframes fallIn { 0% { transform: translateY(-100%); opacity: 0; } 100% { transform: translateY(0); opacity: 1; } }
        footer { flex-shrink: 0; padding: 5px 20px; font-size: 11px; margin-top: auto; color: #999; text-align: center; }
        footer a { color: #999; }
        .toggle-header-btn { position: fixed; top: 10px; right: 10px; z-index: 1000; background: rgba(255,255,255,0.9); border: none; border-radius: 20px; padding: 8px 12px; font-size: 12px; cursor: pointer; display: none; }
        .toggle-header-btn.show { display: block; }
        .game-message { position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: rgba(0,0,0,0.9); color: #fff; padding: 30px 40px; border-radius: 16px; font-size: 20px; font-weight: bold; text-align: center; z-index: 2000; display: none; }
        .game-message.show { display: block; }
        .game-message button { margin-top: 15px; padding: 10px 20px; font-size: 16px; }
        .gem-0 { background: linear-gradient(135deg, #e74c3c, #c0392b); }
        .gem-1 { background: linear-gradient(135deg, #3498db, #2980b9); }
        .gem-2 { background: linear-gradient(135deg, #2ecc71, #27ae60); }
        .gem-3 { background: linear-gradient(135deg, #f1c40f, #f39c12); }
        .gem-4 { background: linear-gradient(135deg, #9b59b6, #8e44ad); }
        .gem-5 { background: linear-gradient(135deg, #e67e22, #d35400); }
        .gem-6 { background: linear-gradient(135deg, #1abc9c, #16a085); }
        .gem-7 { background: linear-gradient(135deg, #ecf0f1, #bdc3c7); }
    </style>
</head>
<body>
    <header class="game-header-section" id="headerSection">
        <a href="../../index.php" class="logo"> Bejeweled</a>
        <nav>
            <a href="../../index.php">게임</a>
            <a href="../../blog/">블로그</a>
        </nav>
    </header>
    <main class="game-area">
        <div style="display: flex; justify-content: space-between; align-items: center; padding: 8px 10px; background: #fff; border-radius: 8px; margin-bottom: 10px;">
            <div style="display: flex; gap: 15px;">
                <div style="text-align: center;"><div style="font-size: 10px; color: #888;">SCORE</div><div style="font-size: 18px; font-weight: bold;" id="score">0</div></div>
                <div style="text-align: center;"><div style="font-size: 10px; color: #888;">LEVEL</div><div style="font-size: 18px; font-weight: bold;" id="level">1</div></div>
                <div style="text-align: center;"><div style="font-size: 10px; color: #888;">TARGET</div><div style="font-size: 18px; font-weight: bold;" id="target">500</div></div>
            </div>
            <div style="display: flex; gap: 8px;">
                <select id="difficulty" onchange="initGame()" style="padding: 8px; border-radius: 6px; border: 1px solid #ddd;">
                    <option value="easy">쉬움</option><option value="normal" selected>보통</option><option value="hard">어려움</option>
                </select>
            </div>
        </div>
        <div style="display: flex; gap: 8px; margin-bottom: 10px;">
            <button onclick="initGame()" style="flex:1; padding: 12px; background: #8f7a66; color: #fff; border: none; border-radius: 8px; font-size: 14px; font-weight: 600;"> 새 게임</button>
            <button onclick="showHint()" style="flex:1; padding: 12px; background: #8f7a66; color: #fff; border: none; border-radius: 8px; font-size: 14px; font-weight: 600;"> 힌트</button>
            <button onclick="toggleHeader()" style="padding: 12px 16px; background: #f5f5f5; border: none; border-radius: 8px; font-size: 14px;"> </button>
        </div>
        <div class="game-board-container"><div id="game-board"></div></div>
    </main>
    <button class="toggle-header-btn" id="toggleBtn" onclick="toggleHeader()"> 메뉴 보기</button>
    <div class="game-message" id="gameMessage"><div id="messageText"></div><button class="btn btn-primary" onclick="initGame()">다시하기</button></div>
    <footer><p>© <?= date('Y') ?> <a href="https://tomseol.pe.kr/" target="_blank">tomseol.pe.kr</a></p></footer>
    <script src="game.js"></script>
</body>
</html>
