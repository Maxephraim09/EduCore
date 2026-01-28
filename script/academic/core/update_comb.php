<?php
chdir('../../');
session_start();
require_once('db/config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id        = $_POST['id'];
    $class_id  = $_POST['class'];
    $subject_id = $_POST['subject'];
    $teacher_id = $_POST['teacher'];
    $reg_date  = date('Y-m-d H:i:s');

    try {
        $conn = new PDO(
            'mysql:host=' . DBHost . ';dbname=' . DBName . ';charset=' . DBCharset,
            DBUser,
            DBPass
        );
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $conn->prepare("
            UPDATE tbl_subject_combinations 
            SET class_id = ?, subject_id = ?, teacher_id = ?, reg_date = ? 
            WHERE id = ?
        ");
        $stmt->execute([$class_id, $subject_id, $teacher_id, $reg_date, $id]);

        $_SESSION['reply'] = array(array("success", "Subject combination updated successfully"));
        header("Location: ../combinations");
        exit;

    } catch (PDOException $e) {
        echo "Database connection failed: " . $e->getMessage();
    }
} else {
    header("Location: ../");
    exit;
}
?>
