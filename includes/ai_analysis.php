<?php
/**
 * AI 분석 클래스
 * 
 * 이 클래스는 위험성 평가 시스템의 AI 분석 기능을 제공합니다.
 * 위험 예측, 패턴 인식, 자동화된 분석, 추천 등의 기능을 포함합니다.
 * 
 * @version 1.0
 */

class AIAnalysis {
    private $mysqli;
    
    /**
     * 생성자
     */
    public function __construct() {
        global $mysqli;
        $this->mysqli = $mysqli;
    }
    
    /**
     * 평가 데이터 기반 위험 수준 예측
     * 
     * @param array $assessmentData 평가 데이터
     * @return array 예측된 위험 수준 및 확률
     */
    public function predictRiskLevel($assessmentData) {
        // 실제 구현에서는 ML 알고리즘이나 API 호출이 이루어질 것입니다.
        // 여기서는 시뮬레이션된 결과를 반환합니다.
        
        $hazardType = isset($assessmentData['hazard_type']) ? escapeInput($assessmentData['hazard_type']) : '';
        $projectType = isset($assessmentData['project_type']) ? escapeInput($assessmentData['project_type']) : '';
        
        // 로그 기록
        if(isset($_SESSION['user_id'])) {
            logActivity(
                $_SESSION['user_id'],
                'ai_analysis',
                '위험 수준 예측 분석이 수행되었습니다.',
                null,
                'risk_prediction'
            );
        }
        
        // 유사한 위험 유형의 과거 데이터 조회
        $sql = "SELECT risk_level, COUNT(*) as count 
                FROM risk_items 
                WHERE hazard_type LIKE '%$hazardType%' 
                GROUP BY risk_level 
                ORDER BY count DESC";
        
        $result = $this->mysqli->query($sql);
        
        $predictions = [
            'very_high' => 0,
            'high' => 0,
            'medium' => 0,
            'low' => 0,
            'very_low' => 0
        ];
        
        $totalCount = 0;
        
        if($result && $result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $predictions[$row['risk_level']] = $row['count'];
                $totalCount += $row['count'];
            }
        }
        
        // 확률 계산
        foreach($predictions as $level => $count) {
            $predictions[$level] = $totalCount > 0 ? round(($count / $totalCount) * 100) : 0;
        }
        
        // 가장 높은 확률의 위험 수준 결정
        arsort($predictions);
        $predictedLevel = key($predictions);
        
