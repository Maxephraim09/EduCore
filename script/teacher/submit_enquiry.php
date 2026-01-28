<?php
chdir('../');
session_start();
require_once('db/config.php');
require_once('const/school.php');
require_once('const/check_session.php');

// Only allow teachers (level 2)
if (!($res == "1" && $level == "2")) {
    header("location:../");
    exit;
}


// Default values
$page_title = "Submit Enquiries";
$site_name = "Site Name";
$school_logo = "images/default-logo.png"; // fallback logo
$favicon = "images/icon.png"; // fallback favicon
$current_session = "";

$message = '';
if(isset($_SESSION['enquiry_msg'])){
    $message = $_SESSION['enquiry_msg'];
    unset($_SESSION['enquiry_msg']);
}

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


    // Get active session
    $stmt = $conn->prepare("SELECT * FROM tbl_sessions WHERE is_active = 1 LIMIT 1");
    $stmt->execute();
    $active_session = $stmt->fetch(PDO::FETCH_ASSOC);


    $stmt = $conn->prepare("SELECT * FROM teacher_enquiries WHERE teacher_id = ? ORDER BY created_at DESC");
    $stmt->execute([$account_id]);
    $enquiries = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch(PDOException $e) {
    $error = "Connection failed: " . $e->getMessage();
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
        <h1><?php echo htmlspecialchars($page_title); ?> - <?php echo isset($active_session['session_name']) ? htmlspecialchars($active_session['session_name']) : 'No Active Session'; ?></h1>
    </div>
     <div>
    <a href="teacher/view_enquiries"><button class="btn btn-primary app_btn">Submit</button></a>
</div>
  </div>


<div class="row">
<div class="col-md-6 center_form">
<div class="tile">
<div class="tile-body">
<div class="table-responsive">
<h3 class="tile-title mb-3">Send a New Enquiry</h3>

<?php if($message): ?>
<div class="alert alert-info"><?php echo htmlspecialchars($message); ?></div>
<?php endif; ?>

<form class="app_frm" method="POST" enctype="multipart/form-data" action="teacher/core/submit_enquiry.php" autocomplete="off">
    <div class="mb-2">
        <label class="form-label">Sender Name</label>
        <input type="text" class="form-control" name="sender_name" value="<?php echo htmlspecialchars($fname.' '.$lname); ?>" readonly>
    </div>

    <div class="mb-2">
        <label class="form-label">Title</label>
        <input type="text" class="form-control" name="title" required placeholder="Enter title">
    </div>

    <div class="mb-2">
        <label class="form-label">Category</label>
        <select class="form-control select2" name="category" required>
            <option value="">-- Select Category --</option>
            <option>Complain</option>
            <option>Technical Issue</option>
            <option>Payment Issue</option>
            <option>Other</option>
        </select>
    </div>

    <div class="mb-2">
        <label class="form-label">Message</label>
        <textarea id="summernote" name="message" required></textarea>
    </div>

    <div class="mb-2">
        <label class="form-label">Upload File (Optional)</label>
        <input type="file" class="form-control" name="file">
    </div>

    <div class="mb-3">
        <label class="form-label">Alternative Contact (Email or Phone)</label>
        <input type="text" class="form-control" name="alt_contact" placeholder="Enter email or phone number">
    </div>

    <button type="submit" name="submit" value="1" class="btn btn-primary app_btn">Submit Enquiry</button>
</form>

</div>
</div>
</div>
</div>
</div>
</main>

<script src="js/jquery-3.7.0.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/main.js"></script>
<script src="loader/waitMe.js"></script>
<script src="js/sweetalert2@11.js"></script>
<script src="js/forms.js"></script>
<script src="select2/dist/js/select2.full.min.js"></script>
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<!-- Bootstrap Bundle (with Popper) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.full.min.js"></script>
<!-- Summernote JS -->
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
<!-- Your Custom JS -->
<script>
$(document).ready(function() {
    $('.select2').select2();

    $('#summernote').summernote({
        height: 200,
        placeholder: 'Type your enquiry message here...'
    });
});
</script>

</body>
</html>
