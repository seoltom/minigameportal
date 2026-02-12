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
    transition: background 0.3s, color 0.3s;
}
header.dark {
    background: #1a1a2e;
}
header.dark .logo {
    color: #fff;
}
header.dark nav a {
    color: #ccc;
}
header.dark nav a.active {
    color: #4ade80;
}
.header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 12px;
    max-width: 1200px;
    margin: 0 auto;
    width: 100%;
    box-sizing: border-box;
}
.logo {
    font-size: 15px;
    font-weight: bold;
    color: #4f46e5;
    flex: 0 0 auto;
}
nav {
    display: flex;
    gap: 10px;
    flex: 0 0 auto;
    align-items: center;
}
nav a {
    font-size: 12px;
    color: #666;
    text-decoration: none;
    padding: 4px 8px;
}
nav a.active {
    color: #4f46e5;
    font-weight: 600;
}
.theme-btn {
    background: none;
    border: 1px solid #ddd;
    border-radius: 20px;
    padding: 6px 12px;
    cursor: pointer;
    font-size: 14px;
    transition: all 0.3s;
}
header.dark .theme-btn {
    border-color: #444;
    color: #fff;
}
.theme-btn:hover {
    background: #f0f0f0;
}
header.dark .theme-btn:hover {
    background: #333;
}
</style>

<script>
function setTheme(dark) {
    document.querySelectorAll('header').forEach(h => {
        h.classList.toggle('dark', dark);
    });
    localStorage.setItem('darkMode', dark ? '1' : '0');
}

function toggleTheme() {
    const isDark = localStorage.getItem('darkMode') === '1';
    setTheme(!isDark);
}

// Load saved theme
if (localStorage.getItem('darkMode') === '1') {
    setTheme(true);
}
</script>

<header>
    <div class="header-content">
        <a href="<?= $homeUrl ?>" class="logo">🎮 <?= SITE_NAME ?></a>
        <nav>
            <a href="<?= $homeUrl ?>" <?= !$isGamePage ? 'class="active"' : '' ?>>미니게임</a>
            <a href="<?= $blogUrl ?>">블로그</a>
            <button class="theme-btn" onclick="toggleTheme()" title="테마 전환">🌙</button>
        </nav>
    </div>
</header>
