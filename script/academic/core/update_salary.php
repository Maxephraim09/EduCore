<?php
session_start();
require_once('../../db/config.php');

try {
    $conn = new PDO("mysql:host=" . DBHost . ";dbname=" . DBName . ";charset=" . DBCharset, DBUser, DBPass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (!empty($_POST['id'])) {
        $id = intval($_POST['id']);
        $qualification = trim($_POST['qualification']);
        $role_id = intval($_POST['role_id']);
        $employment_type = trim($_POST['employment_type']);
        $account_number = trim($_POST['account_number']);
        $bank_name = trim($_POST['bank_name']);
        $bank_code = trim($_POST['bank_code']);
        $salary_amount = floatval($_POST['salary_amount']);
        $status = trim($_POST['status']);

        $stmt = $conn->prepare("UPDATE tbl_salary_mapping 
            SET qualification = :qualification, role_id = :role_id, employment_type = :employment_type, 
                account_number = :account_number, bank_name = :bank_name, bank_code = :bank_code, 
                salary_amount = :salary_amount, status = :status 
            WHERE id = :id");

        $stmt->bindParam(':qualification', $qualification);
        $stmt->bindParam(':role_id', $role_id);
        $stmt->bindParam(':employment_type', $employment_type);
        $stmt->bindParam(':account_number', $account_number);
        $stmt->bindParam(':bank_name', $bank_name);
        $stmt->bindParam(':bank_code', $bank_code);
        $stmt->bindParam(':salary_amount', $salary_amount);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $id);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Salary record updated successfully!";
        } else {
            $_SESSION['error'] = "Failed to update salary record.";
        }
    } else {
        $_SESSION['error'] = "Missing salary record ID.";
    }
} catch (PDOException $e) {
    $_SESSION['error'] = "Database Error: " . $e->getMessage();
}

header("Location: ../manage_salary.php");
exit;
?>
