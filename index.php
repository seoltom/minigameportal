<?php
/**
 * 미니게임포털 - 메인 페이지
 */

require_once 'config.php';

// 카테고리 필터
$category = $_GET['category'] ?? 'all';
$search = $_GET['search'] ?? '';

// 게임 데이터 (완료: ✅, 개발예정: 🔜)
$games = [
    // 퍼즐/전략 게임
    ['id' => 1, 'name' => '2048', 'category' => 'puzzle', 'icon' => '🎮', 'desc' => '숫자 합치기 퍼즐 게임', 'status' => 'completed'],
    ['id' => 2, 'name' => 'Tetris', 'category' => 'puzzle', 'icon' => '🧱', 'desc' => '고전 블록 쌓기 게임', 'status' => 'completed'],
    ['id' => 3, 'name' => 'Sudoku', 'category' => 'puzzle', 'icon' => '🔢', 'desc' => '숫자 퍼즐 게임', 'status' => 'upcoming'],
    ['id' => 4, 'name' => 'Mahjong Connect', 'category' => 'puzzle', 'icon' => '🀄', 'desc' => '마작 연결 퍼즐', 'status' => 'completed'],
    ['id' => 5, 'name' => 'Bejeweled', 'category' => 'puzzle', 'icon' => '💎', 'desc' => '보석 매칭 게임', 'status' => 'completed'],
    ['id' => 6, 'name' => 'Candy Crush', 'category' => 'puzzle', 'icon' => '🍬', 'desc' => '사탕 매칭 퍼즐', 'status' => 'upcoming'],
    ['id' => 7, 'name' => 'Minesweeper', 'category' => 'puzzle', 'icon' => '💣', 'desc' => '지뢰 찾기 퍼즐', 'status' => 'completed'],
    ['id' => 8, 'name' => 'Memory', 'category' => 'puzzle', 'icon' => '🧠', 'desc' => '카드 짝맞추기', 'status' => 'completed'],
    ['id' => 9, 'name' => 'Cut the Rope', 'category' => 'puzzle', 'icon' => '✂️', 'desc' => '밧줄 자르기 물리 퍼즐', 'status' => 'upcoming'],
    ['id' => 10, 'name' => 'Tower Defense', 'category' => 'puzzle', 'icon' => '🏰', 'desc' => '타워 디펜스 전략', 'status' => 'upcoming'],
    ['id' => 11, 'name' => 'Brick Breaker', 'category' => 'puzzle', 'icon' => '🧱', 'desc' => '벽돌 깨기 게임', 'status' => 'completed'],
    ['id' => 12, 'name' => 'Tic-Tac-Toe', 'category' => 'puzzle', 'icon' => '⭕', 'desc' => 'CPU와 틱택토', 'status' => 'completed'],
    // 레이싱/스포츠
    ['id' => 13, 'name' => 'Turbo Racing', 'category' => 'racing', 'icon' => '🏎️', 'desc' => '3D 레이싱 게임', 'status' => 'upcoming'],
    ['id' => 14, 'name' => 'Hill Climb Racing', 'category' => 'racing', 'icon' => '🏔️', 'desc' => '언덕 등반 레이싱', 'status' => 'upcoming'],
    ['id' => 15, 'name' => 'Moto X3M', 'category' => 'racing', 'icon' => '🏍️', 'desc' => '오토바이 모터크로스', 'status' => 'upcoming'],
    ['id' => 16, 'name' => 'Soccer Physics', 'category' => 'racing', 'icon' => '⚽', 'desc' => '축구 캐주얼 게임', 'status' => 'upcoming'],
    // 액션/어드벤처
    ['id' => 17, 'name' => 'Mario Run', 'category' => 'action', 'icon' => '🍄', 'desc' => '마리오 런 게임', 'status' => 'completed'],
    ['id' => 18, 'name' => 'Flappy Bird', 'category' => 'action', 'icon' => '🐦', 'desc' => '새 날개짓 게임', 'status' => 'completed'],
    ['id' => 19, 'name' => 'Doodle Jump', 'category' => 'action', 'icon' => '📝', 'desc' => '점프 게임', 'status' => 'upcoming'],
    ['id' => 20, 'name' => 'Temple Run', 'category' => 'action', 'icon' => '🏃', 'desc' => '템플 런 달리기', 'status' => 'upcoming'],
    ['id' => 21, 'name' => 'Snake', 'category' => 'action', 'icon' => '🐍', 'desc' => '뱀 먹기 게임', 'status' => 'completed'],
    ['id' => 22, 'name' => 'Pong', 'category' => 'action', 'icon' => '🏓', 'desc' => '탁구 게임', 'status' => 'completed'],
    ['id' => 23, 'name' => 'Subway Surfers', 'category' => 'action', 'icon' => '🚇', 'desc' => '지하철 서핑', 'status' => 'upcoming'],
    ['id' => 24, 'name' => 'Jetpack Joyride', 'category' => 'action', 'icon' => '🚀', 'desc' => '제트팩 달리기', 'status' => 'upcoming'],
    // 카지노/보드
    ['id' => 25, 'name' => 'Solitaire', 'category' => 'casino', 'icon' => '🃏', 'desc' => '솔리테어 카드 게임', 'status' => 'completed'],
    ['id' => 26, 'name' => 'Spider Solitaire', 'category' => 'casino', 'icon' => '🕷️', 'desc' => '스파이더 솔리테어', 'status' => 'upcoming'],
    ['id' => 27, 'name' => 'FreeCell', 'category' => 'casino', 'icon' => '🎴', 'desc' => '프리셀 카드 게임', 'status' => 'upcoming'],
    ['id' => 28, 'name' => 'Chess', 'category' => 'casino', 'icon' => '♟️', 'desc' => '온라인 체스', 'status' => 'upcoming'],
    ['id' => 29, 'name' => 'Checkers', 'category' => 'casino', 'icon' => '⚫', 'desc' => '체커 게임', 'status' => 'upcoming'],
    ['id' => 30, 'name' => 'Backgammon', 'category' => 'casino', 'icon' => '🎲', 'desc' => '백개몬 게임', 'status' => 'upcoming'],
    ['id' => 31, 'name' => 'Dominoes', 'category' => 'casino', 'icon' => '🀱', 'desc' => '도미노 게임', 'status' => 'upcoming'],
    ['id' => 32, 'name' => 'Bingo', 'category' => 'casino', 'icon' => '🔴', 'desc' => '빙고 게임', 'status' => 'upcoming'],
];

