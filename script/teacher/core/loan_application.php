<?php
chdir('../../');
session_start();
require_once('db/config.php');
require_once('const/check_session.php');

if(!($res == "1" && $level == "2")) {
    header("location:../../");
    exit;
}

if(isset($_POST['submit'])){
    $staff_id = $account_id;
    $loan_amount = floatval($_POST['loan_amount']);
    $repayment_date = $_POST['repayment_date'];

    try {
        $conn = new PDO('mysql:host='.DBHost.';dbname='.DBName.';charset='.DBCharset, DBUser, DBPass);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $conn->prepare("INSERT INTO tbl_loan_applications (staff_id, loan_amount, repayment_date, status, repayment_status, created_at) VALUES (?, ?, ?, 0, 0, NOW())");
        $stmt->execute([$staff_id, $loan_amount, $repayment_date]);

        $_SESSION['loan_msg'] = "Loan application submitted successfully!";
        header("location:../loan_application");
        exit;

    } catch(PDOException $e) {
        $_SESSION['loan_msg'] = "Error submitting loan: " . $e->getMessage();
        header("location:../loan_application");
        exit;
    }
} else {
    header("location:../loan_application");
    exit;
}
