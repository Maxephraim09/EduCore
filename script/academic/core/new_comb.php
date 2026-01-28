<?php
chdir('../../');
session_start();
require_once('db/config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $subject = $_POST['subject'];
    $teacher = $_POST['teacher'];
    $reg_date = date('Y-m-d G:i:s');

    try {
        $conn = new PDO('mysql:host='.DBHost.';dbname='.DBName.';charset='.DBCharset.'', DBUser, DBPass);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Loop through each selected class
        foreach ($_POST['class'] as $class_id) {
            // Check if this combination already exists
            $check = $conn->prepare("SELECT * FROM tbl_subject_combinations WHERE subject_id=? AND class_id=?");
            $check->execute([$subject, $class_id]);

            if ($check->rowCount() == 0) {
                // Insert the combination
                $stmt = $conn->prepare("INSERT INTO tbl_subject_combinations (class_id, subject_id, teacher_id, reg_date) VALUES (?,?,?,?)");
                $stmt->execute([$class_id, $subject, $teacher, $reg_date]);
            }
        }

        $_SESSION['reply'] = array(array("success", "Subject combinations created successfully"));
        header("location:../combinations");
        exit();

    } catch (PDOException $e) {
        echo "Connection failed: " . $e->getMessage();
    }

} else {
    header("location:../");
}
?>
