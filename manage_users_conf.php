<?php
// Connect to database
require 'connectDB.php';

// Add user
if (isset($_POST['Add'])) {
    $user_id = $_POST['user_id'];
    $Uname = $_POST['name'];
    $class = isset($_POST['class']) ? $_POST['class'] : '';
    $dev_dep = isset($_POST['device_dep']) ? $_POST['device_dep'] : '';
    $no_room = isset($_POST['no_room']) ? $_POST['no_room'] : '';
    $Gender = isset($_POST['gender']) ? $_POST['gender'] : '';
    $phone_number = isset($_POST['phone_number']) ? $_POST['phone_number'] : '';

    $sql = "SELECT add_card FROM users WHERE id=?";
    $result = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($result, $sql)) {
        error_log("SQL Error: " . mysqli_error($conn));
        echo "SQL_Error";
        exit();
    } else {
        mysqli_stmt_bind_param($result, "i", $user_id);
        mysqli_stmt_execute($result);
        $resultl = mysqli_stmt_get_result($result);
        if ($row = mysqli_fetch_assoc($resultl)) {
            if ($row['add_card'] == 0) {
                $sql = "UPDATE users SET username=?, gender=?, user_date=CURDATE(), device_dep=?, class=?, no_room=?, phone_number=?, add_card=1 WHERE id=?";
                $result = mysqli_stmt_init($conn);
                if (!mysqli_stmt_prepare($result, $sql)) {
                    error_log("SQL Error: " . mysqli_error($conn));
                    echo "SQL_Error_select_Fingerprint";
                    exit();
                } else {
                    mysqli_stmt_bind_param($result, "ssssssi", $Uname, $Gender, $dev_dep, $class, $no_room, $phone_number, $user_id);
                    mysqli_stmt_execute($result);

                    echo 1;
                    exit();
                }
            } else {
                echo "This User already exists";
                exit();
            }
        } else {
            exit();
        }
    }
}

// Update an existing user
if (isset($_POST['Update'])) {
    $user_id = $_POST['user_id'];
    $Uname = $_POST['name'];
    $class = isset($_POST['class']) ? $_POST['class'] : '';
    $dev_dep = isset($_POST['device_dep']) ? $_POST['device_dep'] : '';
    $no_room = isset($_POST['no_room']) ? $_POST['no_room'] : '';
    $Gender = $_POST['gender'];
    $phone_number = isset($_POST['phone_number']) ? $_POST['phone_number'] : '';

    $sql = "SELECT add_card FROM users WHERE id=?";
    $result = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($result, $sql)) {
        error_log("SQL Error: " . mysqli_error($conn));
        echo "SQL_Error";
        exit();
    } else {
        mysqli_stmt_bind_param($result, "i", $user_id);
        mysqli_stmt_execute($result);
        $resultl = mysqli_stmt_get_result($result);
        if ($row = mysqli_fetch_assoc($resultl)) {
            if ($row['add_card'] == 0) {
                echo "First, You need to add the User!";
                exit();
            } else {
                if (empty($Uname) && empty($dev_dep) && empty($class) && empty($no_room) && empty($phone_number)) {
                    echo "Empty Fields";
                    exit();
                } else {
                    $sql = "UPDATE users SET username=?, gender=?, class=?, no_room=?, device_dep=?, phone_number=? WHERE id=?";
                    $result = mysqli_stmt_init($conn);
                    if (!mysqli_stmt_prepare($result, $sql)) {
                        error_log("SQL Error: " . mysqli_error($conn));
                        echo "SQL_Error_update_User";
                        exit();
                    } else {
                        mysqli_stmt_bind_param($result, "ssssssi", $Uname, $Gender, $class, $no_room, $dev_dep, $phone_number, $user_id);
                        mysqli_stmt_execute($result);

                        echo 1;
                        exit();
                    }
                }
            }
        } else {
            echo "There's no selected User to be updated!";
            exit();
        }
    }
}

// Select user
if (isset($_GET['select'])) {
    $card_uid = $_GET['card_uid'];

    $sql = "SELECT * FROM users WHERE card_uid=?";
    $result = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($result, $sql)) {
        error_log("SQL Error: " . mysqli_error($conn));
        echo "SQL_Error_Select";
        exit();
    } else {
        mysqli_stmt_bind_param($result, "s", $card_uid);
        mysqli_stmt_execute($result);
        $resultl = mysqli_stmt_get_result($result);
        header('Content-Type: application/json');
        $data = array();
        while ($row = mysqli_fetch_assoc($resultl)) {
            $data[] = $row;
        }
        mysqli_stmt_close($result);
        mysqli_close($conn);
        echo json_encode($data);
    }
}

// Delete user
if (isset($_POST['delete'])) {
    $user_id = $_POST['user_id'];

    if (empty($user_id)) {
        echo "There is no selected user to remove";
        exit();
    } else {
        $sql = "DELETE FROM users WHERE id=?";
        $result = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($result, $sql)) {
            error_log("SQL Error: " . mysqli_error($conn));
            echo "SQL_Error_delete";
            exit();
        } else {
            mysqli_stmt_bind_param($result, "i", $user_id);
            mysqli_stmt_execute($result);
            echo 1;
            exit();
        }
    }
}
?>
