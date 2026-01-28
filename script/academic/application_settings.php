<?php
chdir('../');
session_start();
require_once('db/config.php');
require_once('const/school.php');
require_once('const/check_session.php');
require_once('const/academic_dashboard.php');
if ($res == "1" && $level == "1") {}else{header("location:../");}

try {
    $conn = new PDO("mysql:host=".DBHost.";dbname=".DBName.";charset=".DBCharset, DBUser, DBPass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch all application settings
    $stmt = $conn->query("SELECT * FROM tbl_application_settings ORDER BY id DESC");
    $applications = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch(PDOException $e) {
    die("Database error: ".$e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<head>
<title>MGTech - sApplication Settings</title>
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

<!-- Main -->
<main class="app-content">
<div class="app-title d-flex justify-content-between align-items-center mb-3">
  <h1>Application Settings</h1>
  <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addModal">
    <i class="bi bi-plus-circle"></i> Add Setting
  </button>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form class="modal-content"action="academic/core/application_settings"  method="POST" >
      <div class="modal-header">
        <h5 class="modal-title">Add Application Setting</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label>Application Type</label>
          <select name="type" class="form-control" required onchange="toggleFee(this.value)">
            <option value="">Select Type</option>
            <option value="student">Student Application</option>
            <option value="staff">Staff Application</option>
          </select>
        </div>
        <div class="mb-3">
          <label>Start Date</label>
          <input type="date" name="start_date" class="form-control" required>
        </div>
        <div class="mb-3">
          <label>End Date</label>
          <input type="date" name="end_date" class="form-control" required>
        </div>
        <div class="mb-3" id="feeField" style="display:none;">
          <label>Application Fee (₦)</label>
          <input type="number" name="application_fee" step="0.01" class="form-control">
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Save</button>
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
      </div>
    </form>
  </div>
</div>

<!-- Table -->
<div class="row">
  <div class="col-md-12">
    <div class="tile">
      <h3 class="tile-title">Existing Application Settings</h3>
      <div class="table-responsive">
        <table class="table table-bordered" id="settingsTable">
          <thead>
            <tr>
              <th>#</th>
              <th>Type</th>
              <th>Start Date</th>
              <th>End Date</th>
              <th>Fee (₦)</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
          <?php foreach($applications as $i => $app): ?>
            <tr>
              <td><?= $i+1 ?></td>
              <td><?= ucfirst($app['type']) ?></td>
              <td><?= $app['start_date'] ?></td>
              <td><?= $app['end_date'] ?></td>
              <td><?= $app['application_fee'] ? number_format($app['application_fee'],2) : '-' ?></td>
              <td>
                <span class="badge bg-<?= $app['status']=='active'?'success':'secondary' ?>">
                  <?= ucfirst($app['status']) ?>
                </span>
              </td>
              <td>
                <a href="academic/edit_application_setting?id=<?= $app['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                <a href="academic/toggle_application_setting?id=<?= $app['id'] ?>&status=<?= $app['status'] ?>" class="btn btn-sm btn-info">
                  <?= $app['status']=='active'?'Deactivate':'Activate' ?>
                </a>
                <a href="academic/delete_application_setting?id=<?= $app['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this setting?')">Delete</a>
              </td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
</main>

<!-- JS -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/v/bs5/dt-1.13.4/datatables.min.js"></script>
<script>
$(document).ready(function(){
    $('#settingsTable').DataTable();
});

function toggleFee(type) {
  document.getElementById('feeField').style.display = (type === 'student') ? 'block' : 'none';
}
</script>
</body>
</html>
