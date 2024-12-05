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

        // Mode 0: Adding or making a card available
        if ($device_mode == 0) {

            // Check if the card already exists in the users table
            $sql = "SELECT * FROM users WHERE card_uid=?";
            $stmt = mysqli_prepare($conn, $sql);
            if (!$stmt) {
                echo "SQL_Error_Select_card: " . mysqli_error($conn);
                exit();
            }
            mysqli_stmt_bind_param($stmt, "s", $card_uid);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            // If card exists, update it as the available card
            if ($row = mysqli_fetch_assoc($result)) {
                // Check if any card is already selected as available
                $sql = "SELECT card_select FROM users WHERE card_select=1";
                $stmt = mysqli_prepare($conn, $sql);
                if (!$stmt) {
                    echo "SQL_Error_Select: " . mysqli_error($conn);
                    exit();
                }
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);

                // If another card is selected as available, deselect it
                if ($row = mysqli_fetch_assoc($result)) {
                    $sql = "UPDATE users SET card_select=0 WHERE card_select=1";
                    $stmt = mysqli_prepare($conn, $sql);
                    if (!$stmt) {
                        echo "SQL_Error_Update_card_select: " . mysqli_error($conn);
                        exit();
                    }
                    mysqli_stmt_execute($stmt);
                }

                // Mark the current card as available
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
            } 
            // If card does not exist, add it as a new card
            else {
                $sql = "INSERT INTO users (card_uid, card_select) VALUES (?, 1)";
                $stmt = mysqli_prepare($conn, $sql);
                if (!$stmt) {
                    echo "SQL_Error_Insert_new_card: " . mysqli_error($conn);
                    exit();
                }
                mysqli_stmt_bind_param($stmt, "s", $card_uid);
                if (mysqli_stmt_execute($stmt)) {
                    echo "new card added";
                } else {
                    echo "SQL_Error_Execute_new_card: " . mysqli_error($conn);
                }
                exit();
            }
        } else {
            echo "Invalid device mode!";
            exit();
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
