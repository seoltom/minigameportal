/**
 * Solitaire 게임 로직 (Simplified Klondike)
 */

const SUITS = ['♥', '♦', '♣', '♠'];
const RANKS = ['A', '2', '3', '4', '5', '6', '7', '8', '9', '10', 'J', 'Q', 'K'];

let deck = [];
let stock = [];
let waste = [];
let foundations = [[], [], [], []];
let tableau = [[], [], [], [], [], [], []];
let selectedCard = null;
let selectedSource = null;
let score = 0;

function init() {
    initGame();
}

function createDeck() {
    const newDeck = [];
    for (const suit of SUITS) {
        for (let i = 0; i < RANKS.length; i++) {
            newDeck.push({
                suit: suit,
                rank: RANKS[i],
                value: i + 1,
                color: (suit === '♥' || suit === '♦') ? 'red' : 'black',
                faceUp: false
            });
        }
    }
    return newDeck;
}

function shuffleDeck(d) {
    for (let i = d.length - 1; i > 0; i--) {
        const j = Math.floor(Math.random() * (i + 1));
        [d[i], d[j]] = [d[j], d[i]];
    }
    return d;
}

function initGame() {
    deck = shuffleDeck(createDeck());
    stock = [];
    waste = [];
    foundations = [[], [], [], []];
    tableau = [[], [], [], [], [], [], []];
    selectedCard = null;
    selectedSource = null;
    score = 0;
    
    // 타로 분배
    let idx = 0;
    for (let i = 0; i < 7; i++) {
        for (let j = i; j < 7; j++) {
            const card = deck[idx++];
            if (i === j) card.faceUp = true;
            tableau[j].push(card);
        }
    }
    
    // 남은 카드를 stock에
    while (idx < deck.length) {
        stock.push(deck[idx++]);
    }
    
    document.getElementById('score').textContent = score;
    render();
}

function render() {
    renderStock();
    renderWaste();
    renderFoundations();
    renderTableau();
}

function renderStock() {
    const stockEl = document.getElementById('stock');
    if (stock.length > 0) {
        stockEl.innerHTML = '🃏';
        stockEl.style.background = 'linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%)';
        stockEl.style.border = '2px solid #1e40af';
    } else {
        stockEl.innerHTML = '🔄';
        stockEl.style.background = 'rgba(0,0,0,0.3)';
        stockEl.style.border = '2px dashed rgba(255,255,255,0.3)';
    }
}

function renderWaste() {
    const wasteEl = document.getElementById('waste');
    wasteEl.innerHTML = '';
    
    if (waste.length > 0) {
        const card = waste[waste.length - 1];
        wasteEl.appendChild(createCardElement(card));
        
        if (selectedSource === 'waste') {
            wasteEl.querySelector('.card').classList.add('selected');
        }
    }
}

function renderFoundations() {
    const suitSymbols = ['♥', '♦', '♣', '♠'];
    for (let i = 0; i < 4; i++) {
        const foundationEl = document.getElementById(`foundation-${i}`);
        foundationEl.innerHTML = '';
        
        if (foundations[i].length > 0) {
            const card = foundations[i][foundations[i].length - 1];
            foundationEl.appendChild(createCardElement(card));
            foundationEl.style.background = 'rgba(255,255,255,0.9)';
        } else {
            foundationEl.innerHTML = suitSymbols[i];
            foundationEl.style.background = 'rgba(0,0,0,0.3)';
        }
    }
}

function renderTableau() {
    for (let i = 0; i < 7; i++) {
        const colEl = document.getElementById(`col-${i}`);
        colEl.innerHTML = '';
        
        tableau[i].forEach((card, idx) => {
            const cardEl = createCardElement(card);
            cardEl.style.top = (idx * 25) + 'px';
            
            if (selectedSource === `tableau-${i}` && idx >= tableau[i].indexOf(card)) {
                cardEl.classList.add('selected');
            }
            
            cardEl.onclick = () => handleTableauClick(i, idx);
            colEl.appendChild(cardEl);
        });
    }
}

function createCardElement(card) {
    const el = document.createElement('div');
    el.className = `card ${card.faceUp ? card.color : 'face-down'}`;
    
    if (card.faceUp) {
        el.innerHTML = `<div class="card-content">${card.rank}${card.suit}</div>`;
    }
    
    return el;
}

function drawCard() {
    if (stock.length > 0) {
        const card = stock.pop();
        card.faceUp = true;
        waste.push(card);
    } else {
        // stock을 다시 채움
        while (waste.length > 0) {
            const card = waste.pop();
            card.faceUp = false;
            stock.push(card);
        }
    }
    render();
}

