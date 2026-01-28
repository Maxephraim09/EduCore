<?php
chdir('../../');
session_start();
require_once('db/config.php');
require_once('const/school.php'); 
require_once('const/check_session.php');

// ==============================
// ✅ Restrict Access to Admins
// ==============================
if (!($res == "1" && $level == "1")) {
    header("location:../../");
    exit;
}


try {
    $conn = new PDO("mysql:host=".DBHost.";dbname=".DBName.";charset=".DBCharset, DBUser, DBPass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // ADD
    if (isset($_POST['add_calendar'])) {
        $session_id = $_POST['session_id'];
        $activity = trim($_POST['activity']);
        $start_date = $_POST['start_date'];
        $end_date = $_POST['end_date'];

        if (empty($activity) || empty($start_date) || empty($end_date)) {
            echo "<script>alert('All fields are required.'); window.history.back();</script>";
            exit;
        }

        $days = (strtotime($end_date) - strtotime($start_date)) / (60*60*24) + 1;

        $stmt = $conn->prepare("INSERT INTO tbl_academic_calendar (session_id, activity, start_date, end_date, num_days)
                                VALUES (:session_id, :activity, :start_date, :end_date, :num_days)");
        $stmt->execute([
            ':session_id' => $session_id,
            ':activity' => $activity,
            ':start_date' => $start_date,
            ':end_date' => $end_date,
            ':num_days' => $days
        ]);
        echo "<script>alert('Activity added successfully!'); window.location.href='../../academic/academic_calendar';</script>";
        exit;
    }

    // UPDATE
    if (isset($_POST['update_calendar'])) {
        $id = $_POST['id'];
        $activity = trim($_POST['activity']);
        $start_date = $_POST['start_date'];
        $end_date = $_POST['end_date'];
        $days = (strtotime($end_date) - strtotime($start_date)) / (60*60*24) + 1;

        $stmt = $conn->prepare("UPDATE tbl_academic_calendar 
                                SET activity=:activity, start_date=:start_date, end_date=:end_date, num_days=:num_days 
                                WHERE id=:id");
        $stmt->execute([
            ':activity' => $activity,
            ':start_date' => $start_date,
            ':end_date' => $end_date,
            ':num_days' => $days,
            ':id' => $id
        ]);
        echo "<script>alert('Activity updated successfully!'); window.location.href='../../academic/academic_calendar';</script>";
        exit;
    }

    // DELETE
    if (isset($_GET['delete'])) {
        $id = $_GET['delete'];
        $conn->prepare("DELETE FROM tbl_academic_calendar WHERE id=:id")->execute([':id'=>$id]);
        echo "<script>alert('Activity deleted successfully.'); window.location.href='../../academic/academic-calendar';</script>";
        exit;
    }

} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}
?>
