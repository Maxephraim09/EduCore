<?php
session_start();
require_once('../../db/config.php');
require_once('../../const/school.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../../PHPMailer/src/Exception.php';
require '../../PHPMailer/src/PHPMailer.php';
require '../../PHPMailer/src/SMTP.php';

try {
    $conn = new PDO("mysql:host=" . DBHost . ";dbname=" . DBName . ";charset=" . DBCharset, DBUser, DBPass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);

        // Fetch applicant
        $stmt = $conn->prepare("SELECT * FROM tbl_staff_applications WHERE id=:id");
        $stmt->execute([':id' => $id]);
        $app = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($app && $app['status'] == 'Pending') {
            // Insert into tbl_staff
            $passwordHash = password_hash($app['number'], PASSWORD_DEFAULT);
            $insert = $conn->prepare("INSERT INTO tbl_staff (fname, lname, gender, email, password, level, status)
                                      VALUES (:fname, :lname, :gender, :email, :password, 2, 1)");
            $insert->execute([
                ':fname' => $app['first_name'],
                ':lname' => $app['last_name'],
                ':gender' => $app['gender'],
                ':email' => $app['email'],
                ':password' => $passwordHash
            ]);

            // Update status
            $conn->prepare("UPDATE tbl_staff_applications SET status='Approved' WHERE id=:id")
                ->execute([':id' => $id]);

            // Fetch email API settings
            $settings = $conn->query("SELECT * FROM email_api_settings LIMIT 1")->fetch(PDO::FETCH_ASSOC);

            if ($settings) {
                $mail = new PHPMailer(true);
                $mail->isSMTP();
                $mail->Host = $settings['host'];
                $mail->SMTPAuth = true;
                $mail->Username = $settings['username'];
                $mail->Password = $settings['password'];
                $mail->SMTPSecure = $settings['encryption'];
                $mail->Port = $settings['port'];

                $mail->setFrom($settings['from_email'], $settings['from_name']);
                $mail->addAddress($app['email'], $app['first_name'].' '.$app['last_name']);
                $mail->isHTML(true);
                $mail->Subject = "Employment Approval - SRMS";
                $mail->Body = "
                    <h3>Dear {$app['first_name']},</h3>
                    <p>Congratulations! Your staff application has been <b>approved</b>.</p>
                    <p><b>Login Details:</b><br>
                    Email: {$app['email']}<br>
                    Password: {$app['number']}</p>
                    <p>Please log in and change your password immediately.</p>
                    <p>Regards,<br>SRMS Management</p>
                ";

                $mail->send();
            }

            $_SESSION['success'] = "Staff approved and notified successfully!";
        } else {
            $_SESSION['error'] = "Invalid or already approved application.";
        }
    } else {
        $_SESSION['error'] = "Missing application ID.";
    }

} catch (Exception $e) {
    $_SESSION['error'] = "Mailer Error: " . $e->getMessage();
} catch (PDOException $e) {
    $_SESSION['error'] = "Database Error: " . $e->getMessage();
}

header("Location: ../manage_staff_applications.php");
exit;
?>
