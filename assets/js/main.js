// AOS 초기화
AOS.init({
    duration: 1000,
    easing: 'ease-in-out',
    once: true,
    mirror: false
});

// 맨 위로 버튼
document.addEventListener('DOMContentLoaded', function() {
    const backToTop = document.querySelector('.back-to-top');
    
    if (backToTop) {
        // 페이지 스크롤에 따라 버튼 표시 여부 결정
        window.addEventListener('scroll', function() {
            if (window.scrollY > 300) {
                backToTop.classList.add('active');
            } else {
                backToTop.classList.remove('active');
            }
        });

        // 버튼 클릭 이벤트
        backToTop.addEventListener('click', function(e) {
            e.preventDefault();
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }

    // 내비게이션바 스크롤 효과
    const navbar = document.querySelector('.navbar');
    if (navbar) {
        window.addEventListener('scroll', function() {
            if (window.scrollY > 50) {
                navbar.classList.add('navbar-scrolled');
            } else {
                navbar.classList.remove('navbar-scrolled');
            }
        });
    }

    // 뉴스레터 폼 유효성 검사
    const newsletterForm = document.querySelector('.newsletter-form');
    if (newsletterForm) {
        newsletterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const emailInput = this.querySelector('input[type="email"]');
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            
            if (!emailRegex.test(emailInput.value)) {
                alert('유효한 이메일 주소를 입력해주세요.');
                return;
            }
            
            // 성공 메시지 표시
            const formGroup = emailInput.closest('.input-group');
            const successMsg = document.createElement('div');
            successMsg.classList.add('alert', 'alert-success', 'mt-3');
            successMsg.textContent = '뉴스레터 구독이 완료되었습니다. 감사합니다!';
            
            formGroup.parentNode.appendChild(successMsg);
            emailInput.value = '';
            
            // 3초 후 성공 메시지 제거
            setTimeout(() => {
                successMsg.remove();
            }, 3000);
        });
    }

    // 부드러운 스크롤 이동
    const smoothScroll = document.querySelectorAll('a.nav-link, .smooth-scroll');
    
    for (let i = 0; i < smoothScroll.length; i++) {
        const anchor = smoothScroll[i];
        
        anchor.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            
            if (href.charAt(0) === '#' && href.length > 1) {
                e.preventDefault();
                
                const targetId = href.substring(1);
                const targetElement = document.getElementById(targetId);
                
                if (targetElement) {
                    // 네비게이션바 높이를 고려한 스크롤 위치 계산
                    const navHeight = document.querySelector('.navbar').offsetHeight;
                    const targetPosition = targetElement.offsetTop - navHeight;
                    
                    window.scrollTo({
                        top: targetPosition,
                        behavior: 'smooth'
                    });
                    
                    // 모바일 메뉴 닫기
                    const navbarCollapse = document.querySelector('.navbar-collapse');
                    if (navbarCollapse && navbarCollapse.classList.contains('show')) {
                        document.querySelector('.navbar-toggler').click();
                    }
                }
            }
        });
    }

    // FAQ 아코디언 기능
    const faqItems = document.querySelectorAll('.faq-item');
    
    for (let i = 0; i < faqItems.length; i++) {
        const header = faqItems[i].querySelector('.faq-header');
        
        header.addEventListener('click', function() {
            this.parentNode.classList.toggle('active');
            
            const body = this.nextElementSibling;
            
            if (body.style.maxHeight) {
                body.style.maxHeight = null;
            } else {
                body.style.maxHeight = body.scrollHeight + 'px';
            }
        });
    }

    // 개선된 숫자 카운팅 애니메이션 함수
    function animateCounters() {
        const statNumbers = document.querySelectorAll('.stat-number');
        
        statNumbers.forEach(statNumber => {
            // 최종값을 즉시 표시
            const target = parseInt(statNumber.getAttribute('data-count'));
            statNumber.textContent = target;
            
            // 플러스 기호가 있는 경우 추가
            const suffix = statNumber.getAttribute('data-suffix') || '';
            if (suffix) {
                statNumber.textContent += suffix;
            }
        });
    }
    
    // 페이지 애니메이션 효과 적용
    applyAnimations();

    // IntersectionObserver를 사용하여 요소가 보일 때 애니메이션 실행
    const observer = new IntersectionObserver(entries => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                animateCounters();
                applyAnimations();
                animateHeroSection();
                observer.disconnect(); // 한 번만 실행
            }
        });
    }, { threshold: 0.2 }); // 20% 이상 보일 때 실행

    // Stats 섹션 관찰 시작
    const statsSection = document.querySelector('#stats');
    if (statsSection) {
        observer.observe(statsSection);
    }

    // 히어로 섹션 효과
    animateHeroSection();
});

// 페이지 애니메이션 효과 적용
function applyAnimations() {
    // 카드 요소들에 등장 애니메이션 적용
    const cards = document.querySelectorAll('.gov-card, .feature-card, .vision-card, .mission-card, .testimonial-card, .stat-card');
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';
        card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        
        // 순차적으로 등장하도록 지연 적용
        setTimeout(() => {
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, 120 * index);
    });
    
    // 섹션 제목 애니메이션
    const sectionTitles = document.querySelectorAll('.section-title');
    sectionTitles.forEach(title => {
        title.style.opacity = '0';
        title.style.transform = 'translateY(-20px)';
        title.style.transition = 'opacity 0.8s ease, transform 0.8s ease';
    });
    
    // 스크롤 시 섹션 제목 나타나는 효과
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, { threshold: 0.3 });
    
    sectionTitles.forEach(title => {
        observer.observe(title);
    });
}

// 히어로 섹션 효과
function animateHeroSection() {
    const heroContent = document.querySelector('.hero h1');
    const heroText = document.querySelector('.hero p');
    const heroButtons = document.querySelector('.hero .mt-4');
    const heroImage = document.querySelector('.hero img');
    
    if(heroContent) {
        heroContent.style.opacity = '0';
        heroContent.style.transform = 'translateX(-30px)';
        heroContent.style.transition = 'opacity 0.8s ease, transform 0.8s ease';
        setTimeout(() => {
            heroContent.style.opacity = '1';
            heroContent.style.transform = 'translateX(0)';
        }, 300);
    }
    
    if(heroText) {
        heroText.style.opacity = '0';
        heroText.style.transform = 'translateX(-20px)';
        heroText.style.transition = 'opacity 0.8s ease, transform 0.8s ease';
        setTimeout(() => {
            heroText.style.opacity = '1';
            heroText.style.transform = 'translateX(0)';
        }, 600);
    }
    
    if(heroButtons) {
        heroButtons.style.opacity = '0';
        heroButtons.style.transform = 'translateY(20px)';
        heroButtons.style.transition = 'opacity 0.8s ease, transform 0.8s ease';
        setTimeout(() => {
            heroButtons.style.opacity = '1';
            heroButtons.style.transform = 'translateY(0)';
        }, 900);
    }
    
    if(heroImage) {
        heroImage.style.opacity = '0';
        heroImage.style.transform = 'translateX(30px)';
        heroImage.style.transition = 'opacity 1s ease, transform 1s ease';
        setTimeout(() => {
            heroImage.style.opacity = '1';
            heroImage.style.transform = 'translateX(0)';
        }, 600);
    }
}

// 페이지 로드 시 히어로 섹션 애니메이션 실행
window.addEventListener('load', animateHeroSection); 