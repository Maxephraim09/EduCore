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

    // Fetch active session
    $session_stmt = $conn->query("SELECT id, session_name FROM tbl_sessions WHERE is_active = 1 LIMIT 1");
    $active_session = $session_stmt->fetch(PDO::FETCH_ASSOC);
    $current_session = $active_session ? $active_session['session_name'] : 'No Active Session';
    $current_session_id = $active_session ? $active_session['id'] : 0;

    // Fetch active term
    $term_stmt = $conn->query("SELECT id, name FROM tbl_terms WHERE status = 1 LIMIT 1");
    $active_term = $term_stmt->fetch(PDO::FETCH_ASSOC);
    $current_term = $active_term ? $active_term['name'] : 'No Active Term';
    $current_term_id = $active_term ? $active_term['id'] : 0;

    // Fetch all classes
    $class_stmt = $conn->query("SELECT id, name FROM tbl_classes ORDER BY name ASC");
    $classes = $class_stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "<div class='text-danger'>" . $e->getMessage() . "</div>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>MGTechs - Manage Fees</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<base href="../">
<link rel="stylesheet" href="css/main.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
<link rel="stylesheet" href="https://cdn.datatables.net/v/bs5/dt-1.13.4/datatables.min.css">
<link rel="stylesheet" href="loader/waitMe.css">
<link href="select2/dist/css/select2.min.css" rel="stylesheet" />
<style>
.select2-container { z-index: 9999 !important; width: 100% !important; }
</style>
</head>

<body class="app sidebar-mini">

<header class="app-header">
  <a class="app-header__logo" href="#">MGTechs</a>
  <a class="app-sidebar__toggle" href="#" data-toggle="sidebar"></a>
  <ul class="app-nav">
    <li class="dropdown">
      <a class="app-nav__item" href="#" data-bs-toggle="dropdown"><i class="bi bi-person fs-4"></i></a>
      <ul class="dropdown-menu settings-menu dropdown-menu-right">
        <li><a class="dropdown-item" href="admin/profile"><i class="bi bi-person me-2 fs-5"></i> Profile</a></li>
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
    <h1>Fees Management</h1>
    <ul class="app-breadcrumb breadcrumb">
      <li class="breadcrumb-item"><button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addModal">Add Fee</button></li>
      <li class="breadcrumb-item"><button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#printModal">Print Fees</button></li>
    </ul>
  </div>

  <!-- Add Fee Modal -->
  <div class="modal fade" id="addModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header"><h5 class="modal-title">Add New Fee Mapping</h5></div>
        <div class="modal-body">
          <form class="app_frm" method="POST" action="academic/core/add_fee_mapping">
            <div class="mb-2">
              <label>Session</label>
              <input type="text" class="form-control" name="session" value="<?= htmlspecialchars($current_session); ?>" readonly>
              <input type="hidden" name="session_id" value="<?= $current_session_id; ?>">
            </div>

            <div class="mb-2">
              <label>Term</label>
              <input type="text" class="form-control" name="term" value="<?= htmlspecialchars($current_term); ?>" readonly>
              <input type="hidden" name="term_id" value="<?= $current_term_id; ?>">
            </div>

            <div class="mb-2">
              <label>Select Class</label>
              <select class="form-control select2" name="class_id" required>
                <option disabled selected>Select Class</option>
                <?php foreach ($classes as $class): ?>
                  <option value="<?= $class['id']; ?>"><?= htmlspecialchars($class['name']); ?></option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="mb-2">
              <label>Fee Amount (₦)</label>
              <input type="number" class="form-control" name="amount" placeholder="Enter amount" required>
            </div>

            <button type="submit" class="btn btn-primary app_btn">Add Fee</button>
            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
          </form>
        </div>
      </div>
    </div>
  </div>
  <!-- Edit Fee Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header"><h5 class="modal-title">Edit Fee</h5></div>
      <div class="modal-body">
        <form id="editFeeForm" method="POST" action="academic/core/update_fee.php">
          <input type="hidden" name="id" id="editFeeId">

          <div class="mb-2">
            <label>Class</label>
            <input type="text" class="form-control" id="editClassName" readonly>
          </div>

          <div class="mb-2">
            <label>Amount (₦)</label>
            <input type="number" class="form-control" name="amount" id="editAmount" required>
          </div>

          <button type="submit" class="btn btn-primary">Update Fee</button>
          <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
        </form>
      </div>
    </div>
  </div>
