<?php
session_start();
require_once('../../db/config.php');

if (!isset($_GET['id'])) {
    $_SESSION['error'] = "Missing application ID.";
    header("Location: ../manage_student_application.php");
    exit;
}

try {
    $conn = new PDO("mysql:host=" . DBHost . ";dbname=" . DBName . ";charset=" . DBCharset, DBUser, DBPass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $id = intval($_GET['id']);

    // Update status
    $stmt = $conn->prepare("UPDATE tbl_applications SET status = 'Admitted' WHERE id = :id");
    $stmt->bindParam(':id', $id);
    $stmt->execute();

    $_SESSION['success'] = "Application approved successfully! Student admitted.";
    header("Location: ../manage_student_application.php");
    exit;

} catch (PDOException $e) {
    $_SESSION['error'] = "Error: " . $e->getMessage();
    header("Location: ../manage_student_application.php");
    exit;
}
?>
