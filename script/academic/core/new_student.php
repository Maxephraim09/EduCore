<?php
chdir('../../');
session_start();
require_once('db/config.php');
require_once('const/check_session.php');

if (!($res == "1" && $level == "1")) {
    header("location:../../");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Collect and sanitize input
    $regno  = trim($_POST['regno']);
    $fname  = trim($_POST['fname']);
    $mname  = trim($_POST['mname']);
    $lname  = trim($_POST['lname']);
    $gender = $_POST['gender'];
    $class  = $_POST['class'];
    $email  = trim($_POST['email']);
    $password = $_POST['password'];
    $cpassword = $_POST['cpassword'];

    // 1. Password match check
    if ($password !== $cpassword) {
        $_SESSION['error'] = "Passwords do not match!";
        header("location:../register_students.php");
        exit;
    }

    // 2. Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // 3. Handle image upload
    $imageName = "DEFAULT";
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed = ['jpg','jpeg','png'];
        $fileName = $_FILES['image']['name'];
        $fileTmp  = $_FILES['image']['tmp_name'];
        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if (in_array($ext, $allowed)) {
            $imageName = uniqid().'.'.$ext;
            move_uploaded_file($fileTmp, '../../images/students/'.$imageName);
        } else {
            $_SESSION['error'] = "Invalid image format. Only JPG, JPEG, PNG allowed.";
            header("location:../register_students.php");
            exit;
        }
    }

    try {
        $conn = new PDO("mysql:host=".DBHost.";dbname=".DBName.";charset=".DBCharset, DBUser, DBPass);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // 4. Check if registration number or email exists
        $stmt = $conn->prepare("SELECT COUNT(*) FROM tbl_students WHERE regno=? OR email=?");
        $stmt->execute([$regno, $email]);
        if ($stmt->fetchColumn() > 0) {
            $_SESSION['error'] = "Registration number or email already exists!";
            header("location:../register_students.php");
            exit;
        }

        // 5. Insert student
        $stmt = $conn->prepare("INSERT INTO tbl_students (regno,fname,mname,lname,gender,class,email,password,image) VALUES (?,?,?,?,?,?,?,?,?)");
        $stmt->execute([$regno,$fname,$mname,$lname,$gender,$class,$email,$hashedPassword,$imageName]);

        $_SESSION['success'] = "Student registered successfully!";
        header("location:../manage_students.php");
        exit;

    } catch(PDOException $e) {
        $_SESSION['error'] = "Database error: " . $e->getMessage();
        header("location:../students.php");
        exit;
    }
} else {
    header("location:../register_students.php");
    exit;
}
