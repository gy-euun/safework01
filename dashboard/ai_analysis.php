<?php
// 설정 파일과 세션 포함
include '../includes/config.php';

// 로그인 상태 확인
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: ../login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="AI 기반 위험성평가 시스템 - AI 분석 도구">
    <title>AI 분석 - AI 기반 위험성평가 시스템 | KRDS</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <!-- 웹 접근성 개선을 위한 스크립트 -->
    <script src="https://cdn.jsdelivr.net/npm/focus-visible@5.2.0/dist/focus-visible.min.js" defer></script>
    <!-- 차트 라이브러리 -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <!-- 건너뛰기 링크 (접근성) -->
    <a href="#main-content" class="skip-link">본문 바로가기</a>
    
    <nav class="navbar navbar-expand-lg navbar-dark bg-navy">
        <div class="container">
            <a class="navbar-brand" href="../index.php">
                <span class="brand-logo">Risk Nain</span>
                <span class="brand-tagline">위험성평가 시스템</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="메뉴 토글">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">
                            <i class="fas fa-tachometer-alt me-1"></i> 대시보드
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="assessments.php">
                            <i class="fas fa-clipboard-list me-1"></i> 평가 관리
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="reports.php">
                            <i class="fas fa-chart-pie me-1"></i> 보고서
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="ai_analysis.php">
                            <i class="fas fa-robot me-1"></i> AI 분석
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../logout.php">
                            <i class="fas fa-sign-out-alt me-1"></i> 로그아웃
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container py-5" id="main-content">
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">대시보드</a></li>
                <li class="breadcrumb-item active" aria-current="page">AI 분석</li>
            </ol>
        </nav>

        <div class="row mb-4">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center">
                    <h1>AI 위험성 분석</h1>
                    <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#aiMethodologyModal">
                        <i class="fas fa-info-circle me-1"></i> AI 분석 방법론
                    </button>
                </div>
                <p class="text-muted">인공지능을 활용한 위험성 평가 데이터 분석 및 예측</p>
            </div>
        </div>

        <!-- 상태 요약 카드 -->
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="dashboard-card h-100">
                    <div class="card-body text-center">
                        <div class="dashboard-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h3 class="h5">분석된 평가</h3>
                        <p class="fs-3 fw-bold text-primary">128</p>
                        <p class="small text-muted">지난 달 대비 <span class="text-success">+15%</span></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="dashboard-card h-100">
                    <div class="card-body text-center">
                        <div class="dashboard-icon">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <h3 class="h5">식별된 위험 요소</h3>
                        <p class="fs-3 fw-bold text-primary">324</p>
                        <p class="small text-muted">지난 달 대비 <span class="text-danger">+8%</span></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="dashboard-card h-100">
                    <div class="card-body text-center">
                        <div class="dashboard-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <h3 class="h5">제안된 안전 조치</h3>
                        <p class="fs-3 fw-bold text-primary">256</p>
                        <p class="small text-muted">지난 달 대비 <span class="text-success">+12%</span></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="dashboard-card h-100">
                    <div class="card-body text-center">
                        <div class="dashboard-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <h3 class="h5">조치 이행률</h3>
                        <p class="fs-3 fw-bold text-primary">84%</p>
                        <p class="small text-muted">지난 달 대비 <span class="text-success">+5%</span></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- AI 인사이트 섹션 -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="dashboard-card">
                    <div class="card-header dashboard-card-header">
                        <h2 class="h4 mb-0">AI 주요 인사이트</h2>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-primary d-flex align-items-center" role="alert">
                            <i class="fas fa-lightbulb me-3 fs-4"></i>
                            <div>
                                <strong>중요 발견사항:</strong> 최근 분석에 따르면, 전기 관련 위험 요소가 지난 3개월 동안 18% 증가했습니다. 전기 작업 안전 교육을 강화하는 것이 권장됩니다.
                            </div>
                        </div>
                        
                        <div class="alert alert-warning d-flex align-items-center" role="alert">
                            <i class="fas fa-exclamation-circle me-3 fs-4"></i>
                            <div>
                                <strong>주의 필요:</strong> 외벽작업 관련 위험성이 높은 수준으로 유지되고 있습니다. 추가적인 안전 대책을 고려해 보세요.
                            </div>
                        </div>
                        
                        <div class="alert alert-success d-flex align-items-center" role="alert">
                            <i class="fas fa-thumbs-up me-3 fs-4"></i>
                            <div>
                                <strong>긍정적 추세:</strong> 화재 위험 관련 조치가 효과적으로 이행되어 해당 위험성이 30% 감소했습니다.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 위험성 트렌드 차트 -->
        <div class="row mb-4">
            <div class="col-md-8">
                <div class="dashboard-card h-100">
                    <div class="card-header dashboard-card-header d-flex justify-content-between align-items-center">
                        <h2 class="h4 mb-0">위험성 추세 분석</h2>
                        <div class="btn-group" role="group" aria-label="기간 선택">
                            <button type="button" class="btn btn-sm btn-outline-primary active">6개월</button>
                            <button type="button" class="btn btn-sm btn-outline-primary">1년</button>
                            <button type="button" class="btn btn-sm btn-outline-primary">전체</button>
                        </div>
                    </div>
                    <div class="card-body">
                        <canvas id="riskTrendChart" height="300" aria-label="위험성 추세 분석 차트" role="img"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="dashboard-card h-100">
                    <div class="card-header dashboard-card-header">
                        <h2 class="h4 mb-0">위험 카테고리 분포</h2>
                    </div>
                    <div class="card-body">
                        <canvas id="riskCategoryChart" height="300" aria-label="위험 카테고리 분포 차트" role="img"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- 예측 및 안전 개선 영역 -->
        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="dashboard-card h-100">
                    <div class="card-header dashboard-card-header">
                        <h2 class="h4 mb-0">AI 위험 예측</h2>
                    </div>
                    <div class="card-body">
                        <p class="mb-4">AI가 분석한 향후 3개월 동안의 주요 위험 예측입니다.</p>
                        
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span>추락 위험</span>
                                <span class="text-danger">높음 (85%)</span>
                            </div>
                            <div class="progress">
                                <div class="progress-bar bg-danger" role="progressbar" style="width: 85%" aria-valuenow="85" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span>전기 위험</span>
                                <span class="text-warning">중간 (65%)</span>
                            </div>
                            <div class="progress">
                                <div class="progress-bar bg-warning" role="progressbar" style="width: 65%" aria-valuenow="65" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span>화재 위험</span>
                                <span class="text-primary">낮음 (30%)</span>
                            </div>
                            <div class="progress">
                                <div class="progress-bar bg-primary" role="progressbar" style="width: 30%" aria-valuenow="30" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span>기계적 위험</span>
                                <span class="text-warning">중간 (55%)</span>
                            </div>
                            <div class="progress">
                                <div class="progress-bar bg-warning" role="progressbar" style="width: 55%" aria-valuenow="55" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                        
                        <div class="text-center mt-4">
                            <a href="#" class="btn btn-outline-primary">상세 예측 보기</a>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 mb-4">
                <div class="dashboard-card h-100">
                    <div class="card-header dashboard-card-header">
                        <h2 class="h4 mb-0">AI 추천 안전 개선 사항</h2>
                    </div>
                    <div class="card-body">
                        <p class="mb-4">AI가 분석한 데이터를 기반으로 권장되는 안전 개선 사항입니다.</p>
                        
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item px-0">
                                <div class="d-flex w-100 justify-content-between">
                                    <h5 class="mb-1">안전 교육 강화</h5>
                                    <span class="badge bg-danger">높은 우선순위</span>
                                </div>
                                <p class="mb-1">고소작업 및 추락방지 교육을 월 1회에서 2회로 증가시키는 것을 권장합니다.</p>
                                <small class="text-muted">예상 위험 감소율: 25%</small>
                            </li>
                            <li class="list-group-item px-0">
                                <div class="d-flex w-100 justify-content-between">
                                    <h5 class="mb-1">추가 안전 장비 도입</h5>
                                    <span class="badge bg-warning">중간 우선순위</span>
                                </div>
                                <p class="mb-1">작업자용 선진 안전모 및 추락방지 장비로 업그레이드하는 것을 고려하세요.</p>
                                <small class="text-muted">예상 위험 감소율: 18%</small>
                            </li>
                            <li class="list-group-item px-0">
                                <div class="d-flex w-100 justify-content-between">
                                    <h5 class="mb-1">정기 점검 빈도 증가</h5>
                                    <span class="badge bg-warning">중간 우선순위</span>
                                </div>
                                <p class="mb-1">전기 설비 점검 주기를 3개월에서 1개월로 단축하는 것을 권장합니다.</p>
                                <small class="text-muted">예상 위험 감소율: 15%</small>
                            </li>
                            <li class="list-group-item px-0">
                                <div class="d-flex w-100 justify-content-between">
                                    <h5 class="mb-1">작업 프로세스 개선</h5>
                                    <span class="badge bg-primary">낮은 우선순위</span>
                                </div>
                                <p class="mb-1">고위험 작업에 대한 표준 작업 절차를 재검토하고 업데이트하는 것을 권장합니다.</p>
                                <small class="text-muted">예상 위험 감소율: 10%</small>
                            </li>
                        </ul>
                        
                        <div class="text-center mt-4">
                            <a href="#" class="btn btn-outline-primary">맞춤형 개선 계획 생성</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- 위험성 상세 분석 도구 섹션 -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="dashboard-card">
                    <div class="card-header dashboard-card-header">
                        <h2 class="h4 mb-0">위험성 심층 분석 도구</h2>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3 mb-md-0">
                                <div class="card h-100">
                                    <div class="card-body text-center">
                                        <i class="fas fa-microscope text-primary fs-1 mb-3"></i>
                                        <h3 class="h5">위험 요소 상세 분석</h3>
                                        <p class="card-text">특정 위험 요소에 대한 심층 분석을 수행합니다.</p>
                                        <a href="#" class="btn btn-sm btn-outline-primary">분석 시작</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3 mb-md-0">
                                <div class="card h-100">
                                    <div class="card-body text-center">
                                        <i class="fas fa-project-diagram text-primary fs-1 mb-3"></i>
                                        <h3 class="h5">위험 요소 상관관계 분석</h3>
                                        <p class="card-text">위험 요소 간의 연관성을 파악하고 근본 원인을 분석합니다.</p>
                                        <a href="#" class="btn btn-sm btn-outline-primary">상관관계 보기</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card h-100">
                                    <div class="card-body text-center">
                                        <i class="fas fa-brain text-primary fs-1 mb-3"></i>
                                        <h3 class="h5">AI 안전 시나리오 시뮬레이션</h3>
                                        <p class="card-text">다양한 안전 조치의 효과를 시뮬레이션하여 최적의 솔루션을 찾습니다.</p>
                                        <a href="#" class="btn btn-sm btn-outline-primary">시뮬레이션 실행</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- AI 분석 방법론 모달 -->
    <div class="modal fade" id="aiMethodologyModal" tabindex="-1" aria-labelledby="aiMethodologyModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="aiMethodologyModalLabel">AI 분석 방법론</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h6 class="fw-bold mb-3">AI 위험성 평가 분석 프로세스</h6>
                    <p>KRDS 위험성평가 시스템은 다음과 같은 방법론을 통해 AI 기반 분석을 수행합니다:</p>
                    
                    <div class="d-flex mb-3">
                        <div class="me-3">
                            <div class="bg-primary rounded-circle text-white d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">1</div>
                        </div>
                        <div>
                            <h6 class="fw-bold">데이터 수집 및 전처리</h6>
                            <p>위험성 평가 데이터, 사고 기록, 안전 점검 결과 등 다양한 소스에서 데이터를 수집하고 정제합니다.</p>
                        </div>
                    </div>
                    
                    <div class="d-flex mb-3">
                        <div class="me-3">
                            <div class="bg-primary rounded-circle text-white d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">2</div>
                        </div>
                        <div>
                            <h6 class="fw-bold">패턴 인식 및 상관관계 분석</h6>
                            <p>머신러닝 알고리즘을 활용하여 위험 요소 간의 패턴과 상관관계를 분석합니다.</p>
                        </div>
                    </div>
                    
                    <div class="d-flex mb-3">
                        <div class="me-3">
                            <div class="bg-primary rounded-circle text-white d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">3</div>
                        </div>
                        <div>
                            <h6 class="fw-bold">예측 모델링</h6>
                            <p>과거 데이터를 기반으로 미래의 위험성을 예측하는 모델을 구축합니다.</p>
                        </div>
                    </div>
                    
                    <div class="d-flex mb-3">
                        <div class="me-3">
                            <div class="bg-primary rounded-circle text-white d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">4</div>
                        </div>
                        <div>
                            <h6 class="fw-bold">개선 사항 도출</h6>
                            <p>분석 결과를 바탕으로 위험 감소를 위한 최적의 조치를 제안합니다.</p>
                        </div>
                    </div>
                    
                    <div class="d-flex mb-4">
                        <div class="me-3">
                            <div class="bg-primary rounded-circle text-white d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">5</div>
                        </div>
                        <div>
                            <h6 class="fw-bold">효과 검증 및 지속적 개선</h6>
                            <p>제안된 조치의 효과를 검증하고, 결과를 시스템에 피드백하여 지속적으로 개선합니다.</p>
                        </div>
                    </div>
                    
                    <div class="alert alert-info" role="alert">
                        <h6 class="fw-bold">데이터 보안 및 개인정보 보호</h6>
                        <p class="mb-0">KRDS는 모든 데이터 처리 과정에서 개인정보 보호법과 정보보안 정책을 준수합니다. 수집된 데이터는 익명화되어 처리되며, 안전한 환경에서 분석됩니다.</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">이해했습니다</button>
                </div>
            </div>
        </div>
    </div>

    <footer class="footer bg-navy text-white py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-6 mb-4 mb-md-0">
                    <h4 class="footer-title">KRDS 위험성평가 시스템</h4>
                    <p>AI 기술을 활용한 효율적이고 정확한 위험성 평가 솔루션</p>
                </div>
                <div class="col-md-3 mb-4 mb-md-0">
                    <h5 class="footer-subtitle">바로가기</h5>
                    <ul class="footer-links">
                        <li><a href="index.php">대시보드</a></li>
                        <li><a href="assessments.php">평가 관리</a></li>
                        <li><a href="reports.php">보고서</a></li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h5 class="footer-subtitle">고객 지원</h5>
                    <ul class="footer-links">
                        <li><a href="#">이용 가이드</a></li>
                        <li><a href="#">FAQ</a></li>
                        <li><a href="#">고객센터</a></li>
                    </ul>
                </div>
            </div>
            <hr class="footer-divider">
            <div class="row">
                <div class="col-md-6 text-center text-md-start">
                    <p class="mb-0">&copy; 2023 KRDS 위험성평가 시스템. All Rights Reserved.</p>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <div class="footer-social">
                        <a href="#" class="social-link"><i class="fab fa-facebook-f"></i><span class="visually-hidden">페이스북</span></a>
                        <a href="#" class="social-link"><i class="fab fa-twitter"></i><span class="visually-hidden">트위터</span></a>
                        <a href="#" class="social-link"><i class="fab fa-instagram"></i><span class="visually-hidden">인스타그램</span></a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // 위험성 추세 차트
        const riskTrendCtx = document.getElementById('riskTrendChart').getContext('2d');
        new Chart(riskTrendCtx, {
            type: 'line',
            data: {
                labels: ['1월', '2월', '3월', '4월', '5월', '6월'],
                datasets: [
                    {
                        label: '추락 위험',
                        data: [65, 60, 68, 74, 80, 85],
                        borderColor: '#FF6384',
                        backgroundColor: 'rgba(255, 99, 132, 0.1)',
                        tension: 0.3,
                        fill: true
                    },
                    {
                        label: '전기 위험',
                        data: [45, 50, 55, 60, 62, 65],
                        borderColor: '#FFCD56',
                        backgroundColor: 'rgba(255, 205, 86, 0.1)',
                        tension: 0.3,
                        fill: true
                    },
                    {
                        label: '화재 위험',
                        data: [60, 55, 50, 45, 35, 30],
                        borderColor: '#4BC0C0',
                        backgroundColor: 'rgba(75, 192, 192, 0.1)',
                        tension: 0.3,
                        fill: true
                    },
                    {
                        label: '기계적 위험',
                        data: [40, 45, 48, 50, 52, 55],
                        borderColor: '#36A2EB',
                        backgroundColor: 'rgba(54, 162, 235, 0.1)',
                        tension: 0.3,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: false
                    },
                    legend: {
                        position: 'bottom'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: '위험 지수'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: '월'
                        }
                    }
                }
            }
        });

        // 위험 카테고리 분포 차트
        const riskCategoryCtx = document.getElementById('riskCategoryChart').getContext('2d');
        new Chart(riskCategoryCtx, {
            type: 'doughnut',
            data: {
                labels: ['추락', '전기', '화재', '기계적', '화학물질', '기타'],
                datasets: [{
                    data: [35, 25, 15, 12, 8, 5],
                    backgroundColor: [
                        '#0050a2',
                        '#0078d4',
                        '#4ba0ff',
                        '#8dc2ff',
                        '#bfdaff',
                        '#e6f2ff'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    });
    </script>
</body>
</html> 