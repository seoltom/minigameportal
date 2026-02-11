<?php
/**
 * 공통 헤더
 * 모든 페이지에서 include하여 사용
 */

// 설정 파일 경로 결정 (루트에 있는 config.php 사용)
$configPath = dirname(__FILE__) . '/config.php';
if (file_exists($configPath)) {
    require_once $configPath;
}

// 현재 PHP 파일의 경로에서 games 폴더가 포함되어 있는지 확인
$currentFile = $_SERVER['PHP_SELF'];
$isGamePage = (strpos($currentFile, '/games/') !== false);
$headerPath = $isGamePage ? '../' : '';
?>
<header>
    <div class="header-content">
        <a href="<?= $headerPath ?>index.php" class="logo">🎮 <?= SITE_NAME ?></a>
        <nav>
            <a href="<?= $headerPath ?>index.php" <?= !$isGamePage ? 'class="active"' : '' ?>>미니게임</a>
            <a href="<?= $headerPath ?>blog/">블로그</a>
        </nav>
    </div>
</header>
