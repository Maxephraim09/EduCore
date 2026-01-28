<?php
require_once('../../db/config.php');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $session_name = trim($_POST['session_name']);
  if ($session_name != '') {
    try {
      $conn = new PDO("mysql:host=".DBHost.";dbname=".DBName.";charset=".DBCharset, DBUser, DBPass);
      $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $stmt = $conn->prepare("INSERT INTO tbl_sessions (session_name) VALUES (:name)");
      $stmt->execute([':name' => $session_name]);
      $_SESSION['reply'] = ['type' => 'success', 'message' => 'New session added successfully.'];
    } catch (PDOException $e) {
      $_SESSION['reply'] = ['type' => 'danger', 'message' => 'Error: '.$e->getMessage()];
    }
  }
  header("Location: ../academic_session");
  exit;
}
?>
