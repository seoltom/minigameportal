<?php
/**
 * 공통 헤더
 * 모든 페이지에서 include하여 사용
 */

// 현재 스크립트의 디렉토리 경로
$currentDir = dirname(__FILE__);

// 설정 파일 경로 결정 (루트에 있는 config.php 사용)
$configPath = $currentDir . '/config.php';
if (file_exists($configPath)) {
    require_once $configPath;
}

// 헤더 경로 결정
// 현재 파일이 루트에 있으면 '', 게임 폴더에 있으면 '../'
$isRoot = ($currentDir === dirname($_SERVER['DOCUMENT_ROOT'] . $_SERVER['PHP_SELF'])) || 
          (strpos($_SERVER['PHP_SELF'], '/games/') === false);
$headerPath = $isRoot ? '' : '../';
?>
<header>
    <div class="header-content">
        <a href="<?= $headerPath ?>index.php" class="logo">🎮 <?= SITE_NAME ?></a>
        <nav>
            <a href="<?= $headerPath ?>index.php" <?= $isRoot ? 'class="active"' : '' ?>>미니게임</a>
            <a href="<?= $headerPath ?>blog/">블로그</a>
        </nav>
    </div>
</header>
