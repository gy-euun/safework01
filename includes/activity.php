<?php
/**
 * 활동 로그 관리 클래스
 * 
 * 이 클래스는 시스템 내의 사용자 활동을 기록하고 조회하는 기능을 제공합니다.
 * 사용자 활동 추적, 통계, 분석 등의 기능을 포함합니다.
 * 
 * @version 1.0
 */

// 필요한 파일 불러오기
require_once 'config.php';
require_once 'database.php';

class Activity {
    private $db;
    
    /**
     * 생성자
     */
    public function __construct() {
        $this->db = new Database();
    }
    
    /**
     * 활동 로그 기록
     * 
     * @param int $userId 사용자 ID
     * @param string $activityType 활동 유형
     * @param string $description 활동 설명
     * @param int|null $relatedId 관련 항목 ID
     * @param string|null $relatedType 관련 항목 유형
     * @return int|bool 생성된 활동 로그 ID 또는 실패 시 false
     */
    public function logActivity($userId, $activityType, $description, $relatedId = null, $relatedType = null) {
        $data = [
            'user_id' => $userId,
            'activity_type' => $activityType,
            'description' => $description,
            'related_id' => $relatedId,
            'related_type' => $relatedType,
            'ip_address' => $_SERVER['REMOTE_ADDR']
        ];
        
        return $this->db->createRecord('activities', $data);
    }
    
    /**
     * 특정 사용자의 활동 로그 조회
     * 
     * @param int $userId 사용자 ID
     * @param array $filters 필터 조건
     * @param int $limit 조회할 최대 레코드 수
     * @param int $offset 오프셋
     * @return array 활동 로그 목록
     */
    public function getUserActivities($userId, $filters = [], $limit = 0, $offset = 0) {
        $conditions = array_merge(['user_id' => $userId], $filters);
        
        $sql = "SELECT a.*, u.username, u.full_name, u.email
                FROM activities a
                JOIN users u ON a.user_id = u.id
                WHERE a.user_id = '{$this->db->escape($userId)}'";
        
        // 활동 유형 필터
        if (isset($filters['activity_type'])) {
            $activityType = $this->db->escape($filters['activity_type']);
            $sql .= " AND a.activity_type = '$activityType'";
        }
        
        // 날짜 범위 필터
        if (isset($filters['start_date']) && isset($filters['end_date'])) {
            $startDate = $this->db->escape($filters['start_date']);
            $endDate = $this->db->escape($filters['end_date']);
            $sql .= " AND DATE(a.created_at) BETWEEN '$startDate' AND '$endDate'";
        } else if (isset($filters['start_date'])) {
            $startDate = $this->db->escape($filters['start_date']);
            $sql .= " AND DATE(a.created_at) >= '$startDate'";
        } else if (isset($filters['end_date'])) {
            $endDate = $this->db->escape($filters['end_date']);
            $sql .= " AND DATE(a.created_at) <= '$endDate'";
        }
        
        // 관련 항목 필터
        if (isset($filters['related_type'])) {
            $relatedType = $this->db->escape($filters['related_type']);
            $sql .= " AND a.related_type = '$relatedType'";
        }
        
        if (isset($filters['related_id'])) {
            $relatedId = $this->db->escape($filters['related_id']);
            $sql .= " AND a.related_id = '$relatedId'";
        }
        
        // 키워드 검색
        if (isset($filters['keyword'])) {
            $keyword = $this->db->escape($filters['keyword']);
            $sql .= " AND (a.description LIKE '%$keyword%')";
        }
        
        $sql .= " ORDER BY a.created_at DESC";
        
        if ($limit > 0) {
            $limit = (int)$limit;
            $offset = (int)$offset;
            $sql .= " LIMIT $limit OFFSET $offset";
        }
        
        $result = $this->db->query($sql);
        
        return $this->db->resultToArray($result);
    }
    
