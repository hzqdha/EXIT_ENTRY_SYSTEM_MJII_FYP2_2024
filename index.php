<?php
session_start();
if (!isset($_SESSION['Admin-name'])) {
    header("location: login.php");
}
?>


<!DOCTYPE html>
<html>
<head>
<link rel="website icon" type="png" href="icons/logoMJII.png">
    <title>mjii | Student List</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="images/favicon.png">

    <script type="text/javascript" src="js/jquery-2.2.3.min.js"></script>
    <script type="text/javascript" src="js/bootstrap.js"></script>
    <link rel="stylesheet" type="text/css" href="css/Users.css">
    <style>
        .container-section h1 {
            font-family: 'Arial', sans-serif;
            color: #343a40; /* Text color */
            text-align: center;
            margin-bottom: 20px;
        }

        /* Slide in down animation */
        @keyframes slideInDown {
            from {
                transform: translateY(-100%);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .slideInDown {
            animation: slideInDown 0.9s forwards;
        }

        /* Button style */
        .export-button {
            background-color: #ed1854; /* Soft red */
            color: #ffffff;
            border: none;
            border-radius: 10px;
            padding: 10px 20px;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.3s ease, box-shadow 0.3s ease;
            font-size: 16px;
            font-weight: bold;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.0);
        }

        .export-button:hover {
            background-color: #2184ee;
            transform: translateY(-3px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3);
        }

        /* Modal style */
        .modal-header, .modal-footer {
            background-color: #ffffff;
            color: #000000;
        }

        .modal-content {
            border-radius: 10px;
            border: 20px solid #007bff;
        }

        /* Table style */
        .table-responsive {
            background-color: transparent;
            border-radius: 20px;
            padding: 0px;
            box-shadow: 0px 0px 0px rgba(0, 0, 0, 0.1);
        }

        .table th, .table td {
            text-align: center;
            padding: 12px;
        }

        .table-primary {
            background-color: #007bff;
            color: #ffffff;
        }

        .table-secondary {
            background-color: #f1f1f1;
        }

        .filter-box {
            margin-bottom: 20px;
        }

        .form-style-5 {
            margin-top: 10px;
        }
    </style>

    <script>
        $(document).ready(function() {
            $('#dev_sel').on('change', function() {
                var department = $(this).val();
                if(department) {
                    $.ajax({
                        type: 'POST',
                        url: 'getClasses.php',
                        data: 'department=' + department,
                        success: function(html) {
                            $('#class_sel').html(html);
                        }
                    });
                } else {
                    $('#class_sel').html('<option value="">Select department first</option>');
                }
            });
        });
    </script>
</head>
<body>
    <?php include 'header.php'; ?> 
    <main>
        <section class="container-section">
            <h1 class="slideInDown animated">Here are all the Students</h1>

            <section>
                <div class="form-style-5 text-center">
                    <button type="button" class="export-button slideInDown" data-toggle="modal" data-target="#Filter-export">Filter Student List</button>
                </div>
            </section>

            <!-- Modal for Log Filter -->
            <div class="modal fade" id="Filter-export" tabindex="-1" role="dialog" aria-labelledby="FilterLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="FilterLabel">Log Filter</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form method="POST" action="">
                                <div class="filter-box">
                                    <div class="row">
                                        <div class="col-lg-12 col-sm-12">
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

                                        <div class="col-lg-12 col-sm-12">
                                            <label for="Class"><b>Filter By Class:</b></label>
                                            <select class="class_sel" name="class_sel" id="class_sel">
                                                <option value="0">All Classes</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <input type="submit" name="To_Excel" value="Filter" class="btn btn-primary">
                        </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- User table -->
            <div class="table-responsive slideInRight animated"> 
                <table class="table">
                    <thead class="table-primary">
                        <tr>
                            <th>ID | Name</th>
                            <th>Gender</th>
                            <th>Phone Number</th>
                            <th>Card UID</th>
                            <th>Date</th>
                            <th>Department</th>
                            <th>Class</th>
                            <th>Rooms</th>
                        </tr>
                    </thead>
                    <tbody class="table-secondary">
                        <?php
                          // Connect to database
                          require 'connectDB.php';

                          // Fetch filter values
                          $department_filter = isset($_POST['dev_sel']) ? $_POST['dev_sel'] : 0;
                          $class_filter = isset($_POST['class_sel']) ? $_POST['class_sel'] : 0;

                          $sql = "SELECT * FROM users WHERE add_card=1";

                          // Apply filters if selected
                          if ($department_filter != 0) {
                              $sql .= " AND device_dep = ?";
                          }
                          if ($class_filter != 0) {
                              $sql .= " AND class = ?";
                          }

                          $sql .= " ORDER BY id DESC";

                          $stmt = mysqli_stmt_init($conn);
                          if (!mysqli_stmt_prepare($stmt, $sql)) {
                              echo '<p class="error">SQL Error</p>';
                          } else {
                              if ($department_filter != 0 && $class_filter != 0) {
                                  mysqli_stmt_bind_param($stmt, "ss", $department_filter, $class_filter);
                              } elseif ($department_filter != 0) {
                                  mysqli_stmt_bind_param($stmt, "s", $department_filter);
                              } elseif ($class_filter != 0) {
                                  mysqli_stmt_bind_param($stmt, "s", $class_filter);
                              }

                              mysqli_stmt_execute($stmt);
                              $resultl = mysqli_stmt_get_result($stmt);
                              if (mysqli_num_rows($resultl) > 0){
                                  while ($row = mysqli_fetch_assoc($resultl)){
                        ?>
                                      <tr>
                                      <td><?php echo $row['id']; echo" | "; echo $row['username'];?></td>
                                      <td><?php echo $row['gender'];?></td>
                                      <td><?php echo $row['phone_number'];?></td>
                                      <td><?php echo $row['card_uid'];?></td>
                                      <td><?php echo $row['user_date'];?></td>
                                      <td><?php echo $row['device_dep'];?></td>
                                      <td><?php echo $row['class'];?></td>
                                      <td><?php echo $row['no_room'];?></td>
                                      </tr>
                        <?php
                                  }
                              } else {
                                  echo '<tr><td colspan="8">No users found</td></tr>';
                              }
                          }
                        ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</body>
</html>
