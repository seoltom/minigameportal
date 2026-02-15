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
        .post-list { max-width: 800px; margin: 0 auto; padding: 40px 20px; }
        .post-item { background: #fff; border-radius: 12px; padding: 25px; margin-bottom: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); transition: transform 0.3s; }
        .post-item:hover { transform: translateY(-3px); }
        .post-title { font-size: 20px; font-weight: 600; margin-bottom: 10px; color: #333; }
        .post-date { font-size: 14px; color: #888; margin-bottom: 15px; }
        .post-excerpt { color: #666; line-height: 1.8; }
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
            <div class="post-item">
                <a href="post-20260215c.php">
                    <h2 class="post-title">🏎️ 2026년 2월 15일 레이싱 게임 업데이트</h2>
                    <p class="post-date">2026.02.15</p>
                    <p class="post-excerpt">새로운 레이싱 게임 Turbo Racing이 추가되었습니다! 빠른 레이싱을 즐겨보세요...</p>
                </a>
            </div>
            
            <!-- 2026년 2월 15일 후반 포스트 -->
            <div class="post-item">
                <a href="post-20260215b.php">
                    <h2 class="post-title">🎮 2026년 2월 15일 후반 게임 업데이트</h2>
                    <p class="post-date">2026.02.15</p>
                    <p class="post-excerpt">새로운 게임 Cut the Rope가 추가되었습니다! 물리 퍼즐을 경험해보세요...</p>
                </a>
            </div>
            
            <!-- 2026년 2월 15일 전반 포스트 -->
            <div class="post-item">
                <a href="post-20260215.php">
                    <h2 class="post-title">🎮 2026년 2월 15일 게임 업데이트</h2>
                    <p class="post-date">2026.02.15</p>
                    <p class="post-excerpt">새로운 게임들이 추가되었습니다! Sudoku, Candy Crush가 새롭게 출시되었며...</p>
                </a>
            </div>
            
            <!-- 예시 포스트 -->
            <div class="post-item">
                <a href="post.php?id=1">
                    <h2 class="post-title">미니게임포털 오픈!</h2>
                    <p class="post-date">2026.02.10</p>
                    <p class="post-excerpt">드디어 미니게임포털을 오픈합니다! 30개 이상의 다양한 미니게임을 즐기실 수 있습니다...</p>
                </a>
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
    if (localStorage.getItem('darkMode') === '1') {
        document.body.classList.add('dark-mode');
        document.querySelector('header').classList.add('dark');
    }
    </script>
</body>
</html>