    /**
     * 전체 활동 로그 조회
     * 
     * @param array $filters 필터 조건
     * @param int $limit 조회할 최대 레코드 수
     * @param int $offset 오프셋
     * @return array 활동 로그 목록
     */
    public function getAllActivities($filters = [], $limit = 0, $offset = 0) {
        $sql = "SELECT a.*, u.username, u.full_name, u.department
                FROM activities a
                JOIN users u ON a.user_id = u.id
                WHERE 1=1";
        
        // 사용자 필터
        if (isset($filters['user_id'])) {
            $userId = $this->db->escape($filters['user_id']);
            $sql .= " AND a.user_id = '$userId'";
        }
        
        // 부서 필터
        if (isset($filters['department'])) {
            $department = $this->db->escape($filters['department']);
            $sql .= " AND u.department = '$department'";
        }
        
        // 활동 유형 필터
        if (isset($filters['activity_type'])) {
            $activityType = $this->db->escape($filters['activity_type']);
            $sql .= " AND a.activity_type = '$activityType'";
        }
        
        // 날짜 범위 필터
        if (isset($filters['start_date']) && isset($filters['end_date'])) {
            $startDate = $this->db->escape($filters['start_date']);
            $endDate = $this->db->escape($filters['end_date']);
            $sql .= " AND DATE(a.created_at) BETWEEN '$startDate' AND '$endDate'";
        } else if (isset($filters['start_date'])) {
            $startDate = $this->db->escape($filters['start_date']);
            $sql .= " AND DATE(a.created_at) >= '$startDate'";
        } else if (isset($filters['end_date'])) {
            $endDate = $this->db->escape($filters['end_date']);
            $sql .= " AND DATE(a.created_at) <= '$endDate'";
        }
        
        // 관련 항목 필터
        if (isset($filters['related_type'])) {
            $relatedType = $this->db->escape($filters['related_type']);
            $sql .= " AND a.related_type = '$relatedType'";
        }
        
        if (isset($filters['related_id'])) {
            $relatedId = $this->db->escape($filters['related_id']);
            $sql .= " AND a.related_id = '$relatedId'";
        }
        
        // 키워드 검색
        if (isset($filters['keyword'])) {
            $keyword = $this->db->escape($filters['keyword']);
            $sql .= " AND (a.description LIKE '%$keyword%')";
        }
        
        $sql .= " ORDER BY a.created_at DESC";
        
        if ($limit > 0) {
            $limit = (int)$limit;
            $offset = (int)$offset;
            $sql .= " LIMIT $limit OFFSET $offset";
        }
        
        $result = $this->db->query($sql);
        
        return $this->db->resultToArray($result);
    }
    
    /**
     * 특정 항목과 관련된 활동 로그 조회
     * 
     * @param string $relatedType 관련 항목 유형
     * @param int $relatedId 관련 항목 ID
     * @param int $limit 조회할 최대 레코드 수
     * @return array 활동 로그 목록
     */
    public function getItemActivities($relatedType, $relatedId, $limit = 0) {
        $relatedType = $this->db->escape($relatedType);
        $relatedId = $this->db->escape($relatedId);
        
        $sql = "SELECT a.*, u.username, u.full_name, u.email
                FROM activities a
                JOIN users u ON a.user_id = u.id
                WHERE a.related_type = '$relatedType' AND a.related_id = '$relatedId'
                ORDER BY a.created_at DESC";
        
        if ($limit > 0) {
            $limit = (int)$limit;
            $sql .= " LIMIT $limit";
        }
        
        $result = $this->db->query($sql);
        
        return $this->db->resultToArray($result);
    }
    
    /**
     * 활동 로그 수 카운트
     * 
     * @param array $filters 필터 조건
     * @return int 활동 로그 수
     */
    public function countActivities($filters = []) {
        $sql = "SELECT COUNT(*) as count FROM activities a 
                JOIN users u ON a.user_id = u.id 
                WHERE 1=1";
        
        // 사용자 필터
        if (isset($filters['user_id'])) {
            $userId = $this->db->escape($filters['user_id']);
            $sql .= " AND a.user_id = '$userId'";
        }
        
        // 부서 필터
        if (isset($filters['department'])) {
            $department = $this->db->escape($filters['department']);
            $sql .= " AND u.department = '$department'";
        }
        
        // 활동 유형 필터
        if (isset($filters['activity_type'])) {
            $activityType = $this->db->escape($filters['activity_type']);
            $sql .= " AND a.activity_type = '$activityType'";
        }
        
        // 날짜 범위 필터
        if (isset($filters['start_date']) && isset($filters['end_date'])) {
            $startDate = $this->db->escape($filters['start_date']);
            $endDate = $this->db->escape($filters['end_date']);
            $sql .= " AND DATE(a.created_at) BETWEEN '$startDate' AND '$endDate'";
        } else if (isset($filters['start_date'])) {
            $startDate = $this->db->escape($filters['start_date']);
            $sql .= " AND DATE(a.created_at) >= '$startDate'";
        } else if (isset($filters['end_date'])) {
            $endDate = $this->db->escape($filters['end_date']);
            $sql .= " AND DATE(a.created_at) <= '$endDate'";
        }
        
        // 관련 항목 필터
        if (isset($filters['related_type'])) {
            $relatedType = $this->db->escape($filters['related_type']);
            $sql .= " AND a.related_type = '$relatedType'";
        }
        
        if (isset($filters['related_id'])) {
            $relatedId = $this->db->escape($filters['related_id']);
            $sql .= " AND a.related_id = '$relatedId'";
        }
        
        // 키워드 검색
        if (isset($filters['keyword'])) {
            $keyword = $this->db->escape($filters['keyword']);
            $sql .= " AND (a.description LIKE '%$keyword%')";
        }
        
        $result = $this->db->query($sql);
        $row = $result->fetch_assoc();
        
        return (int)$row['count'];
    }
    
