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
    <link href="https://fonts.googleapis.com/css2?family=Pretendard:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .post-list { max-width: 800px; margin: 0 auto; padding: 40px 20px; }
        .post-item { background: #fff; border-radius: 12px; padding: 25px; margin-bottom: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); transition: transform 0.3s; }
        .post-item:hover { transform: translateY(-3px); }
        .post-title { font-size: 20px; font-weight: 600; margin-bottom: 10px; }
        .post-date { font-size: 14px; color: #888; margin-bottom: 15px; }
        .post-excerpt { color: #666; line-height: 1.8; }
    </style>
</head>
<body>
    <header>
        <div class="header-content">
            <a href="../index.php" class="logo">🎮 <?= SITE_NAME ?></a>
            <nav>
                <a href="../index.php">미니게임</a>
                <a href="index.php" class="active">블로그</a>
            </nav>
        </div>
    </header>

    <main class="container">
        <div class="post-list">
            <h1 style="margin-bottom: 30px; font-size: 28px;">📝 블로그</h1>
            
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
</body>
</html>
