# 위험성 평가 시스템 (Risk Assessment System)

## 프로젝트 소개
이 프로젝트는 산업 현장에서의 위험성 평가를 위한 웹 기반 시스템입니다. AI 기술을 활용하여 작업장의 위험 요소를 식별하고, 위험성을 평가하며, 관리 대책을 제안합니다.

## 주요 기능
- **AI 위험성 평가**: 작업 환경 정보를 입력하면 AI가 자동으로 위험 요소를 식별하고 위험성을 평가합니다.
- **위험성 평가 결과 관리**: 평가 결과를 Excel 형식의 표로 확인하고 다운로드할 수 있습니다.
- **위험성 분석**: 위험 요소에 대한 상세 분석 및 통계를 제공합니다.
- **활동 기록**: 시스템 내의 모든 활동을 기록하고 관리합니다.

## 기술 스택
- PHP
- MySQL
- HTML/CSS
- JavaScript
- Bootstrap

## 설치 및 실행 방법
1. XAMPP 또는 유사한 웹 서버 환경을 설치합니다.
2. 프로젝트 파일을 웹 서버의 htdocs 디렉토리에 복사합니다.
3. MySQL 데이터베이스를 생성하고 `database/schema.sql` 파일을 실행하여 테이블을 생성합니다.
4. `includes/config.php` 파일에서 데이터베이스 연결 정보를 설정합니다.
5. 웹 브라우저에서 `http://localhost/risk9/` 주소로 접속합니다.

## 라이센스
이 프로젝트는 MIT 라이센스 하에 배포됩니다. 