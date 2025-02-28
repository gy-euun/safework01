<?php
// 설정 파일 포함
require_once 'config.php';

/**
 * 사용자 인증 관련 클래스
 */
class Auth {
    private $db;
    
    /**
     * 생성자
     */
    public function __construct() {
        global $mysqli;
        $this->db = $mysqli;
    }
    
    /**
     * 사용자 로그인
     * 
     * @param string $username 사용자명
     * @param string $password 비밀번호
     * @return bool 로그인 성공 여부
     */
    public function login($username, $password) {
        // 입력값 검증
        if(empty($username) || empty($password)) {
            setErrorMessage('사용자명과 비밀번호를 모두 입력해주세요.');
            return false;
        }
        
        // SQL 인젝션 방지
        $username = escapeInput($username);
        
        // 사용자 조회
        $sql = "SELECT id, username, password, full_name, is_admin FROM users WHERE username = '$username'";
        $result = $this->db->query($sql);
        
        if($result->num_rows == 1) {
            $row = $result->fetch_assoc();
            
            // 비밀번호 검증
            if(password_verify($password, $row['password'])) {
                // 세션에 사용자 정보 저장
                $_SESSION['loggedin'] = true;
                $_SESSION['id'] = $row['id'];
                $_SESSION['username'] = $row['username'];
                $_SESSION['full_name'] = $row['full_name'];
                $_SESSION['is_admin'] = $row['is_admin'];
                
                // 로그인 활동 기록
                logActivity($_SESSION['id'], 'login', $_SESSION['full_name'] . '님이 로그인했습니다.');
                
                return true;
            } else {
                setErrorMessage('비밀번호가 일치하지 않습니다.');
                return false;
            }
        } else {
            setErrorMessage('존재하지 않는 사용자입니다.');
            return false;
        }
    }
    
    /**
     * 사용자 로그아웃
     */
    public function logout() {
        // 로그아웃 활동 기록
        if(isset($_SESSION['id'])) {
            logActivity($_SESSION['id'], 'logout', $_SESSION['full_name'] . '님이 로그아웃했습니다.');
        }
        
        // 세션 변수 초기화
        $_SESSION = array();
        
        // 세션 쿠키 삭제
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        
        // 세션 파기
        session_destroy();
    }
    
    /**
     * 사용자 회원가입
     * 
     * @param array $userData 사용자 데이터
     * @return bool 회원가입 성공 여부
     */
    public function register($userData) {
        // 필수 입력값 검증
        if(empty($userData['username']) || empty($userData['password']) || empty($userData['confirm_password']) || empty($userData['email']) || empty($userData['full_name'])) {
            setErrorMessage('모든 필수 항목을 입력해주세요.');
            return false;
        }
        
        // 비밀번호 확인
        if($userData['password'] !== $userData['confirm_password']) {
            setErrorMessage('비밀번호가 일치하지 않습니다.');
            return false;
        }
        
        // 이메일 형식 검증
        if(!filter_var($userData['email'], FILTER_VALIDATE_EMAIL)) {
            setErrorMessage('유효한 이메일 주소를 입력해주세요.');
            return false;
        }
        
        // SQL 인젝션 방지
        $username = escapeInput($userData['username']);
        $email = escapeInput($userData['email']);
        $full_name = escapeInput($userData['full_name']);
        $department = isset($userData['department']) ? escapeInput($userData['department']) : '';
        $position = isset($userData['position']) ? escapeInput($userData['position']) : '';
        
        // 사용자명 중복 검사
        $sql = "SELECT id FROM users WHERE username = '$username'";
        $result = $this->db->query($sql);
        
        if($result->num_rows > 0) {
            setErrorMessage('이미 사용 중인 사용자명입니다.');
            return false;
        }
        
        // 이메일 중복 검사
        $sql = "SELECT id FROM users WHERE email = '$email'";
        $result = $this->db->query($sql);
        
        if($result->num_rows > 0) {
            setErrorMessage('이미 등록된 이메일 주소입니다.');
            return false;
        }
        
        // 비밀번호 해싱
        $password_hash = password_hash($userData['password'], PASSWORD_DEFAULT);
        
        // 사용자 등록
        $sql = "INSERT INTO users (username, password, email, full_name, department, position) 
                VALUES ('$username', '$password_hash', '$email', '$full_name', '$department', '$position')";
        
        if($this->db->query($sql)) {
            setSuccessMessage('회원가입이 완료되었습니다. 이제 로그인할 수 있습니다.');
            return true;
        } else {
            setErrorMessage('회원가입 중 오류가 발생했습니다: ' . $this->db->error);
            return false;
        }
    }
    
