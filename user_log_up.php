<?php
session_start();
?>
<div class="table-responsive" style="max-height: 600px;"> 
  <table class="table">
    <thead class="table-primary">
      <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Card UID</th>
        <th>DEPARTMENT</th>
        <th>Class</th>
        <th>Room</th>
        <th>Date In</th>
        <th>Date Out</th>
        <th>Time Out</th>
        <th>Time IN</th>
        <th>Status</th>
        <th>Student No</th>
      </tr>
    </thead>
    <tbody class="table-secondary">
      <?php
        // Connect to database
        require 'connectDB.php';
        $searchQuery = "1=1"; // Default query to always return true

        // Initialize filter variables
        $Start_date = "";
        $End_date = "";
        $Start_time = "";
        $End_time = "";
        $device_dep = "";
        $status_sel = "";
        $phone_number = "";
        
        // Date Out filter
        if (isset($_POST['date_sel']) && $_POST['date_sel'] == "Date_out") {
          if (isset($_POST['date_sel_start']) && $_POST['date_sel_start'] != 0) {
            $Start_date = $_POST['date_sel_start'];
            $searchQuery .= " AND users_logs.checkoutdate1 >= '$Start_date'";
          }
          if (isset($_POST['date_sel_end']) && $_POST['date_sel_end'] != 0) {
            $End_date = $_POST['date_sel_end'];
            $searchQuery .= " AND users_logs.checkoutdate1 <= '$End_date'";
          }
        }

        // Date In filter
        if (isset($_POST['date_sel']) && $_POST['date_sel'] == "Date_in") {
          if (isset($_POST['date_sel_start']) && $_POST['date_sel_start'] != 0) {
            $Start_date = $_POST['date_sel_start'];
            $searchQuery .= " AND users_logs.checkindate1 >= '$Start_date'";
          }
          if (isset($_POST['date_sel_end']) && $_POST['date_sel_end'] != 0) {
            $End_date = $_POST['date_sel_end'];
            $searchQuery .= " AND users_logs.checkindate1 <= '$End_date'";
          }
        }

        // Time-In filter
        if (isset($_POST['time_sel']) && $_POST['time_sel'] == "Time_out") {
          if (isset($_POST['time_sel_start']) && $_POST['time_sel_start'] != 0) {
            $Start_time = $_POST['time_sel_start'];
            $searchQuery .= " AND users_logs.timeout1 >= '$Start_time'";
          }
          if (isset($_POST['time_sel_end']) && $_POST['time_sel_end'] != 0) {
            $End_time = $_POST['time_sel_end'];
            $searchQuery .= " AND users_logs.timeout1 <= '$End_time'";
          }
        }

        // Time-Out filter
        if (isset($_POST['time_sel']) && $_POST['time_sel'] == "Time_in") {
          if (isset($_POST['time_sel_start']) && $_POST['time_sel_start'] != 0) {
            $Start_time = $_POST['time_sel_start'];
            $searchQuery .= " AND users_logs.timein1 >= '$Start_time'";
          }
          if (isset($_POST['time_sel_end']) && $_POST['time_sel_end'] != 0) {
            $End_time = $_POST['time_sel_end'];
            $searchQuery .= " AND users_logs.timein1 <= '$End_time'";
          }
        }

        // Department filter
        if (isset($_POST['device_dep']) && $_POST['device_dep'] != 0) {
          $device_dep = $_POST['device_dep'];
          $searchQuery .= " AND users_logs.device_dep = '$device_dep'";
        }

        // Status filter
        if (isset($_POST['status_sel']) && $_POST['status_sel'] != "") {
          $status_sel = $_POST['status_sel'];
          $searchQuery .= " AND users_logs.status = '$status_sel'";
        }

        // Phone Number filter
        if (isset($_POST['phone_number']) && $_POST['phone_number'] != 0) {
          $phone_number = $_POST['phone_number'];
          $searchQuery .= " AND users_logs.phone_number = '$phone_number'";
        }

        // "Filter Out Only" option
        $filterOutOnly = isset($_POST['filter_out_only']) && $_POST['filter_out_only'] == 'true';
        if ($filterOutOnly) {
            $searchQuery .= " AND users_logs.status = 'OUT'"; // Adjust status column as needed
        }

        // SQL query
        $sql = "SELECT users_logs.id, users_logs.username, users_logs.card_uid, users.device_dep, users.class, users.no_room, 
                       users_logs.checkoutdate1, users_logs.checkindate1, users_logs.timeout1, users_logs.timein1, 
                       users_logs.status, users.phone_number 
                FROM users_logs 
                INNER JOIN users ON users_logs.username = users.username 
                WHERE $searchQuery 
                ORDER BY users_logs.id DESC";

        $result = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($result, $sql)) {
            echo '<p class="error">SQL Error</p>';
        } else {
            mysqli_stmt_execute($result);
            $resultl = mysqli_stmt_get_result($result);
            if (mysqli_num_rows($resultl) > 0) {
                while ($row = mysqli_fetch_assoc($resultl)) {
                    // Dapatkan waktu masuk dalam format jam
                    $timeIn = date("H:i", strtotime($row['timein1']));
                    $isLateEntry = false;

                    // Semak jika waktu masuk adalah selepas 10 malam atau sebelum 7 pagi
                    if ($timeIn >= "22:00" || $timeIn < "07:00") {
                        $isLateEntry = true; // Set flag jika syarat dipenuhi
                    }
      ?>
                  <tr style="<?php echo $isLateEntry ? 'background-color: red; color: black;' : ''; ?>">
                      <td><?php echo $row['id']; ?></td>
                      <td><?php echo $row['username']; ?></td>
                      <td><?php echo $row['card_uid']; ?></td>
                      <td><?php echo $row['device_dep']; ?></td>
                      <td><?php echo $row['class']; ?></td>
                      <td><?php echo $row['no_room']; ?></td>
                      <td><?php echo $row['checkoutdate1']; ?></td>
                      <td><?php echo $row['checkindate1']; ?></td>
                      <td><?php echo $row['timeout1']; ?></td>
                      <td><?php echo $row['timein1']; ?></td>
                      <td><?php echo $row['status']; ?></td>
                      <td><?php echo $row['phone_number']; ?></td> 
                  </tr>
      <?php
                }
            } else {
                echo "<tr><td colspan='12'>No records found.</td></tr>";
            }
        }
      ?>
    </tbody>
  </table>
</div>
