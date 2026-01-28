<?php
require_once('../db/config.php');
session_start();

if (!isset($_SESSION['res']) || $_SESSION['level'] != 1) exit;

try {
  $conn = new PDO("mysql:host=".DBHost.";dbname=".DBName.";charset=".DBCharset, DBUser, DBPass);
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  $stmt = $conn->prepare("
    SELECT s.id, CONCAT(s.lname, ' ', s.fname, ' ', s.mname) AS fullname
    FROM tbl_students s
    INNER JOIN tbl_student_subjects ss
      ON s.id = ss.student_id
    WHERE ss.class_id = ? AND ss.subject_id = ? AND ss.session_id = ? AND ss.term_id = ?
  ");
  $stmt->execute([$_GET['class_id'], $_GET['subject_id'], $_GET['session_id'], $_GET['term_id']]);
  $students = $stmt->fetchAll(PDO::FETCH_ASSOC);

  header('Content-Type: text/csv');
  header('Content-Disposition: attachment; filename="result_template.csv"');
  $output = fopen('php://output', 'w');
  fputcsv($output, ['student_id', 'ca_score', 'exam_score']);

  foreach ($students as $s) {
    fputcsv($output, [$s['id'], '', '']);
  }
  fclose($output);

} catch (PDOException $e) {
  die('Error: ' . $e->getMessage());
}
?>
