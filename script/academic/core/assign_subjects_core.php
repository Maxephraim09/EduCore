<?php
chdir('../../');
session_start();
require_once('db/config.php');
require_once('const/school.php');
require_once('const/check_session.php');

if (!($res == "1" && $level == "1")) {
    header("location:../../");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $class_id = $_POST['class_id'];
    $session_id = $_POST['session_id'];
    $term_id = $_POST['term_id'];
    $subjects_data = $_POST['subjects'] ?? [];

    try {
        $conn = new PDO("mysql:host=" . DBHost . ";dbname=" . DBName . ";charset=" . DBCharset, DBUser, DBPass);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Start transaction
        $conn->beginTransaction();

        // Remove unassigned subjects first
        $student_ids = array_keys($subjects_data);
        if (!empty($student_ids)) {
            $placeholders = implode(',', array_fill(0, count($student_ids), '?'));
            $stmt = $conn->prepare("DELETE FROM tbl_student_subjects WHERE student_id IN ($placeholders) AND class_id=? AND session_id=? AND term_id=?");
            $stmt->execute(array_merge($student_ids, [$class_id, $session_id, $term_id]));
        }

        // Insert new assignments
$stmt = $conn->prepare("
    INSERT IGNORE INTO tbl_student_subjects 
    (student_id, class_id, subject_id, session_id, term_id) 
    VALUES (?,?,?,?,?)
");
       foreach ($subjects_data as $student_id => $sub_ids) {
    $sub_ids = array_unique($sub_ids); // prevent duplicates
    foreach ($sub_ids as $sub_id) {
        $stmt->execute([$student_id, $class_id, $sub_id, $session_id, $term_id]);
    }
}


        $conn->commit();

        header("Location: ../assign_subjects.php?class_id=$class_id&session_id=$session_id&term_id=$term_id&success=1");
        exit;

    } catch (PDOException $e) {
        if($conn->inTransaction()) $conn->rollBack();
        die("Error: " . $e->getMessage());
    }
} else {
    header("Location: ../assign_subjects.php");
    exit;
}
