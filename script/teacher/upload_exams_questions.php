<?php
chdir('../');
session_start();
require_once('db/config.php');
require_once('const/school.php');
require_once('const/check_session.php');

// Only allow teachers
if (!($res == "1" && $level == "2")) {
    header("location:../");
    exit;
}



try {
    $conn = new PDO("mysql:host=".DBHost.";dbname=".DBName.";charset=".DBCharset, DBUser, DBPass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  // Active session
    $stmt = $conn->query("SELECT * FROM tbl_sessions WHERE satus = 1 LIMIT 1");
    $active_session = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Fetch the latest enabled term
$stmt = $conn->query("SELECT * FROM tbl_terms WHERE status = 1 ORDER BY id DESC LIMIT 1");
$term = $stmt->fetch(PDO::FETCH_ASSOC);

} catch(PDOException $e){
    die("Database error: ".$e->getMessage());
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<base href="../">
<link rel="stylesheet" type="text/css" href="css/main.css">
<link rel="icon" href="images/icon.ico">
<link rel="stylesheet" type="text/css" href="cdn.jsdelivr.net/npm/bootstrap-icons%401.10.5/font/bootstrap-icons.css">
<link type="text/css" rel="stylesheet" href="loader/waitMe.css">

<!-- Bootstrap 5 -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Select2 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
<!-- Summernote -->
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
</head>
<body class="app sidebar-mini">

<header class="app-header"><a class="app-header__logo" href="javascript:void(0);">MGTechs</a>
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
<li><a class="app-menu__item" href="teacher/terms"><i class="app-menu__icon feather icon-folder"></i><span class="app-menu__label">Academic Terms</span></a></li>
<li><a class="app-menu__item" href="teacher/my_timetable"><i class="app-menu__icon feather icon-book-open"></i><span class="app-menu__label">My TimeTable</span></a></li>
<li><a class="app-menu__item" href="teacher/academic_calendar"><i class="app-menu__icon feather icon-book-open"></i><span class="app-menu__label">Academic calendar</span></a></li>
<li><a class="app-menu__item" href="teacher/manage_exams_questions"><i class="app-menu__icon feather icon-book-open"></i><span class="app-menu__label">Exams Questions</span></a></li>

<li class="treeview"><a class="app-menu__item" href="javascript:void(0);" data-toggle="treeview"><i class="app-menu__icon feather icon-users"></i><span class="app-menu__label">Students</span><i class="treeview-indicator bi bi-chevron-right"></i></a>
<ul class="treeview-menu">
<li><a class="treeview-item" href="teacher/list_students"><i class="icon bi bi-circle-fill"></i> List Students</a></li>
<li><a class="treeview-item" href="teacher/export_students"><i class="icon bi bi-circle-fill"></i> Export Students</a></li>
<li><a class="app-menu__item" href="teacher/combinations"><i class="app-menu__icon feather icon-book-open"></i><span class="app-menu__label">My Subject</span></a></li>
</ul>
</li>

<li class="treeview"><a class="app-menu__item" href="javascript:void(0);" data-toggle="treeview"><i class="app-menu__icon feather icon-file-text"></i><span class="app-menu__label">Loan Application</span><i class="treeview-indicator bi bi-chevron-right"></i></a>
<ul class="treeview-menu">
<li><a class="treeview-item" href="teacher/loan_application"><i class="icon bi bi-circle-fill"></i> Loan Application</a></li>
<li><a class="treeview-item" href="teacher/view_my_loans"><i class="icon bi bi-circle-fill"></i> View My Loans</a></li>
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
<li><a class="treeview-item" href="teacher/manage_results"><i class="icon bi bi-circle-fill"></i> View Results</a></li>
</ul>
</li>

<li><a class="app-menu__item" href="teacher/grading-system"><i class="app-menu__icon feather icon-award"></i><span class="app-menu__label">Grading System</span></a></li>
<li><a class="app-menu__item" href="teacher/division-system"><i class="app-menu__icon feather icon-layers"></i><span class="app-menu__label">Division System</span></a></li>
<li><a class="app-menu__item" href="teacher/division-system"><i class="app-menu__icon feather icon-layers"></i><span class="app-menu__label">Download Logs</span></a></li>
</ul>

</aside>

<main class="app-content">
<div class="app-title">
    <div>
        <h1>Submit Exams Questions</h1>
    </div>
</div>

<div class="row">
<div class="col-md-12 center_form">
<div class="tile">
<div class="tile-body">
<div class="table-responsive">
<h3 class="tile-title">Submit Exams Questions</h3>

<?php if($message): ?>
<div class="alert alert-info"><?php echo htmlspecialchars($message); ?></div>
<?php endif; ?>

    <form method="POST" enctype="multipart/form-data">

    <div class="mb-3">
        <label class="form-label">Session</label>
        <input type="text" class="form-control" value="<?= htmlspecialchars($active_session['session_name'] ?? '-') ?>" readonly>
    </div>

<div class="mb-3">
    <label class="form-label">Term</label>
    <input type="text" class="form-control" value="<?= htmlspecialchars($term['name'] ?? '-') ?>" readonly>
</div>


    <div class="mb-3">
        <label class="form-label">Class</label>
        <select class="form-control select2" name="class_id" required>
            <option value="">-- Select Class --</option>
            <?php foreach($classes as $c): ?>
                <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="mb-3">
        <label class="form-label">Subject</label>
        <select class="form-control select2" name="subject_id" required>
            <option value="">-- Select Subject --</option>
            <?php foreach($subjects as $s): ?>
                <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['name']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="mb-3">
        <label class="form-label">Instructions (optional)</label>
        <textarea class="form-control" name="instructions" rows="3" placeholder="Enter instructions if any"></textarea>
    </div>

    <div class="mb-3">
        <label class="form-label">Question Text</label>
        <textarea id="summernote" name="question_text"><?= htmlspecialchars($_POST['question_text'] ?? '') ?></textarea>
    </div>

    <div class="mb-3">
        <label class="form-label">Upload Question File</label>
        <input type="file" class="form-control" name="question_file" >
    </div>

    <button type="submit" name="submit" class="btn btn-primary">Upload</button>
</form>

</div>
</main>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.full.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
<script>
$(document).ready(function(){
    $('.select2').select2({width:'100%'});

    // Apply Summernote to Question Text only
    $('#summernote').summernote({
        height: 200,
        placeholder: 'Enter question text here...'
    });
});
</script>
</body>
</html>
