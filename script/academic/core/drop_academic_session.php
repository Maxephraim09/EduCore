<?php
require_once('../../db/config.php');
if (isset($_GET['id'])) {
  try {
    $id = intval($_GET['id']);
    $conn = new PDO("mysql:host=".DBHost.";dbname=".DBName.";charset=".DBCharset, DBUser, DBPass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $conn->prepare("DELETE FROM tbl_sessions WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $_SESSION['reply'] = ['type' => 'success', 'message' => 'Session deleted successfully.'];
  } catch (PDOException $e) {
    $_SESSION['reply'] = ['type' => 'danger', 'message' => $e->getMessage()];
  }
  header("Location: ../academic_session");
  exit;
}
?>
