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

  $paystack = $conn->query("SELECT * FROM tbl_paystack_api_settings ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
  $email = $conn->query("SELECT * FROM email_api_settings ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
  $sms = $conn->query("SELECT * FROM sms_api_settings ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
  die("Database Error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<head>
<title>MGTechs - API Settings</title>
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
    <h1><i class="bi bi-gear"></i> Manage Paystack API</h1>
    <div class="d-flex justify-content-between align-items-center mb-4">
  <div>
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addModal"><i class="bi bi-plus-circle"></i> Add API</button>
    <button onclick="window.print()" class="btn btn-success btn-sm"><i class="bi bi-printer"></i> Print</button>
  </div>
</div>
  </div>

  <div class="tile mb-4">
  <h5>Payment (PayStack)</h5>
  <div class="table-responsive">
    <table class="table table-bordered align-middle">
      <thead>
        <tr><th>#</th><th>API Name</th><th>Secret Key</th><th>Public Key</th><th>Environment</th><th>Date</th><th>Action</th></tr>
      </thead>
      <tbody>
      <?php foreach($paystack as $i=>$p): ?>
        <tr>
          <td><?= $i+1 ?></td>
          <td><?= htmlspecialchars($p['api_name']) ?></td>
          <td><?= htmlspecialchars(substr($p['api_secret_key'],0,15)) ?>...</td>
          <td><?= htmlspecialchars(substr($p['api_public_key'],0,15)) ?>...</td>
          <td><?= ucfirst($p['environment']) ?></td>
          <td><?= $p['created_at'] ?></td>
          <td>
            <button class="btn btn-warning btn-sm editBtn" 
              data-type="paystack"
              data-id="<?= $p['id'] ?>"
              data-name="<?= htmlspecialchars($p['api_name']) ?>"
              data-secret="<?= htmlspecialchars($p['api_secret_key']) ?>"
              data-public="<?= htmlspecialchars($p['api_public_key']) ?>"
              data-env="<?= $p['environment'] ?>">
              <i class="bi bi-pencil"></i>
            </button>
            <a href="academic/core/add_api_setting.php?action=delete&tbl=paystack&id=<?= $p['id'] ?>" 
               onclick="return confirm('Delete this API?')" class="btn btn-danger btn-sm"><i class="bi bi-trash"></i></a>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- EMAIL API SETTINGS -->
<div class="tile mb-4">
  <h5>Email (PHPMailer)</h5>
  <div class="table-responsive">
    <table class="table table-bordered align-middle">
      <thead>
        <tr><th>#</th><th>Host</th><th>Username</th><th>From Email</th><th>From Name</th><th>Port</th><th>Encryption</th><th>Action</th></tr>
      </thead>
      <tbody>
      <?php foreach($email as $i=>$e): ?>
        <tr>
          <td><?= $i+1 ?></td>
          <td><?= htmlspecialchars($e['host']) ?></td>
          <td><?= htmlspecialchars($e['username']) ?></td>
          <td><?= htmlspecialchars($e['from_email']) ?></td>
          <td><?= htmlspecialchars($e['from_name']) ?></td>
          <td><?= $e['port'] ?></td>
          <td><?= strtoupper($e['encryption']) ?></td>
          <td>
            <button class="btn btn-warning btn-sm editBtn"
              data-type="email"
              data-id="<?= $e['id'] ?>"
              data-host="<?= htmlspecialchars($e['host']) ?>"
              data-user="<?= htmlspecialchars($e['username']) ?>"
              data-pass="<?= htmlspecialchars($e['password']) ?>"
              data-fromemail="<?= htmlspecialchars($e['from_email']) ?>"
              data-fromname="<?= htmlspecialchars($e['from_name']) ?>"
              data-port="<?= $e['port'] ?>"
              data-encrypt="<?= $e['encryption'] ?>">
              <i class="bi bi-pencil"></i>
            </button>
            <a href="academic/core/add_api_setting.php?action=delete&tbl=email&id=<?= $e['id'] ?>" 
               onclick="return confirm('Delete this Email API?')" class="btn btn-danger btn-sm"><i class="bi bi-trash"></i></a>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- SMS SETTINGS -->
<div class="tile">
  <h5>SMS (Infobip)</h5>
  <div class="table-responsive">
    <table class="table table-bordered align-middle">
      <thead>
        <tr><th>#</th><th>API Name</th><th>Base URL</th><th>API Key</th><th>Sender ID</th><th>Environment</th><th>Date</th><th>Action</th></tr>
      </thead>
      <tbody>
      <?php foreach($sms as $i=>$s): ?>
        <tr>
          <td><?= $i+1 ?></td>
          <td><?= htmlspecialchars($s['api_name']) ?></td>
          <td><?= htmlspecialchars($s['base_url']) ?></td>
          <td><?= htmlspecialchars(substr($s['api_key'],0,15)) ?>...</td>
          <td><?= htmlspecialchars($s['sender_id']) ?></td>
          <td><?= ucfirst($s['environment']) ?></td>
          <td><?= $s['created_at'] ?></td>
          <td>
            <button class="btn btn-warning btn-sm editBtn"
              data-type="sms"
              data-id="<?= $s['id'] ?>"
              data-name="<?= htmlspecialchars($s['api_name']) ?>"
              data-url="<?= htmlspecialchars($s['base_url']) ?>"
              data-key="<?= htmlspecialchars($s['api_key']) ?>"
              data-sender="<?= htmlspecialchars($s['sender_id']) ?>"
              data-env="<?= $s['environment'] ?>">
              <i class="bi bi-pencil"></i>
            </button>
            <a href="academic/core/add_api_setting.php?action=delete&tbl=sms&id=<?= $s['id'] ?>" 
               onclick="return confirm('Delete this SMS API?')" class="btn btn-danger btn-sm"><i class="bi bi-trash"></i></a>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Add/Edit Modal -->
<div class="modal fade" id="addModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form id="editApiForm" method="POST">
        <div class="modal-header">
          <h5 class="modal-title">Add / Edit API</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="id" id="editId">
          <input type="hidden" name="type" id="apiTypeField">
          <div id="formFields"></div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-success" type="submit">Save</button>
          <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        </div>
      </form>
    </div>
  </div>
</div>


<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Edit button handler
$('.editBtn').on('click', function() {
  const type = $(this).data('type');
  const id = $(this).data('id');
  $('#apiTypeField').val(type);
  $('#editId').val(id);

  let html = '';
  const envOptions = (selected='test') =>
    `<select name="environment" class="form-select">
       <option value="test" ${selected=='test'?'selected':''}>Test</option>
       <option value="live" ${selected=='live'?'selected':''}>Live</option>
     </select>`;

  if (type === 'paystack') {
    html = `
      <div class="mb-3"><label>API Name</label><input name="api_name" class="form-control" value="${$(this).data('name')}"></div>
      <div class="mb-3"><label>Secret Key</label><input name="api_secret_key" class="form-control" value="${$(this).data('secret')}"></div>
      <div class="mb-3"><label>Public Key</label><input name="api_public_key" class="form-control" value="${$(this).data('public')}"></div>
      <div class="mb-3"><label>Environment</label>${envOptions($(this).data('env'))}</div>`;
  } else if (type === 'email') {
    html = `
      <div class="row"><div class="col-md-6 mb-3"><label>Host</label><input name="host" class="form-control" value="${$(this).data('host')}"></div>
      <div class="col-md-6 mb-3"><label>Username</label><input name="username" class="form-control" value="${$(this).data('user')}"></div></div>
      <div class="mb-3"><label>Password</label><input type="password" name="password" class="form-control" value="${$(this).data('pass')}"></div>
      <div class="row"><div class="col-md-6 mb-3"><label>From Email</label><input name="from_email" class="form-control" value="${$(this).data('fromemail')}"></div>
      <div class="col-md-6 mb-3"><label>From Name</label><input name="from_name" class="form-control" value="${$(this).data('fromname')}"></div></div>
      <div class="row"><div class="col-md-6 mb-3"><label>Port</label><input name="port" class="form-control" value="${$(this).data('port')}"></div>
      <div class="col-md-6 mb-3"><label>Encryption</label><input name="encryption" class="form-control" value="${$(this).data('encrypt')}"></div>
      <div class="mb-3"><label>Environment</label>${envOptions($(this).data('env')||'test')}</div>`;
  } else if (type === 'sms') {
    html = `
      <div class="mb-3"><label>API Name</label><input name="api_name" class="form-control" value="${$(this).data('name')}"></div>
      <div class="mb-3"><label>Base URL</label><input name="base_url" class="form-control" value="${$(this).data('url')}"></div>
      <div class="mb-3"><label>API Key</label><input name="api_key" class="form-control" value="${$(this).data('key')}"></div>
      <div class="mb-3"><label>Sender ID</label><input name="sender_id" class="form-control" value="${$(this).data('sender')}"></div>
      <div class="mb-3"><label>Environment</label>${envOptions($(this).data('env'))}</div>`;
  }

  $('#formFields').html(html);
  new bootstrap.Modal(document.getElementById('addModal')).show();
});

// AJAX submit
$('#editApiForm').on('submit', function(e) {
  e.preventDefault();
  const type = $('#apiTypeField').val();
  $.ajax({
    url: 'academic/core/add_api_setting.php', // your PHP handler
    method: 'POST',
    data: $(this).serialize(),
    dataType: 'json',
    success: function(res) {
      if(res.status=='success') {
        alert('API updated successfully!');
        location.reload();
      } else {
        alert('Error: ' + res.message);
      }
    },
    error: function(err) {
      console.error(err);
      alert('Unexpected error occurred.');
    }
  });
});

</script>
</body>
</html>
