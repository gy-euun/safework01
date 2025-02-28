<?php
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

// 관리자 계정 데이터
$username = 'admin';
$password = password_hash('admin123', PASSWORD_DEFAULT); // 비밀번호 해시 생성
$email = 'admin@risk9.com';
$fullName = 'System Admin';
$isAdmin = 1;

// 이미 존재하는지 확인
$check_sql = "SELECT * FROM users WHERE username = ?";
$check_stmt = $mysqli->prepare($check_sql);
$check_stmt->bind_param("s", $username);
$check_stmt->execute();
$result = $check_stmt->get_result();

if($result->num_rows > 0) {
    echo "사용자 이름 '{$username}'은(는) 이미 존재합니다.";
} else {
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
        echo "<br>사용자 이름: " . $username;
        echo "<br>비밀번호: admin123";
        echo "<br>이메일: " . $email;
    } else {
        echo "Error: " . $stmt->error;
    }

    // Statement 종료
    $stmt->close();
}

// 연결 종료
$mysqli->close();
?> 