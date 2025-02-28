<?php
// 설정 파일 포함
require_once 'config.php';

/**
 * 보고서 관리 클래스
 */
class Report {
    private $db;
    
    /**
     * 생성자
     */
    public function __construct() {
        global $mysqli;
        $this->db = $mysqli;
    }
    
    /**
     * 새 보고서 생성
     * 
     * @param array $data 보고서 데이터
     * @param array $assessmentIds 포함할 평가 ID 배열
     * @return int|bool 성공 시 보고서 ID, 실패 시 false
     */
    public function createReport($data, $assessmentIds = []) {
        // 필수 입력값 검증
        if(empty($data['title']) || empty($data['type'])) {
            setErrorMessage('필수 항목을 모두 입력해주세요.');
            return false;
        }
        
        // SQL 인젝션 방지
        $title = escapeInput($data['title']);
        $type = escapeInput($data['type']);
        $description = isset($data['description']) ? escapeInput($data['description']) : '';
        $authorId = $_SESSION['id'];
        $department = isset($data['department']) ? escapeInput($data['department']) : '';
        $format = isset($data['format']) ? escapeInput($data['format']) : 'pdf';
        $status = isset($data['status']) ? escapeInput($data['status']) : 'draft';
        
        // 포함 항목 설정
        $includeExecutiveSummary = isset($data['include_executive_summary']) ? 1 : 0;
        $includeMethodology = isset($data['include_methodology']) ? 1 : 0;
        $includeFindings = isset($data['include_findings']) ? 1 : 0;
        $includeRiskAnalysis = isset($data['include_risk_analysis']) ? 1 : 0;
        $includeRecommendations = isset($data['include_recommendations']) ? 1 : 0;
        $includeAppendices = isset($data['include_appendices']) ? 1 : 0;
        
        // SQL 쿼리 작성
        $sql = "INSERT INTO reports (title, type, description, author_id, department, format, status,
                include_executive_summary, include_methodology, include_findings, include_risk_analysis,
                include_recommendations, include_appendices)
                VALUES ('$title', '$type', '$description', '$authorId', '$department', '$format', '$status',
                '$includeExecutiveSummary', '$includeMethodology', '$includeFindings', '$includeRiskAnalysis',
                '$includeRecommendations', '$includeAppendices')";
        
