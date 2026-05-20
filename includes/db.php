<?php
// ─── db.php — Database Connection ────────────────────────────
// Update credentials to match your MySQL setup
 
define('DB_HOST', 'localhost');
define('DB_USER', 'root');       // ← change to your MySQL username
define('DB_PASS', 'Password123');           // ← change to your MySQL password
define('DB_NAME', 'acadex');
 
function get_db(): mysqli {
    static $conn = null;
    if ($conn === null) {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ($conn->connect_error) {
            die(json_encode(['error' => 'DB connection failed: ' . $conn->connect_error]));
        }
        $conn->set_charset('utf8mb4');
    }
    return $conn;
}
 
// Helper: run a query and return all rows as assoc array
function db_query(string $sql, array $params = [], string $types = ''): array {
    $db   = get_db();
    $stmt = $db->prepare($sql);
    if (!$stmt) return [];
    if ($params) $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}
 
// Helper: run INSERT/UPDATE/DELETE, return affected rows
function db_execute(string $sql, array $params = [], string $types = ''): int {
    $db   = get_db();
    $stmt = $db->prepare($sql);
    if (!$stmt) return 0;
    if ($params) $stmt->bind_param($types, ...$params);
    $stmt->execute();
    return $stmt->affected_rows;
}
 
// Helper: get single row
function db_row(string $sql, array $params = [], string $types = ''): ?array {
    $rows = db_query($sql, $params, $types);
    return $rows[0] ?? null;
}
?>
 