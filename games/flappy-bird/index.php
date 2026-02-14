<?php
require_once '../../config.php';
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <title>Flappy Bird - <?= SITE_NAME ?></title>
    <link rel="stylesheet" href="../../css/style.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body { height: 100%; overflow: hidden; }
        body { 
            background: #70c5ce;
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
        
        #game-area {
            flex: 1;
            position: relative;
            background: linear-gradient(to bottom, #70c5ce 0%, #70c5ce 75%, #ded895 75%, #ded895 100%);
            overflow: hidden;
            cursor: pointer;
        }
        
        #bird {
            position: absolute;
            width: 35px;
            height: 35px;
            line-height: 35px;
            text-align: center;
            font-size: 30px;
            z-index: 10;
            transform-origin: center;
        }
        
        .pipe {
            position: absolute;
            width: 55px;
            z-index: 5;
        }
        .pipe-top {
            background: linear-gradient(to right, #73bf2e 0%, #9ace5c 100%);
            border-radius: 5px 5px 0 0;
        }
        .pipe-bottom {
            background: linear-gradient(to right, #73bf2e 0%, #9ace5c 100%);
            border-radius: 0 0 5px 5px;
        }
        
        .score {
            position: absolute;
            top: 15%;
            left: 50%;
            transform: translateX(-50%);
            font-size: 55px;
            font-weight: bold;
            color: #fff;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
            z-index: 20;
        }
        
        .game-message {
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
        .game-message.show { display: block; }
        
        .cloud {
            position: absolute;
            font-size: 45px;
            opacity: 0.8;
            z-index: 1;
        }
        
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
    
    <div id="game-area">
        <div id="bird">🐦</div>
        <div class="score" id="score">0</div>
    </div>
    
    <div class="game-message" id="gameMessage">
        <div id="messageText"></div>
        <button class="btn" onclick="startGame()" style="margin-top:15px;">재시작</button>
    </div>
    
    <script>
    // ========== 효과음 시스템 ==========
    const SoundSystem = {
        ctx: null,
        enabled: false,
        
        init: function() {
            try {
                this.ctx = new (window.AudioContext || window.webkitAudioContext)();
                this.enabled = true;
                console.log('🔊 오디오 초기화 완료');
            } catch(e) {
                console.log('❌ 오디오 지원 안됨:', e);
            }
        },
        
        play: function(type) {
            if (!this.enabled || !this.ctx) return;
            
            const ctx = this.ctx;
            const now = ctx.currentTime;
            
            // 오디오 컨텍스트가 suspended면 resume
            if (ctx.state === 'suspended') {
                ctx.resume().then(() => this._playSound(type));
                return;
            }
            
            this._playSound(type);
        },
        
        _playSound: function(type) {
            const ctx = this.ctx;
            const now = ctx.currentTime;
            
            switch(type) {
                case 'jump':
                    // 빠직! - 짧은 오실레이터
                    const osc1 = ctx.createOscillator();
                    const gain1 = ctx.createGain();
                    osc1.connect(gain1);
                    gain1.connect(ctx.destination);
                    osc1.frequency.setValueAtTime(450, now);
                    osc1.frequency.exponentialRampToValueAtTime(650, now + 0.08);
                    gain1.gain.setValueAtTime(0.4, now);
                    gain1.gain.exponentialRampToValueAtTime(0.01, now + 0.1);
                    osc1.start(now);
                    osc1.stop(now + 0.1);
                    break;
                    
                case 'score':
                    // 딩! - 높은 순수음
                    const osc2 = ctx.createOscillator();
                    const gain2 = ctx.createGain();
                    osc2.connect(gain2);
                    gain2.connect(ctx.destination);
                    osc2.type = 'sine';
                    osc2.frequency.setValueAtTime(1000, now);
                    osc2.frequency.setValueAtTime(1500, now + 0.05);
                    gain2.gain.setValueAtTime(0.3, now);
                    gain2.gain.exponentialRampToValueAtTime(0.01, now + 0.15);
                    osc2.start(now);
                    osc2.stop(now + 0.15);
                    break;
                    
                case 'hit':
                    // 꽝! - 낮은 충돌음
                    const osc3 = ctx.createOscillator();
                    const gain3 = ctx.createGain();
                    osc3.connect(gain3);
                    gain3.connect(ctx.destination);
                    osc3.type = 'square';
                    osc3.frequency.setValueAtTime(150, now);
                    osc3.frequency.exponentialRampToValueAtTime(50, now + 0.15);
                    gain3.gain.setValueAtTime(0.3, now);
                    gain3.gain.exponentialRampToValueAtTime(0.01, now + 0.15);
                    osc3.start(now);
                    osc3.stop(now + 0.15);
                    break;
                    
                case 'die':
                    // 투욱... - 죽음음
                    const osc4 = ctx.createOscillator();
                    const gain4 = ctx.createGain();
                    osc4.connect(gain4);
                    gain4.connect(ctx.destination);
                    osc4.type = 'sawtooth';
                    osc4.frequency.setValueAtTime(400, now);
                    osc4.frequency.exponentialRampToValueAtTime(30, now + 0.5);
                    gain4.gain.setValueAtTime(0.25, now);
                    gain4.gain.exponentialRampToValueAtTime(0.01, now + 0.5);
                    osc4.start(now);
                    osc4.stop(now + 0.5);
                    break;
                    
                case 'start':
                    // 삐용삐용! - 시작음
                    const osc5 = ctx.createOscillator();
                    const gain5 = ctx.createGain();
                    osc5.connect(gain5);
                    gain5.connect(ctx.destination);
                    osc5.type = 'sine';
                    osc5.frequency.setValueAtTime(300, now);
                    osc5.frequency.setValueAtTime(500, now + 0.1);
                    osc5.frequency.setValueAtTime(700, now + 0.2);
                    gain5.gain.setValueAtTime(0.25, now);
                    gain5.gain.exponentialRampToValueAtTime(0.01, now + 0.3);
                    osc5.start(now);
                    osc5.stop(now + 0.3);
                    break;
            }
        }
    };
    
    // ========== 게임 ==========
    const area = document.getElementById('game-area');
    const bird = document.getElementById('bird');
    
    let birdX = 80;
    let birdY = 200;
    let birdVY = 0;
    let pipes = [], clouds = [];
    let score = 0, best = 0;
    let running = false, animId = null;
    let gravity = 0.4;
    let jumpForce = -7;
    let pipeSpeed = 3;
    let nextPipeTime = 0;
    
    const birdEmojis = ['🐦', '🐤', '🕊️'];
    let currentBirdIdx = 0;
    
    function init() {
        // 모든 터치/클릭에 오디오 초기화
        const initAudio = () => {
            SoundSystem.init();
            // 오디오 초기화 후 이벤트 리스너 제거
            document.removeEventListener('click', initAudio);
            area.removeEventListener('touchstart', initAudio);
            area.removeEventListener('mousedown', initAudio);
        };
        
        document.addEventListener('click', initAudio);
        area.addEventListener('touchstart', initAudio, { passive: false });
        area.addEventListener('mousedown', initAudio);
        
        area.addEventListener('touchstart', handleJump, { passive: false });
        area.addEventListener('mousedown', handleJump);
        document.addEventListener('keydown', (e) => {
            if (e.code === 'Space' || e.code === 'ArrowUp') {
                e.preventDefault();
                handleJump();
            }
        });
        
        for (let i = 0; i < 4; i++) {
            createCloud(Math.random() * area.clientWidth, Math.random() * 50);
        }
        
        showStartScreen();
        
        best = localStorage.getItem('flappyBest') || 0;
        
        if (localStorage.getItem('darkMode') === '1') {
            document.body.classList.add('dark-mode');
            document.querySelector('header').classList.add('dark');
        }
    }
    
    function handleJump(e) {
        if (e) e.preventDefault();
        
        if (!running) {
            startGame();
        } else {
            birdVY = jumpForce;
            SoundSystem.play('jump');
            if (navigator.vibrate) navigator.vibrate(10);
        }
    }
    
    function showStartScreen() {
        birdX = 80;
        birdY = area.clientHeight * 0.4;
        bird.style.left = birdX + 'px';
        bird.style.top = birdY + 'px';
        bird.style.transform = 'rotate(0deg)';
        
        document.getElementById('messageText').innerHTML = '🐦 날개짓<br><br>화면을 터치하여 시작!<br><br><small>🔊 터치하면 소리 나옵니다</small>';
        document.getElementById('gameMessage').classList.add('show');
    }
    
    function startGame() {
        SoundSystem.init();
        SoundSystem.play('start');
        
        running = true;
        score = 0;
        birdY = area.clientHeight * 0.4;
        birdVY = 0;
        currentBirdIdx = (currentBirdIdx + 1) % birdEmojis.length;
        bird.textContent = birdEmojis[currentBirdIdx];
        
        pipes.forEach(p => { p.topEl.remove(); p.bottomEl.remove(); });
        pipes = [];
        
        document.getElementById('score').textContent = score;
        document.getElementById('gameMessage').classList.remove('show');
        
        nextPipeTime = Date.now() + 1500;
        
        if (animId) cancelAnimationFrame(animId);
        animId = requestAnimationFrame(update);
    }
    
    function createPipe() {
        const gap = 150;
        const areaH = area.clientHeight * 0.75;
        const minPipe = 60;
        
        const topHeight = minPipe + Math.random() * (areaH - gap - minPipe * 2);
        const bottomY = topHeight + gap;
        const bottomH = areaH - bottomY;
        
        const pipeX = area.clientWidth + 20;
        const pipeW = 55;
        
        const topEl = document.createElement('div');
        topEl.className = 'pipe pipe-top';
        topEl.style.height = topHeight + 'px';
        topEl.style.left = pipeX + 'px';
        topEl.style.top = '0';
        area.appendChild(topEl);
        
        const bottomEl = document.createElement('div');
        bottomEl.className = 'pipe pipe-bottom';
        bottomEl.style.height = bottomH + 'px';
        bottomEl.style.left = pipeX + 'px';
        bottomEl.style.top = bottomY + 'px';
        area.appendChild(bottomEl);
        
        pipes.push({ 
            x: pipeX, width: pipeW,
            topHeight: topHeight, bottomY: bottomY,
            topEl: topEl, bottomEl: bottomEl, passed: false 
        });
    }
    
    function createCloud(x, y) {
        const el = document.createElement('div');
        el.className = 'cloud';
        el.textContent = '☁️';
        el.style.left = x + 'px';
        el.style.top = y + '%';
        area.appendChild(el);
        
        clouds.push({ el: el, x: x, speed: 0.3 + Math.random() * 0.3 });
    }
    
    function update() {
        if (!running) return;
        
        const w = area.clientWidth;
        const h = area.clientHeight;
        const groundY = h * 0.75;
        const birdSize = 30;
        
        birdVY += gravity;
        birdY += birdVY;
        
        const rotation = Math.min(Math.max(birdVY * 2.5, -25), 90);
        bird.style.transform = `rotate(${rotation}deg)`;
        bird.style.top = birdY + 'px';
        
        if (birdY < 0 || birdY + birdSize > groundY) {
            SoundSystem.play('die');
            gameOver();
            return;
        }
        
        const now = Date.now();
        if (now > nextPipeTime) {
            createPipe();
            nextPipeTime = now + 1800 - Math.min(score * 15, 700);
        }
        
        clouds.forEach(c => {
            c.x -= c.speed;
            if (c.x < -60) c.x = w + 60;
            c.el.style.left = c.x + 'px';
        });
        
        pipes.forEach(p => {
            p.x -= pipeSpeed;
            p.topEl.style.left = p.x + 'px';
            p.bottomEl.style.left = p.x + 'px';
            
            if (!p.passed && p.x + p.width < birdX) {
                p.passed = true;
                score++;
                document.getElementById('score').textContent = score;
                SoundSystem.play('score');
                if (navigator.vibrate) navigator.vibrate(8);
            }
            
            const birdLeft = birdX + 5;
            const birdRight = birdX + birdSize - 5;
            const birdTop = birdY + 5;
            const birdBottom = birdY + birdSize - 5;
            const pipeLeft = p.x;
            const pipeRight = p.x + p.width;
            
            if (birdRight > pipeLeft && birdLeft < pipeRight) {
                if (birdTop < p.topHeight || birdBottom > p.bottomY) {
                    SoundSystem.play('hit');
                    gameOver();
                    return;
                }
            }
            
            if (p.x < -p.width) {
                p.topEl.remove();
                p.bottomEl.remove();
            }
        });
        
        pipes = pipes.filter(p => p.x > -100);
        animId = requestAnimationFrame(update);
    }
    
    function gameOver() {
        running = false;
        cancelAnimationFrame(animId);
        
        bird.textContent = '💀';
        if (navigator.vibrate) navigator.vibrate([80, 40, 80]);
        
        let msg = '💀 게임 오버!<br>점수: ' + score;
        if (score > best) {
            best = score;
            localStorage.setItem('flappyBest', best);
            msg += '<br>🏆 최고 점수!';
        }
        msg += '<br><br>최고: ' + best;
        
        setTimeout(() => {
            document.getElementById('messageText').innerHTML = msg;
            document.getElementById('gameMessage').classList.add('show');
        }, 400);
    }
    
    init();
    </script>
</body>
</html>
