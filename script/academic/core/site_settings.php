<?php
chdir('../../');
session_start();
require_once('db/config.php');
require_once('const/school.php'); 
require_once('const/check_session.php');

// ✅ Only admin allowed
if (!($res == "1" && $level == "1")) {
    header("location:../../");
    exit;
}

try {
    $conn = new PDO("mysql:host=".DBHost.";dbname=".DBName.";charset=".DBCharset, DBUser, DBPass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // ===============================
    // UPDATE or INSERT SETTINGS
    // ===============================
    if (isset($_POST['save_settings'])) {
        $site_name = trim($_POST['site_name']);
        $site_code = trim($_POST['site_code']);
        $website = trim($_POST['website']);
        $address = trim($_POST['address']);
        $email = trim($_POST['email']);
        $phone = trim($_POST['phone']);

        $facebook = trim($_POST['facebook']);
        $x = trim($_POST['x']);
        $whatsapp = trim($_POST['whatsapp']);
        $linkedin = trim($_POST['linkedin']);
        $instagram = trim($_POST['instagram']);

        $account_name = trim($_POST['account_name']);
        $bank_name = trim($_POST['bank_name']);
        $account_number = trim($_POST['account_number']);

        // Check if record exists
        $stmt = $conn->query("SELECT id FROM tbl_site_settings LIMIT 1");
        $exists = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($exists) {
            // Update existing
            $stmt = $conn->prepare("UPDATE tbl_site_settings SET 
                site_name=:site_name, site_code=:site_code, website=:website, address=:address, email=:email, phone=:phone,
                facebook_url=:facebook, x_url=:x, whatsapp_url=:whatsapp, linkedin_url=:linkedin, instagram_url=:instagram,
                account_name=:account_name, bank_name=:bank_name, account_number=:account_number
                WHERE id=:id
            ");
            $stmt->execute([
                ':site_name'=>$site_name,
                ':site_code'=>$site_code,
                ':website'=>$website,
                ':address'=>$address,
                ':email'=>$email,
                ':phone'=>$phone,
                ':facebook'=>$facebook,
                ':x'=>$x,
                ':whatsapp'=>$whatsapp,
                ':linkedin'=>$linkedin,
                ':instagram'=>$instagram,
                ':account_name'=>$account_name,
                ':bank_name'=>$bank_name,
                ':account_number'=>$account_number,
                ':id'=>$exists['id']
            ]);
        } else {
            // Insert new
            $stmt = $conn->prepare("INSERT INTO tbl_site_settings 
                (site_name, site_code, website, address, email, phone,
                 facebook_url, x_url, whatsapp_url, linkedin_url, instagram_url,
                 account_name, bank_name, account_number)
                 VALUES 
                (:site_name, :site_code, :website, :address, :email, :phone,
                 :facebook, :x, :whatsapp, :linkedin, :instagram,
                 :account_name, :bank_name, :account_number)");
            $stmt->execute([
                ':site_name'=>$site_name,
                ':site_code'=>$site_code,
                ':website'=>$website,
                ':address'=>$address,
                ':email'=>$email,
                ':phone'=>$phone,
                ':facebook'=>$facebook,
                ':x'=>$x,
                ':whatsapp'=>$whatsapp,
                ':linkedin'=>$linkedin,
                ':instagram'=>$instagram,
                ':account_name'=>$account_name,
                ':bank_name'=>$bank_name,
                ':account_number'=>$account_number
            ]);
        }

        echo "<script>alert('Settings saved successfully!'); window.location.href='../../academic/site-settings.php';</script>";
        exit;
    }

} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}
?>
