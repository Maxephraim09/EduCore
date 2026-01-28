<?php
chdir('../');
session_start();
require_once('db/config.php');
require_once('const/school.php');
require_once('const/check_session.php');
if ($res == "1" && $level == "2") {} else { header("location:../"); }


// Capture error message from session
$error_msg = $_SESSION['result_error'] ?? '';
unset($_SESSION['result_error']); // remove it after displaying
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<title>SRMS - View Results</title>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<base href="../">
<link rel="stylesheet" type="text/css" href="css/main.css">
<link rel="icon" href="images/icon.ico">
<link rel="stylesheet" type="text/css" href="cdn.jsdelivr.net/npm/bootstrap-icons%401.10.5/font/bootstrap-icons.css">
<link rel="stylesheet" href="cdn.datatables.net/v/bs5/dt-1.13.4/datatables.min.css">
<link rel="stylesheet" href="loader/waitMe.css">
<link rel="stylesheet" href="select2/dist/css/select2.min.css">
</head>

<body class="app sidebar-mini">

<header class="app-header">
  <a class="app-header__logo" href="javascript:void(0);">SRMS</a>
  <a class="app-sidebar__toggle" href="#" data-toggle="sidebar" aria-label="Hide Sidebar"></a>
  <ul class="app-nav">
    <li class="dropdown">
      <a class="app-nav__item" href="#" data-bs-toggle="dropdown" aria-label="Open Profile Menu"><i class="bi bi-person fs-4"></i></a>
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
      <p class="app-sidebar__user-name"><?php echo $fname . ' ' . $lname; ?></p>
      <p class="app-sidebar__user-designation">Teacher</p>
    </div>
  </div>
  <ul class="app-menu">
    <li><a class="app-menu__item" href="teacher"><i class="app-menu__icon feather icon-monitor"></i><span class="app-menu__label">Dashboard</span></a></li>
    <li><a class="app-menu__item" href="teacher/terms"><i class="app-menu__icon feather icon-folder"></i><span class="app-menu__label">Academic Terms</span></a></li>
    <li><a class="app-menu__item" href="teacher/combinations"><i class="app-menu__icon feather icon-book-open"></i><span class="app-menu__label">Subject Combinations</span></a></li>
    <li class="treeview"><a class="app-menu__item" href="javascript:void(0);" data-toggle="treeview"><i class="app-menu__icon feather icon-users"></i><span class="app-menu__label">Students</span><i class="treeview-indicator bi bi-chevron-right"></i></a>
      <ul class="treeview-menu">
        <li><a class="treeview-item" href="teacher/list_students"><i class="icon bi bi-circle-fill"></i> List Students</a></li>
        <li><a class="treeview-item" href="teacher/export_students"><i class="icon bi bi-circle-fill"></i> Export Students</a></li>
      </ul>
    </li>
    <li class="treeview is-expanded"><a class="app-menu__item" href="javascript:void(0);" data-toggle="treeview"><i class="app-menu__icon feather icon-file-text"></i><span class="app-menu__label">Examination Results</span><i class="treeview-indicator bi bi-chevron-right"></i></a>
      <ul class="treeview-menu">
        <li><a class="treeview-item" href="teacher/import_results"><i class="icon bi bi-circle-fill"></i> Import Results</a></li>
        <li><a class="treeview-item active" href="teacher/manage_results"><i class="icon bi bi-circle-fill"></i> View Results</a></li>
      </ul>
    </li>
    <li><a class="app-menu__item" href="teacher/grading-system"><i class="app-menu__icon feather icon-award"></i><span class="app-menu__label">Grading System</span></a></li>
    <li><a class="app-menu__item" href="teacher/division-system"><i class="app-menu__icon feather icon-layers"></i><span class="app-menu__label">Division System</span></a></li>
  </ul>
</aside>

<main class="app-content">
<div class="app-title">
  <div><h1>View Results</h1></div>
</div>

