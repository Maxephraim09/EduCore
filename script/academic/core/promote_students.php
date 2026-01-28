<?php
chdir('../');
session_start();
require_once('../db/config.php');

header('Content-Type: application/json');

// Enable full error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

try {
    $conn = new PDO("mysql:host=".DBHost.";dbname=".DBName.";charset=".DBCharset, DBUser, DBPass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (!isset($_POST['class_id'], $_POST['session_id'], $_POST['next_session_id'])) {
        echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
        exit;
    }

    $classId        = intval($_POST['class_id']);
    $currentSession = intval($_POST['session_id']);
    $nextSession    = intval($_POST['next_session_id']);

    // Fetch students
    $stmt = $conn->prepare("SELECT id, CONCAT(fname,' ',mname,' ',lname) AS fullname FROM tbl_students WHERE class = ? AND session_id = ?");
    $stmt->execute([$classId, $currentSession]);
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$students) {
        echo json_encode(['success'=>false, 'message'=>'No students found in this class/session']);
        exit;
    }

    // Fetch promotion mapping
    $map_stmt = $conn->prepare("SELECT to_class_id FROM tbl_class_promotion WHERE from_class_id = ?");
    $map_stmt->execute([$classId]);
    $map = $map_stmt->fetch(PDO::FETCH_ASSOC);

    if (!$map) {
        echo json_encode(['success'=>false, 'message'=>'No promotion mapping defined for this class']);
        exit;
    }

    $toClassId = $map['to_class_id'];

    $update_stmt = $conn->prepare("UPDATE tbl_students SET class = ?, session_id = ? WHERE id = ?");
    $insert_stmt = $conn->prepare("INSERT INTO tbl_promotions 
        (student_id, student_name, from_class_id, to_class_id, from_session_id, to_session_id, promoted_at)
        VALUES (?, ?, ?, ?, ?, ?, NOW())");

    $promoted = [];

    foreach($students as $s) {
        $studentId = intval($s['id']);  // explicitly cast to integer
        $fullname  = $s['fullname'];

        // Update student
        $update_stmt->execute([$toClassId, $nextSession, $studentId]);

        // Insert promotion
        $inserted = $insert_stmt->execute([
            $studentId,
            $fullname,
            $classId,
            $toClassId,
            $currentSession,
            $nextSession
        ]);

        if (!$inserted) {
            throw new Exception("Failed to insert promotion for student ID {$studentId}");
        }

        $promoted[] = [
            'name' => $fullname,
            'from_class' => getClassName($classId, $conn),
            'to_class' => getClassName($toClassId, $conn),
            'from_session' => getSessionName($currentSession, $conn),
            'to_session' => getSessionName($nextSession, $conn)
        ];
    }

    echo json_encode([
        'success' => true,
        'message' => 'Promotion completed successfully!',
        'students' => $promoted
    ]);

} catch (Exception $e) {
    echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
}

function getClassName($id, $conn){
    $stmt = $conn->prepare("SELECT name FROM tbl_classes WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetchColumn() ?: 'Unknown';
}

function getSessionName($id, $conn){
    $stmt = $conn->prepare("SELECT session_name FROM tbl_sessions WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetchColumn() ?: 'Unknown';
}
