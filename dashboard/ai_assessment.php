<?php
// 에러 표시 설정
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 설정 파일과 세션 포함
include '../includes/config.php';
include '../includes/activity.php';

// 로그인 상태 확인
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: ../login.php");
    exit;
}

// 클래스 인스턴스 생성
$activity = new Activity();

// 새로운 AI 평가 생성 처리
$aiGeneratedContent = "";
$isGeneratedContent = false;
$formData = [];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["generate_assessment"])) {
    // 폼 데이터 처리
    $formData = [
        'project_name' => $_POST['project_name'] ?? '',
        'process_name' => $_POST['process_name'] ?? '',
        'worker_count' => $_POST['worker_count'] ?? '',
        'equipment' => $_POST['equipment'] ?? '',
        'weather' => $_POST['weather'] ?? '',
        'assessment_date' => $_POST['assessment_date'] ?? date('Y-m-d'),
        'manager' => $_POST['manager'] ?? $_SESSION["username"],
        'industry_type' => $_POST['industry_type'] ?? '',
        'note' => $_POST['note'] ?? ''
    ];
    
    // 활동 로그 기록
    logActivity($_SESSION["id"], "ai_analysis", "AI 위험성평가를 생성했습니다: " . $formData['process_name']);
    
    // AI 생성 결과 시뮬레이션 (실제로는 API 호출 등을 통해 생성)
    $isGeneratedContent = true;
    // 더미 데이터 - 실제 구현에서는 AI API 응답으로 대체됨
    $aiGeneratedContent = [
        'hazards' => [
            [
                'type' => '추락',
                'name' => '고소작업 중 추락',
                'detail_task' => '철골작업',
                'situation' => '작업 발판 미끄러짐, 안전대 미착용으로 인한 추락 위험',
                'cause' => '작업 발판 미끄러움, 안전장비 미착용',
                'likelihood' => 4, // 1-5 척도
                'severity' => 5, // 1-5 척도
                'risk_level' => '매우 높음',
                'control' => '안전대 착용 의무화, 작업 발판 미끄럼 방지 조치, 안전난간 설치',
                'responsibility' => '안전관리자',
                'period' => '즉시',
                // 개선 후 위험성 추가
                'improved_likelihood' => 2,
                'improved_severity' => 4,
                'improved_risk_level' => '중간'
            ],
            [
                'type' => '낙하',
                'name' => '공구 및 자재 낙하',
                'detail_task' => '외부마감',
                'situation' => '고소작업 중 공구나 자재가 떨어져 하부 작업자 부상 위험',
                'cause' => '공구 안전줄 미사용, 자재 안전조치 미흡',
                'likelihood' => 3,
                'severity' => 4,
                'risk_level' => '높음',
                'control' => '공구 안전줄 사용, 작업구역 통제, 자재 적치 시 안전조치',
                'responsibility' => '작업반장',
                'period' => '작업 전',
                // 개선 후 위험성 추가
                'improved_likelihood' => 1,
                'improved_severity' => 3,
                'improved_risk_level' => '낮음'
            ],
            [
                'type' => '협착',
                'name' => '장비 사이 협착',
                'detail_task' => '중장비 작업',
                'situation' => '장비 조작 중 신체 일부가 끼이는 위험',
                'cause' => '장비 안전장치 고장, 안전거리 미확보',
                'likelihood' => 2,
                'severity' => 4,
                'risk_level' => '중간',
                'control' => '장비 사용 전 안전교육, 2인 1조 작업, 장비 방호장치 점검',
                'responsibility' => '장비 담당자',
                'period' => '작업 전',
                // 개선 후 위험성 추가
                'improved_likelihood' => 1,
                'improved_severity' => 3,
                'improved_risk_level' => '낮음'
            ],
            [
                'type' => '전도',
                'name' => '작업장 내 넘어짐',
                'detail_task' => '내부작업',
                'situation' => '작업장 바닥 장애물, 미끄러운 바닥으로 인한 넘어짐 위험',
                'cause' => '정리정돈 미흡, 바닥 미끄러움',
                'likelihood' => 3,
                'severity' => 2,
                'risk_level' => '중간',
                'control' => '작업장 정리정돈, 미끄럼 방지 조치, 적절한 조명 확보',
                'responsibility' => '현장 관리자',
                'period' => '상시',
                // 개선 후 위험성 추가
                'improved_likelihood' => 1,
                'improved_severity' => 2,
                'improved_risk_level' => '매우 낮음'
            ],
            [
                'type' => '화재',
                'name' => '용접 작업 중 화재',
                'detail_task' => '용접작업',
                'situation' => '용접 불꽃에 의한 가연물 점화로 화재 발생 위험',
                'cause' => '가연물 방치, 화기 관리 소홀',
                'likelihood' => 2,
                'severity' => 5,
                'risk_level' => '높음',
                'control' => '작업 구역 내 가연물 제거, 소화기 비치, 감시자 배치',
                'responsibility' => '용접작업자',
                'period' => '작업 중',
                // 개선 후 위험성 추가
                'improved_likelihood' => 1,
                'improved_severity' => 4,
                'improved_risk_level' => '중간'
            ]
        ]
    ];
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="AI 기반 위험성평가 시스템 - 인공지능을 활용한 위험성 평가 자동화">
    <title>AI 위험성평가 - RISK NINE</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <!-- 웹 접근성 개선을 위한 스크립트 -->
    <script src="https://cdn.jsdelivr.net/npm/focus-visible@5.2.0/dist/focus-visible.min.js" defer></script>
    <!-- Chart.js 포함 -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --sidebar-width: 280px;
            --primary-color: #2c3e50;
            --primary-dark: #1a2530;
            --secondary-color: #3498db;
            --transition: all 0.3s ease;
        }
        
        body {
            background-color: #f8f9fa;
            overflow-x: hidden;
        }
        
        .sidebar-container {
            position: fixed;
            width: var(--sidebar-width);
            height: 100vh;
            background-color: var(--primary-color);
            color: white;
            transition: var(--transition);
            z-index: 1000;
            overflow-y: auto;
        }
        
        .sidebar-header {
            padding: 20px;
            background-color: var(--primary-dark);
        }
        
        .brand-logo {
            font-size: 24px;
            font-weight: 700;
            color: white;
            text-decoration: none;
        }
        
        .risk {
            color: #f1c40f;
        }
        
        .nine {
            color: white;
        }
        
        .brand-tagline {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.7);
        }
        
        .sidebar-menu {
            padding: 20px 0;
        }
        
        .sidebar-item {
            display: block;
            padding: 12px 20px;
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            transition: var(--transition);
            font-weight: 500;
        }
        
        .sidebar-item i {
            width: 24px;
            margin-right: 10px;
            text-align: center;
        }
        
        .sidebar-item:hover, .sidebar-item.active {
            color: white;
            background-color: rgba(255, 255, 255, 0.1);
        }
        
        .sidebar-subitem {
            padding: 10px 20px 10px 56px;
            display: block;
            color: rgba(255, 255, 255, 0.75);
            text-decoration: none;
            transition: var(--transition);
            position: relative;
            font-size: 0.95rem;
        }
        
        .sidebar-subitem i {
            width: 20px;
            margin-right: 8px;
            font-size: 0.9rem;
        }
        
        .sidebar-subitem:hover, .sidebar-subitem.active {
            color: white;
            background-color: rgba(255, 255, 255, 0.05);
        }
        
        .sidebar-dropdown-icon {
            transition: transform 0.3s;
        }
        
        .sidebar-item[aria-expanded="true"] .sidebar-dropdown-icon {
            transform: rotate(180deg);
        }
        
        .sidebar-divider {
            height: 1px;
            background-color: rgba(255, 255, 255, 0.1);
            margin: 0 20px;
        }
        
        .main-content {
            margin-left: var(--sidebar-width);
            padding: 30px;
            transition: all 0.3s;
        }
        
        .navbar-container {
            padding: 15px 30px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            margin-bottom: 30px;
        }
        
        .navbar-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .mobile-menu-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 1.2rem;
            cursor: pointer;
            color: var(--primary-color);
        }
        
        .ai-form-container {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            margin-bottom: 2rem;
        }
        
        .process-step {
            padding: 1.5rem;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            position: relative;
        }
        
        .process-number {
            width: 40px;
            height: 40px;
            background-color: var(--primary-color);
            color: #fff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin: 0 auto 1rem;
        }
        
        .assessment-result-container {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 2rem;
        }
        
        .hazard-item {
            border-left: 4px solid var(--primary-color);
            background-color: #f8f9fa;
            padding: 1rem;
            border-radius: 0.25rem;
            margin-bottom: 1rem;
        }
        
        .risk-very-high {
            border-left-color: #d32f2f;
        }
        
        .risk-high {
            border-left-color: #f44336;
        }
        
        .risk-medium {
            border-left-color: #ff9800;
        }
        
        .risk-low {
            border-left-color: #4caf50;
        }
        
        .submit-button {
            background-color: var(--primary-color);
            color: white;
            padding: 0.75rem 2rem;
            font-weight: 600;
            border: none;
            border-radius: 0.25rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .submit-button:hover {
            background-color: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
        }
        
        .risk-badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
            border-radius: 1rem;
            font-weight: 600;
        }
        
        .risk-badge.very-high {
            background-color: #ffebee;
            color: #d32f2f;
        }
        
        .risk-badge.high {
            background-color: #ffebee;
            color: #f44336;
        }
        
        .risk-badge.medium {
            background-color: #fff3e0;
            color: #ff9800;
        }
        
        .risk-badge.low {
            background-color: #e8f5e9;
            color: #4caf50;
        }
        
        @media (max-width: 991.98px) {
            .sidebar-container {
                transform: translateX(-100%);
                transition: transform 0.3s;
            }
            
            .sidebar-container.mobile-open {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .mobile-menu-toggle {
                display: block;
            }
            
            .sidebar-show {
                transform: translateX(0);
            }
        }
    </style>
</head>
<body>
    <!-- 건너뛰기 링크 (접근성) -->
    <a href="#main-content" class="skip-link">본문 바로가기</a>
    
    <!-- 사이드바 시작 -->
    <div class="sidebar-container" id="sidebar">
        <div class="sidebar-header">
            <a href="../index.php" class="d-block">
                <span class="brand-logo"><span class="risk">RISK</span> <span class="nine">NINE</span></span>
            </a>
            <div class="mt-2">
                <span class="brand-tagline">AI 위험성평가 시스템</span>
            </div>
        </div>
        <div class="sidebar-menu">
            <a href="index.php" class="sidebar-item">
                <i class="fas fa-tachometer-alt"></i> 대시보드
            </a>
            
            <!-- AI 위험성평가 메뉴 -->
            <a href="ai_assessment.php" class="sidebar-item active">
                <i class="fas fa-robot"></i> AI 위험성평가 작성
            </a>
            
            <a href="safety_videos.php" class="sidebar-item">
                <i class="fas fa-video"></i> 안전교육 영상
            </a>
            
            <a href="workers.php" class="sidebar-item">
                <i class="fas fa-hard-hat"></i> 작업자 관리
            </a>
            
            <!-- 마이페이지 메뉴 (드롭다운) -->
            <div class="sidebar-dropdown">
                <a href="#" class="sidebar-item d-flex justify-content-between align-items-center" data-bs-toggle="collapse" data-bs-target="#myPageSubmenu">
                    <span><i class="fas fa-user"></i> 마이페이지</span>
                    <i class="fas fa-chevron-down sidebar-dropdown-icon"></i>
                </a>
                <div class="collapse" id="myPageSubmenu">
                    <a href="profile.php" class="sidebar-subitem">프로필 관리</a>
                    <a href="settings.php" class="sidebar-subitem">설정</a>
                </div>
            </div>
            
            <!-- 보고서 메뉴 (드롭다운) -->
            <div class="sidebar-dropdown">
                <a href="#" class="sidebar-item d-flex justify-content-between align-items-center" data-bs-toggle="collapse" data-bs-target="#reportSubmenu">
                    <span><i class="fas fa-file-alt"></i> 보고서</span>
                    <i class="fas fa-chevron-down sidebar-dropdown-icon"></i>
                </a>
                <div class="collapse" id="reportSubmenu">
                    <a href="reports.php" class="sidebar-subitem">평가 보고서</a>
                    <a href="statistics.php" class="sidebar-subitem">통계</a>
                </div>
            </div>
            
            <div class="sidebar-divider"></div>
            
            <a href="../logout.php" class="sidebar-item">
                <i class="fas fa-sign-out-alt"></i> 로그아웃
            </a>
        </div>
    </div>
    <!-- 사이드바 끝 -->

    <!-- 메인 컨텐츠 시작 -->
    <div class="main-content" id="main-content">
        <!-- 상단 네비게이션 시작 -->
        <div class="navbar-container">
            <div class="navbar-content">
                <div class="d-flex align-items-center">
                    <button class="mobile-menu-toggle me-3 d-lg-none" id="toggleMenu">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h4 class="mb-0">AI 위험성평가</h4>
                </div>
                <div class="d-flex align-items-center">
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="userMenu" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user-circle me-1"></i> <?php echo htmlspecialchars($_SESSION["username"] ?? "사용자"); ?>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userMenu">
                            <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user me-2"></i> 프로필</a></li>
                            <li><a class="dropdown-item" href="settings.php"><i class="fas fa-cog me-2"></i> 설정</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="../logout.php"><i class="fas fa-sign-out-alt me-2"></i> 로그아웃</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!-- 상단 네비게이션 끝 -->
        
        <!-- 알림 메시지 표시 -->
        <?php echo displayMessages(); ?>
        
        <!-- AI 평가 폼 시작 -->
        <div class="container-fluid p-4">
            <?php if (!$isGeneratedContent): ?>
            <div class="row mb-4">
                <div class="col-12">
                    <div class="ai-form-container">
                        <h4 class="mb-4">AI 위험성평가 생성</h4>
                        <p class="text-muted mb-4">공정명, 작업 인원, 사용 장비 등 기본적인 정보를 입력하시면 AI가 자동으로 위험성평가표를 생성합니다.</p>
                        
                        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="project_name" class="form-label">프로젝트명 <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="project_name" name="project_name" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="process_name" class="form-label">공정명 <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="process_name" name="process_name" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="industry_type" class="form-label">업종</label>
                                    <select class="form-select" id="industry_type" name="industry_type">
                                        <option value="">선택하세요</option>
                                        <option value="건설업">건설업</option>
                                        <option value="제조업">제조업</option>
                                        <option value="서비스업">서비스업</option>
                                        <option value="운수업">운수업</option>
                                        <option value="기타">기타</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="worker_count" class="form-label">작업 인원 <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="worker_count" name="worker_count" min="1" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="assessment_date" class="form-label">평가 일자</label>
                                    <input type="date" class="form-control" id="assessment_date" name="assessment_date" value="<?php echo date('Y-m-d'); ?>">
                                </div>
                                <div class="col-md-6">
                                    <label for="equipment" class="form-label">사용 장비 <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="equipment" name="equipment" placeholder="예: 크레인, 지게차, 용접기" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="weather" class="form-label">작업 환경/날씨</label>
                                    <input type="text" class="form-control" id="weather" name="weather" placeholder="예: 실내, 맑음, 비, 눈">
                                </div>
                                <div class="col-md-6">
                                    <label for="manager" class="form-label">관리자</label>
                                    <input type="text" class="form-control" id="manager" name="manager" value="<?php echo $_SESSION["username"]; ?>">
                                </div>
                                <div class="col-md-6">
                                    <label for="note" class="form-label">추가 사항</label>
                                    <textarea class="form-control" id="note" name="note" rows="3" placeholder="특별한 작업 조건이나 위험 요소가 있다면 여기에 기록하세요."></textarea>
                                </div>
                                <div class="col-12 text-center mt-4">
                                    <button type="submit" class="submit-button" name="generate_assessment">
                                        <i class="fas fa-robot me-2"></i> AI 위험성평가 생성하기
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <?php else: ?>
            <!-- AI 평가 결과 -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="assessment-result-container">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4 class="mb-0">AI 위험성평가 결과</h4>
                            <div>
                                <button class="btn btn-outline-primary me-2">
                                    <i class="fas fa-file-excel me-1"></i> 엑셀 다운로드
                                </button>
                                <button class="btn btn-outline-danger">
                                    <i class="fas fa-file-pdf me-1"></i> PDF 다운로드
                                </button>
                            </div>
                        </div>
                        
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">평가 정보</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>프로젝트명:</strong> <?php echo escapeOutput($formData['project_name']); ?></p>
                                        <p><strong>공정명:</strong> <?php echo escapeOutput($formData['process_name']); ?></p>
                                        <p><strong>업종:</strong> <?php echo escapeOutput($formData['industry_type'] ?: '미지정'); ?></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>작업 인원:</strong> <?php echo escapeOutput($formData['worker_count']); ?>명</p>
                                        <p><strong>평가 일자:</strong> <?php echo escapeOutput($formData['assessment_date']); ?></p>
                                        <p><strong>관리자:</strong> <?php echo escapeOutput($formData['manager']); ?></p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>사용 장비:</strong> <?php echo escapeOutput($formData['equipment']); ?></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>작업 환경/날씨:</strong> <?php echo escapeOutput($formData['weather'] ?: '미지정'); ?></p>
                                    </div>
                                </div>
                                <?php if(!empty($formData['note'])): ?>
                                <div class="row">
                                    <div class="col-12">
                                        <p><strong>추가 사항:</strong> <?php echo escapeOutput($formData['note']); ?></p>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <h5 class="mb-3">식별된 위험 요소</h5>
                        
                        <!-- 위험성 평가 기준표 추가 -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">위험성 평가 기준표</h6>
                                    </div>
                                    <div class="card-body p-0">
                                        <table class="table table-bordered table-sm mb-0">
                                            <thead>
                                                <tr class="text-center bg-light">
                                                    <th>위험성</th>
                                                    <th>중대성(1-5)</th>
                                                    <th>가능성(1-5)</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td class="bg-danger text-white text-center">매우 높음</td>
                                                    <td>5 (치명적)</td>
                                                    <td>5 (확실)</td>
                                                </tr>
                                                <tr>
                                                    <td class="bg-warning text-dark text-center">높음</td>
                                                    <td>4 (중상)</td>
                                                    <td>4 (상당히 가능)</td>
                                                </tr>
                                                <tr>
                                                    <td class="bg-info text-dark text-center">중간</td>
                                                    <td>3 (경상)</td>
                                                    <td>3 (가능)</td>
                                                </tr>
                                                <tr>
                                                    <td class="bg-success text-white text-center">낮음</td>
                                                    <td>2 (경미)</td>
                                                    <td>2 (가능성 낮음)</td>
                                                </tr>
                                                <tr>
                                                    <td class="bg-light text-dark text-center">매우 낮음</td>
                                                    <td>1 (무시가능)</td>
                                                    <td>1 (희박)</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">위험성 결정표</h6>
                                    </div>
                                    <div class="card-body p-0">
                                        <table class="table table-bordered table-sm mb-0">
                                            <thead>
                                                <tr class="text-center bg-light">
                                                    <th>가능성/중대성</th>
                                                    <th>1</th>
                                                    <th>2</th>
                                                    <th>3</th>
                                                    <th>4</th>
                                                    <th>5</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td class="text-center bg-light"><strong>5</strong></td>
                                                    <td class="bg-info">5</td>
                                                    <td class="bg-warning">10</td>
                                                    <td class="bg-warning">15</td>
                                                    <td class="bg-danger">20</td>
                                                    <td class="bg-danger">25</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-center bg-light"><strong>4</strong></td>
                                                    <td class="bg-info">4</td>
                                                    <td class="bg-info">8</td>
                                                    <td class="bg-warning">12</td>
                                                    <td class="bg-warning">16</td>
                                                    <td class="bg-danger">20</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-center bg-light"><strong>3</strong></td>
                                                    <td class="bg-success">3</td>
                                                    <td class="bg-info">6</td>
                                                    <td class="bg-info">9</td>
                                                    <td class="bg-warning">12</td>
                                                    <td class="bg-warning">15</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-center bg-light"><strong>2</strong></td>
                                                    <td class="bg-success">2</td>
                                                    <td class="bg-success">4</td>
                                                    <td class="bg-info">6</td>
                                                    <td class="bg-info">8</td>
                                                    <td class="bg-warning">10</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-center bg-light"><strong>1</strong></td>
                                                    <td class="bg-success">1</td>
                                                    <td class="bg-success">2</td>
                                                    <td class="bg-success">3</td>
                                                    <td class="bg-success">4</td>
                                                    <td class="bg-info">5</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- 엑셀 형식의 위험성평가 결과 테이블 -->
                        <div class="table-responsive mb-4">
                            <table class="table table-bordered table-hover">
                                <thead class="table-light">
                                    <tr class="text-center">
                                        <th rowspan="2">번호</th>
                                        <th rowspan="2">업종</th>
                                        <th rowspan="2">공정</th>
                                        <th rowspan="2">세부작업</th>
                                        <th rowspan="2">분류</th>
                                        <th rowspan="2">유해위험요인</th>
                                        <th rowspan="2">원인</th>
                                        <th colspan="3">현재 위험성 척도</th>
                                        <th rowspan="2">위험성 수준</th>
                                        <th rowspan="2">관리대책</th>
                                        <th rowspan="2">담당자</th>
                                        <th rowspan="2">조치기한</th>
                                        <th colspan="3">개선 후 위험성 척도</th>
                                        <th rowspan="2">개선 후<br>위험성 수준</th>
                                    </tr>
                                    <tr class="text-center">
                                        <th>가능성</th>
                                        <th>중대성</th>
                                        <th>위험성</th>
                                        <th>가능성</th>
                                        <th>중대성</th>
                                        <th>위험성</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($aiGeneratedContent['hazards'] as $index => $hazard): ?>
                                    <?php 
                                        $riskValue = $hazard['likelihood'] * $hazard['severity'];
                                        $riskClass = '';
                                        switch($hazard['risk_level']) {
                                            case '매우 높음':
                                                $riskClass = 'table-danger';
                                                break;
                                            case '높음':
                                                $riskClass = 'table-warning';
                                                break;
                                            case '중간':
                                                $riskClass = 'table-info';
                                                break;
                                            case '낮음':
                                            case '매우 낮음':
                                                $riskClass = 'table-success';
                                                break;
                                        }
                                        
                                        // 개선 후 위험성 값 계산
                                        $improvedRiskValue = $hazard['improved_likelihood'] * $hazard['improved_severity'];
                                        $improvedRiskClass = '';
                                        switch($hazard['improved_risk_level']) {
                                            case '매우 높음':
                                                $improvedRiskClass = 'table-danger';
                                                break;
                                            case '높음':
                                                $improvedRiskClass = 'table-warning';
                                                break;
                                            case '중간':
                                                $improvedRiskClass = 'table-info';
                                                break;
                                            case '낮음':
                                                $improvedRiskClass = 'table-success';
                                                break;
                                            case '매우 낮음':
                                                $improvedRiskClass = 'table-light';
                                                break;
                                        }
                                    ?>
                                    <tr>
                                        <td class="text-center"><?php echo $index + 1; ?></td>
                                        <td><?php echo escapeOutput($formData['industry_type'] ?: '미지정'); ?></td>
                                        <td><?php echo escapeOutput($formData['process_name']); ?></td>
                                        <td><?php echo escapeOutput($hazard['detail_task']); ?></td>
                                        <td><?php echo escapeOutput($hazard['type']); ?></td>
                                        <td><?php echo escapeOutput($hazard['name']); ?></td>
                                        <td><?php echo escapeOutput($hazard['cause']); ?></td>
                                        <td class="text-center <?php echo $riskClass; ?>"><?php echo $hazard['likelihood']; ?></td>
                                        <td class="text-center <?php echo $riskClass; ?>"><?php echo $hazard['severity']; ?></td>
                                        <td class="text-center <?php echo $riskClass; ?>"><?php echo $riskValue; ?></td>
                                        <td class="text-center fw-bold <?php echo $riskClass; ?>"><?php echo escapeOutput($hazard['risk_level']); ?></td>
                                        <td><?php echo escapeOutput($hazard['control']); ?></td>
                                        <td><?php echo escapeOutput($hazard['responsibility']); ?></td>
                                        <td><?php echo escapeOutput($hazard['period']); ?></td>
                                        <td class="text-center <?php echo $improvedRiskClass; ?>"><?php echo $hazard['improved_likelihood']; ?></td>
                                        <td class="text-center <?php echo $improvedRiskClass; ?>"><?php echo $hazard['improved_severity']; ?></td>
                                        <td class="text-center <?php echo $improvedRiskClass; ?>"><?php echo $improvedRiskValue; ?></td>
                                        <td class="text-center fw-bold <?php echo $improvedRiskClass; ?>"><?php echo escapeOutput($hazard['improved_risk_level']); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- 서명 영역 추가 -->
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">승인 및 서명</h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <div class="border rounded p-3 text-center">
                                            <p class="mb-4">작성자</p>
                                            <p class="mb-1"><strong><?php echo escapeOutput($_SESSION["username"]); ?></strong></p>
                                            <p class="mb-2 small"><?php echo date('Y-m-d'); ?></p>
                                            <div class="mt-2">(서명)</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="border rounded p-3 text-center">
                                            <p class="mb-4">검토자</p>
                                            <p class="mb-1"><strong><?php echo escapeOutput($formData['manager']); ?></strong></p>
                                            <p class="mb-2 small"><?php echo date('Y-m-d'); ?></p>
                                            <div class="mt-2">(서명)</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="border rounded p-3 text-center">
                                            <p class="mb-4">승인자</p>
                                            <p class="mb-1"><strong>_______________</strong></p>
                                            <p class="mb-2 small">날짜: _______________</p>
                                            <div class="mt-2">(서명)</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-4 text-center">
                            <a href="ai_assessment.php" class="btn btn-outline-secondary me-2">
                                <i class="fas fa-redo me-1"></i> 새로 작성하기
                            </a>
                            <button class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> 저장하기
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- AI 위험성평가 설명 -->
            <div class="row">
                <div class="col-12">
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">AI 위험성평가란?</h5>
                        </div>
                        <div class="card-body">
                            <p>AI 위험성평가는 인공지능 기술을 활용하여 작업 현장의 위험 요소를 자동으로 식별하고 평가하는 혁신적인 방식입니다. 작업자와 관리자는 최소한의 정보만 입력하면 인공지능이 다음과 같은 작업을 수행합니다:</p>
                            
                            <div class="row mt-4">
                                <div class="col-md-6">
                                    <div class="d-flex mb-3">
                                        <div class="me-3">
                                            <i class="fas fa-search" style="font-size: 1.5rem; color: var(--primary-color);"></i>
                                        </div>
                                        <div>
                                            <h6>위험 요소 식별</h6>
                                            <p class="text-muted small">입력된 공정, 장비, 환경 정보를 바탕으로 발생 가능한 위험 요소를 자동으로 식별합니다.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex mb-3">
                                        <div class="me-3">
                                            <i class="fas fa-calculator" style="font-size: 1.5rem; color: var(--primary-color);"></i>
                                        </div>
                                        <div>
                                            <h6>위험성 산출</h6>
                                            <p class="text-muted small">발생가능성과 중대성을 계산하여 각 위험 요소의 위험성 수준을 평가합니다.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex mb-3">
                                        <div class="me-3">
                                            <i class="fas fa-shield-alt" style="font-size: 1.5rem; color: var(--primary-color);"></i>
                                        </div>
                                        <div>
                                            <h6>관리대책 제시</h6>
                                            <p class="text-muted small">각 위험 요소에 대한 효과적인 관리대책을 제안하여 사고 예방에 도움을 줍니다.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex mb-3">
                                        <div class="me-3">
                                            <i class="fas fa-file-alt" style="font-size: 1.5rem; color: var(--primary-color);"></i>
                                        </div>
                                        <div>
                                            <h6>문서 자동화</h6>
                                            <p class="text-muted small">위험성평가 결과를 표준화된 형식의 문서로 자동 생성하여 업무 효율성을 높입니다.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- AI 평가 폼 끝 -->
    </div>
    <!-- 메인 컨텐츠 끝 -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 사이드바 토글 (모바일)
            const toggleMenu = document.getElementById('toggleMenu');
            const sidebar = document.getElementById('sidebar');
            
            if (toggleMenu) {
                toggleMenu.addEventListener('click', function() {
                    sidebar.classList.toggle('mobile-open');
                });
            }
            
            // 드롭다운 메뉴 활성화 - 부트스트랩 토글 방식으로 수정
            const dropdownItems = document.querySelectorAll('.sidebar-dropdown > .sidebar-item');
            
            dropdownItems.forEach(item => {
                item.addEventListener('click', function(e) {
                    // 이벤트 기본 동작 방지 (링크 이동 방지)
                    e.preventDefault();
                    
                    const targetId = this.getAttribute('data-bs-target');
                    const submenu = document.querySelector(targetId);
                    
                    if(submenu) {
                        const isExpanded = this.getAttribute('aria-expanded') === 'true';
                        
                        // 상태 토글
                        this.setAttribute('aria-expanded', !isExpanded);
                        submenu.classList.toggle('show');
                        
                        // 다른 모든 드롭다운 닫기
                        const otherDropdowns = document.querySelectorAll('.sidebar-dropdown > .collapse.show');
                        otherDropdowns.forEach(dropdown => {
                            if (dropdown !== submenu) {
                                dropdown.classList.remove('show');
                                const toggler = dropdown.previousElementSibling;
                                toggler.setAttribute('aria-expanded', 'false');
                            }
                        });
                    }
                });
            });
            
            // 현재 페이지 메뉴 활성화
            const currentPath = window.location.pathname;
            const currentPage = currentPath.split('/').pop();
            
            const sidebarItems = document.querySelectorAll('.sidebar-item, .sidebar-subitem');
            sidebarItems.forEach(item => {
                const href = item.getAttribute('href');
                if (href && href === currentPage) {
                    item.classList.add('active');
                    
                    // 드롭다운 내부 항목인 경우 부모 드롭다운도 활성화
                    const parentDropdown = item.closest('.collapse');
                    if (parentDropdown) {
                        parentDropdown.classList.add('show');
                        const toggler = parentDropdown.previousElementSibling;
                        toggler.classList.add('active');
                        toggler.setAttribute('aria-expanded', 'true');
                    }
                }
            });
            
            // 페이지 애니메이션
            document.querySelectorAll('.card, .stats-card, .ai-form-container').forEach(function(element) {
                element.classList.add('fade-in');
            });
        });
    </script>
</body>
</html> 