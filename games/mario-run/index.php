<?php
/**
 * Mario Run 게임 페이지 - 모바일 최적화
 */
require_once '../../config.php';
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Mario Run - <?= SITE_NAME ?></title>
    <link rel="stylesheet" href="../../css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Pretendard:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        html, body {
            overflow: hidden;
            height: 100%;
            background: linear-gradient(87deg, #5BC0DE 0%, #D6AE01 100%);
        }
        
        body {
            display: flex;
            flex-direction: column;
            height: 100%;
            touch-action: manipulation;
        }
        
        .game-header-section {
            flex-shrink: 0;
            transition: transform 0.3s ease;
            background: rgba(0,0,0,0.3);
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
            position: relative;
        }
        
        #game-canvas {
            background: linear-gradient(to bottom, #5c94fc 0%, #5c94fc 60%, #8b4513 60%, #654321 100%);
            border-radius: 8px;
            margin: 10px;
            flex: 1;
            position: relative;
            overflow: hidden;
        }
        
        /* 구름 */
        .cloud {
            position: absolute;
            font-size: 40px;
            opacity: 0.8;
            animation: cloudMove 20s linear infinite;
        }
        
        @keyframes cloudMove {
            0% { transform: translateX(100vw); }
            100% { transform: translateX(-100px); }
        }
        
        /* 지면 */
        .ground {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 25%;
            background: linear-gradient(to bottom, #8b4513 0%, #654321 100%);
            border-top: 4px solid #4a3728;
        }
        
        /* 파이프 */
        .pipe {
            position: absolute;
            bottom: 25%;
            width: 50px;
            background: linear-gradient(to right, #228b22 0%, #32cd32 50%, #228b22 100%);
            border-radius: 5px 5px 0 0;
            border: 2px solid #1a6b1a;
        }
        
        .pipe::before {
            content: '';
            position: absolute;
            top: -10px;
            left: -5px;
            width: 60px;
            height: 15px;
            background: linear-gradient(to right, #228b22 0%, #32cd32 50%, #228b22 100%);
            border-radius: 5px;
            border: 2px solid #1a6b1a;
        }
        
        /* 버섯 */
        .mushroom {
            position: absolute;
            font-size: 35px;
            z-index: 10;
        }
        
        /* 별 */
        .star {
            position: absolute;
            font-size: 30px;
            animation: starSpin 1s linear infinite;
            z-index: 10;
        }
        
        @keyframes starSpin {
            0%, 100% { transform: rotateY(0deg); }
            50% { transform: rotateY(180deg); }
        }
        
        /* 마리오 */
        #mario {
            position: absolute;
            font-size: 45px;
            bottom: 25%;
            left: 50px;
            transition: bottom 0.1s;
            z-index: 20;
            filter: drop-shadow(2px 2px 2px rgba(0,0,0,0.5));
        }
        
        #mario.jumping {
            animation: marioJump 0.5s ease-out;
        }
        
        @keyframes marioJump {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px) rotate(10deg); }
        }
        
        /* 장애물 (고블린) */
        .goblin {
            position: absolute;
            bottom: 25%;
            font-size: 40px;
            z-index: 15;
            animation: goblinMove 3s linear infinite;
        }
        
        @keyframes goblinMove {
            0% { transform: translateX(0); }
            50% { transform: translateX(-10px) rotate(-5deg); }
            100% { transform: translateX(0); }
        }
        
        /* 점수 표시 */
        .score-display {
            position: absolute;
            top: 10px;
            right: 15px;
            background: rgba(0,0,0,0.7);
            color: #fff;
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 16px;
            font-weight: bold;
            z-index: 100;
        }
        
        .controls-hint {
            position: absolute;
            bottom: 30%;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(0,0,0,0.6);
            color: #fff;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 12px;
            z-index: 100;
        }
        
        /* 게임 오버레이 */
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
        
        .game-over-title {
            font-size: 28px;
            margin-bottom: 15px;
        }
        
        .final-score {
            font-size: 24px;
            color: #ffd700;
            margin-bottom: 20px;
        }
        
        footer {
            flex-shrink: 0;
            padding: 5px 20px;
            font-size: 11px;
            background: rgba(0,0,0,0.3);
            color: #fff;
        }
        
        footer a {
            color: #fff;
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
        <div id="game-canvas">
            <!-- 구름 -->
            <div class="cloud" style="top: 10%; animation-delay: 0s;">☁️</div>
            <div class="cloud" style="top: 5%; animation-delay: 5s;">☁️</div>
            <div class="cloud" style="top: 15%; animation-delay: 10s;">☁️</div>
            
            <!-- 지면 -->
            <div class="ground"></div>
            
            <!-- 마리오 -->
            <div id="mario">🍄</div>
            
            <!-- 점수 -->
            <div class="score-display">
                <span>⭐ <span id="score">0</span></span>
                <span style="margin-left: 15px;">🏃 <span id="distance">0</span>m</span>
            </div>
            
            <!-- 컨트롤 힌트 -->
            <div class="controls-hint" id="controlsHint">👆 탭하여 점프!</div>
        </div>
    </main>

    <div class="game-message" id="gameMessage">
        <div class="game-over-title" id="messageTitle">💀 게임 오버!</div>
        <div class="final-score" id="messageScore">점수: 0</div>
        <button class="btn btn-primary" onclick="startGame()">🔄 다시하기</button>
    </div>

    <footer>
        <p>© <?= date('Y') ?> <a href="https://tomseol.pe.kr/" target="_blank">tomseol.pe.kr</a></p>
    </footer>

    <script src="game.js"></script>
</body>
</html>