        return [
            'predicted_level' => $predictedLevel,
            'probabilities' => $predictions,
            'confidence' => $predictions[$predictedLevel],
            'similar_cases_count' => $totalCount
        ];
    }
    
    /**
     * 특정 평가의 위험 요소 자동 식별
     * 
     * @param int $assessmentId 평가 ID
     * @return array 식별된 위험 요소 목록
     */
    public function identifyHazards($assessmentId) {
        $assessmentId = escapeInput($assessmentId);
        
        // 평가 정보 조회
        $sql = "SELECT * FROM assessments WHERE id = '$assessmentId'";
        $result = $this->mysqli->query($sql);
        
        if(!$result || $result->num_rows == 0) {
            return [
                'status' => 'error',
                'message' => '유효하지 않은 평가 ID입니다.'
            ];
        }
        
        $assessment = $result->fetch_assoc();
        
        // 로그 기록
        if(isset($_SESSION['user_id'])) {
            logActivity(
                $_SESSION['user_id'],
                'ai_analysis',
                "평가 '{$assessment['title']}'에 대한 위험 요소 자동 식별이 수행되었습니다.",
                $assessmentId,
                'assessment'
            );
        }
        
        // 유사한 프로젝트 타입의 위험 요소 조회
        $sql = "SELECT ri.* FROM risk_items ri 
                JOIN assessments a ON ri.assessment_id = a.id 
                WHERE a.project LIKE '%{$assessment['project']}%' 
                AND a.id != '$assessmentId'
                ORDER BY ri.risk_level DESC
                LIMIT 10";
        
        $result = $this->mysqli->query($sql);
        
        $identifiedHazards = [];
        
        if($result && $result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $identifiedHazards[] = [
                    'hazard_type' => $row['hazard_type'],
                    'hazard_name' => $row['hazard_name'],
                    'hazard_situation' => $row['hazard_situation'],
                    'suggested_likelihood' => $row['likelihood'],
                    'suggested_severity' => $row['severity'],
                    'suggested_risk_level' => $row['risk_level'],
                    'suggested_control_measures' => $row['control_measures'],
                    'confidence' => $this->calculateSimilarityScore($assessment, $row) * 100
                ];
            }
        }
        
        return [
            'status' => 'success',
            'identified_count' => count($identifiedHazards),
            'hazards' => $identifiedHazards
        ];
    }
    
    /**
     * 위험 패턴 분석
     * 
     * @param array $filters 필터 조건
     * @return array 식별된 패턴 목록
     */
    public function analyzeRiskPatterns($filters = []) {
        $whereClause = "1=1";
        
        if(!empty($filters)) {
            if(isset($filters['project_type'])) {
                $projectType = escapeInput($filters['project_type']);
                $whereClause .= " AND a.project LIKE '%$projectType%'";
            }
            
            if(isset($filters['hazard_type'])) {
                $hazardType = escapeInput($filters['hazard_type']);
                $whereClause .= " AND ri.hazard_type LIKE '%$hazardType%'";
            }
            
            if(isset($filters['period'])) {
                $period = escapeInput($filters['period']);
                $date = date('Y-m-d', strtotime("-$period days"));
                $whereClause .= " AND a.created_at >= '$date'";
            }
        }
        
        // 로그 기록
        if(isset($_SESSION['user_id'])) {
            logActivity(
                $_SESSION['user_id'],
                'ai_analysis',
                '위험 패턴 분석이 수행되었습니다.',
                null,
                'pattern_analysis'
            );
        }
        
        // 위험 패턴 분석 쿼리
        $sql = "SELECT ri.hazard_type, ri.risk_level, COUNT(*) as occurrence_count,
                AVG(ri.likelihood) as avg_likelihood, AVG(ri.severity) as avg_severity
                FROM risk_items ri
                JOIN assessments a ON ri.assessment_id = a.id
                WHERE $whereClause
                GROUP BY ri.hazard_type, ri.risk_level
                HAVING occurrence_count > 1
                ORDER BY occurrence_count DESC, avg_severity DESC";
        
        $result = $this->mysqli->query($sql);
        
        $patterns = [];
        
        if($result && $result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $patterns[] = [
                    'hazard_type' => $row['hazard_type'],
                    'risk_level' => $row['risk_level'],
                    'occurrence_count' => $row['occurrence_count'],
                    'avg_likelihood' => round($row['avg_likelihood'], 1),
                    'avg_severity' => round($row['avg_severity'], 1),
                    'significance' => $this->calculateSignificance($row['occurrence_count'], $row['avg_severity'])
                ];
            }
        }
        
        // 위험 추세 분석
        $trendSql = "SELECT DATE_FORMAT(a.created_at, '%Y-%m') as month, 
                    COUNT(*) as assessment_count,
                    SUM(CASE WHEN ri.risk_level IN ('high', 'very_high') THEN 1 ELSE 0 END) as high_risk_count
                    FROM assessments a
                    LEFT JOIN risk_items ri ON a.id = ri.assessment_id
                    WHERE a.created_at >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
                    GROUP BY month
                    ORDER BY month ASC";
        
        $trendResult = $this->mysqli->query($trendSql);
        $trends = [];
        
        if($trendResult && $trendResult->num_rows > 0) {
            while($row = $trendResult->fetch_assoc()) {
                $trends[] = [
                    'month' => $row['month'],
                    'assessment_count' => $row['assessment_count'],
                    'high_risk_count' => $row['high_risk_count'],
                    'high_risk_percentage' => $row['assessment_count'] > 0 
                        ? round(($row['high_risk_count'] / $row['assessment_count']) * 100) 
                        : 0
                ];
            }
        }
        
        return [
            'status' => 'success',
            'patterns' => $patterns,
            'trends' => $trends
        ];
    }
    
    /**
     * 안전성 향상을 위한 맞춤형 대책 추천
     * 
     * @param int $assessmentId 평가 ID
     * @return array 추천된 안전 대책 목록
     */
    public function recommendSafetyMeasures($assessmentId) {
        $assessmentId = escapeInput($assessmentId);
        
        // 평가 정보 조회
        $sql = "SELECT * FROM assessments WHERE id = '$assessmentId'";
        $result = $this->mysqli->query($sql);
        
        if(!$result || $result->num_rows == 0) {
            return [
                'status' => 'error',
                'message' => '유효하지 않은 평가 ID입니다.'
            ];
        }
        
        $assessment = $result->fetch_assoc();
        
        // 위험 항목 조회
        $sql = "SELECT * FROM risk_items WHERE assessment_id = '$assessmentId'";
        $result = $this->mysqli->query($sql);
        
        $riskItems = [];
        if($result && $result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $riskItems[] = $row;
            }
        }
        
        // 로그 기록
        if(isset($_SESSION['user_id'])) {
            logActivity(
                $_SESSION['user_id'],
                'ai_analysis',
                "평가 '{$assessment['title']}'에 대한 안전 대책 추천이 수행되었습니다.",
                $assessmentId,
                'assessment'
            );
        }
        
        $recommendations = [];
        
        // 각 위험 항목에 대한 대책 추천
        foreach($riskItems as $item) {
            // 유사한 위험 항목에 적용된 대책 조회
            $hazardType = escapeInput($item['hazard_type']);
            $riskLevel = escapeInput($item['risk_level']);
            
            $sql = "SELECT ri.control_measures, COUNT(*) as usage_count
                    FROM risk_items ri
                    WHERE ri.hazard_type LIKE '%$hazardType%'
                    AND ri.id != '{$item['id']}'
                    AND ri.control_measures IS NOT NULL
                    AND ri.control_measures != ''
                    GROUP BY ri.control_measures
                    ORDER BY usage_count DESC, ri.created_at DESC
                    LIMIT 3";
            
            $measureResult = $this->mysqli->query($sql);
            
            $measures = [];
            if($measureResult && $measureResult->num_rows > 0) {
                while($row = $measureResult->fetch_assoc()) {
                    $measures[] = [
                        'measure' => $row['control_measures'],
                        'usage_count' => $row['usage_count'],
                        'effectiveness' => $this->estimateEffectiveness($riskLevel, $row['usage_count'])
                    ];
                }
            }
            
            $recommendations[] = [
                'risk_item_id' => $item['id'],
                'hazard_type' => $item['hazard_type'],
                'hazard_name' => $item['hazard_name'],
                'current_risk_level' => $item['risk_level'],
                'recommended_measures' => $measures,
                'priority' => $this->calculatePriority($item['risk_level'])
            ];
        }
        
        // 우선순위에 따라 정렬
        usort($recommendations, function($a, $b) {
            return $b['priority'] - $a['priority'];
        });
        
        return [
            'status' => 'success',
            'assessment_id' => $assessmentId,
            'assessment_title' => $assessment['title'],
            'recommendations_count' => count($recommendations),
            'recommendations' => $recommendations
        ];
    }
    
    /**
     * 진행 중인 모든 평가에 대한 종합 AI 분석 수행
     * 
     * @return array 종합 분석 결과
     */
    public function performComprehensiveAnalysis() {
        // 로그 기록
        if(isset($_SESSION['user_id'])) {
            logActivity(
                $_SESSION['user_id'],
                'ai_analysis',
                '종합 AI 분석이 수행되었습니다.',
                null,
                'comprehensive_analysis'
            );
        }
        
        // 진행 중인 평가 통계
        $sql = "SELECT COUNT(*) as total_assessments,
                SUM(CASE WHEN status = 'in_progress' THEN 1 ELSE 0 END) as in_progress,
                SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending
                FROM assessments";
        
        $result = $this->mysqli->query($sql);
        $assessmentStats = $result->fetch_assoc();
        
        // 위험 수준별 항목 수
        $sql = "SELECT risk_level, COUNT(*) as count
                FROM risk_items
                GROUP BY risk_level
                ORDER BY FIELD(risk_level, 'very_high', 'high', 'medium', 'low', 'very_low')";
        
        $result = $this->mysqli->query($sql);
        
        $riskLevelDistribution = [];
        $totalRiskItems = 0;
        
        if($result && $result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $riskLevelDistribution[$row['risk_level']] = $row['count'];
                $totalRiskItems += $row['count'];
            }
        }
        
        // 최근 완료된 평가의 위험 감소율
        $sql = "SELECT a.id, a.title, a.risk_level as initial_risk_level,
                COUNT(ri.id) as total_risk_items,
                SUM(CASE WHEN ri.status = 'resolved' THEN 1 ELSE 0 END) as resolved_items
                FROM assessments a
                JOIN risk_items ri ON a.id = ri.assessment_id
                WHERE a.status = 'completed'
                GROUP BY a.id
                ORDER BY a.completion_date DESC
                LIMIT 5";
        
        $result = $this->mysqli->query($sql);
        
        $recentCompletions = [];
        
        if($result && $result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $resolutionRate = $row['total_risk_items'] > 0 
                    ? round(($row['resolved_items'] / $row['total_risk_items']) * 100) 
                    : 0;
                
                $recentCompletions[] = [
                    'assessment_id' => $row['id'],
                    'title' => $row['title'],
                    'initial_risk_level' => $row['initial_risk_level'],
                    'total_risk_items' => $row['total_risk_items'],
                    'resolved_items' => $row['resolved_items'],
                    'resolution_rate' => $resolutionRate
                ];
            }
        }
        
        // 가장 빈번한 위험 유형
        $sql = "SELECT hazard_type, COUNT(*) as count
                FROM risk_items
                GROUP BY hazard_type
                ORDER BY count DESC
                LIMIT 5";
        
        $result = $this->mysqli->query($sql);
        
        $commonHazards = [];
        
        if($result && $result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $commonHazards[] = [
                    'hazard_type' => $row['hazard_type'],
                    'count' => $row['count'],
                    'percentage' => $totalRiskItems > 0 
                        ? round(($row['count'] / $totalRiskItems) * 100) 
                        : 0
                ];
            }
        }
        
        // 미해결 고위험 항목
        $sql = "SELECT ri.*, a.title as assessment_title
                FROM risk_items ri
                JOIN assessments a ON ri.assessment_id = a.id
                WHERE ri.risk_level IN ('very_high', 'high')
                AND ri.status != 'resolved'
                ORDER BY FIELD(ri.risk_level, 'very_high', 'high'), ri.created_at DESC
                LIMIT 10";
        
        $result = $this->mysqli->query($sql);
        
        $highRiskItems = [];
        
        if($result && $result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $highRiskItems[] = [
                    'id' => $row['id'],
                    'assessment_title' => $row['assessment_title'],
                    'hazard_type' => $row['hazard_type'],
                    'hazard_name' => $row['hazard_name'],
                    'risk_level' => $row['risk_level'],
                    'status' => $row['status'],
                    'priority' => $this->calculatePriority($row['risk_level'])
                ];
            }
        }
        
        return [
            'status' => 'success',
            'analysis_date' => date('Y-m-d H:i:s'),
            'assessment_stats' => $assessmentStats,
            'risk_level_distribution' => $riskLevelDistribution,
            'recent_completions' => $recentCompletions,
            'common_hazards' => $commonHazards,
            'high_risk_items' => $highRiskItems,
            'total_risk_items' => $totalRiskItems
        ];
    }
    
    /**
     * 위험 시각화 데이터 생성
     * 
     * @return array 시각화를 위한 데이터
     */
    public function generateVisualizationData() {
        // 로그 기록
        if(isset($_SESSION['user_id'])) {
            logActivity(
                $_SESSION['user_id'],
                'ai_analysis',
                '위험 시각화 데이터가 생성되었습니다.',
                null,
                'visualization'
            );
        }
        
        // 월별 위험 추세
        $sql = "SELECT DATE_FORMAT(a.created_at, '%Y-%m') as month,
                COUNT(ri.id) as total_risks,
                SUM(CASE WHEN ri.risk_level = 'very_high' THEN 1 ELSE 0 END) as very_high,
                SUM(CASE WHEN ri.risk_level = 'high' THEN 1 ELSE 0 END) as high,
                SUM(CASE WHEN ri.risk_level = 'medium' THEN 1 ELSE 0 END) as medium,
                SUM(CASE WHEN ri.risk_level = 'low' THEN 1 ELSE 0 END) as low,
                SUM(CASE WHEN ri.risk_level = 'very_low' THEN 1 ELSE 0 END) as very_low
                FROM assessments a
                JOIN risk_items ri ON a.id = ri.assessment_id
                WHERE a.created_at >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
                GROUP BY month
                ORDER BY month ASC";
        
        $result = $this->mysqli->query($sql);
        
        $monthlyTrends = [];
        
        if($result && $result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $monthlyTrends[] = [
                    'month' => $row['month'],
                    'total_risks' => $row['total_risks'],
                    'very_high' => $row['very_high'],
                    'high' => $row['high'],
                    'medium' => $row['medium'],
                    'low' => $row['low'],
                    'very_low' => $row['very_low']
                ];
            }
        }
        
        // 위험 유형별 분포
        $sql = "SELECT ri.hazard_type,
                COUNT(ri.id) as count,
                SUM(CASE WHEN ri.risk_level IN ('very_high', 'high') THEN 1 ELSE 0 END) as high_risks
                FROM risk_items ri
                GROUP BY ri.hazard_type
                ORDER BY count DESC
                LIMIT 10";
        
        $result = $this->mysqli->query($sql);
        
        $hazardDistribution = [];
        
        if($result && $result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $hazardDistribution[] = [
                    'hazard_type' => $row['hazard_type'],
                    'count' => $row['count'],
                    'high_risks' => $row['high_risks'],
                    'high_risk_percentage' => $row['count'] > 0 
                        ? round(($row['high_risks'] / $row['count']) * 100) 
                        : 0
                ];
            }
        }
        
        // 부서별 위험 평가 통계
        $sql = "SELECT u.department,
                COUNT(DISTINCT a.id) as assessments_count,
                COUNT(ri.id) as risks_count,
                SUM(CASE WHEN ri.status = 'resolved' THEN 1 ELSE 0 END) as resolved_count
                FROM users u
                JOIN assessments a ON u.id = a.created_by
                LEFT JOIN risk_items ri ON a.id = ri.assessment_id
                WHERE u.department IS NOT NULL AND u.department != ''
                GROUP BY u.department
                ORDER BY assessments_count DESC";
        
        $result = $this->mysqli->query($sql);
        
        $departmentStats = [];
        
        if($result && $result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $resolutionRate = $row['risks_count'] > 0 
                    ? round(($row['resolved_count'] / $row['risks_count']) * 100) 
                    : 0;
                
                $departmentStats[] = [
                    'department' => $row['department'],
                    'assessments_count' => $row['assessments_count'],
                    'risks_count' => $row['risks_count'],
                    'resolved_count' => $row['resolved_count'],
                    'resolution_rate' => $resolutionRate
                ];
            }
        }
        
        // 위험 심각도 vs 발생 가능성 매트릭스
        $sql = "SELECT likelihood, severity, COUNT(*) as count
                FROM risk_items
                GROUP BY likelihood, severity";
        
        $result = $this->mysqli->query($sql);
        
        $riskMatrix = [];
        
        // 초기화
        for($i = 1; $i <= 5; $i++) {
            for($j = 1; $j <= 5; $j++) {
                $riskMatrix[$i][$j] = 0;
            }
        }
        
        if($result && $result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $likelihood = $row['likelihood'];
                $severity = $row['severity'];
                $riskMatrix[$likelihood][$severity] = $row['count'];
            }
        }
        
        return [
            'status' => 'success',
            'monthly_trends' => $monthlyTrends,
            'hazard_distribution' => $hazardDistribution,
            'department_stats' => $departmentStats,
            'risk_matrix' => $riskMatrix
        ];
    }
    
    /**
     * AI 분석 결과에 대한 PDF 보고서 생성
     * 
     * @param int $analysisType 분석 유형 (1: 종합, 2: 위험 패턴, 3: 시각화)
     * @param array $options 추가 옵션
     * @return array 생성된 PDF 파일 정보
     */
    public function generateAIReport($analysisType, $options = []) {
        // 실제 구현에서는 TCPDF, FPDF 등을 사용하여 PDF 생성
        // 현재는 시뮬레이션된 결과만 반환
        
        $analysisTypeNames = [
            1 => '종합 AI 분석',
            2 => '위험 패턴 분석',
            3 => '위험 시각화 분석'
        ];
        
        $analysisName = isset($analysisTypeNames[$analysisType]) 
            ? $analysisTypeNames[$analysisType] 
            : '알 수 없는 분석';
        
        // 로그 기록
        if(isset($_SESSION['user_id'])) {
            logActivity(
                $_SESSION['user_id'],
                'ai_analysis',
                "$analysisName 보고서가 생성되었습니다.",
                null,
                'ai_report'
            );
        }
        
        $fileName = 'AI_Analysis_' . date('Ymd_His') . '.pdf';
        $filePath = '../uploads/reports/' . $fileName;
        
        return [
            'status' => 'success',
            'file_name' => $fileName,
            'file_path' => $filePath,
            'analysis_type' => $analysisName,
            'generation_date' => date('Y-m-d H:i:s'),
            'size' => '1.2 MB' // 실제 크기는 생성된 파일에 따라 달라질 것입니다.
        ];
    }
    
    /**
     * 유사성 점수 계산
     * 
     * @param array $assessment 평가 데이터
     * @param array $riskItem 위험 항목 데이터
     * @return float 유사성 점수 (0-1)
     */
    private function calculateSimilarityScore($assessment, $riskItem) {
        // 실제 구현에서는 더 복잡한 유사성 계산 알고리즘이 사용될 것입니다.
        // 현재는 간단한 시뮬레이션 값만 반환합니다.
        return mt_rand(60, 95) / 100;
    }
    
    /**
     * 중요도 계산
     * 
     * @param int $occurrenceCount 발생 횟수
     * @param float $avgSeverity 평균 심각도
     * @return float 중요도 점수 (0-1)
     */
    private function calculateSignificance($occurrenceCount, $avgSeverity) {
        // 실제 구현에서는 더 복잡한 중요도 계산 알고리즘이 사용될 것입니다.
        // 현재는 발생 횟수와 심각도를 단순 결합한 값만 반환합니다.
        $normalizedCount = min($occurrenceCount / 10, 1);
        $normalizedSeverity = min($avgSeverity / 5, 1);
        
        return ($normalizedCount * 0.4) + ($normalizedSeverity * 0.6);
    }
    
    /**
     * 효과성 추정
     * 
     * @param string $riskLevel 위험 수준
     * @param int $usageCount 사용 횟수
     * @return float 효과성 점수 (0-1)
     */
    private function estimateEffectiveness($riskLevel, $usageCount) {
        // 위험 수준에 따른 가중치
        $weights = [
            'very_high' => 0.9,
            'high' => 0.8,
            'medium' => 0.7,
            'low' => 0.6,
            'very_low' => 0.5
        ];
        
        $weight = isset($weights[$riskLevel]) ? $weights[$riskLevel] : 0.7;
        $normalizedUsage = min($usageCount / 5, 1);
        
        return $weight * $normalizedUsage;
    }
    
    /**
     * 우선순위 계산
     * 
     * @param string $riskLevel 위험 수준
     * @return int 우선순위 값 (1-5)
     */
    private function calculatePriority($riskLevel) {
        $priorities = [
            'very_high' => 5,
            'high' => 4,
            'medium' => 3,
            'low' => 2,
            'very_low' => 1
        ];
        
        return isset($priorities[$riskLevel]) ? $priorities[$riskLevel] : 3;
    }
}
?> 