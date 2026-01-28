<?php
chdir('../');
session_start();
require_once('db/config.php');
require_once('const/school.php');
require_once('const/check_session.php');

if (!($res == "1" && $level == "1")) {
    header("location:../");
    exit;
}

$id = $_GET['id'] ?? null;
if ($id) {
    try {
        $conn = new PDO("mysql:host=" . DBHost . ";dbname=" . DBName . ";charset=" . DBCharset, DBUser, DBPass);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $conn->prepare("DELETE FROM tbl_class_promotion WHERE id = :id");
        $stmt->execute([':id' => $id]);

        $_SESSION['success'] = "Mapping deleted successfully.";
        header("location: ../academic/promote_mapping.php");
        exit;
    } catch (PDOException $e) {
        die("Database error: " . $e->getMessage());
    }
} else {
    $_SESSION['error'] = "Invalid mapping ID.";
    header("location: ../academic/promote_mapping.php");
    exit;
}
?>
