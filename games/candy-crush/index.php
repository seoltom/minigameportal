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
        
        #game-area {
            flex: 1;
            display: grid;
            gap: 3px;
            padding: 10px;
            justify-content: center;
            align-content: center;
            background: linear-gradient(to bottom, #2d1b4e, #1a1a2e);
        }
        
        .candy {
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            cursor: pointer;
            transition: transform 0.15s, box-shadow 0.15s;
            user-select: none;
            box-shadow: inset 0 -3px 6px rgba(0,0,0,0.2);
        }
        .candy:active { transform: scale(0.9); }
        .candy.selected {
            transform: scale(1.1);
            box-shadow: 0 0 15px rgba(255,255,255,0.8);
            z-index: 10;
        }
        .candy.matched {
            animation: pop 0.3s ease-out forwards;
        }
        @keyframes pop {
            0% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.3); }
            100% { transform: scale(0); opacity: 0; }
        }
        
        .candy-1 { background: linear-gradient(135deg, #ff6b6b, #ee5a5a); }
        .candy-2 { background: linear-gradient(135deg, #ffd93d, #f0c929); }
        .candy-3 { background: linear-gradient(135deg, #6bcb77, #5ab868); }
        .candy-4 { background: linear-gradient(135deg, #4d96ff, #3b7ddd); }
        .candy-5 { background: linear-gradient(135deg, #c56cf0, #b04ae0); }
        .candy-6 { background: linear-gradient(135deg, #ff9f43, #ee8e32); }
        
        .combo-text {
            position: fixed;
            font-size: 32px;
            font-weight: bold;
            color: #ffd700;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
            pointer-events: none;
            animation: comboAnim 1s ease-out forwards;
            z-index: 100;
        }
        @keyframes comboAnim {
            0% { transform: scale(0.5); opacity: 0; }
            30% { transform: scale(1.2); opacity: 1; }
            100% { transform: scale(1) translateY(-50px); opacity: 0; }
        }
        
        .controls {
            display: flex;
            gap: 8px;
            padding: 8px 12px;
            justify-content: center;
            flex-wrap: wrap;
            background: rgba(0,0,0,0.2);
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
        <div class="info-box">점수: <span class="info-value" id="score">0</span></div>
        <div class="info-box">타겟: <span class="info-value" id="target">1000</span></div>
        <div class="info-box">레벨: <span class="info-value" id="level">1</span></div>
    </div>
    
    <div id="game-area"></div>
    
    <div class="controls">
        <button class="btn" onclick="initGame()">새 게임</button>
        <button class="btn" onclick="showHint()">힌트</button>
        <button class="btn" onclick="changeLevel()">레벨: <span id="levelBtn">1</span></button>
    </div>
    
    <div class="game-message" id="gameMessage">
        <div id="messageText"></div>
        <button class="btn" onclick="initGame()" style="margin-top:15px;">재시작</button>
    </div>
    
    <script>
    const CANDIES = ['🍬', '🍭', '🍫', '🍩', '🧁', '🍪'];
    const LEVELS = [
        { rows: 6, cols: 6, target: 500, time: 60 },
        { rows: 7, cols: 7, target: 800, time: 90 },
        { rows: 8, cols: 8, target: 1200, time: 120 },
        { rows: 8, cols: 8, target: 1500, time: 120 },
        { rows: 8, cols: 8, target: 2000, time: 120 }
    ];
    
    let levelIdx = 0;
    let board = [], rows = 6, cols = 6, score = 0, target = 500;
    let selected = null, isAnimating = false;
    let timeLeft = 60, timer = null;
    
    function initGame() {
        const level = LEVELS[levelIdx];
        rows = level.rows;
        cols = level.cols;
        target = level.target;
        timeLeft = level.time;
        
        // 보드 생성
        board = [];
        for (let i = 0; i < rows; i++) {
            board[i] = [];
            for (let j = 0; j < cols; j++) {
                board[i][j] = getRandomCandy(i, j);
            }
        }
        
        // 초기 매칭 제거
        while (findMatches().length > 0) {
            for (let i = 0; i < rows; i++) {
                for (let j = 0; j < cols; j++) {
                    board[i][j] = getRandomCandy(i, j);
                }
            }
        }
        
        score = 0;
        selected = null;
        isAnimating = false;
        
        document.getElementById('score').textContent = score;
        document.getElementById('target').textContent = target;
        document.getElementById('level').textContent = levelIdx + 1;
        document.getElementById('levelBtn').textContent = levelIdx + 1;
        document.getElementById('gameMessage').classList.remove('show');
        
        renderBoard();
        startTimer();
        
        if (localStorage.getItem('darkMode') === '1') {
            document.body.classList.add('dark-mode');
            document.querySelector('header').classList.add('dark');
        }
    }
    
    function getRandomCandy(r, c) {
        let types = [];
        for (let t = 0; t < 6; t++) {
            // 가로 매칭 방지
            if (c >= 2 && board[r][c-1] === t && board[r][c-2] === t) continue;
            // 세로 매칭 방지
            if (r >= 2 && board[r-1][c] === t && board[r-2][c] === t) continue;
            types.push(t);
        }
        return types.length > 0 ? types[Math.floor(Math.random() * types.length)] : Math.floor(Math.random() * 6);
    }
    
    function renderBoard() {
        const container = document.getElementById('game-area');
        container.innerHTML = '';
        
        const winW = container.clientWidth - 20;
        const winH = container.clientHeight - 20;
        const cellW = Math.floor((winW - (cols - 1) * 3) / cols);
        const cellH = Math.floor((winH - (rows - 1) * 3) / rows);
        const size = Math.min(cellW, cellH, 50);
        
        container.style.gridTemplateColumns = `repeat(${cols}, ${size}px)`;
        
        for (let i = 0; i < rows; i++) {
            for (let j = 0; j < cols; j++) {
                const candy = document.createElement('div');
                candy.className = `candy candy-${board[i][j]}` + (selected && selected.r === i && selected.c === j ? ' selected' : '');
                candy.textContent = CANDIES[board[i][j]];
                candy.style.width = size + 'px';
                candy.style.height = size + 'px';
                candy.dataset.r = i;
                candy.dataset.c = j;
                candy.onclick = () => clickCandy(i, j);
                container.appendChild(candy);
            }
        }
    }
    
    function clickCandy(r, c) {
        if (isAnimating || timeLeft <= 0) return;
        
        if (!selected) {
            selected = {r, c};
            renderBoard();
        } else if (selected.r === r && selected.c === c) {
            selected = null;
            renderBoard();
        } else {
            // 인접한지 확인
            const dist = Math.abs(selected.r - r) + Math.abs(selected.c - c);
            if (dist === 1) {
                swapCandy(selected.r, selected.c, r, c);
                selected = null;
            } else {
                selected = {r, c};
                renderBoard();
            }
        }
    }
    
    async function swapCandy(r1, c1, r2, c2) {
        isAnimating = true;
        
        // 교환
        [board[r1][c1], board[r2][c2]] = [board[r2][c2], board[r1][c1]];
        renderBoard();
        
        await sleep(100);
        
        const matches = findMatches();
        if (matches.length > 0) {
            await processMatches();
        } else {
            // 다시 교환
            [board[r1][c1], board[r2][c2]] = [board[r2][c2], board[r1][c1]];
            renderBoard();
            
            // 흔들림
            const c1El = document.querySelector(`.candy[data-r="${r1}"][data-c="${c1}"]`);
            const c2El = document.querySelector(`.candy[data-r="${r2}"][data-c="${c2}"]`);
            if (c1El) c1El.style.animation = 'shake 0.3s';
            if (c2El) c2El.style.animation = 'shake 0.3s';
            await sleep(300);
            renderBoard();
        }
        
        isAnimating = false;
    }
    
    function findMatches() {
        const matches = [];
        const matched = new Set();
        
        // 가로
        for (let i = 0; i < rows; i++) {
            for (let j = 0; j < cols - 2; j++) {
                const c = board[i][j];
                if (c === board[i][j+1] && c === board[i][j+2]) {
                    let k = j;
                    while (k < cols && board[i][k] === c) {
                        matched.add(`${i},${k}`);
                        k++;
                    }
                }
            }
        }
        
        // 세로
        for (let j = 0; j < cols; j++) {
            for (let i = 0; i < rows - 2; i++) {
                const c = board[i][j];
                if (c === board[i+1][j] && c === board[i+2][j]) {
                    let k = i;
                    while (k < rows && board[k][j] === c) {
                        matched.add(`${k},${j}`);
                        k++;
                    }
                }
            }
        }
        
        return Array.from(matched).map(s => {
            const [r, c] = s.split(',').map(Number);
            return {r, c};
        });
    }
    
    async function processMatches() {
        let matches = findMatches();
        let combo = 0;
        
        while (matches.length > 0) {
            combo++;
            
            // 점수
            const points = matches.length * 10 + (matches.length > 3 ? (matches.length - 3) * 20 : 0);
            const comboBonus = combo > 1 ? combo * 30 : 0;
            score += points + comboBonus;
            document.getElementById('score').textContent = score;
            
            // 콤보 표시
            if (combo > 1) {
                showComboText(combo);
            }
            
            // 진동
            if (navigator.vibrate) navigator.vibrate(30);
            
            // 애니메이션
            matches.forEach(m => {
                const el = document.querySelector(`.candy[data-r="${m.r}"][data-c="${m.c}"]`);
                if (el) el.classList.add('matched');
            });
            
            await sleep(300);
            
            // 제거
            matches.forEach(m => {
                board[m.r][m.c] = null;
            });
            
            // 낙하
            await dropCandies();
            
            // 보충
            await fillBoard();
            
            matches = findMatches();
        }
        
        // 클리어 체크
        if (score >= target) {
            levelClear();
        }
    }
    
    async function dropCandies() {
        for (let j = 0; j < cols; j++) {
            let empty = rows - 1;
            for (let i = rows - 1; i >= 0; i--) {
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
        for (let j = 0; j < cols; j++) {
            for (let i = rows - 1; i >= 0; i--) {
                if (board[i][j] === null) {
                    board[i][j] = Math.floor(Math.random() * 6);
                    renderBoard();
                    await sleep(80);
                }
            }
        }
        
        // 새 매칭 확인
        const newMatches = findMatches();
        if (newMatches.length > 0) {
            await sleep(200);
            await processMatches();
        }
    }
    
    function showComboText(combo) {
        const text = document.createElement('div');
        text.className = 'combo-text';
        text.textContent = `${combo} COMBO!`;
        text.style.left = '50%';
        text.style.top = '50%';
        text.style.transform = 'translate(-50%, -50%)';
        document.body.appendChild(text);
        setTimeout(() => text.remove(), 1000);
    }
    
    function showHint() {
        if (isAnimating || timeLeft <= 0) return;
        
        for (let i = 0; i < rows; i++) {
            for (let j = 0; j < cols; j++) {
                // 오른쪽과 교환
                if (j < cols - 1) {
                    [board[i][j], board[i][j+1]] = [board[i][j+1], board[i][j]];
                    if (findMatches().length > 0) {
                        const el = document.querySelector(`.candy[data-r="${i}"][data-c="${j}"]`);
                        const el2 = document.querySelector(`.candy[data-r="${i}"][data-c="${j+1}"]`);
                        if (el) el.style.background = '#ff9f43';
                        if (el2) el2.style.background = '#ff9f43';
                        [board[i][j], board[i][j+1]] = [board[i][j+1], board[i][j]];
                        setTimeout(() => renderBoard(), 1000);
                        return;
                    }
                    [board[i][j], board[i][j+1]] = [board[i][j+1], board[i][j]];
                }
                
                // 아래와 교환
                if (i < rows - 1) {
                    [board[i][j], board[i+1][j]] = [board[i+1][j], board[i][j]];
                    if (findMatches().length > 0) {
                        const el = document.querySelector[data-r="${i(`.candy}"][data-c="${j}"]`);
                        const el2 = document.querySelector(`.candy[data-r="${i+1}"][data-c="${j}"]`);
                        if (el) el.style.background = '#ff9f43';
                        if (el2) el2.style.background = '#ff9f43';
                        [board[i][j], board[i+1][j]] = [board[i+1][j], board[i][j]];
                        setTimeout(() => renderBoard(), 1000);
                        return;
                    }
                    [board[i][j], board[i+1][j]] = [board[i+1][j], board[i][j]];
                }
            }
        }
    }
    
    function changeLevel() {
        levelIdx = (levelIdx + 1) % LEVELS.length;
        document.getElementById('levelBtn').textContent = levelIdx + 1;
        initGame();
    }
    
    function startTimer() {
        clearInterval(timer);
        timer = setInterval(() => {
            timeLeft--;
            if (timeLeft <= 0) {
                clearInterval(timer);
                gameOver();
            }
        }, 1000);
    }
    
    function levelClear() {
        clearInterval(timer);
        if (navigator.vibrate) navigator.vibrate([100, 50, 100]);
        
        let msg = `🎉 레벨 ${levelIdx + 1} 클리어!<br>점수: ${score}`;
        if (levelIdx < LEVELS.length - 1) {
            msg += '<br><br><button class="btn" onclick="nextLevel()">다음 레벨</button>';
        } else {
            msg += '<br><br>🎊 모든 레벨 완료!';
        }
        
        document.getElementById('messageText').innerHTML = msg;
        document.getElementById('gameMessage').classList.add('show');
    }
    
    function gameOver() {
        clearInterval(timer);
        if (navigator.vibrate) navigator.vibrate(200);
        
        document.getElementById('messageText').innerHTML = `⏰ 시간 초과!<br>점수: ${score}<br>타겟: ${target}`;
        document.getElementById('gameMessage').classList.add('show');
    }
    
    function nextLevel() {
        if (levelIdx < LEVELS.length - 1) {
            levelIdx++;
            document.getElementById('levelBtn').textContent = levelIdx + 1;
            initGame();
        }
    }
    
    function sleep(ms) {
        return new Promise(r => setTimeout(r, ms));
    }
    
    window.onresize = renderBoard;
    
    // CSS에 shake 애니메이션 추가
    const style = document.createElement('style');
    style.textContent = `
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }
    `;
    document.head.appendChild(style);
    
    initGame();
    </script>
</body>
</html>
