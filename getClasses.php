<?php
require 'connectDB.php';

if (isset($_POST['department'])) {
    $department = $_POST['department'];

    $sql = "SELECT DISTINCT class FROM users WHERE device_dep = ? ORDER BY class ASC";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        echo '<option value="">SQL Error</option>';
    } else {
        mysqli_stmt_bind_param($stmt, "s", $department);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) > 0) {
            echo '<option value="0">All Classes</option>';
            while ($row = mysqli_fetch_assoc($result)) {
                echo '<option value="'.$row['class'].'">'.$row['class'].'</option>';
            }
        } else {
            echo '<option value="">No classes available</option>';
        }
    }
}
?>
