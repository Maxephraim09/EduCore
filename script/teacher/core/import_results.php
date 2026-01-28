<?php
chdir('../../');
session_start();
require_once('db/config.php');
require_once('const/check_session.php');
require_once(__DIR__ . '/../../../vendor/autoload.php');
 // PhpSpreadsheet autoload
use PhpOffice\PhpSpreadsheet\IOFactory;    // <-- move 'use' here

if(!($res == "1" && $level == "2")) {
    header("location:../../");
    exit;
}



if (!isset($_SESSION['account_id'])) exit('Unauthorized');

if (isset($_POST['submit'])) {

    $term_id    = $_POST['term_id'];
    $class_id   = $_POST['class_id'];
    $subject_id = $_POST['subject_id'];
    $session_id = $_SESSION['active_session_id']; // active session

    try {
        $conn = new PDO("mysql:host=".DBHost.";dbname=".DBName, DBUser, DBPass);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Check score entry permission
        $stmt = $conn->prepare("SELECT status FROM tbl_score_entry_settings WHERE session_id=? AND term_id=?");
        $stmt->execute([$session_id, $term_id]);
        $status = $stmt->fetchColumn() ?: 0;

        if ($status == 0) {
            $_SESSION['result_error'] = "SCORE ENTRY FOR THE SELECTION NOT PERMITTED, CONTACT ADMIN";
            header("location:../import_results");
            exit;
        }

        // File upload
        $fileName = $_FILES['file']['name'];
        $fileTmp  = $_FILES['file']['tmp_name'];
        $ext = pathinfo($fileName, PATHINFO_EXTENSION);

        if (!in_array($ext, ['csv','xls','xlsx'])) die("Invalid file type");

        if ($ext == 'csv') {
            $file = fopen($fileTmp,'r');
            fgetcsv($file); // skip header
            while (($row = fgetcsv($file)) !== FALSE) {
                [$student_id, $first, $second, $third, $exam] = $row;
                $total = $first + $second + $third + $exam;

                // Fetch grade
                $gradeRow = $conn->prepare("SELECT name, remark FROM tbl_grade_system WHERE :score BETWEEN min AND max LIMIT 1");
                $gradeRow->execute(['score'=>$total]);
                $grade = $gradeRow->fetch(PDO::FETCH_ASSOC);
                $g = $grade['name'] ?? '';
                $r = $grade['remark'] ?? '';

                // Check existing result
                $check = $conn->prepare("SELECT id FROM tbl_results WHERE student_id=? AND class_id=? AND subject_id=? AND term_id=? AND session_id=?");
                $check->execute([$student_id, $class_id, $subject_id, $term_id, $session_id]);

                if ($check->rowCount() > 0) {
                    $upd = $conn->prepare("UPDATE tbl_results SET first_test=?, second_test=?, third_test=?, exam_score=?, total_score=?, grade=?, remark=?, updated_at=NOW() WHERE student_id=? AND class_id=? AND subject_id=? AND term_id=? AND session_id=?");
                    $upd->execute([$first,$second,$third,$exam,$total,$g,$r,$student_id,$class_id,$subject_id,$term_id,$session_id]);
                } else {
                    $ins = $conn->prepare("INSERT INTO tbl_results (student_id,class_id,subject_id,session_id,term_id,first_test,second_test,third_test,exam_score,total_score,grade,remark,created_at) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,NOW())");
                    $ins->execute([$student_id,$class_id,$subject_id,$session_id,$term_id,$first,$second,$third,$exam,$total,$g,$r]);
                }
            }
            fclose($file);
        } else {
            $spreadsheet = IOFactory::load($fileTmp);
            $data = $spreadsheet->getActiveSheet()->toArray();
            for ($i=1; $i<count($data); $i++) {
                [$student_id, $first, $second, $third, $exam] = $data[$i];
                $total = $first + $second + $third + $exam;

                $gradeRow = $conn->prepare("SELECT name, remark FROM tbl_grade_system WHERE :score BETWEEN min AND max LIMIT 1");
                $gradeRow->execute(['score'=>$total]);
                $grade = $gradeRow->fetch(PDO::FETCH_ASSOC);
                $g = $grade['name'] ?? '';
                $r = $grade['remark'] ?? '';

                $check = $conn->prepare("SELECT id FROM tbl_results WHERE student_id=? AND class_id=? AND subject_id=? AND term_id=? AND session_id=?");
                $check->execute([$student_id, $class_id, $subject_id, $term_id, $session_id]);

                if ($check->rowCount() > 0) {
                    $upd = $conn->prepare("UPDATE tbl_results SET first_test=?, second_test=?, third_test=?, exam_score=?, total_score=?, grade=?, remark=?, updated_at=NOW() WHERE student_id=? AND class_id=? AND subject_id=? AND term_id=? AND session_id=?");
                    $upd->execute([$first,$second,$third,$exam,$total,$g,$r,$student_id,$class_id,$subject_id,$term_id,$session_id]);
                } else {
                    $ins = $conn->prepare("INSERT INTO tbl_results (student_id,class_id,subject_id,session_id,term_id,first_test,second_test,third_test,exam_score,total_score,grade,remark,created_at) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,NOW())");
                    $ins->execute([$student_id,$class_id,$subject_id,$session_id,$term_id,$first,$second,$third,$exam,$total,$g,$r]);
                }
            }
        }

        $_SESSION['result_error'] = "Results imported successfully";
        header("location:../import_results");
        exit;

    } catch (PDOException $e) {
        $_SESSION['result_error'] = "Database error: " . $e->getMessage();
        header("location:../import_results");
        exit;
    }

} else {
    header("location:../");
    exit;
}
