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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $from_class = $_POST['from_class_id'] ?? null;
    $to_class = $_POST['to_class_id'] ?? null;
    $order = $_POST['order'] ?? 0;

    if ($id && $from_class && $to_class) {
        try {
            $conn = new PDO("mysql:host=" . DBHost . ";dbname=" . DBName . ";charset=" . DBCharset, DBUser, DBPass);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $stmt = $conn->prepare("UPDATE tbl_class_promotion SET from_class_id = :from_class, to_class_id = :to_class, `order` = :order WHERE id = :id");
            $stmt->execute([
                ':from_class' => $from_class,
                ':to_class' => $to_class,
                ':order' => $order,
                ':id' => $id
            ]);

            $_SESSION['success'] = "Mapping updated successfully.";
            header("location: ../academic/maping.php");
            exit;
        } catch (PDOException $e) {
            die("Database error: " . $e->getMessage());
        }
    } else {
        $_SESSION['error'] = "All fields are required.";
        header("location: ../academic/maping.php");
        exit;
    }
} else {
    header("location: ../academic/maping.php");
    exit;
}
?>
