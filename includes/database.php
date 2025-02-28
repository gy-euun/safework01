<?php
/**
 * 데이터베이스 액세스 클래스
 * 
 * 이 클래스는 데이터베이스 연결과 기본적인 CRUD 작업을 위한 메서드를 제공합니다.
 * 모든 데이터베이스 관련 작업에 활용할 수 있는 기본 클래스입니다.
 * 
 * @version 1.0
 */

class Database {
    private $mysqli;
    
    /**
     * 생성자
     */
    public function __construct() {
        global $mysqli;
        $this->mysqli = $mysqli;
    }
    
    /**
     * 쿼리 실행
     * 
     * @param string $sql SQL 쿼리
     * @return mixed 쿼리 결과 객체 또는 false
     */
    public function query($sql) {
        return $this->mysqli->query($sql);
    }
    
    /**
     * 단일 레코드 조회
     * 
     * @param string $table 테이블 이름
     * @param int|string $id 조회할 레코드의 ID
     * @param string $idField ID 필드 이름 (기본값: 'id')
     * @return array|null 조회된 레코드 또는 null
     */
    public function getRecord($table, $id, $idField = 'id') {
        $table = $this->escape($table);
        $idField = $this->escape($idField);
        $id = $this->escape($id);
        
        $sql = "SELECT * FROM $table WHERE $idField = '$id'";
        $result = $this->mysqli->query($sql);
        
        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        
        return null;
    }
    
