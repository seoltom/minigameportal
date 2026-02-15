<?php
require_once '../../config.php';
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <title>Candy Crush - <?= SITE_NAME ?></title>
    <link rel="stylesheet" href="../../css/style.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body { height: 100%; overflow: hidden; }
        body { 
            background: linear-gradient(135deg, #2d1b4e, #1a1a2e);
            display: flex;
            flex-direction: column;
            touch-action: manipulation;
            user-select: none;
            font-family: inherit;
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
        
        #game-container {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 10px;
        }
        
        #game-board {
            display: grid;
            gap: 3px;
            background: rgba(0,0,0,0.3);
            padding: 5px;
            border-radius: 10px;
        }
        
        .candy {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            cursor: pointer;
            transition: transform 0.1s, box-shadow 0.1s;
        }
        .candy:hover { transform: scale(1.05); }
        .candy:active { transform: scale(0.95); }
        .candy.selected {
            transform: scale(1.1);
            box-shadow: 0 0 10px rgba(255,255,255,0.8);
        }
        
        .candy-0 { background: linear-gradient(135deg, #ff6b6b, #ee5a5a); }
        .candy-1 { background: linear-gradient(135deg, #ffd93d, #f0c929); }
        .candy-2 { background: linear-gradient(135deg, #6bcb77, #5ab868); }
        .candy-3 { background: linear-gradient(135deg, #4d96ff, #3b7ddd); }
        .candy-4 { background: linear-gradient(135deg, #c56cf0, #b04ae0); }
        .candy-5 { background: linear-gradient(135deg, #ff9f43, #ee8e32); }
        
        .controls {
            display: flex;
            gap: 8px;
            padding: 10px;
            justify-content: center;
            background: rgba(0,0,0,0.2);
            flex-wrap: wrap;
        }
        .btn {
            padding: 10px 18px;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            cursor: pointer;
            background: #8f7a66;
            color: #fff;
        }
        
        .message {
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
        .message.show { display: block; }
        
        body.dark-mode { background: #1a1a2e !important; }
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
        <div class="info-box">점수: <span class="info-value" id="score">0</span></div>
        <div class="info-box">타겟: <span class="info-value" id="target">500</span></div>
        <div class="info-box">레벨: <span class="info-value" id="level">1</span></div>
    </div>
    
    <div id="game-container">
        <div id="game-board"></div>
    </div>
    
    <div class="controls">
        <button class="btn" onclick="initGame()">새 게임</button>
        <button class="btn" onclick="changeLevel()">레벨</button>
    </div>
    
    <div class="message" id="message">
        <div id="msg-text"></div>
        <button class="btn" onclick="initGame()" style="margin-top:15px;">재시작</button>
    </div>
    
    <script>
    const CANDIES = ['🍬', '🍭', '🍫', '🍩', '🧁', '🍪'];
    const COLORS = [0, 1, 2, 3, 4, 5];
    
    const LEVELS = [
        { size: 6, target: 500 },
        { size: 7, target: 800 },
        { size: 8, target: 1200 },
        { size: 8, target: 1500 },
        { size: 8, target: 2000 }
    ];
    
    let board = [];
    let size = 6;
    let selected = null;
    let score = 0;
    let target = 500;
    let levelIdx = 0;
    
    function initGame() {
        const level = LEVELS[levelIdx];
        size = level.size;
        target = level.target;
        
        board = [];
        for (let i = 0; i < size; i++) {
            board[i] = [];
            for (let j = 0; j < size; j++) {
                board[i][j] = getCandy(i, j);
            }
        }
        
        // 초기 매칭 제거
        while (findMatches().length > 0) {
            for (let i = 0; i < size; i++) {
                for (let j = 0; j < size; j++) {
                    board[i][j] = getCandy(i, j);
                }
            }
        }
        
        score = 0;
        selected = null;
        
        updateDisplay();
        document.getElementById('message').classList.remove('show');
        render();
        
        if (localStorage.getItem('darkMode') === '1') {
            document.body.classList.add('dark-mode');
        }
    }
    
    function getCandy(r, c) {
        let types = [];
        for (let t = 0; t < 6; t++) {
            if (c >= 2 && board[r][c-1] === t && board[r][c-2] === t) continue;
            if (r >= 2 && board[r-1][c] === t && board[r-2][c] === t) continue;
            types.push(t);
        }
        return types.length > 0 ? types[Math.floor(Math.random() * types.length)] : Math.floor(Math.random() * 6);
    }
    
    function render() {
        const boardEl = document.getElementById('game-board');
        boardEl.innerHTML = '';
        boardEl.style.gridTemplateColumns = `repeat(${size}, 40px)`;
        
        for (let i = 0; i < size; i++) {
            for (let j = 0; j < size; j++) {
                const cell = document.createElement('div');
                cell.className = 'candy candy-' + board[i][j];
                cell.textContent = CANDIES[board[i][j]];
                cell.dataset.r = i;
                cell.dataset.c = j;
                cell.onclick = function() { clickCandy(i, j); };
                boardEl.appendChild(cell);
            }
        }
    }
    
    function clickCandy(r, c) {
        if (selected === null) {
            selected = {r, c};
            render();
        } else if (selected.r === r && selected.c === c) {
            selected = null;
            render();
        } else {
            const dist = Math.abs(selected.r - r) + Math.abs(selected.c - c);
            if (dist === 1) {
                swapAndMatch(selected.r, selected.c, r, c);
                selected = null;
            } else {
                selected = {r, c};
                render();
            }
        }
    }
    
    function swapAndMatch(r1, c1, r2, c2) {
        [board[r1][c1], board[r2][c2]] = [board[r2][c2], board[r1][c1]];
        render();
        
        const matches = findMatches();
        if (matches.length > 0) {
            processMatches();
        } else {
            setTimeout(() => {
                [board[r1][c1], board[r2][c2]] = [board[r2][c2], board[r1][c1]];
                render();
            }, 200);
        }
    }
    
    function findMatches() {
        const matches = new Set();
        
        for (let i = 0; i < size; i++) {
            for (let j = 0; j < size - 2; j++) {
                const c = board[i][j];
                if (c === board[i][j+1] && c === board[i][j+2]) {
                    let k = j;
                    while (k < size && board[i][k] === c) {
                        matches.add(i + ',' + k);
                        k++;
                    }
                }
            }
        }
        
        for (let j = 0; j < size; j++) {
            for (let i = 0; i < size - 2; i++) {
                const c = board[i][j];
                if (c === board[i+1][j] && c === board[i+2][j]) {
                    let k = i;
                    while (k < size && board[k][j] === c) {
                        matches.add(k + ',' + j);
                        k++;
                    }
                }
            }
        }
        
        return Array.from(matches).map(s => {
            const [r, c] = s.split(',').map(Number);
            return {r, c};
        });
    }
    
    function processMatches() {
        let matches = findMatches();
        let combo = 0;
        
        while (matches.length > 0) {
            combo++;
            
            const points = matches.length * 10 + (matches.length > 3 ? (matches.length - 3) * 20 : 0);
            score += points;
            updateDisplay();
            
            if (navigator.vibrate && combo > 1) navigator.vibrate(20);
            
            matches.forEach(m => { board[m.r][m.c] = null; });
            render();
            
            setTimeout(() => {
                dropCandies();
                fillBoard();
            }, 200);
            
            matches = findMatches();
        }
        
        if (score >= target) {
            setTimeout(levelClear, 300);
        }
    }
    
    function dropCandies() {
        for (let j = 0; j < size; j++) {
            let empty = size - 1;
            for (let i = size - 1; i >= 0; i--) {
                if (board[i][j] !== null) {
                    if (i !== empty) {
                        board[empty][j] = board[i][j];
                        board[i][j] = null;
                    }
                    empty--;
                }
            }
        }
        render();
    }
    
    function fillBoard() {
        for (let j = 0; j < size; j++) {
            for (let i = size - 1; i >= 0; i--) {
                if (board[i][j] === null) {
                    board[i][j] = Math.floor(Math.random() * 6);
                    render();
                }
            }
        }
        
        setTimeout(() => {
            const matches = findMatches();
            if (matches.length > 0) {
                processMatches();
            }
        }, 200);
    }
    
    function updateDisplay() {
        document.getElementById('score').textContent = score;
        document.getElementById('target').textContent = target;
        document.getElementById('level').textContent = levelIdx + 1;
    }
    
    function changeLevel() {
        levelIdx = (levelIdx + 1) % LEVELS.length;
        initGame();
    }
    
    function levelClear() {
        const msg = document.getElementById('message');
        document.getElementById('msg-text').innerHTML = '🎉 클리어!<br>점수: ' + score;
        msg.classList.add('show');
    }
    
    initGame();
    </script>
</body>
</html>
