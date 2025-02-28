<?php
include 'includes/config.php';

$username = $password = $confirm_password = $email = $full_name = "";
$username_err = $password_err = $confirm_password_err = $email_err = $full_name_err = "";

if($_SERVER["REQUEST_METHOD"] == "POST"){
    // 사용자 이름 검증
    if(empty(trim($_POST["username"]))){
        $username_err = "사용자 이름을 입력해주세요.";
    } else{
        $sql = "SELECT id FROM users WHERE username = ?";
        
        if($stmt = mysqli_prepare($mysqli, $sql)){
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            $param_username = trim($_POST["username"]);
            
            if(mysqli_stmt_execute($stmt)){
                mysqli_stmt_store_result($stmt);
                
                if(mysqli_stmt_num_rows($stmt) == 1){
                    $username_err = "이미 사용 중인 사용자 이름입니다.";
                } else{
                    $username = trim($_POST["username"]);
                }
            } else{
                echo "오류가 발생했습니다. 나중에 다시 시도해주세요.";
            }

            mysqli_stmt_close($stmt);
        }
    }
    
    // 이메일 검증
    if(empty(trim($_POST["email"]))){
        $email_err = "이메일을 입력해주세요.";     
    } else{
        $sql = "SELECT id FROM users WHERE email = ?";
        
        if($stmt = mysqli_prepare($mysqli, $sql)){
            mysqli_stmt_bind_param($stmt, "s", $param_email);
            $param_email = trim($_POST["email"]);
            
            if(mysqli_stmt_execute($stmt)){
                mysqli_stmt_store_result($stmt);
                
                if(mysqli_stmt_num_rows($stmt) == 1){
                    $email_err = "이미 사용 중인 이메일입니다.";
                } else{
                    $email = trim($_POST["email"]);
                }
            } else{
                echo "오류가 발생했습니다. 나중에 다시 시도해주세요.";
            }

            mysqli_stmt_close($stmt);
        }
    }
    
    // 이름 검증
    if(empty(trim($_POST["full_name"]))){
        $full_name_err = "이름을 입력해주세요.";     
    } else{
        $full_name = trim($_POST["full_name"]);
    }
    
    // 비밀번호 검증
    if(empty(trim($_POST["password"]))){
        $password_err = "비밀번호를 입력해주세요.";     
    } elseif(strlen(trim($_POST["password"])) < 6){
        $password_err = "비밀번호는 최소 6자 이상이어야 합니다.";
    } else{
        $password = trim($_POST["password"]);
    }
    
    // 비밀번호 확인 검증
    if(empty(trim($_POST["confirm_password"]))){
        $confirm_password_err = "비밀번호 확인을 입력해주세요.";     
    } else{
        $confirm_password = trim($_POST["confirm_password"]);
        if(empty($password_err) && ($password != $confirm_password)){
            $confirm_password_err = "비밀번호가 일치하지 않습니다.";
        }
    }
    
    // 입력 오류 확인 후 데이터베이스에 삽입
    if(empty($username_err) && empty($password_err) && empty($confirm_password_err) && empty($email_err) && empty($full_name_err)){
        $sql = "INSERT INTO users (username, password, email, full_name) VALUES (?, ?, ?, ?)";
         
        if($stmt = mysqli_prepare($mysqli, $sql)){
            mysqli_stmt_bind_param($stmt, "ssss", $param_username, $param_password, $param_email, $param_full_name);
            
            $param_username = $username;
            $param_password = password_hash($password, PASSWORD_DEFAULT); // 비밀번호를 해시화
            $param_email = $email;
            $param_full_name = $full_name;
            
            if(mysqli_stmt_execute($stmt)){
                // 계정 생성 성공 후 로그인 페이지로 리디렉션
                header("location: login.php");
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
    <meta name="description" content="RISK NINE - 회원가입 | 지능형 위험성평가 시스템에 가입하세요">
    <title>회원가입 - RISK NINE 위험성평가 시스템</title>
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
                            <a class="nav-link btn btn-outline-light px-3" href="login.php">
                                로그인
                            </a>
                        </li>
                        <li class="nav-item ms-lg-2">
                            <a class="nav-link btn btn-accent px-3 active" href="register.php">
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
            <div class="login-card fade-in register-card">
                <div class="login-logo">
                    <span class="brand-logo"><span class="risk">RISK</span> <span class="nine">NINE</span></span>
                </div>
                <h1 class="login-title">회원가입</h1>
                <p class="text-center mb-4">RISK NINE에 가입하고 AI 기반 위험성평가 시스템을 이용하세요.</p>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <div class="mb-3">
                        <label for="username" class="form-label">사용자 이름</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0">
                                <i class="fas fa-user text-primary"></i>
                            </span>
                            <input type="text" name="username" id="username" class="form-control border-start-0 <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $username; ?>" placeholder="로그인에 사용할 사용자 이름">
                        </div>
                        <span class="invalid-feedback"><?php echo $username_err; ?></span>
                    </div>
                    <div class="mb-3">
                        <label for="full_name" class="form-label">이름</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0">
                                <i class="fas fa-user-tag text-primary"></i>
                            </span>
                            <input type="text" name="full_name" id="full_name" class="form-control border-start-0 <?php echo (!empty($full_name_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $full_name; ?>" placeholder="실명 입력">
                        </div>
                        <span class="invalid-feedback"><?php echo $full_name_err; ?></span>
                    </div>    
                    <div class="mb-3">
                        <label for="email" class="form-label">이메일</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0">
                                <i class="fas fa-envelope text-primary"></i>
                            </span>
                            <input type="email" name="email" id="email" class="form-control border-start-0 <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $email; ?>" placeholder="이메일 주소">
                        </div>
                        <span class="invalid-feedback"><?php echo $email_err; ?></span>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">비밀번호</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0">
                                <i class="fas fa-lock text-primary"></i>
                            </span>
                            <input type="password" name="password" id="password" class="form-control border-start-0 <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>" placeholder="비밀번호 (6자 이상)">
                        </div>
                        <span class="invalid-feedback"><?php echo $password_err; ?></span>
                    </div>
                    <div class="mb-4">
                        <label for="confirm_password" class="form-label">비밀번호 확인</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0">
                                <i class="fas fa-check-double text-primary"></i>
                            </span>
                            <input type="password" name="confirm_password" id="confirm_password" class="form-control border-start-0 <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>" placeholder="비밀번호 확인">
                        </div>
                        <span class="invalid-feedback"><?php echo $confirm_password_err; ?></span>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="agree_terms" required>
                            <label class="form-check-label" for="agree_terms">
                                <a href="#" class="text-primary">이용약관</a> 및 <a href="#" class="text-primary">개인정보 처리방침</a>에 동의합니다.
                            </label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <button type="submit" class="btn btn-primary w-100 btn-lg">회원가입 완료</button>
                    </div>
                    <p class="text-center mb-0">이미 계정이 있으신가요? <a href="login.php" class="text-primary">로그인하기</a></p>
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
            const registerCard = document.querySelector('.register-card');
            registerCard.style.opacity = 0;
            setTimeout(() => {
                registerCard.style.opacity = 1;
                registerCard.style.transform = 'translateY(0)';
            }, 100);
        });
    </script>
</body>
</html> 