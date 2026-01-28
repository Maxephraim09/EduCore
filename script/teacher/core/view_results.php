<?php
chdir('../../');
session_start();
require_once('db/config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $session_id = $_POST['session'] ?? 0;
    $term_id    = $_POST['term'] ?? 0;
    $class_id   = $_POST['class'] ?? 0;
    $subject_id = $_POST['subject'] ?? 0;

    try {
        $conn = new PDO('mysql:host='.DBHost.';dbname='.DBName.';charset='.DBCharset, DBUser, DBPass);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Check score entry permission
        $stmt = $conn->prepare("SELECT status FROM tbl_score_entry_settings WHERE session_id = ? AND term_id = ?");
        $stmt->execute([$session_id, $term_id]);
        $status = $stmt->fetchColumn();
        $status = $status !== false ? $status : 0;

        if ($status == 0) {
            // Score entry disabled
            $_SESSION['result_error'] = "RESULT ENTRY NOT PERMITTED, CONTACT ADMIN";
            header("location:../../teacher/manage_results.php");
            exit;
        }

        // Score entry allowed, save data
        $_SESSION['result__data'] = $_POST;
        header("location:../results");
        exit;

    } catch (PDOException $e) {
        $_SESSION['result_error'] = "Database error: " . $e->getMessage();
        header("location:../../teacher/manage_results.php");
        exit;
    }

} else {
    header("location:../");
    exit;
}
