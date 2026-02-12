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

// 게임 이름 추출 (있는 경우)
$gameName = '';
if ($isGamePage) {
    $pathParts = explode('/', dirname($scriptPath));
    $gameFolder = end($pathParts);
    // 폴더 이름을 게임 이름으로 변환
    $gameName = ucwords(str_replace('-', ' ', $gameFolder));
    // 특수 이름 매핑
    $gameNameMap = [
        '2048' => '2048',
        'Tetris' => '테트리스',
        'Mahjong Connect' => '마작 연결',
        'Bejeweled' => '보석 매칭',
        'Minesweeper' => '지뢰 찾기',
        'Memory' => '카드 맞추기',
        'Brick Breaker' => '벽돌 깨기',
        'Tic Tac Toe' => '틱택토',
        'Mario Run' => '마리오 런',
        'Flappy Bird' => '플래피 버드',
        'Snake' => '스네이크',
        'Pong' => '퐁',
        'Solitaire' => '솔리테어',
    ];
    if (isset($gameNameMap[$gameName])) {
        $gameName = $gameNameMap[$gameName];
    }
}

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
    if (dark) {
        document.body.classList.add('dark-mode');
    } else {
        document.body.classList.remove('dark-mode');
    }
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

<style>
/* 다크 모드 전체 적용 - 강제 덮어쓰기 */
body.dark-mode {
    background: #1a1a2e !important;
    color: #fff !important;
}
body.dark-mode html,
body.dark-mode body {
    background: #1a1a2e !important;
}
body.dark-mode .game-area,
body.dark-mode .game-container,
body.dark-mode #game-board,
body.dark-mode .game-info,
body.dark-mode .controls,
body.dark-mode .game-board-container,
body.dark-mode #game-canvas {
    background: transparent !important;
}
body.dark-mode .score-box,
body.dark-mode .info-box,
body.dark-mode .info-item,
body.dark-mode .stats {
    background: rgba(255,255,255,0.1) !important;
    color: #fff !important;
}
body.dark-mode .score-label,
body.dark-mode .info-label {
    color: #ccc !important;
}
body.dark-mode .tile,
body.dark-mode .gem {
    background: rgba(255,255,255,0.1) !important;
}
body.dark-mode .game-message {
    background: rgba(0,0,0,0.95) !important;
}
body.dark-mode footer,
body.dark-mode footer a {
    color: #888 !important;
}
</style>

<header>
    <div class="header-content">
        <a href="<?= $homeUrl ?>" class="logo">🎮 <?= SITE_NAME ?><?= $gameName ? ' - ' . $gameName : '' ?></a>
        <nav>
            <a href="<?= $homeUrl ?>" <?= !$isGamePage ? 'class="active"' : '' ?>>미니게임</a>
            <a href="<?= $blogUrl ?>">블로그</a>
            <button class="theme-btn" onclick="toggleTheme()" title="테마 전환">🌙</button>
        </nav>
    </div>
</header>
