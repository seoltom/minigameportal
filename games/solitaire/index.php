<?php
/**
 * Solitaire 게임 페이지 - 모바일 최적화
 */
require_once '../../config.php';
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <title>솔리테어 - <?= SITE_NAME ?></title>
    <link rel="stylesheet" href="../../css/style.css">
    <style>
        header { background: #fff; box-shadow: 0 2px 8px rgba(0,0,0,0.08); position: sticky; top: 0; z-index: 100; }
        html, body {
            overflow: hidden;
            height: 100%;
            background: linear-gradient(135deg, #1a5f2a 0%, #0d3d1a 100%);
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
            color: #ffd700;
        }
        
        .game-header-section nav {
            display: flex;
            gap: 15px;
        }
        
        .game-header-section nav a {
            font-size: 13px;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
        }
        
        .game-area {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            padding: 10px;
            gap: 10px;
        }
        
        .top-row {
            display: flex;
            gap: 8px;
            width: 100%;
            justify-content: space-between;
        }
        
        .stock, .waste, .foundation {
            width: 45px;
            height: 60px;
            background: rgba(0,0,0,0.3);
            border-radius: 6px;
            border: 2px dashed rgba(255,255,255,0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }
        
        .foundation {
            font-size: 20px;
        }
        
        #tableau {
            display: flex;
            gap: 6px;
            width: 100%;
            justify-content: center;
        }
        
        .column {
            width: 45px;
            min-height: 60px;
            background: rgba(0,0,0,0.2);
            border-radius: 6px;
            position: relative;
        }
        
        .card {
            width: 45px;
            height: 60px;
            background: #fff;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            font-weight: bold;
            position: absolute;
            box-shadow: 0 2px 4px rgba(0,0,0,0.3);
            cursor: pointer;
        }
        
        .card.red { color: #dc2626; }
        .card.black { color: #1f2937; }
        
        .card.face-down {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            border: 2px solid #1e40af;
        }
        
        .card.face-down .card-content {
            display: none;
        }
        
        .card.selected {
            box-shadow: 0 0 0 3px #ffd700, 0 4px 8px rgba(0,0,0,0.5);
            transform: translateY(-5px);
        }
        
        .card.moving {
            z-index: 1000;
            pointer-events: none;
        }
        
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
        
        .score-display {
            color: #ffd700;
            font-size: 14px;
        }
        
        footer {
            flex-shrink: 0;
            padding: 5px 20px;
            font-size: 10px;
            color: rgba(255,255,255,0.5);
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
        <a href="../../index.php" class="logo">🃏 Solitaire</a>
        <nav>
            <a href="../../index.php">게임</a>
            <a href="../../blog/">블로그</a>
        </nav>
    </header>

    <main class="game-area">
        <div class="top-row">
            <div class="stock" id="stock" onclick="drawCard()">🃏</div>
            <div class="waste" id="waste"></div>
            <div style="display: flex; gap: 4px;">
                <div class="foundation" id="foundation-0">🃎</div>
                <div class="foundation" id="foundation-1">🃎</div>
                <div class="foundation" id="foundation-2">🃎</div>
                <div class="foundation" id="foundation-3">🃎</div>
            </div>
        </div>
        
        <div id="tableau">
            <div class="column" id="col-0"></div>
            <div class="column" id="col-1"></div>
            <div class="column" id="col-2"></div>
            <div class="column" id="col-3"></div>
            <div class="column" id="col-4"></div>
            <div class="column" id="col-5"></div>
            <div class="column" id="col-6"></div>
        </div>
        
        <div class="controls">
            <div class="score-display">점수: <span id="score">0</span></div>
            <button class="btn btn-reset" onclick="initGame()">🔄 새로하기</button>
        </div>
    </main>

    <footer>
        <p>© <?= date('Y') ?> <a href="https://tomseol.pe.kr/" target="_blank">tomseol.pe.kr</a></p>
    </footer>

    <script src="game.js"></script>
</body>
</html>
