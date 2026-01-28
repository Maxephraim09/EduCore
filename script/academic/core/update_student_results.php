<?php
chdir('../');
session_start();

require_once('db/config.php');
require_once('const/school.php');
require_once('const/check_session.php');

// Ensure only authorized academic staff
if ($res != "1" || $level != "1") {
    header("location:../");
    exit;
}

if (!isset($_POST['update_results'], $_POST['student_id'], $_POST['class_id'], $_POST['session_id'], $_POST['term_id'], $_POST['scores'])) {
    die("<div class='alert alert-danger text-center'>Invalid request!</div>");
}

$student_id = $_POST['student_id'];
$class_id   = $_POST['class_id'];
$session_id = $_POST['session_id'];
$term_id    = $_POST['term_id'];
$scores     = $_POST['scores'];

try {
    $conn = new PDO(
        "mysql:host=" . DBHost . ";dbname=" . DBName . ";charset=" . DBCharset,
        DBUser,
        DBPass
    );
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmtSelect = $conn->prepare("
        SELECT id FROM tbl_results 
        WHERE student_id = ? AND class_id = ? AND session_id = ? AND term_id = ? AND subject_id = ?
    ");

    $stmtUpdate = $conn->prepare("
        UPDATE tbl_results SET 
            first_test = ?, 
            second_test = ?, 
            third_test = ?, 
            exam_score = ?, 
            total_score = ?, 
            updated_at = NOW()
        WHERE id = ?
    ");

    $stmtInsert = $conn->prepare("
        INSERT INTO tbl_results 
            (student_id, class_id, subject_id, session_id, term_id, first_test, second_test, third_test, exam_score, total_score, created_at) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
    ");

    foreach ($scores as $subject_id => $score) {
        $first  = floatval($score['first'] ?? 0);
        $second = floatval($score['second'] ?? 0);
        $third  = floatval($score['third'] ?? 0);
        $exam   = floatval($score['exam'] ?? 0);
        $total  = $first + $second + $third + $exam;

        // Check if result exists
        $stmtSelect->execute([$student_id, $class_id, $session_id, $term_id, $subject_id]);
        $existing = $stmtSelect->fetch(PDO::FETCH_ASSOC);

        if ($existing) {
            $stmtUpdate->execute([$first, $second, $third, $exam, $total, $existing['id']]);
        } else {
            $stmtInsert->execute([$student_id, $class_id, $subject_id, $session_id, $term_id, $first, $second, $third, $exam, $total]);
        }
    }

    echo "<div class='alert alert-success text-center'>Results updated successfully!</div>";
} catch (PDOException $e) {
    die("<div class='alert alert-danger text-center'>Database Error: " . htmlspecialchars($e->getMessage()) . "</div>");
}
?>
