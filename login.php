<?php
include 'includes/config.php';

$username = $password = "";
$username_err = $password_err = $login_err = "";

if($_SERVER["REQUEST_METHOD"] == "POST"){
    // 사용자 이름 검증
    if(empty(trim($_POST["username"]))){
        $username_err = "사용자 이름을 입력해주세요.";
    } else{
        $username = trim($_POST["username"]);
    }
    
    // 비밀번호 검증
    if(empty(trim($_POST["password"]))){
        $password_err = "비밀번호를 입력해주세요.";
    } else{
        $password = trim($_POST["password"]);
    }
    
    // 인증 시도
    if(empty($username_err) && empty($password_err)){
        $sql = "SELECT id, username, password FROM users WHERE username = ?";
        
        if($stmt = mysqli_prepare($mysqli, $sql)){
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            $param_username = $username;
            
            if(mysqli_stmt_execute($stmt)){
                mysqli_stmt_store_result($stmt);
                
                if(mysqli_stmt_num_rows($stmt) == 1){                    
                    mysqli_stmt_bind_result($stmt, $id, $username, $hashed_password);
                    if(mysqli_stmt_fetch($stmt)){
                        if(password_verify($password, $hashed_password)){
                            // 비밀번호 일치, 세션 시작
                            session_start();
                            
                            // 세션 변수 저장
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["username"] = $username;                            
                            
                            // 대시보드로 리디렉션
                            header("location: dashboard/");
                        } else{
                            // 비밀번호 불일치
                            $login_err = "잘못된 사용자 이름 또는 비밀번호입니다.";
                        }
                    }
                } else{
                    // 사용자 이름 없음
                    $login_err = "잘못된 사용자 이름 또는 비밀번호입니다.";
                }
            } else{
                echo "오류가 발생했습니다. 나중에 다시 시도해주세요.";
            }

            mysqli_stmt_close($stmt);
        }
    }
    
    mysqli_close($mysqli);
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="RISK NINE - 로그인 | 지능형 위험성평가 시스템에 로그인하세요">
    <title>로그인 - RISK NINE 위험성평가 시스템</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+KR:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <!-- 건너뛰기 링크 (접근성) -->
    <a href="#main-content" class="skip-link">본문 바로가기</a>
    
    <!-- 헤더 -->
    <header class="header">
        <nav class="navbar navbar-expand-lg navbar-dark bg-navy">
            <div class="container">
                <a class="navbar-brand" href="index.php">
                    <span class="brand-logo"><span class="risk">RISK</span> <span class="nine">NINE</span></span>
                    <span class="brand-divider">|</span>
                    <span class="brand-tagline">AI 위험성평가 시스템</span>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="메뉴 토글">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="index.php#service">서비스 소개</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="index.php#features">주요 기능</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="index.php#process">이용 절차</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="index.php#support">고객 지원</a>
                        </li>
                        <li class="nav-item ms-lg-2">
                            <a class="nav-link btn btn-outline-light px-3 active" href="login.php">
                                로그인
                            </a>
                        </li>
                        <li class="nav-item ms-lg-2">
                            <a class="nav-link btn btn-accent px-3" href="register.php">
                                회원가입
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <!-- 메인 컨텐츠 -->
    <main id="main-content">
        <div class="login-container">
            <div class="login-card fade-in">
                <div class="login-logo">
                    <span class="brand-logo"><span class="risk">RISK</span> <span class="nine">NINE</span></span>
                </div>
                <h1 class="login-title">로그인</h1>
                <?php 
                if(!empty($login_err)){
                    echo '<div class="alert alert-danger">' . $login_err . '</div>';
                }        
                ?>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <div class="mb-4">
                        <label for="username" class="form-label">사용자 이름</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0">
                                <i class="fas fa-user text-primary"></i>
                            </span>
                            <input type="text" name="username" id="username" class="form-control border-start-0 <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $username; ?>" placeholder="사용자 이름 입력">
                        </div>
                        <span class="invalid-feedback"><?php echo $username_err; ?></span>
                    </div>    
                    <div class="mb-4">
                        <label for="password" class="form-label">비밀번호</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0">
                                <i class="fas fa-lock text-primary"></i>
                            </span>
                            <input type="password" name="password" id="password" class="form-control border-start-0 <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>" placeholder="비밀번호 입력">
                        </div>
                        <span class="invalid-feedback"><?php echo $password_err; ?></span>
                    </div>
                    <div class="mb-4">
                        <button type="submit" class="btn btn-primary w-100 btn-lg">로그인</button>
                    </div>
                    <p class="text-center mb-0">계정이 없으신가요? <a href="register.php" class="text-primary">지금 가입하세요</a></p>
                </form>
            </div>
        </div>
    </main>

    <!-- 푸터 -->
    <footer class="footer bg-navy text-white py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4 mb-lg-0">
                    <h5 class="footer-title">RISK NINE</h5>
                    <p class="footer-subtitle">AI 위험성평가 시스템</p>
                    <p class="mb-4">최신 인공지능 기술로 산업 현장의 안전을 강화하고, 위험을 효과적으로 관리하는 종합 솔루션을 제공합니다.</p>
                </div>
                <div class="col-lg-2 col-md-4 mb-4 mb-lg-0">
                    <h5 class="footer-title">페이지</h5>
                    <ul class="footer-links">
                        <li><a href="index.php">홈</a></li>
                        <li><a href="index.php#service">서비스 소개</a></li>
                        <li><a href="index.php#features">주요 기능</a></li>
                        <li><a href="index.php#process">이용 절차</a></li>
                        <li><a href="index.php#support">고객 지원</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-md-4 mb-4 mb-lg-0">
                    <h5 class="footer-title">법적 정보</h5>
                    <ul class="footer-links">
                        <li><a href="#">개인정보 처리방침</a></li>
                        <li><a href="#">이용약관</a></li>
                        <li><a href="#">라이선스</a></li>
                    </ul>
                </div>
                <div class="col-lg-4 col-md-4">
                    <h5 class="footer-title">연락처</h5>
                    <ul class="footer-links">
                        <li><i class="fas fa-envelope me-2"></i> info@risknine.com</li>
                        <li><i class="fas fa-phone me-2"></i> 02-123-4567</li>
                        <li><i class="fas fa-map-marker-alt me-2"></i> 서울특별시 강남구 테헤란로 123</li>
                    </ul>
                    <div class="mt-4">
                        <a href="#" class="social-link"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
            </div>
            <hr class="footer-divider">
            <div class="text-center">
                <p>&copy; 2023 RISK NINE. All Rights Reserved.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // 페이지 로드 시 애니메이션 효과
        document.addEventListener('DOMContentLoaded', function() {
            const loginCard = document.querySelector('.login-card');
            loginCard.style.opacity = 0;
            setTimeout(() => {
                loginCard.style.opacity = 1;
                loginCard.style.transform = 'translateY(0)';
            }, 100);
        });
    </script>
</body>
</html> 