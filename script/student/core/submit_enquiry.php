<?php
session_start();
chdir('../../'); // FIXED: two levels up
require_once('db/config.php');
require_once('const/school.php');
require_once('const/check_session.php');

// Only allow students (level 3)
if (!($res == "1" && $level == "3")) {
    $_SESSION['enquiry_msg'] = "Session expired. Please login again.";
    header("location:../");
    exit;
}


// If student is not logged in properly
if (!isset($_SESSION['stdid'])) {
    $_SESSION['enquiry_msg'] = "Session expired. Please login again.";
    header("location:../submit_enquiry");
    exit;
}

$student_id = $_SESSION['stdid'];

// Handle form submission
if (isset($_POST['submit'])) {

    $title      = trim($_POST['title']);
    $category   = trim($_POST['category']);
    $message    = trim($_POST['message']);
    $alt_contact = trim($_POST['alt_contact']);

    // -------------------------------
    // 1. GET STUDENT CLASS TEACHER
    // -------------------------------

    $stmt = $conn->prepare("SELECT class, class_teacher FROM students WHERE id = ?");
    $stmt->execute([$student_id]);
    $stu = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$stu) {
        $_SESSION['enquiry_msg'] = "Unable to find your class information.";
        header("location:../submit_enquiry");
        exit;
    }

    $teacher_id = $stu['class_teacher'];

    if (empty($teacher_id)) {
        $_SESSION['enquiry_msg'] = "Your class has no assigned teacher. Contact admin!";
        header("location:../submit_enquiry");
        exit;
    }

    // -------------------------------
    // 2. FILE UPLOAD (OPTIONAL)
    // -------------------------------
    $file_name = NULL;

    if (!empty($_FILES['file']['name'])) {

        $target_dir = "uploads/enquiries/";

        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $file_tmp  = $_FILES['file']['tmp_name'];
        $original  = basename($_FILES['file']['name']);

        $file_name = time() . "_" . preg_replace("/[^A-Za-z0-9.\-]/", "_", $original);
        $file_path = $target_dir . $file_name;

        move_uploaded_file($file_tmp, $file_path);
    }

    // -------------------------------
    // 3. INSERT INTO enquiry TABLE
    // -------------------------------

    $sql = "INSERT INTO enquiries 
            (student_id, teacher_id, title, category, message, file, alt_contact, status, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, 'Pending', NOW())";

    $stmt = $conn->prepare($sql);

    $ok = $stmt->execute([
        $student_id,
        $teacher_id,
        $title,
        $category,
        $message,
        $file_name,
        $alt_contact
    ]);

    if ($ok) {
        $_SESSION['enquiry_msg'] = "Your enquiry has been submitted successfully.";
    } else {
        $_SESSION['enquiry_msg'] = "Error submitting enquiry. Please try again.";
    }

    header("location:../submit_enquiry");
    exit;
}

?>
