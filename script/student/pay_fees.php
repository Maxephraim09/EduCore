<?php
chdir('../');
session_start();
require_once('db/config.php');
require_once('const/school.php');
require_once('const/check_session.php');

// Only allow students (level 3)
if (!($res == "1" && $level == "3")) {
    header("location:../");
    exit;
}

$error = '';
$amount_to_pay = 0;
$paid_record = null;

// Default values
$page_title = "Pay Fees";
$site_name = "Site Name";
$school_logo = "images/default-logo.png"; // fallback logo
$favicon = "images/icon.png"; // fallback favicon
$current_session = "";

try {
    $conn = new PDO("mysql:host=".DBHost.";dbname=".DBName.";charset=".DBCharset, DBUser, DBPass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

     // Fetch school logo from tbl_school
    $stmt = $conn->prepare("SELECT logo FROM tbl_school LIMIT 1");
    $stmt->execute();
    $logo_file = $stmt->fetchColumn();
    if ($logo_file) {
        $school_logo = "images/logo/" . $logo_file;
        $favicon = $school_logo; // use logo as favicon if you like
    }

        // Fetch site name
    $stmt = $conn->prepare("SELECT site_name FROM tbl_site_settings LIMIT 1");
    $stmt->execute();
    $site_name = $stmt->fetchColumn() ?: "Site Name";


    // Fetch student info
    $stmt = $conn->prepare("SELECT * FROM tbl_students WHERE id = ? LIMIT 1");
    $stmt->execute([$account_id]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);
    if(!$student) throw new Exception("Student not found.");

    $fname = $student['fname'];
    $lname = $student['lname'];
    $gender = $student['gender'];
    $email = $student['email'];
    $img = $student['display_image'];
    $act_class_id = $student['class'];

    // Fetch class name
    $stmt = $conn->prepare("SELECT name FROM tbl_classes WHERE id = ? LIMIT 1");
    $stmt->execute([$act_class_id]);
    $class_row = $stmt->fetch(PDO::FETCH_ASSOC);
    $act_class = $class_row ? $class_row['name'] : 'Unknown';

    // Fetch active session
    $stmt = $conn->query("SELECT * FROM tbl_sessions WHERE is_active = 1 LIMIT 1");
    $current_session = $stmt->fetch(PDO::FETCH_ASSOC);
    if(!$current_session) throw new Exception("No active session found.");

    // Get fees mapping for student's class
    $stmt = $conn->prepare("SELECT amount FROM tbl_fee_mapping WHERE session_id = ? AND class_id = ? LIMIT 1");
    $stmt->execute([$current_session['id'], $act_class_id]);
    $fee = $stmt->fetch(PDO::FETCH_ASSOC);
    $amount_to_pay = $fee ? $fee['amount'] : 0;

    // Check if student has already paid
    $stmt = $conn->prepare("SELECT * FROM tbl_student_fees WHERE student_id = ? AND session_id = ? AND status = 'paid' LIMIT 1");
    $stmt->execute([$account_id, $current_session['id']]);
    $paid_record = $stmt->fetch(PDO::FETCH_ASSOC);

    // If already paid, set amount to 0 to trigger "Download Receipt"
    if($paid_record){
        $amount_to_pay = 0;
    }

    // Get Paystack API keys
    $stmt = $conn->query("SELECT * FROM tbl_paystack_api_settings LIMIT 1");
    $paystack = $stmt->fetch(PDO::FETCH_ASSOC);

} catch(PDOException $e){
    $error = "Database error: " . $e->getMessage();
} catch(Exception $e){
    $error = $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

<!-- SEO -->
<title><?php echo htmlspecialchars($site_name); ?> - <?php echo htmlspecialchars($page_title); ?></title>
<meta name="description" content="<?php echo htmlspecialchars($site_name); ?> Dashboard">
<meta name="keywords" content="dashboard, <?php echo htmlspecialchars($site_name); ?>, admin, management">
<meta name="author" content="MGTechs">
<meta name="robots" content="noindex, nofollow"> <!-- Use noindex for internal dashboards -->
<meta name="language" content="English">
<meta name="revisit-after" content="1 days">
<meta name="distribution" content="global">
<meta name="rating" content="general">

<!-- Social Sharing / Open Graph -->
<meta property="og:title" content="<?php echo htmlspecialchars($site_name); ?> Dashboard">
<meta property="og:description" content="Manage your tasks, students, classes, and settings in <?php echo htmlspecialchars($site_name); ?>">
<meta property="og:type" content="website">
<meta property="og:url" content="<?php echo $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']; ?>">
<meta property="og:image" content="images/logo.png">

<!-- Twitter Card -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?php echo htmlspecialchars($site_name); ?> Dashboard">
<meta name="twitter:description" content="Manage your tasks, students, classes, and settings in <?php echo htmlspecialchars($site_name); ?>">
<meta name="twitter:image" content="images/logo.png">

<!-- Caching & Security -->
<meta http-equiv="cache-control" content="no-cache, no-store, must-revalidate">
<meta http-equiv="expires" content="0">
<meta http-equiv="pragma" content="no-cache">
<meta http-equiv="Content-Security-Policy" content="default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline'">

<!-- Favicon -->
<link rel="icon" href="<?php echo htmlspecialchars($favicon); ?>" type="image/x-icon">

<base href="../">
<link rel="stylesheet" type="text/css" href="css/main.css">
<link rel="icon" href="images/icon.ico">
<link rel="stylesheet" type="text/css" href="cdn.jsdelivr.net/npm/bootstrap-icons%401.10.5/font/bootstrap-icons.css">
<link type="text/css" rel="stylesheet" href="loader/waitMe.css">
</head>
<body class="app sidebar-mini">

<header class="app-header">
    <a class="app-header__logo" href="javascript:void(0);">
        <img src="<?php echo htmlspecialchars($school_logo); ?>" alt="Logo" height="40">MGTechs
    </a>
<a class="app-sidebar__toggle" href="#" data-toggle="sidebar" aria-label="Hide Sidebar"></a>

<ul class="app-nav">

<li class="dropdown"><a class="app-nav__item" href="#" data-bs-toggle="dropdown" aria-label="Open Profile Menu"><i class="bi bi-person fs-4"></i></a>
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
<li><a class="app-menu__item" href="student/submit_enquiry"><i class="app-menu__icon feather icon-user"></i><span class="app-menu__label">Enquiries</span></a></li>
<li><a class="app-menu__item" href="student/#"><i class="app-menu__icon feather icon-file-text"></i><span class="app-menu__label">CBT </span></a></li>

<li><a class="app-menu__item" href="student/results"><i class="app-menu__icon feather icon-file-text"></i><span class="app-menu__label">My Results</span></a></li>
<li><a class="app-menu__item" href="student/grading-system"><i class="app-menu__icon feather icon-award"></i><span class="app-menu__label">Grading System</span></a></li>
<li><a class="app-menu__item" href="student/division-system"><i class="app-menu__icon feather icon-layers"></i><span class="app-menu__label">Division System</span></a></li>
<li><a class="app-menu__item" href="student/pay_fees"><i class="app-menu__icon feather icon-user"></i><span class="app-menu__label">Pay Fees</span></a></li>

</ul>
</aside>


<main class="app-content">
<div class="app-title">
<div>
<h1>Dashboard -  <?= htmlspecialchars($current_session['session_name'] ?? '') ?> Session</h1>
</div>
<div><a href="student/view_enquiries"><button class="btn btn-primary">Submit Enquiry</button></a></div>
</div>
<div class="row justify-content-center">
    <div class="col-md-12">
        <div class="tile text-center p-4 shadow-sm rounded">

            <!-- Student Image -->
            <img src="images/students/<?= $img === 'DEFAULT' ? strtolower($gender).'.png' : htmlspecialchars($img) ?>" 
                 class="student-img mb-3" 
                 style="width: 150px; height: 150px; border-radius: 50%; object-fit: cover; display: block; margin: 0 auto;">

            <!-- Student Name -->
            <h4 class="mb-2"><?= htmlspecialchars($fname.' '.$lname) ?></h4>

            <!-- Class Name -->
            <p class="mb-1"><strong>Class:</strong> <?= htmlspecialchars($act_class) ?></p>

            <!-- Session Name -->
            <p class="mb-1"><strong>Session:</strong> <?= htmlspecialchars($current_session['session_name'] ?? '') ?></p>

            <!-- Amount -->
            <p class="mb-3"><strong>Amount:</strong> ₦<?= number_format($fee['amount'] ?? 0, 2) ?></p>

            <!-- Payment Button / Download Receipt -->
            <?php if($error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php elseif($paid_record): ?>
                <a href="student/download_receipt.php?student_id=<?= $account_id ?>&session_id=<?= $current_session['id'] ?>" 
                   class="btn btn-success btn-lg">Download Receipt</a>
            <?php elseif($amount_to_pay > 0): ?>
                <button class="btn btn-pay btn-lg" id="payBtn" style="background-color: #17a2b8; color: #fff;">Proceed to Pay</button>
            <?php else: ?>
                <div class="alert alert-success">You have no outstanding fees.</div>
            <?php endif; ?>

        </div>
    </div>
</div>
</main>

<script src="js/jquery-3.7.0.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/main.js"></script>
<script src="loader/waitMe.js"></script>
<script src="js/forms.js"></script>
<script type="text/javascript" src="js/plugins/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="js/plugins/dataTables.bootstrap.min.html"></script>
<script type="text/javascript">$('#srmsTable').DataTable({"sort" : false});</script>
<script src="js/sweetalert2@11.js"></script>
<script src="https://js.paystack.co/v1/inline.js"></script>

<?php if($amount_to_pay > 0 && !empty($paystack['api_public_key'])): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const payBtn = document.getElementById('payBtn');
    if(payBtn){
        payBtn.addEventListener('click', function(){
            let handler = PaystackPop.setup({
                key: '<?= $paystack['api_public_key'] ?>',
                email: '<?= $email ?>',
                amount: <?= $amount_to_pay * 100 ?>,
                currency: 'NGN',
                ref: '<?= $account_id . "_" . time() ?>',
                callback: function(response){
                    window.location.href = 'student/verify_payment.php?ref=' + response.reference + '&session_id=<?= $current_session['id'] ?>';
                },
                onClose: function(){
                    alert('Payment cancelled.');
                }
            });
            handler.openIframe();
        });
    }
});
</script>
<?php endif; ?>

</body>
</html>
