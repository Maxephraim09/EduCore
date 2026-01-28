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

try {
    $conn = new PDO(
        "mysql:host=" . DBHost . ";dbname=" . DBName . ";charset=" . DBCharset,
        DBUser,
        DBPass
    );
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['student_id'], $_POST['session_id'], $_POST['term_id'])) {
        $student_id = $_POST['student_id'];
        $session_id = $_POST['session_id'];
        $term_id = $_POST['term_id'];

        // Loop through subjects
        foreach ($_POST['subject_id'] as $i => $subject_id) {
            $first_test  = isset($_POST['first_test'][$i])  ? (float)$_POST['first_test'][$i]  : 0;
            $second_test = isset($_POST['second_test'][$i]) ? (float)$_POST['second_test'][$i] : 0;
            $third_test  = isset($_POST['third_test'][$i])  ? (float)$_POST['third_test'][$i]  : 0;
            $exam_score  = isset($_POST['exam_score'][$i])  ? (float)$_POST['exam_score'][$i]  : 0;
            $total_score = $first_test + $second_test + $third_test + $exam_score;

            // Check existing result
            $check = $conn->prepare("SELECT id FROM tbl_results 
                                     WHERE student_id=? AND subject_id=? AND session_id=? AND term_id=?");
            $check->execute([$student_id, $subject_id, $session_id, $term_id]);

            if ($check->rowCount() > 0) {
                // Update existing record
                $update = $conn->prepare("UPDATE tbl_results 
                    SET first_test=?, second_test=?, third_test=?, exam_score=?, total=?
                    WHERE student_id=? AND subject_id=? AND session_id=? AND term_id=?");
                $update->execute([
                    $first_test, $second_test, $third_test, $exam_score, $total_score,
                    $student_id, $subject_id, $session_id, $term_id
                ]);
            } else {
                // Insert new record
                $insert = $conn->prepare("INSERT INTO tbl_results 
                    (student_id, subject_id, session_id, term_id, first_test, second_test, third_test, exam_score, total)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $insert->execute([
                    $student_id, $subject_id, $session_id, $term_id,
                    $first_test, $second_test, $third_test, $exam_score, $total_score
                ]);
            }
        }

        // For AJAX request
        if (isset($_POST['ajax']) && $_POST['ajax'] == '1') {
            echo json_encode(['status' => 'success', 'message' => 'Results saved successfully']);
            exit;
        }

        // Redirect back to the score sheet with success message
        header("Location: ../single_score_sheet?student_id=$student_id&session_id=$session_id&term_id=$term_id&saved=1");
        exit;
    } else {
        echo "Invalid request.";
    }

} catch (PDOException $e) {
    echo "Database Error: " . $e->getMessage();
}
