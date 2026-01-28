<?php
require_once('../db/config.php');
session_start();

if (!isset($_SESSION['res']) || $_SESSION['level'] != 1) exit;

try {
  $conn = new PDO("mysql:host=".DBHost.";dbname=".DBName.";charset=".DBCharset, DBUser, DBPass);
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  if (!empty($_FILES['csv_file']['tmp_name'])) {
    $file = fopen($_FILES['csv_file']['tmp_name'], 'r');
    fgetcsv($file); // Skip header

    while (($row = fgetcsv($file, 1000, ',')) !== FALSE) {
      list($student_id, $ca, $exam) = $row;
      $stmt = $conn->prepare("
        INSERT INTO tbl_results (student_id, class_id, subject_id, session_id, term_id, ca_score, exam_score)
        VALUES (?, ?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE ca_score = VALUES(ca_score), exam_score = VALUES(exam_score)
      ");
      $stmt->execute([
        $student_id,
        $_POST['class_id'],
        $_POST['subject_id'],
        $_POST['session_id'],
        $_POST['term_id'],
        $ca,
        $exam
      ]);
    }
    fclose($file);
  }

  echo "<script>alert('CSV Imported Successfully'); window.location='../academic/enter_results.php?class_id={$_POST['class_id']}&subject_id={$_POST['subject_id']}&session_id={$_POST['session_id']}&term_id={$_POST['term_id']}';</script>";

} catch (PDOException $e) {
  die('Error: ' . $e->getMessage());
}
?>
