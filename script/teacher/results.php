<?php
chdir('../');
session_start();
require_once('db/config.php');
require_once('const/school.php');
require_once('const/check_session.php');

if ($res == "1" && $level == "2") {} else { header("location:../"); }

if (!isset($_SESSION['result__data'])) {
  header("location:./");
  exit;
}

$term = $_SESSION['result__data']['term'];
$class = $_SESSION['result__data']['class'];
$subject = $_SESSION['result__data']['subject'];
$session_id = $_SESSION['result__data']['session'];

try {
  $conn = new PDO('mysql:host='.DBHost.';dbname='.DBName.';charset='.DBCharset, DBUser, DBPass);
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  // Fetch labels
  $term_name = $conn->query("SELECT name FROM tbl_terms WHERE id = $term")->fetchColumn();
  $class_name = $conn->query("SELECT name FROM tbl_classes WHERE id = $class")->fetchColumn();
  $subject_name = $conn->query("SELECT name FROM tbl_subjects WHERE id = $subject")->fetchColumn();

  $title = "$subject_name - $term_name - $class_name Results";

  // Fetch students in the class
  $students = $conn->prepare("SELECT * FROM tbl_students WHERE class = ? ORDER BY lname ASC");
  $students->execute([$class]);
  $student_list = $students->fetchAll(PDO::FETCH_ASSOC);

  // Fetch grading
  $grading = $conn->query("SELECT * FROM tbl_grade_system")->fetchAll(PDO::FETCH_ASSOC);

  // Handle form submit
  if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_scores'])) {
    foreach ($_POST['student_id'] as $index => $student_id) {
      $first = $_POST['first_test'][$index];
      $second = $_POST['second_test'][$index];
      $third = $_POST['third_test'][$index];
      $exam = $_POST['exam_score'][$index];

      $total = $first + $second + $third + $exam;
      $grade = 'N/A';
      $remark = 'N/A';

      foreach ($grading as $g) {
        if ($total >= $g['min'] && $total <= $g['max']) {
          $grade = $g['name'];
          $remark = $g['remark'];
          break;
        }
      }

      // Check if record exists
      $check = $conn->prepare("SELECT id FROM tbl_results WHERE student_id=? AND class_id=? AND subject_id=? AND session_id=? AND term_id=?");
      $check->execute([$student_id, $class, $subject, $session_id, $term]);

      if ($check->rowCount() > 0) {
        // Update
        $update = $conn->prepare("UPDATE tbl_results 
          SET first_test=?, second_test=?, third_test=?, exam_score=?, total_score=?, grade=?, remark=?, updated_at=NOW()
          WHERE student_id=? AND class_id=? AND subject_id=? AND session_id=? AND term_id=?");
        $update->execute([$first, $second, $third, $exam, $total, $grade, $remark, $student_id, $class, $subject, $session_id, $term]);
      } else {
        // Insert
        $insert = $conn->prepare("INSERT INTO tbl_results (student_id, class_id, subject_id, session_id, term_id, first_test, second_test, third_test, exam_score, total_score, grade, remark, created_at)
          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
        $insert->execute([$student_id, $class, $subject, $session_id, $term, $first, $second, $third, $exam, $total, $grade, $remark]);
      }
    }
    $msg = "Scores saved successfully!";
  }

} catch (PDOException $e) {
  die("Connection failed: " . $e->getMessage());
}
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
    <div><h1><?php echo $title; ?></h1></div>
  </div>

  <div class="row">
  <div class="col-md-12 center_form">
    <div class="tile">
      <div class="tile-body">
        <h3 class="tile-title">View Results</h3>

  <?php if (!empty($msg)) { ?>
  <div class="alert alert-success"><?php echo $msg; ?></div>
  <?php } ?>

  <form method="POST">
    <div class="table-responsive">
      <table class="table table-bordered table-hover" id="resultTable">
        <thead>
          <tr>
            <th>#</th>
            <th>Student Name</th>
            <th>1st Test</th>
            <th>2nd Test</th>
            <th>3rd Test</th>
            <th>Exam</th>
            <th>Total</th>
            <th>Grade</th>
            <th>Remark</th>
          </tr>
        </thead>
        <tbody>
        <?php
        $sn = 1;
        foreach ($student_list as $stu) {
          // fetch existing record if available
          $check = $conn->prepare("SELECT * FROM tbl_results WHERE student_id=? AND class_id=? AND subject_id=? AND session_id=? AND term_id=?");
          $check->execute([$stu['id'], $class, $subject, $session_id, $term]);
          $res = $check->fetch(PDO::FETCH_ASSOC);

          $first = $res['first_test'] ?? '';
          $second = $res['second_test'] ?? '';
          $third = $res['third_test'] ?? '';
          $exam = $res['exam_score'] ?? '';
          $total = $res['total_score'] ?? '';
          $grade = $res['grade'] ?? '';
          $remark = $res['remark'] ?? '';
        ?>
        <tr>
          <td><?php echo $sn++; ?></td>
          <td>
            <?php echo $stu['lname'].' '.$stu['fname'].' '.$stu['mname']; ?>
            <input type="hidden" name="student_id[]" value="<?php echo $stu['id']; ?>">
          </td>
          <td><input type="number" name="first_test[]" value="<?php echo $first; ?>" class="form-control" min="0" max="10"></td>
          <td><input type="number" name="second_test[]" value="<?php echo $second; ?>" class="form-control" min="0" max="10"></td>
          <td><input type="number" name="third_test[]" value="<?php echo $third; ?>" class="form-control" min="0" max="10"></td>
          <td><input type="number" name="exam_score[]" value="<?php echo $exam; ?>" class="form-control" min="0" max="70"></td>
          <td><?php echo $total; ?></td>
          <td><?php echo $grade; ?></td>
          <td><?php echo $remark; ?></td>
        </tr>
        <?php } ?>
        </tbody>
      </table>
    </div>
    <button type="submit" name="save_scores" class="btn btn-primary mt-3">Save Scores</button>
  </form>
</main>

<script src="js/jquery-3.7.0.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/main.js"></script>
<script type="text/javascript" src="js/plugins/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="js/plugins/dataTables.bootstrap.min.js"></script>
<script>$('#resultTable').DataTable({"sort": false});</script>

</body>
</html>