    /**
     * 특정 기간 동안의 활동 통계 조회
     * 
     * @param string $period 기간 (day, week, month)
     * @param array $filters 필터 조건
     * @return array 활동 통계
     */
    public function getActivityStats($period = 'day', $filters = []) {
        $dateFormat = '%Y-%m-%d';
        $groupBy = 'DATE(a.created_at)';
        $dateInterval = '1 DAY';
        
        if ($period === 'week') {
            $dateFormat = '%Y-%u';
            $groupBy = 'YEARWEEK(a.created_at, 1)';
            $dateInterval = '1 WEEK';
        } else if ($period === 'month') {
            $dateFormat = '%Y-%m';
            $groupBy = 'DATE_FORMAT(a.created_at, "%Y-%m")';
            $dateInterval = '1 MONTH';
        }
        
        $sql = "SELECT 
                DATE_FORMAT(a.created_at, '$dateFormat') as period,
                COUNT(*) as total_activities,
                COUNT(DISTINCT a.user_id) as active_users,
                SUM(CASE WHEN a.activity_type = 'login' THEN 1 ELSE 0 END) as login_count,
                SUM(CASE WHEN a.activity_type = 'create_assessment' THEN 1 ELSE 0 END) as create_assessment_count,
                SUM(CASE WHEN a.activity_type = 'update_assessment' THEN 1 ELSE 0 END) as update_assessment_count,
                SUM(CASE WHEN a.activity_type = 'complete_assessment' THEN 1 ELSE 0 END) as complete_assessment_count,
                SUM(CASE WHEN a.activity_type = 'create_report' THEN 1 ELSE 0 END) as create_report_count,
                SUM(CASE WHEN a.activity_type = 'update_report' THEN 1 ELSE 0 END) as update_report_count,
                SUM(CASE WHEN a.activity_type = 'identify_risk' THEN 1 ELSE 0 END) as identify_risk_count,
                SUM(CASE WHEN a.activity_type = 'update_risk' THEN 1 ELSE 0 END) as update_risk_count,
                SUM(CASE WHEN a.activity_type = 'resolve_risk' THEN 1 ELSE 0 END) as resolve_risk_count,
                SUM(CASE WHEN a.activity_type = 'ai_analysis' THEN 1 ELSE 0 END) as ai_analysis_count
                FROM activities a
                JOIN users u ON a.user_id = u.id
                WHERE 1=1";
        
        // 사용자 필터
        if (isset($filters['user_id'])) {
            $userId = $this->db->escape($filters['user_id']);
            $sql .= " AND a.user_id = '$userId'";
        }
        
        // 부서 필터
        if (isset($filters['department'])) {
            $department = $this->db->escape($filters['department']);
            $sql .= " AND u.department = '$department'";
        }
        
        // 날짜 범위 필터
        if (isset($filters['start_date']) && isset($filters['end_date'])) {
            $startDate = $this->db->escape($filters['start_date']);
            $endDate = $this->db->escape($filters['end_date']);
            $sql .= " AND DATE(a.created_at) BETWEEN '$startDate' AND '$endDate'";
        } else if (isset($filters['start_date'])) {
            $startDate = $this->db->escape($filters['start_date']);
            $sql .= " AND DATE(a.created_at) >= '$startDate'";
        } else if (isset($filters['end_date'])) {
            $endDate = $this->db->escape($filters['end_date']);
            $sql .= " AND DATE(a.created_at) <= '$endDate'";
        } else {
            // 기본값: 최근 30일
            $sql .= " AND a.created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
        }
        
        $sql .= " GROUP BY $groupBy ORDER BY period ASC";
        
        $result = $this->db->query($sql);
        
        return $this->db->resultToArray($result);
    }
    
