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
    $conn = new PDO("mysql:host=".DBHost.";dbname=".DBName.";charset=".DBCharset, DBUser, DBPass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch all sessions
    $sessions_stmt = $conn->query("SELECT * FROM tbl_sessions ORDER BY id ASC");
    $sessions = $sessions_stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get active session
    $active_stmt = $conn->query("SELECT * FROM tbl_sessions WHERE is_active=1 LIMIT 1");
    $active_session = $active_stmt->fetch(PDO::FETCH_ASSOC);

    // Fetch all classes
    $classes_stmt = $conn->query("SELECT * FROM tbl_classes ORDER BY id ASC");
    $classes = $classes_stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch promotion mapping
    $mappings_stmt = $conn->query("SELECT * FROM tbl_class_promotion");
    $mappings = $mappings_stmt->fetchAll(PDO::FETCH_ASSOC);
    $promotion_map = [];
    foreach($mappings as $map){
        $promotion_map[$map['from_class_id']] = $map['to_class_id'];
    }

} catch(PDOException $e) {
    die("Database error: ".$e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<head>
<title>MGTechs - Promted Students</title>
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
<div class="app-title">
    <h1>Promote Students</h1>
    <?php if($active_session): ?>
        <p class="text-success">Current Active Session: <strong><?= htmlspecialchars($active_session['session_name']); ?></strong></p>
    <?php else: ?>
        <p class="text-danger"><strong>No active session!</strong></p>
    <?php endif; ?>
</div>

<div class="row">
<div class="col-md-8 center_form">
    <div class="tile">
        <div class="tile-body">
            <form id="promotionForm" class="app_frm">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label>Current Session</label>
                        <select class="form-control select2" name="current_session" id="currentSession" required>
                            <?php if($active_session): ?>
                                <option value="<?= $active_session['id'] ?>" selected><?= htmlspecialchars($active_session['session_name']); ?></option>
                            <?php else: ?>
                                <option value="" disabled selected>No active session</option>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label>Next Session</label>
                        <select class="form-control select2" name="next_session" id="nextSession" required>
                            <option value="" disabled selected>Select Next Session</option>
                            <?php foreach($sessions as $s): ?>
                                <?php if(!$active_session || $s['id'] != $active_session['id']): ?>
                                    <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['session_name']); ?></option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label>Select Class</label>
                    <select class="form-control select2" name="class_id" id="classSelect" required>
                        <option value="" disabled selected>Select Class</option>
                        <?php foreach($classes as $class): ?>
                            <option value="<?= $class['id'] ?>"><?= htmlspecialchars($class['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3" id="studentsList" style="display:none;">
                    <h5>Students in Selected Class:</h5>
                    <table class="table table-bordered" id="studentsTable">
                        <thead>
                            <tr><th>#</th><th>Name</th></tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>

                <button type="submit" class="btn btn-primary app_btn">Authorize Promotion</button>
            </form>
        </div>
    </div>

    <!-- Promoted Students Table -->
    <div class="tile mt-3">
        <h5>Successfully Promoted Students</h5>
        <div class="table-responsive">
            <table class="table table-bordered" id="promotedTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>From Class</th>
                        <th>To Class</th>
                        <th>From Session</th>
                        <th>To Session</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
</div>
</main>
<!-- jQuery first -->
<script src="js/jquery-3.7.0.min.js"></script>

<!-- Bootstrap bundle -->
<script src="js/bootstrap.bundle.min.js"></script>

<!-- DataTables CSS & JS (full CDN links) -->
<link rel="stylesheet" href="https://cdn.datatables.net/v/bs5/dt-1.13.4/datatables.min.css"/>
<script src="https://cdn.datatables.net/v/bs5/dt-1.13.4/datatables.min.js"></script>

<!-- Select2 -->
<link href="select2/dist/css/select2.min.css" rel="stylesheet" />
<script src="select2/dist/js/select2.full.min.js"></script>

<!-- SweetAlert -->
<script src="js/sweetalert2@11.js"></script>

<!-- Your main JS -->
<script src="js/main.js"></script>

<script>
$(document).ready(function(){

    // Initialize Select2
    $('.select2').select2();

    // Initialize empty DataTables (tables must exist in HTML first)
    let studentsTable = $('#studentsTable').DataTable({
        columns: [
            { title: "#" },
            { title: "Name" }
        ]
    });

    let promotedTable = $('#promotedTable').DataTable({
        columns: [
            { title: "#" },
            { title: "Name" },
            { title: "From Class" },
            { title: "To Class" },
            { title: "From Session" },
            { title: "To Session" }
        ]
    });

    // Fetch students for selected class & session
    function fetchStudents(){
        let classId = $('#classSelect').val();
        let sessionId = $('#currentSession').val();
        if(!classId || !sessionId) return;

        $.ajax({
            url: 'core/fetch_students.php',
            type: 'POST',
            data: { class_id: classId, session_id: sessionId },
            dataType: 'json',
            success: function(students){
                studentsTable.clear();
                if(students.length){
                    $('#studentsList').show();
                    students.forEach((s,i)=>{
                        studentsTable.row.add([
                            i+1,
                            s.name
                        ]);
                    });
                    studentsTable.draw();
                } else {
                    $('#studentsList').hide();
                    Swal.fire('No students found in this class/session');
                }
            },
            error: function(xhr){
                Swal.fire('Error', 'Unable to fetch students: '+xhr.responseText, 'error');
            }
        });
    }

    $('#classSelect, #currentSession').change(fetchStudents);

    // Promote students
    $('#promotionForm').submit(function(e){
        e.preventDefault();
        let currentSession = $('#currentSession').val();
        let nextSession = $('#nextSession').val();
        let classId = $('#classSelect').val();

        if(!currentSession || !nextSession || !classId){
            Swal.fire('Please select all fields');
            return;
        }

        $.ajax({
            url: '/srms/script/academic/core/promote_students.php',
            type: 'POST',
            data: { class_id: classId, session_id: currentSession, next_session_id: nextSession },
            dataType: 'json',
            beforeSend: function(){ Swal.showLoading(); },
            success: function(res){
                Swal.close();
                if(res.success){
                    Swal.fire('Success', res.message, 'success');

                    // Populate promoted table
                    promotedTable.clear();
                    res.students.forEach((s,i)=>{
                        promotedTable.row.add([
                            i+1,
                            s.name,
                            s.from_class,
                            s.to_class,
                            s.from_session,
                            s.to_session
                        ]);
                    });
                    promotedTable.draw();

                    $('#studentsList').hide();
                    $('#classSelect').val(null).trigger('change');
                } else {
                    Swal.fire('Error', res.message, 'error');
                }
            },
            error: function(xhr){
                Swal.close();
                Swal.fire('Error', 'Something went wrong: '+xhr.responseText, 'error');
            }
        });
    });

});
</script>

</body>
</html>
