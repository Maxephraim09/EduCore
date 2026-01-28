<?php
session_start();
require_once('../../db/config.php');
require_once('../../const/school.php');
require_once('../../const/check_session.php');

if (!isset($_POST['class_id']) || empty($_POST['class_id'])) {
    echo '<option disabled selected value="">No class selected</option>';
    exit;
}

$class_id = intval($_POST['class_id']);
$teacher_id = $account_id; // from check_session.php (current logged-in teacher)

try {
    $conn = new PDO('mysql:host=' . DBHost . ';dbname=' . DBName . ';charset=' . DBCharset, DBUser, DBPass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // ✅ Fetch subjects the teacher teaches for this class
    $stmt = $conn->prepare("
        SELECT s.id, s.name 
        FROM tbl_subject_combinations sc
        INNER JOIN tbl_subjects s ON sc.subject_id = s.id
        WHERE sc.class_id = :class_id AND sc.teacher_id = :teacher_id
        ORDER BY s.name ASC
    ");
    $stmt->execute([':class_id' => $class_id, ':teacher_id' => $teacher_id]);

    if ($stmt->rowCount() > 0) {
        echo '<option selected disabled value="">Select Subject</option>';
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo '<option value="' . $row['id'] . '">' . htmlspecialchars($row['name']) . '</option>';
        }
    } else {
        echo '<option selected disabled>No subjects found for this class</option>';
    }

} catch (PDOException $e) {
    echo '<option disabled>Error: ' . $e->getMessage() . '</option>';
}
?>
