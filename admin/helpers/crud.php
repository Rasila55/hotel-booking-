<?php
require_once __DIR__ . '/../../includes/db.php';


/**
 * CRUD Helper Functions
 * Simple and secure database operations using prepared statements
 */

// ==================== CREATE ====================

/**
 * Insert a new record into database
 * @param string $table - Table name
 * @param array $data - Associative array of column => value
 * @return int|false - Returns inserted ID on success, false on failure
 */
function create($table, $data) {
    $conn = getDBConnection();
    
    $columns = implode(', ', array_keys($data));
    $placeholders = implode(', ', array_fill(0, count($data), '?'));
    
    $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        return false;
    }
    
    $types = '';
    $values = [];
    
    foreach ($data as $value) {
        if (is_int($value)) {
            $types .= 'i';
        } elseif (is_float($value)) {
            $types .= 'd';
        } else {
            $types .= 's';
        }
        $values[] = $value;
    }
    
    $stmt->bind_param($types, ...$values);
    $result = $stmt->execute();
    
    if ($result) {
        $insertId = $conn->insert_id;
        $stmt->close();
        return $insertId;
    }
    
    $stmt->close();
    return false;
}

// ==================== READ ====================

/**
 * Get all records from a table
 * @param string $table - Table name
 * @param array $conditions - WHERE conditions (optional) ['column' => 'value']
 * @param string $orderBy - ORDER BY clause (optional) 'column DESC'
 * @param int $limit - LIMIT clause (optional)
 * @return array - Array of records
 */
function readAll($table, $conditions = [], $orderBy = '', $limit = null) {
    require_once __DIR__ . '/../../includes/db.php';
    $conn = getDBConnection();

    
    $sql = "SELECT * FROM $table";
    $types = '';
    $values = [];
    
    // Add WHERE conditions
    if (!empty($conditions)) {
        $whereClauses = [];
        foreach ($conditions as $column => $value) {
            $whereClauses[] = "$column = ?";
            if (is_int($value)) {
                $types .= 'i';
            } elseif (is_float($value)) {
                $types .= 'd';
            } else {
                $types .= 's';
            }
            $values[] = $value;
        }
        $sql .= " WHERE " . implode(' AND ', $whereClauses);
    }
    
    // Add ORDER BY
    if ($orderBy) {
        $sql .= " ORDER BY $orderBy";
    }
    
    // Add LIMIT
    if ($limit) {
        $sql .= " LIMIT $limit";
    }
    
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        return [];
    }
    
    if (!empty($values)) {
        $stmt->bind_param($types, ...$values);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    $records = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    
    return $records;
}

/**
 * Get a single record by ID or conditions
 * @param string $table - Table name
 * @param mixed $idOrConditions - ID value or array of conditions
 * @param string $idColumn - ID column name (default: 'id')
 * @return array|null - Single record or null
 */
function readOne($table, $idOrConditions, $idColumn = 'id') {
    $conn = getDBConnection();
    
    if (is_array($idOrConditions)) {
        // Multiple conditions
        $conditions = $idOrConditions;
        $whereClauses = [];
        $types = '';
        $values = [];
        
        foreach ($conditions as $column => $value) {
            $whereClauses[] = "$column = ?";
            if (is_int($value)) {
                $types .= 'i';
            } elseif (is_float($value)) {
                $types .= 'd';
            } else {
                $types .= 's';
            }
            $values[] = $value;
        }
        
        $sql = "SELECT * FROM $table WHERE " . implode(' AND ', $whereClauses) . " LIMIT 1";
    } else {
        // Single ID
        $sql = "SELECT * FROM $table WHERE $idColumn = ? LIMIT 1";
        $types = is_int($idOrConditions) ? 'i' : 's';
        $values = [$idOrConditions];
    }
    
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        return null;
    }
    
    $stmt->bind_param($types, ...$values);
    $stmt->execute();
    $result = $stmt->get_result();
    $record = $result->fetch_assoc();
    $stmt->close();
    
    return $record;
}

/**
 * Execute custom SELECT query
 * @param string $query - SQL query with placeholders
 * @param array $params - Parameters to bind
 * @param string $types - Parameter types (i, d, s, b)
 * @return array - Array of records
 */
function query($query, $params = [], $types = '') {
    $conn = getDBConnection();
    
    $stmt = $conn->prepare($query);
    
    if (!$stmt) {
        return [];
    }
    
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    $records = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    
    return $records;
}

// ==================== UPDATE ====================

/**
 * Update record(s) in database
 * @param string $table - Table name
 * @param array $data - Data to update ['column' => 'value']
 * @param array $conditions - WHERE conditions ['column' => 'value']
 * @return bool - Success status
 */