        // 쿼리 실행
        if($this->db->query($sql)) {
            $reportId = $this->db->insert_id;
            
            // 평가 연결
            if(!empty($assessmentIds)) {
                $this->linkAssessments($reportId, $assessmentIds);
            }
            
            // 활동 로그 기록
            logActivity($_SESSION['id'], 'create_report', $_SESSION['full_name'] . '님이 "' . $title . '" 보고서를 생성했습니다.', $reportId, 'report');
            
            setSuccessMessage('보고서가 성공적으로 생성되었습니다.');
            return $reportId;
        } else {
            setErrorMessage('보고서 생성 중 오류가 발생했습니다: ' . $this->db->error);
            return false;
        }
    }
    
    /**
     * 보고서 정보 업데이트
     * 
     * @param int $id 보고서 ID
     * @param array $data 보고서 데이터
     * @param array $assessmentIds 포함할 평가 ID 배열
     * @return bool 업데이트 성공 여부
     */
    public function updateReport($id, $data, $assessmentIds = []) {
        // 필수 입력값 검증
        if(empty($data['title']) || empty($data['type'])) {
            setErrorMessage('필수 항목을 모두 입력해주세요.');
            return false;
        }
        
        // SQL 인젝션 방지
        $id = escapeInput($id);
        $title = escapeInput($data['title']);
        $type = escapeInput($data['type']);
        $description = isset($data['description']) ? escapeInput($data['description']) : '';
        $department = isset($data['department']) ? escapeInput($data['department']) : '';
        $format = isset($data['format']) ? escapeInput($data['format']) : 'pdf';
        $status = isset($data['status']) ? escapeInput($data['status']) : 'draft';
        
        // 포함 항목 설정
        $includeExecutiveSummary = isset($data['include_executive_summary']) ? 1 : 0;
        $includeMethodology = isset($data['include_methodology']) ? 1 : 0;
        $includeFindings = isset($data['include_findings']) ? 1 : 0;
        $includeRiskAnalysis = isset($data['include_risk_analysis']) ? 1 : 0;
        $includeRecommendations = isset($data['include_recommendations']) ? 1 : 0;
        $includeAppendices = isset($data['include_appendices']) ? 1 : 0;
        
        // SQL 쿼리 작성
        $sql = "UPDATE reports SET 
                title = '$title', 
                type = '$type', 
                description = '$description', 
                department = '$department', 
                format = '$format', 
                status = '$status',
                include_executive_summary = '$includeExecutiveSummary',
                include_methodology = '$includeMethodology',
                include_findings = '$includeFindings',
                include_risk_analysis = '$includeRiskAnalysis',
                include_recommendations = '$includeRecommendations',
                include_appendices = '$includeAppendices'
                WHERE id = '$id'";
        
        // 쿼리 실행
        if($this->db->query($sql)) {
            // 평가 연결 업데이트
            if(isset($assessmentIds)) {
                // 기존 연결 삭제
                $this->db->query("DELETE FROM report_assessments WHERE report_id = '$id'");
                
                // 새 연결 추가
                if(!empty($assessmentIds)) {
                    $this->linkAssessments($id, $assessmentIds);
                }
            }
            
            // 활동 로그 기록
            logActivity($_SESSION['id'], 'update_report', $_SESSION['full_name'] . '님이 "' . $title . '" 보고서를 업데이트했습니다.', $id, 'report');
            
            setSuccessMessage('보고서가 성공적으로 업데이트되었습니다.');
            return true;
        } else {
            setErrorMessage('보고서 업데이트 중 오류가 발생했습니다: ' . $this->db->error);
            return false;
        }
    }
    
    /**
     * 보고서 정보 조회
     * 
     * @param int $id 보고서 ID
     * @return array|bool 보고서 정보 또는 실패 시 false
     */
    public function getReport($id) {
        $id = escapeInput($id);
        
        $sql = "SELECT r.*, u.full_name as author_name
                FROM reports r
                LEFT JOIN users u ON r.author_id = u.id
                WHERE r.id = '$id'";
        
        $result = $this->db->query($sql);
        
        if($result->num_rows == 1) {
            $report = $result->fetch_assoc();
            
            // 연결된 평가 가져오기
            $report['assessments'] = $this->getLinkedAssessments($id);
            
            return $report;
        } else {
            return false;
        }
    }
    
    /**
     * 보고서 삭제
     * 
     * @param int $id 보고서 ID
     * @return bool 삭제 성공 여부
     */
    public function deleteReport($id) {
        $id = escapeInput($id);
        
        // 보고서 정보 조회 (로그용)
        $report = $this->getReport($id);
        if(!$report) {
            setErrorMessage('보고서를 찾을 수 없습니다.');
            return false;
        }
        
        // 파일 삭제 (있는 경우)
        if(!empty($report['file_path']) && file_exists($report['file_path'])) {
            unlink($report['file_path']);
        }
        
        // 평가 연결 삭제
        $this->db->query("DELETE FROM report_assessments WHERE report_id = '$id'");
        
        // 보고서 삭제
        $sql = "DELETE FROM reports WHERE id = '$id'";
        
        if($this->db->query($sql)) {
            // 활동 로그 기록
            logActivity($_SESSION['id'], 'delete_report', $_SESSION['full_name'] . '님이 "' . $report['title'] . '" 보고서를 삭제했습니다.');
            
            setSuccessMessage('보고서가 성공적으로 삭제되었습니다.');
            return true;
        } else {
            setErrorMessage('보고서 삭제 중 오류가 발생했습니다: ' . $this->db->error);
            return false;
        }
    }
    
    /**
     * 보고서 상태 변경
     * 
     * @param int $id 보고서 ID
     * @param string $status 새 상태
     * @return bool 변경 성공 여부
     */
    public function changeReportStatus($id, $status) {
        $id = escapeInput($id);
        $status = escapeInput($status);
        
        if(!in_array($status, ['draft', 'review', 'completed'])) {
            setErrorMessage('유효하지 않은 보고서 상태입니다.');
            return false;
        }
        
        // 보고서 정보 조회 (로그용)
        $report = $this->getReport($id);
        if(!$report) {
            setErrorMessage('보고서를 찾을 수 없습니다.');
            return false;
        }
        
        $sql = "UPDATE reports SET status = '$status' WHERE id = '$id'";
        
        if($this->db->query($sql)) {
            // 활동 로그 기록
            $statusText = '';
            switch($status) {
                case 'draft': $statusText = '초안'; break;
                case 'review': $statusText = '검토 중'; break;
                case 'completed': $statusText = '완료됨'; break;
            }
            
            logActivity($_SESSION['id'], 'update_report', $_SESSION['full_name'] . '님이 "' . $report['title'] . '" 보고서 상태를 "' . $statusText . '"으로 변경했습니다.', $id, 'report');
            
            setSuccessMessage('보고서 상태가 성공적으로 변경되었습니다.');
            return true;
        } else {
            setErrorMessage('보고서 상태 변경 중 오류가 발생했습니다: ' . $this->db->error);
            return false;
        }
    }
    
    /**
     * 보고서와 평가 연결
     * 
     * @param int $reportId 보고서 ID
     * @param array $assessmentIds 평가 ID 배열
     * @return bool 연결 성공 여부
     */
    private function linkAssessments($reportId, $assessmentIds) {
        $reportId = escapeInput($reportId);
        $values = [];
        
        foreach($assessmentIds as $assessmentId) {
            $assessmentId = escapeInput($assessmentId);
            $values[] = "('$reportId', '$assessmentId')";
        }
        
        if(empty($values)) {
            return true;
        }
        
        $sql = "INSERT INTO report_assessments (report_id, assessment_id) VALUES " . implode(', ', $values);
        
        return $this->db->query($sql);
    }
    
    /**
     * 보고서에 연결된 평가 조회
     * 
     * @param int $reportId 보고서 ID
     * @return array 연결된 평가 목록
     */
    public function getLinkedAssessments($reportId) {
        $reportId = escapeInput($reportId);
        
        $sql = "SELECT a.*, ra.report_id
                FROM assessments a
                JOIN report_assessments ra ON a.id = ra.assessment_id
                WHERE ra.report_id = '$reportId'";
        
        $result = $this->db->query($sql);
        
        $assessments = array();
        while($row = $result->fetch_assoc()) {
            $assessments[] = $row;
        }
        
        return $assessments;
    }
    
    /**
     * 모든 보고서 목록 조회
     * 
     * @param array $filters 필터 조건
     * @param string $orderBy 정렬 기준
     * @param int $limit 항목 수 제한
     * @param int $offset 시작 위치
     * @return array 보고서 목록
     */
    public function getAllReports($filters = [], $orderBy = 'created_at DESC', $limit = 0, $offset = 0) {
        $where = "1=1";
        
        // 필터 적용
        if(isset($filters['search']) && !empty($filters['search'])) {
            $search = escapeInput($filters['search']);
            $where .= " AND (r.title LIKE '%$search%' OR r.description LIKE '%$search%')";
        }
        
        if(isset($filters['type']) && !empty($filters['type'])) {
            $type = escapeInput($filters['type']);
            $where .= " AND r.type = '$type'";
        }
        
        if(isset($filters['status']) && !empty($filters['status'])) {
            $status = escapeInput($filters['status']);
            $where .= " AND r.status = '$status'";
        }
        
        if(isset($filters['author_id']) && !empty($filters['author_id'])) {
            $authorId = escapeInput($filters['author_id']);
            $where .= " AND r.author_id = '$authorId'";
        }
        
        if(isset($filters['from_date']) && !empty($filters['from_date'])) {
            $fromDate = escapeInput($filters['from_date']);
            $where .= " AND DATE(r.created_at) >= '$fromDate'";
        }
        
        if(isset($filters['to_date']) && !empty($filters['to_date'])) {
            $toDate = escapeInput($filters['to_date']);
            $where .= " AND DATE(r.created_at) <= '$toDate'";
        }
        
        // 정렬 및 제한 설정
        $orderBy = escapeInput($orderBy);
        $limitClause = '';
        if($limit > 0) {
            $limit = intval($limit);
            $offset = intval($offset);
            $limitClause = "LIMIT $offset, $limit";
        }
        
        $sql = "SELECT r.*, u.full_name as author_name,
                (SELECT COUNT(*) FROM report_assessments WHERE report_id = r.id) as assessment_count
                FROM reports r
                LEFT JOIN users u ON r.author_id = u.id
                WHERE $where
                ORDER BY $orderBy
                $limitClause";
        
        $result = $this->db->query($sql);
        
        $reports = array();
        while($row = $result->fetch_assoc()) {
            $reports[] = $row;
        }
        
        return $reports;
    }
    
    /**
     * 보고서 개수 조회
     * 
     * @param array $filters 필터 조건
     * @return int 보고서 개수
     */
    public function countReports($filters = []) {
        $where = "1=1";
        
        // 필터 적용
        if(isset($filters['search']) && !empty($filters['search'])) {
            $search = escapeInput($filters['search']);
            $where .= " AND (title LIKE '%$search%' OR description LIKE '%$search%')";
        }
        
        if(isset($filters['type']) && !empty($filters['type'])) {
            $type = escapeInput($filters['type']);
            $where .= " AND type = '$type'";
        }
        
        if(isset($filters['status']) && !empty($filters['status'])) {
            $status = escapeInput($filters['status']);
            $where .= " AND status = '$status'";
        }
        
        if(isset($filters['author_id']) && !empty($filters['author_id'])) {
            $authorId = escapeInput($filters['author_id']);
            $where .= " AND author_id = '$authorId'";
        }
        
        $sql = "SELECT COUNT(*) as count FROM reports WHERE $where";
        $result = $this->db->query($sql);
        $row = $result->fetch_assoc();
        
        return $row['count'];
    }
    
    /**
     * 보고서 파일 업로드
     * 
     * @param int $id 보고서 ID
     * @param array $file $_FILES 배열
     * @return bool 업로드 성공 여부
     */
    public function uploadReportFile($id, $file) {
        $id = escapeInput($id);
        
        // 보고서 정보 조회
        $report = $this->getReport($id);
        if(!$report) {
            setErrorMessage('보고서를 찾을 수 없습니다.');
            return false;
        }
        
        // 파일 유효성 검사
        if($file['error'] !== UPLOAD_ERR_OK) {
            $errorMessages = [
                UPLOAD_ERR_INI_SIZE => '파일이 PHP에서 허용된 최대 크기를 초과했습니다.',
                UPLOAD_ERR_FORM_SIZE => '파일이 폼에서 허용된 최대 크기를 초과했습니다.',
                UPLOAD_ERR_PARTIAL => '파일이 일부만 업로드되었습니다.',
                UPLOAD_ERR_NO_FILE => '파일이 업로드되지 않았습니다.',
                UPLOAD_ERR_NO_TMP_DIR => '임시 폴더가 없습니다.',
                UPLOAD_ERR_CANT_WRITE => '디스크에 파일을 쓸 수 없습니다.',
                UPLOAD_ERR_EXTENSION => 'PHP 확장에 의해 파일 업로드가 중지되었습니다.'
            ];
            
            $errorMessage = isset($errorMessages[$file['error']]) ? 
                            $errorMessages[$file['error']] : 
                            '알 수 없는 오류로 파일 업로드에 실패했습니다.';
            
            setErrorMessage($errorMessage);
            return false;
        }
        
        // 파일 확장자 검사
        $allowedExtensions = ['pdf', 'docx', 'doc', 'xlsx', 'xls', 'pptx', 'ppt', 'zip'];
        $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        if(!in_array($fileExtension, $allowedExtensions)) {
            setErrorMessage('허용되지 않는 파일 형식입니다. 허용된 형식: ' . implode(', ', $allowedExtensions));
            return false;
        }
        
        // 파일 크기 검사 (10MB 제한)
        $maxFileSize = 10 * 1024 * 1024; // 10MB
        if($file['size'] > $maxFileSize) {
            setErrorMessage('파일 크기가 너무 큽니다. 최대 10MB까지 허용됩니다.');
            return false;
        }
        
        // 업로드 디렉토리 확인 및 생성
        $uploadDir = '../uploads/reports/';
        if(!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        // 기존 파일 삭제
        if(!empty($report['file_path']) && file_exists($report['file_path'])) {
            unlink($report['file_path']);
        }
        
        // 파일명 생성 (보고서ID_타임스탬프.확장자)
        $newFileName = $id . '_' . time() . '.' . $fileExtension;
        $targetFilePath = $uploadDir . $newFileName;
        
        // 파일 이동
        if(move_uploaded_file($file['tmp_name'], $targetFilePath)) {
            // 데이터베이스 업데이트
            $filePath = escapeInput($targetFilePath);
            $sql = "UPDATE reports SET file_path = '$filePath' WHERE id = '$id'";
            
            if($this->db->query($sql)) {
                // 활동 로그 기록
                logActivity($_SESSION['id'], 'update_report', $_SESSION['full_name'] . '님이 "' . $report['title'] . '" 보고서 파일을 업로드했습니다.', $id, 'report');
                
                setSuccessMessage('보고서 파일이 성공적으로 업로드되었습니다.');
                return true;
            } else {
                // 파일은 업로드되었지만 DB 업데이트 실패 시 파일 삭제
                unlink($targetFilePath);
                setErrorMessage('보고서 파일 정보 업데이트 중 오류가 발생했습니다: ' . $this->db->error);
                return false;
            }
        } else {
            setErrorMessage('파일 업로드 중 오류가 발생했습니다.');
            return false;
        }
    }
    
    /**
     * 보고서 생성을 위한 템플릿 추천
     * 
     * @param array $assessmentIds 평가 ID 배열
     * @param string $templateType 템플릿 유형
     * @param array $focusAreas 중점 영역 배열
     * @return array 템플릿 추천 데이터
     */
    public function recommendTemplate($assessmentIds, $templateType = 'standard', $focusAreas = []) {
        // 실제 구현에서는 AI 알고리즘을 통해 템플릿을 추천하는 로직이 포함됩니다.
        // 여기서는 간단한 예시만 제공합니다.
        
        // 추천 템플릿 기본 구조
        $template = [
            'title' => '',
            'type' => $templateType,
            'sections' => [
                'executive_summary' => true,
                'methodology' => true, 
                'findings' => true,
                'risk_analysis' => true,
                'recommendations' => true,
                'appendices' => false
            ],
            'content_suggestions' => []
        ];
        
        // 연결된 평가 정보 가져오기
        $assessments = [];
        $highRiskCount = 0;
        $projectNames = [];
        
        foreach($assessmentIds as $assessmentId) {
            $assessmentId = escapeInput($assessmentId);
            $sql = "SELECT * FROM assessments WHERE id = '$assessmentId'";
            $result = $this->db->query($sql);
            
            if($result->num_rows == 1) {
                $assessment = $result->fetch_assoc();
                $assessments[] = $assessment;
                $projectNames[] = $assessment['project'];
                
                // 고위험 항목 수 계산
                if(in_array($assessment['risk_level'], ['high', 'very_high'])) {
                    $highRiskCount++;
                }
                
                // 위험 항목 정보 가져오기
                $riskItemsSql = "SELECT * FROM risk_items WHERE assessment_id = '$assessmentId'";
                $riskItemsResult = $this->db->query($riskItemsSql);
                
                $riskItems = [];
                while($row = $riskItemsResult->fetch_assoc()) {
                    $riskItems[] = $row;
                }
                
                $assessment['risk_items'] = $riskItems;
            }
        }
        
        // 템플릿 제목 설정
        if(count($projectNames) == 1) {
            $template['title'] = $projectNames[0] . ' 위험성 평가 보고서';
        } else {
            $template['title'] = '종합 위험성 평가 보고서';
        }
        
        // 템플릿 유형에 따른 조정
        switch($templateType) {
            case 'executive':
                $template['sections']['methodology'] = false;
                $template['sections']['appendices'] = false;
                $template['content_suggestions'][] = '• 주요 발견사항과 위험 요소에 집중한 요약 보고서';
                $template['content_suggestions'][] = '• 모든 고위험 항목에 대한 개요와 권장 조치사항 포함';
                break;
                
            case 'detailed':
                $template['sections']['appendices'] = true;
                $template['content_suggestions'][] = '• 모든 위험 요소와 통제 방안에 대한 상세 분석';
                $template['content_suggestions'][] = '• 각 위험 항목별 세부 원인 분석 및 개선 로드맵 포함';
                break;
                
            case 'compliance':
                $template['content_suggestions'][] = '• 관련 법규 및 규정 준수 여부에 초점';
                $template['content_suggestions'][] = '• 규정 요구사항 대비 현황 분석 및 개선점 제시';
                break;
                
            default: // standard
                $template['content_suggestions'][] = '• 주요 위험 요소 및 대책에 대한 균형 잡힌 보고서';
                $template['content_suggestions'][] = '• 핵심 위험 영역과 개선 우선순위 제시';
                break;
        }
        
        // 중점 영역에 따른 조정
        if(in_array('high_risks', $focusAreas)) {
            $template['content_suggestions'][] = '• 고위험 항목(위험 수준 높음/매우 높음)에 대한 상세 분석';
        }
        
        if(in_array('trend_analysis', $focusAreas)) {
            $template['content_suggestions'][] = '• 기간별 위험성 변화 추이 및 패턴 분석';
        }
        
        if(in_array('mitigation_effectiveness', $focusAreas)) {
            $template['content_suggestions'][] = '• 위험 완화 조치의 효과성 평가 및 개선 방안';
        }
        
        if(in_array('industry_benchmarking', $focusAreas)) {
            $template['content_suggestions'][] = '• 산업 표준 및 벤치마크 대비 현황 비교 분석';
        }
        
        return $template;
    }
    
    /**
     * 보고서 다운로드 기록
     * 
     * @param int $id 보고서 ID
     * @return bool 기록 성공 여부
     */
    public function logReportDownload($id) {
        $id = escapeInput($id);
        
        // 보고서 정보 조회
        $report = $this->getReport($id);
        if(!$report) {
            return false;
        }
        
        // 활동 로그 기록
        logActivity($_SESSION['id'], 'download_report', $_SESSION['full_name'] . '님이 "' . $report['title'] . '" 보고서를 다운로드했습니다.', $id, 'report');
        
        return true;
    }
    
    /**
     * 보고서 공유
     * 
     * @param int $id 보고서 ID
     * @param array $recipients 수신자 정보
     * @param string $message 공유 메시지
     * @return bool 공유 성공 여부
     */
    public function shareReport($id, $recipients, $message = '') {
        $id = escapeInput($id);
        
        // 보고서 정보 조회
        $report = $this->getReport($id);
        if(!$report) {
            setErrorMessage('보고서를 찾을 수 없습니다.');
            return false;
        }
        
        // 실제 구현에서는 이메일 전송 등의 공유 기능이 포함됩니다.
        // 여기서는 활동 로그만 기록합니다.
        $recipientCount = count($recipients);
        
        // 활동 로그 기록
        logActivity($_SESSION['id'], 'share_report', $_SESSION['full_name'] . '님이 "' . $report['title'] . '" 보고서를 ' . $recipientCount . '명의 사용자와 공유했습니다.', $id, 'report');
        
        setSuccessMessage('보고서가 성공적으로 공유되었습니다.');
        return true;
    }
}
?> 