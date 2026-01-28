<?php
chdir('../');
session_start();
require_once('db/config.php');
require_once('const/school.php');
require_once('const/check_session.php');

// Only allow academic users (level 1)
if (!($res == "1" && $level == "1")) {
    header("location:../");
    exit;
}

try {
    $conn = new PDO("mysql:host=".DBHost.";dbname=".DBName.";charset=".DBCharset, DBUser, DBPass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 1️⃣ Get site code
    $stmt = $conn->query("SELECT site_code FROM tbl_site_settings LIMIT 1");
    $site = $stmt->fetch(PDO::FETCH_ASSOC);
    $siteCode = $site ? $site['site_code'] : 'XXX';

    // 2️⃣ Generate registration number
    $currentYear = date('Y'); // e.g., 2025
    $yearShort = date('y');   // e.g., 25

    $stmt = $conn->prepare("SELECT COUNT(*) FROM tbl_students WHERE YEAR(created_at) = ?");
    $stmt->execute([$currentYear]);
    $studentsThisYear = $stmt->fetchColumn();

    $serial = str_pad($studentsThisYear + 1, 4, '0', STR_PAD_LEFT);
    $regNo = $siteCode . '/' . $yearShort . '/' . $serial;

    // 3️⃣ Fetch classes for dropdown
    $stmt = $conn->query("SELECT id, name FROM tbl_classes ORDER BY name ASC");
    $classes = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch(PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<title>MGTechs - Register Student</title>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<base href="../">
<link rel="stylesheet" type="text/css" href="css/main.css">
<link rel="icon" href="images/icon.ico">
<link rel="stylesheet" type="text/css" href="cdn.jsdelivr.net/npm/bootstrap-icons%401.10.5/font/bootstrap-icons.css">
<link type="text/css" rel="stylesheet" href="loader/waitMe.css">
<link href="select2/dist/css/select2.min.css" rel="stylesheet" />
</head>
<body class="app sidebar-mini">

<header class="app-header">
<a class="app-header__logo" href="javascript:void(0);">MGTechs</a>
<a class="app-sidebar__toggle" href="#" data-toggle="sidebar" aria-label="Hide Sidebar"></a>
<ul class="app-nav">
<li class="dropdown">
    <a class="app-nav__item" href="#" data-bs-toggle="dropdown" aria-label="Open Profile Menu"><i class="bi bi-person fs-4"></i></a>
    <ul class="dropdown-menu settings-menu dropdown-menu-right">
        <li><a class="dropdown-item" href="academic/profile"><i class="bi bi-person me-2 fs-5"></i> Profile</a></li>
        <li><a class="dropdown-item" href="logout"><i class="bi bi-box-arrow-right me-2 fs-5"></i> Logout</a></li>
    </ul>
</li>
</ul>
</header>

<div class="app-sidebar__overlay" data-toggle="sidebar"></div>
<aside class="app-sidebar">
<div class="app-sidebar__user">
<div>
<p class="app-sidebar__user-name"><?php echo htmlspecialchars($fname.' '.$lname); ?></p>
<p class="app-sidebar__user-designation">Academic</p>
</div>
</div>
<!-- Your sidebar menu here -->
</aside>

<main class="app-content">
<div class="app-title">
<div>
<h1>Register Students</h1>
</div>
</div>

<div class="row">
<div class="col-md-6 center_form">
<div class="tile">
<div class="tile-body">
<h3 class="tile-title">Register Students</h3>

<form enctype="multipart/form-data" action="academic/core/new_student" class="app_frm" method="POST" autocomplete="OFF">
<!-- Registration Number -->
<div class="mb-2">
<label class="form-label">Registration Number</label>
<input name="regno" required class="form-control" type="text" value="<?php echo htmlspecialchars($regNo); ?>" readonly>
</div>

<!-- Names -->
<div class="mb-2">
<label class="form-label">First Name</label>
<input name="fname" required class="form-control" type="text" onkeypress="return lettersOnly(event)" placeholder="Enter first name">
</div>

<div class="mb-2">
<label class="form-label">Middle Name</label>
<input name="mname" required class="form-control" type="text" onkeypress="return lettersOnly(event)" placeholder="Enter middle name">
</div>

<div class="mb-2">
<label class="form-label">Last Name</label>
<input name="lname" required class="form-control" type="text" onkeypress="return lettersOnly(event)" placeholder="Enter last name">
</div>

<!-- Gender -->
<div class="mb-2">
<label class="form-label">Gender</label>
<select class="form-control" name="gender" required>
<option selected disabled value="">Select gender</option>
<option value="Male">Male</option>
<option value="Female">Female</option>
</select>
</div>

<!-- Class -->
<div class="mb-2">
<label class="form-label">Select Class</label>
<select class="form-control select2" name="class" required style="width: 100%;">
<option value="" selected disabled>Select One</option>
<?php foreach($classes as $class): ?>
<option value="<?php echo htmlspecialchars($class['id']); ?>"><?php echo htmlspecialchars($class['name']); ?></option>
<?php endforeach; ?>
</select>
</div>

<!-- Email -->
<div class="mb-2">
<label class="form-label">Email</label>
<input name="email" required class="form-control" type="email" placeholder="Enter email address">
</div>

<!-- Password -->
<div class="mb-2">
<label class="form-label">Password</label>
<input type="password" class="form-control" id="npass" name="password" placeholder="***************">
</div>

<div class="mb-2">
<label class="form-label">Confirm Password</label>
<input type="password" class="form-control" id="cnpass" name="cpassword" placeholder="***************">
</div>

<!-- Display Image -->
<div class="mb-3">
<label class="form-label">Display Image (Optional)</label>
<input name="image" class="form-control" type="file" accept=".png, .jpg, .jpeg">
</div>

<button id="sub_btnp2" class="btn btn-primary app_btn" type="submit">Register Student</button>
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
<script>
$('.select2').select2();

function lettersOnly(evt) {
    var charCode = evt.which ? evt.which : evt.keyCode;
    if ((charCode >= 65 && charCode <= 90) || (charCode >= 97 && charCode <= 122) || charCode === 32) return true;
    return false;
}
</script>
<?php require_once('const/check-reply.php'); ?>
</body>
</html>
