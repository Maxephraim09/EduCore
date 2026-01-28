<?php
require_once('../../db/config.php');

if (isset($_GET['class_id'])) {
    $class_id = intval($_GET['class_id']);

    try {
        $conn = new PDO("mysql:host=" . DBHost . ";dbname=" . DBName, DBUser, DBPass);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Fetch subjects linked to this class via tbl_subject_combinations
        $query = "
            SELECT s.id, s.name
            FROM tbl_subject_combinations sc
            INNER JOIN tbl_subjects s ON sc.subject_id = s.id
            WHERE sc.class_id = :class_id
            ORDER BY s.name ASC
        ";
        $stmt = $conn->prepare($query);
        $stmt->execute(['class_id' => $class_id]);
        $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // If no subjects found, return a default message
        if (empty($subjects)) {
            echo '<option value="">-- No subjects assigned to this class --</option>';
            exit;
        }

        // Build the dropdown options
        $options = '<option value="">-- Select Subject --</option>';
        foreach ($subjects as $sub) {
            $options .= '<option value="' . $sub['id'] . '">' . htmlspecialchars($sub['name']) . '</option>';
        }

        echo $options;

    } catch (PDOException $e) {
        echo '<option>Error loading subjects: ' . htmlspecialchars($e->getMessage()) . '</option>';
    }
} else {
    echo '<option value="">-- Invalid Request --</option>';
}
?>
