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

if(empty($_GET['student_id']) || empty($_GET['session_id'])){
    die("Invalid request.");
}

$student_id = intval($_GET['student_id']);
$session_id = intval($_GET['session_id']);

try {
    $conn = new PDO("mysql:host=".DBHost.";dbname=".DBName.";charset=".DBCharset, DBUser, DBPass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch school LOGO
$stmt = $conn->query("SELECT * FROM tbl_school LIMIT 1");
$school = $stmt->fetch(PDO::FETCH_ASSOC);
$school_logo = $school ? $school['logo'] : 'default_logo.png';


    // Fetch site settings
    $stmt = $conn->query("SELECT * FROM tbl_site_settings LIMIT 1");
    $site = $stmt->fetch(PDO::FETCH_ASSOC);

    // Fetch student info
    $stmt = $conn->prepare("SELECT * FROM tbl_students WHERE id = ? LIMIT 1");
    $stmt->execute([$student_id]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);
    if(!$student) throw new Exception("Student not found.");

    // Fetch class name
    $stmt = $conn->prepare("SELECT name FROM tbl_classes WHERE id = ? LIMIT 1");
    $stmt->execute([$student['class']]);
    $class_row = $stmt->fetch(PDO::FETCH_ASSOC);
    $class_name = $class_row ? $class_row['name'] : 'Unknown';

    // Fetch session
    $stmt = $conn->prepare("SELECT * FROM tbl_sessions WHERE id = ? LIMIT 1");
    $stmt->execute([$session_id]);
    $session = $stmt->fetch(PDO::FETCH_ASSOC);
    if(!$session) throw new Exception("Session not found.");

    // Fetch payment record
    $stmt = $conn->prepare("SELECT * FROM tbl_student_fees WHERE student_id = ? AND session_id = ? AND status = 'paid' LIMIT 1");
    $stmt->execute([$student_id, $session_id]);
    $payment = $stmt->fetch(PDO::FETCH_ASSOC);
    if(!$payment) throw new Exception("No payment record found for this student and session.");

    // Use correct column names
    $amount_paid = $payment['amount'];
    $payment_date = $payment['paid_at'];
    $outstanding_fee = $payment['outstanding'];
    $payment_ref = $payment['payment_reference'];

    // Fetch fee amount for class/session
    $stmt = $conn->prepare("SELECT amount FROM tbl_fee_mapping WHERE session_id = ? AND class_id = ? LIMIT 1");
    $stmt->execute([$session_id, $student['class']]);
    $fee_mapping = $stmt->fetch(PDO::FETCH_ASSOC);
    $total_fee = $fee_mapping ? $fee_mapping['amount'] : 0;

    // Number to words function
    function numberToWords($number) {
    $hyphen      = '-';
    $conjunction = ' and ';
    $separator   = ', ';
    $negative    = 'negative ';
    $decimal     = ' point ';
    $dictionary  = [
        0                   => 'zero',
        1                   => 'one',
        2                   => 'two',
        3                   => 'three',
        4                   => 'four',
        5                   => 'five',
        6                   => 'six',
        7                   => 'seven',
        8                   => 'eight',
        9                   => 'nine',
        10                  => 'ten',
        11                  => 'eleven',
        12                  => 'twelve',
        13                  => 'thirteen',
        14                  => 'fourteen',
        15                  => 'fifteen',
        16                  => 'sixteen',
        17                  => 'seventeen',
        18                  => 'eighteen',
        19                  => 'nineteen',
        20                  => 'twenty',
        30                  => 'thirty',
        40                  => 'forty',
        50                  => 'fifty',
        60                  => 'sixty',
        70                  => 'seventy',
        80                  => 'eighty',
        90                  => 'ninety',
        100                 => 'hundred',
        1000                => 'thousand',
        1000000             => 'million',
        1000000000          => 'billion',
        1000000000000       => 'trillion',
        1000000000000000    => 'quadrillion',
        1000000000000000000 => 'quintillion'
    ];

    if (!is_numeric($number)) return false;

    if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
        // overflow
        trigger_error(
            'numberToWords only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX,
            E_USER_WARNING
        );
        return false;
    }

    if ($number < 0) {
        return $negative . numberToWords(abs($number));
    }

    $string = $fraction = null;

    if (strpos($number, '.') !== false) {
        list($number, $fraction) = explode('.', $number);
    }

    switch (true) {
        case $number < 21:
            $string = $dictionary[$number];
            break;
        case $number < 100:
            $tens   = ((int) ($number / 10)) * 10;
            $units  = $number % 10;
            $string = $dictionary[$tens];
            if ($units) {
                $string .= $hyphen . $dictionary[$units];
            }
            break;
        case $number < 1000:
            $hundreds  = (int) ($number / 100);
            $remainder = $number % 100;
            $string = $dictionary[$hundreds] . ' ' . $dictionary[100];
            if ($remainder) {
                $string .= $conjunction . numberToWords($remainder);
            }
            break;
        default:
            $baseUnit = pow(1000, floor(log($number, 1000)));
            $numBaseUnits = (int) ($number / $baseUnit);
            $remainder = $number % $baseUnit;
            $string = numberToWords($numBaseUnits) . ' ' . $dictionary[$baseUnit];
            if ($remainder) {
                $string .= $remainder < 100 ? $conjunction : $separator;
                $string .= numberToWords($remainder);
            }
            break;
    }

    if (null !== $fraction && is_numeric($fraction)) {
        $string .= $decimal;
        $words = [];
        foreach (str_split((string) $fraction) as $number) {
            $words[] = $dictionary[$number];
        }
        $string .= implode(' ', $words);
    }

    return ucfirst($string);
}


    // Generate QR code URL
    $verification_url = "https://{$site['website']}/verify_receipt.php?ref={$payment_ref}";

} catch(PDOException $e){
    die("Database error: ".$e->getMessage());
} catch(Exception $e){
    die($e->getMessage());
}

