<?php
/**
 * 미니게임포털 설정 파일
 */

// 세션 시작
session_start();

// 기본 설정
define('SITE_NAME', '설탕포털');
define('SITE_URL', 'http://tomseol.pe.kr');
define('ROOT_PATH', __DIR__);

// 경로 설정 (메인 페이지 또는 게임 페이지에서 호출됨에 따라 다름)
if (file_exists(__DIR__ . '/header.php')) {
    define('IS_ROOT', true);
} else {
    define('IS_ROOT', false);
}

// 에러 표시 (개발 환경)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 카테고리 정의
$GLOBALS['CATEGORIES'] = [
    'puzzle' => '🎯 퍼즐/전략',
    'racing' => '🏎️ 레이싱/스포츠',
    'action' => '🎮 액션/어드벤처',
    'casino' => '🎲 카지노/보드'
];
