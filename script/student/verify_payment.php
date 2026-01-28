<?php
chdir('../');
session_start();
require_once('db/config.php');
require_once('const/school.php');
require_once('const/check_session.php');

if (!($res == "1" && $level == "3")) {
    header("location:../");
    exit;
}

$error = '';
$success = '';
$ref = $_GET['ref'] ?? '';
$session_id = $_GET['session_id'] ?? '';

if (!$ref || !$session_id) {
    $error = "Invalid payment reference or session.";
} else {
    try {
        $conn = new PDO("mysql:host=".DBHost.";dbname=".DBName.";charset=".DBCharset, DBUser, DBPass);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Fetch student info
        $stmt = $conn->prepare("SELECT * FROM tbl_students WHERE id = ? LIMIT 1");
        $stmt->execute([$account_id]);
        $student = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$student) {
            throw new Exception("Student not found.");
        }

        // Get Paystack secret key
        $stmt = $conn->query("SELECT api_secret_key FROM tbl_paystack_api_settings LIMIT 1");
        $paystack = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$paystack || empty($paystack['api_secret_key'])) {
            throw new Exception("Paystack API key not configured.");
        }

        $secret_key = $paystack['api_secret_key'];

        // Verify payment with Paystack
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => "https://api.paystack.co/transaction/verify/$ref",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer $secret_key",
                "Cache-Control: no-cache"
            ],
        ]);
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            throw new Exception("Curl Error: $err");
        }

        $result = json_decode($response, true);

        if (!$result['status'] || $result['data']['status'] != 'success') {
            throw new Exception("Payment verification failed.");
        }

        // Check if payment already exists
        $stmt = $conn->prepare("SELECT * FROM tbl_student_fees WHERE student_id = ? AND session_id = ? LIMIT 1");
        $stmt->execute([$account_id, $session_id]);
        $already_paid = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$already_paid) {
            // Get amount from tbl_fee_mapping
            $stmt = $conn->prepare("SELECT amount FROM tbl_fee_mapping WHERE session_id = ? AND class_id = ? LIMIT 1");
            $stmt->execute([$session_id, $student['class']]);
            $fee = $stmt->fetch(PDO::FETCH_ASSOC);
            $amount = $fee ? $fee['amount'] : 0;

            // Insert payment record
            $stmt = $conn->prepare("
                INSERT INTO tbl_student_fees 
                (student_id, session_id, term_id, class_id, amount, outstanding, status, payment_reference, paid_at, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
            ");
            $stmt->execute([
                $account_id,        // student_id
                $session_id,        // session_id
                0,                  // term_id (0 if not used)
                $student['class'],  // class_id
                $amount,            // amount
                0,                  // outstanding
                'paid',             // status
                $ref                // payment_reference
            ]);
        }

        $success = "Payment successful! Reference: $ref";

    } catch (PDOException $e) {
        $error = "Database error: " . $e->getMessage();
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<head>
<title>MGTechs - Dashboard</title>
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
<li><a class="dropdown-item" href="student/settings"><i class="bi bi-person me-2 fs-5"></i> Change Password</a></li>
<li><a class="dropdown-item" href="logout"><i class="bi bi-box-arrow-right me-2 fs-5"></i> Logout</a></li>
</ul>
</li>
</ul>
</header>

<div class="app-sidebar__overlay" data-toggle="sidebar"></div>
<aside class="app-sidebar">
<div class="app-sidebar__user">
<div>
<p class="app-sidebar__user-name"><?php echo $fname.' '.$lname; ?></p>
<p class="app-sidebar__user-designation">Student</p>
</div>
</div>
<ul class="app-menu">
<li><a class="app-menu__item active" href="student"><i class="app-menu__icon feather icon-monitor"></i><span class="app-menu__label">Dashboard</span></a></li>
<li><a class="app-menu__item" href="student/view"><i class="app-menu__icon feather icon-user"></i><span class="app-menu__label">My Profile</span></a></li>
<li><a class="app-menu__item" href="student/subjects"><i class="app-menu__icon feather icon-book-open"></i><span class="app-menu__label">My Subjects</span></a></li>
<li><a class="app-menu__item" href="student/#"><i class="app-menu__icon feather icon-file-text"></i><span class="app-menu__label">My Lession TimeTable</span></a></li>
<li><a class="app-menu__item" href="student/#"><i class="app-menu__icon feather icon-file-text"></i><span class="app-menu__label">Academic Calandar</span></a></li>
<li><a class="app-menu__item" href="student/#"><i class="app-menu__icon feather icon-file-text"></i><span class="app-menu__label">My Assignment</span></a></li>
<li><a class="app-menu__item" href="student/#"><i class="app-menu__icon feather icon-file-text"></i><span class="app-menu__label">CBT </span></a></li>

<li><a class="app-menu__item" href="student/results"><i class="app-menu__icon feather icon-file-text"></i><span class="app-menu__label">My Results</span></a></li>
<li><a class="app-menu__item" href="student/grading-system"><i class="app-menu__icon feather icon-award"></i><span class="app-menu__label">Grading System</span></a></li>
<li><a class="app-menu__item" href="student/division-system"><i class="app-menu__icon feather icon-layers"></i><span class="app-menu__label">Division System</span></a></li>
<li><a class="app-menu__item" href="student/submit_enquiry"><i class="app-menu__icon feather icon-user"></i><span class="app-menu__label">Enquiries</span></a></li>
<li><a class="app-menu__item" href="student/pay_fees"><i class="app-menu__icon feather icon-user"></i><span class="app-menu__label">Pay Fees</span></a></li>

</ul>
</aside>

<main class="app-content">
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="tile text-center p-4 shadow-sm rounded">
            <?php if($error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php else: ?>
                <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>
            <a href="student/pay_fees" class="btn btn-primary mt-3">Back to Fees Page</a>
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

</body>
</html>
