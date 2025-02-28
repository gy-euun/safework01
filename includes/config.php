<?php
// 세션 시작
session_start();

// 데이터베이스 접속 정보
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'risk9');

// 데이터베이스 연결
$mysqli = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// 연결 확인
if($mysqli === false){
    die("ERROR: 데이터베이스 연결에 실패했습니다. " . $mysqli->connect_error);
}

// UTF-8 문자셋 설정
$mysqli->set_charset("utf8mb4");

// 사이트 기본 설정
define('SITE_NAME', 'RISK NINE 위험성평가 시스템');
define('SITE_URL', 'http://localhost/risk9');

// 오류 보고 설정(개발 시에만 활성화, 운영 시 주석 처리)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 활동 로그 기록 함수
function logActivity($userId, $activityType, $description, $relatedId = null, $relatedType = null) {
    global $mysqli;
    
    $userId = $mysqli->real_escape_string($userId);
    $activityType = $mysqli->real_escape_string($activityType);
    $description = $mysqli->real_escape_string($description);
    $relatedId = $relatedId ? $mysqli->real_escape_string($relatedId) : null;
    $relatedType = $relatedType ? $mysqli->real_escape_string($relatedType) : null;
    $ipAddress = $_SERVER['REMOTE_ADDR'];
    
    $sql = "INSERT INTO activities (user_id, activity_type, description, related_id, related_type, ip_address) 
            VALUES ('$userId', '$activityType', '$description', ";
    
    $sql .= $relatedId ? "'$relatedId'" : "NULL";
    $sql .= ", ";
    $sql .= $relatedType ? "'$relatedType'" : "NULL";
    $sql .= ", '$ipAddress')";
    
    $mysqli->query($sql);
}

// 변수 이스케이프 함수
function escapeInput($input) {
    global $mysqli;
    return $mysqli->real_escape_string($input);
}

// 날짜를 한국어 형식으로 변환
function formatDateKorean($date) {
    if (empty($date)) return '';
    $timestamp = strtotime($date);
    return date('Y년 m월 d일', $timestamp);
}

// CSRF 토큰 생성 함수
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// CSRF 토큰 검증 함수
function validateCSRFToken($token) {
    if (!isset($_SESSION['csrf_token']) || $token !== $_SESSION['csrf_token']) {
        return false;
    }
    return true;
}

// XSS 방지를 위한 출력 이스케이프 함수
function escapeOutput($output) {
    return htmlspecialchars($output, ENT_QUOTES, 'UTF-8');
}

// 성공 알림 메시지 설정
function setSuccessMessage($message) {
    $_SESSION['success_message'] = $message;
}

// 오류 알림 메시지 설정
function setErrorMessage($message) {
    $_SESSION['error_message'] = $message;
}

// 알림 메시지 표시 후 제거
function displayMessages() {
    $html = '';
    
    if (isset($_SESSION['success_message'])) {
        $html .= '<div class="alert alert-success alert-dismissible fade show" role="alert">';
        $html .= escapeOutput($_SESSION['success_message']);
        $html .= '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="닫기"></button>';
        $html .= '</div>';
        unset($_SESSION['success_message']);
    }
    
    if (isset($_SESSION['error_message'])) {
        $html .= '<div class="alert alert-danger alert-dismissible fade show" role="alert">';
        $html .= escapeOutput($_SESSION['error_message']);
        $html .= '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="닫기"></button>';
        $html .= '</div>';
        unset($_SESSION['error_message']);
    }
    
    return $html;
}
?> 