<?php
session_start();
require_once('../../db/config.php');

try {
    $conn = new PDO("mysql:host=" . DBHost . ";dbname=" . DBName . ";charset=" . DBCharset, DBUser, DBPass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (!empty($_POST['id'])) {
        $stmt = $conn->prepare("
            UPDATE tbl_staff_applications 
            SET first_name=:fname, last_name=:lname, email=:email, number=:num, address=:addr,
                qualification=:qual, category=:cat, status=:status
            WHERE id=:id
        ");
        $stmt->execute([
            ':fname' => $_POST['first_name'],
            ':lname' => $_POST['last_name'],
            ':email' => $_POST['email'],
            ':num' => $_POST['number'],
            ':addr' => $_POST['address'],
            ':qual' => $_POST['qualification'],
            ':cat' => $_POST['category'],
            ':status' => $_POST['status'],
            ':id' => $_POST['id']
        ]);
        $_SESSION['success'] = "Application updated successfully.";
    } else {
        $_SESSION['error'] = "Missing application ID.";
    }

} catch (PDOException $e) {
    $_SESSION['error'] = "Error: " . $e->getMessage();
}

header("Location: ../manage_staff_applications.php");
exit;
?>
