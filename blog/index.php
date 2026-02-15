<?php
/**
 * 블로그 목록 페이지
 */

require_once '../config.php';
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>블로그 - <?= SITE_NAME ?></title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        header { background: #fff; box-shadow: 0 2px 8px rgba(0,0,0,0.08); position: sticky; top: 0; z-index: 100; transition: background 0.3s, color 0.3s; }
        header.dark { background: #1a1a2e; }
        header.dark .logo { color: #fff !important; }
        header.dark nav a { color: #ccc !important; }
        header.dark nav a.active { color: #4ade80 !important; }
        .header-content { display: flex; justify-content: space-between; align-items: center; padding: 8px 12px; max-width: 1200px; margin: 0 auto; width: 100%; box-sizing: border-box; }
        .logo { font-size: 15px; font-weight: bold; color: #4f46e5; flex: 0 0 auto; }
        nav { display: flex; gap: 10px; flex: 0 0 auto; align-items: center; }
        nav a { font-size: 12px; color: #666; text-decoration: none; padding: 4px 8px; }
        nav a.active { color: #4f46e5; font-weight: 600; }
        .theme-btn { background: none; border: 1px solid #ddd; border-radius: 20px; padding: 6px 12px; cursor: pointer; font-size: 14px; transition: all 0.3s; }
        header.dark .theme-btn { border-color: #444; color: #fff; }
        .theme-btn:hover { background: #f0f0f0; }
        header.dark .theme-btn:hover { background: #333; }
        
        body.dark-mode { background: #1a1a2e !important; color: #fff !important; }
        body.dark-mode .post-item { background: rgba(255,255,255,0.1) !important; color: #fff !important; }
        body.dark-mode .post-title { color: #fff !important; }
        body.dark-mode .post-excerpt { color: #ccc !important; }
        .post-content { display: none; margin-top: 20px; padding-top: 20px; border-top: 1px solid #eee; }
        body.dark-mode .post-content { border-top-color: #444; }
        .post-content.show { display: block; animation: fadeIn 0.3s; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        .post-list { max-width: 800px; margin: 0 auto; padding: 40px 20px; }
        .post-item { background: #fff; border-radius: 12px; padding: 25px; margin-bottom: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); transition: transform 0.3s; cursor: pointer; }
        .post-item:hover { transform: translateY(-3px); }
        .post-title { font-size: 20px; font-weight: 600; margin-bottom: 10px; color: #333; }
        .post-date { font-size: 14px; color: #888; margin-bottom: 15px; }
        .post-excerpt { color: #666; line-height: 1.8; }
        .game-list { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px; margin: 20px 0; }
        .game-item { background: #f8f8f8; border-radius: 10px; padding: 15px; text-align: center; }
        body.dark-mode .game-item { background: rgba(255,255,255,0.05); }
        .game-icon { font-size: 32px; margin-bottom: 8px; }
        .game-name { font-weight: 600; color: #333; margin-bottom: 5px; font-size: 14px; }
        body.dark-mode .game-name { color: #fff; }
        .game-desc { font-size: 12px; color: #666; }
        body.dark-mode .game-desc { color: #aaa; }
        footer { padding: 20px; text-align: center; font-size: 14px; color: #888; }
        footer a { color: #888; }
        body.dark-mode footer, body.dark-mode footer a { color: #888 !important; }
    </style>
</head>
<body>
    <header>
        <div class="header-content">
            <a href="../index.php" class="logo">🎮 <?= SITE_NAME ?></a>
            <nav>
                <a href="../index.php">미니게임</a>
                <a href="index.php" class="active">블로그</a>
                <button class="theme-btn" onclick="toggleTheme()" title="테마 전환">🌙</button>
            </nav>
        </div>
    </header>

    <main class="container">
        <div class="post-list">
            <h1 style="margin-bottom: 30px; font-size: 28px;">📝 블로그</h1>
            
            <!-- 2026년 2월 15일 레이싱 포스트 -->
            <div class="post-item" onclick="togglePost(this)">
                <h2 class="post-title">🏎️ 2026년 2월 15일 레이싱 게임 업데이트</h2>
                <p class="post-date">2026.02.15</p>
                <p class="post-excerpt">새로운 레이싱 게임 Turbo Racing이 추가되었습니다! 빠른 레이싱을 즐겨보세요...</p>
                <div class="post-content">
                    <p>새로운 레이싱 게임이 추가되었습니다! 빠른 레이싱을 즐겨보세요.</p>
                    <h3 style="margin-top:20px;">🆕 새로 추가된 게임</h3>
                    <div class="game-list">
                        <div class="game-item">
                            <div class="game-icon">🏎️</div>
                            <div class="game-name">Turbo Racing</div>
                            <div class="game-desc">고속 레이싱 게임</div>
                        </div>
                    </div>
                    <h3 style="margin-top:20px;">✨ 게임 특징</h3>
                    <ul style="margin:15px 0 0 20px;line-height:1.8;">
                        <li><strong>조작법</strong> - 터치 슬라이드 또는 버튼으로 좌우 이동</li>
                        <li><strong>장애물</strong> - 다양한 차량을 피하세요</li>
                        <li><strong>코인</strong> - 🪙 수집으로 추가 점수</li>
                        <li><strong>레벨</strong> - 점수에 따라 레벨업, 속도 증가</li>
                    </ul>
                    <p style="margin-top:20px;text-align:center;"><a href="../games/turbo-racing/" style="color:#4f46e5;">🎮 게임 하러가기</a></p>
                </div>
            </div>
            
            <!-- 2026년 2월 15일 후반 포스트 -->
            <div class="post-item" onclick="togglePost(this)">
                <h2 class="post-title">🎮 2026년 2월 15일 후반 게임 업데이트</h2>
                <p class="post-date">2026.02.15</p>
                <p class="post-excerpt">새로운 게임 Cut the Rope가 추가되었습니다! 물리 퍼즐을 경험해보세요...</p>
                <div class="post-content">
                    <p>오늘도 새로운 게임들이 추가되었습니다! 물리 퍼즐 게임부터 다양한 업데이트를 확인하세요.</p>
                    <h3 style="margin-top:20px;">🆕 새로 추가된 게임</h3>
                    <div class="game-list">
                        <div class="game-item">
                            <div class="game-icon">✂️</div>
                            <div class="game-name">Cut the Rope</div>
                            <div class="game-desc">밧줄 자르기 물리 퍼즐</div>
                        </div>
                    </div>
                    <h3 style="margin-top:20px;">🔧 업데이트된 게임</h3>
                    <div class="game-list">
                        <div class="game-item">
                            <div class="game-icon">🍬</div>
                            <div class="game-name">Candy Crush</div>
                            <div class="game-desc">사탕 매칭</div>
                        </div>
                    </div>
                    <h3 style="margin-top:20px;">✨ 주요 업데이트 내용</h3>
                    <ul style="margin:15px 0 0 20px;line-height:1.8;">
                        <li><strong>Cut the Rope</strong> - 새로운 물리 퍼즐 게임! 로프를 끊어서 사탕을 오뇽에게 먹이세요. 5개의 레벨이 준비되어 있습니다.</li>
                        <li><strong>Candy Crush</strong> - 교환 및 매칭 로직 개선으로 더 부드럽게 플레이할 수 있습니다.</li>
                    </ul>
                    <p style="margin-top:20px;text-align:center;"><a href="../index.php" style="color:#4f46e5;">🎮 게임 하러가기</a></p>
                </div>
            </div>
            
            <!-- 2026년 2월 15일 전반 포스트 -->
            <div class="post-item" onclick="togglePost(this)">
                <h2 class="post-title">🎮 2026년 2월 15일 게임 업데이트</h2>
                <p class="post-date">2026.02.15</p>
                <p class="post-excerpt">새로운 게임들이 추가되었습니다! Sudoku, Candy Crush가 새롭게 출시되었으며...</p>
                <div class="post-content">
                    <p>새로운 게임들이 추가되었습니다! 퍼즐부터 레이싱까지 다양한 게임을 즐겨보세요.</p>
                    <h3 style="margin-top:20px;">🆕 새로 추가된 게임</h3>
                    <div class="game-list">
                        <div class="game-item">
                            <div class="game-icon">🧩</div>
                            <div class="game-name">Sudoku</div>
                            <div class="game-desc">숫자 퍼즐</div>
                        </div>
                        <div class="game-item">
                            <div class="game-icon">🍬</div>
                            <div class="game-name">Candy Crush</div>
                            <div class="game-desc">사탕 매칭</div>
                        </div>
                    </div>
                    <h3 style="margin-top:20px;">🔧 업데이트된 게임</h3>
                    <div class="game-list">
                        <div class="game-item">
                            <div class="game-icon">🐍</div>
                            <div class="game-name">Snake</div>
                            <div class="game-desc">뱀 게임</div>
                        </div>
                        <div class="game-item">
                            <div class="game-icon">🏓</div>
                            <div class="game-name">Pong</div>
                            <div class="game-desc">퐁 게임</div>
                        </div>
                        <div class="game-item">
                            <div class="game-icon">🃏</div>
                            <div class="game-name">Solitaire</div>
                            <div class="game-desc">솔리테어</div>
                        </div>
                        <div class="game-item">
                            <div class="game-icon">🐦</div>
                            <div class="game-name">Flappy Bird</div>
                            <div class="game-desc">날개 달린 새</div>
                        </div>
                    </div>
                    <p style="margin-top:20px;text-align:center;"><a href="../index.php" style="color:#4f46e5;">🎮 게임 하러가기</a></p>
                </div>
            </div>
            
            <!-- 예시 포스트 -->
            <div class="post-item" onclick="togglePost(this)">
                <h2 class="post-title">🎮 미니게임포털 오픈!</h2>
                <p class="post-date">2026.02.10</p>
                <p class="post-excerpt">드디어 미니게임포털을 오픈합니다! 30개 이상의 다양한 미니게임을 즐기실 수 있습니다...</p>
                <div class="post-content">
                    <p>드디어 미니게임포털을 오픈합니다!</p>
                    <p style="margin-top:15px;">현재 15개 이상의 게임이 준비되어 있으며, 매일 새로운 게임이 추가됩니다.</p>
                    <h3 style="margin-top:20px;">🎮 제공 게임</h3>
                    <ul style="margin:15px 0 0 20px;line-height:1.8;">
                        <li>퍼즐 게임 (2048, Tetris, Sudoku 등)</li>
                        <li>레이싱 게임 (Turbo Racing)</li>
                        <li>아케이드 게임 (Flappy Bird, Snake 등)</li>
                        <li>카드 게임 (Solitaire)</li>
                    </ul>
                    <p style="margin-top:20px;text-align:center;"><a href="../index.php" style="color:#4f46e5;">🎮 게임 하러가기</a></p>
                </div>
            </div>
        </div>
    </main>

    <footer>
        <p>© <?= date('Y') ?> <a href="https://tomseol.pe.kr/" target="_blank">tomseol.pe.kr</a>에서 제작한 <?= SITE_NAME ?></p>
    </footer>
    <script>
    function toggleTheme() {
        const isDark = document.body.classList.contains('dark-mode');
        document.body.classList.toggle('dark-mode');
        document.querySelector('header').classList.toggle('dark');
        localStorage.setItem('darkMode', isDark ? '0' : '1');
    }
    function togglePost(el) {
        el.querySelector('.post-content').classList.toggle('show');
    }
    if (localStorage.getItem('darkMode') === '1') {
        document.body.classList.add('dark-mode');
        document.querySelector('header').classList.add('dark');
    }
    </script>
</body>
</html>