function update($table, $data, $conditions) {
    $conn = getDBConnection();
    
    if (empty($data) || empty($conditions)) {
        return false;
    }
    
    // Build SET clause
    $setClauses = [];
    foreach (array_keys($data) as $column) {
        $setClauses[] = "$column = ?";
    }
    
    // Build WHERE clause
    $whereClauses = [];
    foreach (array_keys($conditions) as $column) {
        $whereClauses[] = "$column = ?";
    }
    
    $sql = "UPDATE $table SET " . implode(', ', $setClauses) . 
           " WHERE " . implode(' AND ', $whereClauses);
    
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        return false;
    }
    
    // Prepare types and values
    $types = '';
    $values = [];
    
    // Add data values
    foreach ($data as $value) {
        if (is_int($value)) {
            $types .= 'i';
        } elseif (is_float($value)) {
            $types .= 'd';
        } else {
            $types .= 's';
        }
        $values[] = $value;
    }
    
    // Add condition values
    foreach ($conditions as $value) {
        if (is_int($value)) {
            $types .= 'i';
        } elseif (is_float($value)) {
            $types .= 'd';
        } else {
            $types .= 's';
        }
        $values[] = $value;
    }
    
    $stmt->bind_param($types, ...$values);
    $result = $stmt->execute();
    $stmt->close();
    
    return $result;
}

/**
 * Update record by ID
 * @param string $table - Table name
 * @param int|string $id - ID value
 * @param array $data - Data to update
 * @param string $idColumn - ID column name (default: 'id')
 * @return bool - Success status
 */
function updateById($table, $id, $data, $idColumn = 'id') {
    return update($table, $data, [$idColumn => $id]);
}

// ==================== DELETE ====================

/**
 * Delete record(s) from database
 * @param string $table - Table name
 * @param array $conditions - WHERE conditions ['column' => 'value']
 * @return bool - Success status
 */
function delete($table, $conditions) {
    $conn = getDBConnection();
    
    if (empty($conditions)) {
        return false;
    }
    
    // Build WHERE clause
    $whereClauses = [];
    $types = '';
    $values = [];
    
    foreach ($conditions as $column => $value) {
        $whereClauses[] = "$column = ?";
        if (is_int($value)) {
            $types .= 'i';
        } elseif (is_float($value)) {
            $types .= 'd';
        } else {
            $types .= 's';
        }
        $values[] = $value;
    }
    
    $sql = "DELETE FROM $table WHERE " . implode(' AND ', $whereClauses);
    
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        return false;
    }
    
    $stmt->bind_param($types, ...$values);
    $result = $stmt->execute();
    $stmt->close();
    
    return $result;
}

/**
 * Delete record by ID
 * @param string $table - Table name
 * @param int|string $id - ID value
 * @param string $idColumn - ID column name (default: 'id')
 * @return bool - Success status
 */
function deleteById($table, $id, $idColumn = 'id') {
    return delete($table, [$idColumn => $id]);
}

// ==================== UTILITY FUNCTIONS ====================

/**
 * Count records in table
 * @param string $table - Table name
 * @param array $conditions - WHERE conditions (optional)
 * @return int - Count of records
 */
function countRecords($table, $conditions = []) {
    $conn = getDBConnection();
    
    $sql = "SELECT COUNT(*) as total FROM $table";
    $types = '';
    $values = [];
    
    if (!empty($conditions)) {
        $whereClauses = [];
        foreach ($conditions as $column => $value) {
            $whereClauses[] = "$column = ?";
            if (is_int($value)) {
                $types .= 'i';
            } elseif (is_float($value)) {
                $types .= 'd';
            } else {
                $types .= 's';
            }
            $values[] = $value;
        }
        $sql .= " WHERE " . implode(' AND ', $whereClauses);
    }
    
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        return 0;
    }
    
    if (!empty($values)) {
        $stmt->bind_param($types, ...$values);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    
    return (int)$row['total'];
}

/**
 * Check if record exists
 * @param string $table - Table name
 * @param array $conditions - WHERE conditions
 * @return bool - True if exists
 */
function exists($table, $conditions) {
    return countRecords($table, $conditions) > 0;
}

/**
 * Execute custom query (INSERT, UPDATE, DELETE)
 * @param string $query - SQL query
 * @param array $params - Parameters to bind
 * @param string $types - Parameter types
 * @return bool - Success status
 */
function execute($query, $params = [], $types = '') {
    $conn = getDBConnection();
    
    $stmt = $conn->prepare($query);
    
    if (!$stmt) {
        return false;
    }
    
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    
    $result = $stmt->execute();
    $stmt->close();
    
    return $result;
}

/**
 * Get last inserted ID
 * @return int - Last insert ID
 */
function lastInsertId() {
    $conn = getDBConnection();
    return $conn->insert_id;
}

/**
 * Begin transaction
 */
function beginTransaction() {
    $conn = getDBConnection();
    $conn->begin_transaction();
}

/**
 * Commit transaction
 */
function commitTransaction() {
    $conn = getDBConnection();
    $conn->commit();
}

/**
 * Rollback transaction
 */
function rollbackTransaction() {
    $conn = getDBConnection();
    $conn->rollback();
}
?>