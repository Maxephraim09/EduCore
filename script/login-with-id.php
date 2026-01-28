<?php
session_start();
require_once('db/config.php');
require_once('const/school.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>SRMS - Login with ID</title>
<link rel="stylesheet" href="css/main.css">
<link rel="icon" href="images/icon.ico">
<link rel="stylesheet" href="cdn.jsdelivr.net/npm/bootstrap-icons%401.10.5/font/bootstrap-icons.css">
<link rel="stylesheet" href="loader/waitMe.css">
</head>
<body>

<section class="login-content">
<div class="login-box">

<form class="login-form app_frm" action="core/auth-id.php" method="POST" autocomplete="OFF">
<center><img height="140" src="images/logo/<?php echo WBLogo; ?>"></center>
<h4 class="login-head"><?php echo WBName; ?></h4>
<p class="text-center">Login Using Your ID Number</p>

<div class="mb-3">
<label class="form-label">ID Number</label>
<input class="form-control" type="text" placeholder="Enter Your ID Number" required name="id_number">
</div>

<div class="mb-3">
<label class="form-label">Password</label>
<input class="form-control" type="password" placeholder="Login Password" required name="password">
</div>

<div class="mb-3 btn-container d-grid">
<button type="submit" class="btn btn-primary btn-block app_btn">
<i class="bi bi-box-arrow-in-right me-2 fs-5"></i>LOGIN
</button>
</div>
</form>

</div>
</section>

<script src="js/jquery-3.7.0.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/main.js"></script>
<script src="loader/waitMe.js"></script>
<script src="js/forms.js"></script>
<script src="js/sweetalert2@11.js"></script>
<?php require_once('const/check-reply.php'); ?>
</body>
</html>
