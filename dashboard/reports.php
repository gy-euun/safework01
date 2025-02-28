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
    <meta name="description" content="AI 기반 위험성평가 시스템 - 보고서 생성 및 관리">
    <title>보고서 - AI 기반 위험성평가 시스템 | Risk Nain</title>
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
                        <a class="nav-link active" href="reports.php">
                            <i class="fas fa-chart-pie me-1"></i> 보고서
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
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="mb-1">보고서</h1>
                <p class="text-muted">위험성 평가 보고서 생성 및 관리</p>
            </div>
            <div>
                <a href="generate_report.php" class="btn btn-primary">
                    <i class="fas fa-file-alt me-1"></i> 새 보고서 생성
                </a>
            </div>
        </div>

        <!-- 보고서 템플릿 섹션 -->
        <h2 class="h4 mb-3">보고서 템플릿</h2>
        <div class="row g-4 mb-5">
            <div class="col-md-4">
                <div class="dashboard-card h-100">
                    <div class="card-body">
                        <h3 class="h5 mb-3"><i class="fas fa-file-contract text-primary me-2"></i> 정기 위험성 평가 보고서</h3>
                        <p class="card-text">전체 위험성 평가 결과를 종합적으로 보여주는 표준 보고서 템플릿입니다.</p>
                        <a href="#" class="btn btn-sm btn-outline-primary">템플릿 사용</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="dashboard-card h-100">
                    <div class="card-body">
                        <h3 class="h5 mb-3"><i class="fas fa-exclamation-triangle text-warning me-2"></i> 고위험 항목 분석 보고서</h3>
                        <p class="card-text">높은 위험 수준으로 평가된 항목들에 대한 상세 분석 보고서입니다.</p>
                        <a href="#" class="btn btn-sm btn-outline-primary">템플릿 사용</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="dashboard-card h-100">
                    <div class="card-body">
                        <h3 class="h5 mb-3"><i class="fas fa-chart-line text-success me-2"></i> 위험성 추세 분석 보고서</h3>
                        <p class="card-text">기간별 위험성 평가 결과의 변화 추이를 분석하는 보고서입니다.</p>
                        <a href="#" class="btn btn-sm btn-outline-primary">템플릿 사용</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- 최근 생성된 보고서 -->
        <h2 class="h4 mb-3">최근 생성된 보고서</h2>
        <div class="card dashboard-card mb-5">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th scope="col">보고서명</th>
                                <th scope="col">유형</th>
                                <th scope="col">생성일</th>
                                <th scope="col">작성자</th>
                                <th scope="col">상태</th>
                                <th scope="col">작업</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><a href="#" class="text-decoration-none">6월 위험성 평가 보고서</a></td>
                                <td>정기 보고서</td>
                                <td>2023-06-18</td>
                                <td>홍길동</td>
                                <td><span class="badge bg-success">완료</span></td>
                                <td>
                                    <div class="btn-group">
                                        <a href="#" class="btn btn-sm btn-outline-primary" title="보기">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="#" class="btn btn-sm btn-outline-secondary" title="다운로드">
                                            <i class="fas fa-download"></i>
                                        </a>
                                        <a href="#" class="btn btn-sm btn-outline-danger" title="삭제">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td><a href="#" class="text-decoration-none">안전난간 설치공사 위험성 보고서</a></td>
                                <td>프로젝트 보고서</td>
                                <td>2023-06-15</td>
                                <td>김철수</td>
                                <td><span class="badge bg-success">완료</span></td>
                                <td>
                                    <div class="btn-group">
                                        <a href="#" class="btn btn-sm btn-outline-primary" title="보기">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="#" class="btn btn-sm btn-outline-secondary" title="다운로드">
                                            <i class="fas fa-download"></i>
                                        </a>
                                        <a href="#" class="btn btn-sm btn-outline-danger" title="삭제">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td><a href="#" class="text-decoration-none">2023년 2분기 고위험 항목 분석</a></td>
                                <td>분석 보고서</td>
                                <td>2023-06-10</td>
                                <td>이영희</td>
                                <td><span class="badge bg-warning">검토 중</span></td>
                                <td>
                                    <div class="btn-group">
                                        <a href="#" class="btn btn-sm btn-outline-primary" title="보기">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="#" class="btn btn-sm btn-outline-secondary" title="다운로드">
                                            <i class="fas fa-download"></i>
                                        </a>
                                        <a href="#" class="btn btn-sm btn-outline-danger" title="삭제">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- 보고서 요약 및 통계 -->
        <h2 class="h4 mb-3">보고서 데이터 요약</h2>
        <div class="row g-4">
            <div class="col-md-8">
                <div class="dashboard-card">
                    <div class="card-header dashboard-card-header d-flex justify-content-between align-items-center">
                        <h3 class="h5 mb-0">위험 카테고리별 보고서 분포</h3>
                        <div class="btn-group" role="group" aria-label="차트 유형 선택">
                            <button type="button" class="btn btn-sm btn-outline-primary active">파이</button>
                            <button type="button" class="btn btn-sm btn-outline-primary">막대</button>
                        </div>
                    </div>
                    <div class="card-body">
                        <canvas id="reportDistribution" height="300" role="img" aria-label="위험 카테고리별 보고서 분포를 보여주는 차트"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="dashboard-card">
                    <div class="card-header dashboard-card-header">
                        <h3 class="h5 mb-0">보고서 통계</h3>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                총 보고서 수
                                <span class="badge bg-primary rounded-pill">24</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                정기 보고서
                                <span class="badge bg-primary rounded-pill">12</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                프로젝트 보고서
                                <span class="badge bg-primary rounded-pill">8</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                분석 보고서
                                <span class="badge bg-primary rounded-pill">4</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                이번 달 생성 보고서
                                <span class="badge bg-success rounded-pill">5</span>
                            </li>
                        </ul>
                    </div>
                    <div class="card-footer text-center">
                        <a href="#" class="btn btn-sm btn-outline-primary">상세 통계 보기</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="footer bg-navy text-white py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-6 mb-4 mb-md-0">
                    <h4 class="footer-title">Risk Nain 위험성평가 시스템</h4>
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
                    <p class="mb-0">&copy; 2023 Risk Nain 위험성평가 시스템. All Rights Reserved.</p>
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
        // 위험 카테고리별 보고서 분포 차트
        const reportChart = document.getElementById('reportDistribution').getContext('2d');
        new Chart(reportChart, {
            type: 'pie',
            data: {
                labels: ['추락', '전기', '화재', '충돌', '기계적 위험', '기타'],
                datasets: [{
                    data: [35, 20, 15, 10, 15, 5],
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