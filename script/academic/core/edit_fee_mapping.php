<?php
chdir('../../');
session_start();
require_once('db/config.php');
require_once('const/school.php');
require_once('const/check_session.php');

if (!($res == "1" && $level == "1")) {
    header("location:../../");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id = trim($_POST['id'] ?? '');
    $session = trim($_POST['session'] ?? '');
    $term = trim($_POST['term'] ?? '');
    $class_id = trim($_POST['class_id'] ?? '');
    $amount = trim($_POST['amount'] ?? '');

    if (empty($id) || empty($session) || empty($term) || empty($class_id) || empty($amount)) {
        echo "<script>alert('❌ All fields are required.'); window.history.back();</script>";
        exit;
    }

    try {
        $conn = new PDO("mysql:host=" . DBHost . ";dbname=" . DBName . ";charset=" . DBCharset, DBUser, DBPass);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Validate session and term
        $session_stmt = $conn->prepare("SELECT id FROM tbl_sessions WHERE session_name = ? LIMIT 1");
        $session_stmt->execute([$session]);
        $session_row = $session_stmt->fetch(PDO::FETCH_ASSOC);
        $session_id = $session_row ? $session_row['id'] : null;

        $term_stmt = $conn->prepare("SELECT id FROM tbl_terms WHERE name = ? LIMIT 1");
        $term_stmt->execute([$term]);
        $term_row = $term_stmt->fetch(PDO::FETCH_ASSOC);
        $term_id = $term_row ? $term_row['id'] : null;

        if (!$session_id || !$term_id) {
            echo "<script>alert('❌ Invalid session or term detected.'); window.history.back();</script>";
            exit;
        }

        // Check if mapping exists
        $check = $conn->prepare("SELECT * FROM tbl_fee_mapping WHERE id = ?");
        $check->execute([$id]);
        if ($check->rowCount() == 0) {
            echo "<script>alert('❌ Fee mapping record not found.'); window.history.back();</script>";
            exit;
        }

        // Update
        $update = $conn->prepare("
            UPDATE tbl_fee_mapping 
            SET session_id = ?, term_id = ?, class_id = ?, amount = ?, updated_at = NOW()
            WHERE id = ?
        ");
        $update->execute([$session_id, $term_id, $class_id, $amount, $id]);

        echo "<script>alert('✅ Fee mapping updated successfully!'); window.location='../manage_fees.php';</script>";
        exit;

    } catch (PDOException $e) {
        echo "<script>alert('❌ Database Error: " . addslashes($e->getMessage()) . "'); window.history.back();</script>";
        exit;
    }
} else {
    header("location:../manage_fees.php");
    exit;
}
?>
