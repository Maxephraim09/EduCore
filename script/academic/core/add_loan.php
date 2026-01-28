<?php
include('../../db/config.php');
try {
  $conn = new PDO("mysql:host=".DBHost.";dbname=".DBName.";charset=".DBCharset, DBUser, DBPass);
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  $stmt = $conn->prepare("INSERT INTO tbl_loan_applications (staff_id, loan_amount, repayment_date) VALUES (?,?,?)");
  $stmt->execute([$_POST['staff_id'], $_POST['loan_amount'], $_POST['repayment_date']]);

  header("Location: ../manage_loan.php?msg=added");
} catch (PDOException $e) {
  echo "Error: ".$e->getMessage();
}
?>
