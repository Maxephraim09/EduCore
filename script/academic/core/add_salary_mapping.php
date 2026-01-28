<?php
session_start();
require_once('../../db/config.php');

try {
    $conn = new PDO("mysql:host=" . DBHost . ";dbname=" . DBName . ";charset=" . DBCharset, DBUser, DBPass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (
        isset($_POST['staff_id'], $_POST['qualification'], $_POST['role_id'], $_POST['employment_type'],
              $_POST['account_number'], $_POST['bank_name'], $_POST['bank_code'], $_POST['salary_amount'], $_POST['status'])
        && !empty($_POST['staff_id'])
    ) {
        $staff_id = intval($_POST['staff_id']);
        $qualification = trim($_POST['qualification']);
        $role_id = intval($_POST['role_id']);
        $employment_type = trim($_POST['employment_type']);
        $account_number = trim($_POST['account_number']);
        $bank_name = trim($_POST['bank_name']);
        $bank_code = trim($_POST['bank_code']);
        $salary_amount = floatval($_POST['salary_amount']);
        $status = trim($_POST['status']);

        $stmt = $conn->prepare("INSERT INTO tbl_salary_mapping 
            (staff_id, qualification, role_id, employment_type, account_number, bank_name, bank_code, salary_amount, status) 
            VALUES (:staff_id, :qualification, :role_id, :employment_type, :account_number, :bank_name, :bank_code, :salary_amount, :status)");

        $stmt->bindParam(':staff_id', $staff_id);
        $stmt->bindParam(':qualification', $qualification);
        $stmt->bindParam(':role_id', $role_id);
        $stmt->bindParam(':employment_type', $employment_type);
        $stmt->bindParam(':account_number', $account_number);
        $stmt->bindParam(':bank_name', $bank_name);
        $stmt->bindParam(':bank_code', $bank_code);
        $stmt->bindParam(':salary_amount', $salary_amount);
        $stmt->bindParam(':status', $status);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Salary mapping added successfully!";
        } else {
            $_SESSION['error'] = "Failed to add salary mapping.";
        }
    } else {
        $_SESSION['error'] = "Missing required form data.";
    }
} catch (PDOException $e) {
    $_SESSION['error'] = "Database Error: " . $e->getMessage();
}

header("Location: ../manage_salary.php");
exit;
?>