    /**
     * 사용자별 활동 통계 조회
     * 
     * @param array $filters 필터 조건
     * @param int $limit 조회할 최대 레코드 수
     * @return array 사용자별 활동 통계
     */
    public function getUserActivityStats($filters = [], $limit = 0) {
        $sql = "SELECT 
                a.user_id,
                u.username,
                u.full_name,
                u.department,
                u.position,
                COUNT(*) as total_activities,
                COUNT(DISTINCT DATE(a.created_at)) as active_days,
                MAX(a.created_at) as last_activity,
                SUM(CASE WHEN a.activity_type = 'login' THEN 1 ELSE 0 END) as login_count,
                SUM(CASE WHEN a.activity_type = 'create_assessment' THEN 1 ELSE 0 END) as create_assessment_count,
                SUM(CASE WHEN a.activity_type = 'update_assessment' THEN 1 ELSE 0 END) as update_assessment_count,
                SUM(CASE WHEN a.activity_type = 'complete_assessment' THEN 1 ELSE 0 END) as complete_assessment_count,
                SUM(CASE WHEN a.activity_type = 'create_report' THEN 1 ELSE 0 END) as create_report_count,
                SUM(CASE WHEN a.activity_type = 'update_report' THEN 1 ELSE 0 END) as update_report_count,
                SUM(CASE WHEN a.activity_type = 'identify_risk' THEN 1 ELSE 0 END) as identify_risk_count,
                SUM(CASE WHEN a.activity_type = 'update_risk' THEN 1 ELSE 0 END) as update_risk_count,
                SUM(CASE WHEN a.activity_type = 'resolve_risk' THEN 1 ELSE 0 END) as resolve_risk_count,
                SUM(CASE WHEN a.activity_type = 'ai_analysis' THEN 1 ELSE 0 END) as ai_analysis_count
                FROM activities a
                JOIN users u ON a.user_id = u.id
                WHERE 1=1";
        
        // 부서 필터
        if (isset($filters['department'])) {
            $department = $this->db->escape($filters['department']);
            $sql .= " AND u.department = '$department'";
        }
        
        // 날짜 범위 필터
        if (isset($filters['start_date']) && isset($filters['end_date'])) {
            $startDate = $this->db->escape($filters['start_date']);
            $endDate = $this->db->escape($filters['end_date']);
            $sql .= " AND DATE(a.created_at) BETWEEN '$startDate' AND '$endDate'";
        } else if (isset($filters['start_date'])) {
            $startDate = $this->db->escape($filters['start_date']);
            $sql .= " AND DATE(a.created_at) >= '$startDate'";
        } else if (isset($filters['end_date'])) {
            $endDate = $this->db->escape($filters['end_date']);
            $sql .= " AND DATE(a.created_at) <= '$endDate'";
        } else {
            // 기본값: 최근 30일
            $sql .= " AND a.created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
        }
        
        $sql .= " GROUP BY a.user_id ORDER BY total_activities DESC";
        
        if ($limit > 0) {
            $limit = (int)$limit;
            $sql .= " LIMIT $limit";
        }
        
        $result = $this->db->query($sql);
        
        return $this->db->resultToArray($result);
    }
    
    /**
     * 활동 유형별 통계 조회
     * 
     * @param array $filters 필터 조건
     * @return array 활동 유형별 통계
     */
    public function getActivityTypeStats($filters = []) {
        $sql = "SELECT 
                a.activity_type,
                COUNT(*) as count,
                COUNT(DISTINCT a.user_id) as unique_users,
                COUNT(DISTINCT DATE(a.created_at)) as active_days,
                MIN(a.created_at) as first_activity,
                MAX(a.created_at) as last_activity
                FROM activities a
                JOIN users u ON a.user_id = u.id
                WHERE 1=1";
        
        // 사용자 필터
        if (isset($filters['user_id'])) {
            $userId = $this->db->escape($filters['user_id']);
            $sql .= " AND a.user_id = '$userId'";
        }
        
        // 부서 필터
        if (isset($filters['department'])) {
            $department = $this->db->escape($filters['department']);
            $sql .= " AND u.department = '$department'";
        }
        
        // 날짜 범위 필터
        if (isset($filters['start_date']) && isset($filters['end_date'])) {
            $startDate = $this->db->escape($filters['start_date']);
            $endDate = $this->db->escape($filters['end_date']);
            $sql .= " AND DATE(a.created_at) BETWEEN '$startDate' AND '$endDate'";
        } else if (isset($filters['start_date'])) {
            $startDate = $this->db->escape($filters['start_date']);
            $sql .= " AND DATE(a.created_at) >= '$startDate'";
        } else if (isset($filters['end_date'])) {
            $endDate = $this->db->escape($filters['end_date']);
            $sql .= " AND DATE(a.created_at) <= '$endDate'";
        } else {
            // 기본값: 최근 30일
            $sql .= " AND a.created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
        }
        
        $sql .= " GROUP BY a.activity_type ORDER BY count DESC";
        
        $result = $this->db->query($sql);
        
        return $this->db->resultToArray($result);
    }
    
