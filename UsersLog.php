<?php
session_start();
if (!isset($_SESSION['Admin-name'])) {
    header("location: login.php");
}
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="website icon" type="image/png" href="icons/logoMJII.png">
    <title>mjii | Student Logs</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="css/userslog.css">
    <script type="text/javascript" src="js/jquery-2.2.3.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.3.1.js"
            integrity="sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60="
            crossorigin="anonymous"></script>
    <script type="text/javascript" src="js/bootbox.min.js"></script>
    <script type="text/javascript" src="js/bootstrap.js"></script>
    <script src="js/user_log.js"></script>
    <style>
       /* Switch Styling */
.switch {
    position: relative;
    display: inline-block;
    width: 60px; /* Increased width for smoother toggle movement */
    height: 30px; /* Increased height for better visibility */
}

.switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

.slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ddd;
    border-radius: 50px; /* Makes the switch rounded */
    transition: background-color 0.3s, transform 0.3s;
}

.slider:before {
    position: absolute;
    content: "";
    height: 20px; /* Ensures the toggle button is not too large */
    width: 20px;
    border-radius: 50%;
    left: 4px;
    bottom: 5px;
    background-color: white;
    transition: transform 0.3s;
}

input:checked + .slider {
    background-color: #4CAF50; /* Green color when active */
}

input:checked + .slider:before {
    transform: translateX(40px); /* Moves the toggle button when active */
}

/* Style for the "Student OUT" label */
label {
    font-size: 2.1rem;
    font-weight: bold;
    color: #black;
    margin-right: 5px;
    transition: all 0.3s ease-in-out;
}

.switch label {
    font-size: 1.1rem;
    color: #555;
    font-weight: normal;
    margin-top: 10px;
    transition: color 0.3s ease;
}

.switch input:checked + .slider + label {
    color: #4CAF50; /* Green color when toggle is active */
}

h1 {
    font-size: 2.5rem;
    font-weight: bold;
    color: #black;
    margin-bottom: 30px;
    text-align: center;
    animation: slideInDown 0.5s ease-out;
}

@keyframes slideInDown {
    from {
        transform: translateY(-20px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

    </style>
    <script>
      $(document).ready(function() {
          function loadUserLogs(filterOutOnly = false) {
              $.ajax({
                  url: "user_log_up.php",
                  type: 'POST',
                  data: { 
                      'select_date': 0,
                      'filter_out_only': filterOutOnly
                  }
              }).done(function(data) {
                  $('#userslog').html(data); // Update the content of the logs
              });
          }

          loadUserLogs();

          setInterval(function() {
              var toggleState = $('#toggleOutOnly').prop('checked');
              loadUserLogs(toggleState);
          }, 5000);

          $('#toggleOutOnly').change(function() {
              var toggleState = $(this).prop('checked');
              loadUserLogs(toggleState);
          });
      });
    </script>
</head>
<body>
<?php include 'header.php'; ?> 
<section class="container py-lg-5">
    <h1 class="slideInDown animated">Here are the Users daily logs</h1>
   
    <!-- Switch "Student OUT" -->
    <div align="center">
        <label>Student OUT:</label>
        <label class="switch">
            <input type="checkbox" id="toggleOutOnly">
            <span class="slider round"></span>
        </label>
    </div>
   
    <div class="form-style-5">
        <button type="button" data-toggle="modal" data-target="#Filter-export">Log Filter / Export to Excel</button>
    </div>
    <!-- Log filter -->
    <div class="modal fade bd-example-modal-lg" id="Filter-export" tabindex="-1" role="dialog" aria-labelledby="Filter/Export" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg animate" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title" id="exampleModalLongTitle">Filter Student Entry Exit:</h3>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" action="Export_Excel.php" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-lg-6 col-sm-12">
                                    <div class="panel panel-primary">
                                        <div class="panel-heading">Filter By:</div>
                                        <div class="panel-body">
                                            <input type="radio" id="radio-one" name="date_sel" value="Date_in" checked/>
                                            <label for="radio-one">Date-in</label>
                                            <input type="radio" id="radio-two" name="date_sel" value="date_out" />
                                            <label for="radio-two">Date-out</label>
                                            <br>
                                            <label for="Start-Date"><b>Select from this Date:</b></label>
                                            <input type="date" name="date_sel_start" id="date_sel_start">
                                            <label for="End-Date"><b>To End of this Date:</b></label>
                                            <input type="date" name="date_sel_end" id="date_sel_end">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-sm-12">
                                    <div class="panel panel-primary">
                                        <div class="panel-heading">Filter By:</div>
                                        <div class="panel-body">
                                            <input type="radio" id="radio-three" name="time_sel" value="Time_in" checked/>
                                            <label for="radio-three">Time-in</label>
                                            <input type="radio" id="radio-four" name="time_sel" value="Time_out" />
                                            <label for="radio-four">Time-out</label>
                                            <br>
                                            <label for="Start-Time"><b>Select from this Time:</b></label>
                                            <input type="time" name="time_sel_start" id="time_sel_start">
                                            <label for="End-Time"><b>To End of this Time:</b></label>
                                            <input type="time" name="time_sel_end" id="time_sel_end">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6 col-sm-12">
                                    <label for="Device"><b>Filter By Department:</b></label>
                                    <select class="dev_sel" name="dev_sel" id="dev_sel">
                                        <option value="0">All Departments</option>
                                        <?php
                                        require 'connectDB.php';
                                        $sql = "SELECT DISTINCT device_dep FROM users ORDER BY device_dep ASC";
                                        $result = mysqli_stmt_init($conn);
                                        if (!mysqli_stmt_prepare($result, $sql)) {
                                            echo '<p class="error">SQL Error</p>';
                                        } else {
                                            mysqli_stmt_execute($result);
                                            $resultl = mysqli_stmt_get_result($result);
                                            while ($row = mysqli_fetch_assoc($resultl)){
                                        ?>
                                            <option value="<?php echo $row['device_dep'];?>"><?php echo $row['device_dep']; ?></option>
                                        <?php
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-lg-6 col-sm-12">
                                    <label for="Export"><b>Export to Excel:</b></label>
                                    <input type="submit" name="To_Excel" value="Export">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- //Log filter -->
    <div class="slideInRight animated">
        <div id="userslog"></div>
    </div>
</section>
</body>
</html>
