<?php
// =========================
// SAVE_RESULTS.PHP (Enhanced Final Version)
// =========================

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

chdir('../../');
session_start();

require_once('script/db/config.php');
require_once('script/const/school.php');
require_once('script/const/check_session.php');
require_once('script/const/calculations.php');

// ✅ Ensure the user is an admin or authorized academic staff
if (!isset($res) || $res != "1" || $level != "1") {
    header("location:../");
    exit;
}

// ✅ Read from session (set in manage_results.php)
if (
    !isset($_SESSION['class_id']) ||
    !isset($_SESSION['term_id']) ||
    !isset($_SESSION['session_id'])
) {
    die("<h3 style='color:red; text-align:center;'>❌ ERROR LOADING CLASS, TERM OR SESSION<br><small>Session data missing. Please go back and reselect class, term & session.</small></h3>");
}

$class_id   = $_SESSION['class_id'];
$term_id    = $_SESSION['term_id'];
$session_id = $_SESSION['session_id'];

try {
    $conn = new PDO("mysql:host=" . DBHost . ";dbname=" . DBName . ";charset=" . DBCharset, DBUser, DBPass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // ✅ Fetch grading and division systems
    $grading = $conn->query("SELECT * FROM tbl_grade_system ORDER BY min ASC")->fetchAll(PDO::FETCH_ASSOC);
    $divisions = $conn->query("SELECT * FROM tbl_division_system ORDER BY min ASC")->fetchAll(PDO::FETCH_ASSOC);

    // ✅ Fetch class, term & session
    $class_data = $conn->prepare("SELECT * FROM tbl_classes WHERE id = ?");
    $class_data->execute([$class_id]);
    $class_data = $class_data->fetch(PDO::FETCH_ASSOC);

    $term_data = $conn->prepare("SELECT * FROM tbl_terms WHERE id = ?");
    $term_data->execute([$term_id]);
    $term_data = $term_data->fetch(PDO::FETCH_ASSOC);

    $session_data = $conn->prepare("SELECT * FROM tbl_sessions WHERE id = ?");
    $session_data->execute([$session_id]);
    $session_data = $session_data->fetch(PDO::FETCH_ASSOC);

    if (!$class_data || !$term_data || !$session_data) {
        die("<h3 style='color:red; text-align:center;'>❌ ERROR LOADING CLASS AND TERM<br><small>Invalid class, term or session selected.</small></h3>");
    }

    $title = strtoupper($class_data['name'] . " - " . $term_data['name'] . " (" . $session_data['session_name'] . ") RESULTS");

    // ✅ Fetch students in this class
    $students_stmt = $conn->prepare("SELECT * FROM tbl_students WHERE class = ?");
    $students_stmt->execute([$class_id]);
    $students = $students_stmt->fetchAll(PDO::FETCH_ASSOC);

    // ✅ Fetch subjects mapped to this class
    $subject_stmt = $conn->prepare("
        SELECT sc.*, s.name AS subject_name
        FROM tbl_subject_combinations sc
        LEFT JOIN tbl_subjects s ON sc.subject_id = s.id
        WHERE sc.class_id = ?
    ");
    $subject_stmt->execute([$class_id]);
    $subject_combos = $subject_stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("<h3 style='color:red; text-align:center;'>Database Connection Error:<br>" . htmlspecialchars($e->getMessage()) . "</h3>");
}
?>


<!DOCTYPE html>
<html lang="en">
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<head>
<title>MGTechs - Students Results</title>
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

<style>

.progress { height: 18px; background-color: #eee; border-radius: 10px; overflow: hidden; }
.progress-bar { height: 18px; line-height: 18px; font-size: 12px; }
.avatar_img_sm { width: 45px; height: 45px; border-radius: 50%; object-fit: cover; }
</style>

<main class="app-content">
    <div class="app-title d-flex justify-content-between align-items-center">
        <h3><?= htmlspecialchars($title) ?></h3>
        <a href="academic/core/bulk_report.php?class=<?= $class_id ?>&term=<?= $term_id ?>&session=<?= $session_id ?>" 
           class="btn btn-success btn-sm">
           <i class="bi bi-printer"></i> Generate Bulk Report Cards
        </a>
    </div>

    <div class="tile">
        <div class="tile-body">
            <?php if (empty($students)): ?>
                <div class="alert alert-warning text-center">
                    No students found in this class.
                </div>
            <?php else: ?>
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="srmsTable">
                    <thead class="table-dark">
                        <tr>
                            <th>Photo</th>
                            <th>Reg No</th>
                            <th>Student Name</th>
                            <th>Total Marks</th>
                            <th>Average</th>
                            <th>Grade</th>
                            <th>Remark</th>
                            <th>Result Status</th>
                            <th>Points (3 Terms Avg)</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($students as $student): 
                            $total_score = 0;
                            $num_subjects = 0;

                            // Calculate total marks across all subjects for this term
                            foreach ($subject_combos as $subject) {
                                $score_stmt = $conn->prepare("
                                    SELECT total_score 
                                    FROM tbl_results
                                    WHERE student_id = ? 
                                    AND class_id = ? 
                                    AND term_id = ? 
                                    AND subject_id = ? 
                                    AND session_id = ?
                                ");
                                $score_stmt->execute([$student['id'], $class_id, $term_id, $subject['subject_id'], $session_id]);
                                $row = $score_stmt->fetch(PDO::FETCH_ASSOC);

                                $score = $row ? floatval($row['total_score']) : 0;
                                $total_score += $score;
                                if ($score > 0) $num_subjects++;
                            }

                            $average = $num_subjects > 0 ? round($total_score / $num_subjects, 2) : 0;

                            // Determine grade & remark
                            $grade = $remark = "N/A";
                            foreach ($grading as $g) {
                                if ($average >= $g['min'] && $average <= $g['max']) {
                                    $grade = $g['name'];
                                    $remark = $g['remark'];
                                    break;
                                }
                            }

                            // ✅ Determine completion progress
                            $total_subjects = count($subject_combos);
                            $completion_rate = $total_subjects > 0 ? round(($num_subjects / $total_subjects) * 100, 0) : 0;
                            $status = ($completion_rate == 100) ? "Completed" : "In Progress";
                            $status_color = ($completion_rate == 100) ? "bg-success" : "bg-warning";

                            // ✅ Compute total average for all 3 terms
                            $term_avg_stmt = $conn->prepare("
                                SELECT AVG(total_score) AS avg_score
                                FROM tbl_results 
                                WHERE student_id = ? AND class_id = ? AND session_id = ?
                            ");
                            $term_avg_stmt->execute([$student['id'], $class_id, $session_id]);
                            $points_data = $term_avg_stmt->fetch(PDO::FETCH_ASSOC);
                            $points = $points_data ? round($points_data['avg_score'], 2) : 0;

                            $avatar = (!empty($student['display_image']) && file_exists('images/students/'.$student['display_image']))
                                ? 'images/students/'.$student['display_image']
                                : 'images/students/default.png';
                        ?>
                        <tr>
                            <td><img src="<?= htmlspecialchars($avatar) ?>" class="avatar_img_sm"></td>
                            <td><?= htmlspecialchars($student['id']) ?></td>
                            <td><?= htmlspecialchars($student['fname'].' '.$student['lname']) ?></td>
                            <td><?= $total_score ?></td>
                            <td><?= $average ?></td>
                            <td><?= htmlspecialchars($grade) ?></td>
                            <td><?= htmlspecialchars($remark) ?></td>
                            <td>
                                <div class="progress">
                                    <div class="progress-bar <?= $status_color ?>" role="progressbar" 
                                         style="width: <?= $completion_rate ?>%">
                                         <?= $completion_rate ?>% <?= $status ?>
                                    </div>
                                </div>
                            </td>
                            <td><?= $points ?></td>
                            <td>
                                <a href="academic/edit_student_results.php?std=<?= $student['id'] ?>&term_id=<?= $term_id ?>&session_id=<?= $session_id ?>" 
                                    class="btn btn-sm btn-primary"><i class="bi bi-pencil-square"></i> Edit</a>

                                <a href="academic/save_pdf.php?std=<?= $student['id'] ?>&term=<?= $term_id ?>&session=<?= $session_id ?>" 
                                   class="btn btn-sm btn-success"><i class="bi bi-printer"></i>Print</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<script src="../../js/jquery-3.7.0.min.js"></script>
<script src="../../js/bootstrap.bundle.min.js"></script>
<script src="../../js/plugins/jquery.dataTables.min.js"></script>
<script src="../../js/plugins/dataTables.bootstrap.min.js"></script>
<script>
$('#srmsTable').DataTable();
</script>
</body>
</html>