    /**
     * 오늘의 활동 요약 조회
     * 
     * @return array 오늘의 활동 요약
     */
    public function getTodayActivitySummary() {
        $today = date('Y-m-d');
        
        $sql = "SELECT 
                COUNT(*) as total_activities,
                COUNT(DISTINCT a.user_id) as active_users,
                SUM(CASE WHEN a.activity_type = 'login' THEN 1 ELSE 0 END) as login_count,
                SUM(CASE WHEN a.activity_type = 'create_assessment' THEN 1 ELSE 0 END) as create_assessment_count,
                SUM(CASE WHEN a.activity_type = 'update_assessment' THEN 1 ELSE 0 END) as update_assessment_count,
                SUM(CASE WHEN a.activity_type = 'complete_assessment' THEN 1 ELSE 0 END) as complete_assessment_count,
                SUM(CASE WHEN a.activity_type = 'create_report' THEN 1 ELSE 0 END) as create_report_count,
                SUM(CASE WHEN a.activity_type = 'download_report' THEN 1 ELSE 0 END) as download_report_count,
                SUM(CASE WHEN a.activity_type = 'identify_risk' THEN 1 ELSE 0 END) as identify_risk_count,
                SUM(CASE WHEN a.activity_type = 'resolve_risk' THEN 1 ELSE 0 END) as resolve_risk_count,
                SUM(CASE WHEN a.activity_type = 'ai_analysis' THEN 1 ELSE 0 END) as ai_analysis_count
                FROM activities a
                WHERE DATE(a.created_at) = '$today'";
        
        $result = $this->db->query($sql);
        
        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        
        return [
            'total_activities' => 0,
            'active_users' => 0,
            'login_count' => 0,
            'create_assessment_count' => 0,
            'update_assessment_count' => 0,
            'complete_assessment_count' => 0,
            'create_report_count' => 0,
            'download_report_count' => 0,
            'identify_risk_count' => 0,
            'resolve_risk_count' => 0,
            'ai_analysis_count' => 0
        ];
    }
    
    /**
     * 최근 활동 조회
     * 
     * @param int $limit 조회할 최대 레코드 수
     * @return array 최근 활동 목록
     */
    public function getRecentActivities($limit = 10) {
        $sql = "SELECT a.*, u.username, u.full_name, u.department
                FROM activities a
                JOIN users u ON a.user_id = u.id
                ORDER BY a.created_at DESC
                LIMIT " . (int)$limit;
        
        $result = $this->db->query($sql);
        
        return $this->db->resultToArray($result);
    }
    
    /**
     * 최근 로그인 활동 조회
     * 
     * @param int $limit 조회할 최대 레코드 수
     * @return array 최근 로그인 활동 목록
     */
    public function getRecentLogins($limit = 10) {
        $sql = "SELECT a.*, u.username, u.full_name, u.email, u.department
                FROM activities a
                JOIN users u ON a.user_id = u.id
                WHERE a.activity_type = 'login'
                ORDER BY a.created_at DESC
                LIMIT " . (int)$limit;
        
        $result = $this->db->query($sql);
        
        return $this->db->resultToArray($result);
    }
    
    /**
     * 활동 로그 삭제 (관리자용)
     * 
     * @param int $id 활동 로그 ID
     * @return bool 성공 여부
     */
    public function deleteActivity($id) {
        return $this->db->deleteRecord('activities', $id);
    }
    
    /**
     * 기간별 활동 로그 정리 (관리자용)
     * 
     * @param int $days 보관 기간 (일)
     * @return bool 성공 여부
     */
    public function purgeOldActivities($days = 90) {
        $days = (int)$days;
        $cutoffDate = date('Y-m-d', strtotime("-$days days"));
        
        $sql = "DELETE FROM activities WHERE DATE(created_at) < '$cutoffDate'";
        
        return $this->db->query($sql);
    }
}
?> 