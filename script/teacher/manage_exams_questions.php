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
$page_title = "Exams Questions";
$site_name = "Site Name";
$school_logo = "images/default-logo.png"; // fallback logo
$favicon = "images/icon.png"; // fallback favicon
$current_session = "";

//$message = '';
//$error = '';

// Connect
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


    // Handle AJAX delete request
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
        $del_id = intval($_POST['id'] ?? 0);
        // Ensure the record belongs to this teacher
        $stmt = $conn->prepare("SELECT file_path FROM teacher_exams WHERE id = ? AND teacher_id = ? LIMIT 1");
        $stmt->execute([$del_id, $account_id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            // Attempt to delete file from disk if exists and path is inside uploads
            if (!empty($row['file_path'])) {
                $file_on_disk = __DIR__ . '/../' . ltrim($row['file_path'], '/');
                if (file_exists($file_on_disk) && is_file($file_on_disk)) {
                    @unlink($file_on_disk);
                }
            }
            $del = $conn->prepare("DELETE FROM teacher_exams WHERE id = ? AND teacher_id = ?");
            $del->execute([$del_id, $account_id]);
            echo json_encode(['success' => true, 'message' => 'Submission deleted successfully.']);
            exit;
        } else {
            echo json_encode(['success' => false, 'message' => 'Submission not found or access denied.']);
            exit;
        }
    }

    // Fetch teacher submissions
    $stmt = $conn->prepare("
        SELECT 
    te.*, 
    s.session_name, 
    t.name AS term_name, 
    c.name AS class_name, 
    sub.name AS subject_name

        FROM teacher_exams te
        LEFT JOIN tbl_sessions s ON te.session_id = s.id
        LEFT JOIN tbl_terms t ON te.term_id = t.id
        LEFT JOIN tbl_classes c ON te.class_id = c.id
        LEFT JOIN tbl_subjects sub ON te.subject_id = sub.id
        WHERE te.teacher_id = ?
        ORDER BY te.created_at DESC
    ");
    $stmt->execute([$account_id]);
    $submissions = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $error = "Connection failed: " . $e->getMessage();
    $submissions = [];
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
    <a href="teacher/upload_exams_questions"><button>Submit</button></a>
</div>
  </div>

    <?php if(isset($error)) { ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php } ?>

    <div class="row">
    <div class="col-md-12">
      <div class="tile">
        <div class="tile-body">
          <div class="table-responsive">
            <h3 class="tile-title"><?php echo htmlspecialchars($page_title); ?></h3>
            <table class="table table-hover table-bordered" id="srmsTable">
    <thead>
        <tr>
            <th>#</th>
            <th>Session</th>
            <th>Term</th>
            <th>Class</th>
            <th>Subject</th>
            <th>Status</th>
            <th>Date Submitted</th>
            <th>File</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
<?php
if(!empty($submissions)){
    $i = 1;
    foreach($submissions as $s){
        $status = htmlspecialchars($s['status']);
        $file_path = htmlspecialchars($s['file_path']);
        $file_exists = !empty($s['file_path']) && file_exists(__DIR__ . '/../' . ltrim($s['file_path'], '/'));
        echo "<tr>
                <td>{$i}</td>
                <td>".htmlspecialchars($s['session_name'] ?: '-')."</td>
                <td>".htmlspecialchars($s['term_name'] ?: '-')."</td>
                <td>".htmlspecialchars($s['class_name'] ?: '-')."</td>
                <td>".htmlspecialchars($s['subject_name'] ?: '-')."</td>
                <td>".htmlspecialchars($status)."</td>
                <td>".date('d-m-Y', strtotime($s['created_at']))."</td>
                <td>";
        if($file_exists){
            $download_url = htmlspecialchars($s['file_path']);
            echo "<a class='btn btn-sm btn-success' href='{$download_url}' target='_blank'><i class='bi bi-download'></i> Download</a>";
        } else {
            echo "-";
        }
        echo "</td>
              <td>
                <button class='btn btn-sm btn-info view-submission' 
                    data-instructions='".htmlspecialchars($s['instructions'] ?? '', ENT_QUOTES)."' 
                    data-question='".htmlspecialchars($s['question_text'] ?? '', ENT_QUOTES)."'>
                    <i class='bi bi-eye'></i> View
                </button>
                <a class='btn btn-sm btn-primary' href='teacher/upload_exams_questions?id=".intval($s['id'])."'><i class='bi bi-pencil'></i> Edit</a>
                <button class='btn btn-sm btn-danger delete-submission' data-id='".intval($s['id'])."'><i class='bi bi-trash'></i> Delete</button>
              </td>
             </tr>";
        $i++;
    }
} else {
    // ***** FIXED ROW FOR DATATABLES *****
    echo "<tr>
            <td class='text-center'>-</td>
            <td class='text-center'>-</td>
            <td class='text-center'>-</td>
            <td class='text-center'>-</td>
            <td class='text-center'>-</td>
            <td class='text-center'>-</td>
            <td class='text-center'>-</td>
            <td class='text-center'>-</td>
            <td class='text-center'>No submissions found</td>
          </tr>";
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

<!-- View Modal (SweetAlert used for simplicity, but keep hidden container for graceful fallback) -->
<div id="submission-data" style="display:none;">
    <div id="instructions-content"></div>
    <div id="question-content" class="mt-2"></div>
</div>

<script src="js/jquery-3.7.0.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/main.js"></script>
<script src="loader/waitMe.js"></script>
<script src="js/sweetalert2@11.js"></script>
<script src="js/forms.js"></script>
<script src="select2/dist/js/select2.full.min.js"></script>
<script src="js/plugins/jquery.dataTables.min.js"></script>
<script src="js/plugins/dataTables.bootstrap5.min.js"></script>
<script type="text/javascript">$('#srmsTable').DataTable({"sort" : false});</script>
<?php require_once('const/check-reply.php'); ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

<script>
$('#examsTable').DataTable({
    "order": [[6, "desc"]],
    "pageLength": 10
});

// View submission (instructions + question text)
$(document).on('click', '.view-submission', function(){
    var instructions = $(this).data('instructions') || '';
    var question = $(this).data('question') || '';

    var html = '';
    if(instructions.trim() !== ''){
        html += '<h5>Instructions</h5><div style=\"text-align:left;\">' + nl2br(escapeHtml(instructions)) + '</div>';
    }
    if(question.trim() !== ''){
        html += '<h5 class=\"mt-3\">Question Text</h5><div style=\"text-align:left;\">' + nl2br(escapeHtml(question)) + '</div>';
    }
    if(html === ''){
        html = '<em>No instructions or question text provided.</em>';
    }

    Swal.fire({
        title: 'Submission Details',
        html: html,
        width: 800,
        icon: 'info',
        confirmButtonText: 'Close'
    });
});

// Delete with confirmation (AJAX POST)
$(document).on('click', '.delete-submission', function(){
    var id = $(this).data('id');
    Swal.fire({
        title: 'Delete Submission?',
        text: 'This will permanently delete the submission and any uploaded file.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if(result.isConfirmed){
            // send AJAX POST
            $.ajax({
                url: '',
                method: 'POST',
                dataType: 'json',
                data: { action: 'delete', id: id },
                success: function(resp){
                    if(resp && resp.success){
                        Swal.fire({ icon: 'success', title: 'Deleted', text: resp.message }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({ icon: 'error', title: 'Error', text: (resp && resp.message) ? resp.message : 'Unable to delete.' });
                    }
                },
                error: function(){
                    Swal.fire({ icon: 'error', title: 'Error', text: 'Server error. Please try again.' });
                }
            });
        }
    });
});

// helper functions
function escapeHtml(text) {
    return String(text)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

function nl2br(str) {
    return str.replace(/\n/g, '<br>');
}
</script>

</body>
</html>


