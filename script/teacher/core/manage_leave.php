<?php
session_start();

// Absolute paths
require_once(__DIR__ . '/../../db/config.php');
require_once(__DIR__ . '/../../const/check_session.php');

// Only allow teachers
if (!($res == "1" && $level == "2")) {
    header("location:../../");
    exit;
}

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $user_name = $_POST['user_name'] ?? '';
    $purpose = $_POST['purpose'] ?? '';
    $start_date = $_POST['start_date'] ?? '';
    $returning_date = $_POST['returning_date'] ?? '';
    $number_of_days = $_POST['number_of_days'] ?? 0;

    try {
        $conn = new PDO('mysql:host='.DBHost.';dbname='.DBName.';charset='.DBCharset, DBUser, DBPass);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $conn->prepare("
            INSERT INTO tbl_leave_applications 
            (staff_id, user_name, purpose, start_date, returning_date, number_of_days, status, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, 0, NOW())
        ");
        $stmt->execute([$account_id, $user_name, $purpose, $start_date, $returning_date, $number_of_days]);

        $_SESSION['leave_msg'] = "Leave application submitted successfully.";
        header("location:../manage_leave");
        exit;

    } catch(PDOException $e){
        $_SESSION['leave_msg'] = "Error: " . $e->getMessage();
        header("location:../manage_leave");
        exit;
    }
}
?>
