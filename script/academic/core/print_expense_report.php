<?php
session_start();
require_once('../../db/config.php');
require_once('../../const/school.php');

if (!isset($_SESSION['current_session_id']) || !isset($_SESSION['current_term_id'])) {
    die("Session or term not found. Please re-login.");
}

$current_session = $_SESSION['current_session_id'];
$current_term    = $_SESSION['current_term_id'];
$report_type     = $_GET['report_type'] ?? 'all';

try {
    $conn = new PDO("mysql:host=" . DBHost . ";dbname=" . DBName . ";charset=" . DBCharset, DBUser, DBPass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Build the query based on filter
    if ($report_type == 'annual') {
        $query = "SELECT e.*, c.name AS category_name 
                  FROM tbl_expenses e 
                  JOIN tbl_category c ON e.category_id = c.id 
                  WHERE e.session_id = ?
                  ORDER BY e.date DESC";
        $params = [$current_session];

        $title = "Annual Expense Report - Session $current_session";

    } elseif ($report_type == 'termly') {
        $query = "SELECT e.*, c.name AS category_name 
                  FROM tbl_expenses e 
                  JOIN tbl_category c ON e.category_id = c.id 
                  WHERE e.session_id = ? AND e.term_id = ?
                  ORDER BY e.date DESC";
        $params = [$current_session, $current_term];

        $title = "Termly Expense Report - Session $current_session, Term $current_term";

    } else {
        $query = "SELECT e.*, c.name AS category_name 
                  FROM tbl_expenses e 
                  JOIN tbl_category c ON e.category_id = c.id 
                  ORDER BY e.date DESC";
        $params = [];

        $title = "Comprehensive Expense Report (All Time)";
    }

    $stmt = $conn->prepare($query);
    $stmt->execute($params);
    $expenses = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title><?= htmlspecialchars($title) ?></title>
<link rel="stylesheet" href="../../css/bootstrap.min.css">
<style>
body { font-family: Arial, sans-serif; }
table { width: 100%; border-collapse: collapse; margin-top: 20px; }
th, td { border: 1px solid #999; padding: 8px; text-align: left; }
th { background-color: #f0f0f0; }
.text-center { text-align: center; }
@media print {
    .no-print { display: none; }
}
</style>
</head>
<body>

<h2 class="text-center"><?= htmlspecialchars($title) ?></h2>
<p class="text-center"><strong>Date Generated:</strong> <?= date('d M, Y h:i A') ?></p>

<table>
  <thead>
    <tr>
      <th>Date</th>
      <th>Category</th>
      <th>Title</th>
      <th>Description</th>
      <th>Amount</th>
      <th>Balance</th>
      <th>Status</th>
    </tr>
  </thead>
  <tbody>
  <?php 
  if (count($expenses) > 0): 
      $total_amount = 0;
      $total_balance = 0;
      foreach ($expenses as $row): 
          $total_amount += $row['amount'];
          $total_balance += $row['balance'];
  ?>
    <tr>
      <td><?= htmlspecialchars($row['date']) ?></td>
      <td><?= htmlspecialchars($row['category_name']) ?></td>
      <td><?= htmlspecialchars($row['title']) ?></td>
      <td><?= htmlspecialchars($row['description']) ?></td>
      <td>₦<?= number_format($row['amount'], 2) ?></td>
      <td>₦<?= number_format($row['balance'], 2) ?></td>
      <td><?= htmlspecialchars($row['status']) ?></td>
    </tr>
  <?php endforeach; ?>
  <tr>
    <th colspan="4" class="text-end">TOTAL</th>
    <th>₦<?= number_format($total_amount, 2) ?></th>
    <th>₦<?= number_format($total_balance, 2) ?></th>
    <th></th>
  </tr>
  <?php else: ?>
    <tr><td colspan="7" class="text-center">No expenses found for this report.</td></tr>
  <?php endif; ?>
  </tbody>
</table>

<div class="no-print text-center mt-3">
  <button class="btn btn-primary" onclick="window.print()">Print Report</button>
</div>

</body>
</html>
