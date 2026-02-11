<?php
/**
 * 공통 헤더
 * 모든 페이지에서 include하여 사용
 */

// 경로 결정
$headerPath = IS_ROOT ? '' : '../';
$currentPage = basename($_SERVER['PHP_SELF']);

// 현재 페이지가 게임 페이지인 경우 (games 폴더 내)
$isGamePage = strpos($_SERVER['PHP_SELF'], '/games/') !== false;
?>
<header>
    <div class="header-content">
        <a href="<?= $headerPath ?>index.php" class="logo">🎮 <?= SITE_NAME ?></a>
        <nav>
            <a href="<?= $headerPath ?>index.php" <?= !$isGamePage && $currentPage === 'index.php' ? 'class="active"' : '' ?>>미니게임</a>
            <a href="<?= $headerPath ?>blog/">블로그</a>
        </nav>
    </div>
</header>
