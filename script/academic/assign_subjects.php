<?php
chdir('../');
session_start();
require_once('db/config.php');
require_once('const/school.php');
require_once('const/check_session.php');

if (!($res == "1" && $level == "1")) {
    header("location:../");
    exit;
}

try {
    $conn = new PDO("mysql:host=" . DBHost . ";dbname=" . DBName . ";charset=" . DBCharset, DBUser, DBPass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Classes
    $classes = $conn->query("SELECT id, name FROM tbl_classes ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);

    // Sessions
    $sessions = $conn->query("SELECT id, session_name AS name FROM tbl_sessions ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);

    // Terms
    $terms = $conn->query("SELECT id, name FROM tbl_terms ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);

    // Current active session
    $currentSession = $conn->query("SELECT id FROM tbl_sessions WHERE is_active=1 LIMIT 1")->fetchColumn();

    // Current active term
    $currentTerm = $conn->query("SELECT id FROM tbl_terms WHERE status=1 ORDER BY id DESC LIMIT 1")->fetchColumn();

} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Handle class selection for auto-loading
$class_id = $_GET['class_id'] ?? null;
$session_id = $_GET['session_id'] ?? $currentSession;
$term_id = $_GET['term_id'] ?? $currentTerm;

$students = [];
$subjects = [];

if ($class_id) {
    // Fetch students
    $stmt = $conn->prepare("SELECT id, fname, lname FROM tbl_students WHERE class = ?");
    $stmt->execute([$class_id]);
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch subjects assigned to class
    $stmt = $conn->prepare("
        SELECT s.id, s.name 
        FROM tbl_subject_combinations sc
        JOIN tbl_subjects s ON sc.subject_id = s.id
        WHERE sc.class_id = ?
    ");
    $stmt->execute([$class_id]);
    $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<head>
<title>MGTechs - Students Subjects</title>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<base href="../">
<link rel="stylesheet" type="text/css" href="css/main.css">
<link rel="icon" href="images/icon.ico">
<link rel="stylesheet" type="text/css" href="cdn.jsdelivr.net/npm/bootstrap-icons%401.10.5/font/bootstrap-icons.css">
<link type="text/css" rel="stylesheet" href="loader/waitMe.css">
</head>
<body class="app sidebar-mini">

<header class="app-header"><a class="app-header__logo" href="javascript:void(0);">MGTechs</a>
<a class="app-sidebar__toggle" href="#" data-toggle="sidebar" aria-label="Hide Sidebar"></a>

<ul class="app-nav">

<li class="dropdown"><a class="app-nav__item" href="#" data-bs-toggle="dropdown" aria-label="Open Profile Menu"><i class="bi bi-person fs-4"></i></a>
<ul class="dropdown-menu settings-menu dropdown-menu-right">
<li><a class="dropdown-item" href="academic/profile"><i class="bi bi-person me-2 fs-5"></i> Profile</a></li>
<li><a class="dropdown-item" href="logout"><i class="bi bi-box-arrow-right me-2 fs-5"></i> Logout</a></li>
</ul>
</li>
</ul>
</header>

<!--========== START SIDE  NAVS =================-->
<div class="app-sidebar__overlay" data-toggle="sidebar"></div>
<aside class="app-sidebar">
<div class="app-sidebar__user">
<div>
<p class="app-sidebar__user-name"><?php echo $fname.' '.$lname; ?></p>
<p class="app-sidebar__user-designation">Academic</p>
</div>
</div>
<ul class="app-menu">
<li><a class="app-menu__item active" href="academic"><i class="app-menu__icon feather icon-monitor"></i><span class="app-menu__label">Dashboard</span></a></li>


<!--========== START ACADEMICS NAVS =================-->
<li class="treeview"><a class="app-menu__item" href="javascript:void(0);" data-toggle="treeview"><i class="app-menu__icon feather icon-file-text"></i><span class="app-menu__label">Academics</span><i class="treeview-indicator bi bi-chevron-right"></i></a>
<ul class="treeview-menu">

<li><a class="treeview-item" href="academic/terms"><i class="icon bi bi-circle-fill"></i>Academic Terms</span></a></li>
<li><a class="treeview-item" href="academic/classes"><i class="icon bi bi-circle-fill"></i>Classes</span></a></li>
<li><a class="treeview-item" href="academic/subjects"><i class="icon bi bi-circle-fill"></i>Subjects </span></a></li>
<li><a class="treeview-item" href="academic/academic_session"><i class="icon bi bi-circle-fill"></i>Academic Sessions</span></a></li>
<li><a class="treeview-item" href="academic/academic_calendar"><i class="icon bi bi-circle-fill"></i>Academic Calendar</span></a></li>
<li><a class="treeview-item" href="academic/lession_timetable"><i class="icon bi bi-circle-fill"></i>Lesson Timetable</span></a></li>
<li><a class="treeview-item" href="academic/duty_roster"><i class="icon bi bi-circle-fill"></i>Duty Roster</span></a></li>
<li><a class="treeview-item" href="academic/school_heads"><i class="icon bi bi-circle-fill"></i>School Hedas</span></a></li>

</ul>
</li>
<!--========== END ACADEMICS NAVS =================-->


<!--========== START APPLICATION/ADMISSION NAVS =================-->
<li class="treeview"><a class="app-menu__item" href="javascript:void(0);" data-toggle="treeview"><i class="app-menu__icon feather icon-users"></i><span class="app-menu__label">Applications</span><i class="treeview-indicator bi bi-chevron-right"></i></a>
<ul class="treeview-menu">
<li><a class="treeview-item" href="academic/manage_student_application"><i class="icon bi bi-circle-fill"></i>Students Application</a></li>
<li><a class="treeview-item" href="academic/manage_staff_application"><i class="icon bi bi-circle-fill"></i>Staff Application</a></li>
</ul>
</li>
<!--========== ENDS APPLICATION/ADMISSION NAVS =================-->



<!--========== START STUDENT NAVS =================-->
<li class="treeview"><a class="app-menu__item" href="javascript:void(0);" data-toggle="treeview"><i class="app-menu__icon feather icon-users"></i><span class="app-menu__label">Students </span><i class="treeview-indicator bi bi-chevron-right"></i></a>
<ul class="treeview-menu">

<li><a class="treeview-item" href="academic/new_student"><i class="icon bi bi-circle-fill"></i>Add Student</a></li>
<li><a class="treeview-item" href="academic/inport_students"><i class="icon bi bi-circle-fill"></i>Inport Students</a></li>
<li><a class="treeview-item" href="academic/assign_subjects"><i class="icon bi bi-circle-fill"></i> Students Subjects</span></a></li>
<li><a class="treeview-item" href="academic/manage_students"><i class="icon bi bi-circle-fill"></i>Manage Students</a></li>
</ul>
</li>
<!--========== ENDS STUDENT NAVS =================-->

 
<!--========== START STAFF NAVS =================-->
<li class="treeview"><a class="app-menu__item" href="javascript:void(0);" data-toggle="treeview"><i class="app-menu__icon feather icon-users"></i><span class="app-menu__label">Staff</span><i class="treeview-indicator bi bi-chevron-right"></i></a>
<ul class="treeview-menu">

<li><a class="treeview-item" href="academic/new_staff"><i class="icon bi bi-circle-fill"></i>New Staff</a></li>
<li><a class="treeview-item" href="academic/import_staff"><i class="icon bi bi-circle-fill"></i>Import Staffs</a></li>
<li><a class="treeview-item" href="academic/combinations"><i class="icon bi bi-circle-fill"></i>Subject Combinations</span></a></li>
<li><a class="treeview-item" href="academic/teachers"><i class="icon bi bi-circle-fill"></i>Manage Staff</a></li>

</ul>
</li>
<!--========== ENDS STAFF NAVS =================-->


<!--========== START EXAMS RESULTS NAVS =================-->
<li class="treeview"><a class="app-menu__item" href="javascript:void(0);" data-toggle="treeview"><i class="app-menu__icon feather icon-file-text"></i><span class="app-menu__label">Examination Results</span><i class="treeview-indicator bi bi-chevron-right"></i></a>
<ul class="treeview-menu">

<li><a class="treeview-item" href="academic/manage_results"><i class="icon bi bi-circle-fill"></i> Manage Scores</a></li>
<li><a class="treeview-item" href="academic/individual_results"><i class="icon bi bi-circle-fill"></i> Individual Scores</a></li>
<li><a class="treeview-item" href="academic/promote_students"><i class="icon bi bi-circle-fill"></i> Promote Students</a></li>
<li><a class="treeview-item" href="academic/maping"><i class="icon bi bi-circle-fill"></i> Promotion Mapping</a></li>
<li><a class="treeview-item" href="academic/view_result_settings"><i class="icon bi bi-circle-fill"></i> View Result Settings</a></li>
<li><a class="treeview-item" href="academic/score_entry_settings"><i class="icon bi bi-circle-fill"></i> Score Entry Settings</a></li>
</ul>
</li>
<!--========== ENDS EXAMS RESULTS NAVS =================-->


<!--========== START PAYMENT NAVS =================-->
<li class="treeview"><a class="app-menu__item" href="javascript:void(0);" data-toggle="treeview"><i class="app-menu__icon feather icon-file-text"></i><span class="app-menu__label">Payment</span><i class="treeview-indicator bi bi-chevron-right"></i></a>
<ul class="treeview-menu">

<li><a class="treeview-item" href="academic/manage_fees"><i class="icon bi bi-circle-fill"></i> Manage Fees </a></li>
<li><a class="treeview-item" href="academic/manage_salary"><i class="icon bi bi-circle-fill"></i> Manage Salary</a></li>
<li><a class="treeview-item" href="academic/manage_loan"><i class="icon bi bi-circle-fill"></i>Manage Loan</a></li>
<li><a class="treeview-item" href="academic/expenses"><i class="icon bi bi-circle-fill"></i>Manage Expenses</a></li>

</ul>
</li>
<!--========== END PAYMENT NAVS =================-->


<!--========== START REPORT GENERATION NAVS =================-->
<li class="treeview"><a class="app-menu__item" href="javascript:void(0);" data-toggle="treeview"><i class="app-menu__icon feather icon-file-text"></i><span class="app-menu__label">Generate Report</span><i class="treeview-indicator bi bi-chevron-right"></i></a>
<ul class="treeview-menu">

<li><a class="treeview-item" href="academic/fees_report"><i class="icon bi bi-circle-fill"></i>Fees Report</span></a></li>
<li><a class="treeview-item" href="academic/expenses_report"><i class="icon bi bi-circle-fill"></i>Expenses Report</span></a></li>
<li><a class="treeview-item" href="academic/salaries_report"><i class="icon bi bi-circle-fill"></i>Salaries Report</span></a></li>
<li><a class="treeview-item" href="academic/loan_report"><i class="icon bi bi-circle-fill"></i>Loan Report</span></a></li>
<li><a class="treeview-item" href="academic/other_report"><i class="icon bi bi-circle-fill"></i>Other Report</span></a></li>
<li><a class="treeview-item" href="academic/bulk_result_report"><i class="icon bi bi-circle-fill"></i>Bulk Result Report</span></a></li>
<li><a class="treeview-item" href="academic/single_result_report"><i class="icon bi bi-circle-fill"></i>Other Report</span></a></li>

</ul>
</li>
<!--========== END REPORT GENERATION NAVS =================-->


<!--========== START GRADING NAVS =================-->
<li class="treeview"><a class="app-menu__item" href="javascript:void(0);" data-toggle="treeview"><i class="app-menu__icon feather icon-file-text"></i><span class="app-menu__label">Grading</span><i class="treeview-indicator bi bi-chevron-right"></i></a>
<ul class="treeview-menu">

<li><a class="treeview-item" href="academic/grading-system"><i class="icon bi bi-circle-fill"></i>Grading System</span></a></li>
<li><a class="treeview-item" href="academic/division-system"><i class="icon bi bi-circle-fill"></i>Division System</span></a></li>
</ul>
</li>
<!--========== END GRADING NAVS =================-->


<!--========== START ANNOUCEMEENT NAVS =================-->
<li class="treeview"><a class="app-menu__item" href="javascript:void(0);" data-toggle="treeview"><i class="app-menu__icon feather icon-file-text"></i><span class="app-menu__label">Announcements</span><i class="treeview-indicator bi bi-chevron-right"></i></a>
<ul class="treeview-menu">

<li><a class="treeview-item" href="academic/announcement"><i class="icon bi bi-circle-fill"></i>Notifications</span></a></li>
<li><a class="treeview-item" href="academic/send_sms"><i class="icon bi bi-circle-fill"></i>Send Email</span></a></li>
<li><a class="treeview-item" href="academic/send_email"><i class="icon bi bi-circle-fill"></i>Send SMS</span></a></li>
<li><a class="treeview-item" href="academic/view_enquiries"><i class="icon bi bi-circle-fill"></i>View Enquiries</span></a></li>

</ul>
</li>
<!--========== END ANNOUCEMEENT =================-->

<!--========== START ENQUERIES NAVS =================-->
<li class="treeview"><a class="app-menu__item" href="javascript:void(0);" data-toggle="treeview"><i class="app-menu__icon feather icon-file-text"></i><span class="app-menu__label">ENQUERIES</span><i class="treeview-indicator bi bi-chevron-right"></i></a>
<ul class="treeview-menu">

<li><a class="treeview-item" href="academic/announcement"><i class="icon bi bi-circle-fill"></i>Notifications</span></a></li>
<li><a class="treeview-item" href="academic/send_sms"><i class="icon bi bi-circle-fill"></i>Send Email</span></a></li>
<li><a class="treeview-item" href="academic/send_email"><i class="icon bi bi-circle-fill"></i>Send SMS</span></a></li>

</ul>
</li>
<!--========== END ENQUERIES =================-->


<!--========== START SCHOOL INVENTORY NAVS =================-->
<li class="treeview"><a class="app-menu__item" href="javascript:void(0);" data-toggle="treeview"><i class="app-menu__icon feather icon-file-text"></i><span class="app-menu__label">School Inventory</span><i class="treeview-indicator bi bi-chevron-right"></i></a>
<ul class="treeview-menu">

<li><a class="treeview-item" href="academic/new_assests"><i class="icon bi bi-circle-fill"></i>New Assests</span></a></li>
<li><a class="treeview-item" href="academic/manage_assests"><i class="icon bi bi-circle-fill"></i>Manage_assets</span></a></li>
</ul>
</li>
<!--========== END SCHOOL INVENTORY NAVS =================-->

<!--========== START SITE CONFIGURATIONS NAVS =================-->
<li class="treeview"><a class="app-menu__item" href="javascript:void(0);" data-toggle="treeview"><i class="app-menu__icon feather icon-file-text"></i><span class="app-menu__label">Site Configurations</span><i class="treeview-indicator bi bi-chevron-right"></i></a>
<ul class="treeview-menu">

<li><a class="treeview-item" href="academic/api_settings"><i class="icon bi bi-circle-fill"></i>Api Settings</span></a></li>
<li><a class="treeview-item" href="academic/site_settings"><i class="icon bi bi-circle-fill"></i>Site Settings</span></a></li>
<li><a class="treeview-item" href="academic/application_settings"><i class="icon bi bi-circle-fill"></i>Application Settings</span></a></li>
<li><a class="treeview-item" href="academic/fees_settings"><i class="icon bi bi-circle-fill"></i>School Fees Settings</span></a></li>
<li><a class="treeview-item" href="academic/salary_settings"><i class="icon bi bi-circle-fill"></i>Salary Settings</span></a></li>


</ul>
</li>
<!--========== END SITE CONFIGURATIONS NAVS =================-->
</aside>

<!--========== END SIDE  NAVS =================-->



<main class="app-content">
<div class="app-title"><h1><i class="bi bi-list-task"></i> Assign Subjects to Students</h1></div>

<div class="row">
<div class="col-md-12">
<div class="tile">

<form method="GET" class="row mb-4">
    <div class="col-md-3">
        <label class="form-label">Select Class</label>
        <select name="class_id" class="form-select" required>
            <option value="">-- Select Class --</option>
            <?php foreach ($classes as $cl): ?>
                <option value="<?= $cl['id']; ?>" <?= $class_id == $cl['id'] ? 'selected' : ''; ?>>
                    <?= htmlspecialchars($cl['name']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="col-md-3">
        <label class="form-label">Session</label>
        <select name="session_id" class="form-select" required>
            <option value="">-- Select Session --</option>
            <?php foreach ($sessions as $s): ?>
                <option value="<?= $s['id']; ?>" <?= $session_id == $s['id'] ? 'selected' : ''; ?>>
                    <?= htmlspecialchars($s['name']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="col-md-3">
        <label class="form-label">Term</label>
        <select name="term_id" class="form-select" required>
            <option value="">-- Select Term --</option>
            <?php foreach ($terms as $t): ?>
                <option value="<?= $t['id']; ?>" <?= $term_id == $t['id'] ? 'selected' : ''; ?>>
                    <?= htmlspecialchars($t['name']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="col-md-3 d-flex align-items-end">
        <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search"></i> Load Students</button>
    </div>
</form>

<?php if($class_id && !empty($students) && !empty($subjects)): ?>
<form method="POST" action="/srms/script/academic/core/assign_subjects_core">
    <input type="hidden" name="class_id" value="<?= $class_id; ?>">
    <input type="hidden" name="session_id" value="<?= $session_id; ?>">
    <input type="hidden" name="term_id" value="<?= $term_id; ?>">

    <div class="table-responsive">
    <table class="table table-bordered table-striped align-middle">
        <thead class="table-dark">
            <tr>
                <th>Student Name</th>
                <?php foreach ($subjects as $sub): ?>
                    <th><?= htmlspecialchars($sub['name']); ?></th>
                <?php endforeach; ?>
                <th><input type="checkbox" id="checkAll"> All</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($students as $st): ?>
            <tr>
                <td><strong><?= htmlspecialchars($st['fname'] . ' ' . $st['lname']); ?></strong></td>
                <?php foreach ($subjects as $sub): 
                    $chk = $conn->prepare("SELECT COUNT(*) FROM tbl_student_subjects WHERE student_id=? AND class_id=? AND subject_id=? AND session_id=? AND term_id=?");
                    $chk->execute([$st['id'], $class_id, $sub['id'], $session_id, $term_id]);
                    $isChecked = $chk->fetchColumn() > 0;
                ?>
                <td class="text-center">
                    <input type="checkbox" name="subjects[<?= $st['id']; ?>][]" value="<?= $sub['id']; ?>" <?= $isChecked ? 'checked' : ''; ?>>
                </td>
                <?php endforeach; ?>
                <td class="text-center"><input type="checkbox" class="select-row"></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    </div>

    <div class="mt-3">
        <button type="submit" class="btn btn-success"><i class="bi bi-save"></i> Save Assignments</button>
    </div>
</form>

<script>
document.getElementById('checkAll').addEventListener('change', function() {
    const checked = this.checked;
    document.querySelectorAll('tbody input[type="checkbox"]').forEach(cb => cb.checked = checked);
});
</script>
<?php elseif($class_id): ?>
<div class="alert alert-warning">No students or subjects found for this class.</div>
<?php endif; ?>

</div></div></div>
</main>

<script src="../js/jquery-3.7.0.min.js"></script>
<script src="../js/bootstrap.bundle.min.js"></script>
</body>
</html>