// Include QR library
require_once('../script/qrcode/qrcodes/lib/qrlib.php');

//CREATE FOLDER IF NOT EXIST
$qr_dir = __DIR__ . '/../tmp'; // Absolute path
if (!file_exists($qr_dir)) {
    mkdir($qr_dir, 0777, true); // create folder if it doesn't exist
}
$qr_file = $qr_dir . '/qrcode_'.$payment['id'].'.png';


// Generate QR code image
$qr_dir = __DIR__ . '/../../tmp'; // relative to this script
if (!file_exists($qr_dir)) mkdir($qr_dir, 0777, true);

$qr_file = $qr_dir . '/qrcode_'.$payment['id'].'.png';
$qr_url  = '/srms/tmp/qrcode_'.$payment['id'].'.png'; // URL path for browser

require_once('../script/qrcode/qrcodes/lib/qrlib.php');
QRcode::png($verification_url, $qr_file, QR_ECLEVEL_H, 4);



?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Fee Receipt - <?= htmlspecialchars($student['fname'].' '.$student['lname']) ?></title>
<link rel="stylesheet" href="../css/main.css">
<style>
body { font-family: Arial, sans-serif; margin: 20px; background: #f9f9f9; }
.receipt { background: #fff; padding: 30px; max-width: 800px; margin: auto; border: 1px solid #ccc; border-radius: 8px; }
.header { text-align: center; margin-bottom: 20px; }
.header img { max-width: 100px; margin-bottom: 10px; }
.header h1 { margin: 0; font-size: 26px; }
.header p { margin: 2px 0; font-size: 14px; }
.receipt h2 { text-align: center; margin-bottom: 20px; text-decoration: underline; }
.receipt table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
.receipt table td, .receipt table th { padding: 10px; border: 1px solid #333; }
.receipt table th { background: #f2f2f2; text-align: left; }
.student-photo { width: 100px; height: 100px; object-fit: cover; border-radius: 50%; }
.qr { text-align: center; margin-top: 20px; }
.qr img { width: 120px; }
</style>
</head>
<body>

<div class="receipt">
    <div class="header">
        <img src="../images/logo/<?= htmlspecialchars($school_logo) ?>" alt="School Logo" style="max-width:100px;">
        <h1><?= htmlspecialchars($site['site_name']) ?></h1>
        <p><?= htmlspecialchars($site['address']) ?></p>
        <p>Website: <?= htmlspecialchars($site['website']) ?> | Email: <?= htmlspecialchars($site['email']) ?> | Phone: <?= htmlspecialchars($site['phone']) ?></p>
    </div>

    <h2>FEE PAYMENT RECEIPT</h2>

    <table>
        <tr>
            <th>Receipt No.</th>
            <td><?= htmlspecialchars($payment_ref) ?></td>
            <th>Date</th>
            <td><?= date("d-m-Y", strtotime($payment_date)) ?></td>
        </tr>
        <tr>
            <th>Student Photo</th>
            <td colspan="3" style="text-align:center;">
                <img src="../images/students/<?= $student['display_image']=='DEFAULT' ? strtolower($student['gender']).'.png' : htmlspecialchars($student['display_image']) ?>" alt="Student Photo" class="student-photo">
            </td>
        </tr>
        <tr>
            <th>Student Name</th>
            <td><?= htmlspecialchars($student['fname'].' '.$student['lname']) ?></td>
            <th>Class</th>
            <td><?= htmlspecialchars($class_name) ?></td>
        </tr>
        <tr>
            <th>Session</th>
            <td><?= htmlspecialchars($session['session_name']) ?></td>
            <th>Payment For</th>
            <td>School Fees</td>
        </tr>
        <tr>
            <th>Amount Paid</th>
            <td>₦<?= number_format($amount_paid, 2) ?></td>
            <th>Amount in Words</th>
            <td><?= numberToWords($amount_paid) ?> Naira</td>
        </tr>
        <tr>
            <th>Outstanding Fees</th>
            <td colspan="3">₦<?= number_format($outstanding_fee, 2) ?></td>
        </tr>
    </table>

<div class="qr">
    <p>Scan QR code to verify receipt</p>
    <img src="<?= htmlspecialchars($qr_url) ?>" alt="QR Code">
</div>

<div>
<button>Print Receipt</button>
</div>

</div>



</body>
</html>