<div class="row">
  <div class="col-md-5 center_form">
    <div class="tile">
      <div class="tile-body">
        <h3 class="tile-title">View Results</h3>
        <!-- Display error message if exists -->
        <?php if($error_msg): ?>
        <div class="alert alert-danger">
            <?php echo htmlspecialchars($error_msg); ?>
        </div>
    <?php endif; ?>
        <form class="app_frm" enctype="multipart/form-data" method="POST" autocomplete="OFF" action="teacher/core/view_results">

          <!-- Academic Session -->
          <div class="mb-3">
            <label class="form-label">Select Academic Session</label>
            <select class="form-control select2" name="session" required>
              <option value="">Select Session</option>
              <?php
              try {
                $conn = new PDO('mysql:host='.DBHost.';dbname='.DBName.';charset='.DBCharset, DBUser, DBPass);
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $stmt = $conn->prepare("SELECT id, session_name FROM tbl_sessions ORDER BY id DESC");
                $stmt->execute();
                $sessions = $stmt->fetchAll(PDO::FETCH_ASSOC);
                foreach ($sessions as $row) {
                  echo '<option value="'.$row['id'].'">'.$row['session_name'].'</option>';
                }
              } catch (PDOException $e) {
                echo "<option>Error loading sessions</option>";
              }
              ?>
            </select>
          </div>

          <!-- Term -->
          <div class="mb-3">
            <label class="form-label">Select Term</label>
            <select class="form-control select2" name="term" required>
              <option value="">Select Term</option>
              <?php
              try {
                $stmt = $conn->prepare("SELECT id, name FROM tbl_terms WHERE status = '1'");
                $stmt->execute();
                $terms = $stmt->fetchAll(PDO::FETCH_ASSOC);
                foreach ($terms as $row) {
                  echo '<option value="'.$row['id'].'">'.$row['name'].'</option>';
                }
              } catch (PDOException $e) {
                echo "<option>Error loading terms</option>";
              }
              ?>
            </select>
          </div>

          <!-- Class -->
          <div class="mb-3">
            <label class="form-label">Select Class</label>
            <select onchange="fetch_subjects(this.value);" class="form-control select2" name="class" id="class_select" required>
              <option value="">Select Class</option>
              <?php
              try {
                $stmt = $conn->prepare("
                  SELECT DISTINCT c.id, c.name 
                  FROM tbl_subject_combinations sc
                  INNER JOIN tbl_classes c ON sc.class_id = c.id
                  WHERE sc.teacher_id = ?
                ");
                $stmt->execute([$account_id]);
                $classes = $stmt->fetchAll(PDO::FETCH_ASSOC);
                foreach ($classes as $row) {
                  echo '<option value="'.$row['id'].'">'.$row['name'].'</option>';
                }
              } catch (PDOException $e) {
                echo "<option>Error loading classes</option>";
              }
              ?>
            </select>
          </div>

          <!-- Subject -->
          <div class="mb-3">
            <label class="form-label">Select Subject</label>
            <select class="form-control" name="subject" id="sub_imp" required>
              <option value="">Select Subject</option>
            </select>
          </div>

          <button type="submit" name="submit" value="1" class="btn btn-primary app_btn">View Results</button>
        </form>
      </div>
    </div>
  </div>
</div>
</main>

<script src="js/jquery-3.7.0.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/main.js"></script>
<script src="loader/waitMe.js"></script>
<script src="js/sweetalert2@11.js"></script>
<script src="js/forms.js"></script>
<script src="select2/dist/js/select2.full.min.js"></script>
<?php require_once('const/check-reply.php'); ?>
<script>
function fetch_subjects(class_id) {
    if (class_id === "") return;

    $.ajax({
        url: "teacher/core/fetch_subjects.php",
        type: "POST",
        data: { class_id: class_id },
        beforeSend: function(){
            $("#sub_imp").html('<option>Loading subjects...</option>');
        },
        success: function(data){
            $("#sub_imp").html(data);
        },
        error: function(){
            $("#sub_imp").html('<option>Error loading subjects</option>');
        }
    });
}

</script>

</body>
</html>
