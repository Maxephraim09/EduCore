<?php
chdir('../');
session_start();
require_once('db/config.php');
require_once('const/school.php');
require_once('const/check_session.php');

if ($res != "1" || $level != "2") {
    header("location:../");
    exit;
}

// Get student ID from GET parameter
$student_id = isset($_GET['id']) ? $_GET['id'] : '';

if(!$student_id){
    die("Invalid student ID.");
}

try {
    $conn = new PDO('mysql:host='.DBHost.';dbname='.DBName.';charset='.DBCharset, DBUser, DBPass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch student details
    $stmt = $conn->prepare("SELECT s.*, c.name AS class_name, se.session_name 
                            FROM tbl_students s
                            LEFT JOIN tbl_classes c ON s.class = c.id
                            LEFT JOIN tbl_sessions se ON s.session_id = se.id
                            WHERE s.id = ?");
    $stmt->execute([$student_id]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);

    if(!$student){
        die("Student not found.");
    }

    // Fetch subjects assigned to this student for the session
    $stmt = $conn->prepare("
        SELECT sub.name 
        FROM tbl_student_subjects ss
        JOIN tbl_subjects sub ON ss.subject_id = sub.id
        WHERE ss.student_id = ? AND ss.session_id = ?
    ");
    $stmt->execute([$student_id, $student['session_id']]);
    $subjects = $stmt->fetchAll(PDO::FETCH_COLUMN);

} catch(PDOException $e){
    die("Database error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>SRMS - List Students</title>
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<base href="../">
<link rel="stylesheet" href="css/main.css">
<link rel="icon" href="images/icon.ico">
<link rel="stylesheet" href="cdn.jsdelivr.net/npm/bootstrap-icons%401.10.5/font/bootstrap-icons.css">
<link rel="stylesheet" href="cdn.datatables.net/v/bs5/dt-1.13.4/datatables.min.css">
<link rel="stylesheet" href="loader/waitMe.css">
<link rel="stylesheet" href="select2/dist/css/select2.min.css">
</head>
<body class="app sidebar-mini">

<header class="app-header">
    <a class="app-header__logo" href="javascript:void(0);">SRMS</a>
    <a class="app-sidebar__toggle" href="#" data-toggle="sidebar" aria-label="Hide Sidebar"></a>
    <ul class="app-nav">
        <li class="dropdown">
            <a class="app-nav__item" href="#" data-bs-toggle="dropdown" aria-label="Open Profile Menu">
                <i class="bi bi-person fs-4"></i>
            </a>
            <ul class="dropdown-menu settings-menu dropdown-menu-right">
                <li><a class="dropdown-item" href="teacher/profile"><i class="bi bi-person me-2 fs-5"></i> Profile</a></li>
                <li><a class="dropdown-item" href="logout"><i class="bi bi-box-arrow-right me-2 fs-5"></i> Logout</a></li>
            </ul>
        </li>
    </ul>
</header>

<div class="app-sidebar__overlay" data-toggle="sidebar"></div>
<aside class="app-sidebar">
    <div class="app-sidebar__user">
        <div>
            <p class="app-sidebar__user-name"><?php echo $fname.' '.$lname; ?></p>
            <p class="app-sidebar__user-designation">Teacher</p>
        </div>
    </div>
    <ul class="app-menu">
        <li><a class="app-menu__item" href="teacher"><i class="app-menu__icon feather icon-monitor"></i><span class="app-menu__label">Dashboard</span></a></li>
        <li><a class="app-menu__item" href="teacher/terms"><i class="app-menu__icon feather icon-folder"></i><span class="app-menu__label">Academic Terms</span></a></li>
        <li><a class="app-menu__item" href="teacher/combinations"><i class="app-menu__icon feather icon-book-open"></i><span class="app-menu__label">Subject Combinations</span></a></li>
        <li class="treeview is-expanded">
            <a class="app-menu__item" href="javascript:void(0);" data-toggle="treeview"><i class="app-menu__icon feather icon-users"></i><span class="app-menu__label">Students</span><i class="treeview-indicator bi bi-chevron-right"></i></a>
            <ul class="treeview-menu">
                <li><a class="treeview-item active" href="teacher/list_students"><i class="icon bi bi-circle-fill"></i> List Students</a></li>
                <li><a class="treeview-item" href="teacher/export_students"><i class="icon bi bi-circle-fill"></i> Export Students</a></li>
            </ul>
        </li>
        <li class="treeview">
            <a class="app-menu__item" href="javascript:void(0);" data-toggle="treeview"><i class="app-menu__icon feather icon-file-text"></i><span class="app-menu__label">Examination Results</span><i class="treeview-indicator bi bi-chevron-right"></i></a>
            <ul class="treeview-menu">
                <li><a class="treeview-item" href="teacher/import_results"><i class="icon bi bi-circle-fill"></i> Import Results</a></li>
                <li><a class="treeview-item" href="teacher/manage_results"><i class="icon bi bi-circle-fill"></i> View Results</a></li>
            </ul>
        </li>
        <li><a class="app-menu__item" href="teacher/grading-system"><i class="app-menu__icon feather icon-award"></i><span class="app-menu__label">Grading System</span></a></li>
        <li><a class="app-menu__item" href="teacher/division-system"><i class="app-menu__icon feather icon-layers"></i><span class="app-menu__label">Division System</span></a></li>
    </ul>
</aside>

<main class="app-content">
<div class="app-title">
    <h1>Student Details - <?php echo $student['fname'].' '.$student['lname']; ?></h1>
</div>

<div class="row">
<div class="col-md-8">
<div class="tile">
<div class="tile-body">
    <h4>Basic Information</h4>
    <table class="table table-bordered">
        <tr><th>Student ID</th><td><?php echo htmlspecialchars($student['id']); ?></td></tr>
        <tr><th>Full Name</th><td><?php echo htmlspecialchars($student['fname'].' '.($student['mname'] ? $student['mname'].' ' : '').$student['lname']); ?></td></tr>
        <tr><th>Gender</th><td><?php echo htmlspecialchars($student['gender']); ?></td></tr>
        <tr><th>Email</th><td><?php echo htmlspecialchars($student['email']); ?></td></tr>
        <tr><th>Class</th><td><?php echo htmlspecialchars($student['class_name']); ?></td></tr>
        <tr><th>Session</th><td><?php echo htmlspecialchars($student['session_name']); ?></td></tr>
        <tr><th>Status</th><td><?php echo $student['status'] == 1 ? 'Enabled' : 'Disabled'; ?></td></tr>
    </table>

    <h4>Subjects</h4>
    <?php if(count($subjects) > 0): ?>
        <ul>
            <?php foreach($subjects as $sub): ?>
                <li><?php echo htmlspecialchars($sub); ?></li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>No subjects assigned for this session.</p>
    <?php endif; ?>

    <a href="teacher/list_students" class="btn btn-secondary mt-3">Back to List</a>
</div>
</div>
</div>
</div>

</main>

<script src="js/jquery-3.7.0.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="select2/dist/js/select2.full.min.js"></script>
</body>
</html>