</div>


  <!-- Print Fees Modal -->
  <div class="modal fade" id="printModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header"><h5 class="modal-title">Generate Fee Report</h5></div>
        <div class="modal-body">
          <form method="GET" action="academic/core/print_fee_report" target="_blank">
            <div class="mb-2">
              <label>Select Report Type</label>
              <select name="report_type" class="form-control select2" required>
                <option value="session">Current Session</option>
                <option value="term">Current Term</option>
                <option value="all">All Records</option>
              </select>
            </div>
            <button type="submit" class="btn btn-success">Generate Report</button>
            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Fees Table -->
  <div class="row">
    <div class="col-md-12">
      <div class="tile">
        <div class="tile-body">
          <h3 class="tile-title">Current Fees Mapping</h3>
          <div class="table-responsive">
            <table class="table table-bordered" id="feeTable">
              <thead>
                <tr>
                  <th>Session</th>
                  <th>Term</th>
                  <th>Class</th>
                  <th>Amount (₦)</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
<?php
try {
    $stmt = $conn->prepare("
        SELECT f.id, s.session_name, t.name AS term_name, c.name AS class_name, f.amount 
        FROM tbl_fee_mapping f
        JOIN tbl_sessions s ON f.session_id = s.id
        JOIN tbl_terms t ON f.term_id = t.id
        JOIN tbl_classes c ON f.class_id = c.id
        ORDER BY f.id DESC
    ");
    $stmt->execute();
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
        ?>
        <tr>
            <td><?= htmlspecialchars($row['session_name']); ?></td>
            <td><?= htmlspecialchars($row['term_name']); ?></td>
            <td><?= htmlspecialchars($row['class_name']); ?></td>
            <td><?= '₦' . number_format($row['amount'], 2); ?></td>
            <td>
                <button class="btn btn-sm btn-primary editFeeBtn" 
                    data-id="<?= $row['id']; ?>" 
                    data-amount="<?= $row['amount']; ?>" 
                    data-class="<?= htmlspecialchars($row['class_name']); ?>">
                    <i class="bi bi-pencil"></i> Edit
                </button>
                <a href="academic/pay_fee.php?id=<?= $row['id']; ?>" class="btn btn-sm btn-success">
                    <i class="bi bi-cash"></i> Pay Fees
                </a>
            </td>
        </tr>
        <?php
    }
} catch (PDOException $e) {
    echo "<tr><td colspan='5' class='text-danger'>" . $e->getMessage() . "</td></tr>";
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

<!-- JS FILES -->
<script src="js/jquery-3.7.0.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/main.js"></script>
<script src="select2/dist/js/select2.full.min.js"></script>
<script src="js/plugins/jquery.dataTables.min.js"></script>
<script src="js/plugins/dataTables.bootstrap.min.js"></script>

<script>
$(document).ready(function() {
  $('.select2').select2({ width: '100%', placeholder: "Select an option" });
  $('#addModal, #printModal').on('shown.bs.modal', function () {
    $(this).find('.select2').select2({ dropdownParent: $(this), width: '100%' });
  });
  $('#feeTable').DataTable();
});
</script>


<script>
$(document).ready(function() {
  // Initialize Select2
  $('.select2').select2({ width: '100%' });

  // Enable Select2 inside modals
  $('#addModal, #printModal').on('shown.bs.modal', function() {
    $(this).find('.select2').select2({ dropdownParent: $(this) });
  });

  // Datatable
  $('#feeTable').DataTable();

  // Edit Fee Modal trigger
  $(document).on('click', '.editFeeBtn', function() {
    var id = $(this).data('id');
    var amount = $(this).data('amount');
    var className = $(this).data('class');

    $('#editFeeId').val(id);
    $('#editClassName').val(className);
    $('#editAmount').val(amount);
    $('#editModal').modal('show');
  });
});
</script>

</body>
</html>
