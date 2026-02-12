<?php
/**
 * 공통 헤더
 * 모든 페이지에서 include하여 사용
 */

// 설정 파일 경로 결정
$basePath = dirname(__FILE__);
require_once $basePath . '/config.php';

// 현재 스크립트 경로에서 게임 페이지인지 확인
$scriptPath = $_SERVER['PHP_SELF'];
$isGamePage = (strpos($scriptPath, '/games/') !== false);

// 절대 경로로 링크 설정
$homeUrl = 'http://tomseol.pe.kr/';
$blogUrl = 'http://tomseol.pe.kr/blog/';
?>
<style>
header {
    background: #fff;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    position: sticky;
    top: 0;
    z-index: 100;
}
.header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 20px;
    max-width: 1200px;
    margin: 0 auto;
}
.logo {
    font-size: 18px;
    font-weight: bold;
    color: #4f46e5;
    white-space: nowrap;
}
nav {
    display: flex;
    gap: 15px;
}
nav a {
    font-size: 14px;
    color: #666;
    text-decoration: none;
    white-space: nowrap;
}
nav a.active {
    color: #4f46e5;
    font-weight: 600;
}
</style>
<header>
    <div class="header-content">
        <a href="<?= $homeUrl ?>" class="logo">🎮 <?= SITE_NAME ?></a>
        <nav>
            <a href="<?= $homeUrl ?>" <?= !$isGamePage ? 'class="active"' : '' ?>>미니게임</a>
            <a href="<?= $blogUrl ?>">블로그</a>
        </nav>
    </div>
</header>
