<?php
require_once '../../config.php';
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <title>Mahjong Connect - <?= SITE_NAME ?></title>
    <link rel="stylesheet" href="../../css/style.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body { height: 100%; overflow: hidden; }
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
        
        .game-info {
            display: flex;
            gap: 10px;
            padding: 8px 12px;
            justify-content: center;
            flex-wrap: wrap;
        }
        .info-box {
            background: #bbada0;
            color: #fff;
            padding: 6px 15px;
            border-radius: 6px;
            text-align: center;
            font-size: 12px;
        }
        .info-value {
            font-size: 16px;
            font-weight: bold;
            color: #fff;
        }
        
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
        .tile {
            background: #c9b99a;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            cursor: pointer;
            user-select: none;
            -webkit-user-select: none;
        }
        .tile.selected {
            border: 3px solid #f59563;
            background: #f2b179;
        }
        .tile.matched {
            visibility: hidden;
            opacity: 0;
        }
        .tile.hint {
            animation: blink 0.3s infinite;
        }
        @keyframes blink {
            50% { opacity: 0.5; }
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
        body.dark-mode .tile { background: rgba(255,255,255,0.1); }
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
        <div class="info-box">남은쌍: <span class="info-value" id="pairs">0</span></div>
        <div class="info-box">시간: <span class="info-value" id="time">0:00</span></div>
    </div>
    
    <div class="controls">
        <button class="btn" onclick="initGame()">새 게임</button>
        <button class="btn" onclick="showHint()">힌트</button>
        <select class="btn" id="level" onchange="initGame()">
            <option value="easy">쉬움</option>
            <option value="normal" selected>보통</option>
            <option value="hard">어려움</option>
        </select>
    </div>
    
    <div id="game-board"></div>
    
    <div class="game-message" id="gameMessage">
        <div id="messageText"></div>
        <button class="btn" onclick="initGame()" style="margin-top:15px;">다시하기</button>
    </div>
    
    <script>
    const TILES = ['🀄','🀅','🀆','🀇','🀈','🀉','🀊','🀋','🀌','🀍','🀎','🀏','🀐','🀑','🀒','🀓'];
    
    const LEVELS = {
        easy: { rows: 4, cols: 6, time: 180 },
        normal: { rows: 6, cols: 8, time: 300 },
        hard: { rows: 8, cols: 10, time: 480 }
    };
    
    let board = [], rows = 6, cols = 8, selected = null, score = 0, pairsLeft = 0, timeLeft = 300, timer = null;
    
    function initGame() {
        const level = LEVELS[document.getElementById('level').value];
        rows = level.rows;
        cols = level.cols;
        timeLeft = level.time;
        
        const total = rows * cols, pairs = total / 2;
        pairsLeft = pairs;
        
        let tiles = [];
        for (let i = 0; i < pairs; i++) {
            const t = TILES[i % TILES.length];
            tiles.push(t, t);
        }
        for (let i = tiles.length - 1; i > 0; i--) {
            const j = Math.floor(Math.random() * (i + 1));
            [tiles[i], tiles[j]] = [tiles[j], tiles[i]];
        }
        
        board = Array(rows + 2).fill().map(() => Array(cols + 2).fill(0));
        let idx = 0;
        for (let i = 1; i <= rows; i++) {
            for (let j = 1; j <= cols; j++) {
                board[i][j] = tiles[idx++];
            }
        }
        
        score = 0;
        selected = null;
        renderBoard();
        updateStats();
        startTimer();
    }
    
    function renderBoard() {
        const container = document.getElementById('game-board');
        container.innerHTML = '';
        
        const winW = container.clientWidth - 20;
        const winH = container.clientHeight - 20;
        const tileW = Math.floor((winW - (cols - 1) * 2) / cols);
        const tileH = Math.floor((winH - (rows - 1) * 2) / rows);
        const size = Math.min(tileW, tileH, 45);
        
        container.style.gridTemplateColumns = `repeat(${cols}, ${size}px)`;
        
        for (let i = 1; i <= rows; i++) {
            for (let j = 1; j <= cols; j++) {
                const tile = document.createElement('div');
                tile.className = 'tile';
                tile.textContent = board[i][j] || '';
                if (board[i][j] === 0) tile.classList.add('matched');
                tile.dataset.r = i;
                tile.dataset.c = j;
                tile.style.width = size + 'px';
                tile.style.height = Math.floor(size * 1.2) + 'px';
                tile.style.fontSize = Math.floor(size * 0.7) + 'px';
                tile.onclick = () => clickTile(i, j);
                container.appendChild(tile);
            }
        }
    }
    
    function clickTile(r, c) {
        if (!board[r][c] || timeLeft <= 0) return;
        
        const tile = document.querySelector(`.tile[data-r="${r}"][data-c="${c}"]`);
        
        if (!selected) {
            selected = { r, c, el: tile };
            tile.classList.add('selected');
        } else if (selected.r === r && selected.c === c) {
            tile.classList.remove('selected');
            selected = null;
        } else if (board[selected.r][selected.c] === board[r][c]) {
            const path = findPath(selected.r, selected.c, r, c);
            if (path) {
                score += 100;
                pairsLeft--;
                board[selected.r][selected.c] = 0;
                board[r][c] = 0;
                if (navigator.vibrate) navigator.vibrate(30);
                
                selected.el.classList.remove('selected');
                selected.el.classList.add('matched');
                tile.classList.add('matched');
                
                setTimeout(() => {
                    renderBoard();
                    updateStats();
                    checkEnd();
                }, 200);
                selected = null;
            } else {
                selected.el.classList.remove('selected');
                selected = { r, c, el: tile };
                tile.classList.add('selected');
            }
        } else {
            selected.el.classList.remove('selected');
            selected = { r, c, el: tile };
            tile.classList.add('selected');
        }
    }
    
    function findPath(r1, c1, r2, c2) {
        const dirs = [[-1,0],[1,0],[0,-1],[0,1]];
        const q = [{r:r1,c:c1,path:[]}];
        const v = new Set([`${r1},${c1}`]);
        
        while (q.length) {
            const {r,c,p} = q.shift();
            if (r === r2 && c === c2) return p;
            for (const [dr,dc] of dirs) {
                const nr = r + dr, nc = c + dc;
                if (nr < 0 || nr > rows + 1 || nc < 0 || nc > cols + 1) continue;
                if (v.has(`${nr},${nc}`)) continue;
                if (board[nr][nc] !== 0 && !(nr === r2 && nc === c2)) continue;
                v.add(`${nr},${nc}`);
                q.push({r:nr,c:nc,path:[...p,{r:nr,c:nc}]});
            }
        }
        return null;
    }
    
    function showHint() {
        if (timeLeft <= 0) return;
        for (let i = 1; i <= rows; i++) {
            for (let j = 1; j <= cols; j++) {
                if (!board[i][j]) continue;
                for (let ii = 1; ii <= rows; ii++) {
                    for (let jj = 1; jj <= cols; jj++) {
                        if ((i !== ii || j !== jj) && board[i][j] === board[ii][jj] && findPath(i,j,ii,jj)) {
                            document.querySelector(`.tile[data-r="${i}"][data-c="${j}"]`)?.classList.add('hint');
                            document.querySelector(`.tile[data-r="${ii}"][data-c="${jj}"]`)?.classList.add('hint');
                            setTimeout(() => {
                                document.querySelector(`.tile[data-r="${i}"][data-c="${j}"]`)?.classList.remove('hint');
                                document.querySelector(`.tile[data-r="${ii}"][data-c="${jj}"]`)?.classList.remove('hint');
                            }, 800);
                            return;
                        }
                    }
                }
            }
        }
    }
    
    function checkEnd() {
        if (pairsLeft === 0) {
            score += timeLeft * 10;
            if (navigator.vibrate) navigator.vibrate([100,50,100]);
            showMsg('🎉 클리어!<br>점수: ' + score);
            clearInterval(timer);
        } else if (timeLeft <= 0) {
            if (navigator.vibrate) navigator.vibrate(200);
            showMsg('😢 시간 초과!');
            clearInterval(timer);
        }
    }
    
    function startTimer() {
        clearInterval(timer);
        timer = setInterval(() => {
            timeLeft--;
            updateStats();
            if (timeLeft <= 0) checkEnd();
        }, 1000);
    }
    
    function updateStats() {
        document.getElementById('score').textContent = score;
        document.getElementById('pairs').textContent = pairsLeft;
        const m = Math.floor(timeLeft / 60), s = timeLeft % 60;
        document.getElementById('time').textContent = m + ':' + s.toString().padStart(2,'0');
    }
    
    function showMsg(text) {
        document.getElementById('messageText').innerHTML = text;
        document.getElementById('gameMessage').classList.add('show');
    }
    
    window.onresize = renderBoard;
    
    // 다크 모드
    if (localStorage.getItem('darkMode') === '1') {
        document.body.classList.add('dark-mode');
        document.querySelector('header')?.classList.add('dark');
    }
    
    initGame();
    </script>
</body>
</html>
