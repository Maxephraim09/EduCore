<?php
include('../../db/config.php');
try {
  $conn = new PDO("mysql:host=".DBHost.";dbname=".DBName.";charset=".DBCharset, DBUser, DBPass);
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  $stmt = $conn->prepare("UPDATE tbl_loan_applications SET approved_amount=?, repayment_date=?, status='Approved' WHERE id=?");
  $stmt->execute([$_POST['approved_amount'], $_POST['repayment_date'], $_POST['id']]);

  header("Location: ../manage_loan.php?msg=approved");
} catch (PDOException $e) {
  echo "Error: ".$e->getMessage();
}
?>
