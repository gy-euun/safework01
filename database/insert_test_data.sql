-- 관리자 계정 생성 (비밀번호: admin123)
INSERT INTO users (username, password, email, full_name, is_admin) 
VALUES ('admin', '$2y$10$yUzf9ROiRsZCTVpn6dI1.eCRQS90aR3uVBCXWysLU1p9TSEBbZ7CC', 'admin@risk9.com', '시스템 관리자', 1);

-- 일반 사용자 계정 생성 (비밀번호: user123)
INSERT INTO users (username, password, email, full_name, department, position) 
VALUES 
('홍길동', '$2y$10$KkBY7LxX9CLnVeY5.y5KOePIPgx5zZI8mNvbKbJVpLlqo/fGc3o9a', 'hong@risk9.com', '홍길동', '안전관리부', '팀장'),
('김철수', '$2y$10$KkBY7LxX9CLnVeY5.y5KOePIPgx5zZI8mNvbKbJVpLlqo/fGc3o9a', 'kim@risk9.com', '김철수', '시공부', '과장'),
('이영희', '$2y$10$KkBY7LxX9CLnVeY5.y5KOePIPgx5zZI8mNvbKbJVpLlqo/fGc3o9a', 'lee@risk9.com', '이영희', '설계부', '대리'),
('박지민', '$2y$10$KkBY7LxX9CLnVeY5.y5KOePIPgx5zZI8mNvbKbJVpLlqo/fGc3o9a', 'park@risk9.com', '박지민', '품질관리부', '사원'),
('김미나', '$2y$10$KkBY7LxX9CLnVeY5.y5KOePIPgx5zZI8mNvbKbJVpLlqo/fGc3o9a', 'mina@risk9.com', '김미나', '안전관리부', '대리');

-- 샘플 평가 데이터
INSERT INTO assessments (title, project, assessment_type, description, status, risk_level, start_date, due_date, created_by, assigned_to, progress_rate) 
VALUES 
('안전난간 설치공사', '신축건물 A', '시공 안전성', '신축건물의 안전난간 설치공사에 대한 위험성 평가', 'in_progress', 'high', '2023-06-15', '2023-06-30', 1, 2, 60),
('기초공사 안전진단', '리모델링 B', '구조 안전성', '리모델링 건물의 기초공사에 대한 안전 진단', 'completed', 'medium', '2023-06-10', '2023-06-20', 2, 3, 100),
('전기배선 위험성평가', '사무실 C', '전기 안전', '사무실 건물의 전기 배선 시스템에 대한 위험성 평가', 'completed', 'low', '2023-06-05', '2023-06-15', 3, 4, 100),
('고소작업 안전대책', '외장공사 D', '작업자 안전', '외장공사 진행 시 고소작업에 대한 안전 대책 평가', 'pending', 'very_high', '2023-06-02', '2023-07-10', 4, 5, 0),
('화재위험 평가', '주거시설 E', '화재 안전', '주거시설의 화재 위험 요소에 대한 종합 평가', 'completed', 'medium', '2023-05-28', '2023-06-10', 5, 1, 100);

-- 샘플 위험 항목 데이터
INSERT INTO risk_items (assessment_id, hazard_type, hazard_name, hazard_situation, likelihood, severity, risk_level, control_measures, responsible_person, implementation_period, status)
VALUES
(1, '추락', '안전난간 미설치', '작업자가 안전난간이 없는 상태에서 작업 시 추락 위험', 4, 5, 'very_high', '작업 전 임시 안전난간 설치, 안전대 착용 의무화', '홍길동', '즉시', 'in_progress'),
(1, '자재 낙하', '자재 적재 불량', '자재 적재 불량으로 인한 낙하물 발생 위험', 3, 4, 'high', '자재 적재 시 안전기준 준수, 안전모 착용 의무화', '김철수', '3일 이내', 'identified'),
(2, '붕괴', '지반 약화', '기초공사 중 지반 약화로 인한 붕괴 위험', 2, 5, 'high', '지반 조사 및 보강 작업 선행, 작업 구역 통제', '이영희', '작업 전', 'resolved'),
(3, '감전', '배선 노출', '노출된 전기 배선으로 인한 감전 위험', 2, 3, 'medium', '작업 전 전원 차단, 절연 장갑 착용', '박지민', '즉시', 'resolved'),
(4, '추락', '안전장비 미착용', '고소작업 시 안전장비 미착용으로 인한 추락 위험', 4, 5, 'very_high', '안전장비 착용 의무화 및 점검 강화, 안전교육 실시', '김미나', '즉시', 'identified'),
(5, '화재', '전기 과부하', '전기 과부하로 인한 화재 발생 위험', 3, 4, 'high', '전기 용량 점검 및 분산 조치, 자동 차단기 설치', '홍길동', '1주일 이내', 'resolved');

-- 샘플 보고서 데이터
INSERT INTO reports (title, type, description, author_id, department, format, status)
VALUES
('6월 위험성 평가 보고서', '정기 보고서', '6월 진행된 위험성 평가 결과를 종합한 월간 보고서', 1, '안전관리부', 'pdf', 'completed'),
('안전난간 설치공사 위험성 보고서', '프로젝트 보고서', '안전난간 설치공사의 위험성 평가 결과 보고서', 2, '시공부', 'pdf', 'completed'),
('2023년 2분기 고위험 항목 분석', '분석 보고서', '2분기에 식별된 고위험 항목들에 대한 심층 분석 보고서', 3, '설계부', 'docx', 'review');

-- 보고서와 평가 연결
INSERT INTO report_assessments (report_id, assessment_id)
VALUES
(1, 1), (1, 2), (1, 3), (1, 5),
(2, 1),
(3, 1), (3, 4);

-- 샘플 활동 로그 데이터
INSERT INTO activities (user_id, activity_type, description, related_id, related_type, ip_address)
VALUES
(1, 'login', '관리자님이 로그인했습니다.', NULL, NULL, '192.168.1.100'),
(2, 'create_assessment', '홍길동님이 "기초공사 안전진단" 평가를 생성했습니다.', 2, 'assessment', '192.168.1.101'),
(3, 'update_assessment', '김철수님이 "전기배선 위험성평가" 내용을 수정했습니다.', 3, 'assessment', '192.168.1.102'),
(4, 'identify_risk', '이영희님이 "고소작업 안전대책"에서 위험 요소를 식별했습니다.', 5, 'risk_item', '192.168.1.103'),
(5, 'create_report', '박지민님이 "6월 위험성 평가 보고서"를 생성했습니다.', 1, 'report', '192.168.1.104'),
(1, 'share_report', '관리자님이 "안전난간 설치공사 위험성 보고서"를 공유했습니다.', 2, 'report', '192.168.1.100'),
(2, 'complete_assessment', '홍길동님이 "기초공사 안전진단" 평가를 완료했습니다.', 2, 'assessment', '192.168.1.101'),
(3, 'comment', '김철수님이 "화재위험 평가"에 의견을 추가했습니다.', 5, 'assessment', '192.168.1.102'); 