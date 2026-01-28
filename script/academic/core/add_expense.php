<?php
session_start();
require_once('../../db/config.php');
require_once('../../const/school.php');

if (!isset($_SESSION['current_session_id']) || !isset($_SESSION['current_term_id'])) {
    die("Session or term not found. Please re-login.");
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $category     = $_POST['category'];
    $session_id   = $_SESSION['current_session_id'];
    $term_id      = $_SESSION['current_term_id'];
    $title        = trim($_POST['title']);
    $description  = trim($_POST['description']);
    $amount       = floatval($_POST['amount']);
    $balance      = floatval($_POST['balance']);
    $status       = trim($_POST['status']);
    $date         = date('Y-m-d');

    try {
        $conn = new PDO("mysql:host=" . DBHost . ";dbname=" . DBName . ";charset=" . DBCharset, DBUser, DBPass);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $conn->prepare("
            INSERT INTO tbl_expenses (category_id, session_id, term_id, title, description, amount, balance, status, date)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$category, $session_id, $term_id, $title, $description, $amount, $balance, $status, $date]);

        $_SESSION['success'] = "Expense added successfully!";
        header("Location: ../../admin/expenses");
        exit;

    } catch (PDOException $e) {
        $_SESSION['error'] = "Database Error: " . $e->getMessage();
        header("Location: ../../admin/expenses");
        exit;
    }
} else {
    header("Location: ../../admin/expenses");
    exit;
}
