<?php
require_once '../../config.php';
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <title>Cut the Rope - <?= SITE_NAME ?></title>
    <link rel="stylesheet" href="../../css/style.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body { height: 100%; overflow: hidden; }
        body { 
            background: linear-gradient(to bottom, #87CEEB 0%, #87CEEB 60%, #8B4513 60%, #A0522D 100%);
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
        
        .info {
            display: flex;
            gap: 10px;
            padding: 8px 12px;
            justify-content: center;
            background: rgba(0,0,0,0.2);
        }
        .info-box {
            background: rgba(255,255,255,0.2);
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
            overflow: hidden;
            cursor: crosshair;
        }
        
        #candy {
            position: absolute;
            width: 50px;
            height: 50px;
            font-size: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10;
        }
        
        .rope {
            position: absolute;
            width: 3px;
            background: #8B4513;
            transform-origin: top center;
            z-index: 5;
        }
        
        .om-nom {
            position: absolute;
            width: 60px;
            height: 60px;
            font-size: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 8;
        }
        
        .star {
            position: absolute;
            width: 35px;
            height: 35px;
            font-size: 30px;
            z-index: 6;
            animation: twinkle 1s infinite alternate;
        }
        @keyframes twinkle {
            from { transform: scale(1); }
            to { transform: scale(1.2); }
        }
        
        .star.collected {
            animation: collect 0.3s ease-out forwards;
        }
        @keyframes collect {
            to { transform: scale(1.5); opacity: 0; }
        }
        
        .level-complete {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(0,0,0,0.85);
            color: #fff;
            padding: 30px;
            border-radius: 15px;
            font-size: 20px;
            text-align: center;
            z-index: 2000;
            display: none;
        }
        .level-complete.show { display: block; }
        
        .game-over {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(0,0,0,0.85);
            color: #fff;
            padding: 30px;
            border-radius: 15px;
            font-size: 20px;
            text-align: center;
            z-index: 2000;
            display: none;
        }
        .game-over.show { display: block; }
        
        body.dark-mode { background: #1a1a2e !important; }
        body.dark-mode header { background: #1a1a2e; }
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
    
    <div class="info">
        <div class="info-box">레벨: <span class="info-value" id="level">1</span></div>
        <div class="info-box">별: <span class="info-value" id="stars">0</span>/3</div>
    </div>
    
    <div id="game-area">
        <div id="candy">🍬</div>
        <div class="om-nom" id="omnom">👾</div>
    </div>
    
    <div class="level-complete" id="levelComplete">
        <div id="levelMsg"></div>
        <button class="btn" onclick="nextLevel()" style="margin-top:15px;">다음 레벨</button>
    </div>
    
    <div class="game-over" id="gameOver">
        <div id="gameOverMsg"></div>
        <button class="btn" onclick="restartLevel()" style="margin-top:15px;">재시도</button>
    </div>
    
    <script>
    const gameArea = document.getElementById('game-area');
    const candy = document.getElementById('candy');
    const omnom = document.getElementById('omnom');
    
    let ropes = [];
    let stars = [];
    
    // 물리 변수
    let candyX = 0, candyY = 0;
    let velocityX = 0, velocityY = 0;
    let gravity = 0.3;
    let damping = 0.99;
    let isSwinging = false;
    
    // 게임 상태
    let level = 1;
    let starsCollected = 0;
    let gameRunning = false;
    let animationId = null;
    
    const LEVELS = [
        // 레벨 1: 간단한 1개 로프
        {
            ropes: [{ x: 150, y: 10, length: 150 }],
            stars: [{ x: 200, y: 250 }],
            omnom: { x: 250, y: 400 },
            candy: { x: 150, y: 160 }
        },
        // 레벨 2: 2개 로프
        {
            ropes: [
                { x: 100, y: 10, length: 120 },
                { x: 200, y: 10, length: 120 }
            ],
            stars: [
                { x: 150, y: 200 },
                { x: 250, y: 280 }
            ],
            omnom: { x: 150, y: 400 },
            candy: { x: 150, y: 130 }
        },
        // 레벨 3: 3개 로프 + 별 3개
        {
            ropes: [
                { x: 80, y: 10, length: 100 },
                { x: 150, y: 10, length: 130 },
                { x: 220, y: 10, length: 100 }
            ],
            stars: [
                { x: 100, y: 200 },
                { x: 150, y: 280 },
                { x: 200, y: 200 }
            ],
            omnom: { x: 150, y: 400 },
            candy: { x: 150, y: 140 }
        },
        // 레벨 4: 대각선 로프
        {
            ropes: [
                { x: 50, y: 10, length: 180 },
                { x: 200, y: 10, length: 180 }
            ],
            stars: [
                { x: 100, y: 300 },
                { x: 200, y: 250 },
                { x: 150, y: 350 }
            ],
            omnom: { x: 280, y: 380 },
            candy: { x: 125, y: 190 }
        },
        // 레벨 5: 복잡한 구조
        {
            ropes: [
                { x: 100, y: 10, length: 100 },
                { x: 175, y: 10, length: 150 },
                { x: 250, y: 10, length: 100 }
            ],
            stars: [
                { x: 80, y: 250 },
                { x: 175, y: 200 },
                { x: 270, y: 250 }
            ],
            omnom: { x: 175, y: 400 },
            candy: { x: 175, y: 160 }
        }
    ];
    
    function initGame() {
        level = 1;
        loadLevel(level);
        
        // 터치/클릭으로 로프 자르기
        gameArea.addEventListener('touchstart', cutRope, { passive: false });
        gameArea.addEventListener('mousedown', cutRope);
        
        if (localStorage.getItem('darkMode') === '1') {
            document.body.classList.add('dark-mode');
        }
    }
    
    function loadLevel(lvl) {
        const levelData = LEVELS[(lvl - 1) % LEVELS.length];
        const w = gameArea.clientWidth;
        const h = gameArea.clientHeight;
        
        // 위치 조정 (화면 중앙)
        const offsetX = (w - 320) / 2;
        const offsetY = 20;
        
        // 로프 생성
        ropes = levelData.ropes.map(r => ({
            x: r.x + offsetX,
            y: r.y + offsetY,
            length: r.length,
            active: true
        }));
        
        // 별 생성
        stars = levelData.stars.map(s => ({
            x: s.x + offsetX,
            y: s.y + offsetY,
            collected: false
        }));
        
        // 오옴 위치
        omnom.style.left = (levelData.omnom.x + offsetX) + 'px';
        omnom.style.top = (levelData.omnom.y + offsetY) + 'px';
        
        // 사탕 초기 위치
        candyX = levelData.candy.x + offsetX;
        candyY = levelData.candy.y + offsetY;
        
        velocityX = 0;
        velocityY = 0;
        starsCollected = 0;
        gameRunning = true;
        
        document.getElementById('level').textContent = lvl;
        document.getElementById('stars').textContent = '0';
        
        document.getElementById('levelComplete').classList.remove('show');
        document.getElementById('gameOver').classList.remove('show');
        
        renderRopes();
        renderStars();
        updateCandyPosition();
        
        if (animationId) cancelAnimationFrame(animationId);
        gameLoop();
    }
    
    function renderRopes() {
        // 기존 로프 제거
        document.querySelectorAll('.rope').forEach(el => el.remove());
        
        ropes.forEach(rope => {
            if (!rope.active) return;
            
            const el = document.createElement('div');
            el.className = 'rope';
            el.dataset.x = rope.x;
            el.dataset.y = rope.y;
            el.dataset.length = rope.length;
            
            // 로프 각도 계산
            const dx = candyX - rope.x;
            const dy = candyY - rope.y;
            const length = Math.sqrt(dx * dx + dy * dy);
            const angle = Math.atan2(dy, dx) * 180 / Math.PI;
            
            el.style.left = rope.x + 'px';
            el.style.top = rope.y + 'px';
            el.style.height = length + 'px';
            el.style.transform = `rotate(${angle}deg)`;
            
            gameArea.appendChild(el);
        });
    }
    
    function renderStars() {
        document.querySelectorAll('.star').forEach(el => el.remove());
        
        stars.forEach((star, idx) => {
            if (star.collected) return;
            
            const el = document.createElement('div');
            el.className = 'star';
            el.textContent = '⭐';
            el.style.left = star.x + 'px';
            el.style.top = star.y + 'px';
            el.dataset.idx = idx;
            
            gameArea.appendChild(el);
        });
    }
    
    function cutRope(e) {
        if (!gameRunning) return;
        e.preventDefault();
        
        const rect = gameArea.getBoundingClientRect();
        const touchX = e.touches ? e.touches[0].clientX : e.clientX;
        const touchY = e.touches ? e.touches[0].clientY : e.clientY;
        
        const x = touchX - rect.left;
        const y = touchY - rect.top;
        
        // 로프 체크 (터치 위치에서 가까운 로프)
        ropes.forEach(rope => {
            if (!rope.active) return;
            
            // 로프와 터치 위치 거리
            const ropeStartX = rope.x;
            const ropeStartY = rope.y;
            const ropeEndX = candyX;
            const ropeEndY = candyY;
            
            // 선분과 점 사이 거리
            const dist = pointToLineDistance(x, y, ropeStartX, ropeStartY, ropeEndX, ropeEndY);
            
            if (dist < 20) {
                rope.active = false;
                renderRopes();
                if (navigator.vibrate) navigator.vibrate(20);
            }
        });
    }
    
    function pointToLineDistance(px, py, x1, y1, x2, y2) {
        const A = px - x1;
        const B = py - y1;
        const C = x2 - x1;
        const D = y2 - y1;
        
        const dot = A * C + B * D;
        const lenSq = C * C + D * D;
        let param = -1;
        
        if (lenSq !== 0) param = dot / lenSq;
        
        let xx, yy;
        
        if (param < 0) {
            xx = x1;
            yy = y1;
        } else if (param > 1) {
            xx = x2;
            yy = y2;
        } else {
            xx = x1 + param * C;
            yy = y1 + param * D;
        }
        
        const dx = px - xx;
        const dy = py - yy;
        
        return Math.sqrt(dx * dx + dy * dy);
    }
    
    function updateCandyPosition() {
        candy.style.left = (candyX - 25) + 'px';
        candy.style.top = (candyY - 25) + 'px';
    }
    
    function gameLoop() {
        if (!gameRunning) return;
        
        const w = gameArea.clientWidth;
        const h = gameArea.clientHeight;
        
        // 활성 로프 수
        const activeRopes = ropes.filter(r => r.active);
        
        if (activeRopes.length > 0) {
            // 로프에 의해 흔들림
            isSwinging = true;
            
            // 각 로프에서 당기는 힘 계산
            let fx = 0, fy = 0;
            
            activeRopes.forEach(rope => {
                const dx = candyX - rope.x;
                const dy = candyY - rope.y;
                const dist = Math.sqrt(dx * dx + dy * dy);
                const stretch = dist - rope.length;
                
                // 탄성 힘
                const k = 0.1; // 탄성 계수
                fx -= k * stretch * (dx / dist);
                fy -= k * stretch * (dy / dist);
            });
            
            velocityX += fx;
            velocityY += gravity + fy;
            
        } else {
            // 자유낙하
            isSwinging = false;
            velocityY += gravity;
        }
        
        // 속도 감쇠
        velocityX *= damping;
        velocityY *= damping;
        
        // 위치 업데이트
        candyX += velocityX;
        candyY += velocityY;
        
        // 벽 충돌
        if (candyX < 25) {
            candyX = 25;
            velocityX *= -0.7;
        }
        if (candyX > w - 25) {
            candyX = w - 25;
            velocityX *= -0.7;
        }
        
        // 바닥/천장 충돌
        if (candyY > h - 25) {
            // 게임 오버
            gameOver();
            return;
        }
        if (candyY < 25) {
            candyY = 25;
            velocityY *= -0.5;
        }
        
        // 별 수집 체크
        stars.forEach((star, idx) => {
            if (star.collected) return;
            
            const dx = candyX - (star.x + 17);
            const dy = candyY - (star.y + 17);
            const dist = Math.sqrt(dx * dx + dy * dy);
            
            if (dist < 35) {
                star.collected = true;
                starsCollected++;
                document.getElementById('stars').textContent = starsCollected;
                
                // 별 애니메이션
                const starEl = document.querySelector(`.star[data-idx="${idx}"]`);
                if (starEl) {
                    starEl.classList.add('collected');
                    setTimeout(() => starEl.remove(), 300);
                }
                
                if (navigator.vibrate) navigator.vibrate(15);
            }
        });
        
        // 오옴 충돌 (게임 클리어)
        const omnomRect = omnom.getBoundingClientRect();
        const gameRect = gameArea.getBoundingClientRect();
        const omX = omnomRect.left - gameRect.left + 30;
        const omY = omnomRect.top - gameRect.top + 30;
        
        const dx = candyX - omX;
        const dy = candyY - omY;
        const dist = Math.sqrt(dx * dx + dy * dy);
        
        if (dist < 40) {
            levelComplete();
            return;
        }
        
        // 로프 렌더링
        if (isSwinging) {
            renderRopes();
        }
        
        updateCandyPosition();
        
        animationId = requestAnimationFrame(gameLoop);
    }
    
    function levelComplete() {
        gameRunning = false;
        cancelAnimationFrame(animationId);
        
        omnom.textContent = '😋';
        if (navigator.vibrate) navigator.vibrate([50, 30, 50]);
        
        const msg = `🎉 레벨 ${level} 클리어!<br>별: ${starsCollected}/3`;
        
        document.getElementById('levelMsg').innerHTML = msg;
        document.getElementById('levelComplete').classList.add('show');
    }
    
    function nextLevel() {
        level++;
        omnom.textContent = '👾';
        loadLevel(level);
    }
    
    function gameOver() {
        gameRunning = false;
        cancelAnimationFrame(animationId);
        
        candy.textContent = '💫';
        if (navigator.vibrate) navigator.vibrate(100);
        
        document.getElementById('gameOverMsg').innerHTML = `💀 레벨 ${level} 실패!<br>사탕이 떨어졌습니다.`;
        document.getElementById('gameOver').classList.add('show');
    }
    
    function restartLevel() {
        candy.textContent = '🍬';
        omnom.textContent = '👾';
        loadLevel(level);
    }
    
    initGame();
    </script>
</body>
</html>
