<?php
/**
 * Gym System Database Helper
 * Provides safe database interaction using prepared statements.
 */

/**
 * Executes a prepared statement and returns the result.
 * 
 * @param mysqli $db The database connection.
 * @param string $sql The SQL query with ? placeholders.
 * @param string $types String containing the type of each parameter (e.g. "ssi").
 * @param array $params Array of parameters to bind.
 * @return mysqli_result|bool|int Returns mysqli_result for SELECT, true/false for others, or insert_id for INSERT.
 */
function safe_query($db, $sql, $types = "", $params = []) {
    $stmt = mysqli_prepare($db, $sql);
    if (!$stmt) {
        die("MySQL Prepare Error: " . mysqli_error($db));
    }

    if ($types !== "" && !empty($params)) {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }

    if (!mysqli_stmt_execute($stmt)) {
        die("MySQL Execute Error: " . mysqli_stmt_error($stmt));
    }

    $result = mysqli_stmt_get_result($stmt);
    
    // If it's not a SELECT query (result is false), check if it was an INSERT/UPDATE/DELETE
    if ($result === false) {
        if (strpos(strtolower(trim($sql)), 'insert') === 0) {
            $id = mysqli_stmt_insert_id($stmt);
            mysqli_stmt_close($stmt);
            return $id;
        }
        $affected = mysqli_stmt_affected_rows($stmt);
        mysqli_stmt_close($stmt);
        return $affected >= 0;
    }

    mysqli_stmt_close($stmt);
    return $result;
}

/**
 * Fetch a single row as an associative array.
 */
function safe_fetch_assoc($db, $sql, $types = "", $params = []) {
    $result = safe_query($db, $sql, $types, $params);
    if ($result instanceof mysqli_result) {
        return mysqli_fetch_assoc($result);
    }
    return null;
}

/**
 * Fetch all rows as an array of associative arrays.
 */
function safe_fetch_all($db, $sql, $types = "", $params = []) {
    $result = safe_query($db, $sql, $types, $params);
    $rows = [];
    if ($result instanceof mysqli_result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
    }
    return $rows;
}
?>
