-- 기존 테이블 삭제 (개발 환경에서만 사용)
DROP TABLE IF EXISTS activities;
DROP TABLE IF EXISTS report_assessments;
DROP TABLE IF EXISTS risk_items;
DROP TABLE IF EXISTS reports;
DROP TABLE IF EXISTS assessments;
DROP TABLE IF EXISTS users;

-- 사용자 테이블
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    full_name VARCHAR(100) NOT NULL,
    department VARCHAR(100),
    position VARCHAR(100),
    is_admin BOOLEAN DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 평가 테이블
CREATE TABLE assessments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    project VARCHAR(255) NOT NULL,
    assessment_type VARCHAR(50) NOT NULL,
    description TEXT,
    status ENUM('pending', 'in_progress', 'completed') NOT NULL DEFAULT 'pending',
    risk_level ENUM('very_low', 'low', 'medium', 'high', 'very_high'),
    start_date DATE NOT NULL,
    end_date DATE,
    due_date DATE,
    created_by INT NOT NULL,
    assigned_to INT,
    completion_date DATE,
    progress_rate INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (assigned_to) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 위험 항목 테이블
CREATE TABLE risk_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    assessment_id INT NOT NULL,
    hazard_type VARCHAR(100) NOT NULL,
    hazard_name VARCHAR(255) NOT NULL,
    hazard_situation TEXT NOT NULL,
    likelihood INT NOT NULL, -- 1-5 척도
    severity INT NOT NULL, -- 1-5 척도
    risk_level ENUM('very_low', 'low', 'medium', 'high', 'very_high') NOT NULL,
    control_measures TEXT,
    responsible_person VARCHAR(100),
    implementation_period VARCHAR(100),
    status ENUM('identified', 'in_progress', 'resolved') NOT NULL DEFAULT 'identified',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (assessment_id) REFERENCES assessments(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 보고서 테이블
CREATE TABLE reports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    type VARCHAR(50) NOT NULL,
    description TEXT,
    author_id INT NOT NULL,
    department VARCHAR(100),
    format VARCHAR(20) NOT NULL DEFAULT 'pdf',
    status ENUM('draft', 'review', 'completed') NOT NULL DEFAULT 'draft',
    include_executive_summary BOOLEAN DEFAULT 1,
    include_methodology BOOLEAN DEFAULT 1,
    include_findings BOOLEAN DEFAULT 1,
    include_risk_analysis BOOLEAN DEFAULT 1,
    include_recommendations BOOLEAN DEFAULT 1,
    include_appendices BOOLEAN DEFAULT 0,
    file_path VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 보고서와 평가의 관계를 저장하는 중간 테이블
CREATE TABLE report_assessments (
    report_id INT NOT NULL,
    assessment_id INT NOT NULL,
    PRIMARY KEY (report_id, assessment_id),
    FOREIGN KEY (report_id) REFERENCES reports(id) ON DELETE CASCADE,
    FOREIGN KEY (assessment_id) REFERENCES assessments(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 활동 로그 테이블
CREATE TABLE activities (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    activity_type ENUM(
        'login', 'logout', 'create_assessment', 'update_assessment', 'complete_assessment',
        'create_report', 'update_report', 'download_report', 'identify_risk',
        'update_risk', 'resolve_risk', 'share_report', 'ai_analysis', 'comment'
    ) NOT NULL,
    description TEXT NOT NULL,
    related_id INT, -- 연관된 평가/보고서/위험 항목의 ID
    related_type VARCHAR(50), -- 'assessment', 'report', 'risk_item' 등
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci; 