<?php
chdir('../');
session_start();
require_once('db/config.php');
require_once('const/school.php');
require_once('const/check_session.php');
require_once('const/teacher_dashboard.php');
if ($res == "1" && $level == "2") {}else{header("location:../");}

// Default values
$page_title = "Teacher Dashboard";
$site_name = "Site Name";
$school_logo = "images/default-logo.png"; // fallback logo
$favicon = "images/icon.png"; // fallback favicon
$current_session = "";

try {
    $conn = new PDO('mysql:host='.DBHost.';dbname='.DBName.';charset='.DBCharset, DBUser, DBPass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch school logo from tbl_school
    $stmt = $conn->prepare("SELECT logo FROM tbl_school LIMIT 1");
    $stmt->execute();
    $logo_file = $stmt->fetchColumn();
    if ($logo_file) {
        $school_logo = "images/logo/" . $logo_file;
        $favicon = $school_logo; // use logo as favicon if you like
    }

        // Fetch site name
    $stmt = $conn->prepare("SELECT site_name FROM tbl_site_settings LIMIT 1");
    $stmt->execute();
    $site_name = $stmt->fetchColumn() ?: "Site Name";

    // Fetch current active session
    $stmt = $conn->prepare("SELECT session_name FROM tbl_sessions WHERE is_active = 1 LIMIT 1");
    $stmt->execute();
    $current_session = $stmt->fetchColumn() ?: "No Active Session";

    // Subjects count
    $stmt = $conn->prepare("SELECT COUNT(DISTINCT subject_id) FROM tbl_subject_combinations WHERE teacher_id = ?");
    $stmt->execute([$account_id]);
    $my_subject = $stmt->fetchColumn();

    // Classes count
    $stmt = $conn->prepare("SELECT COUNT(DISTINCT class_id) FROM tbl_subject_combinations WHERE teacher_id = ?");
    $stmt->execute([$account_id]);
    $my_class = $stmt->fetchColumn();

    // Students count
    $stmt = $conn->prepare("
        SELECT COUNT(s.id)
        FROM tbl_students s
        INNER JOIN tbl_subject_combinations sc
            ON s.`class` = sc.class_id
        WHERE sc.teacher_id = ? AND s.status = 1
    ");
    $stmt->execute([$account_id]);
    $my_students = $stmt->fetchColumn();

} catch(PDOException $e) {
    $my_subject = $my_class = $my_students = 0;
    error_log("Teacher Dashboard Error: " . $e->getMessage());
}

?>

<!DOCTYPE html>
<html lang="en">
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

<!-- SEO -->
<title><?php echo htmlspecialchars($site_name); ?> - <?php echo htmlspecialchars($page_title); ?></title>
<meta name="description" content="<?php echo htmlspecialchars($site_name); ?> Dashboard">
<meta name="keywords" content="dashboard, <?php echo htmlspecialchars($site_name); ?>, admin, management">
<meta name="author" content="MGTechs">
<meta name="robots" content="noindex, nofollow"> <!-- Use noindex for internal dashboards -->
<meta name="language" content="English">
<meta name="revisit-after" content="1 days">
<meta name="distribution" content="global">
<meta name="rating" content="general">

<!-- Social Sharing / Open Graph -->
<meta property="og:title" content="<?php echo htmlspecialchars($site_name); ?> Dashboard">
<meta property="og:description" content="Manage your tasks, students, classes, and settings in <?php echo htmlspecialchars($site_name); ?>">
<meta property="og:type" content="website">
<meta property="og:url" content="<?php echo $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']; ?>">
<meta property="og:image" content="images/logo.png">

<!-- Twitter Card -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?php echo htmlspecialchars($site_name); ?> Dashboard">
<meta name="twitter:description" content="Manage your tasks, students, classes, and settings in <?php echo htmlspecialchars($site_name); ?>">
<meta name="twitter:image" content="images/logo.png">

<!-- Caching & Security -->
<meta http-equiv="cache-control" content="no-cache, no-store, must-revalidate">
<meta http-equiv="expires" content="0">
<meta http-equiv="pragma" content="no-cache">
<meta http-equiv="Content-Security-Policy" content="default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline'">

<!-- Favicon -->
<link rel="icon" href="<?php echo htmlspecialchars($favicon); ?>" type="image/x-icon">

<base href="../">
<link rel="stylesheet" type="text/css" href="css/main.css">
<link rel="icon" href="images/icon.ico">
<link rel="stylesheet" type="text/css" href="cdn.jsdelivr.net/npm/bootstrap-icons%401.10.5/font/bootstrap-icons.css">
<link type="text/css" rel="stylesheet" href="loader/waitMe.css">
</head>
<body class="app sidebar-mini">

<header class="app-header">
    <a class="app-header__logo" href="javascript:void(0);">
        <img src="<?php echo htmlspecialchars($school_logo); ?>" alt="Logo" height="40">
    </a>
<a class="app-sidebar__toggle" href="#" data-toggle="sidebar" aria-label="Hide Sidebar"></a>

<ul class="app-nav">

<li class="dropdown"><a class="app-nav__item" href="#" data-bs-toggle="dropdown" aria-label="Open Profile Menu"><i class="bi bi-person fs-4"></i></a>
<ul class="dropdown-menu settings-menu dropdown-menu-right">
<li><a class="dropdown-item" href="teacher/profile"><i class="bi bi-person me-2 fs-5"></i> Profile</a></li>
<li><a class="dropdown-item" href="logout"><i class="bi bi-box-arrow-right me-2 fs-5"></i> Logout</a></li>
</ul>
</li>
</ul>
</header>

<!--=========== SIDEBAR START =============-->
<div class="app-sidebar__overlay" data-toggle="sidebar"></div>
<aside class="app-sidebar">
<div class="app-sidebar__user">
<div>
<p class="app-sidebar__user-name"><?php echo $fname.' '.$lname; ?></p>
<p class="app-sidebar__user-designation">Teacher</p>
</div>
</div>

<ul class="app-menu">
<li><a class="app-menu__item active" href="teacher"><i class="app-menu__icon feather icon-monitor"></i><span class="app-menu__label">Dashboard</span></a></li>

<li class="treeview"><a class="app-menu__item" href="javascript:void(0);" data-toggle="treeview"><i class="app-menu__icon feather icon-users"></i><span class="app-menu__label">Academics</span><i class="treeview-indicator bi bi-chevron-right"></i></a>
<ul class="treeview-menu">
<li><a class="app-menu__item" href="teacher/terms"><i class="app-menu__icon feather icon-folder"></i><span class="app-menu__label">Academic Terms</span></a></li>
<li><a class="app-menu__item" href="teacher/my_timetable"><i class="app-menu__icon feather icon-book-open"></i><span class="app-menu__label">My TimeTable</span></a></li>
<li><a class="app-menu__item" href="teacher/academic_calendar"><i class="app-menu__icon feather icon-book-open"></i><span class="app-menu__label">Academic calendar</span></a></li>
<li><a class="app-menu__item" href="teacher/combinations"><i class="app-menu__icon feather icon-book-open"></i><span class="app-menu__label">Duty Ruster</span></a></li>
<li><a class="app-menu__item" href="teacher/manage_exams_questions"><i class="app-menu__icon feather icon-book-open"></i><span class="app-menu__label">Exams Questions</span></a></li>
<li><a class="treeview-item" href="teacher/manage_results"><i class="icon bi bi-circle-fill"></i> Assignment</a></li>
<li><a class="app-menu__item" href="teacher/combinations"><i class="app-menu__icon feather icon-book-open"></i><span class="app-menu__label">My Subject</span></a></li>
</ul>
</li>

<li class="treeview"><a class="app-menu__item" href="javascript:void(0);" data-toggle="treeview"><i class="app-menu__icon feather icon-users"></i><span class="app-menu__label">Students</span><i class="treeview-indicator bi bi-chevron-right"></i></a>
<ul class="treeview-menu">
<li><a class="treeview-item" href="teacher/list_students"><i class="icon bi bi-circle-fill"></i> List Students</a></li>
<li><a class="treeview-item" href="teacher/export_students"><i class="icon bi bi-circle-fill"></i> Export Students</a></li>
</ul>
</li>

<li class="treeview"><a class="app-menu__item" href="javascript:void(0);" data-toggle="treeview"><i class="app-menu__icon feather icon-file-text"></i><span class="app-menu__label">Loan & Leave App</span><i class="treeview-indicator bi bi-chevron-right"></i></a>
<ul class="treeview-menu">
<li><a class="treeview-item" href="teacher/loan_application"><i class="icon bi bi-circle-fill"></i> Loan Application</a></li>
<li><a class="treeview-item" href="teacher/view_my_loans"><i class="icon bi bi-circle-fill"></i> View My Loans</a></li>
<li><a class="treeview-item" href="teacher/manage_leave"><i class="icon bi bi-circle-fill"></i> Leave Application</a></li>
</ul>
</li>

<li class="treeview"><a class="app-menu__item" href="javascript:void(0);" data-toggle="treeview"><i class="app-menu__icon feather icon-file-text"></i><span class="app-menu__label">Enqueries</span><i class="treeview-indicator bi bi-chevron-right"></i></a>
<ul class="treeview-menu">
<li><a class="treeview-item" href="teacher/submit_enquiry"><i class="icon bi bi-circle-fill"></i> Submit Enquery</a></li>
<li><a class="treeview-item" href="teacher/view_enquiries"><i class="icon bi bi-circle-fill"></i> View Enqueries</a></li>
</ul>
</li>

<li class="treeview"><a class="app-menu__item" href="javascript:void(0);" data-toggle="treeview"><i class="app-menu__icon feather icon-file-text"></i><span class="app-menu__label">Examination Results</span><i class="treeview-indicator bi bi-chevron-right"></i></a>
<ul class="treeview-menu">
<li><a class="treeview-item" href="teacher/import_results"><i class="icon bi bi-circle-fill"></i> Import Results</a></li>
<li><a class="treeview-item" href="teacher/manage_results"><i class="icon bi bi-circle-fill"></i> Score Sheet</a></li>
<li><a class="treeview-item" href="teacher/manage_results"><i class="icon bi bi-circle-fill"></i> Report Cards</a></li>
<li><a class="treeview-item" href="teacher/manage_results"><i class="icon bi bi-circle-fill"></i> Attendance</a></li>
</ul>
</li>

<li><a class="app-menu__item" href="teacher/grading-system"><i class="app-menu__icon feather icon-award"></i><span class="app-menu__label">CBT</span></a></li>
<li><a class="app-menu__item" href="teacher/grading-system"><i class="app-menu__icon feather icon-award"></i><span class="app-menu__label">Grading System</span></a></li>
<li><a class="app-menu__item" href="teacher/division-system"><i class="app-menu__icon feather icon-layers"></i><span class="app-menu__label">Division System</span></a></li>
<li><a class="app-menu__item" href="teacher/division-system"><i class="app-menu__icon feather icon-layers"></i><span class="app-menu__label">Download Logs</span></a></li>
</ul>

</aside>


<main class="app-content">
<div class="app-title">
<div>
<h1>Dashboard - <?php echo htmlspecialchars($current_session); ?> Session</h1>
</div>

</div>
<div class="row">
<div class="col-md-6 col-lg-4">
<div class="widget-small primary coloured-icon"><i class="icon feather icon-book-open fs-1"></i>
<div class="info">
<h4>Subjects</h4>
<p><b><?php echo number_format($my_subject); ?></b></p>
</div>
</div>
</div>
<div class="col-md-6 col-lg-4">
<div class="widget-small primary coloured-icon"><i class="icon feather icon-home fs-1"></i>
<div class="info">
<h4>Classes</h4>
<p><b><?php echo number_format($my_class); ?></b></p>
</div>
</div>
</div>
<div class="col-md-6 col-lg-4">
<div class="widget-small primary coloured-icon"><i class="icon feather icon-users fs-1"></i>
<div class="info">
<h4>Students</h4>
<p><b><?php echo number_format($my_students); ?></b></p>
</div>
</div>
</div>

</div>
<div class="row">
<div class="col-md-12">
<div class="tile">
<h4 class="tile-title">Announcements</h4>

<?php

try {
$conn = new PDO('mysql:host='.DBHost.';dbname='.DBName.';charset='.DBCharset.';collation='.DBCollation.';prefix='.DBPrefix.'', DBUser, DBPass);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$stmt = $conn->prepare("SELECT * FROM tbl_announcements WHERE level = '0' OR level = '2' ORDER BY id DESC");
$stmt->execute();
$result = $stmt->fetchAll();

if (count($result) < 1) {
?>
<div class="alert alert-dismissible alert-info">
<strong>There is no any announcements at the moment</strong>
</div>
<?php
}
foreach($result as $row)
{
?>
<div class="col-lg-12 mb-3">
<div class="bs-component">
<div class="list-group">
<a class="list-group-item list-group-item-action active"><?php echo $row[1]; ?></a>
<a class="list-group-item list-group-item-action"><?php echo $row[2]; ?></a>
<a class="list-group-item list-group-item-action disabled"><?php echo $row[3]; ?></a></div>
</div>
</div>
<?php
}

}catch(PDOException $e)
{
echo "Connection failed: " . $e->getMessage();
}

?>




</div>
</div>

</div>
</main>

<script src="js/jquery-3.7.0.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/main.js"></script>
<script src="loader/waitMe.js"></script>
<script src="js/forms.js"></script>
<script src="js/sweetalert2@11.js"></script>

</body>

</html>