function handleWasteClick() {
    if (waste.length === 0) return;
    
    if (selectedCard) {
        // foundation으로 이동 시도
        const foundationsIdx = tryMoveToFoundation(waste[waste.length - 1], 'waste');
        if (foundationsIdx !== -1) {
            selectedCard = null;
            selectedSource = null;
            return;
        }
    } else {
        // 선택
        selectedCard = waste[waste.length - 1];
        selectedSource = 'waste';
    }
    render();
}

function handleFoundationClick(idx) {
    if (foundations[idx].length === 0) return;
    
    if (selectedCard && selectedSource !== `foundation-${idx}`) {
        const card = foundations[idx][foundations[idx].length - 1];
        if (selectedSource === 'waste') {
            foundations[idx].push(waste.pop());
            score += 10;
            waste.pop(); // 이미foundation에 push해서 다시 pop
            foundations[idx].push(card);
        } else {
            const colIdx = parseInt(selectedSource.split('-')[1]);
            const movingCard = tableau[colIdx].pop();
            foundations[idx].push(movingCard);
            score += 10;
            
            // 다음 카드 flip
            if (tableau[colIdx].length > 0) {
                tableau[colIdx][tableau[colIdx].length - 1].faceUp = true;
            }
        }
        selectedCard = null;
        selectedSource = null;
        document.getElementById('score').textContent = score;
        render();
    }
}

function handleTableauClick(colIdx, cardIdx) {
    const column = tableau[colIdx];
    const card = column[cardIdx];
    
    if (!card.faceUp) return;
    
    if (selectedCard) {
        // 다른 카드로 이동 시도
        if (tryMoveToTableau(colIdx, cardIdx)) {
            selectedCard = null;
            selectedSource = null;
        }
    } else {
        // 선택
        selectedCard = card;
        selectedSource = `tableau-${colIdx}`;
    }
    render();
}

function tryMoveToFoundation(card, source) {
    for (let i = 0; i < 4; i++) {
        const foundation = foundations[i];
        
        if (foundation.length === 0) {
            if (card.value === 1) { // Ace
                moveToFoundation(i, card, source);
                return i;
            }
        } else {
            const topCard = foundation[foundation.length - 1];
            if (topCard.suit === card.suit && card.value === topCard.value + 1) {
                moveToFoundation(i, card, source);
                return i;
            }
        }
    }
    return -1;
}

function moveToFoundation(fIdx, card, source) {
    if (source === 'waste') {
        foundations[fIdx].push(waste.pop());
    } else {
        const colIdx = parseInt(source.split('-')[1]);
        foundations[fIdx].push(tableau[colIdx].pop());
        
        // 다음 카드 flip
        if (tableau[colIdx].length > 0) {
            tableau[colIdx][tableau[colIdx].length - 1].faceUp = true;
        }
    }
    score += 10;
    document.getElementById('score').textContent = score;
    render();
}

function tryMoveToTableau(targetColIdx, targetCardIdx) {
    if (selectedSource === 'waste') {
        const card = waste[waste.length - 1];
        const targetCard = tableau[targetColIdx][targetCardIdx];
        
        if (canPlaceOnTableau(card, targetCard)) {
            tableau[targetColIdx].push(waste.pop());
            render();
            return true;
        }
    } else if (selectedSource.startsWith('tableau-')) {
        const sourceColIdx = parseInt(selectedSource.split('-')[1]);
        const sourceColumn = tableau[sourceColIdx];
        const cardIdx = sourceColumn.findIndex(c => c === selectedCard);
        
        if (cardIdx === -1) return false;
        
        const cardsToMove = sourceColumn.slice(cardIdx);
        const targetCard = tableau[targetColIdx][targetCardIdx];
        
        if (canPlaceOnTableau(cardsToMove[0], targetCard)) {
            tableau[targetColIdx] = tableau[targetColIdx].concat(cardsToMove);
            tableau[sourceColIdx].splice(cardIdx);
            
            // 다음 카드 flip
            if (tableau[sourceColIdx].length > 0) {
                tableau[sourceColIdx][tableau[sourceColIdx].length - 1].faceUp = true;
            }
            score += 5;
            document.getElementById('score').textContent = score;
            render();
            return true;
        }
    }
    return false;
}

function canPlaceOnTableau(card, targetCard) {
    if (!targetCard) {
        return card.value === 13; // King
    }
    return card.color !== targetCard.color && card.value === targetCard.value - 1;
}

// 이벤트 리스너
document.getElementById('waste').onclick = handleWasteClick;

for (let i = 0; i < 4; i++) {
    document.getElementById(`foundation-${i}`).onclick = () => handleFoundationClick(i);
}

// 초기화 실행
init();
