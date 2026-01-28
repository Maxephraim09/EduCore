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

// ==============================
// ✅ Load Current Academic Session
// ==============================
$current_session = $_SESSION['current_session_id'] ?? null;

try {
    $conn = new PDO(
        "mysql:host=" . DBHost . ";dbname=" . DBName . ";charset=" . DBCharset,
        DBUser,
        DBPass
    );
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // If session is not set, fetch active one from DB
    if (!$current_session) {
        $stmt = $conn->query("SELECT id FROM tbl_sessions WHERE is_active = 1 LIMIT 1");
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $current_session = $row['id'];
            $_SESSION['current_session_id'] = $current_session;
        } else {
            die('No active academic session found.');
        }
    }

} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}

// ==============================
// ✅ Handle POST Request
// ==============================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $class_id   = trim($_POST['class'] ?? '');
    $day        = trim($_POST['day'] ?? '');
    $start_time = trim($_POST['start_time'] ?? '');
    $end_time   = trim($_POST['end_time'] ?? '');
    $subject    = trim($_POST['subject'] ?? '');

    // Validation
    if (empty($class_id) || empty($day) || empty($start_time) || empty($end_time) || empty($subject)) {
        echo "<script>alert('All fields are required.'); window.history.back();</script>";
        exit;
    }

    try {
        // ==============================
        // ✅ Insert into tbl_timetable
        // ==============================
        $stmt = $conn->prepare("
            INSERT INTO tbl_timetable (day, start_time, end_time, class_id, subject, session_id)
            VALUES (:day, :start_time, :end_time, :class_id, :subject, :session_id)
        ");

        $stmt->execute([
            ':day' => $day,
            ':start_time' => $start_time,
            ':end_time' => $end_time,
            ':class_id' => $class_id,
            ':subject' => $subject,
            ':session_id' => $current_session
        ]);

        echo "<script>alert('Lesson added successfully!'); window.location.href='../../academic/lession_timetable';</script>";
        exit;
    } catch (PDOException $e) {
        echo "<script>alert('Database Error: " . addslashes($e->getMessage()) . "'); window.history.back();</script>";
        exit;
    }
} else {
    header("location:../../academic/lession__timetable");
    exit;
}
?>
