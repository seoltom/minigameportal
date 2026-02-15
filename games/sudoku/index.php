<?php
require_once '../../config.php';
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <title>Sudoku - <?= SITE_NAME ?></title>
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
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 10px;
            overflow: auto;
        }
        
        #sudoku-board {
            display: grid;
            grid-template-columns: repeat(9, 1fr);
            gap: 1px;
            background: #fff;
            padding: 2px;
            border-radius: 8px;
            max-width: 400px;
            width: 100%;
            aspect-ratio: 1;
        }
        
        .cell {
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: clamp(14px, 4vw, 22px);
            font-weight: bold;
            cursor: pointer;
            user-select: none;
        }
        .cell.dark-mode { background: #2a2a40; color: #fff; }
        
        .cell:nth-child(3n) { border-right: 2px solid #333; }
        .cell:nth-child(9n) { border-right: none; }
        
        .cell:nth-child(n+19):nth-child(-n+27),
        .cell:nth-child(n+46):nth-child(-n+54) { border-bottom: 2px solid #333; }
        
        .cell.fixed { background: #e0e0e0; color: #333; }
        .cell.fixed.dark-mode { background: #3a3a50; color: #ccc; }
        
        .cell.selected { background: #93c5fd !important; }
        .cell.selected.dark-mode { background: #4a5568 !important; }
        
        .cell.highlight { background: #bfdbfe; }
        .cell.highlight.dark-mode { background: #3a3a50; }
        
        .cell.error { background: #fecaca !important; }
        .cell.error.dark-mode { background: #7f1d1d !important; }
        
        .cell.same { background: #dbeafe; }
        .cell.same.dark-mode { background: #374151; }
        
        .numpad {
            display: flex;
            gap: 6px;
            margin-top: 15px;
            flex-wrap: wrap;
            justify-content: center;
            max-width: 400px;
        }
        
        .num-btn {
            width: 45px;
            height: 45px;
            border: none;
            border-radius: 8px;
            background: #4ade80;
            color: #fff;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .num-btn:active { transform: scale(0.95); }
        .num-btn.clear { background: #f87171; }
        .num-btn.note { background: #60a5fa; }
        
        .controls {
            display: flex;
            gap: 8px;
            margin-top: 15px;
            flex-wrap: wrap;
            justify-content: center;
        }
        
        .btn {
            padding: 10px 18px;
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
        <div class="info-box">난이도: <span class="info-value" id="level">중간</span></div>
        <div class="info-box">시간: <span class="info-value" id="timer">0:00</span></div>
        <div class="info-box">남은: <span class="info-value" id="remaining">81</span></div>
    </div>
    
    <div id="game-area">
        <div id="sudoku-board"></div>
        
        <div class="numpad">
            <button class="num-btn" onclick="inputNumber(1)">1</button>
            <button class="num-btn" onclick="inputNumber(2)">2</button>
            <button class="num-btn" onclick="inputNumber(3)">3</button>
            <button class="num-btn" onclick="inputNumber(4)">4</button>
            <button class="num-btn" onclick="inputNumber(5)">5</button>
            <button class="num-btn" onclick="inputNumber(6)">6</button>
            <button class="num-btn" onclick="inputNumber(7)">7</button>
            <button class="num-btn" onclick="inputNumber(8)">8</button>
            <button class="num-btn" onclick="inputNumber(9)">9</button>
        </div>
        
        <div class="controls">
            <button class="btn" onclick="toggleNote()">📝 메모</button>
            <button class="btn" onclick="undoMove()">↩️ 취소</button>
            <button class="btn" onclick="changeLevel()">🔄 새 게임</button>
        </div>
    </div>
    
    <div class="game-message" id="gameMessage">
        <div id="messageText"></div>
        <button class="btn" onclick="initGame()" style="margin-top:15px;">재시작</button>
    </div>
    
    <script>
    let board = [];
    let solution = [];
    let notes = [];
    let selectedCell = null;
    let noteMode = false;
    let timerInterval = null;
    let seconds = 0;
    let gameStarted = false;
    let history = [];
    
    const difficulties = {
        easy: { name: '쉬움', removes: 35 },
        medium: { name: '중간', removes: 45 },
        hard: { name: '어려움', removes: 55 }
    };
    let currentLevel = 'medium';
    
    function initGame() {
        // 스도쿠 생성
        const fullBoard = generateSudoku();
        solution = JSON.parse(JSON.stringify(fullBoard));
        
        // 퍼즐 생성 (숫자 제거)
        board = JSON.parse(JSON.stringify(fullBoard));
        const removes = difficulties[currentLevel].removes;
        const positions = [];
        for (let i = 0; i < 81; i++) positions.push(i);
        
        for (let i = 0; i < removes; i++) {
            const idx = Math.floor(Math.random() * positions.length);
            board[positions[idx]] = 0;
            positions.splice(idx, 1);
        }
        
        notes = Array(81).fill().map(() => new Set());
        selectedCell = null;
        noteMode = false;
        seconds = 0;
        gameStarted = false;
        history = [];
        
        if (timerInterval) clearInterval(timerInterval);
        
        document.getElementById('level').textContent = difficulties[currentLevel].name;
        document.getElementById('timer').textContent = '0:00';
        document.getElementById('remaining').textContent = countRemaining();
        document.getElementById('gameMessage').classList.remove('show');
        
        renderBoard();
        
        if (localStorage.getItem('darkMode') === '1') {
            document.body.classList.add('dark-mode');
            document.querySelector('header').classList.add('dark');
        }
    }
    
    function generateSudoku() {
        // 빈 보드
        let grid = Array(81).fill(0);
        
        // 첫 행 랜덤으로 채우기
        const nums = [1,2,3,4,5,6,7,8,9];
        for (let i = 0; i < 9; i++) {
            const idx = Math.floor(Math.random() * nums.length);
            grid[i] = nums[idx];
            nums.splice(idx, 1);
        }
        
        // 스도쿠 생성 (백트래킹)
        if (solve(grid)) {
            return grid;
        }
        return generateSudoku();
    }
    
    function solve(grid) {
        for (let i = 0; i < 81; i++) {
            if (grid[i] === 0) {
                const nums = [1,2,3,4,5,6,7,8,9];
                shuffleArray(nums);
                
                for (const num of nums) {
                    if (isValid(grid, i, num)) {
                        grid[i] = num;
                        if (solve(grid)) return true;
                        grid[i] = 0;
                    }
                }
                return false;
            }
        }
        return true;
    }
    
    function shuffleArray(arr) {
        for (let i = arr.length - 1; i > 0; i--) {
            const j = Math.floor(Math.random() * (i + 1));
            [arr[i], arr[j]] = [arr[j], arr[i]];
        }
    }
    
    function isValid(grid, pos, num) {
        const row = Math.floor(pos / 9);
        const col = pos % 9;
        
        // 행 검사
        for (let c = 0; c < 9; c++) {
            if (grid[row * 9 + c] === num) return false;
        }
        
        // 열 검사
        for (let r = 0; r < 9; r++) {
            if (grid[r * 9 + col] === num) return false;
        }
        
        // 3x3 박스 검사
        const boxRow = Math.floor(row / 3) * 3;
        const boxCol = Math.floor(col / 3) * 3;
        for (let r = 0; r < 3; r++) {
            for (let c = 0; c < 3; c++) {
                if (grid[(boxRow + r) * 9 + boxCol + c] === num) return false;
            }
        }
        
        return true;
    }
    
    function renderBoard() {
        const container = document.getElementById('sudoku-board');
        container.innerHTML = '';
        
        const isDark = document.body.classList.contains('dark-mode');
        
        for (let i = 0; i < 81; i++) {
            const cell = document.createElement('div');
            cell.className = 'cell' + (isDark ? ' dark-mode' : '');
            
            if (board[i] === 0) {
                const noteArr = Array.from(notes[i]).sort();
                cell.textContent = noteArr.join(' ');
                cell.style.fontSize = '10px';
            } else {
                cell.textContent = board[i];
            }
            
            if (selectedCell === i) {
                cell.classList.add('selected');
            }
            
            if (solution[i] !== 0 && board[i] !== 0 && board[i] !== solution[i]) {
                cell.classList.add('error');
            }
            
            if (selectedCell !== null && board[i] !== 0 && board[i] === board[selectedCell]) {
                cell.classList.add('same');
            }
            
            cell.onclick = () => selectCell(i);
            
            container.appendChild(cell);
        }
    }
    
    function selectCell(index) {
        if (solution[index] === 0) return; // 고정 셀 선택 불가
        
        if (!gameStarted) {
            gameStarted = true;
            timerInterval = setInterval(updateTimer, 1000);
        }
        
        selectedCell = index;
        renderBoard();
    }
    
    function inputNumber(num) {
        if (selectedCell === null) return;
        if (solution[selectedCell] === 0) return;
        
        // 히스토리 저장
        history.push({ cell: selectedCell, prevValue: board[selectedCell], prevNotes: new Set(notes[selectedCell]) });
        
        if (noteMode) {
            // 메모 모드
            if (notes[selectedCell].has(num)) {
                notes[selectedCell].delete(num);
            } else {
                notes[selectedCell].add(num);
            }
        } else {
            // 일반 입력
            board[selectedCell] = board[selectedCell] === num ? 0 : num;
            if (board[selectedCell] !== 0) {
                notes[selectedCell].clear();
            }
        }
        
        document.getElementById('remaining').textContent = countRemaining();
        renderBoard();
        checkWin();
    }
    
    function toggleNote() {
        noteMode = !noteMode;
    }
    
    function undoMove() {
        if (history.length === 0) return;
        const last = history.pop();
        board[last.cell] = last.prevValue;
        notes[last.cell] = new Set(last.prevNotes);
        document.getElementById('remaining').textContent = countRemaining();
        renderBoard();
    }
    
    function changeLevel() {
        const levels = ['easy', 'medium', 'hard'];
        const idx = levels.indexOf(currentLevel);
        currentLevel = levels[(idx + 1) % 3];
        document.getElementById('level').textContent = difficulties[currentLevel].name;
        initGame();
    }
    
    function updateTimer() {
        seconds++;
        const m = Math.floor(seconds / 60);
        const s = seconds % 60;
        document.getElementById('timer').textContent = m + ':' + s.toString().padStart(2, '0');
    }
    
    function countRemaining() {
        return board.filter(x => x === 0).length;
    }
    
    function checkWin() {
        const remaining = countRemaining();
        document.getElementById('remaining').textContent = remaining;
        
        if (remaining === 0) {
            clearInterval(timerInterval);
            
            // 오류 검사
            let hasError = false;
            for (let i = 0; i < 81; i++) {
                if (board[i] !== 0 && board[i] !== solution[i]) {
                    hasError = true;
                    break;
                }
            }
            
            if (!hasError) {
                const m = Math.floor(seconds / 60);
                const s = seconds % 60;
                document.getElementById('messageText').innerHTML = '🎉 클리어!<br><br>시간: ' + m + ':' + s.toString().padStart(2, '0');
                document.getElementById('gameMessage').classList.add('show');
            }
        }
    }
    
    // 키보드 지원
    document.addEventListener('keydown', (e) => {
        if (e.key >= '1' && e.key <= '9') {
            inputNumber(parseInt(e.key));
        } else if (e.key === 'Backspace' || e.key === 'Delete') {
            if (selectedCell !== null && history.length > 0) {
                undoMove();
            }
        } else if (e.key === 'n' || e.key === 'N') {
            toggleNote();
        }
    });
    
    initGame();
    </script>
</body>
</html>
