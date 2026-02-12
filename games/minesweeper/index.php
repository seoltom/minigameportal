<?php
require_once '../../config.php';
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <title>Minesweeper - <?= SITE_NAME ?></title>
    <link rel="stylesheet" href="../../css/style.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body { height: 100%; overflow: hidden; }
        body { 
            background: #1a1a2e;
            display: flex;
            flex-direction: column;
            touch-action: manipulation;
            user-select: none;
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
        
        .game-info {
            display: flex;
            gap: 10px;
            padding: 8px 12px;
            justify-content: center;
            background: rgba(0,0,0,0.3);
        }
        .info-box {
            background: rgba(255,255,255,0.1);
            color: #fff;
            padding: 6px 15px;
            border-radius: 6px;
            text-align: center;
            font-size: 12px;
        }
        .info-value { font-size: 16px; font-weight: bold; color: #ffd700; }
        
        .controls {
            display: flex;
            gap: 8px;
            padding: 8px 12px;
            justify-content: center;
            flex-wrap: wrap;
        }
        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            font-size: 12px;
            cursor: pointer;
            background: #8f7a66;
            color: #fff;
        }
        
        #game-board {
            flex: 1;
            display: grid;
            gap: 2px;
            padding: 10px;
            justify-content: center;
            align-content: center;
        }
        .cell {
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            user-select: none;
            background: #4a5568;
        }
        .cell.revealed { background: #2d3748; cursor: default; }
        .cell.flagged { background: #4a5568; }
        .cell.mine { background: #e53e3e; }
        .cell[data-num="1"] { color: #63b3ed; }
        .cell[data-num="2"] { color: #48bb78; }
        .cell[data-num="3"] { color: #f56565; }
        .cell[data-num="4"] { color: #805ad5; }
        .cell[data-num="5"] { color: #ed8936; }
        .cell[data-num="6"] { color: #38b2ac; }
        .cell[data-num="7"] { color: #1a202c; }
        .cell[data-num="8"] { color: #a0aec0; }
        
        .game-message {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(0,0,0,0.9);
            color: #fff;
            padding: 30px;
            border-radius: 12px;
            font-size: 20px;
            text-align: center;
            z-index: 2000;
            display: none;
        }
        .game-message.show { display: block; }
        
        body.dark-mode { background: #1a1a2e !important; color: #fff !important; }
        body.dark-mode header { background: #1a1a2e !important; }
        body.dark-mode .logo { color: #fff !important; }
        body.dark-mode nav a { color: #ccc !important; }
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
    
    <div class="game-info">
        <div class="info-box">지뢰: <span class="info-value" id="mines">10</span></div>
        <div class="info-box">시간: <span class="info-value" id="time">0</span></div>
    </div>
    
    <div class="controls">
        <button class="btn" onclick="initGame()">새 게임</button>
        <button class="btn" onclick="changeLevel()"><span id="levelLabel">보통</span></button>
    </div>
    
    <div id="game-board"></div>
    
    <div class="game-message" id="gameMessage">
        <div id="messageText"></div>
        <button class="btn" onclick="initGame()" style="margin-top:15px;">다시하기</button>
    </div>
    
    <script>
    const LEVELS = {
        easy: { rows: 8, cols: 10, mines: 10, label: '쉬움' },
        normal: { rows: 10, cols: 12, mines: 20, label: '보통' },
        hard: { rows: 12, cols: 14, mines: 35, label: '어려움' }
    };
    
    let level = 'normal';
    let board = [], revealed = [], flagged = [];
    let rows = 10, cols = 12, mines = 20;
    let gameOver = false, firstClick = true;
    let time = 0, timer = null;
    
    function changeLevel() {
        const levels = ['easy', 'normal', 'hard'];
        const idx = levels.indexOf(level);
        level = levels[(idx + 1) % 3];
        document.getElementById('levelLabel').textContent = LEVELS[level].label;
        initGame();
    }
    
    function initGame() {
        const config = LEVELS[level];
        rows = config.rows;
        cols = config.cols;
        mines = config.mines;
        
        board = Array(rows).fill().map(() => Array(cols).fill(0));
        revealed = Array(rows).fill().map(() => Array(cols).fill(false));
        flagged = Array(rows).fill().map(() => Array(cols).fill(false));
        
        gameOver = false;
        firstClick = true;
        time = 0;
        
        clearInterval(timer);
        document.getElementById('time').textContent = '0';
        document.getElementById('mines').textContent = mines;
        document.getElementById('gameMessage').classList.remove('show');
        
        renderBoard();
        
        if (localStorage.getItem('darkMode') === '1') {
            document.body.classList.add('dark-mode');
            document.querySelector('header').classList.add('dark');
        }
    }
    
    function placeMines(excludeR, excludeC) {
        let placed = 0;
        while (placed < mines) {
            const r = Math.floor(Math.random() * rows);
            const c = Math.floor(Math.random() * cols);
            
            if (Math.abs(r - excludeR) <= 1 && Math.abs(c - excludeC) <= 1) continue;
            if (board[r][c] === -1) continue;
            
            board[r][c] = -1;
            placed++;
        }
        
        // 숫자 계산
        for (let r = 0; r < rows; r++) {
            for (let c = 0; c < cols; c++) {
                if (board[r][c] === -1) continue;
                let count = 0;
                for (let dr = -1; dr <= 1; dr++) {
                    for (let dc = -1; dc <= 1; dc++) {
                        const nr = r + dr, nc = c + dc;
                        if (nr >= 0 && nr < rows && nc >= 0 && nc < cols && board[nr][nc] === -1) count++;
                    }
                }
                board[r][c] = count;
            }
        }
    }
    
    function renderBoard() {
        const container = document.getElementById('game-board');
        container.innerHTML = '';
        
        const winW = container.clientWidth - 20;
        const winH = container.clientHeight - 20;
        const cellW = Math.floor((winW - (cols - 1) * 2) / cols);
        const cellH = Math.floor((winH - (rows - 1) * 2) / rows);
        const size = Math.min(cellW, cellH, 38);
        
        container.style.gridTemplateColumns = `repeat(${cols}, ${size}px)`;
        
        for (let r = 0; r < rows; r++) {
            for (let c = 0; c < cols; c++) {
                const cell = document.createElement('div');
                cell.className = 'cell';
                cell.style.width = size + 'px';
                cell.style.height = size + 'px';
                cell.dataset.r = r;
                cell.dataset.c = c;
                
                if (revealed[r][c]) {
                    cell.classList.add('revealed');
                    if (board[r][c] === -1) {
                        cell.classList.add('mine');
                        cell.textContent = '💣';
                    } else if (board[r][c] > 0) {
                        cell.textContent = board[r][c];
                        cell.dataset.num = board[r][c];
                    }
                } else if (flagged[r][c]) {
                    cell.classList.add('flagged');
                    cell.textContent = '🚩';
                }
                
                cell.onclick = () => clickCell(r, c);
                cell.oncontextmenu = (e) => { e.preventDefault(); toggleFlag(r, c); };
                
                container.appendChild(cell);
            }
        }
    }
    
    function clickCell(r, c) {
        if (gameOver || revealed[r][c] || flagged[r][c]) return;
        
        if (firstClick) {
            firstClick = false;
            placeMines(r, c);
            timer = setInterval(() => {
                time++;
                document.getElementById('time').textContent = time;
            }, 1000);
        }
        
        if (board[r][c] === -1) {
            // 지뢰 밟음
            revealed[r][c] = true;
            revealAll();
            renderBoard();
            gameOver = true;
            clearInterval(timer);
            if (navigator.vibrate) navigator.vibrate(200);
            showMsg('💥 게임 오버!');
        } else {
            reveal(r, c);
            renderBoard();
            checkWin();
        }
    }
    
    function reveal(r, c) {
        if (r < 0 || r >= rows || c < 0 || c >= cols) return;
        if (revealed[r][c] || flagged[r][c]) return;
        
        revealed[r][c] = true;
        
        if (board[r][c] === 0) {
            for (let dr = -1; dr <= 1; dr++) {
                for (let dc = -1; dc <= 1; dc++) {
                    reveal(r + dr, c + dc);
                }
            }
        }
    }
    
    function revealAll() {
        for (let r = 0; r < rows; r++) {
            for (let c = 0; c < cols; c++) {
                if (board[r][c] === -1) revealed[r][c] = true;
            }
        }
    }
    
    function toggleFlag(r, c) {
        if (gameOver || revealed[r][c]) return;
        flagged[r][c] = !flagged[r][c];
        const flagCount = flagged.flat().filter(f => f).length;
        document.getElementById('mines').textContent = mines - flagCount;
        renderBoard();
    }
    
    function checkWin() {
        let unrevealed = 0;
        for (let r = 0; r < rows; r++) {
            for (let c = 0; c < cols; c++) {
                if (!revealed[r][c]) unrevealed++;
            }
        }
        
        if (unrevealed === mines) {
            gameOver = true;
            clearInterval(timer);
            if (navigator.vibrate) navigator.vibrate([100, 50, 100]);
            
            // 모든 지뢰 표시
            for (let r = 0; r < rows; r++) {
                for (let c = 0; c < cols; c++) {
                    if (board[r][c] === -1) flagged[r][c] = true;
                }
            }
            renderBoard();
            showMsg('🎉 클리어! 💣');
        }
    }
    
    function showMsg(text) {
        document.getElementById('messageText').innerHTML = text;
        document.getElementById('gameMessage').classList.add('show');
    }
    
    window.onresize = renderBoard;
    
    initGame();
    </script>
</body>
</html>
