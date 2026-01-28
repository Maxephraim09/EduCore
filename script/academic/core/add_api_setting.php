<?php
session_start();
require_once('../../db/config.php');
header('Content-Type: application/json'); // Ensure JSON output
error_reporting(0);

// Only POST requests allowed
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
    exit;
}

// Get parameters
$action = $_POST['action'] ?? 'add';
$type   = $_POST['type'] ?? '';
$id     = intval($_POST['id'] ?? 0);

// Validate API type
if (!in_array($type, ['paystack','email','sms'])) {
    echo json_encode(['status'=>'error','message'=>'Invalid API type']);
    exit;
}

try {
    if ($action === 'delete') {
        if (!$id) {
            throw new Exception('Missing ID for delete');
        }

        $table = ($type==='paystack') ? 'tbl_paystack_api_settings' : ($type==='email' ? 'email_api_settings' : 'sms_api_settings');
        $stmt = $conn->prepare("DELETE FROM $table WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();

        echo json_encode(['status'=>'success','message'=>'Deleted successfully']);
        exit;
    }

    // Collect fields based on type
    switch($type) {
        case 'paystack':
            $api_name = $_POST['api_name'] ?? '';
            $api_secret_key = $_POST['api_secret_key'] ?? '';
            $api_public_key = $_POST['api_public_key'] ?? '';
            $environment = $_POST['environment'] ?? 'test';

            if (!$api_name || !$api_secret_key || !$api_public_key) {
                throw new Exception('Missing required Paystack parameters');
            }

            if ($id) {
                $stmt = $conn->prepare("UPDATE tbl_paystack_api_settings SET api_name=?, api_secret_key=?, api_public_key=?, environment=? WHERE id=?");
                $stmt->bind_param("ssssi",$api_name,$api_secret_key,$api_public_key,$environment,$id);
            } else {
                $stmt = $conn->prepare("INSERT INTO tbl_paystack_api_settings (api_name, api_secret_key, api_public_key, environment, created_at) VALUES (?,?,?,?,NOW())");
                $stmt->bind_param("ssss",$api_name,$api_secret_key,$api_public_key,$environment);
            }
            break;

        case 'email':
            $host = $_POST['host'] ?? '';
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            $from_email = $_POST['from_email'] ?? '';
            $from_name = $_POST['from_name'] ?? '';
            $port = $_POST['port'] ?? '';
            $encryption = $_POST['encryption'] ?? '';
            $reply_to = $_POST['reply_to'] ?? '';

            if (!$host || !$username || !$password || !$from_email || !$from_name || !$port) {
                throw new Exception('Missing required Email parameters');
            }

            if ($id) {
                $stmt = $conn->prepare("UPDATE email_api_settings SET host=?, username=?, password=?, from_email=?, from_name=?, port=?, encryption=?, reply_to=? WHERE id=?");
                $stmt->bind_param("ssssssssi",$host,$username,$password,$from_email,$from_name,$port,$encryption,$reply_to,$id);
            } else {
                $stmt = $conn->prepare("INSERT INTO email_api_settings (host, username, password, from_email, from_name, port, encryption, reply_to, created_at) VALUES (?,?,?,?,?,?,?,?,NOW())");
                $stmt->bind_param("sssssssss",$host,$username,$password,$from_email,$from_name,$port,$encryption,$reply_to);
            }
            break;

        case 'sms':
            $api_name = $_POST['api_name'] ?? '';
            $base_url = $_POST['base_url'] ?? '';
            $api_key = $_POST['api_key'] ?? '';
            $sender_id = $_POST['sender_id'] ?? '';
            $environment = $_POST['environment'] ?? 'test';

            if (!$api_name || !$base_url || !$api_key || !$sender_id) {
                throw new Exception('Missing required SMS parameters');
            }

            if ($id) {
                $stmt = $conn->prepare("UPDATE sms_api_settings SET api_name=?, base_url=?, api_key=?, sender_id=?, environment=? WHERE id=?");
                $stmt->bind_param("sssssi",$api_name,$base_url,$api_key,$sender_id,$environment,$id);
            } else {
                $stmt = $conn->prepare("INSERT INTO sms_api_settings (api_name, base_url, api_key, sender_id, environment, created_at) VALUES (?,?,?,?,?,NOW())");
                $stmt->bind_param("sssss",$api_name,$base_url,$api_key,$sender_id,$environment);
            }
            break;
    }

    $stmt->execute();
    echo json_encode(['status'=>'success','message'=>'Saved successfully']);
    exit;

} catch(Exception $e) {
    echo json_encode(['status'=>'error','message'=>'Unexpected error: '.$e->getMessage()]);
    exit;
}
?>
