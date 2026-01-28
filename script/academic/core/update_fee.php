<?php
session_start();
require_once('../../db/config.php');

try {
    $conn = new PDO("mysql:host=" . DBHost . ";dbname=" . DBName . ";charset=" . DBCharset, DBUser, DBPass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (!empty($_POST['id']) && isset($_POST['amount'])) {
        $id = intval($_POST['id']);
        $amount = floatval($_POST['amount']);

        $stmt = $conn->prepare("UPDATE tbl_fee_mapping SET amount = :amount WHERE id = :id");
        $stmt->bindParam(':amount', $amount);
        $stmt->bindParam(':id', $id);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Fee updated successfully!";
        } else {
            $_SESSION['error'] = "Failed to update fee record.";
        }
    } else {
        $_SESSION['error'] = "Missing required data.";
    }

} catch (PDOException $e) {
    $_SESSION['error'] = "Database Error: " . $e->getMessage();
}

header("Location: ../manage_fees.php");
exit;
?>
