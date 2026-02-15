<?php
require_once '../../config.php';
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <title>Solitaire - <?= SITE_NAME ?></title>
    <link rel="stylesheet" href="../../css/style.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body { height: 100%; overflow: hidden; }
        body { 
            background: linear-gradient(135deg, #1a5f2a 0%, #0d3d1a 100%);
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
            background: rgba(0,0,0,0.2);
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
            position: relative;
            display: flex;
            flex-direction: column;
            padding: 10px;
            overflow: hidden;
        }
        
        .top-row {
            display: flex;
            gap: 8px;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        
        .card-slot {
            width: 45px;
            height: 60px;
            background: rgba(0,0,0,0.3);
            border-radius: 6px;
            border: 2px dashed rgba(255,255,255,0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
        }
        
        .foundations {
            display: flex;
            gap: 6px;
        }
        .foundation {
            width: 45px;
            height: 60px;
            background: rgba(0,0,0,0.3);
            border-radius: 6px;
            border: 2px dashed rgba(255,255,255,0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }
        
        #tableau {
            flex: 1;
            display: flex;
            gap: 6px;
            justify-content: center;
        }
        
        .column {
            width: 45px;
            min-height: 60px;
            position: relative;
        }
        
        .card {
            width: 45px;
            height: 60px;
            background: #fff;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            font-weight: bold;
            position: absolute;
            box-shadow: 0 2px 4px rgba(0,0,0,0.3);
            cursor: pointer;
            transition: transform 0.1s;
        }
        .card:active { transform: scale(0.95); }
        .card.red { color: #dc2626; }
        .card.black { color: #1f2937; }
        .card.face-down { 
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
        }
        
        .controls {
            display: flex;
            gap: 10px;
            padding: 10px;
            justify-content: center;
            background: rgba(0,0,0,0.2);
        }
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            font-size: 14px;
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
        <div class="info-box">남은: <span class="info-value" id="remaining">52</span></div>
    </div>
    
    <div id="game-area">
        <div class="top-row">
            <div class="card-slot" id="stock" onclick="drawCard()">🃏</div>
            <div class="card-slot" id="waste"></div>
            <div class="foundations">
                <div class="foundation" id="foundation-0">♥</div>
                <div class="foundation" id="foundation-1">♦</div>
                <div class="foundation" id="foundation-2">♣</div>
                <div class="foundation" id="foundation-3">♠</div>
            </div>
        </div>
        <div id="tableau">
            <div class="column" id="col-0"></div>
            <div class="column" id="col-1"></div>
            <div class="column" id="col-2"></div>
            <div class="column" id="col-3"></div>
            <div class="column" id="col-4"></div>
            <div class="column" id="col-5"></div>
            <div class="column" id="col-6"></div>
        </div>
    </div>
    
    <div class="controls">
        <button class="btn" onclick="initGame()">새 게임</button>
    </div>
    
    <div class="game-message" id="gameMessage">
        <div id="messageText"></div>
        <button class="btn" onclick="initGame()" style="margin-top:15px;">재시작</button>
    </div>
    
    <script>
    const suits = ['♥', '♦', '♣', '♠'];
    const ranks = ['A', '2', '3', '4', '5', '6', '7', '8', '9', '10', 'J', 'Q', 'K'];
    
    let deck = [];
    let stock = [];
    let waste = [];
    let foundations = [[], [], [], []];
    let tableau = [[], [], [], [], [], [], []];
    let selectedCard = null;
    let selectedFrom = null;
    let score = 0;
    
    function createDeck() {
        deck = [];
        for (let s of suits) {
            for (let i = 0; i < ranks.length; i++) {
                deck.push({ suit: s, rank: i, faceUp: false });
            }
        }
        // 셔플
        for (let i = deck.length - 1; i > 0; i--) {
            const j = Math.floor(Math.random() * (i + 1));
            [deck[i], deck[j]] = [deck[j], deck[i]];
        }
    }
    
    function initGame() {
        createDeck();
        stock = [...deck.slice(28)];
        waste = [];
        foundations = [[], [], [], []];
        tableau = [[], [], [], [], [], [], []];
        selectedCard = null;
        selectedFrom = null;
        score = 0;
        
        // 테이블로 분배
        let idx = 0;
        for (let i = 0; i < 7; i++) {
            for (let j = i; j < 7; j++) {
                const card = stock.splice(stock.length - 1, 1)[0];
                if (i === j) card.faceUp = true;
                tableau[j].push(card);
            }
        }
        
        updateDisplay();
        document.getElementById('gameMessage').classList.remove('show');
        
        if (localStorage.getItem('darkMode') === '1') {
            document.body.classList.add('dark-mode');
            document.querySelector('header').classList.add('dark');
        }
    }
    
    function updateDisplay() {
        document.getElementById('score').textContent = score;
        document.getElementById('remaining').textContent = stock.length;
        
        // Stock
        document.getElementById('stock').textContent = stock.length > 0 ? '🃏' : '🔄';
        
        // Waste
        const wasteEl = document.getElementById('waste');
        wasteEl.innerHTML = '';
        if (waste.length > 0) {
            const card = waste[waste.length - 1];
            wasteEl.appendChild(createCardElement(card, false, 'waste', waste.length - 1));
        }
        
        // Foundations
        for (let i = 0; i < 4; i++) {
            const el = document.getElementById('foundation-' + i);
            el.innerHTML = '';
            if (foundations[i].length > 0) {
                const card = foundations[i][foundations[i].length - 1];
                el.appendChild(createCardElement(card, true, 'foundation', i));
            }
        }
        
        // Tableau
        for (let i = 0; i < 7; i++) {
            const colEl = document.getElementById('col-' + i);
            colEl.innerHTML = '';
            tableau[i].forEach((card, idx) => {
                const cardEl = createCardElement(card, true, 'tableau', i, idx);
                cardEl.style.top = (idx * 25) + 'px';
                colEl.appendChild(cardEl);
            });
        }
    }
    
    function createCardElement(card, faceUp, from, colIdx, rowIdx) {
        const el = document.createElement('div');
        el.className = 'card';
        
        if (!faceUp) {
            el.classList.add('face-down');
        } else {
            el.textContent = card.rank + card.suit;
            el.classList.add(['♥', '♦'].includes(card.suit) ? 'red' : 'black');
            el.onclick = () => selectCard(card, from, colIdx, rowIdx);
        }
        
        return el;
    }
    
    function selectCard(card, from, colIdx, rowIdx) {
        // foundation에서 카드 선택 시 테이블로 이동 불가
        if (from === 'foundation') return;
        
        if (selectedCard) {
            // 이미 선택된 카드가 있으면 이동 시도
            if (canMove(card, selectedCard)) {
                moveCard(from, colIdx, rowIdx, 'tableau', colIdx);
            } else {
                // foundation으로 시도
                if (from === 'tableau' && rowIdx === tableau[colIdx].length - 1) {
                    if (canMoveToFoundation(card, colIdx)) {
                        moveCard(from, colIdx, rowIdx, 'foundation', getFoundationIdx(card));
                    } else {
                        selectedCard = null;
                        selectedFrom = null;
                    }
                } else {
                    selectedCard = null;
                    selectedFrom = null;
                }
            }
        } else {
            // foundation 맨 위 카드 or 테이블 맨 위 카드만 선택
            if (from === 'waste' && waste.length > 0 && card === waste[waste.length - 1]) {
                selectedCard = card;
                selectedFrom = { from: 'waste', colIdx: -1, rowIdx: -1 };
            } else if (from === 'tableau') {
                const col = tableau[colIdx];
                if (rowIdx === col.length - 1 || (rowIdx < col.length - 1 && col[rowIdx].faceUp)) {
                    selectedCard = card;
                    selectedFrom = { from, colIdx, rowIdx };
                }
            }
        }
        updateDisplay();
    }
    
    function canMove(card, target) {
        if (target.suit === '♥' || target.suit === '♦') {
            return card.suit === '♣' || card.suit === '♠';
        } else {
            return card.suit === '♥' || card.suit === '♦';
        }
    }
    
    function canMoveToFoundation(card, colIdx) {
        const foundationsBySuit = { '♥': 0, '♦': 1, '♣': 2, '♠': 3 };
        const suitIdx = foundationsBySuit[card.suit];
        const pile = foundations[suitIdx];
        
        if (pile.length === 0) {
            return card.rank === 0; // A만
        }
        return card.rank === pile[pile.length - 1].rank + 1;
    }
    
    function getFoundationIdx(card) {
        const foundationsBySuit = { '♥': 0, '♦': 1, '♣': 2, '♠': 3 };
        return foundationsBySuit[card.suit];
    }
    
    function moveCard(from, colIdx, rowIdx, to, toIdx) {
        let cards = [];
        
        if (from === 'waste') {
            cards = [waste.pop()];
        } else if (from === 'tableau') {
            cards = tableau[colIdx].splice(rowIdx);
        }
        
        if (to === 'tableau') {
            tableau[toIdx].push(...cards);
            score += 5;
            // 뒤집힌 카드 있으면 드러내기
            if (tableau[colIdx].length > 0) {
                const last = tableau[colIdx][tableau[colIdx].length - 1];
                if (!last.faceUp) {
                    last.faceUp = true;
                    score += 5;
                }
            }
        } else if (to === 'foundation') {
            foundations[toIdx].push(cards[0]);
            score += 10;
            
            // 모든 카드 foundations에 있으면 클리어
            checkWin();
        }
        
        selectedCard = null;
        selectedFrom = null;
        updateDisplay();
    }
    
    function drawCard() {
        if (stock.length > 0) {
            const card = stock.pop();
            card.faceUp = true;
            waste.push(card);
            selectedCard = null;
            selectedFrom = null;
            updateDisplay();
        } else {
            // stock 리셋
            while (waste.length > 0) {
                const card = waste.pop();
                card.faceUp = false;
                stock.unshift(card);
            }
            updateDisplay();
        }
    }
    
    function checkWin() {
        const total = foundations.reduce((sum, pile) => sum + pile.length, 0);
        if (total === 52) {
            document.getElementById('messageText').innerHTML = '🎉 클리어!<br><br>점수: ' + score;
            document.getElementById('gameMessage').classList.add('show');
        }
    }
    
    initGame();
    </script>
</body>
</html>
