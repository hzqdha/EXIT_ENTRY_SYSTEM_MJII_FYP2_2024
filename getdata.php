<?php  
// Connect to database
require 'connectDB.php';
date_default_timezone_set('Asia/Kuala_Lumpur');
$d = date("Y-m-d");
$t = date("H:i:s"); // 24-hour format

if (isset($_GET['card_uid']) && isset($_GET['device_token'])) {
    
    $card_uid = $_GET['card_uid'];
    $device_uid = $_GET['device_token'];

    // Step 1: Check if the device exists in the database
    $sql = "SELECT * FROM devices WHERE device_uid=?";
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        echo "SQL_Error_Select_device: " . mysqli_error($conn);
        exit();
    }
    mysqli_stmt_bind_param($stmt, "s", $device_uid);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if ($row = mysqli_fetch_assoc($result)) {
        $device_mode = $row['device_mode'];
        $device_dep = $row['device_dep'];

        if ($device_mode == 1) {
            // Step 2: Check if the card already exists in the users table
            $sql = "SELECT * FROM users WHERE card_uid=?";
            $stmt = mysqli_prepare($conn, $sql);
            if (!$stmt) {
                echo "SQL_Error_Select_card: " . mysqli_error($conn);
                exit();
            }
            mysqli_stmt_bind_param($stmt, "s", $card_uid);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if ($row = mysqli_fetch_assoc($result)) {
                // Existing card detected for Login or Logout
                if ($row['add_card'] == 1) {
                    if ($row['device_uid'] == $device_uid || $row['device_uid'] == 0) {
                        $Uname = $row['username'];
                        $Number = $row['serialnumber'];
                        $device_dep = $row['device_dep'];
                        $class = $row['class'];
                        $no_room = $row['no_room'];
                        $phone_number = $row['phone_number'];

                        // Step 3: Check if there is an existing login without a logout
                        $sql = "SELECT * FROM users_logs WHERE card_uid=? AND checkoutdate1=? AND checkindate1='0000-00-00' AND card_out=0";
                        $stmt = mysqli_prepare($conn, $sql);
                        if (!$stmt) {
                            echo "SQL_Error_Select_logs: " . mysqli_error($conn);
                            exit();
                        }
                        mysqli_stmt_bind_param($stmt, "ss", $card_uid, $d);
                        mysqli_stmt_execute($stmt);
                        $result = mysqli_stmt_get_result($stmt);

                        // Logout if there is an existing login record without a logout
                        if ($row = mysqli_fetch_assoc($result)) {
                            $sql = "UPDATE users_logs SET timein1=?, checkindate1=?, card_out=1, status='IN' WHERE card_uid=? AND checkoutdate1=? AND card_out=0";
                            $stmt = mysqli_prepare($conn, $sql);
                            if (!$stmt) {
                                echo "SQL_Error_Update_logout: " . mysqli_error($conn);
                                exit();
                            }
                            mysqli_stmt_bind_param($stmt, "ssss", $t, $d, $card_uid, $d);
                            if (mysqli_stmt_execute($stmt)) {
                                echo "logout: " . $Uname;
                            } else {
                                echo "SQL_Error_Execute_logout: " . mysqli_error($conn);
                            }
                            exit();
                        } 
                        // If no existing login, proceed with login
                        else {
                            $sql = "INSERT INTO users_logs (username, serialnumber, card_uid, device_uid, device_dep, class, no_room, checkoutdate1, checkindate1, timeout1, timein1, phone_number, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'OUT')";
                            $stmt = mysqli_prepare($conn, $sql);
                            if (!$stmt) {
                                echo "SQL_Error_Insert_login: " . mysqli_error($conn);
                                exit();
                            }
                            $checkindate1 = "0000-00-00";
                            $timeout1 = "00:00:00";
                            mysqli_stmt_bind_param($stmt, "ssssssssssss", $Uname, $Number, $card_uid, $device_uid, $device_dep, $class, $no_room, $d, $checkindate1, $t, $timeout1, $phone_number);
                            if (mysqli_stmt_execute($stmt)) {
                                echo "login: " . $Uname;
                            } else {
                                echo "SQL_Error_Execute_login: " . mysqli_error($conn);
                            }
                            exit();
                        }
                    } else {
                        echo "Not Allowed!";
                        exit();
                    }
                } else if ($row['add_card'] == 0) {
                    echo "Not registered!";
                    exit();
                }
            } else {
                echo "Card not found!";
                exit();
            }
        } else if ($device_mode == 0) {
            // The Card is available
            $sql = "SELECT * FROM users WHERE card_uid=?";
            $stmt = mysqli_prepare($conn, $sql);
            if (!$stmt) {
                echo "SQL_Error_Select_card: " . mysqli_error($conn);
                exit();
            }
            mysqli_stmt_bind_param($stmt, "s", $card_uid);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if ($row = mysqli_fetch_assoc($result)) {
                $sql = "SELECT card_select FROM users WHERE card_select=1";
                $stmt = mysqli_prepare($conn, $sql);
                if (!$stmt) {
                    echo "SQL_Error_Select: " . mysqli_error($conn);
                    exit();
                }
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                
                if ($row = mysqli_fetch_assoc($result)) {
                    $sql = "UPDATE users SET card_select=0";
                    $stmt = mysqli_prepare($conn, $sql);
                    if (!$stmt) {
                        echo "SQL_Error_Update_card_select: " . mysqli_error($conn);
                        exit();
                    }
                    mysqli_stmt_execute($stmt);

                    $sql = "UPDATE users SET card_select=1 WHERE card_uid=?";
                    $stmt = mysqli_prepare($conn, $sql);
                    if (!$stmt) {
                        echo "SQL_Error_Update_available_card: " . mysqli_error($conn);
                        exit();
                    }
                    mysqli_stmt_bind_param($stmt, "s", $card_uid);
                    if (mysqli_stmt_execute($stmt)) {
                        echo "available";
                    } else {
                        echo "SQL_Error_Execute_available_card: " . mysqli_error($conn);
                    }
                    exit();
                } else {
                    $sql = "UPDATE users SET card_select=1 WHERE card_uid=?";
                    $stmt = mysqli_prepare($conn, $sql);
                    if (!$stmt) {
                        echo "SQL_Error_Update_new_card: " . mysqli_error($conn);
                        exit();
                    }
                    mysqli_stmt_bind_param($stmt, "s", $card_uid);
                    if (mysqli_stmt_execute($stmt)) {
                        echo "available";
                    } else {
                        echo "SQL_Error_Execute_new_card: " . mysqli_error($conn);
                    }
                    exit();
                }
            } else {
                echo "Card not found!";
                exit();
            }
        }
    } else {
        echo "Invalid Device!";
        exit();
    }
} else {
    echo "Missing parameters!";
    exit();
}
?>