// 완료/예정 개수
$completedCount = count(array_filter($games, fn($g) => $g['status'] === 'completed'));
$upcomingCount = count(array_filter($games, fn($g) => $g['status'] === 'upcoming'));

// 필터링
$filteredGames = array_filter($games, function($game) use ($category, $search) {
    // 카테고리 필터
    if ($category !== 'all' && $game['category'] !== $category) {
        return false;
    }
    // 검색 필터
    if ($search && stripos($game['name'], $search) === false && stripos($game['desc'], $search) === false) {
        return false;
    }
    return true;
});

?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?= SITE_NAME ?> - 다양한 미니게임을 즐기세요!</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Pretendard:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* 게임 카드 스타일 */
        .game-card {
            position: relative;
        }
        
        /* 게임 이름 옆 인라인 라벨 */
        .status-badge-inline {
            padding: 3px 10px;
            border-radius: 10px;
            font-size: 11px;
            font-weight: 600;
            margin-left: 8px;
            vertical-align: middle;
        }
        
        .status-badge-inline.completed {
            background: linear-gradient(135deg, #22c55e, #16a34a);
            color: #fff;
        }
        
        .status-badge-inline.upcoming {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: #fff;
        }
        
        /* 완료 게임 카드 하이라이트 */
        .game-card.completed-game:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(34, 197, 94, 0.3);
        }
        
        /* 예정 게임은 약간 투명하게 */
        .game-card.upcoming-game {
            opacity: 0.7;
        }
        
        .game-card.upcoming-game:hover {
            transform: none;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <?php require_once 'header.php'; ?>

    <!-- 메인 콘텐츠 -->
    <main class="container">
        <!-- 검색 -->
        <div class="search-container">
            <form class="search-box" method="GET">
                <input type="text" name="search" placeholder="검색어를 입력하세요..." value="<?= htmlspecialchars($search) ?>">
                <button type="submit">🔍 검색</button>
            </form>
        </div>

        <!-- 카테고리 필터 -->
        <div style="display: flex; gap: 10px; justify-content: center; margin-bottom: 30px; flex-wrap: wrap;">
            <a href="?category=all" style="padding: 8px 20px; border-radius: 20px; background: <?= $category === 'all' ? '#4f46e5' : '#fff' ?>; color: <?= $category === 'all' ? '#fff' : '#666' ?>; box-shadow: 0 2px 10px rgba(0,0,0,0.1); font-size: 14px;">
                전체 (<?= count($games) ?>)
            </a>
            <?php foreach ($CATEGORIES as $key => $name): ?>
                <?php 
                    $count = count(array_filter($games, fn($g) => $g['category'] === $key));
                ?>
                <a href="?category=<?= $key ?>" style="padding: 8px 20px; border-radius: 20px; background: <?= $category === $key ? '#4f46e5' : '#fff' ?>; color: <?= $category === $key ? '#fff' : '#666' ?>; box-shadow: 0 2px 10px rgba(0,0,0,0.1); font-size: 14px;">
                    <?= $name ?> (<?= $count ?>)
                </a>
            <?php endforeach; ?>
        </div>

        <!-- 완료/예정 통계 -->
        <div style="display: flex; gap: 12px; justify-content: center; margin-bottom: 20px;">
            <span style="padding: 6px 14px; background: linear-gradient(135deg, #22c55e, #16a34a); color: #fff; border-radius: 20px; font-size: 12px; font-weight: 600;">✅ 완료: <?= $completedCount ?></span>
            <span style="padding: 6px 14px; background: linear-gradient(135deg, #f59e0b, #d97706); color: #fff; border-radius: 20px; font-size: 12px; font-weight: 600;">🔜 개발예정: <?= $upcomingCount ?></span>
        </div>

        <!-- 게임 그리드 -->
        <div class="game-grid">
            <?php foreach ($filteredGames as $game): ?>
                <?php 
                    $statusLabel = $game['status'] === 'completed' ? '완료' : '개발예정';
                    $statusClass = $game['status'];
                    $cardClass = $game['status'] === 'completed' ? 'completed-game' : 'upcoming-game';
                    $href = $game['status'] === 'completed' ? 'games/' . strtolower(str_replace(' ', '-', str_replace('-', '', $game['name']))) . '/' : '#';
                ?>
                <a href="<?= $href ?>" 
                   class="game-card <?= $cardClass ?>" 
                   <?= $game['status'] === 'upcoming' ? 'onclick="return false;"' : '' ?>>
                    <div class="game-icon"><?= $game['icon'] ?></div>
                    <div class="game-info">
                        <h3 class="game-title">
                            <?= $game['name'] ?>
                            <span class="status-badge-inline <?= $statusClass ?>"><?= $statusLabel ?></span>
                        </h3>
                        <p class="game-desc"><?= $game['name'] ?> - <?= $game['desc'] ?></p>
                        <div class="game-meta">
                            <span class="game-category"><?= $CATEGORIES[$game['category']] ?></span>
                        </div>
                    </div>
                    <?php if ($game['status'] === 'upcoming'): ?>
                        <div style="position: absolute; inset: 0; background: rgba(255,255,255,0.5); border-radius: 12px;"></div>
                    <?php endif; ?>
                </a>
            <?php endforeach; ?>
        </div>

        <?php if (empty($filteredGames)): ?>
            <div style="text-align: center; padding: 60px 20px; color: #888;">
                <p style="font-size: 48px; margin-bottom: 20px;">🔍</p>
                <p>검색 결과가 없습니다.</p>
            </div>
        <?php endif; ?>
    </main>

    <!-- 푸터 -->
    <footer>
        <p>© <?= date('Y') ?> <a href="https://tomseol.pe.kr/" target="_blank">tomseol.pe.kr</a>에서 제작한 <?= SITE_NAME ?></p>
    </footer>
</body>
</html>
