<?php
chdir('../');
session_start();
require_once('db/config.php');
require_once('const/school.php');
require_once('const/check_session.php');

// Only allow students (level 3)
if (!($res == "1" && $level == "3")) {
    header("location:../");
    exit;
}
$enquiries = [];

// Default values
$page_title = "View Enquiries";
$site_name = "Site Name";
$school_logo = "images/default-logo.png"; // fallback logo
$favicon = "images/icon.png"; // fallback favicon
$current_session = "";

try {
    $conn = new PDO('mysql:host='.DBHost.';dbname='.DBName.';charset='.DBCharset, DBUser, DBPass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $conn->prepare("SELECT * FROM teacher_enquiries WHERE teacher_id = ? ORDER BY created_at DESC");
    $stmt->execute([$account_id]);
    $enquiries = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
        <img src="<?php echo htmlspecialchars($school_logo); ?>" alt="Logo" height="40">MGTechs
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

<div class="app-sidebar__overlay" data-toggle="sidebar"></div>
<aside class="app-sidebar">
<div class="app-sidebar__user">
<div>
<p class="app-sidebar__user-name"><?php echo $fname.' '.$lname; ?></p>
<p class="app-sidebar__user-designation">Student</p>
</div>
</div>
<ul class="app-menu">
<li><a class="app-menu__item active" href="student"><i class="app-menu__icon feather icon-monitor"></i><span class="app-menu__label">Dashboard</span></a></li>
<li><a class="app-menu__item" href="student/view"><i class="app-menu__icon feather icon-user"></i><span class="app-menu__label">My Profile</span></a></li>
<li><a class="app-menu__item" href="student/subjects"><i class="app-menu__icon feather icon-book-open"></i><span class="app-menu__label">My Subjects</span></a></li>
<li><a class="app-menu__item" href="student/#"><i class="app-menu__icon feather icon-file-text"></i><span class="app-menu__label">My Lession TimeTable</span></a></li>
<li><a class="app-menu__item" href="student/#"><i class="app-menu__icon feather icon-file-text"></i><span class="app-menu__label">Academic Calandar</span></a></li>
<li><a class="app-menu__item" href="student/#"><i class="app-menu__icon feather icon-file-text"></i><span class="app-menu__label">My Assignment</span></a></li>
<li><a class="app-menu__item" href="student/submit_enquiry"><i class="app-menu__icon feather icon-user"></i><span class="app-menu__label">Enquiries</span></a></li>
<li><a class="app-menu__item" href="student/#"><i class="app-menu__icon feather icon-file-text"></i><span class="app-menu__label">CBT </span></a></li>

<li><a class="app-menu__item" href="student/results"><i class="app-menu__icon feather icon-file-text"></i><span class="app-menu__label">My Results</span></a></li>
<li><a class="app-menu__item" href="student/grading-system"><i class="app-menu__icon feather icon-award"></i><span class="app-menu__label">Grading System</span></a></li>
<li><a class="app-menu__item" href="student/division-system"><i class="app-menu__icon feather icon-layers"></i><span class="app-menu__label">Division System</span></a></li>
<li><a class="app-menu__item" href="student/pay_fees"><i class="app-menu__icon feather icon-user"></i><span class="app-menu__label">Pay Fees</span></a></li>

</ul>
</aside>


<main class="app-content">
<div class="app-title">
<div>
<h1>Dashboard - <?php echo htmlspecialchars($current_session); ?> Session</h1>
</div>
<div><a href="student/view_enquiries"><button class="btn btn-primary">Submit Enquiry</button></a></div>
</div>

<div class="row">
<div class="col-md-12">
<div class="tile">
<div class="tile-body">
<div class="table-responsive">

<?php if(isset($error)) { ?>
<div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
<?php } ?>

<table class="table table-bordered table-striped" id="enquiriesTable">
    <thead>
        <tr>
            <th>#</th>
            <th>Title</th>
            <th>Category</th>
            <th>Status</th>
            <th>Date Submitted</th>
            <th>Admin Reply</th>
        </tr>
    </thead>
    <tbody>
    <?php
    if(!empty($enquiries)){
        $i = 1;
        foreach($enquiries as $enquiry){
            $status = htmlspecialchars($enquiry['status']);
            $reply = htmlspecialchars($enquiry['reply_message']);
            echo "<tr>
                    <td>{$i}</td>
                    <td>".htmlspecialchars($enquiry['title'])."</td>
                    <td>".htmlspecialchars($enquiry['category'])."</td>
                    <td>{$status}</td>
                    <td>".date('d-m-Y', strtotime($enquiry['created_at']))."</td>
                    <td>";
            if(!empty($reply)){
                echo "<button class='btn btn-sm btn-info view-reply' data-reply='".htmlspecialchars($reply, ENT_QUOTES)."'>View Reply</button>";
            } else {
                echo "-";
            }
            echo "</td>
                  </tr>";
            $i++;
        }
    } else {
        echo '<tr><td colspan="6" class="text-center">No enquiries found.</td></tr>';
    }
    ?>
    </tbody>
</table>

</div>
</div>
</div>
</div>
</div>

</main>

<script src="js/jquery-3.7.0.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/main.js"></script>
<script src="js/plugins/jquery.dataTables.min.js"></script>
<script src="js/plugins/dataTables.bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
<script>
$('#enquiriesTable').DataTable({
    "order": [[0, "desc"]],
    "pageLength": 10
});

// SweetAlert2 popup for admin reply
$(document).on('click', '.view-reply', function(){
    var reply = $(this).data('reply');
    Swal.fire({
        title: 'Admin Reply',
        html: reply,
        icon: 'info',
        confirmButtonText: 'Close'
    });
});
</script>

</body>
</html>
