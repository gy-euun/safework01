<?php
// 설정 파일 포함
require_once 'config.php';
require_once 'database.php';

/**
 * 위험성 평가 관리 클래스
 */
class Assessment {
    private $db;
    
    /**
     * 생성자
     */
    public function __construct() {
        $this->db = new Database();
    }
    
    /**
     * 새 평가 생성
     * 
     * @param array $data 평가 데이터
     * @return int|bool 생성된 평가 ID 또는 실패 시 false
     */
    public function createAssessment($data) {
        // 필수 필드 확인
        if (empty($data['title']) || empty($data['project']) || 
            empty($data['assessment_type']) || empty($data['start_date'])) {
            return false;
        }
        
        // 데이터 준비
        $assessmentData = [
            'title' => $data['title'],
            'project' => $data['project'],
            'assessment_type' => $data['assessment_type'],
            'description' => $data['description'] ?? '',
            'status' => $data['status'] ?? 'pending',
            'risk_level' => $data['risk_level'] ?? null,
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'] ?? null,
            'due_date' => $data['due_date'] ?? null,
            'created_by' => $data['created_by'],
            'assigned_to' => $data['assigned_to'] ?? null,
            'progress_rate' => $data['progress_rate'] ?? 0
        ];
        
        // 트랜잭션 시작
        $this->db->beginTransaction();
        
        try {
            // 평가 생성
            $assessmentId = $this->db->createRecord('assessments', $assessmentData);
            
            if (!$assessmentId) {
                throw new Exception("평가 생성에 실패했습니다.");
            }
            
            // 활동 로그 기록
            logActivity(
                $data['created_by'],
                'create_assessment',
                "'{$data['title']}' 평가가 생성되었습니다.",
                $assessmentId,
                'assessment'
            );
            
            // 트랜잭션 커밋
            $this->db->commitTransaction();
            
            return $assessmentId;
        } catch (Exception $e) {
            // 오류 발생 시 롤백
            $this->db->rollbackTransaction();
            return false;
        }
    }
    
    /**
     * 평가 업데이트
     * 
     * @param int $id 평가 ID
     * @param array $data 업데이트할 평가 데이터
     * @return bool 성공 여부
     */
    public function updateAssessment($id, $data) {
        // 평가 존재 여부 확인
        $assessment = $this->getAssessment($id);
        if (!$assessment) {
            return false;
        }
        
        // 업데이트할 데이터 준비
        $updateData = [];
        
        if (isset($data['title'])) {
            $updateData['title'] = $data['title'];
        }
        
        if (isset($data['project'])) {
            $updateData['project'] = $data['project'];
        }
        
        if (isset($data['assessment_type'])) {
            $updateData['assessment_type'] = $data['assessment_type'];
        }
        
        if (isset($data['description'])) {
            $updateData['description'] = $data['description'];
        }
        
        if (isset($data['status'])) {
            $updateData['status'] = $data['status'];
        }
        
        if (isset($data['risk_level'])) {
            $updateData['risk_level'] = $data['risk_level'];
        }
        
        if (isset($data['start_date'])) {
            $updateData['start_date'] = $data['start_date'];
        }
        
        if (isset($data['end_date'])) {
            $updateData['end_date'] = $data['end_date'];
        }
        
        if (isset($data['due_date'])) {
            $updateData['due_date'] = $data['due_date'];
        }
        
        if (isset($data['assigned_to'])) {
            $updateData['assigned_to'] = $data['assigned_to'];
        }
        
        if (isset($data['progress_rate'])) {
            $updateData['progress_rate'] = $data['progress_rate'];
        }
        
        if (empty($updateData)) {
            return true; // 업데이트할 데이터가 없으면 성공으로 간주
        }
        
        // 트랜잭션 시작
        $this->db->beginTransaction();
        
        try {
            // 평가 업데이트
            $result = $this->db->updateRecord('assessments', $id, $updateData);
            
            if (!$result) {
                throw new Exception("평가 업데이트에 실패했습니다.");
            }
            
            // 활동 로그 기록
            $userId = isset($data['updated_by']) ? $data['updated_by'] : $_SESSION['id'];
            logActivity(
                $userId,
                'update_assessment',
                "'{$assessment['title']}' 평가가 업데이트되었습니다.",
                $id,
                'assessment'
            );
            
            // 트랜잭션 커밋
            $this->db->commitTransaction();
            
            return true;
        } catch (Exception $e) {
            // 오류 발생 시 롤백
            $this->db->rollbackTransaction();
            return false;
        }
    }
    
