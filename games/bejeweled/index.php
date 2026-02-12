<?php
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
            flex-wrap: wrap;
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
        
        #game-board {
            flex: 1;
            display: grid;
            gap: 2px;
            padding: 10px;
            justify-content: center;
            align-content: center;
        }
        .gem {
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            cursor: pointer;
            user-select: none;
            transition: transform 0.1s;
        }
        .gem:active { transform: scale(0.9); }
        .gem.selected { border: 3px solid #fff; transform: scale(1.1); }
        .gem.matched { animation: matchAnim 0.3s forwards; }
        @keyframes matchAnim {
            to { transform: scale(0); opacity: 0; }
        }
        
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
        
        /* 다크 모드 */
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
        <div class="info-box">점수: <span class="info-value" id="score">0</span></div>
        <div class="info-box">타겟: <span class="info-value" id="target">500</span></div>
    </div>
    
    <div id="game-board"></div>
    
    <div class="game-message" id="gameMessage">
        <div id="messageText"></div>
    </div>
    
    <script>
    const GEMS = ['💎','🔴','🟢','⭐','🟣','🟠','🔵','💛'];
    const ROWS = 7, COLS = 8;
    
    let board = [], selected = null, score = 0, target = 500;
    
    function initGame() {
        // 보드 생성
        board = [];
        for (let i = 0; i < ROWS; i++) {
            board[i] = [];
            for (let j = 0; j < COLS; j++) {
                board[i][j] = getGem(i, j);
            }
        }
        
        // 초기 매칭 제거
        while (findMatches().length > 0) {
            for (let i = 0; i < ROWS; i++) {
                for (let j = 0; j < COLS; j++) {
                    board[i][j] = getGem(i, j);
                }
            }
        }
        
        score = 0;
        selected = null;
        renderBoard();
        updateStats();
        
        if (localStorage.getItem('darkMode') === '1') {
            document.body.classList.add('dark-mode');
            document.querySelector('header').classList.add('dark');
        }
    }
    
    function getGem(r, c) {
        let gems = [];
        for (let g = 0; g < 8; g++) {
            if (c >= 2 && board[r][c-1] === g && board[r][c-2] === g) continue;
            if (r >= 2 && board[r-1][c] === g && board[r-2][c] === g) continue;
            gems.push(g);
        }
        return gems.length > 0 ? gems[Math.floor(Math.random() * gems.length)] : Math.floor(Math.random() * 8);
    }
    
    function renderBoard() {
        const container = document.getElementById('game-board');
        container.innerHTML = '';
        
        const winW = container.clientWidth - 20;
        const winH = container.clientHeight - 20;
        const gemW = Math.floor((winW - (COLS - 1) * 2) / COLS);
        const gemH = Math.floor((winH - (ROWS - 1) * 2) / ROWS);
        const size = Math.min(gemW, gemH, 45);
        
        container.style.gridTemplateColumns = `repeat(${COLS}, ${size}px)`;
        
        for (let i = 0; i < ROWS; i++) {
            for (let j = 0; j < COLS; j++) {
                const gem = document.createElement('div');
                const isSel = selected && selected.r === i && selected.c === j;
                gem.className = 'gem' + (isSel ? ' selected' : '');
                gem.textContent = GEMS[board[i][j]];
                gem.style.width = size + 'px';
                gem.style.height = size + 'px';
                gem.style.fontSize = Math.floor(size * 0.6) + 'px';
                gem.dataset.r = i;
                gem.dataset.c = j;
                gem.onclick = () => clickGem(i, j);
                container.appendChild(gem);
            }
        }
    }
    
    function clickGem(r, c) {
        // 빈칸이면 리턴
        if (board[r][c] === null) return;
        
        // 첫 선택
        if (!selected) {
            selected = {r, c};
            renderBoard();
            return;
        }
        
        // 같은 보석 선택 -> 해제
        if (selected.r === r && selected.c === c) {
            selected = null;
            renderBoard();
            return;
        }
        
        // 인접하지 않으면 새 보석 선택
        const dist = Math.abs(selected.r - r) + Math.abs(selected.c - c);
        if (dist !== 1) {
            selected = {r, c};
            renderBoard();
            return;
        }
        
        // 교환
        swap(selected.r, selected.c, r, c);
        selected = null;
    }
    
    async function swap(r1, c1, r2, c2) {
        [board[r1][c1], board[r2][c2]] = [board[r2][c2], board[r1][c1]];
        renderBoard();
        
        const matches = findMatches();
        if (matches.length > 0) {
            await processMatches();
        } else {
            // 다시 교환
            await sleep(150);
            [board[r1][c1], board[r2][c2]] = [board[r2][c2], board[r1][c1]];
            renderBoard();
        }
    }
    
    function findMatches() {
        const matches = new Set();
        
        // 가로
        for (let i = 0; i < ROWS; i++) {
            for (let j = 0; j < COLS - 2; j++) {
                const g = board[i][j];
                if (g !== null && board[i][j+1] === g && board[i][j+2] === g) {
                    let k = j;
                    while (k < COLS && board[i][k] === g) {
                        matches.add(i + ',' + k);
                        k++;
                    }
                }
            }
        }
        
        // 세로
        for (let j = 0; j < COLS; j++) {
            for (let i = 0; i < ROWS - 2; i++) {
                const g = board[i][j];
                if (g !== null && board[i+1][j] === g && board[i+2][j] === g) {
                    let k = i;
                    while (k < ROWS && board[k][j] === g) {
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
    
    async function processMatches() {
        let matches = findMatches();
        while (matches.length > 0) {
            // 점수 추가
            score += matches.length * 10;
            updateStats();
            
            // 애니메이션
            matches.forEach(m => {
                const gem = document.querySelector(`.gem[data-r="${m.r}"][data-c="${m.c}"]`);
                if (gem) gem.classList.add('matched');
            });
            
            await sleep(300);
            
            // 제거
            matches.forEach(m => {
                board[m.r][m.c] = null;
            });
            
            await dropGems();
            await fillBoard();
            
            matches = findMatches();
        }
    }
    
    async function dropGems() {
        for (let j = 0; j < COLS; j++) {
            let empty = ROWS - 1;
            for (let i = ROWS - 1; i >= 0; i--) {
                if (board[i][j] !== null) {
                    if (i !== empty) {
                        board[empty][j] = board[i][j];
                        board[i][j] = null;
                    }
                    empty--;
                }
            }
        }
        renderBoard();
        await sleep(150);
    }
    
    async function fillBoard() {
        for (let j = 0; j < COLS; j++) {
            for (let i = ROWS - 1; i >= 0; i--) {
                if (board[i][j] === null) {
                    board[i][j] = Math.floor(Math.random() * 8);
                    renderBoard();
                    await sleep(100);
                }
            }
        }
    }
    
    function updateStats() {
        document.getElementById('score').textContent = score;
        document.getElementById('target').textContent = target;
    }
    
    function sleep(ms) {
        return new Promise(r => setTimeout(r, ms));
    }
    
    window.onresize = renderBoard;
    
    initGame();
    </script>
</body>
</html>
