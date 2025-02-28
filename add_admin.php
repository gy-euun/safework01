<?php
// 데이터베이스 접속 정보
$dbServer = 'localhost';
$dbUsername = 'root';
$dbPassword = '';
$dbName = 'risk9';

// 데이터베이스 연결
$mysqli = new mysqli($dbServer, $dbUsername, $dbPassword, $dbName);

// 연결 확인
if($mysqli->connect_error) {
    die("ERROR: 데이터베이스 연결에 실패했습니다. " . $mysqli->connect_error);
}

// UTF-8 문자셋 설정
$mysqli->set_charset("utf8mb4");

// 관리자 계정 데이터
$username = 'admin';
$password = '$2y$10$yUzf9ROiRsZCTVpn6dI1.eCRQS90aR3uVBCXWysLU1p9TSEBbZ7CC'; // admin123
$email = 'admin@risk9.com';
$fullName = 'System Admin';
$isAdmin = 1;

// SQL 쿼리 준비
$sql = "INSERT INTO users (username, password, email, full_name, is_admin) 
        VALUES (?, ?, ?, ?, ?)";

// Prepared Statement 생성
$stmt = $mysqli->prepare($sql);

// 변수 바인딩
$stmt->bind_param("ssssi", $username, $password, $email, $fullName, $isAdmin);

// 실행
if($stmt->execute()) {
    echo "관리자 계정이 성공적으로 생성되었습니다.";
} else {
    echo "Error: " . $stmt->error;
}

// 연결 종료
$stmt->close();
$mysqli->close();
?> 