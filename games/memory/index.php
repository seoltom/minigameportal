<?php
require_once '../../config.php';
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <title>Memory - <?= SITE_NAME ?></title>
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
            gap: 6px;
            padding: 10px;
            justify-content: center;
            align-content: center;
        }
        .card {
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            cursor: pointer;
            user-select: none;
            background: #4a5568;
            transition: transform 0.3s, background 0.3s;
            aspect-ratio: 1;
        }
        .card.flipped { background: #fff; transform: rotateY(180deg); }
        .card.matched { background: #48bb78; opacity: 0.5; }
        .card .back { display: none; }
        .card.flipped .back { display: flex; }
        .card.flipped .front { display: none; }
        
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
        <div class="info-box">쌍: <span class="info-value" id="pairs">0</span>/<span id="totalPairs">8</span></div>
        <div class="info-box">횟수: <span class="info-value" id="moves">0</span></div>
    </div>
    
    <div class="controls">
        <button class="btn" onclick="initGame()">새 게임</button>
        <button class="btn" onclick="changeLevel()"><span id="levelLabel">4x4</span></button>
    </div>
    
    <div id="game-board"></div>
    
    <div class="game-message" id="gameMessage">
        <div id="messageText"></div>
        <button class="btn" onclick="initGame()" style="margin-top:15px;">다시하기</button>
    </div>
    
    <script>
    const EMOJIS = ['🐶','🐱','🐭','🐹','🐰','🦊','🐻','🐼','🐨','🐯','🦁','🐮','🐷','🐸','🐵','🐔'];
    const LEVELS = [
        { rows: 4, cols: 4, pairs: 8, label: '4x4' },
        { rows: 4, cols: 5, pairs: 10, label: '4x5' },
        { rows: 5, cols: 6, pairs: 15, label: '5x6' }
    ];
    
    let levelIdx = 0;
    let cards = [], flipped = [], matched = [];
    let moves = 0, matchedCount = 0, isLocked = false;
    
    function changeLevel() {
        levelIdx = (levelIdx + 1) % LEVELS.length;
        document.getElementById('levelLabel').textContent = LEVELS[levelIdx].label;
        initGame();
    }
    
    function initGame() {
        const level = LEVELS[levelIdx];
        const totalPairs = level.pairs;
        
        document.getElementById('totalPairs').textContent = totalPairs;
        document.getElementById('pairs').textContent = '0';
        document.getElementById('moves').textContent = '0';
        document.getElementById('gameMessage').classList.remove('show');
        
        // 카드 생성
        const selected = EMOJIS.slice(0, totalPairs);
        cards = [];
        for (let i = 0; i < totalPairs; i++) {
            cards.push({ id: i, emoji: selected[i] });
            cards.push({ id: i, emoji: selected[i] });
        }
        
        // 셔플
        for (let i = cards.length - 1; i > 0; i--) {
            const j = Math.floor(Math.random() * (i + 1));
            [cards[i], cards[j]] = [cards[j], cards[i]];
        }
        
        flipped = [];
        matched = [];
        moves = 0;
        matchedCount = 0;
        isLocked = false;
        
        renderBoard();
        
        if (localStorage.getItem('darkMode') === '1') {
            document.body.classList.add('dark-mode');
            document.querySelector('header').classList.add('dark');
        }
    }
    
    function renderBoard() {
        const level = LEVELS[levelIdx];
        const container = document.getElementById('game-board');
        container.innerHTML = '';
        
        const winW = container.clientWidth - 20;
        const winH = container.clientHeight - 20;
        const cardW = Math.floor((winW - (level.cols - 1) * 6) / level.cols);
        const cardH = Math.floor((winH - (level.rows - 1) * 6) / level.rows);
        const size = Math.min(cardW, cardH, 70);
        
        container.style.gridTemplateColumns = `repeat(${level.cols}, ${size}px)`;
        
        cards.forEach((card, idx) => {
            const el = document.createElement('div');
            el.className = 'card' + (flipped.includes(idx) ? ' flipped' : '') + (matched.includes(idx) ? ' matched' : '');
            el.style.width = size + 'px';
            el.style.height = size + 'px';
            el.style.fontSize = Math.floor(size * 0.5) + 'px';
            el.dataset.idx = idx;
            el.innerHTML = `<div class="back">${card.emoji}</div><div class="front">❓</div>`;
            el.onclick = () => flipCard(idx);
            container.appendChild(el);
        });
    }
    
    function flipCard(idx) {
        if (isLocked) return;
        if (flipped.length >= 2) return;
        if (matched.includes(idx)) return;
        if (flipped.includes(idx)) return;
        
        flipped.push(idx);
        renderBoard();
        
        if (flipped.length === 2) {
            moves++;
            document.getElementById('moves').textContent = moves;
            checkMatch();
        }
    }
    
    function checkMatch() {
        const [a, b] = flipped;
        
        if (cards[a].id === cards[b].id) {
            // 매칭 성공
            matched.push(a, b);
            matchedCount++;
            document.getElementById('pairs').textContent = matchedCount;
            
            if (navigator.vibrate) navigator.vibrate(30);
            
            flipped = [];
            renderBoard();
            
            const level = LEVELS[levelIdx];
            if (matchedCount === level.pairs) {
                if (navigator.vibrate) navigator.vibrate([100, 50, 100]);
                document.getElementById('messageText').innerHTML = `🎉 클리어!<br>${moves}번 시도`;
                document.getElementById('gameMessage').classList.add('show');
            }
        } else {
            // 매칭 실패
            isLocked = true;
            setTimeout(() => {
                flipped = [];
                isLocked = false;
                renderBoard();
            }, 800);
        }
    }
    
    window.onresize = renderBoard;
    
    initGame();
    </script>
</body>
</html>