    /**
     * 특정 평가 조회
     * 
     * @param int $id 평가 ID
     * @return array|bool 평가 데이터 또는 실패 시 false
     */
    public function getAssessment($id) {
        $sql = "SELECT a.*, u1.username as creator_name, u1.full_name as creator_full_name, 
                u2.username as assignee_name, u2.full_name as assignee_full_name
                FROM assessments a
                LEFT JOIN users u1 ON a.created_by = u1.id
                LEFT JOIN users u2 ON a.assigned_to = u2.id
                WHERE a.id = '{$this->db->escape($id)}'";
        
        $result = $this->db->query($sql);
        
        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        
        return false;
    }
    
    /**
     * 평가 삭제
     * 
     * @param int $id 평가 ID
     * @return bool 성공 여부
     */
    public function deleteAssessment($id) {
        // 평가 존재 여부 확인
        $assessment = $this->getAssessment($id);
        if (!$assessment) {
            return false;
        }
        
        // 트랜잭션 시작
        $this->db->beginTransaction();
        
        try {
            // 관련된 위험 항목 조회
            $sql = "SELECT id FROM risk_items WHERE assessment_id = '{$this->db->escape($id)}'";
            $result = $this->db->query($sql);
            $riskItemIds = [];
            
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $riskItemIds[] = $row['id'];
                }
            }
            
            // 위험 항목 삭제
            if (!empty($riskItemIds)) {
                $sql = "DELETE FROM risk_items WHERE assessment_id = '{$this->db->escape($id)}'";
                $this->db->query($sql);
            }
            
            // 보고서 연결 항목 삭제
            $sql = "DELETE FROM report_assessments WHERE assessment_id = '{$this->db->escape($id)}'";
            $this->db->query($sql);
            
            // 평가 삭제
            $result = $this->db->deleteRecord('assessments', $id);
            
            if (!$result) {
                throw new Exception("평가 삭제에 실패했습니다.");
            }
            
            // 활동 로그 기록
            $userId = isset($_SESSION['id']) ? $_SESSION['id'] : 1; // 기본값 설정
            logActivity(
                $userId,
                'delete_assessment',
                "'{$assessment['title']}' 평가가 삭제되었습니다.",
                null,
                null
            );
            
            // 트랜잭션 커밋
            $this->db->commitTransaction();
            
