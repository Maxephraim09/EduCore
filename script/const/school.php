<?php
try
{
$conn = new PDO('mysql:host='.DBHost.';dbname='.DBName.';charset='.DBCharset.';collation='.DBCollation.';prefix='.DBPrefix.'', DBUser, DBPass);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$stmt = $conn->prepare("SELECT * FROM tbl_school LIMIT 1");
$stmt->execute();
$result = $stmt->fetchAll();
foreach($result as $row)
{
DEFINE('WBName', $row[1]);
DEFINE('WBLogo', $row[2]);
DEFINE('WBResSys', $row[3]);
DEFINE('WBResAvi', $row[4]);
}

}catch(PDOException $e)
{
}

/**
 * Get current academic session
 * Returns the active session or "No active session" if none is active
 */
function current_session() {
    global $conn;
    try {
        $stmt = $conn->query("SELECT session_name FROM tbl_sessions WHERE is_active = 1 LIMIT 1");
        $session = $stmt->fetchColumn();
        return $session ?: 'No active session';
    } catch (PDOException $e) {
        return 'Error fetching session';
    }
}
?>
