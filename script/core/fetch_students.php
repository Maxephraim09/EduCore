<?php
session_start();
require_once('../db/config.php');





header('Content-Type: application/json');

try {
    $conn = new PDO("mysql:host=".DBHost.";dbname=".DBName.";charset=".DBCharset, DBUser, DBPass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $class_id = $_POST['class_id'] ?? null;
    $session_id = $_POST['session_id'] ?? null;

    if(!$class_id || !$session_id){
        echo json_encode([]);
        exit;
    }

    $stmt = $conn->prepare("
        SELECT id, CONCAT(fname,' ',mname,' ',lname) AS name, class, session_id, gender, email, status
        FROM tbl_students
        WHERE class=:class AND session_id=:session AND status=1
        ORDER BY lname ASC
    ");

    $stmt->execute([
        ':class' => $class_id,
        ':session' => $session_id
    ]);

    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($students);

} catch(PDOException $e){
    echo json_encode(['error' => $e->getMessage()]);
}
?>