<?php
chdir('../../');
//session_start();
// Correct paths from core file location
require_once(__DIR__ . '/../../db/config.php');
require_once(__DIR__ . '/../../const/school.php');
require_once(__DIR__ . '/../../const/check_session.php');

if ($res != "1" || $level != "2") {
    header("location:../../");
    exit;
}

try {
    $conn = new PDO("mysql:host=".DBHost.";dbname=".DBName.";charset=".DBCharset, DBUser, DBPass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check if submissions are allowed
    $stmt = $conn->query("SELECT allow_submission FROM tbl_exams_questions_settings ORDER BY id DESC LIMIT 1");
    $settings = $stmt->fetch(PDO::FETCH_ASSOC);
    $allow_submission = $settings['allow_submission'] ?? 1;

    if(!$allow_submission){
        die("Admin has disabled exam submissions currently.");
    }

    // Active session
    $stmt = $conn->query("SELECT * FROM tbl_sessions WHERE satus = 1 LIMIT 1");
    $active_session = $stmt->fetch(PDO::FETCH_ASSOC);


    // Fetch active term
    $stmt = $conn->query("SELECT * FROM tbl_terms WHERE status = 1 ORDER BY id DESC LIMIT 1");
    $term = $stmt->fetch(PDO::FETCH_ASSOC);


    // Classes & subjects assigned to teacher
    $stmt = $conn->prepare("SELECT DISTINCT c.id, c.name
        FROM tbl_subject_combinations sc
        JOIN tbl_classes c ON sc.class_id = c.id
        WHERE sc.teacher_id = ?");
    $stmt->execute([$account_id]);
    $classes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $conn->prepare("SELECT DISTINCT s.id, s.name
        FROM tbl_subject_combinations sc
        JOIN tbl_subjects s ON sc.subject_id = s.id
        WHERE sc.teacher_id = ?");
    $stmt->execute([$account_id]);
    $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch(PDOException $e){
    die("Database error: ".$e->getMessage());
}

// Handle form submission
$message = '';
if(isset($_POST['submit'])){
    $class_id = intval($_POST['class_id']);
    $subject_id = intval($_POST['subject_id']);
    $instructions = trim($_POST['instructions']);
    $question_text = trim($_POST['question_text']);
    $file_path = null;

    // Check if teacher has existing submission
    $stmt = $conn->prepare("SELECT * FROM teacher_exams WHERE teacher_id=? AND session_id=? AND term_id=? AND class_id=? AND subject_id=? LIMIT 1");
    $stmt->execute([$account_id, $active_session['id'], $term['id'], $class_id, $subject_id]);
    $existing = $stmt->fetch(PDO::FETCH_ASSOC);

    if($existing && $existing['status'] == 'APPROVED'){
        $message = "Questions for this selection have already been submitted and approved.";
    } else {
        // Handle file upload
        if(isset($_FILES['question_file']) && $_FILES['question_file']['error'] == 0){
            $upload_dir = '../../uploads/exams/';
            if(!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
            $file_name = time().'_'.basename($_FILES['question_file']['name']);
            $file_path = $upload_dir.$file_name;
            move_uploaded_file($_FILES['question_file']['tmp_name'], $file_path);
            $file_path = 'uploads/exams/'.$file_name;
        }

        if($existing){ // update rejected or pending
            $stmt = $conn->prepare("UPDATE teacher_exams SET instructions=?, question_text=?, file_path=?, status='PENDING', updated_at=NOW() 
                WHERE id=?");
            $stmt->execute([$instructions, $question_text, $file_path, $existing['id']]);
            $message = "Your exam questions have been updated and resubmitted.";
        } else { // insert new
            $stmt = $conn->prepare("INSERT INTO teacher_exams 
                (teacher_id, session_id, term_id, class_id, subject_id, instructions, question_text, file_path, status, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'PENDING', NOW())");
            $stmt->execute([$account_id, $active_session['id'], $term['id'], $class_id, $subject_id, $instructions, $question_text, $file_path]);
            $message = "Exam questions uploaded successfully and pending admin approval!";
        }
    }
}

// Optionally, handle delete (example using GET)
if(isset($_GET['delete_id'])){
    $del_id = intval($_GET['delete_id']);
    $stmt = $conn->prepare("DELETE FROM teacher_exams WHERE id=? AND teacher_id=?");
    $stmt->execute([$del_id, $account_id]);
    $message = "Submission deleted successfully.";
}
?>
