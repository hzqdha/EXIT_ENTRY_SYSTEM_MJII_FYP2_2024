<div class="table-responsive-sm" style="max-height: 870px;">
  <table class="table">
    <thead class="table-primary">
      <tr>
        <th>Card UID</th>
        <th>Name</th>
        <th>Gender</th>
        <th>Date</th>
        <th>Department</th>
        <th>Class</th>
        <th>Rooms</th>
        <th>No Phone</th>
      </tr>
    </thead>
    <tbody>
      <?php
        //Connect to database
        require 'connectDB.php';

        $sql = "SELECT * FROM users ORDER BY id DESC";
        $result = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($result, $sql)) {
            echo '<p class="error">SQL Error</p>';
        } else {
            mysqli_stmt_execute($result);
            $resultl = mysqli_stmt_get_result($result);
            if (mysqli_num_rows($resultl) > 0) {
                while ($row = mysqli_fetch_assoc($resultl)) {
      ?>
                  <tr>
                    <td>
                      <?php  
                        if ($row['card_select'] == 1) {
                            echo "<span><i class='glyphicon glyphicon-ok' title='The selected UID'></i></span>";
                        }
                        $card_uid = $row['card_uid'];
                      ?>
                      <button type="button" class="select_btn" id="<?php echo $card_uid;?>" title="Select this UID"><?php echo $card_uid;?></button>
                    </td>
                    <td><?php echo $row['username'];?></td>
                    <td><?php echo $row['gender'];?></td>
                    <td><?php echo $row['user_date'];?></td>
                    <td><?php echo $row['device_dep'];?></td>
                    <td><?php echo $row['class'];?></td>
                    <td><?php echo $row['no_room'];?></td>
                    <td><?php echo $row['phone_number'];?></td>
                  </tr>
      <?php
                }
            }
        }
        mysqli_stmt_close($result);
        mysqli_close($conn);
      ?>
    </tbody>
  </table>
</div>