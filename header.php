<?php
/**
 * 공통 헤더
 * 모든 페이지에서 include하여 사용
 */

// 설정 파일 경로 결정
$basePath = dirname(__FILE__); // header.php가 있는 디렉토리
require_once $basePath . '/config.php';

// 현재 스크립트 경로에서 루트에서 호출인지 게임 페이지에서 호출인지 확인
$scriptPath = $_SERVER['PHP_SELF'];

// games 폴더 내에 있으면 ../index.php, 루트에 있으면 index.php
$isGamePage = (strpos($scriptPath, '/games/') !== false);
?>
<header>
    <div class="header-content">
        <a href="<?= $isGamePage ? '../index.php' : 'index.php' ?>" class="logo">🎮 <?= SITE_NAME ?></a>
        <nav>
            <a href="<?= $isGamePage ? '../index.php' : 'index.php' ?>" <?= !$isGamePage ? 'class="active"' : '' ?>>미니게임</a>
            <a href="<?= $isGamePage ? '../blog/' : 'blog/' ?>">블로그</a>
        </nav>
    </div>
</header>
