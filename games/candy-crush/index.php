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
            font-family: system-ui, sans-serif;
        }
        header { 
            background: #fff; 
            box-shadow: 0 2px 8px rgba(0,0,0,0.08); 
            position: sticky; 
            top: 0; 
            z-index: 100; 
            flex-shrink: 0;
            padding: 8px 12px;
        }
        .header-content { 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            max-width: 1200px; 
            margin: 0 auto; 
        }
        .logo { font-size: 15px; font-weight: bold; color: #4f46e5; }
        nav { display: flex; gap: 10px; }
        nav a { font-size: 12px; color: #666; text-decoration: none; }
        
        .info {
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
        
        #game {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 10px;
        }
        
        #board {
            display: grid;
            gap: 4px;
            background: rgba(0,0,0,0.4);
            padding: 8px;
            border-radius: 12px;
        }
        
        .cell {
            width: 44px;
            height: 44px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            cursor: pointer;
            transition: transform 0.1s;
            box-shadow: inset 0 -2px 4px rgba(0,0,0,0.2);
        }
        .cell:active { transform: scale(0.9); }
        .cell.selected { 
            transform: scale(1.1);
            box-shadow: 0 0 0 3px #fff, 0 0 15px rgba(255,255,255,0.5);
            z-index: 10;
        }
        
        .c0 { background: linear-gradient(135deg, #ff6b6b, #ee5a5a); }
        .c1 { background: linear-gradient(135deg, #ffd93d, #f0c929); }
        .c2 { background: linear-gradient(135deg, #6bcb77, #5ab868); }
        .c3 { background: linear-gradient(135deg, #4d96ff, #3b7ddd); }
        .c4 { background: linear-gradient(135deg, #c56cf0, #b04ae0); }
        .c5 { background: linear-gradient(135deg, #ff9f43, #ee8e32); }
        
        .controls {
            display: flex;
            gap: 10px;
            padding: 12px;
            justify-content: center;
            background: rgba(0,0,0,0.2);
        }
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            cursor: pointer;
            background: #8f7a66;
            color: #fff;
        }
        
        .popup {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(0,0,0,0.9);
            color: #fff;
            padding: 30px;
            border-radius: 15px;
            font-size: 20px;
            text-align: center;
            z-index: 2000;
            display: none;
        }
        .popup.show { display: block; }
        
        body.dark-mode header { background: #1a1a2e; }
        body.dark-mode .logo { color: #fff; }
        body.dark-mode nav a { color: #ccc; }
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
    
    <div class="info">
        <div class="info-box">점수: <span class="info-value" id="score">0</span></div>
        <div class="info-box">타겟: <span class="info-value" id="target">500</span></div>
    </div>
    
    <div id="game">
        <div id="board"></div>
    </div>
    
    <div class="controls">
        <button class="btn" onclick="initGame()">새 게임</button>
    </div>
    
    <div class="popup" id="popup">
        <div id="msg"></div>
        <button class="btn" onclick="initGame()" style="margin-top:15px;">재시작</button>
    </div>
    
    <script>
    const CANDIES = ['🍬', '🍭', '🍫', '🍩', '🧁', '🍪'];
    const SIZE = 7;
    
    let board = [];
    let selected = null;
    let score = 0;
    let target = 500;
    let processing = false;
    
    function initGame() {
        board = [];
        selected = null;
        score = 0;
        processing = false;
        
        document.getElementById('score').textContent = score;
        document.getElementById('target').textContent = target;
        document.getElementById('popup').classList.remove('show');
        
        // 보드 생성
        for (let i = 0; i < SIZE; i++) {
            board[i] = [];
            for (let j = 0; j < SIZE; j++) {
                board[i][j] = getCandy(i, j);
            }
        }
        
        // 초기 매칭 제거
        while (findMatches().length > 0) {
            for (let i = 0; i < SIZE; i++) {
                for (let j = 0; j < SIZE; j++) {
                    board[i][j] = getCandy(i, j);
                }
            }
        }
        
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
        const boardEl = document.getElementById('board');
        boardEl.innerHTML = '';
        boardEl.style.gridTemplateColumns = `repeat(${SIZE}, 44px)`;
        
        for (let i = 0; i < SIZE; i++) {
            for (let j = 0; j < SIZE; j++) {
                const cell = document.createElement('div');
                cell.className = 'cell c' + board[i][j];
                if (selected && selected.r === i && selected.c === j) {
                    cell.classList.add('selected');
                }
                cell.textContent = CANDIES[board[i][j]];
                cell.onclick = function() { clickCell(i, j); };
                boardEl.appendChild(cell);
            }
        }
    }
    
    function clickCell(r, c) {
        if (processing) return;
        
        if (!selected) {
            selected = {r, c};
            render();
        } else if (selected.r === r && selected.c === c) {
            selected = null;
            render();
        } else {
            // 인접 확인
            const dist = Math.abs(selected.r - r) + Math.abs(selected.c - c);
            if (dist === 1) {
                swapCells(selected.r, selected.c, r, c);
                selected = null;
            } else {
                selected = {r, c};
                render();
            }
        }
    }
    
    function swapCells(r1, c1, r2, c2) {
        processing = true;
        
        // 교환
        const temp = board[r1][c1];
        board[r1][c1] = board[r2][c2];
        board[r2][c2] = temp;
        render();
        
        setTimeout(function() {
            const matches = findMatches();
            if (matches.length > 0) {
                processMatches();
            } else {
                // 다시 교환
                const temp2 = board[r1][c1];
                board[r1][c1] = board[r2][c2];
                board[r2][c2] = temp2;
                processing = false;
                render();
            }
        }, 150);
    }
    
    function findMatches() {
        const matches = [];
        const seen = new Set();
        
        // 가로
        for (let i = 0; i < SIZE; i++) {
            for (let j = 0; j < SIZE - 2; j++) {
                const c = board[i][j];
                if (c === board[i][j+1] && c === board[i][j+2]) {
                    let k = j;
                    while (k < SIZE && board[i][k] === c) {
                        const key = i + ',' + k;
                        if (!seen.has(key)) {
                            seen.add(key);
                            matches.push({r: i, c: k});
                        }
                        k++;
                    }
                }
            }
        }
        
        // 세로
        for (let j = 0; j < SIZE; j++) {
            for (let i = 0; i < SIZE - 2; i++) {
                const c = board[i][j];
                if (c === board[i+1][j] && c === board[i+2][j]) {
                    let k = i;
                    while (k < SIZE && board[k][j] === c) {
                        const key = k + ',' + j;
                        if (!seen.has(key)) {
                            seen.add(key);
                            matches.push({r: k, c: j});
                        }
                        k++;
                    }
                }
            }
        }
        
        return matches;
    }
    
    function processMatches() {
        const matches = findMatches();
        
        if (matches.length > 0) {
            // 점수
            score += matches.length * 10;
            document.getElementById('score').textContent = score;
            
            // 제거
            matches.forEach(function(m) {
                board[m.r][m.c] = null;
            });
            
            render();
            
            setTimeout(function() {
                dropCandies();
            }, 200);
        } else {
            processing = false;
            
            // 클리어 체크
            if (score >= target) {
                document.getElementById('msg').innerHTML = '🎉 클리어!<br>점수: ' + score;
                document.getElementById('popup').classList.add('show');
            }
        }
    }
    
    function dropCandies() {
        // 아래로 이동
        for (let j = 0; j < SIZE; j++) {
            let empty = SIZE - 1;
            for (let i = SIZE - 1; i >= 0; i--) {
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
        
        // 보충
        setTimeout(function() {
            fillBoard();
        }, 150);
    }
    
    function fillBoard() {
        let filled = false;
        
        for (let j = 0; j < SIZE; j++) {
            for (let i = SIZE - 1; i >= 0; i--) {
                if (board[i][j] === null) {
                    board[i][j] = Math.floor(Math.random() * 6);
                    filled = true;
                }
            }
        }
        
        render();
        
        // 새 매칭 확인
        setTimeout(function() {
            const matches = findMatches();
            if (matches.length > 0) {
                processMatches();
            } else {
                processing = false;
            }
        }, 150);
    }
    
    initGame();
    </script>
</body>
</html>