    /**
     * 여러 레코드 조회
     * 
     * @param string $table 테이블 이름
     * @param array $conditions 조건 배열 (예: ['status' => 'active'])
     * @param string $orderBy 정렬 기준 (예: 'created_at DESC')
     * @param int $limit 조회할 최대 레코드 수
     * @param int $offset 오프셋
     * @return array 조회된 레코드 배열
     */
    public function getRecords($table, $conditions = [], $orderBy = '', $limit = 0, $offset = 0) {
        $table = $this->escape($table);
        $whereClause = $this->buildWhereClause($conditions);
        
        $sql = "SELECT * FROM $table";
        
        if (!empty($whereClause)) {
            $sql .= " WHERE $whereClause";
        }
        
        if (!empty($orderBy)) {
            $sql .= " ORDER BY " . $this->escape($orderBy);
        }
        
        if ($limit > 0) {
            $limit = (int)$limit;
            $offset = (int)$offset;
            $sql .= " LIMIT $limit OFFSET $offset";
        }
        
        $result = $this->mysqli->query($sql);
        $records = [];
        
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $records[] = $row;
            }
        }
        
        return $records;
    }
    
    /**
     * 레코드 수 카운트
     * 
     * @param string $table 테이블 이름
     * @param array $conditions 조건 배열
     * @return int 레코드 수
     */
    public function countRecords($table, $conditions = []) {
        $table = $this->escape($table);
        $whereClause = $this->buildWhereClause($conditions);
        
        $sql = "SELECT COUNT(*) as count FROM $table";
        
        if (!empty($whereClause)) {
            $sql .= " WHERE $whereClause";
        }
        
        $result = $this->mysqli->query($sql);
        
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return (int)$row['count'];
        }
        
        return 0;
    }
    
    /**
     * 레코드 생성
     * 
     * @param string $table 테이블 이름
     * @param array $data 생성할 데이터
     * @return int|bool 생성된 레코드의 ID 또는 실패 시 false
     */
    public function createRecord($table, $data) {
        $table = $this->escape($table);
        
        $columns = [];
        $values = [];
        
        foreach ($data as $column => $value) {
            $columns[] = $this->escape($column);
            
            if ($value === null) {
                $values[] = "NULL";
            } else {
                $values[] = "'" . $this->escape($value) . "'";
            }
        }
        
        $columnsStr = implode(", ", $columns);
        $valuesStr = implode(", ", $values);
        
        $sql = "INSERT INTO $table ($columnsStr) VALUES ($valuesStr)";
        
        if ($this->mysqli->query($sql)) {
            return $this->mysqli->insert_id;
        }
        
        return false;
    }
    
    /**
     * 레코드 업데이트
     * 
     * @param string $table 테이블 이름
     * @param int|string $id 업데이트할 레코드의 ID
     * @param array $data 업데이트할 데이터
     * @param string $idField ID 필드 이름 (기본값: 'id')
     * @return bool 성공 여부
     */
    public function updateRecord($table, $id, $data, $idField = 'id') {
        $table = $this->escape($table);
        $idField = $this->escape($idField);
        $id = $this->escape($id);
        
        $setParts = [];
        
        foreach ($data as $column => $value) {
            $column = $this->escape($column);
            
            if ($value === null) {
                $setParts[] = "$column = NULL";
            } else {
                $value = $this->escape($value);
                $setParts[] = "$column = '$value'";
            }
        }
        
        $setClause = implode(", ", $setParts);
        
        $sql = "UPDATE $table SET $setClause WHERE $idField = '$id'";
        
        return $this->mysqli->query($sql);
    }
    
    /**
     * 레코드 삭제
     * 
     * @param string $table 테이블 이름
     * @param int|string $id 삭제할 레코드의 ID
     * @param string $idField ID 필드 이름 (기본값: 'id')
     * @return bool 성공 여부
     */
    public function deleteRecord($table, $id, $idField = 'id') {
        $table = $this->escape($table);
        $idField = $this->escape($idField);
        $id = $this->escape($id);
        
        $sql = "DELETE FROM $table WHERE $idField = '$id'";
        
        return $this->mysqli->query($sql);
    }
    
    /**
     * 트랜잭션 시작
     * 
     * @return bool 성공 여부
     */
    public function beginTransaction() {
        return $this->mysqli->begin_transaction();
    }
    
    /**
     * 트랜잭션 커밋
     * 
     * @return bool 성공 여부
     */
    public function commitTransaction() {
        return $this->mysqli->commit();
    }
    
    /**
     * 트랜잭션 롤백
     * 
     * @return bool 성공 여부
     */
    public function rollbackTransaction() {
        return $this->mysqli->rollback();
    }
    
    /**
     * 입력값 이스케이프 처리
     * 
     * @param mixed $value 이스케이프할 값
     * @return mixed 이스케이프된 값
     */
    public function escape($value) {
        if (is_array($value)) {
            $escapedArray = [];
            foreach ($value as $key => $val) {
                $escapedArray[$key] = $this->escape($val);
            }
            return $escapedArray;
        }
        
        return $this->mysqli->real_escape_string($value);
    }
    
    /**
     * 조건 배열로부터 WHERE 절 생성
     * 
     * @param array $conditions 조건 배열
     * @return string WHERE 절 문자열
     */
    private function buildWhereClause($conditions) {
        if (empty($conditions)) {
            return '';
        }
        
        $whereParts = [];
        
        foreach ($conditions as $column => $value) {
            $column = $this->escape($column);
            
            if (is_array($value)) {
                // IN 조건
                if (isset($value['in'])) {
                    $inValues = array_map(function($val) {
                        return "'" . $this->escape($val) . "'";
                    }, $value['in']);
                    
                    $whereParts[] = "$column IN (" . implode(", ", $inValues) . ")";
                }
                // BETWEEN 조건
                else if (isset($value['between'])) {
                    $min = $this->escape($value['between'][0]);
                    $max = $this->escape($value['between'][1]);
                    $whereParts[] = "$column BETWEEN '$min' AND '$max'";
                }
                // LIKE 조건
                else if (isset($value['like'])) {
                    $likeValue = $this->escape($value['like']);
                    $whereParts[] = "$column LIKE '%$likeValue%'";
                }
                // 비교 연산자 조건
                else if (isset($value['operator']) && isset($value['value'])) {
                    $operator = $value['operator'];
                    $val = $this->escape($value['value']);
                    $whereParts[] = "$column $operator '$val'";
                }
            } else if ($value === null) {
                $whereParts[] = "$column IS NULL";
            } else {
                $value = $this->escape($value);
                $whereParts[] = "$column = '$value'";
            }
        }
        
        return implode(" AND ", $whereParts);
    }
    
    /**
     * 오류 발생 여부 확인
     * 
     * @return bool 오류 발생 여부
     */
    public function hasError() {
        return $this->mysqli->errno > 0;
    }
    
    /**
     * 오류 메시지 반환
     * 
     * @return string 오류 메시지
     */
    public function getError() {
        return $this->mysqli->error;
    }
    
    /**
     * SQL 쿼리 결과를 배열로 변환
     * 
     * @param object $result mysqli 쿼리 결과 객체
     * @return array 결과 배열
     */
    public function resultToArray($result) {
        $array = [];
        
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $array[] = $row;
            }
        }
        
        return $array;
    }
    
    /**
     * mysqli 인스턴스 반환
     * 
     * @return mysqli mysqli 인스턴스
     */
    public function getMysqli() {
        return $this->mysqli;
    }
}
?> 