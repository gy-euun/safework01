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
    <meta name="description" content="AI 기반 위험성평가 시스템 - 활동 내역">
    <title>활동 내역 - AI 기반 위험성평가 시스템 | RISK NINE</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <!-- 웹 접근성 개선을 위한 스크립트 -->
    <script src="https://cdn.jsdelivr.net/npm/focus-visible@5.2.0/dist/focus-visible.min.js" defer></script>
</head>
<body>
    <!-- 건너뛰기 링크 (접근성) -->
    <a href="#main-content" class="skip-link">본문 바로가기</a>
    
    <nav class="navbar navbar-expand-lg navbar-dark bg-navy">
        <div class="container">
            <a class="navbar-brand" href="../index.php">
                <span class="brand-logo"><span class="risk">RISK</span> <span class="nine">NINE</span></span>
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
                        <a class="nav-link" href="ai_analysis.php">
                            <i class="fas fa-robot me-1"></i> AI 분석
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="activities.php">
                            <i class="fas fa-history me-1"></i> 활동 내역
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
                <li class="breadcrumb-item active" aria-current="page">활동 내역</li>
            </ol>
        </nav>

        <div class="row mb-4">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center">
                    <h1>활동 내역</h1>
                    <div>
                        <button type="button" class="btn btn-outline-primary me-2" id="exportActivities">
                            <i class="fas fa-file-export me-1"></i> 내보내기
                        </button>
                        <button type="button" class="btn btn-outline-secondary" id="filterActivities" data-bs-toggle="modal" data-bs-target="#filterModal">
                            <i class="fas fa-filter me-1"></i> 필터
                        </button>
                    </div>
                </div>
                <p class="text-muted">위험성 평가 시스템의 모든 활동 로그와 변경 내역을 확인할 수 있습니다.</p>
            </div>
        </div>

        <!-- 활동 요약 카드 -->
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="dashboard-card h-100">
                    <div class="card-body text-center">
                        <div class="dashboard-icon">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <h3 class="h5">오늘 활동</h3>
                        <p class="fs-3 fw-bold text-primary">12</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="dashboard-card h-100">
                    <div class="card-body text-center">
                        <div class="dashboard-icon">
                            <i class="fas fa-calendar-week"></i>
                        </div>
                        <h3 class="h5">이번 주 활동</h3>
                        <p class="fs-3 fw-bold text-primary">58</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="dashboard-card h-100">
                    <div class="card-body text-center">
                        <div class="dashboard-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <h3 class="h5">활성 사용자</h3>
                        <p class="fs-3 fw-bold text-primary">15</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="dashboard-card h-100">
                    <div class="card-body text-center">
                        <div class="dashboard-icon">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <h3 class="h5">수정된 문서</h3>
                        <p class="fs-3 fw-bold text-primary">24</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- 활동 타임라인 -->
        <div class="card dashboard-card mb-4">
            <div class="card-header dashboard-card-header d-flex justify-content-between align-items-center">
                <h2 class="h4 mb-0">활동 타임라인</h2>
                <div class="btn-group" role="group" aria-label="기간 선택">
                    <button type="button" class="btn btn-sm btn-outline-primary active">오늘</button>
                    <button type="button" class="btn btn-sm btn-outline-primary">이번 주</button>
                    <button type="button" class="btn btn-sm btn-outline-primary">이번 달</button>
                    <button type="button" class="btn btn-sm btn-outline-primary">전체</button>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    <div class="list-group-item">
                        <div class="d-flex w-100 justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <div class="bg-primary rounded-circle p-2 me-3">
                                    <i class="fas fa-plus text-white"></i>
                                </div>
                                <div>
                                    <h3 class="h6 mb-0">새 평가 생성</h3>
                                    <p class="mb-0 small text-muted">홍길동님이 '외벽작업 안전대책' 평가를 생성했습니다.</p>
                                </div>
                            </div>
                            <span class="badge bg-light text-dark">오늘 14:25</span>
                        </div>
                    </div>
                    <div class="list-group-item">
                        <div class="d-flex w-100 justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <div class="bg-success rounded-circle p-2 me-3">
                                    <i class="fas fa-check text-white"></i>
                                </div>
                                <div>
                                    <h3 class="h6 mb-0">평가 완료</h3>
                                    <p class="mb-0 small text-muted">김철수님이 '기초공사 안전진단' 평가를 완료했습니다.</p>
                                </div>
                            </div>
                            <span class="badge bg-light text-dark">오늘 11:40</span>
                        </div>
                    </div>
                    <div class="list-group-item">
                        <div class="d-flex w-100 justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <div class="bg-info rounded-circle p-2 me-3">
                                    <i class="fas fa-edit text-white"></i>
                                </div>
                                <div>
                                    <h3 class="h6 mb-0">평가 업데이트</h3>
                                    <p class="mb-0 small text-muted">이영희님이 '전기배선 위험성평가' 내용을 수정했습니다.</p>
                                </div>
                            </div>
                            <span class="badge bg-light text-dark">오늘 10:15</span>
                        </div>
                    </div>
                    <div class="list-group-item">
                        <div class="d-flex w-100 justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <div class="bg-warning rounded-circle p-2 me-3">
                                    <i class="fas fa-comment-dots text-white"></i>
                                </div>
                                <div>
                                    <h3 class="h6 mb-0">의견 추가</h3>
                                    <p class="mb-0 small text-muted">박지민님이 '화재위험 평가'에 의견을 추가했습니다.</p>
                                </div>
                            </div>
                            <span class="badge bg-light text-dark">오늘 09:30</span>
                        </div>
                    </div>
                    <div class="list-group-item">
                        <div class="d-flex w-100 justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <div class="bg-danger rounded-circle p-2 me-3">
                                    <i class="fas fa-exclamation-triangle text-white"></i>
                                </div>
                                <div>
                                    <h3 class="h6 mb-0">고위험 항목 식별</h3>
                                    <p class="mb-0 small text-muted">AI 시스템이 '고소작업 안전대책'에서 고위험 항목을 식별했습니다.</p>
                                </div>
                            </div>
                            <span class="badge bg-light text-dark">어제 17:45</span>
                        </div>
                    </div>
                    <div class="list-group-item">
                        <div class="d-flex w-100 justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <div class="bg-secondary rounded-circle p-2 me-3">
                                    <i class="fas fa-file-pdf text-white"></i>
                                </div>
                                <div>
                                    <h3 class="h6 mb-0">보고서 생성</h3>
                                    <p class="mb-0 small text-muted">김미나님이 '6월 위험성 평가 보고서'를 생성했습니다.</p>
                                </div>
                            </div>
                            <span class="badge bg-light text-dark">어제 15:20</span>
                        </div>
                    </div>
                    <div class="list-group-item">
                        <div class="d-flex w-100 justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <div class="bg-info rounded-circle p-2 me-3">
                                    <i class="fas fa-share-alt text-white"></i>
                                </div>
                                <div>
                                    <h3 class="h6 mb-0">보고서 공유</h3>
                                    <p class="mb-0 small text-muted">홍길동님이 '안전난간 설치공사 위험성 보고서'를 외부 이해관계자와 공유했습니다.</p>
                                </div>
                            </div>
                            <span class="badge bg-light text-dark">어제 14:05</span>
                        </div>
                    </div>
                    <div class="list-group-item">
                        <div class="d-flex w-100 justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <div class="bg-primary rounded-circle p-2 me-3">
                                    <i class="fas fa-robot text-white"></i>
                                </div>
                                <div>
                                    <h3 class="h6 mb-0">AI 분석 완료</h3>
                                    <p class="mb-0 small text-muted">AI 시스템이 '지하실 환기 시스템 평가'에 대한 심층 분석을 완료했습니다.</p>
                                </div>
                            </div>
                            <span class="badge bg-light text-dark">어제 10:30</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer text-center">
                <a href="#" class="btn btn-sm btn-outline-primary">더 많은 활동 보기</a>
            </div>
        </div>

        <!-- 사용자별 활동 -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="dashboard-card h-100">
                    <div class="card-header dashboard-card-header">
                        <h2 class="h4 mb-0">사용자별 활동</h2>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th scope="col">사용자</th>
                                        <th scope="col">활동 수</th>
                                        <th scope="col">최근 활동</th>
                                        <th scope="col">활동 유형</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>홍길동</td>
                                        <td>24</td>
                                        <td>오늘 14:25</td>
                                        <td><span class="badge bg-primary">평가 생성</span></td>
                                    </tr>
                                    <tr>
                                        <td>김철수</td>
                                        <td>18</td>
                                        <td>오늘 11:40</td>
                                        <td><span class="badge bg-success">평가 완료</span></td>
                                    </tr>
                                    <tr>
                                        <td>이영희</td>
                                        <td>15</td>
                                        <td>오늘 10:15</td>
                                        <td><span class="badge bg-info">평가 수정</span></td>
                                    </tr>
                                    <tr>
                                        <td>박지민</td>
                                        <td>12</td>
                                        <td>오늘 09:30</td>
                                        <td><span class="badge bg-warning">의견 추가</span></td>
                                    </tr>
                                    <tr>
                                        <td>김미나</td>
                                        <td>10</td>
                                        <td>어제 15:20</td>
                                        <td><span class="badge bg-secondary">보고서 생성</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="dashboard-card h-100">
                    <div class="card-header dashboard-card-header">
                        <h2 class="h4 mb-0">활동 유형 분포</h2>
                    </div>
                    <div class="card-body">
                        <canvas id="activityTypeChart" height="250" aria-label="활동 유형 분포 차트" role="img"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- 시스템 알림 -->
        <div class="card dashboard-card mb-4">
            <div class="card-header dashboard-card-header">
                <h2 class="h4 mb-0">시스템 알림</h2>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    <div class="list-group-item">
                        <div class="d-flex w-100 justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <div class="bg-danger rounded-circle p-2 me-3">
                                    <i class="fas fa-bell text-white"></i>
                                </div>
                                <div>
                                    <h3 class="h6 mb-0">긴급 알림</h3>
                                    <p class="mb-0 small text-muted">'고소작업 안전대책' 평가에서 발견된 고위험 항목에 대한 조치가 필요합니다.</p>
                                </div>
                            </div>
                            <div>
                                <span class="badge bg-light text-dark me-2">오늘 12:30</span>
                                <button type="button" class="btn btn-sm btn-outline-danger">조치</button>
                            </div>
                        </div>
                    </div>
                    <div class="list-group-item">
                        <div class="d-flex w-100 justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <div class="bg-warning rounded-circle p-2 me-3">
                                    <i class="fas fa-bell text-white"></i>
                                </div>
                                <div>
                                    <h3 class="h6 mb-0">평가 마감 임박</h3>
                                    <p class="mb-0 small text-muted">'전기배선 위험성평가'의 마감일이 2일 남았습니다.</p>
                                </div>
                            </div>
                            <div>
                                <span class="badge bg-light text-dark me-2">어제 09:15</span>
                                <button type="button" class="btn btn-sm btn-outline-warning">확인</button>
                            </div>
                        </div>
                    </div>
                    <div class="list-group-item">
                        <div class="d-flex w-100 justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <div class="bg-info rounded-circle p-2 me-3">
                                    <i class="fas fa-bell text-white"></i>
                                </div>
                                <div>
                                    <h3 class="h6 mb-0">시스템 업데이트</h3>
                                    <p class="mb-0 small text-muted">위험성 평가 시스템이 새 버전으로 업데이트되었습니다. 새로운 기능을 확인해보세요.</p>
                                </div>
                            </div>
                            <div>
                                <span class="badge bg-light text-dark me-2">3일 전</span>
                                <button type="button" class="btn btn-sm btn-outline-info">세부정보</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 필터 모달 -->
    <div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="filterModalLabel">활동 필터링</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="filterForm">
                        <div class="mb-3">
                            <label for="activityType" class="form-label">활동 유형</label>
                            <select class="form-select" id="activityType" multiple size="5">
                                <option value="create" selected>평가 생성</option>
                                <option value="update" selected>평가 수정</option>
                                <option value="complete" selected>평가 완료</option>
                                <option value="comment" selected>의견 추가</option>
                                <option value="report" selected>보고서 생성</option>
                                <option value="risk" selected>위험 식별</option>
                                <option value="share" selected>보고서 공유</option>
                                <option value="ai" selected>AI 분석</option>
                            </select>
                            <div class="form-text">Ctrl 키를 누른 상태에서 여러 항목을 선택할 수 있습니다.</div>
                        </div>
                        <div class="mb-3">
                            <label for="dateRange" class="form-label">기간</label>
                            <select class="form-select" id="dateRange">
                                <option value="today" selected>오늘</option>
                                <option value="yesterday">어제</option>
                                <option value="week">이번 주</option>
                                <option value="month">이번 달</option>
                                <option value="custom">직접 지정</option>
                            </select>
                        </div>
                        <div class="row mb-3" id="customDateRange" style="display: none;">
                            <div class="col-6">
                                <label for="startDate" class="form-label">시작일</label>
                                <input type="date" class="form-control" id="startDate">
                            </div>
                            <div class="col-6">
                                <label for="endDate" class="form-label">종료일</label>
                                <input type="date" class="form-control" id="endDate">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="users" class="form-label">사용자</label>
                            <select class="form-select" id="users" multiple size="5">
                                <option value="all" selected>모든 사용자</option>
                                <option value="홍길동">홍길동</option>
                                <option value="김철수">김철수</option>
                                <option value="이영희">이영희</option>
                                <option value="박지민">박지민</option>
                                <option value="김미나">김미나</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">취소</button>
                    <button type="button" class="btn btn-primary" id="applyFilter">필터 적용</button>
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // 활동 유형 분포 차트
        const activityTypeChart = document.getElementById('activityTypeChart').getContext('2d');
        new Chart(activityTypeChart, {
            type: 'pie',
            data: {
                labels: ['평가 생성', '평가 수정', '평가 완료', '의견 추가', '보고서 생성', '위험 식별', '보고서 공유', 'AI 분석'],
                datasets: [{
                    data: [25, 20, 15, 12, 10, 8, 5, 5],
                    backgroundColor: [
                        '#0050a2',
                        '#0078d4',
                        '#36a0eb',
                        '#4ba0ff',
                        '#6eb5ff',
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
                        position: 'bottom',
                        labels: {
                            boxWidth: 15
                        }
                    }
                }
            }
        });

        // 필터 모달에서 날짜 범위 변경 시 커스텀 날짜 필드 표시/숨김 처리
        document.getElementById('dateRange').addEventListener('change', function() {
            const customDateRange = document.getElementById('customDateRange');
            if (this.value === 'custom') {
                customDateRange.style.display = 'flex';
            } else {
                customDateRange.style.display = 'none';
            }
        });

        // 필터 적용 버튼 클릭 시
        document.getElementById('applyFilter').addEventListener('click', function() {
            // 실제 구현에서는 여기에 필터링 로직을 추가합니다.
            alert('필터가 적용되었습니다. 실제 구현에서는 활동 목록이 필터링됩니다.');
            
            // 모달 닫기
            const modal = bootstrap.Modal.getInstance(document.getElementById('filterModal'));
            modal.hide();
        });

        // 활동 내보내기 버튼 클릭 시
        document.getElementById('exportActivities').addEventListener('click', function() {
            alert('활동 내역을 내보냅니다. 실제 구현에서는 CSV 또는 PDF 파일로 다운로드됩니다.');
        });
    });
    </script>
</body>
</html> 