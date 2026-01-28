<?php
chdir('../../'); // Adjust if this file is inside academic/core/
session_start();
require_once('db/config.php');
require_once('const/school.php');
require_once('const/check_session.php');

// Only allow admin (level 1)
if (!($res == "1" && $level == "1")) {
    header("location:../../");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST['id']) || empty($_POST['reply_message'])) {
        die('Invalid request.');
    }

    $enquiry_id = intval($_POST['id']);
    $reply_message = trim($_POST['reply_message']);

    try {
        $conn = new PDO("mysql:host=".DBHost.";dbname=".DBName.";charset=".DBCharset, DBUser, DBPass);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Update the reply and status
        $stmt = $conn->prepare("UPDATE teacher_enquiries SET reply_message = :reply, status = 'Replied', replied_at = NOW() WHERE id = :id");
        $stmt->execute([
            ':reply' => $reply_message,
            ':id' => $enquiry_id
        ]);

        // Optional: send email to teacher here if you have their email in tbl_staff

        // Redirect back with success
        header("Location: ../../academic/view_enquiries?success=1");
        exit;

    } catch(PDOException $e) {
        echo "Error: ".$e->getMessage();
        exit;
    }
} else {
    header("Location: ../../academic/view_enquiries");
    exit;
}
