<?php
/**
 * 블로그 포스트 - 게임 업데이트용 템플릿
 */

require_once '../config.php';
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? '게임 업데이트' ?> - <?= SITE_NAME ?></title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        header { background: #fff; box-shadow: 0 2px 8px rgba(0,0,0,0.08); position: sticky; top: 0; z-index: 100; }
        header.dark { background: #1a1a2e; }
        header.dark .logo { color: #fff !important; }
        header.dark nav a { color: #ccc !important; }
        .header-content { display: flex; justify-content: space-between; align-items: center; padding: 8px 12px; max-width: 1200px; margin: 0 auto; }
        .logo { font-size: 15px; font-weight: bold; color: #4f46e5; }
        nav { display: flex; gap: 10px; }
        nav a { font-size: 12px; color: #666; text-decoration: none; }
        
        body.dark-mode { background: #1a1a2e !important; color: #fff !important; }
        body.dark-mode .post-content { background: rgba(255,255,255,0.1); }
        body.dark-mode .post-title { color: #fff !important; }
        body.dark-mode .post-meta { color: #aaa !important; }
        body.dark-mode .game-item { background: rgba(255,255,255,0.05); }
        body.dark-mode .game-name { color: #fff !important; }
        
        .post-container { max-width: 800px; margin: 0 auto; padding: 40px 20px; }
        .post-title { font-size: 28px; margin-bottom: 10px; color: #333; }
        .post-meta { font-size: 14px; color: #888; margin-bottom: 30px; }
        .post-content { background: #fff; border-radius: 12px; padding: 30px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); }
        .game-list { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin: 20px 0; }
        .game-item { background: #f8f8f8; border-radius: 10px; padding: 20px; text-align: center; }
        .game-icon { font-size: 40px; margin-bottom: 10px; }
        .game-name { font-weight: 600; color: #333; margin-bottom: 5px; }
        .game-desc { font-size: 13px; color: #666; }
        .features { margin: 20px 0; }
        .features li { margin: 10px 0; line-height: 1.8; }
        footer { padding: 20px; text-align: center; font-size: 14px; color: #888; }
        body.dark-mode footer { color: #888 !important; }
    </style>
</head>
<body>
    <header>
        <div class="header-content">
            <a href="../index.php" class="logo">🎮 <?= SITE_NAME ?></a>
            <nav>
                <a href="../index.php">미니게임</a>
                <a href="index.php">블로그</a>
            </nav>
        </div>
    </header>

    <main class="post-container">
        <h1 class="post-title"><?= $title ?? '게임 업데이트' ?></h1>
        <p class="post-meta"><?= $date ?? date('Y년 m월 d일') ?></p>
        
        <div class="post-content">
            <?= $content ?? '' ?>
        </div>
    </main>

    <footer>
        <p>© <?= date('Y') ?> <a href="https://tomseol.pe.kr/" target="_blank">tomseol.pe.kr</a>에서 제작한 <?= SITE_NAME ?></p>
    </footer>
    
    <script>
    if (localStorage.getItem('darkMode') === '1') {
        document.body.classList.add('dark-mode');
        document.querySelector('header').classList.add('dark');
    }
    </script>
</body>
</html>
