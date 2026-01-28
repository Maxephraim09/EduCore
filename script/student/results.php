<?php
chdir('../');
session_start();
require_once('db/config.php');
require_once('const/school.php');
require_once('const/check_session.php');
require_once('const/calculations.php');

// Ensure student access
if ($res != "1" || $level != "3") { 
    header("location:../"); 
    exit; 
}

$student_id = $account_id; 
$student_class = $class;

// Default values
$page_title = "View Enquiries";
$site_name = "Site Name";
$school_logo = "images/default-logo.png"; // fallback logo
$favicon = "images/icon.png"; // fallback favicon
$current_session = "";

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
    // Fetch current active session
    $stmt = $conn->prepare("SELECT session_name FROM tbl_sessions WHERE is_active = 1 LIMIT 1");
    $stmt->execute();
    $current_session = $stmt->fetchColumn() ?: "No Active Session";
    

// Fetch grading system
$stmt = $conn->prepare("SELECT * FROM tbl_grade_system");
$stmt->execute();
$grading = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch available sessions and terms
$sessions = $conn->query("SELECT * FROM tbl_sessions ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
$terms = $conn->query("SELECT * FROM tbl_terms WHERE status = 1 ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);

// Selected session and term from form or default
$selected_session = $_POST['session_id'] ?? $sessions[0]['id'];
$selected_term = $_POST['term_id'] ?? $terms[0]['id'];

// Check if admin allowed viewing for this session and term
$stmt = $conn->prepare("SELECT status FROM tbl_view_result WHERE session_id = ? AND term_id = ?");
$stmt->execute([$selected_session, $selected_term]);
$viewing_status = $stmt->fetchColumn();
$viewing_status = $viewing_status !== false ? $viewing_status : 0;
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
<h1>Dashboard - <?php echo htmlspecialchars($current_session); ?> Session</h1>
</div>
<div><a href="student/view_enquiries"><button class="btn btn-primary">Submit Enquiry</button></a></div>
</div>

<div class="row">
<div class="col-md-12">
<div class="tile">
<h4 class="tile-title">Select Session & Term</h4>

<form method="POST" class="mb-3">
    <div class="row g-2">
        <div class="col-md-4">
            <select name="session_id" class="form-control" required>
                <?php foreach($sessions as $session){ ?>
                    <option value="<?php echo $session['id']; ?>" <?php if($selected_session==$session['id']) echo 'selected'; ?>>
                        <?php echo $session['session_name']; ?>
                    </option>
                <?php } ?>
            </select>
        </div>
        <div class="col-md-4">
            <select name="term_id" class="form-control" required>
                <?php foreach($terms as $term){ ?>
                    <option value="<?php echo $term['id']; ?>" <?php if($selected_term==$term['id']) echo 'selected'; ?>>
                        <?php echo $term['name']; ?>
                    </option>
                <?php } ?>
            </select>
        </div>
        <div class="col-md-4">
            <button type="submit" class="btn btn-primary w-100">View Result</button>
        </div>
    </div>
</form>

<?php
if ($viewing_status != 1) {
    echo '<div class="alert alert-warning">RESULT NOT READY FOR VIEWING OR ADMIN HAS DISABLED VIEWING. PLEASE CHECK BACK LATER.</div>';
} else {
    try {
        $stmt = $conn->prepare("
            SELECT r.*, s.name AS subject_name
            FROM tbl_results r
            JOIN tbl_subjects s ON r.subject_id = s.id
            WHERE r.student_id = ? AND r.class_id = ? AND r.session_id = ? AND r.term_id = ?
        ");
        $stmt->execute([$student_id, $student_class, $selected_session, $selected_term]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($results) == 0) {
            echo '<div class="alert alert-info">You do not have results for the selected session and term.</div>';
        } else {
            echo '<table class="table table-bordered table-striped table-sm">';
            echo '<thead><tr><th>#</th><th>Subject</th><th>Score</th><th>Grade</th><th>Remark</th></tr></thead><tbody>';

            $n = 1; 
            $total_score = 0; 
            $scores_arr = [];

            foreach ($results as $row) {
                $score = $row['total_score'];
                $grade = $remark = 'N/A';

                foreach ($grading as $g) {
                    if ($score >= $g['min'] && $score <= $g['max']) {
                        $grade = $g['name'];
                        $remark = $g['remark'];
                        break;
                    }
                }

                echo '<tr>';
                echo '<td>'.$n.'</td>';
                echo '<td>'.htmlspecialchars($row['subject_name']).'</td>';
                echo '<td align="center">'.$score.'%</td>';
                echo '<td align="center">'.$grade.'</td>';
                echo '<td align="center">'.$remark.'</td>';
                echo '</tr>';

                $total_score += $score;
                $scores_arr[] = $score;
                $n++;
            }

            $average = count($results) ? round($total_score / count($results)) : 0;
            $final_grade = $final_remark = 'N/A';
            foreach ($grading as $g) {
                if ($average >= $g['min'] && $average <= $g['max']) {
                    $final_grade = $g['name'];
                    $final_remark = $g['remark'];
                    break;
                }
            }

            echo '</tbody></table>';
            echo '<p>';
            echo 'TOTAL SCORE: <span class="badge bg-secondary">'.$total_score.'</span> ';
            echo 'AVERAGE: <span class="badge bg-secondary">'.$average.'</span> ';
            echo 'GRADE: <span class="badge bg-secondary">'.$final_grade.'</span> ';
            echo 'REMARK: <span class="badge bg-secondary">'.$final_remark.'</span> ';
            echo 'DIVISION: <span class="badge bg-secondary">'.get_division($scores_arr).'</span> ';
            echo 'POINTS: <span class="badge bg-secondary">'.get_points($scores_arr).'</span>';
            echo '</p>';

            echo '<a target="_blank" href="student/save_pdf?session='.$selected_session.'&term='.$selected_term.'" class="btn btn-primary btn-sm">Download Result</a>';
        }

    } catch(PDOException $e) {
        echo '<div class="alert alert-danger">Error fetching results: '.$e->getMessage().'</div>';
    }
}
?>

</div>
</div>
</div>
</main>

<script src="js/jquery-3.7.0.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/main.js"></script>
<script src="loader/waitMe.js"></script>
</body>
</html>
