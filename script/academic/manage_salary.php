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

    // fetch staff for select2 live search
    $staff_stmt = $conn->query("SELECT id, fname, lname FROM tbl_staff WHERE status = 1 ORDER BY fname, lname");
    $staffs = $staff_stmt->fetchAll(PDO::FETCH_ASSOC);

    // fetch roles for staff_role table
    $role_stmt = $conn->query("SELECT id, name FROM staff_role ORDER BY name");
    $roles = $role_stmt->fetchAll(PDO::FETCH_ASSOC);

    // fetch existing salary mappings
    $salary_stmt = $conn->prepare("
        SELECT m.id, m.staff_id, s.fname, s.lname, m.qualification, r.name AS role_name,
               m.employment_type, m.account_number, m.bank_name, m.bank_code, m.salary_amount, m.status
        FROM tbl_salary_mapping m
        JOIN tbl_staff s ON m.staff_id = s.id
        LEFT JOIN staff_role r ON m.role_id = r.id
        ORDER BY m.id DESC
    ");
    $salary_stmt->execute();
    $salaries = $salary_stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "<div class='text-danger'>".$e->getMessage()."</div>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>MGTechs - Manage Salary</title>
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
    <h1>Salary Mapping</h1>
    <ul class="app-breadcrumb breadcrumb">
      <li class="breadcrumb-item"><button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addSalaryModal"><i class="bi bi-plus-lg"></i> Add Salary</button></li>
      <li class="breadcrumb-item"><a class="btn btn-success btn-sm" href="academic/pay_salaries.php"><i class="bi bi-cash-stack"></i> Pay Salaries</a></li>
    </ul>
  </div>

  <!-- Add Salary Modal -->
  <div class="modal fade" id="addSalaryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header"><h5 class="modal-title">Add Salary Mapping</h5></div>
        <div class="modal-body">
          <form method="POST" action="academic/core/add_salary_mapping.php" class="app_frm">
            <div class="row">
              <div class="col-md-6 mb-2">
                <label>Select Staff</label>
                <select class="form-control select2" name="staff_id" required>
                  <option value="" disabled selected>Select Staff</option>
                  <?php foreach ($staffs as $s): ?>
                    <option value="<?= $s['id']; ?>"><?= htmlspecialchars($s['fname'].' '.$s['lname']); ?></option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="col-md-6 mb-2">
                <label>Qualification</label>
                <select class="form-control" name="qualification" required>
                  <option value="NO FORMAL EDUCATION">NO FORMAL EDUCATION</option>
                  <option value="FSLC">FSLC</option>
                  <option value="SSCE">SSCE</option>
                  <option value="NCE">NCE</option>
                  <option value="ND">ND</option>
                  <option value="HND">HND</option>
                  <option value="BSc">BSc</option>
                  <option value="MSc">MSc</option>
                  <option value="PHD">PHD</option>
                </select>
              </div>

              <div class="col-md-6 mb-2">
                <label>Role</label>
                <select class="form-control select2" name="role_id" required>
                  <option value="" disabled selected>Select Role</option>
                  <?php foreach ($roles as $r): ?>
                    <option value="<?= $r['id']; ?>"><?= htmlspecialchars($r['name']); ?></option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="col-md-6 mb-2">
                <label>Employment Type</label>
                <select class="form-control" name="employment_type" required>
                  <option value="FULL TIME">FULL TIME</option>
                  <option value="PART TIME">PART TIME</option>
                </select>
              </div>

              <div class="col-md-6 mb-2">
                <label>Account Number</label>
                <input type="text" name="account_number" class="form-control" required>
              </div>

              <div class="col-md-6 mb-2">
                <label>Bank Name</label>
                <input type="text" name="bank_name" class="form-control" required>
              </div>

              <div class="col-md-6 mb-2">
                <label>Bank Short Code</label>
                <input type="text" name="bank_code" class="form-control" required>
              </div>

              <div class="col-md-6 mb-2">
                <label>Salary Amount (₦)</label>
                <input type="number" name="salary_amount" class="form-control" step="0.01" required>
              </div>

              <div class="col-md-6 mb-2">
                <label>Status</label>
                <select name="status" class="form-control" required>
                  <option value="Active">Active</option>
                  <option value="Inactive">Inactive</option>
                </select>
              </div>
            </div>

            <div class="text-end mt-3">
              <button type="submit" class="btn btn-primary">Add Salary</button>
              <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Edit Salary Modal -->
  <div class="modal fade" id="editSalaryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header"><h5 class="modal-title">Edit Salary Mapping</h5></div>
        <div class="modal-body">
          <form method="POST" action="academic/core/update_salary.php" id="editSalaryForm">
            <input type="hidden" name="id" id="editSalaryId">
            <div class="row">
              <div class="col-md-6 mb-2">
                <label>Staff</label>
                <input type="text" id="editStaffName" class="form-control" readonly>
              </div>

              <div class="col-md-6 mb-2">
                <label>Qualification</label>
                <select class="form-control" id="editQualification" name="qualification" required>
                  <option value="NO FORMAL EDUCATION">NO FORMAL EDUCATION</option>
                  <option value="FSLC">FSLC</option>
                  <option value="SSCE">SSCE</option>
                  <option value="NCE">NCE</option>
                  <option value="ND">ND</option>
                  <option value="HND">HND</option>
                  <option value="BSc">BSc</option>
                  <option value="MSc">MSc</option>
                  <option value="PHD">PHD</option>
                </select>
              </div>

              <div class="col-md-6 mb-2">
                <label>Role</label>
                <select class="form-control select2" id="editRoleId" name="role_id" required>
                  <option value="" disabled>Select Role</option>
                  <?php foreach ($roles as $r): ?>
                    <option value="<?= $r['id']; ?>"><?= htmlspecialchars($r['name']); ?></option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="col-md-6 mb-2">
                <label>Employment Type</label>
                <select class="form-control" id="editEmploymentType" name="employment_type" required>
                  <option value="FULL TIME">FULL TIME</option>
                  <option value="PART TIME">PART TIME</option>
                </select>
              </div>

              <div class="col-md-6 mb-2">
                <label>Account Number</label>
                <input type="text" name="account_number" id="editAccountNumber" class="form-control" required>
              </div>

              <div class="col-md-6 mb-2">
                <label>Bank Name</label>
                <input type="text" name="bank_name" id="editBankName" class="form-control" required>
              </div>

              <div class="col-md-6 mb-2">
                <label>Bank Short Code</label>
                <input type="text" name="bank_code" id="editBankCode" class="form-control" required>
              </div>

              <div class="col-md-6 mb-2">
                <label>Salary Amount (₦)</label>
                <input type="number" name="salary_amount" id="editSalaryAmount" class="form-control" step="0.01" required>
              </div>

              <div class="col-md-6 mb-2">
                <label>Status</label>
                <select name="status" id="editStatus" class="form-control" required>
                  <option value="Active">Active</option>
                  <option value="Inactive">Inactive</option>
                </select>
              </div>
            </div>

            <div class="text-end mt-3">
              <button type="submit" class="btn btn-primary">Update Salary</button>
              <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Salary Table -->
  <div class="row">
    <div class="col-md-12">
      <div class="tile">
        <div class="tile-body">
          <h3 class="tile-title">Salary Mappings</h3>
          <div class="table-responsive">
            <table class="table table-bordered" id="salaryTable">
              <thead>
                <tr>
                  <th>Staff</th>
                  <th>Qualification</th>
                  <th>Role</th>
                  <th>Employment</th>
                  <th>Account</th>
                  <th>Bank</th>
                  <th>Code</th>
                  <th>Salary (₦)</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($salaries as $row): ?>
                  <tr>
                    <td><?= htmlspecialchars($row['fname'].' '.$row['lname']); ?></td>
                    <td><?= htmlspecialchars($row['qualification']); ?></td>
                    <td><?= htmlspecialchars($row['role_name'] ?? ''); ?></td>
                    <td><?= htmlspecialchars($row['employment_type']); ?></td>
                    <td><?= htmlspecialchars($row['account_number']); ?></td>
                    <td><?= htmlspecialchars($row['bank_name']); ?></td>
                    <td><?= htmlspecialchars($row['bank_code']); ?></td>
                    <td><?= '₦' . number_format($row['salary_amount'], 2); ?></td>
                    <td><?= htmlspecialchars($row['status']); ?></td>
                    <td>
                      <button class="btn btn-sm btn-primary editSalaryBtn"
                              data-id="<?= $row['id']; ?>"
                              data-staff="<?= htmlspecialchars($row['fname'].' '.$row['lname']); ?>"
                              data-qualification="<?= htmlspecialchars($row['qualification']); ?>"
                              data-role="<?= $row['role_name'] ?? ''; ?>"
                              data-roleid="<?= $row['role_id'] ?? ''; ?>"
                              data-employment="<?= htmlspecialchars($row['employment_type']); ?>"
                              data-account="<?= htmlspecialchars($row['account_number']); ?>"
                              data-bank="<?= htmlspecialchars($row['bank_name']); ?>"
                              data-code="<?= htmlspecialchars($row['bank_code']); ?>"
                              data-salary="<?= $row['salary_amount']; ?>"
                              data-status="<?= htmlspecialchars($row['status']); ?>"
                              data-bs-toggle="modal"
                              data-bs-target="#editSalaryModal">
                        <i class="bi bi-pencil"></i> Edit
                      </button>

                      <a href="academic/pay_salary.php?id=<?= $row['id']; ?>" class="btn btn-sm btn-success">
                        <i class="bi bi-cash"></i> Pay
                      </a>
                    </td>
                  </tr>
                <?php endforeach; ?>
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
$(document).ready(function(){
  // initialize select2
  $('.select2').select2({ width: '100%' });

  // ensure select2 in modal works
  $('#addSalaryModal, #editSalaryModal').on('shown.bs.modal', function () {
    $(this).find('.select2').select2({ dropdownParent: $(this) });
  });

  // datatable
  $('#salaryTable').DataTable();

  // populate edit modal
  $(document).on('click', '.editSalaryBtn', function(){
    var btn = $(this);
    $('#editSalaryId').val(btn.data('id'));
    $('#editStaffName').val(btn.data('staff'));
    $('#editQualification').val(btn.data('qualification'));
    // set role select
    $('#editRoleId').val(btn.data('roleid')).trigger('change');
    $('#editEmploymentType').val(btn.data('employment'));
    $('#editAccountNumber').val(btn.data('account'));
    $('#editBankName').val(btn.data('bank'));
    $('#editBankCode').val(btn.data('code'));
    $('#editSalaryAmount').val(btn.data('salary'));
    $('#editStatus').val(btn.data('status'));
    $('#editSalaryModal').modal('show');
  });
});
</script>

</body>
</html>
