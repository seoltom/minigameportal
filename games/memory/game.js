/**
 * Memory 게임 로직
 */

const PAIRS_COUNT = 8;
const EMOJIS = ['🐶', '🐱', '🐭', '🐹', '🐰', '🦊', '🐻', '🐼', '🐨', '🐯', '🦁', '🐮', '🐷', '🐸', '🐵', '🐔'];

let cards = [];
let flippedCards = [];
let matchedPairs = 0;
let moves = 0;
let isLocked = false;
let gridSize = 4;

function init() {
    toggleDifficulty();
}

function toggleDifficulty() {
    const labels = ['4x4', '4x5', '5x6'];
    const sizes = [4, 4, 5];
    const counts = [8, 10, 15];
    
    const currentLabel = document.getElementById('diffLabel').textContent;
    let currentIndex = labels.indexOf(currentLabel);
    
    currentIndex = (currentIndex + 1) % labels.length;
    
    document.getElementById('diffLabel').textContent = labels[currentIndex];
    
    if (currentIndex === 0) gridSize = 4;
    else if (currentIndex === 1) gridSize = 4;
    else gridSize = 5;
    
    initGame();
}

function initGame() {
    cards = [];
    flippedCards = [];
    matchedPairs = 0;
    moves = 0;
    isLocked = false;
    
    document.getElementById('moves').textContent = '0';
    document.getElementById('pairs').textContent = '0';
    
    createCards();
    renderBoard();
    hideMessage();
}

function createCards() {
    const totalPairs = gridSize === 4 ? 8 : (gridSize === 5 ? 15 : 10);
    const selectedEmojis = EMOJIS.slice(0, totalPairs);
    
    cards = [];
    for (let i = 0; i < totalPairs; i++) {
        cards.push({ id: i, emoji: selectedEmojis[i] });
        cards.push({ id: i, emoji: selectedEmojis[i] });
    }
    
    // 셔플
    for (let i = cards.length - 1; i > 0; i--) {
        const j = Math.floor(Math.random() * (i + 1));
        [cards[i], cards[j]] = [cards[j], cards[i]];
    }
}

function renderBoard() {
    const gameBoard = document.getElementById('game-board');
    gameBoard.innerHTML = '';
    
    const cols = cards.length / gridSize;
    gameBoard.style.gridTemplateColumns = `repeat(${cols}, 55px)`;
    
    cards.forEach((card, index) => {
        const cardEl = document.createElement('div');
        cardEl.className = 'card';
        cardEl.dataset.index = index;
        
        cardEl.innerHTML = `
            <div class="front">❓</div>
            <div class="back">${card.emoji}</div>
        `;
        
        cardEl.addEventListener('click', () => flipCard(index));
        gameBoard.appendChild(cardEl);
    });
}

function flipCard(index) {
    if (isLocked) return;
    if (flippedCards.length >= 2) return;
    if (cards[index].matched) return;
    if (flippedCards.includes(index)) return;
    
    const cardEl = document.querySelector(`.card[data-index="${index}"]`);
    cardEl.classList.add('flipped');
    flippedCards.push(index);
    
    if (flippedCards.length === 2) {
        moves++;
        document.getElementById('moves').textContent = moves;
        checkMatch();
    }
}

function checkMatch() {
    const [first, second] = flippedCards;
    const firstCard = cards[first];
    const secondCard = cards[second];
    
    if (firstCard.id === secondCard.id) {
        // 매칭 성공
        cards[first].matched = true;
        cards[second].matched = true;
        matchedPairs++;
        
        document.getElementById('pairs').textContent = matchedPairs;
        
        setTimeout(() => {
            const firstEl = document.querySelector(`.card[data-index="${first}"]`);
            const secondEl = document.querySelector(`.card[data-index="${second}"]`);
            firstEl.classList.add('matched');
            secondEl.classList.add('matched');
            
            if (navigator.vibrate) navigator.vibrate(30);
            
            flippedCards = [];
            
            // 승리 검사
            const totalPairs = cards.length / 2;
            if (matchedPairs === totalPairs) {
                showMessage(`🎉 클리어!<br><br>${moves}번 시도`);
            }
        }, 300);
    } else {
        // 매칭 실패
        isLocked = true;
        setTimeout(() => {
            const firstEl = document.querySelector(`.card[data-index="${first}"]`);
            const secondEl = document.querySelector(`.card[data-index="${second}"]`);
            firstEl.classList.remove('flipped');
            secondEl.classList.remove('flipped');
            flippedCards = [];
            isLocked = false;
        }, 1000);
    }
}

function showMessage(text) {
    document.getElementById('messageText').innerHTML = text;
    document.getElementById('gameMessage').classList.add('show');
}

function hideMessage() {
    document.getElementById('gameMessage').classList.remove('show');
}

// 초기화 실행
init();
