<?php
session_start();
require_once('../../db/config.php');
require_once('../../const/check_session.php');

// Only allow teachers
if (!($res == "1" && $level == "2")) {
    header("location:../../");
    exit;
}

// Teacher ID from check_session.php
$teacher_id = $account_id;

if (isset($_POST['submit'])) {

    // Prepare variables
    $sender_name = $_POST['sender_name'];
    $title       = $_POST['title'];
    $category    = $_POST['category'];
    $message     = $_POST['message'];
    $alt_contact = $_POST['alt_contact'];

    $file_path = NULL;
    if (!empty($_FILES['file']['name'])) {
        $upload_dir = '../../uploads/enquiries/';
        if (!is_dir($upload_dir)) { mkdir($upload_dir, 0777, true); }

        $file_name = time().'_'.basename($_FILES['file']['name']);
        $target_file = $upload_dir . $file_name;

        if (move_uploaded_file($_FILES['file']['tmp_name'], $target_file)) {
            $file_path = 'uploads/enquiries/' . $file_name;
        }
    }

    // Use prepared statement to prevent SQL injection
    $sql = "INSERT INTO teacher_enquiries 
            (teacher_id, sender_name, title, category, message, file_path, alt_contact, status)
            VALUES (:teacher_id, :sender_name, :title, :category, :message, :file_path, :alt_contact, 'Pending')";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':teacher_id', $teacher_id, PDO::PARAM_INT);
    $stmt->bindParam(':sender_name', $sender_name, PDO::PARAM_STR);
    $stmt->bindParam(':title', $title, PDO::PARAM_STR);
    $stmt->bindParam(':category', $category, PDO::PARAM_STR);
    $stmt->bindParam(':message', $message, PDO::PARAM_STR);
    $stmt->bindParam(':file_path', $file_path, PDO::PARAM_STR);
    $stmt->bindParam(':alt_contact', $alt_contact, PDO::PARAM_STR);

    if ($stmt->execute()) {
        $_SESSION['enquiry_msg'] = "Your enquiry has been successfully submitted!";
    } else {
        $errorInfo = $stmt->errorInfo();
        $_SESSION['enquiry_msg'] = "Error submitting enquiry: " . $errorInfo[2];
    }

    header("Location: ../submit_enquiry");
    exit;
}