    /**
     * 비밀번호 변경
     * 
     * @param int $userId 사용자 ID
     * @param string $currentPassword 현재 비밀번호
     * @param string $newPassword 새 비밀번호
     * @param string $confirmPassword 새 비밀번호 확인
     * @return bool 비밀번호 변경 성공 여부
     */
    public function changePassword($userId, $currentPassword, $newPassword, $confirmPassword) {
        // 입력값 검증
        if(empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
            setErrorMessage('모든 필드를 입력해주세요.');
            return false;
        }
        
        // 새 비밀번호 확인
        if($newPassword !== $confirmPassword) {
            setErrorMessage('새 비밀번호가 일치하지 않습니다.');
            return false;
        }
        
        // 비밀번호 정책 검증 (최소 8자, 영문자, 숫자, 특수문자 포함)
        if(strlen($newPassword) < 8 || !preg_match('/[A-Za-z]/', $newPassword) || !preg_match('/\d/', $newPassword) || !preg_match('/[^A-Za-z\d]/', $newPassword)) {
            setErrorMessage('비밀번호는 8자 이상이며, 영문자, 숫자, 특수문자를 포함해야 합니다.');
            return false;
        }
        
        // 사용자 정보 조회
        $sql = "SELECT password FROM users WHERE id = '$userId'";
        $result = $this->db->query($sql);
        
        if($result->num_rows == 1) {
            $row = $result->fetch_assoc();
            
            // 현재 비밀번호 검증
            if(password_verify($currentPassword, $row['password'])) {
                // 새 비밀번호 해싱
                $password_hash = password_hash($newPassword, PASSWORD_DEFAULT);
                
                // 비밀번호 업데이트
                $sql = "UPDATE users SET password = '$password_hash' WHERE id = '$userId'";
                
                if($this->db->query($sql)) {
                    setSuccessMessage('비밀번호가 성공적으로 변경되었습니다.');
                    return true;
                } else {
                    setErrorMessage('비밀번호 변경 중 오류가 발생했습니다: ' . $this->db->error);
                    return false;
                }
            } else {
                setErrorMessage('현재 비밀번호가 일치하지 않습니다.');
                return false;
            }
        } else {
            setErrorMessage('사용자 정보를 찾을 수 없습니다.');
            return false;
        }
    }
    
    /**
     * 사용자 프로필 업데이트
     * 
     * @param int $userId 사용자 ID
     * @param array $userData 사용자 데이터
     * @return bool 업데이트 성공 여부
     */
    public function updateProfile($userId, $userData) {
        // 필수 입력값 검증
        if(empty($userData['email']) || empty($userData['full_name'])) {
            setErrorMessage('필수 항목을 입력해주세요.');
            return false;
        }
        
        // 이메일 형식 검증
        if(!filter_var($userData['email'], FILTER_VALIDATE_EMAIL)) {
            setErrorMessage('유효한 이메일 주소를 입력해주세요.');
            return false;
        }
        
        // SQL 인젝션 방지
        $email = escapeInput($userData['email']);
        $full_name = escapeInput($userData['full_name']);
        $department = isset($userData['department']) ? escapeInput($userData['department']) : '';
        $position = isset($userData['position']) ? escapeInput($userData['position']) : '';
        
        // 이메일 중복 검사 (현재 사용자 제외)
        $sql = "SELECT id FROM users WHERE email = '$email' AND id != '$userId'";
        $result = $this->db->query($sql);
        
        if($result->num_rows > 0) {
            setErrorMessage('이미 등록된 이메일 주소입니다.');
            return false;
        }
        
        // 프로필 업데이트
        $sql = "UPDATE users SET email = '$email', full_name = '$full_name', 
                department = '$department', position = '$position'
                WHERE id = '$userId'";
        
        if($this->db->query($sql)) {
            // 세션 업데이트
            $_SESSION['full_name'] = $full_name;
            
            setSuccessMessage('프로필이 성공적으로 업데이트되었습니다.');
            return true;
        } else {
            setErrorMessage('프로필 업데이트 중 오류가 발생했습니다: ' . $this->db->error);
            return false;
        }
    }
    
    /**
     * 사용자 정보 조회
     * 
     * @param int $userId 사용자 ID
     * @return array|bool 사용자 정보 또는 실패 시 false
     */
    public function getUserInfo($userId) {
        $sql = "SELECT id, username, email, full_name, department, position, is_admin, created_at 
                FROM users WHERE id = '$userId'";
        $result = $this->db->query($sql);
        
        if($result->num_rows == 1) {
            return $result->fetch_assoc();
        } else {
            return false;
        }
    }
    
    /**
     * 모든 사용자 목록 조회 (관리자 전용)
     * 
     * @return array 사용자 목록
     */
    public function getAllUsers() {
        $sql = "SELECT id, username, email, full_name, department, position, is_admin, created_at 
                FROM users ORDER BY full_name";
        $result = $this->db->query($sql);
        
        $users = array();
        while($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
        
        return $users;
    }
    
    /**
     * 관리자 권한 확인
     * 
     * @return bool 관리자 여부
     */
    public function isAdmin() {
        return isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === 1;
    }
    
    /**
     * 로그인 상태 확인
     * 
     * @return bool 로그인 상태 여부
     */
    public function isLoggedIn() {
        return isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;
    }
}
?> 