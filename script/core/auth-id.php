<?php
session_start();
require_once('../db/config.php');
require_once('../const/rand.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id_number = $_POST['id_number'];
    $password  = $_POST['password'];
    $cookie_length = 4320; // minutes

    try {
        $conn = new PDO("mysql:host=".DBHost.";dbname=".DBName.";charset=".DBCharset, DBUser, DBPass);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Fetch user by ID from staff or students
        $stmt = $conn->prepare("
            SELECT id, password, level, status 
            FROM tbl_staff 
            WHERE id = ?
            UNION
            SELECT id, password, level, status
            FROM tbl_students
            WHERE id = ?
        ");
        $stmt->execute([$id_number, $id_number]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            $_SESSION['reply'] = [['danger','Invalid ID or Password']];
            header("location:../login-with-id.php");
            exit;
        }

        if ($user['status'] == 0) {
            $_SESSION['reply'] = [['danger','Your account is blocked']];
            header("location:../login-with-id.php");
            exit;
        }

        if (!password_verify($password, $user['password'])) {
            $_SESSION['reply'] = [['danger','Invalid ID or Password']];
            header("location:../login-with-id.php");
            exit;
        }

        $account_id = $user['id'];
        $level      = (int)$user['level'];
        $session_id = mb_strtoupper(GRS(20));
        $ip         = $_SERVER['REMOTE_ADDR'];

        // Clear previous sessions
        $stmt = $conn->prepare("DELETE FROM tbl_login_sessions WHERE staff = ? OR student = ?");
        $stmt->execute([$account_id, $account_id]);

        // Determine session column
        $staff_levels = [0,1,2,4,5,6];
        $student_levels = [3];

        if (in_array($level, $staff_levels)) {
            $stmt = $conn->prepare("INSERT INTO tbl_login_sessions (session_key, staff, ip_address) VALUES (?,?,?)");
            $stmt->execute([$session_id, $account_id, $ip]);
        } elseif (in_array($level, $student_levels)) {
            $stmt = $conn->prepare("INSERT INTO tbl_login_sessions (session_key, student, ip_address) VALUES (?,?,?)");
            $stmt->execute([$session_id, $account_id, $ip]);
        }

        // Set cookies
        setcookie("__SRMS__logged", $level, time() + (60*$cookie_length), "/");
        setcookie("__SRMS__key", $session_id, time() + (60*$cookie_length), "/");

        // Redirect based on role
        switch ($level) {
            case 0: header("location:../admin"); break;
            case 1: header("location:../academic"); break;
            case 2: header("location:../teacher"); break;
            case 3: header("location:../student"); break;
            case 4: header("location:../accountant"); break;
            case 5: header("location:../frontdesk"); break;
            case 6: header("location:../librarian"); break;
            default:
                $_SESSION['reply'] = [['danger','Invalid user level']];
                header("location:../login-with-id.php");
        }

    } catch(PDOException $e) {
        echo "Connection failed: ".$e->getMessage();
    }

} else {
    header("location:../login-with-id.php");
}
?>
