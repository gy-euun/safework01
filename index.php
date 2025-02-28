<?php include 'includes/config.php'; ?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="RISK NINE - 지능형 위험성평가 시스템 | 안전한 작업 환경을 위한 AI 기반 솔루션">
    <title>RISK NINE - 지능형 위험성평가 시스템</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+KR:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- 웹 접근성 개선을 위한 스크립트 -->
    <script src="https://cdn.jsdelivr.net/npm/focus-visible@5.2.0/dist/focus-visible.min.js" defer></script>
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
                            <a class="nav-link" href="#service">서비스 소개</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#features">주요 기능</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#process">이용 절차</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#support">고객 지원</a>
                        </li>
                        <?php if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true): ?>
                            <li class="nav-item ms-lg-2">
                                <a class="nav-link btn btn-light px-3" href="dashboard/">
                                    <i class="fas fa-tachometer-alt me-1"></i> 대시보드
                                </a>
                            </li>
                        <?php else: ?>
                            <li class="nav-item ms-lg-2">
                                <a class="nav-link btn btn-outline-light px-3" href="login.php">
                                    로그인
                                </a>
                            </li>
                            <li class="nav-item ms-lg-2">
                                <a class="nav-link btn btn-accent px-3" href="register.php">
                                    회원가입
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <!-- 메인 섹션 -->
    <main id="main-content">
        <!-- 히어로 섹션 -->
        <section class="hero bg-gradient-primary text-white py-5">
            <div class="container py-5">
                <div class="row align-items-center">
                    <div class="col-lg-6">
                        <h1 class="fw-bold mb-4">AI 기반 위험성평가로<br>더 안전한 작업 환경 구축</h1>
                        <p class="mb-4">RISK NINE은 최신 인공지능 기술로 위험 요소를 식별하고 효과적인 대응 방안을 제시하여 산업 현장의 안전성을 향상시킵니다.</p>
                        <div class="mt-4">
                            <a href="register.php" class="btn btn-light btn-lg me-2">
                                시작하기
                            </a>
                            <a href="#features" class="btn btn-outline-light btn-lg">
                                자세히 보기
                            </a>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <img src="assets/images/MAIN-B.png" alt="RISK NINE AI 위험성평가 시스템" class="img-fluid hero-image">
                    </div>
                </div>
            </div>
        </section>

        <!-- 실적 및 통계 섹션 -->
        <section id="stats" class="py-5 bg-navy text-white">
            <div class="container py-4">
                <div class="row justify-content-center mb-5">
                    <div class="col-lg-8 text-center">
                        <h2 class="section-title text-white">실적 및 통계</h2>
                        <p class="section-description text-white-50">RISK NINE이 산업 현장의 안전을 얼마나 향상시켰는지 확인하세요.</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
                        <div class="stat-card text-center">
                            <h3 class="stat-number" data-count="42">0</h3>
                            <p>%</p>
                            <h4 class="stat-title">사고율 감소</h4>
                            <p class="stat-desc">RISK NINE 도입 기업의<br>평균 사고율 감소</p>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
                        <div class="stat-card text-center">
                            <h3 class="stat-number" data-count="1250">0</h3>
                            <p>+</p>
                            <h4 class="stat-title">활성 사용자</h4>
                            <p class="stat-desc">다양한 산업 분야에서<br>활용 중인 사용자 수</p>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
                        <div class="stat-card text-center">
                            <h3 class="stat-number" data-count="85">0</h3>
                            <p>%</p>
                            <h4 class="stat-title">관리 효율성</h4>
                            <p class="stat-desc">안전 관리 효율성<br>평균 향상율</p>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
                        <div class="stat-card text-center">
                            <h3 class="stat-number" data-count="23500">0</h3>
                            <p>+</p>
                            <h4 class="stat-title">위험요소 식별</h4>
                            <p class="stat-desc">AI가 자동 식별한<br>잠재적 위험 요소 수</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 서비스 소개 섹션 -->
        <section id="service" class="py-5">
            <div class="container py-4">
                <div class="row justify-content-center mb-5">
                    <div class="col-lg-8 text-center">
                        <h2 class="section-title">서비스 소개</h2>
                        <p class="section-description">산업 현장에서의 안전은 타협할 수 없는 가치입니다. RISK NINE은 복잡한 위험성 평가 과정을 혁신적으로 개선합니다.</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-4">
                        <div class="gov-card text-center">
                            <div class="mb-4">
                                <i class="fas fa-chart-line fa-3x text-primary"></i>
                            </div>
                            <h3 class="h5 mb-3">데이터 기반 분석</h3>
                            <p>방대한 데이터를 분석하여 위험 요소를 사전에 식별하고 예방합니다.</p>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="gov-card text-center">
                            <div class="mb-4">
                                <i class="fas fa-robot fa-3x text-primary"></i>
                            </div>
                            <h3 class="h5 mb-3">AI 추천 시스템</h3>
                            <p>인공지능이 최적의 대응 방안을 추천하여 의사결정을 돕습니다.</p>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="gov-card text-center">
                            <div class="mb-4">
                                <i class="fas fa-shield-alt fa-3x text-primary"></i>
                            </div>
                            <h3 class="h5 mb-3">안전 관리 시스템</h3>
                            <p>체계적인 안전 관리로 사고 발생률을 현저히 낮춥니다.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 비전 및 미션 섹션 -->
        <section id="vision" class="py-5 bg-light">
            <div class="container py-4">
                <div class="row justify-content-center mb-5">
                    <div class="col-lg-8 text-center">
                        <h2 class="section-title">비전 및 미션</h2>
                        <p class="section-description">RISK NINE이 추구하는 가치와 목표를 소개합니다.</p>
                    </div>
                </div>
                <div class="row align-items-center">
                    <div class="col-lg-6 mb-4 mb-lg-0">
                        <div class="vision-card">
                            <div class="vision-card-body">
                                <h3 class="vision-title">비전</h3>
                                <p class="vision-text">"모든 산업 현장에서 근로자가 안전하게 일할 수 있는 환경 구축"</p>
                                <hr class="vision-divider">
                                <h4 class="vision-subtitle">우리의 비전</h4>
                                <p>RISK NINE은 AI 기술을 활용하여 모든 산업 현장의 위험성을 최소화하고, 더 안전한 작업 환경을 구축하는 데 기여합니다. 우리는 작업 현장에서 발생할 수 있는 모든 사고를 예방하고, 근로자의 생명과 건강을 지키는 것을 최우선으로 합니다.</p>
                                <div class="vision-icon-container">
                                    <div class="vision-icon">
                                        <i class="fas fa-eye"></i>
                                    </div>
                                    <div class="vision-icon">
                                        <i class="fas fa-chart-line"></i>
                                    </div>
                                    <div class="vision-icon">
                                        <i class="fas fa-handshake"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mission-card">
                            <div class="mission-card-body">
                                <h3 class="mission-title">미션</h3>
                                <p class="mission-text">"위험을 9단계 낮춰 안전을 높이다"</p>
                                <hr class="mission-divider">
                                <h4 class="mission-subtitle">우리의 사명</h4>
                                <ul class="mission-list">
                                    <li><i class="fas fa-check"></i> 최첨단 AI 기술로 위험 요소를 사전에 식별하고 예방합니다.</li>
                                    <li><i class="fas fa-check"></i> 데이터 기반의 의사결정으로 효율적인 안전 관리를 지원합니다.</li>
                                    <li><i class="fas fa-check"></i> 모든 산업 분야에 특화된 맞춤형 솔루션을 제공합니다.</li>
                                    <li><i class="fas fa-check"></i> 쉽고 직관적인 인터페이스로 누구나 사용할 수 있는 시스템을 구축합니다.</li>
                                    <li><i class="fas fa-check"></i> 지속적인 학습과 개선으로 안전 관리의 새로운 표준을 제시합니다.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 주요 기능 섹션 -->
        <section id="features" class="py-5 bg-light">
            <div class="container py-4">
                <div class="row justify-content-center mb-5">
                    <div class="col-lg-8 text-center">
                        <h2 class="section-title">주요 기능</h2>
                        <p class="section-description">RISK NINE의 핵심 기능을 통해 산업 현장의 안전을 강화하세요.</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <div class="gov-card">
                            <div class="row">
                                <div class="col-auto">
                                    <div class="feature-icon bg-primary-light">
                                        <i class="fas fa-search text-primary"></i>
                                    </div>
                                </div>
                                <div class="col">
                                    <h3 class="h5 mb-2">위험 요소 식별</h3>
                                    <p class="mb-0">AI 알고리즘을 활용하여 작업 환경의 잠재적 위험 요소를 자동으로 식별합니다.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4">
                        <div class="gov-card">
                            <div class="row">
                                <div class="col-auto">
                                    <div class="feature-icon bg-primary-light">
                                        <i class="fas fa-chart-bar text-primary"></i>
                                    </div>
                                </div>
                                <div class="col">
                                    <h3 class="h5 mb-2">위험도 평가</h3>
                                    <p class="mb-0">식별된 위험 요소의 심각도와 발생 가능성을 분석하여 종합적인 위험도를 평가합니다.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4">
                        <div class="gov-card">
                            <div class="row">
                                <div class="col-auto">
                                    <div class="feature-icon bg-primary-light">
                                        <i class="fas fa-lightbulb text-primary"></i>
                                    </div>
                                </div>
                                <div class="col">
                                    <h3 class="h5 mb-2">대응 방안 추천</h3>
                                    <p class="mb-0">식별된 위험에 대한 최적의 대응 방안을 AI가 자동으로 추천합니다.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4">
                        <div class="gov-card">
                            <div class="row">
                                <div class="col-auto">
                                    <div class="feature-icon bg-primary-light">
                                        <i class="fas fa-file-alt text-primary"></i>
                                    </div>
                                </div>
                                <div class="col">
                                    <h3 class="h5 mb-2">보고서 자동화</h3>
                                    <p class="mb-0">위험성 평가 결과를 종합하여 전문적인 보고서를 자동으로 생성합니다.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 이용 절차 섹션 -->
        <section id="process" class="py-5">
            <div class="container py-4">
                <div class="row justify-content-center mb-5">
                    <div class="col-lg-8 text-center">
                        <h2 class="section-title">이용 절차</h2>
                        <p class="section-description">RISK NINE은 간편한 4단계 프로세스로 위험성 평가를 완료합니다.</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 position-relative">
                        <div class="process-timeline">
                            <div class="row">
                                <div class="col-md-3 mb-4">
                                    <div class="process-step text-center">
                                        <div class="process-number">1</div>
                                        <div class="process-content">
                                            <h3 class="h5 mb-2">계정 생성</h3>
                                            <p>간편한 회원가입으로 서비스를 이용할 수 있습니다.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 mb-4">
                                    <div class="process-step text-center">
                                        <div class="process-number">2</div>
                                        <div class="process-content">
                                            <h3 class="h5 mb-2">작업 환경 설정</h3>
                                            <p>산업 분야와 작업 환경 정보를 입력합니다.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 mb-4">
                                    <div class="process-step text-center">
                                        <div class="process-number">3</div>
                                        <div class="process-content">
                                            <h3 class="h5 mb-2">위험성 평가</h3>
                                            <p>AI가 위험 요소를 분석하고 평가합니다.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 mb-4">
                                    <div class="process-step text-center">
                                        <div class="process-number">4</div>
                                        <div class="process-content">
                                            <h3 class="h5 mb-2">결과 확인</h3>
                                            <p>평가 결과와 대응 방안을 확인합니다.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-4">
                    <div class="col-12 text-center">
                        <a href="register.php" class="btn btn-primary btn-lg">
                            지금 시작하기
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- 고객 사례 및 후기 섹션 -->
        <section id="testimonials" class="py-5 bg-primary-light">
            <div class="container py-4">
                <div class="row justify-content-center mb-5">
                    <div class="col-lg-8 text-center">
                        <h2 class="section-title">고객 사례 및 후기</h2>
                        <p class="section-description">RISK NINE을 도입한 기업들의 성공 사례와 생생한 후기를 확인하세요.</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="testimonial-card">
                            <div class="testimonial-content">
                                <p class="testimonial-text">"RISK NINE 도입 후 사고율이 37% 감소했으며, 안전 관리 효율성이 크게 향상되었습니다. 특히 AI 기반 위험 예측 기능이 매우 유용했습니다."</p>
                                <div class="testimonial-rating">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                </div>
                            </div>
                            <div class="testimonial-user">
                                <img src="assets/images/user1.jpg" alt="김대표님" class="testimonial-avatar">
                                <div class="testimonial-info">
                                    <h4 class="testimonial-name">김대표님</h4>
                                    <p class="testimonial-position">건설사 안전관리 책임자</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="testimonial-card">
                            <div class="testimonial-content">
                                <p class="testimonial-text">"위험성평가 보고서 작성 시간이 기존 대비 75% 단축되었으며, 체계적인.위험 관리 시스템으로 안전 인증 획득이 수월해졌습니다."</p>
                                <div class="testimonial-rating">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star-half-alt"></i>
                                </div>
                            </div>
                            <div class="testimonial-user">
                                <img src="assets/images/user2.jpg" alt="이과장님" class="testimonial-avatar">
                                <div class="testimonial-info">
                                    <h4 class="testimonial-name">이과장님</h4>
                                    <p class="testimonial-position">제조공장 안전팀 과장</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="testimonial-card">
                            <div class="testimonial-content">
                                <p class="testimonial-text">"정부 규제 준수가 쉬워졌으며, AI 기반 분석으로 인력 및 자원 배치 최적화에 큰 도움이 되었습니다. 위험 대응 속도도 크게 향상되었습니다."</p>
                                <div class="testimonial-rating">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                </div>
                            </div>
                            <div class="testimonial-user">
                                <img src="assets/images/user3.jpg" alt="박부장님" class="testimonial-avatar">
                                <div class="testimonial-info">
                                    <h4 class="testimonial-name">박부장님</h4>
                                    <p class="testimonial-position">화학 플랜트 안전관리 부장</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-4">
                    <div class="col-12 text-center">
                        <a href="#" class="btn btn-outline-primary">더 많은 사례 보기</a>
                    </div>
                </div>
            </div>
        </section>

        <!-- FAQ 섹션 -->
        <section id="faq" class="py-5">
            <div class="container py-4">
                <div class="row justify-content-center mb-5">
                    <div class="col-lg-8 text-center">
                        <h2 class="section-title">자주 묻는 질문</h2>
                        <p class="section-description">RISK NINE 서비스에 대해 가장 많이 문의하시는 질문들을 모았습니다.</p>
                    </div>
                </div>
                <div class="row justify-content-center">
                    <div class="col-lg-10">
                        <div class="accordion" id="faqAccordion">
                            <div class="accordion-item">
                                <h3 class="accordion-header" id="faqHeading1">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse1" aria-expanded="true" aria-controls="faqCollapse1">
                                        RISK NINE은 어떤 기업에 적합한가요?
                                    </button>
                                </h3>
                                <div id="faqCollapse1" class="accordion-collapse collapse show" aria-labelledby="faqHeading1" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        <p>RISK NINE은 건설, 제조, 화학, 에너지, 물류 등 안전이 중요한 모든 산업 분야에 적합합니다. 특히 다음과 같은 기업에 큰 도움이 됩니다:</p>
                                        <ul>
                                            <li>위험 요소가 많은 작업 환경을 관리하는 기업</li>
                                            <li>안전 관리 시스템을 디지털화하려는 기업</li>
                                            <li>데이터 기반의 안전 의사결정을 원하는 기업</li>
                                            <li>안전 규제 준수를 체계적으로 관리하고자 하는 기업</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h3 class="accordion-header" id="faqHeading2">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse2" aria-expanded="false" aria-controls="faqCollapse2">
                                        AI가 위험 요소를 어떻게 정확하게 식별하나요?
                                    </button>
                                </h3>
                                <div id="faqCollapse2" class="accordion-collapse collapse" aria-labelledby="faqHeading2" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        <p>RISK NINE의 AI 시스템은 다음과 같은 방법으로 위험 요소를 정확하게 식별합니다:</p>
                                        <ol>
                                            <li><strong>방대한 데이터 학습:</strong> 국내외 수십만 건의 안전 사고 데이터와 위험성 평가 자료를 학습</li>
                                            <li><strong>산업별 특화 알고리즘:</strong> 각 산업 분야의 특성에 맞춘 위험 식별 알고리즘 적용</li>
                                            <li><strong>패턴 인식:</strong> 과거 사고 패턴과 유사한 위험 상황을 자동으로 감지</li>
                                            <li><strong>지속적 학습:</strong> 새로운 안전 사례와 규제 정보를 지속적으로 업데이트하여 정확도 향상</li>
                                        </ol>
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h3 class="accordion-header" id="faqHeading3">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse3" aria-expanded="false" aria-controls="faqCollapse3">
                                        서비스 도입 과정은 어떻게 되나요?
                                    </button>
                                </h3>
                                <div id="faqCollapse3" class="accordion-collapse collapse" aria-labelledby="faqHeading3" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        <p>RISK NINE 서비스 도입은 다음과 같은 단계로 진행됩니다:</p>
                                        <ol>
                                            <li><strong>초기 컨설팅:</strong> 기업의 안전 관리 현황 및 니즈 파악 (무료)</li>
                                            <li><strong>맞춤형 제안:</strong> 기업 특성에 맞는 서비스 구성 및 제안</li>
                                            <li><strong>시스템 설정:</strong> 계정 생성 및 초기 시스템 설정</li>
                                            <li><strong>데이터 통합:</strong> 기존 안전 관리 데이터 통합 (선택 사항)</li>
                                            <li><strong>사용자 교육:</strong> 관리자 및 사용자 대상 시스템 활용 교육</li>
                                            <li><strong>적용 및 운영:</strong> 실제 업무에 적용 및 지속적인 기술 지원</li>
                                        </ol>
                                        <p>전체 도입 과정은 기업 규모와 복잡성에 따라 1~4주 정도 소요됩니다.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h3 class="accordion-header" id="faqHeading4">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse4" aria-expanded="false" aria-controls="faqCollapse4">
                                        기존 안전 관리 시스템과 연동이 가능한가요?
                                    </button>
                                </h3>
                                <div id="faqCollapse4" class="accordion-collapse collapse" aria-labelledby="faqHeading4" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        <p>네, RISK NINE은 다양한 기존 시스템과의 연동을 지원합니다:</p>
                                        <ul>
                                            <li>대부분의 ERP 시스템과 API 연동 가능</li>
                                            <li>기존 안전 관리 솔루션의 데이터 마이그레이션 지원</li>
                                            <li>Excel, CSV 등 다양한 형식의 데이터 가져오기 기능</li>
                                            <li>CCTV, IoT 센서 등 하드웨어 연동 지원 (엔터프라이즈 플랜)</li>
                                        </ul>
                                        <p>구체적인 연동 가능 여부는 기업의 시스템 환경에 따라 달라질 수 있으므로, 상담을 통해 자세히 안내해 드립니다.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h3 class="accordion-header" id="faqHeading5">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse5" aria-expanded="false" aria-controls="faqCollapse5">
                                        데이터 보안은 어떻게 보장되나요?
                                    </button>
                                </h3>
                                <div id="faqCollapse5" class="accordion-collapse collapse" aria-labelledby="faqHeading5" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        <p>RISK NINE은 다음과 같은 보안 체계를 통해 고객 데이터를 안전하게 보호합니다:</p>
                                        <ul>
                                            <li>ISO 27001 인증을 획득한 데이터 보안 체계 운영</li>
                                            <li>AES-256 암호화를 통한 모든 데이터 암호화 저장</li>
                                            <li>HTTPS 프로토콜을 통한 안전한 데이터 전송</li>
                                            <li>역할 기반 접근 제어로 내부 정보 접근 제한</li>
                                            <li>정기적인 보안 취약점 점검 및 업데이트</li>
                                            <li>데이터 센터 이중화로 서비스 안정성 확보</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-4">
                    <div class="col-12 text-center">
                        <p>더 궁금한 점이 있으신가요?</p>
                        <a href="#support" class="btn btn-primary">문의하기</a>
                    </div>
                </div>
            </div>
        </section>

        <!-- 고객 지원 섹션 -->
        <section id="support" class="py-5 bg-light">
            <div class="container py-4">
                <div class="row justify-content-center mb-5">
                    <div class="col-lg-8 text-center">
                        <h2 class="section-title">고객 지원</h2>
                        <p class="section-description">RISK NINE 팀은 항상 고객의 질문에 답변하고 지원할 준비가 되어 있습니다.</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-4">
                        <div class="gov-card text-center">
                            <div class="mb-4">
                                <i class="fas fa-book-open fa-3x text-primary"></i>
                            </div>
                            <h3 class="h5 mb-3">사용 가이드</h3>
                            <p>RISK NINE 사용 방법에 대한 자세한 가이드를 제공합니다.</p>
                            <a href="#" class="btn btn-outline-primary mt-2">가이드 보기</a>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="gov-card text-center">
                            <div class="mb-4">
                                <i class="fas fa-headset fa-3x text-primary"></i>
                            </div>
                            <h3 class="h5 mb-3">고객 센터</h3>
                            <p>전문 상담사가 고객의 문의사항을 신속하게 해결해 드립니다.</p>
                            <a href="#" class="btn btn-outline-primary mt-2">문의하기</a>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="gov-card text-center">
                            <div class="mb-4">
                                <i class="fas fa-question-circle fa-3x text-primary"></i>
                            </div>
                            <h3 class="h5 mb-3">자주 묻는 질문</h3>
                            <p>자주 묻는 질문에 대한 답변을 모았습니다.</p>
                            <a href="#faq" class="btn btn-outline-primary mt-2">FAQ 보기</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- 푸터 -->
    <footer class="footer bg-navy text-white py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4 mb-lg-0">
                    <h3 class="footer-title h5">RISK NINE</h3>
                    <p>AI 기술을 활용한 효율적이고 정확한 위험성 평가 솔루션으로 산업 현장의 안전을 책임집니다.</p>
                    <div class="mt-4">
                        <a href="#" class="social-link"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 mb-4 mb-lg-0">
                    <h4 class="footer-subtitle">바로가기</h4>
                    <ul class="footer-links">
                        <li><a href="#service">서비스 소개</a></li>
                        <li><a href="#features">주요 기능</a></li>
                        <li><a href="#process">이용 절차</a></li>
                        <li><a href="#support">고객 지원</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-md-4 mb-4 mb-lg-0">
                    <h4 class="footer-subtitle">서비스</h4>
                    <ul class="footer-links">
                        <li><a href="#">위험성 평가</a></li>
                        <li><a href="#">AI 위험 분석</a></li>
                        <li><a href="#">보고서 생성</a></li>
                        <li><a href="#">맞춤형 솔루션</a></li>
                    </ul>
                </div>
                <div class="col-lg-4 col-md-4">
                    <h4 class="footer-subtitle">연락처</h4>
                    <ul class="footer-links">
                        <li><i class="fas fa-map-marker-alt me-2"></i> 서울특별시 강남구 테헤란로 123</li>
                        <li><i class="fas fa-phone me-2"></i> 02-1234-5678</li>
                        <li><i class="fas fa-envelope me-2"></i> info@risknine.com</li>
                        <li><i class="fas fa-clock me-2"></i> 평일 09:00 - 18:00</li>
                    </ul>
                </div>
            </div>
            <hr class="footer-divider">
            <div class="row">
                <div class="col-md-6 text-center text-md-start">
                    <p class="mb-0">&copy; 2023 RISK NINE 위험성평가 시스템. All Rights Reserved.</p>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <a href="#" class="text-white text-decoration-none me-3">개인정보처리방침</a>
                    <a href="#" class="text-white text-decoration-none">이용약관</a>
                </div>
            </div>
        </div>
    </footer>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // 스크롤 이벤트 처리
        document.addEventListener('DOMContentLoaded', function() {
            // 네비게이션 바 스크롤 효과
            const navbar = document.querySelector('.navbar');
            window.addEventListener('scroll', function() {
                if (window.scrollY > 100) {
                    navbar.classList.add('navbar-scrolled');
                } else {
                    navbar.classList.remove('navbar-scrolled');
                }
            });

            // 스무스 스크롤
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function(e) {
                    e.preventDefault();
                    const targetId = this.getAttribute('href');
                    if (targetId === '#') return;
                    
                    const targetElement = document.querySelector(targetId);
                    if (targetElement) {
                        window.scrollTo({
                            top: targetElement.offsetTop - 80,
                            behavior: 'smooth'
                        });
                    }
                });
            });
        });
    </script>
</body>
</html> 