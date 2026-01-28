<?php
session_start();
require_once('../../db/config.php');

if (isset($_GET['id'])) {
    try {
        $id = intval($_GET['id']);
        $conn = new PDO("mysql:host=".DBHost.";dbname=".DBName.";charset=".DBCharset, DBUser, DBPass);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // 1️⃣ Deactivate all existing sessions
        $conn->exec("UPDATE tbl_sessions SET is_active = 0");

        // 2️⃣ Activate the selected session
        $stmt = $conn->prepare("UPDATE tbl_sessions SET is_active = 1 WHERE id = :id");
        $stmt->execute([':id' => $id]);

        // 3️⃣ Automatically create 3 terms if not already there
        $terms = ['First Term', 'Second Term', 'Third Term'];
        foreach ($terms as $term) {
            $checkTerm = $conn->prepare("SELECT id FROM tbl_terms WHERE name = ?");
            $checkTerm->execute([$term]);
            if (!$checkTerm->fetch()) {
                $insertTerm = $conn->prepare("INSERT INTO tbl_terms (name, status) VALUES (?, 1)");
                $insertTerm->execute([$term]);
            }
        }

        // 4️⃣ Promote students to next class for the new session
        // (Optional – you can remove this block if you prefer manual promotion)
        $students = $conn->query("SELECT id, class FROM tbl_students")->fetchAll(PDO::FETCH_ASSOC);

        foreach ($students as $student) {
            $nextClass = $student['class'] + 1; // Simple numeric promotion logic
            $update = $conn->prepare("UPDATE tbl_students SET class = ?, session_id = ? WHERE id = ?");
            $update->execute([$nextClass, $id, $student['id']]);
        }

        $_SESSION['reply'] = [
            'type' => 'success',
            'message' => 'Session activated successfully and students promoted to next class.'
        ];

    } catch (PDOException $e) {
        $_SESSION['reply'] = [
            'type' => 'danger',
            'message' => $e->getMessage()
        ];
    }

    header("Location: ../sessions");
    exit;
}
?>
