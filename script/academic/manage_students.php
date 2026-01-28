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
?>
<!DOCTYPE html>
<html lang="en">
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<head>
<title>MGTechs - Manage Students</title>
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
            <a class="app-nav__item" href="#" data-bs-toggle="dropdown" aria-label="Open Profile Menu">
                <i class="bi bi-person fs-4"></i>
            </a>
            <ul class="dropdown-menu settings-menu dropdown-menu-right">
                <li><a class="dropdown-item" href="academic/profile"><i class="bi bi-person me-2 fs-5"></i> Profile</a></li>
                <li><a class="dropdown-item" href="logout"><i class="bi bi-box-arrow-right me-2 fs-5"></i> Logout</a></li>
            </ul>
        </li>
    </ul>
</header>

<!--========== START SIDE NAVS =================-->
<div class="app-sidebar__overlay" data-toggle="sidebar"></div>
<aside class="app-sidebar">
    <div class="app-sidebar__user">
        <div>
            <p class="app-sidebar__user-name"><?php echo $fname.' '.$lname; ?></p>
            <p class="app-sidebar__user-designation">Academic</p>
        </div>
    </div>
    <ul class="app-menu">
        <!-- Dashboard -->
        <li><a class="app-menu__item active" href="academic"><i class="app-menu__icon feather icon-monitor"></i><span class="app-menu__label">Dashboard</span></a></li>
        
        <!-- Add your other sidebar menus here (unchanged from your original code) -->
    </ul>
</aside>
<!--========== END SIDE NAVS =================-->

<main class="app-content">
    <div class="app-title">
        <div>
            <h1>Manage Students</h1>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4 center_form">
            <div class="tile">
                <div class="tile-body">
                    <div class="table-responsive">
                        <h3 class="tile-title">Manage Students</h3>
                        <form class="app_frm" method="POST" autocomplete="OFF" action="academic/core/list_students">

                            <div class="mb-2">
                                <label class="form-label">Select Class</label>
                                <select multiple="true" class="form-control select2" name="class[]" required style="width: 100%;">
                                <?php
                                try {
                                    $conn = new PDO('mysql:host='.DBHost.';dbname='.DBName.';charset='.DBCharset, DBUser, DBPass);
                                    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                                    $stmt = $conn->prepare("SELECT * FROM tbl_classes ORDER BY name ASC");
                                    $stmt->execute();
                                    $classes = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                    foreach($classes as $class) {
                                        echo '<option value="'.$class['id'].'">'.htmlspecialchars($class['name']).'</option>';
                                    }


                                } catch(PDOException $e) {
                                    echo '<option disabled>Connection failed: ' . $e->getMessage() . '</option>';
                                }
                                ?>
                                </select>
                            </div>

                            <button type="submit" name="submit" value="1" class="btn btn-primary app_btn">Manage Students</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script src="js/jquery-3.7.0.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/main.js"></script>
<script src="loader/waitMe.js"></script>
<script src="js/forms.js"></script>
<script src="js/sweetalert2@11.js"></script>
<script src="select2/dist/js/select2.full.min.js"></script>
<?php require_once('const/check-reply.php'); ?>
<script>
$(document).ready(function() {
    $('.select2').select2({
        placeholder: "Select class(es)",
        allowClear: true
    });
});
</script>
</body>
</html>
