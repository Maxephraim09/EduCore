<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

chdir('../../');
session_start();
require_once('db/config.php');
require_once('const/school.php');
require_once('const/check_session.php');

if (!($res == "1" && $level == "1")) {
    header("location:../");
    exit;
}

try {
    $conn = new PDO(
        "mysql:host=" . DBHost . ";dbname=" . DBName . ";charset=" . DBCharset,
        DBUser,
        DBPass
    );
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Function to get grade and remark
    function getGradeAndRemark($conn, $total) {
        $stmt = $conn->prepare("SELECT name, remark FROM tbl_grade_system WHERE :total BETWEEN min AND max LIMIT 1");
        $stmt->execute([':total' => $total]);
        $grade = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($grade) {
            return [$grade['name'], $grade['remark']];
        } else {
            return ['-', 'No Remark'];
        }
    }

    // Common SQL
   // ✅ Make sure your table column can store alphanumeric IDs
// ALTER TABLE tbl_results MODIFY student_id VARCHAR(50) NOT NULL;

// ✅ Also ensure there's a unique key for proper upsert behavior
// ALTER TABLE tbl_results ADD UNIQUE KEY uniq_result (student_id, class_id, subject_id, session_id, term_id);

$insert = $conn->prepare("
    INSERT INTO tbl_results (
        student_id, class_id, subject_id, session_id, term_id,
        first_test, second_test, third_test, exam_score, total_score, grade, remark
    ) VALUES (
        :student_id, :class_id, :subject_id, :session_id, :term_id,
        :first_test, :second_test, :third_test, :exam_score, :total_score, :grade, :remark
    )
    ON DUPLICATE KEY UPDATE
        first_test = VALUES(first_test),
        second_test = VALUES(second_test),
        third_test = VALUES(third_test),
        exam_score = VALUES(exam_score),
        total_score = VALUES(total_score),
        grade = VALUES(grade),
        remark = VALUES(remark)
");

// ✅ Manual Entry Section
if (isset($_POST['save_results']) && !empty($_POST['scores'])) {
    $class_id   = trim($_POST['class_id']);
    $subject_id = trim($_POST['subject_id']);
    $session_id = trim($_POST['session_id']);
    $term_id    = trim($_POST['term_id']);

    $count = 0;

    foreach ($_POST['scores'] as $student_id => $scoreData) {
        $student_id = trim($student_id);

        // skip empty IDs
        if ($student_id === '') continue;

        $first  = (float)($scoreData['first'] ?? 0);
        $second = (float)($scoreData['second'] ?? 0);
        $third  = (float)($scoreData['third'] ?? 0);
        $exam   = (float)($scoreData['exam'] ?? 0);
        $total  = $first + $second + $third + $exam;

        list($grade, $remark) = getGradeAndRemark($conn, $total);

        // Debugging (optional)
        // echo "Inserting student $student_id -> total $total<br>";

        $insert->execute([
            ':student_id'  => $student_id, // ✅ keep as string (VARCHAR)
            ':class_id'    => $class_id,
            ':subject_id'  => $subject_id,
            ':session_id'  => $session_id,
            ':term_id'     => $term_id,
            ':first_test'  => $first,
            ':second_test' => $second,
            ':third_test'  => $third,
            ':exam_score'  => $exam,
            ':total_score' => $total,
            ':grade'       => $grade,
            ':remark'      => $remark
        ]);

        $count++;
    }

    echo "<script>alert('Successfully saved $count result(s) with student IDs.'); window.location.href='../manage_results';</script>";
    exit;
}


    // ---- CSV Upload Section ----
    elseif (isset($_FILES['csv_file']) && $_FILES['csv_file']['error'] == UPLOAD_ERR_OK) {
        $class_id   = $_POST['class_id'];
        $subject_id = $_POST['subject_id'];
        $session_id = $_POST['session_id'];
        $term_id    = $_POST['term_id'];

        $tmpName = $_FILES['csv_file']['tmp_name'];
        $handle = fopen($tmpName, 'r');
        if ($handle === false) {
            throw new Exception("Failed to open uploaded CSV file.");
        }

        $count = 0;
        fgetcsv($handle); // skip header

        while (($data = fgetcsv($handle)) !== false) {
            if (count($data) < 5) continue;
            [$student_id, $first, $second, $third, $exam] = $data;

            $first = (float)$first;
            $second = (float)$second;
            $third = (float)$third;
            $exam = (float)$exam;
            $total = $first + $second + $third + $exam;
            list($grade, $remark) = getGradeAndRemark($conn, $total);

            $insert->execute([
                ':student_id'  => trim($student_id),
                ':class_id'    => $class_id,
                ':subject_id'  => $subject_id,
                ':session_id'  => $session_id,
                ':term_id'     => $term_id,
                ':first_test'  => $first,
                ':second_test' => $second,
                ':third_test'  => $third,
                ':exam_score'  => $exam,
                ':total_score' => $total,
                ':grade'       => $grade,
                ':remark'      => $remark
            ]);

            $count++;
        }
        fclose($handle);

        echo "<script>alert('Successfully imported $count result(s) from CSV with grades.'); window.location.href='../manage_results';</script>";
        exit;
    }

    // ---- No Input Detected ----
    else {
        echo "<h3 style='color:red; text-align:center; margin-top:40px;'>No POST or CSV detected — nothing to save.</h3>";
        echo "<p style='text-align:center;'><a href='../manage_results'>Go back to Results Management</a></p>";
        exit;
    }

} catch (PDOException $e) {
    echo "<h3 style='color:red;'>Database Error: " . htmlspecialchars($e->getMessage()) . "</h3>";
} catch (Exception $ex) {
    echo "<h3 style='color:red;'>Error: " . htmlspecialchars($ex->getMessage()) . "</h3>";
}
?>
