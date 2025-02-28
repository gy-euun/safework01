<?php
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

// 최근 활동 데이터 가져오기
$recentActivities = $activity->getRecentActivities(5); // 최근 5개 활동
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="AI 기반 위험성평가 시스템 대시보드 - 위험성 평가 현황 및 데이터 분석">
    <title>대시보드 - AI 기반 위험성평가 시스템 | RISK NINE</title>
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
            transition: all 0.3s;
            z-index: 1000;
            overflow-y: auto;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }
        
        .sidebar-header {
            padding: 20px;
            background-color: rgba(0, 0, 0, 0.1);
        }
        
        .sidebar-menu {
            padding: 20px 0;
        }
        
        .sidebar-item {
            padding: 12px 20px;
            display: block;
            color: rgba(255, 255, 255, 0.85);
            text-decoration: none;
            transition: var(--transition);
            border-left: 4px solid transparent;
        }
        
        .sidebar-item:hover, .sidebar-item.active {
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
            border-left-color: var(--accent-color);
        }
        
        .sidebar-item i {
            width: 24px;
            margin-right: 10px;
            text-align: center;
        }
        
        /* 서브메뉴 스타일 */
        .sidebar-dropdown-icon {
            transition: transform 0.3s ease;
            font-size: 0.8rem;
        }
        
        .sidebar-item[aria-expanded="true"] .sidebar-dropdown-icon {
            transform: rotate(180deg);
        }
        
        .sidebar-submenu {
            padding: 5px 0;
            background-color: rgba(0, 0, 0, 0.1);
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
        
        .nav-search {
            position: relative;
            width: 300px;
        }
        
        .nav-search input {
            padding-left: 40px;
            border-radius: 50px;
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
        }
        
        .nav-search i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #868e96;
        }
        
        .stats-card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            padding: 20px;
            height: 100%;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }
        
        .stats-icon {
            position: absolute;
            right: 15px;
            top: 15px;
            width: 50px;
            height: 50px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
        }
        
        .stats-number {
            font-size: 2.5rem;
            font-weight: 700;
            margin-top: 20px;
        }
        
        .chart-container {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            padding: 20px;
            height: 100%;
        }
        
        .activity-container {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            padding: 20px;
            height: 100%;
        }
        
        .activity-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .activity-item {
            display: flex;
            padding: 12px 0;
            border-bottom: 1px solid #f1f3f5;
        }
        
        .activity-item:last-child {
            border-bottom: none;
        }
        
        .activity-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background-color: #f1f3f5;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
        }
        
        .activity-content {
            flex: 1;
        }
        
        .task-container {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            padding: 20px;
            height: 100%;
        }
        
        .task-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .task-item {
            display: flex;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 10px;
            margin-bottom: 15px;
            align-items: center;
        }
        
        .task-status {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-right: 15px;
        }
        
        .task-info {
            flex: 1;
        }
        
        .mobile-menu-toggle {
            display: none;
            background: none;
            border: none;
            color: var(--primary-color);
            font-size: 1.5rem;
            cursor: pointer;
        }
        
        @media (max-width: 991.98px) {
            .sidebar-container {
                transform: translateX(-100%);
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
            <a href="index.php" class="sidebar-item active">
                <i class="fas fa-tachometer-alt"></i> 대시보드
            </a>
            
            <!-- AI 위험성평가 메뉴 -->
            <a href="ai_assessment.php" class="sidebar-item">
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
                    <div class="sidebar-submenu">
                        <a href="profile_edit.php" class="sidebar-subitem">
                            <i class="fas fa-user-edit"></i> 정보수정
                        </a>
                        <a href="payment_info.php" class="sidebar-subitem">
                            <i class="fas fa-credit-card"></i> 결제정보
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="sidebar-divider my-3"></div>
            
            <a href="customer_support.php" class="sidebar-item">
                <i class="fas fa-headset"></i> 고객센터
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
                    <button class="mobile-menu-toggle me-3" id="toggleMenu">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h4 class="mb-0">대시보드</h4>
                </div>
                <div class="d-flex align-items-center">
                    <div class="nav-search me-3">
                        <i class="fas fa-search"></i>
                        <input type="text" class="form-control" placeholder="검색..." id="dashboardSearch">
                    </div>
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="userMenu" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user-circle me-1"></i> <?php echo $_SESSION["username"]; ?>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userMenu">
                            <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i> 프로필</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i> 설정</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="../logout.php"><i class="fas fa-sign-out-alt me-2"></i> 로그아웃</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!-- 상단 네비게이션 끝 -->
        
        <!-- 통계 요약 카드 시작 -->
        <div class="row g-4 mb-4">
            <div class="col-xl-3 col-md-6">
                <div class="stats-card">
                    <div class="stats-icon" style="background-color: var(--primary-color);">
                        <i class="fas fa-robot"></i>
                    </div>
                    <h6 class="text-muted mb-1">AI 위험성평가</h6>
                    <div class="stats-number">0</div>
                    <p class="text-muted mb-0">
                        <i class="fas fa-calendar-alt me-1"></i> 생성된 평가 수
                    </p>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="stats-card">
                    <div class="stats-icon" style="background-color: #ffa726;">
                        <i class="fas fa-hard-hat"></i>
                    </div>
                    <h6 class="text-muted mb-1">등록된 작업자</h6>
                    <div class="stats-number">0</div>
                    <p class="text-muted mb-0">
                        <i class="fas fa-users me-1"></i> 관리 중인 작업자
                    </p>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="stats-card">
                    <div class="stats-icon" style="background-color: #66bb6a;">
                        <i class="fas fa-video"></i>
                    </div>
                    <h6 class="text-muted mb-1">안전교육 영상</h6>
                    <div class="stats-number">0</div>
                    <p class="text-muted mb-0">
                        <i class="fas fa-play-circle me-1"></i> 시청 가능한 영상
                    </p>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="stats-card">
                    <div class="stats-icon" style="background-color: #ef5350;">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h6 class="text-muted mb-1">안전 점수</h6>
                    <div class="stats-number">100</div>
                    <p class="text-muted mb-0">
                        <i class="fas fa-check-circle me-1"></i> 안전 관리 상태
                    </p>
                </div>
            </div>
        </div>
        <!-- 통계 요약 카드 끝 -->
        
        <!-- AI 위험성평가 소개 시작 -->
        <div class="row g-4 mb-4">
            <div class="col-lg-8">
                <div class="chart-container" style="height: auto;">
                    <h5 class="mb-4">AI 위험성평가 시스템</h5>
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <div class="feature-card p-4 rounded shadow-sm text-center">
                                <i class="fas fa-robot mb-3" style="font-size: 2rem; color: var(--primary-color);"></i>
                                <h5>AI 자동 작성</h5>
                                <p class="text-muted">최소한의 정보만으로 AI가 위험성평가표를 자동으로 작성합니다.</p>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="feature-card p-4 rounded shadow-sm text-center">
                                <i class="fas fa-file-export mb-3" style="font-size: 2rem; color: var(--accent-color);"></i>
                                <h5>문서 내보내기</h5>
                                <p class="text-muted">엑셀 및 PDF 형식으로 평가 결과를 다운로드할 수 있습니다.</p>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="feature-card p-4 rounded shadow-sm text-center">
                                <i class="fas fa-laptop-code mb-3" style="font-size: 2rem; color: #4CAF50;"></i>
                                <h5>간편한 입력</h5>
                                <p class="text-muted">공정명, 인원, 사용장비, 날씨 등 간단한 정보만 입력하면 됩니다.</p>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="feature-card p-4 rounded shadow-sm text-center">
                                <i class="fas fa-chart-line mb-3" style="font-size: 2rem; color: #2196F3;"></i>
                                <h5>위험성 분석</h5>
                                <p class="text-muted">업종, 공정, 유해위험요인 등을 자동으로 분석하여 제공합니다.</p>
                            </div>
                        </div>
                    </div>
                    <div class="text-center mt-3">
                        <a href="ai_assessment.php" class="btn btn-primary btn-lg">
                            <i class="fas fa-plus-circle me-2"></i> AI 위험성평가 시작하기
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="activity-container">
                    <div class="activity-header">
                        <h5 class="mb-0">최근 활동</h5>
                        <a href="activities.php" class="btn btn-sm btn-outline-primary">모두 보기</a>
                    </div>
                    <?php if(count($recentActivities) > 0): ?>
                        <?php foreach($recentActivities as $activity): ?>
                            <div class="activity-item">
                                <div class="activity-icon">
                                    <?php 
                                    $iconClass = 'fa-bell';
                                    $iconColor = 'var(--primary-color)';
                                    switch($activity['activity_type']) {
                                        case 'login':
                                            $iconClass = 'fa-sign-in-alt';
                                            $iconColor = '#4CAF50';
                                            break;
                                        case 'logout':
                                            $iconClass = 'fa-sign-out-alt';
                                            $iconColor = '#F44336';
                                            break;
                                        case 'ai_analysis':
                                            $iconClass = 'fa-robot';
                                            $iconColor = '#2196F3';
                                            break;
                                    }
                                    ?>
                                    <i class="fas <?php echo $iconClass; ?>" style="color: <?php echo $iconColor; ?>;"></i>
                                </div>
                                <div class="activity-content">
                                    <p class="mb-1" style="font-size: 0.9rem;"><?php echo escapeOutput($activity['description']); ?></p>
                                    <small class="text-muted"><?php echo date('Y-m-d H:i', strtotime($activity['created_at'])); ?></small>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-center my-4 text-muted">최근 활동이 없습니다.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <!-- AI 위험성평가 소개 끝 -->
        
        <!-- AI 위험성평가 진행 과정 시작 -->
        <div class="row g-4">
            <div class="col-12">
                <div class="task-container">
                    <h5 class="mb-4">AI 위험성평가 진행 과정</h5>
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <div class="process-step text-center">
                                <div class="process-number">1</div>
                                <h6 class="mt-3">공정 정보 입력</h6>
                                <p class="text-muted small">공정명, 인원, 사용장비, 날씨 등 기본 정보를 입력합니다.</p>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="process-step text-center">
                                <div class="process-number">2</div>
                                <h6 class="mt-3">AI 분석</h6>
                                <p class="text-muted small">인공지능이 업종, 공정, 유해위험요인 등을 자동으로 분석합니다.</p>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="process-step text-center">
                                <div class="process-number">3</div>
                                <h6 class="mt-3">위험성평가 생성</h6>
                                <p class="text-muted small">위험성 평가표가 자동으로 생성됩니다.</p>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="process-step text-center">
                                <div class="process-number">4</div>
                                <h6 class="mt-3">문서 다운로드</h6>
                                <p class="text-muted small">엑셀 또는 PDF 형식으로 문서를 다운로드할 수 있습니다.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- AI 위험성평가 진행 과정 끝 -->
    </div>
    <!-- 메인 컨텐츠 끝 -->
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 사이드바 토글
            const toggleMenu = document.getElementById('toggleMenu');
            const sidebar = document.getElementById('sidebar');
            
            if (toggleMenu) {
                toggleMenu.addEventListener('click', function() {
                    sidebar.classList.toggle('sidebar-show');
                });
            }
            
            // 현재 페이지에 해당하는 메뉴 항목 활성화
            const currentPageUrl = window.location.pathname;
            const fileName = currentPageUrl.split('/').pop();
            
            // 메인 메뉴 항목 활성화
            document.querySelectorAll('.sidebar-item').forEach(item => {
                const href = item.getAttribute('href');
                if (href && href !== '#' && fileName === href) {
                    item.classList.add('active');
                    
                    // 드롭다운인 경우 부모 메뉴도 활성화
                    const parent = item.closest('.sidebar-dropdown');
                    if (parent) {
                        const parentMenu = parent.querySelector('[data-bs-toggle="collapse"]');
                        const submenu = parent.querySelector('.collapse');
                        if (parentMenu && submenu) {
                            parentMenu.classList.add('active');
                            submenu.classList.add('show');
                        }
                    }
                }
            });
            
            // 서브메뉴 항목 활성화
            document.querySelectorAll('.sidebar-subitem').forEach(item => {
                const href = item.getAttribute('href');
                if (href && fileName === href) {
                    item.classList.add('active');
                    
                    // 부모 드롭다운 열기
                    const parent = item.closest('.collapse');
                    const parentDropdown = item.closest('.sidebar-dropdown');
                    if (parent && parentDropdown) {
                        parent.classList.add('show');
                        const trigger = parentDropdown.querySelector('[data-bs-toggle="collapse"]');
                        if (trigger) {
                            trigger.classList.add('active');
                            trigger.setAttribute('aria-expanded', 'true');
                        }
                    }
                }
            });
            
            // 차트 데이터
            const riskLevelData = {
                labels: ['매우 높음', '높음', '중간', '낮음', '매우 낮음'],
                datasets: [{
                    label: '위험 수준 분포',
                    data: <?php echo json_encode($riskLevelDistribution); ?>,
                    backgroundColor: [
                        '#d32f2f',
                        '#ef5350',
                        '#ffa726',
                        '#66bb6a',
                        '#26c6da'
                    ],
                    borderWidth: 0
                }]
            };
            
            const statusData = {
                labels: ['대기 중', '진행 중', '완료됨'],
                datasets: [{
                    label: '상태 분포',
                    data: <?php echo json_encode($statusDistribution); ?>,
                    backgroundColor: [
                        '#9e9e9e',
                        '#ffa726',
                        '#66bb6a'
                    ],
                    borderWidth: 0
                }]
            };
            
            // 위험 수준 차트
            const riskCtx = document.getElementById('riskDistributionChart').getContext('2d');
            const riskChart = new Chart(riskCtx, {
                type: 'bar',
                data: riskLevelData,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                drawBorder: false
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
            
            // 상태 차트
            const statusCtx = document.getElementById('statusDistributionChart').getContext('2d');
            const statusChart = new Chart(statusCtx, {
                type: 'doughnut',
                data: statusData,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
            
            // 차트 전환
            const chartButtons = document.querySelectorAll('[data-chart]');
            const charts = {
                'risk': document.getElementById('riskDistributionChart'),
                'status': document.getElementById('statusDistributionChart')
            };
            
            chartButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const chartType = this.getAttribute('data-chart');
                    
                    // 버튼 활성화 상태 변경
                    chartButtons.forEach(btn => btn.classList.remove('active'));
                    this.classList.add('active');
                    
                    // 차트 보이기/숨기기
                    Object.keys(charts).forEach(type => {
                        if (type === chartType) {
                            charts[type].style.display = 'block';
                        } else {
                            charts[type].style.display = 'none';
                        }
                    });
                });
            });
            
            // 통계 카드 애니메이션
            document.querySelectorAll('.stats-card').forEach(function(card) {
                card.classList.add('fade-in');
            });
            
            // 페이지 로드 시 애니메이션
            const mainContent = document.getElementById('main-content');
            mainContent.style.opacity = '0';
            setTimeout(() => {
                mainContent.style.transition = 'opacity 0.5s ease';
                mainContent.style.opacity = '1';
            }, 100);
        });
    </script>
</body>
</html> 