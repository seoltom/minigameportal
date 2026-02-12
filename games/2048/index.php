<?php
require_once '../../config.php';
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <title>2048 - <?= SITE_NAME ?></title>
    <link rel="stylesheet" href="../../css/style.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body { 
            height: 100%; 
            overflow: hidden; 
        }
        body { 
            background: #faf8ef;
            display: flex;
            flex-direction: column;
            touch-action: manipulation;
            user-select: none;
            -webkit-user-select: none;
        }
        header { 
            background: #fff; 
            box-shadow: 0 2px 8px rgba(0,0,0,0.08); 
            position: sticky; 
            top: 0; 
            z-index: 100; 
            flex-shrink: 0;
        }
        .header-content { 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            padding: 8px 12px; 
            max-width: 1200px; 
            margin: 0 auto; 
        }
        .logo { font-size: 15px; font-weight: bold; color: #4f46e5; }
        nav { display: flex; gap: 10px; }
        nav a { font-size: 12px; color: #666; text-decoration: none; }
        
        .game-area {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 10px;
        }
        .score-bar {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
            width: 100%;
            max-width: 320px;
            justify-content: center;
        }
        .score-box {
            background: #bbada0;
            padding: 8px 20px;
            border-radius: 6px;
            text-align: center;
            min-width: 80px;
        }
        .score-label {
            font-size: 10px;
            color: #eee4da;
            font-weight: 600;
            text-transform: uppercase;
        }
        .score-value {
            font-size: 20px;
            font-weight: bold;
            color: #fff;
        }
        #game-board {
            background: #bbada0;
            border-radius: 8px;
            padding: 8px;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 6px;
            width: 100%;
            max-width: 320px;
            aspect-ratio: 1;
        }
        .cell {
            background: rgba(238, 228, 218, 0.35);
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            font-weight: bold;
            color: #776e65;
        }
        .cell[data-value="2"] { background: #eee4da; }
        .cell[data-value="4"] { background: #ede0c8; }
        .cell[data-value="8"] { background: #f2b179; color: #f9f6f2; }
        .cell[data-value="16"] { background: #f59563; color: #f9f6f2; }
        .cell[data-value="32"] { background: #f67c5f; color: #f9f6f2; }
        .cell[data-value="64"] { background: #f65e3b; color: #f9f6f2; }
        .cell[data-value="128"] { background: #edcf72; color: #f9f6f2; font-size: 20px; }
        .cell[data-value="256"] { background: #edcc61; color: #f9f6f2; font-size: 20px; }
        .cell[data-value="512"] { background: #edc850; color: #f9f6f2; font-size: 20px; }
        .cell[data-value="1024"] { background: #edc53f; color: #f9f6f2; font-size: 16px; }
        .cell[data-value="2048"] { background: #edc22e; color: #f9f6f2; font-size: 16px; }
        
        .controls {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 8px;
            margin-top: 15px;
            width: 100%;
            max-width: 200px;
        }
        .control-btn {
            padding: 15px;
            border: none;
            border-radius: 8px;
            font-size: 18px;
            cursor: pointer;
            background: #8f7a66;
            color: #fff;
        }
        .control-btn:active { transform: scale(0.95); }
        
        .game-message {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(237, 194, 46, 0.95);
            color: #fff;
            padding: 30px;
            border-radius: 12px;
            font-size: 24px;
            font-weight: bold;
            text-align: center;
            z-index: 2000;
            display: none;
        }
        .game-message.show { display: block; }
        
        /* 다크 모드 */
        body.dark-mode { background: #1a1a2e !important; color: #fff !important; }
        body.dark-mode header { background: #1a1a2e !important; }
        body.dark-mode .logo { color: #fff !important; }
        body.dark-mode nav a { color: #ccc !important; }
        body.dark-mode .cell { background: rgba(255,255,255,0.1) !important; }
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
        <div class="score-bar">
            <div class="score-box">
                <div class="score-label">SCORE</div>
                <div class="score-value" id="score">0</div>
            </div>
            <div class="score-box">
                <div class="score-label">BEST</div>
                <div class="score-value" id="best">0</div>
            </div>
        </div>
        
        <div id="game-board"></div>
        
        <div class="controls">
            <div></div>
            <button class="control-btn" onclick="move('up')">⬆️</button>
            <div></div>
            <button class="control-btn" onclick="move('left')">⬅️</button>
            <button class="control-btn" onclick="move('down')">⬇️</button>
            <button class="control-btn" onclick="move('right')">➡️</button>
        </div>
    </main>
    
    <div class="game-message" id="gameMessage"></div>
    
    <script>
    let board = [];
    let score = 0;
    let best = localStorage.getItem('2048-best') || 0;
    
    function initGame() {
        board = Array(4).fill().map(() => Array(4).fill(0));
        score = 0;
        addRandomTile();
        addRandomTile();
        updateDisplay();
    }
    
    function addRandomTile() {
        let empty = [];
        for (let i = 0; i < 4; i++) {
            for (let j = 0; j < 4; j++) {
                if (board[i][j] === 0) empty.push({i, j});
            }
        }
        if (empty.length > 0) {
            let {i, j} = empty[Math.floor(Math.random() * empty.length)];
            board[i][j] = Math.random() < 0.9 ? 2 : 4;
        }
    }
    
    function updateDisplay() {
        const el = document.getElementById('game-board');
        el.innerHTML = '';
        for (let i = 0; i < 4; i++) {
            for (let j = 0; j < 4; j++) {
                let cell = document.createElement('div');
                cell.className = 'cell';
                let v = board[i][j];
                cell.textContent = v || '';
                cell.dataset.value = v || '';
                el.appendChild(cell);
            }
        }
        document.getElementById('score').textContent = score;
        document.getElementById('best').textContent = best;
    }
    
    function slide(row) {
        let arr = row.filter(v => v);
        for (let i = 0; i < arr.length - 1; i++) {
            if (arr[i] === arr[i + 1]) {
                arr[i] *= 2;
                score += arr[i];
                arr.splice(i + 1, 1);
            }
        }
        while (arr.length < 4) arr.push(0);
        return JSON.stringify(row) !== JSON.stringify(arr);
    }
    
    function move(direction) {
        let moved = false;
        let rotated = JSON.parse(JSON.stringify(board));
        
        // 회전해서 같은 로직 적용
        for (let k = 0; k < ({left:0, up:0, right:2, down:1}[direction] || 0); k++) {
            rotated = rotated[0].map((_, i) => rotated.map(r => r[i]).reverse());
        }
        
        for (let i = 0; i < 4; i++) {
            if (slide(rotated[i])) moved = true;
        }
        
        // 원래 방향으로 회전 복구
        let rotBack = ({left:0, up:0, right:2, down:1}[direction] || 0);
        for (let k = 0; k < (4 - rotBack % 4); k++) {
            rotated = rotated[0].map((_, i) => rotated.map(r => r[i]).reverse());
        }
        board = rotated;
        
        if (moved) {
            addRandomTile();
            updateDisplay();
            if (score > best) {
                best = score;
                localStorage.setItem('2048-best', best);
            }
        }
    }
    
    // 터치 이벤트
    let startX, startY;
    document.addEventListener('touchstart', e => {
        startX = e.touches[0].clientX;
        startY = e.touches[0].clientY;
    });
    document.addEventListener('touchend', e => {
        let endX = e.changedTouches[0].clientX;
        let endY = e.changedTouches[0].clientY;
        let dx = endX - startX, dy = endY - startY;
        if (Math.abs(dx) > Math.abs(dy)) {
            move(dx > 0 ? 'right' : 'left');
        } else {
            move(dy > 0 ? 'down' : 'up');
        }
    });
    
    // 키보드
    document.addEventListener('keydown', e => {
        if (e.key === 'ArrowUp') move('up');
        else if (e.key === 'ArrowDown') move('down');
        else if (e.key === 'ArrowLeft') move('left');
        else if (e.key === 'ArrowRight') move('right');
    });
    
    // 다크 모드
    if (localStorage.getItem('darkMode') === '1') {
        document.body.classList.add('dark-mode');
        document.querySelector('header').classList.add('dark');
    }
    
    initGame();
    </script>
</body>
</html>
