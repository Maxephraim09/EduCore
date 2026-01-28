<?php
chdir('../');
session_start();

require_once('db/config.php');
require_once('const/school.php');
require_once('const/check_session.php');
require_once('tcpdf/tcpdf.php');
require_once('const/calculations.php');

if ($res != "1" || $level != "1" || !isset($_GET['term'], $_GET['std'])) {
    header("location:../");
    exit;
}

$term = (int)$_GET['term'];
$std  = (int)$_GET['std'];

try {
    $conn = new PDO(
        "mysql:host=".DBHost.";dbname=".DBName.";charset=utf8",
        DBUser,
        DBPass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    /* ================= STUDENT ================= */
    $stmt = $conn->prepare("SELECT * FROM tbl_students WHERE id=?");
    $stmt->execute([$std]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$student) exit;

    $fullname = trim($student['fname'].' '.$student['mname'].' '.$student['lname']);
    $gender   = $student['gender'];
    $photo    = $student['display_image'] ?: 'DEFAULT';

    /* ================= TERM ================= */
    $stmt = $conn->prepare("SELECT * FROM tbl_terms WHERE id=?");
    $stmt->execute([$term]);
    $termRow = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$termRow) exit;

    /* ================= SESSION ================= */
    $stmt = $conn->prepare("SELECT * FROM tbl_sessions WHERE is_active=1 LIMIT 1");
    $stmt->execute();
    $sessionRow = $stmt->fetch(PDO::FETCH_ASSOC);

    /* ================= RESULTS ================= */
    $stmt = $conn->prepare("
        SELECT r.*, s.name AS subject_name, c.name AS class_name
        FROM tbl_results r
        JOIN tbl_subjects s ON r.subject_id=s.id
        JOIN tbl_classes c ON r.class_id=c.id
        WHERE r.student_id=? AND r.term_id=?
        ORDER BY s.name
    ");
    $stmt->execute([$std, $term]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (!$results) exit;

    $class_id   = $results[0]['class_id'];
    $class_name = $results[0]['class_name'];

    /* ================= CLASS POSITION ================= */
    $stmt = $conn->prepare("
        SELECT student_id, SUM(total_score) total
        FROM tbl_results
        WHERE class_id=? AND term_id=?
        GROUP BY student_id
        ORDER BY total DESC
    ");
    $stmt->execute([$class_id, $term]);
    $rankings = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $position = 1;
    foreach ($rankings as $k => $r) {
        if ($r['student_id'] == $std) {
            $position = $k + 1;
            break;
        }
    }

    /* ================= LAST TERM ================= */
    $stmt = $conn->prepare("
        SELECT id FROM tbl_terms 
        WHERE id < ? ORDER BY id DESC LIMIT 1
    ");
    $stmt->execute([$term]);
    $lastTerm = $stmt->fetch(PDO::FETCH_ASSOC);
    $last_term_id = $lastTerm['id'] ?? null;

    $lastTermScores = [];
    if ($last_term_id) {
        $stmt = $conn->prepare("
            SELECT subject_id, total_score
            FROM tbl_results
            WHERE student_id=? AND term_id=?
        ");
        $stmt->execute([$std, $last_term_id]);
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $lt) {
            $lastTermScores[$lt['subject_id']] = $lt['total_score'];
        }
    }

    /* ================= SUBJECT POSITION FUNCTION ================= */
    function subject_position($conn, $class_id, $subject_id, $term_id, $student_id) {
        $stmt = $conn->prepare("
            SELECT student_id 
            FROM tbl_results
            WHERE class_id=? AND subject_id=? AND term_id=?
            ORDER BY total_score DESC
        ");
        $stmt->execute([$class_id, $subject_id, $term_id]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($rows as $i => $r) {
            if ($r['student_id'] == $student_id) {
                return $i + 1;
            }
        }
        return '-';
    }

} catch (PDOException $e) {
    die($e->getMessage());
}

/* ================= TCPDF ================= */
$pdf = new TCPDF('P','mm','A4',true,'UTF-8',false);
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->SetMargins(10,10,10);
$pdf->AddPage();
$pdf->SetFont('helvetica','',9);

/* ================= WATERMARK ================= */
$pdf->SetAlpha(0.05);
$pdf->Image('images/logo/'.WBLogo,30,70,150);
$pdf->SetAlpha(1);

/* ================= IMAGES ================= */
$logo = 'images/logo/'.WBLogo;
$passport = ($photo=="DEFAULT") ? "images/students/$gender.png" : "images/students/$photo";

/* ================= HEADER ================= */
$pdf->writeHTML("
<table border='1' cellpadding='6'>
<tr>
<td width='15%' align='center'><img src='$logo' width='70'></td>
<td width='70%' align='center'>
<b style='font-size:15px;'>".WBName."</b><br>
".WBAddress."<br>
<b>STUDENT REPORT CARD</b><br>
{$termRow['name']} | {$sessionRow['session_name']}
</td>
<td width='15%' align='center'><img src='$passport' width='70'></td>
</tr>
</table>
");

/* ================= STUDENT INFO ================= */
$pdf->Ln(3);
$pdf->writeHTML("
<table border='1' cellpadding='5'>
<tr>
<td><b>Name:</b> $fullname</td>
<td><b>Adm No:</b> $std</td>
<td><b>Class:</b> $class_name</td>
</tr>
<tr>
<td><b>Gender:</b> $gender</td>
<td><b>Position:</b> $position</td>
<td><b>Total Subjects:</b> ".count($results)."</td>
</tr>
</table>
");

/* ================= SUBJECT TABLE ================= */
$pdf->Ln(3);

$html = "
<table border='1' cellpadding='4'>
<tr bgcolor='#eeeeee'>
<th>#</th>
<th>Subject</th>
<th>1st CA</th>
<th>2nd CA</th>
<th>3rd CA</th>
<th>Total CA</th>
<th>Exam</th>
<th>Total</th>
<th>Last Term</th>
<th>Pos</th>
<th>Grade</th>
<th>Remark</th>
</tr>";

$n=1; $grand=0; $subs=[];

foreach ($results as $r) {
    $ca1 = (int)$r['first_test'];
    $ca2 = (int)$r['second_test'];
    $ca3 = (int)$r['third_test'];
    $total_ca = $ca1 + $ca2 + $ca3;
    $exam = (int)$r['exam_score'];
    $total = (int)$r['total_score'];

    $grand += $total;
    $subs[] = $total;

    $last = $lastTermScores[$r['subject_id']] ?? '-';
    $pos  = subject_position($conn, $class_id, $r['subject_id'], $term, $std);

    $html .= "
    <tr>
        <td>$n</td>
        <td>{$r['subject_name']}</td>
        <td align='center'>$ca1</td>
        <td align='center'>$ca2</td>
        <td align='center'>$ca3</td>
        <td align='center'>$total_ca</td>
        <td align='center'>$exam</td>
        <td align='center'>$total</td>
        <td align='center'>$last</td>
        <td align='center'>$pos</td>
        <td align='center'>{$r['grade']}</td>
        <td align='center'>{$r['remark']}</td>
    </tr>";
    $n++;
}
$html .= "</table>";
$pdf->writeHTML($html);

/* ================= SUMMARY ================= */
$avg = round($grand / count($subs), 1);

$pdf->Ln(3);
$pdf->writeHTML("
<table border='1' cellpadding='5'>
<tr>
<th>Total</th><th>Average</th><th>Division</th><th>Points</th>
</tr>
<tr>
<td align='center'>$grand</td>
<td align='center'>$avg</td>
<td align='center'>".get_division($subs)."</td>
<td align='center'>".get_points($subs)."</td>
</tr>
</table>
");

/* ================= COMMENTS ================= */
$teacher_comment = ($avg >= 70) ? "Excellent performance. Keep it up." :
                  (($avg >= 50) ? "Good effort. Can do better." :
                  "Needs serious improvement.");

$pdf->Ln(3);
$pdf->writeHTML("
<table border='1' cellpadding='6'>
<tr>
<td width='50%'><b>Teacher's Comment:</b><br>$teacher_comment</td>
<td width='50%'><b>Principal's Comment:</b><br>Promising result. Stay focused.</td>
</tr>
</table>
");

ob_end_clean();
$pdf->Output("Report_Card_$std.pdf", "I");
