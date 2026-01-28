<?php
chdir('../../');
session_start();
require_once('db/config.php');
require_once('const/school.php');
require_once('const/check_session.php');

// ==============================
// ✅ Restrict Access to Admins
// ==============================
if (!($res == "1" && $level == "1")) {
    header("Location: ../../");
    exit;
}

try {
    $conn = new PDO(
        "mysql:host=" . DBHost . ";dbname=" . DBName . ";charset=" . DBCharset,
        DBUser,
        DBPass
    );
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // ==============================
    // ✅ ADD NEW APPLICATION SETTING
    // ==============================
    if (isset($_POST['action']) && $_POST['action'] === 'add') {
        $type = trim($_POST['type']);
        $start_date = $_POST['start_date'];
        $end_date = $_POST['end_date'];
        $fee = !empty($_POST['application_fee']) ? $_POST['application_fee'] : NULL;

        $stmt = $conn->prepare("
            INSERT INTO tbl_application_settings (type, start_date, end_date, application_fee)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$type, $start_date, $end_date, $fee]);

        echo "<script>
                alert('✅ Application Details added successfully!');
                window.location.href='../../academic/application_settings';
              </script>";
        exit;
    }

    // ==============================
    // ✅ EDIT EXISTING RECORD
    // ==============================
    if (isset($_POST['action']) && $_POST['action'] === 'edit') {
        $id = $_POST['id'];
        $type = trim($_POST['type']);
        $start_date = $_POST['start_date'];
        $end_date = $_POST['end_date'];
        $fee = !empty($_POST['application_fee']) ? $_POST['application_fee'] : NULL;

        $stmt = $conn->prepare("
            UPDATE tbl_application_settings
            SET type = ?, start_date = ?, end_date = ?, application_fee = ?
            WHERE id = ?
        ");
        $stmt->execute([$type, $start_date, $end_date, $fee, $id]);

        echo "<script>
                alert('✅ Application setting updated successfully!');
                window.location.href='../../academic/application_settings';
              </script>";
        exit;
    }

    // ==============================
    // ✅ DELETE RECORD
    // ==============================
    if (isset($_GET['delete'])) {
        $id = $_GET['delete'];
        $stmt = $conn->prepare("DELETE FROM tbl_application_settings WHERE id = ?");
        $stmt->execute([$id]);

        header("Location: ../../academic/application_settings?msg=deleted");
        exit;
    }

    // ==============================
    // ✅ TOGGLE STATUS (Active/Inactive)
    // ==============================
    if (isset($_GET['toggle']) && isset($_GET['status'])) {
        $id = $_GET['toggle'];
        $status = ($_GET['status'] === 'active') ? 'inactive' : 'active';

        $stmt = $conn->prepare("
            UPDATE tbl_application_settings
            SET status = ?
            WHERE id = ?
        ");
        $stmt->execute([$status, $id]);

        header("Location: ../../academic/application_settings?msg=status_updated");
        exit;
    }

} catch (PDOException $e) {
    echo "<div style='color:red; font-weight:bold;'>Database Error: " . htmlspecialchars($e->getMessage()) . "</div>";
    exit;
}
?>
