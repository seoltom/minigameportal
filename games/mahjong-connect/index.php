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
        
        .game-info { display: flex; gap: 10px; padding: 8px 12px; justify-content: center; flex-wrap: wrap; }
        .info-box { background: #bbada0; color: #fff; padding: 6px 15px; border-radius: 6px; text-align: center; font-size: 12px; }
        .info-value { font-size: 16px; font-weight: bold; }
        
        .controls { display: flex; gap: 8px; padding: 8px 12px; justify-content: center; flex-wrap: wrap; }
        .btn { padding: 8px 16px; border: none; border-radius: 6px; font-size: 12px; cursor: pointer; background: #8f7a66; color: #fff; }
        
        #game-board { flex: 1; display: grid; gap: 2px; padding: 10px; justify-content: center; align-content: center; }
        .tile { background: #c9b99a; border-radius: 4px; display: flex; align-items: center; justify-content: center; font-size: 20px; cursor: pointer; }
        .tile.selected { border: 3px solid #f59563; background: #f2b179; }
        .tile.matched { visibility: hidden; opacity: 0; }
        
        .game-message { position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: rgba(0,0,0,0.9); color: #fff; padding: 30px; border-radius: 12px; font-size: 20px; text-align: center; z-index: 2000; display: none; }
        .game-message.show { display: block; }
        
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
    const LEVELS = { easy:{rows:4,cols:6,time:180}, normal:{rows:6,cols:8,time:300}, hard:{rows:8,cols:10,time:480} };
    
    let board = [], rows = 6, cols = 8, selected = null, score = 0, pairsLeft = 0, timeLeft = 300, timer = null;
    
    function initGame() {
        // 팝업 닫기
        document.getElementById('gameMessage').classList.remove('show');
        
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
        
        // 패딩 포함 (경로 탐색용)
        board = Array(rows + 2).fill().map(() => Array(cols + 2).fill(null));
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
        
        const containerWidth = Math.min(window.innerWidth - 20, 450);
        const containerHeight = window.innerHeight - 150;
        const tileW = Math.floor((containerWidth - (cols - 1) * 2) / cols);
        const tileH = Math.floor((containerHeight - (rows - 1) * 2) / rows);
        const size = Math.min(tileW, tileH, 42);
        
        container.style.gridTemplateColumns = `repeat(${cols}, ${size}px)`;
        
        for (let i = 1; i <= rows; i++) {
            for (let j = 1; j <= cols; j++) {
                const tile = document.createElement('div');
                const isSelected = selected && selected.r === i && selected.c === j;
                const isMatched = board[i][j] === null;
                
                tile.className = 'tile' + (isSelected ? ' selected' : '') + (isMatched ? ' matched' : '');
                tile.textContent = board[i][j] || '';
                tile.style.width = size + 'px';
                tile.style.height = Math.floor(size * 1.2) + 'px';
                tile.style.fontSize = Math.floor(size * 0.7) + 'px';
                tile.dataset.r = i;
                tile.dataset.c = j;
                
                if (!isMatched) {
                    tile.onclick = () => clickTile(i, j);
                }
                
                container.appendChild(tile);
            }
        }
    }
    
    function clickTile(r, c) {
        if (board[r][c] === null || timeLeft <= 0) return;
        
        // 같은 타일 선택 해제
        if (selected && selected.r === r && selected.c === c) {
            selected = null;
            renderBoard();
            return;
        }
        
        if (!selected) {
            selected = {r, c};
            renderBoard();
        } else {
            // 같은 값인지 확인
            if (board[selected.r][selected.c] === board[r][c]) {
                // 경로가 있는지 확인
                if (canConnect(selected.r, selected.c, r, c)) {
                    // 매칭 성공 - 타일 제거
                    board[selected.r][selected.c] = null;
                    board[r][c] = null;
                    score += 100;
                    pairsLeft--;
                    
                    if (navigator.vibrate) navigator.vibrate(30);
                    
                    setTimeout(() => {
                        renderBoard();
                        updateStats();
                        checkEnd();
                    }, 200);
                }
                // 경로가 없든 있든 선택 해제
                selected = null;
                renderBoard();
            } else {
                // 다른 값 - 새로운 타일 선택
                selected = {r, c};
                renderBoard();
            }
        }
    }
    
    // 경로 찾기 - 0(빈칸)으로 이동 가능
    function canConnect(r1, c1, r2, c2) {
        // 같은 위치면 false
        if (r1 === r2 && c1 === c2) return false;
        
        // 다른 값이면 false
        if (board[r1][c1] !== board[r2][c2]) return false;
        
        const dirs = [[-1,0], [1,0], [0,-1], [0,1]];
        const queue = [{r:r1, c:c1, dist:0}];
        const visited = new Set();
        visited.add(r1 + ',' + c1);
        
        while (queue.length > 0) {
            const {r, c, dist} = queue.shift();
            
            if (r === r2 && c === c2) return true;
            if (dist > 20) continue; // 너무 긴 경로는 무시
            
            for (const [dr, dc] of dirs) {
                const nr = r + dr;
                const nc = c + dc;
                
                // 범위 체크 (패딩 포함)
                if (nr < 0 || nr > rows + 1 || nc < 0 || nc > cols + 1) continue;
                if (visited.has(nr + ',' + nc)) continue;
                
                // 빈칸이거나 목적지
                if (board[nr][nc] === null || (nr === r2 && nc === c2)) {
                    visited.add(nr + ',' + nc);
                    queue.push({r: nr, c: nc, dist: dist + 1});
                }
            }
        }
        return false;
    }
    
    function showHint() {
        if (timeLeft <= 0) return;
        
        for (let i = 1; i <= rows; i++) {
            for (let j = 1; j <= cols; j++) {
                if (!board[i][j]) continue;
                for (let ii = 1; ii <= rows; ii++) {
                    for (let jj = 1; jj <= cols; jj++) {
                        if ((i !== ii || j !== jj) && board[i][j] === board[ii][jj] && canConnect(i,j,ii,jj)) {
                            const t1 = document.querySelector(`.tile[data-r="${i}"][data-c="${j}"]`);
                            const t2 = document.querySelector(`.tile[data-r="${ii}"][data-c="${jj}"]`);
                            if (t1) t1.style.background = '#f59563';
                            if (t2) t2.style.background = '#f59563';
                            setTimeout(() => {
                                if (t1) t1.style.background = '';
                                if (t2) t2.style.background = '';
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
            document.getElementById('messageText').innerHTML = '🎉 클리어!<br>점수: ' + score;
            document.getElementById('gameMessage').classList.add('show');
            clearInterval(timer);
        } else if (timeLeft <= 0) {
            if (navigator.vibrate) navigator.vibrate(200);
            document.getElementById('messageText').innerHTML = '😢 시간 초과!';
            document.getElementById('gameMessage').classList.add('show');
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
        document.getElementById('time').textContent = m + ':' + s.toString().padStart(2, '0');
    }
    
    window.onresize = renderBoard;
    
    if (localStorage.getItem('darkMode') === '1') {
        document.body.classList.add('dark-mode');
        document.querySelector('header')?.classList.add('dark');
    }
    
    initGame();
    </script>
</body>
</html>
