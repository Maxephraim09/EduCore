<?php
session_start();
chdir('../../');
require_once('db/config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];

    try {
        $conn = new PDO(
            'mysql:host=' . DBHost . ';dbname=' . DBName . ';charset=' . DBCharset,
            DBUser,
            DBPass
        );
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Fetch selected combination
        $stmt = $conn->prepare("SELECT * FROM tbl_subject_combinations WHERE id = ?");
        $stmt->execute([$id]);
        $comb = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$comb) {
            echo "<p class='text-danger'>Invalid combination ID.</p>";
            exit;
        }

        // Fetch all lists
        $subjects = $conn->query("SELECT id, name FROM tbl_subjects ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
        $classes = $conn->query("SELECT id, name FROM tbl_classes ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
        $teachers = $conn->query("SELECT id, fname, lname FROM tbl_staff WHERE level='2' ORDER BY fname")->fetchAll(PDO::FETCH_ASSOC);
        ?>

        <form class="app_frm" method="POST" autocomplete="off" action="academic/core/update_comb">

            <div class="mb-2">
                <label class="form-label">Select Subject</label>
                <select class="form-control select3" name="subject" required style="width: 100%;">
                    <option selected disabled value="">Select one</option>
                    <?php foreach ($subjects as $s): ?>
                        <option value="<?php echo $s['id']; ?>" <?php if ($s['id'] == $comb['subject_id']) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($s['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-2">
                <label class="form-label">Select Class</label>
                <select class="form-control select3" name="class" required style="width: 100%;">
                    <option selected disabled value="">Select one</option>
                    <?php foreach ($classes as $c): ?>
                        <option value="<?php echo $c['id']; ?>" <?php if ($c['id'] == $comb['class_id']) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($c['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Select Teacher</label>
                <select class="form-control select3" name="teacher" required style="width: 100%;">
                    <option selected disabled value="">Select one</option>
                    <?php foreach ($teachers as $t): ?>
                        <option value="<?php echo $t['id']; ?>" <?php if ($t['id'] == $comb['teacher_id']) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($t['fname'].' '.$t['lname']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <input type="hidden" name="id" value="<?php echo $comb['id']; ?>">
            <button type="submit" class="btn btn-primary app_btn">Save</button>
            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
        </form>

        <?php
    } catch (PDOException $e) {
        echo "Connection failed: " . $e->getMessage();
    }
}
?>