            return true;
        } catch (Exception $e) {
            // 오류 발생 시 롤백
            $this->db->rollbackTransaction();
            return false;
        }
    }
    
    /**
     * 평가 완료 처리
     * 
     * @param int $id 평가 ID
     * @param array $data 평가 완료 관련 데이터
     * @return bool 성공 여부
     */
    public function completeAssessment($id, $data) {
        // 평가 존재 여부 확인
        $assessment = $this->getAssessment($id);
        if (!$assessment) {
            return false;
        }
        
        if ($assessment['status'] === 'completed') {
            return true; // 이미 완료된 상태면 성공으로 간주
        }
        
        // 완료 데이터 준비
        $updateData = [
            'status' => 'completed',
            'completion_date' => date('Y-m-d'),
            'progress_rate' => 100
        ];
        
        if (isset($data['end_date'])) {
            $updateData['end_date'] = $data['end_date'];
        } else {
            $updateData['end_date'] = date('Y-m-d');
        }
        
        if (isset($data['risk_level'])) {
            $updateData['risk_level'] = $data['risk_level'];
        } else {
            // 위험 항목에 기반한 자동 위험 수준 계산
            $riskLevel = $this->calculateAssessmentRiskLevel($id);
            if ($riskLevel) {
                $updateData['risk_level'] = $riskLevel;
            }
        }
        
        // 트랜잭션 시작
        $this->db->beginTransaction();
        
        try {
            // 평가 업데이트
            $result = $this->db->updateRecord('assessments', $id, $updateData);
            
            if (!$result) {
                throw new Exception("평가 완료 처리에 실패했습니다.");
            }
            
            // 활동 로그 기록
            $userId = isset($data['user_id']) ? $data['user_id'] : $_SESSION['id'];
            logActivity(
                $userId,
                'complete_assessment',
                "'{$assessment['title']}' 평가가 완료되었습니다.",
                $id,
                'assessment'
            );
            
            // 트랜잭션 커밋
            $this->db->commitTransaction();
            
            return true;
        } catch (Exception $e) {
            // 오류 발생 시 롤백
            $this->db->rollbackTransaction();
            return false;
        }
    }
    
    /**
     * 모든 평가 목록 조회
     * 
     * @param array $filters 필터 조건
     * @param string $orderBy 정렬 기준
     * @param int $limit 조회할 최대 레코드 수
     * @param int $offset 오프셋
     * @return array 평가 목록
     */
    public function getAllAssessments($filters = [], $orderBy = 'created_at DESC', $limit = 0, $offset = 0) {
        $sql = "SELECT a.*, u1.username as creator_name, u1.full_name as creator_full_name, 
                u2.username as assignee_name, u2.full_name as assignee_full_name
                FROM assessments a
                LEFT JOIN users u1 ON a.created_by = u1.id
                LEFT JOIN users u2 ON a.assigned_to = u2.id
                WHERE 1=1";
        
        // 제목 검색
        if (isset($filters['title'])) {
            $title = $this->db->escape($filters['title']);
            $sql .= " AND a.title LIKE '%$title%'";
        }
        
        // 프로젝트 검색
        if (isset($filters['project'])) {
            $project = $this->db->escape($filters['project']);
            $sql .= " AND a.project LIKE '%$project%'";
        }
        
        // 평가 유형 필터
        if (isset($filters['assessment_type'])) {
            $assessmentType = $this->db->escape($filters['assessment_type']);
            $sql .= " AND a.assessment_type = '$assessmentType'";
        }
        
        // 상태 필터
        if (isset($filters['status'])) {
            $status = $this->db->escape($filters['status']);
            $sql .= " AND a.status = '$status'";
        }
        
        // 위험 수준 필터
        if (isset($filters['risk_level'])) {
            $riskLevel = $this->db->escape($filters['risk_level']);
            $sql .= " AND a.risk_level = '$riskLevel'";
        }
        
        // 작성자 필터
        if (isset($filters['created_by'])) {
            $createdBy = $this->db->escape($filters['created_by']);
            $sql .= " AND a.created_by = '$createdBy'";
        }
        
        // 담당자 필터
        if (isset($filters['assigned_to'])) {
            $assignedTo = $this->db->escape($filters['assigned_to']);
            $sql .= " AND a.assigned_to = '$assignedTo'";
        }
        
        // 일자 범위 필터
        if (isset($filters['start_date_from']) && isset($filters['start_date_to'])) {
            $startDateFrom = $this->db->escape($filters['start_date_from']);
            $startDateTo = $this->db->escape($filters['start_date_to']);
            $sql .= " AND a.start_date BETWEEN '$startDateFrom' AND '$startDateTo'";
        } else if (isset($filters['start_date_from'])) {
            $startDateFrom = $this->db->escape($filters['start_date_from']);
            $sql .= " AND a.start_date >= '$startDateFrom'";
        } else if (isset($filters['start_date_to'])) {
            $startDateTo = $this->db->escape($filters['start_date_to']);
            $sql .= " AND a.start_date <= '$startDateTo'";
        }
        
        // 키워드 검색
        if (isset($filters['keyword'])) {
            $keyword = $this->db->escape($filters['keyword']);
            $sql .= " AND (a.title LIKE '%$keyword%' OR a.project LIKE '%$keyword%' OR a.description LIKE '%$keyword%')";
        }
        
        // 정렬
        if (!empty($orderBy)) {
            $sql .= " ORDER BY $orderBy";
        }
        
        // 페이지네이션
        if ($limit > 0) {
            $limit = (int)$limit;
            $offset = (int)$offset;
            $sql .= " LIMIT $limit OFFSET $offset";
        }
        
        $result = $this->db->query($sql);
        
        return $this->db->resultToArray($result);
    }
    
    /**
     * 평가 수 카운트
     * 
     * @param array $filters 필터 조건
     * @return int 평가 수
     */
    public function countAssessments($filters = []) {
        $sql = "SELECT COUNT(*) as count FROM assessments a
                LEFT JOIN users u1 ON a.created_by = u1.id
                LEFT JOIN users u2 ON a.assigned_to = u2.id
                WHERE 1=1";
        
        // 제목 검색
        if (isset($filters['title'])) {
            $title = $this->db->escape($filters['title']);
            $sql .= " AND a.title LIKE '%$title%'";
        }
        
        // 프로젝트 검색
        if (isset($filters['project'])) {
            $project = $this->db->escape($filters['project']);
            $sql .= " AND a.project LIKE '%$project%'";
        }
        
        // 평가 유형 필터
        if (isset($filters['assessment_type'])) {
            $assessmentType = $this->db->escape($filters['assessment_type']);
            $sql .= " AND a.assessment_type = '$assessmentType'";
        }
        
        // 상태 필터
        if (isset($filters['status'])) {
            $status = $this->db->escape($filters['status']);
            $sql .= " AND a.status = '$status'";
        }
        
        // 위험 수준 필터
        if (isset($filters['risk_level'])) {
            $riskLevel = $this->db->escape($filters['risk_level']);
            $sql .= " AND a.risk_level = '$riskLevel'";
        }
        
        // 작성자 필터
        if (isset($filters['created_by'])) {
            $createdBy = $this->db->escape($filters['created_by']);
            $sql .= " AND a.created_by = '$createdBy'";
        }
        
        // 담당자 필터
        if (isset($filters['assigned_to'])) {
            $assignedTo = $this->db->escape($filters['assigned_to']);
            $sql .= " AND a.assigned_to = '$assignedTo'";
        }
        
        // 일자 범위 필터
        if (isset($filters['start_date_from']) && isset($filters['start_date_to'])) {
            $startDateFrom = $this->db->escape($filters['start_date_from']);
            $startDateTo = $this->db->escape($filters['start_date_to']);
            $sql .= " AND a.start_date BETWEEN '$startDateFrom' AND '$startDateTo'";
        } else if (isset($filters['start_date_from'])) {
            $startDateFrom = $this->db->escape($filters['start_date_from']);
            $sql .= " AND a.start_date >= '$startDateFrom'";
        } else if (isset($filters['start_date_to'])) {
            $startDateTo = $this->db->escape($filters['start_date_to']);
            $sql .= " AND a.start_date <= '$startDateTo'";
        }
        
        // 키워드 검색
        if (isset($filters['keyword'])) {
            $keyword = $this->db->escape($filters['keyword']);
            $sql .= " AND (a.title LIKE '%$keyword%' OR a.project LIKE '%$keyword%' OR a.description LIKE '%$keyword%')";
        }
        
        $result = $this->db->query($sql);
        $row = $result->fetch_assoc();
        
        return (int)$row['count'];
    }
    
    /**
     * 진행 중인 평가 목록 조회
     * 
     * @param array $filters 필터 조건
     * @param int $limit 조회할 최대 레코드 수
     * @return array 진행 중인 평가 목록
     */
    public function getInProgressAssessments($filters = [], $limit = 0) {
        $filters['status'] = 'in_progress';
        return $this->getAllAssessments($filters, 'due_date ASC', $limit);
    }
    
    /**
     * 완료된 평가 목록 조회
     * 
     * @param array $filters 필터 조건
     * @param int $limit 조회할 최대 레코드 수
     * @return array 완료된 평가 목록
     */
    public function getCompletedAssessments($filters = [], $limit = 0) {
        $filters['status'] = 'completed';
        return $this->getAllAssessments($filters, 'completion_date DESC', $limit);
    }
    
    /**
     * 평가 요약 정보 조회
     * 
     * @return array 평가 요약 정보
     */
    public function getAssessmentSummary() {
        $today = date('Y-m-d');
        $weekAgo = date('Y-m-d', strtotime('-7 days'));
        $monthAgo = date('Y-m-d', strtotime('-30 days'));
        
        // 전체 평가 수
        $sql = "SELECT 
                COUNT(*) as total_count,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_count,
                SUM(CASE WHEN status = 'in_progress' THEN 1 ELSE 0 END) as in_progress_count,
                SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_count,
                SUM(CASE WHEN created_at >= '$weekAgo' THEN 1 ELSE 0 END) as created_last_week,
                SUM(CASE WHEN completion_date IS NOT NULL AND completion_date >= '$weekAgo' THEN 1 ELSE 0 END) as completed_last_week,
                SUM(CASE WHEN risk_level = 'very_high' THEN 1 ELSE 0 END) as very_high_risk_count,
                SUM(CASE WHEN risk_level = 'high' THEN 1 ELSE 0 END) as high_risk_count,
                SUM(CASE WHEN risk_level = 'medium' THEN 1 ELSE 0 END) as medium_risk_count,
                SUM(CASE WHEN risk_level = 'low' THEN 1 ELSE 0 END) as low_risk_count,
                SUM(CASE WHEN risk_level = 'very_low' THEN 1 ELSE 0 END) as very_low_risk_count
                FROM assessments";
        
        $result = $this->db->query($sql);
        $summary = $result->fetch_assoc();
        
        // 만기된 평가 수
        $sql = "SELECT COUNT(*) as overdue_count
                FROM assessments
                WHERE status != 'completed' AND due_date < '$today'";
        
        $result = $this->db->query($sql);
        $overdueCounts = $result->fetch_assoc();
        $summary['overdue_count'] = $overdueCounts['overdue_count'];
        
        // 곧 만기되는 평가 수
        $oneWeekLater = date('Y-m-d', strtotime('+7 days'));
        $sql = "SELECT COUNT(*) as due_soon_count
                FROM assessments
                WHERE status != 'completed' AND due_date BETWEEN '$today' AND '$oneWeekLater'";
        
        $result = $this->db->query($sql);
        $dueSoonCounts = $result->fetch_assoc();
        $summary['due_soon_count'] = $dueSoonCounts['due_soon_count'];
        
        // 위험 항목 통계
        $sql = "SELECT 
                COUNT(*) as total_risk_items,
                SUM(CASE WHEN status = 'identified' THEN 1 ELSE 0 END) as identified_risk_count,
                SUM(CASE WHEN status = 'in_progress' THEN 1 ELSE 0 END) as in_progress_risk_count,
                SUM(CASE WHEN status = 'resolved' THEN 1 ELSE 0 END) as resolved_risk_count,
                SUM(CASE WHEN risk_level = 'very_high' THEN 1 ELSE 0 END) as very_high_risk_items,
                SUM(CASE WHEN risk_level = 'high' THEN 1 ELSE 0 END) as high_risk_items,
                SUM(CASE WHEN risk_level = 'medium' THEN 1 ELSE 0 END) as medium_risk_items,
                SUM(CASE WHEN risk_level = 'low' THEN 1 ELSE 0 END) as low_risk_items,
                SUM(CASE WHEN risk_level = 'very_low' THEN 1 ELSE 0 END) as very_low_risk_items
                FROM risk_items";
        
        $result = $this->db->query($sql);
        $riskStats = $result->fetch_assoc();
        $summary = array_merge($summary, $riskStats);
        
        return $summary;
    }
    
    /**
     * 위험 항목 추가
     * 
     * @param int $assessmentId 평가 ID
     * @param array $data 위험 항목 데이터
     * @return int|bool 생성된 위험 항목 ID 또는 실패 시 false
     */
    public function addRiskItem($assessmentId, $data) {
        // 평가 존재 여부 확인
        $assessment = $this->getAssessment($assessmentId);
        if (!$assessment) {
            return false;
        }
        
        // 필수 필드 확인
        if (empty($data['hazard_type']) || empty($data['hazard_name']) || 
            empty($data['hazard_situation']) || !isset($data['likelihood']) || !isset($data['severity'])) {
            return false;
        }
        
        // 위험 수준 계산
        $riskLevel = $this->calculateRiskLevel($data['likelihood'], $data['severity']);
        
        // 데이터 준비
        $riskItemData = [
            'assessment_id' => $assessmentId,
            'hazard_type' => $data['hazard_type'],
            'hazard_name' => $data['hazard_name'],
            'hazard_situation' => $data['hazard_situation'],
            'likelihood' => $data['likelihood'],
            'severity' => $data['severity'],
            'risk_level' => $riskLevel,
            'control_measures' => $data['control_measures'] ?? '',
            'responsible_person' => $data['responsible_person'] ?? '',
            'implementation_period' => $data['implementation_period'] ?? '',
            'status' => $data['status'] ?? 'identified'
        ];
        
        // 트랜잭션 시작
        $this->db->beginTransaction();
        
        try {
            // 위험 항목 생성
            $riskItemId = $this->db->createRecord('risk_items', $riskItemData);
            
            if (!$riskItemId) {
                throw new Exception("위험 항목 추가에 실패했습니다.");
            }
            
            // 활동 로그 기록
            $userId = isset($data['user_id']) ? $data['user_id'] : $_SESSION['id'];
            logActivity(
                $userId,
                'identify_risk',
                "'{$assessment['title']}' 평가에서 '{$data['hazard_name']}' 위험 요소가 식별되었습니다.",
                $riskItemId,
                'risk_item'
            );
            
            // 트랜잭션 커밋
            $this->db->commitTransaction();
            
            return $riskItemId;
        } catch (Exception $e) {
            // 오류 발생 시 롤백
            $this->db->rollbackTransaction();
            return false;
        }
    }
    
    /**
     * 위험 항목 업데이트
     * 
     * @param int $riskItemId 위험 항목 ID
     * @param array $data 업데이트할 위험 항목 데이터
     * @return bool 성공 여부
     */
    public function updateRiskItem($riskItemId, $data) {
        // 위험 항목 존재 여부 확인
        $riskItem = $this->getRiskItem($riskItemId);
        if (!$riskItem) {
            return false;
        }
        
        // 업데이트할 데이터 준비
        $updateData = [];
        
        if (isset($data['hazard_type'])) {
            $updateData['hazard_type'] = $data['hazard_type'];
        }
        
        if (isset($data['hazard_name'])) {
            $updateData['hazard_name'] = $data['hazard_name'];
        }
        
        if (isset($data['hazard_situation'])) {
            $updateData['hazard_situation'] = $data['hazard_situation'];
        }
        
        // 가능성이나 심각도가 변경되면 위험 수준 재계산
        $recalculateRiskLevel = false;
        
        if (isset($data['likelihood'])) {
            $updateData['likelihood'] = $data['likelihood'];
            $recalculateRiskLevel = true;
        }
        
        if (isset($data['severity'])) {
            $updateData['severity'] = $data['severity'];
            $recalculateRiskLevel = true;
        }
        
        if ($recalculateRiskLevel) {
            $likelihood = isset($data['likelihood']) ? $data['likelihood'] : $riskItem['likelihood'];
            $severity = isset($data['severity']) ? $data['severity'] : $riskItem['severity'];
            $updateData['risk_level'] = $this->calculateRiskLevel($likelihood, $severity);
        }
        
        if (isset($data['control_measures'])) {
            $updateData['control_measures'] = $data['control_measures'];
        }
        
        if (isset($data['responsible_person'])) {
            $updateData['responsible_person'] = $data['responsible_person'];
        }
        
        if (isset($data['implementation_period'])) {
            $updateData['implementation_period'] = $data['implementation_period'];
        }
        
        if (isset($data['status'])) {
            $updateData['status'] = $data['status'];
        }
        
        if (empty($updateData)) {
            return true; // 업데이트할 데이터가 없으면 성공으로 간주
        }
        
        // 트랜잭션 시작
        $this->db->beginTransaction();
        
        try {
            // 위험 항목 업데이트
            $result = $this->db->updateRecord('risk_items', $riskItemId, $updateData);
            
            if (!$result) {
                throw new Exception("위험 항목 업데이트에 실패했습니다.");
            }
            
            // 활동 로그 기록
            $userId = isset($data['user_id']) ? $data['user_id'] : $_SESSION['id'];
            $activityType = isset($data['status']) && $data['status'] === 'resolved' ? 'resolve_risk' : 'update_risk';
            $description = isset($data['status']) && $data['status'] === 'resolved' 
                ? "'{$riskItem['hazard_name']}' 위험 요소가 해결되었습니다." 
                : "'{$riskItem['hazard_name']}' 위험 요소가 업데이트되었습니다.";
            
            logActivity(
                $userId,
                $activityType,
                $description,
                $riskItemId,
                'risk_item'
            );
            
            // 트랜잭션 커밋
            $this->db->commitTransaction();
            
            return true;
        } catch (Exception $e) {
            // 오류 발생 시 롤백
            $this->db->rollbackTransaction();
            return false;
        }
    }
    
    /**
     * 특정 위험 항목 조회
     * 
     * @param int $riskItemId 위험 항목 ID
     * @return array|bool 위험 항목 데이터 또는 실패 시 false
     */
    public function getRiskItem($riskItemId) {
        $sql = "SELECT r.*, a.title as assessment_title, a.project as assessment_project
                FROM risk_items r
                JOIN assessments a ON r.assessment_id = a.id
                WHERE r.id = '{$this->db->escape($riskItemId)}'";
        
        $result = $this->db->query($sql);
        
        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        
        return false;
    }
    
    /**
     * 위험 항목 삭제
     * 
     * @param int $riskItemId 위험 항목 ID
     * @return bool 성공 여부
     */
    public function deleteRiskItem($riskItemId) {
        // 위험 항목 존재 여부 확인
        $riskItem = $this->getRiskItem($riskItemId);
        if (!$riskItem) {
            return false;
        }
        
        // 트랜잭션 시작
        $this->db->beginTransaction();
        
        try {
            // 위험 항목 삭제
            $result = $this->db->deleteRecord('risk_items', $riskItemId);
            
            if (!$result) {
                throw new Exception("위험 항목 삭제에 실패했습니다.");
            }
            
            // 활동 로그 기록
            $userId = isset($_SESSION['id']) ? $_SESSION['id'] : 1; // 기본값 설정
            logActivity(
                $userId,
                'delete_risk',
                "'{$riskItem['hazard_name']}' 위험 요소가 삭제되었습니다.",
                $riskItem['assessment_id'],
                'assessment'
            );
            
            // 트랜잭션 커밋
            $this->db->commitTransaction();
            
            return true;
        } catch (Exception $e) {
            // 오류 발생 시 롤백
            $this->db->rollbackTransaction();
            return false;
        }
    }
    
    /**
     * 특정 평가의 위험 항목 목록 조회
     * 
     * @param int $assessmentId 평가 ID
     * @param array $filters 필터 조건
     * @return array 위험 항목 목록
     */
    public function getRiskItems($assessmentId, $filters = []) {
        $sql = "SELECT r.*, a.title as assessment_title
                FROM risk_items r
                JOIN assessments a ON r.assessment_id = a.id
                WHERE r.assessment_id = '{$this->db->escape($assessmentId)}'";
        
        // 위험 유형 필터
        if (isset($filters['hazard_type'])) {
            $hazardType = $this->db->escape($filters['hazard_type']);
            $sql .= " AND r.hazard_type = '$hazardType'";
        }
        
        // 위험 수준 필터
        if (isset($filters['risk_level'])) {
            $riskLevel = $this->db->escape($filters['risk_level']);
            $sql .= " AND r.risk_level = '$riskLevel'";
        }
        
        // 상태 필터
        if (isset($filters['status'])) {
            $status = $this->db->escape($filters['status']);
            $sql .= " AND r.status = '$status'";
        }
        
        // 순서: 상위 위험이 먼저 나오도록
        $sql .= " ORDER BY 
                CASE r.risk_level
                    WHEN 'very_high' THEN 1
                    WHEN 'high' THEN 2
                    WHEN 'medium' THEN 3
                    WHEN 'low' THEN 4
                    WHEN 'very_low' THEN 5
                END, r.created_at DESC";
        
        $result = $this->db->query($sql);
        
        return $this->db->resultToArray($result);
    }
    
    /**
     * 높은 위험 수준의 항목 목록 조회
     * 
     * @param array $filters 필터 조건
     * @param int $limit 조회할 최대 레코드 수
     * @return array 높은 위험 수준의 위험 항목 목록
     */
    public function getHighRiskItems($filters = [], $limit = 0) {
        $sql = "SELECT r.*, a.title as assessment_title, a.project as assessment_project
                FROM risk_items r
                JOIN assessments a ON r.assessment_id = a.id
                WHERE (r.risk_level = 'very_high' OR r.risk_level = 'high')";
        
        // 평가 ID 필터
        if (isset($filters['assessment_id'])) {
            $assessmentId = $this->db->escape($filters['assessment_id']);
            $sql .= " AND r.assessment_id = '$assessmentId'";
        }
        
        // 상태 필터
        if (isset($filters['status'])) {
            $status = $this->db->escape($filters['status']);
            $sql .= " AND r.status = '$status'";
        } else {
            // 기본적으로 미해결 항목만 조회
            $sql .= " AND r.status != 'resolved'";
        }
        
        // 위험 유형 필터
        if (isset($filters['hazard_type'])) {
            $hazardType = $this->db->escape($filters['hazard_type']);
            $sql .= " AND r.hazard_type = '$hazardType'";
        }
        
        // 정렬: 매우 높은 위험이 먼저, 그 다음 높은 위험
        $sql .= " ORDER BY 
                CASE r.risk_level
                    WHEN 'very_high' THEN 1
                    WHEN 'high' THEN 2
                END, r.created_at DESC";
        
        // 결과 개수 제한
        if ($limit > 0) {
            $limit = (int)$limit;
            $sql .= " LIMIT $limit";
        }
        
        $result = $this->db->query($sql);
        
        return $this->db->resultToArray($result);
    }
    
    /**
     * 평가의 전체 위험 수준 계산 (위험 항목에 기반)
     * 
     * @param int $assessmentId 평가 ID
     * @return string|null 위험 수준 또는 실패 시 null
     */
    private function calculateAssessmentRiskLevel($assessmentId) {
        $sql = "SELECT 
                MAX(CASE 
                    WHEN risk_level = 'very_high' THEN 5
                    WHEN risk_level = 'high' THEN 4
                    WHEN risk_level = 'medium' THEN 3
                    WHEN risk_level = 'low' THEN 2
                    WHEN risk_level = 'very_low' THEN 1
                    ELSE 0
                END) as max_risk_level
                FROM risk_items
                WHERE assessment_id = '{$this->db->escape($assessmentId)}'";
        
        $result = $this->db->query($sql);
        
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $maxLevel = $row['max_risk_level'];
            
            // 숫자를 위험 수준 문자열로 변환
            switch ($maxLevel) {
                case 5: return 'very_high';
                case 4: return 'high';
                case 3: return 'medium';
                case 2: return 'low';
                case 1: return 'very_low';
                default: return 'medium'; // 위험 항목이 없는 경우
            }
        }
        
        return null;
    }
    
    /**
     * 가능성과 심각도에 기반한 위험 수준 계산
     * 
     * @param int $likelihood 가능성 (1-5)
     * @param int $severity 심각도 (1-5)
     * @return string 위험 수준
     */
    private function calculateRiskLevel($likelihood, $severity) {
        $riskScore = $likelihood * $severity;
        
        if ($riskScore >= 20) {
            return 'very_high';
        } else if ($riskScore >= 12) {
            return 'high';
        } else if ($riskScore >= 8) {
            return 'medium';
        } else if ($riskScore >= 3) {
            return 'low';
        } else {
            return 'very_low';
        }
    }
}
?> 