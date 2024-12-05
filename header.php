<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="website icon" type="png" href="icons/logoMJII.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel='stylesheet' type='text/css' href="css/bootstrap.css"/>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="css/header.css"/>
    <title>MJII RFID Attendance</title>
    <style>
        /* Aurora-like color animation for text */
        .logo a {
    font-size: 45px;
    text-decoration: none;
    font-weight: bold;
    background: linear-gradient(45deg, #ff4d4d, #ffffff, #4d4dff, #ff4d4d, #ffffff, #4d4dff);
    background-size: 200% 200%;
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    animation: auroraEffect 6s ease-in-out infinite;
}

@keyframes auroraEffect {
    0% { background-position: 0% 50%; }
    25% { background-position: 50% 50%; }
    50% { background-position: 100% 50%; }
    75% { background-position: 50% 50%; }
    100% { background-position: 0% 50%; }
}

         /* Background aurora effect */
         body {
            margin: 0;
            padding: 0;
            background: linear-gradient(45deg, #ff4d4d, #ffffff, #4d4dff, #ff4d4d, #ffffff, #4d4dff);
            background-size: 200% 200%;
            animation: auroraBackground 10s ease-in-out infinite;
            height: 150vh;

        }

@keyframes auroraBackground {
    0% { background-position: 0% 50%; }
    25% { background-position: 50% 50%; }
    50% { background-position: 100% 50%; }
    75% { background-position: 50% 50%; }
    100% { background-position: 0% 50%; }
}






        
        .topnav {
            background-color: rgba(255, 255, 255, 0);
            border: none;
            color: white;
            padding: 10px;
        }

        .topnav a {
            color: white;
            text-decoration: none;
            padding: 14px 20px;
            display: inline-block;
            position: relative;
            cursor: pointer;
            font-size: 17px;
            padding: 0.5rem 1rem;
            transition: color 0.25s;
        }

        .topnav a::after {
            content: '';
            position: absolute;
            inset: 0;
            background: #ff6f91;
            scale: 1 0;
            z-index: -1;
            transition: 0.45s;
            border-radius: 10px;
        }

        .topnav a:hover {
            color: black;
        }

        .topnav a:hover::after {
            scale: 1 1;
        }

        .container-transparent {
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
            margin: 20px auto;
            max-width: 90%;
        }

        .up_info1, .up_info2 {
            display: none;
        }
    </style>
</head>
<body>
    <header>
        <div class="header">
            <div class="logo">
                <a href="index.php">
                    <img src="icons/logoMJII.png" alt="Logo" style="height: 70px;"> MJII RFID System
                </a>
            </div>
        </div>
        
        <?php  
        if (isset($_GET['error'])) {
            if ($_GET['error'] == "wrongpasswordup") {
                echo '<script type="text/javascript">
                        setTimeout(function () {
                            $(".up_info1").fadeIn(200);
                            $(".up_info1").text("The password is wrong!!");
                            $("#admin-account").modal("show");
                        }, 500);
                        setTimeout(function () {
                            $(".up_info1").fadeOut(1000);
                        }, 3000);
                    </script>';
            }
        } 
        if (isset($_GET['success'])) {
            if ($_GET['success'] == "updated") {
                echo '<script type="text/javascript">
                        setTimeout(function () {
                            $(".up_info2").fadeIn(200);
                            $(".up_info2").text("Your Account has been updated");
                        }, 500);
                        setTimeout(function () {
                            $(".up_info2").fadeOut(1000);
                        }, 3000);
                    </script>';
            }
        }
        if (isset($_GET['login'])) {
            if ($_GET['login'] == "success") {
                echo '<script type="text/javascript">
                        setTimeout(function () {
                            $(".up_info2").fadeIn(200);
                            $(".up_info2").text("You successfully logged in");
                        }, 500);
                        setTimeout(function () {
                            $(".up_info2").fadeOut(1000);
                        }, 4000);
                    </script>';
            }
        }
        ?>

        <div class="container-transparent">
            <div class="topnav" id="myTopnav">
                <a href="index.php">Student List</a>
                <a href="ManageUsers.php">Manage Student</a>
                <a href="UsersLog.php">Student Log</a>
                <a href="StatisticGraph.php">Statistic</a>
                <a href="devices.php">Devices</a>
                <?php  
                    if (isset($_SESSION['Admin-name'])) {
                        echo '<a href="#" data-toggle="modal" data-target="#admin-account">'.$_SESSION['Admin-name'].'</a>';
                        echo '<a href="logout.php">Log Out</a>';
                    }
                    else{
                        echo '<a href="login.php">Log In</a>';
                    }
                ?>
                <a href="javascript:void(0);" class="icon" onclick="navFunction()">
                    <i class="fa fa-bars"></i></a>
            </div>
        </div>

        <div class="up_info1 alert-danger"></div>
        <div class="up_info2 alert-success"></div>
    </header>

    <script>
        function navFunction() {
            var x = document.getElementById("myTopnav");
            if (x.className === "topnav") {
                x.className += " responsive";
            } else {
                x.className = "topnav";
            }
        }
    </script>

    <div class="modal fade" id="admin-account" tabindex="-1" role="dialog" aria-labelledby="Admin Update" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title" id="exampleModalLongTitle">Update Your Account Info:</h3>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="ac_update.php" method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <label for="User-mail"><b>Admin Name:</b></label>
                        <input type="text" name="up_name" placeholder="Enter your Name..." value="<?php echo $_SESSION['Admin-name']; ?>" required/><br>
                        <label for="User-mail"><b>Admin E-mail:</b></label>
                        <input type="email" name="up_email" placeholder="Enter your E-mail..." value="<?php echo $_SESSION['Admin-email']; ?>" required/><br>
                        <label for="User-psw"><b>Password</b></label>
                        <input type="password" name="up_pwd" placeholder="Enter your Password..." required/><br>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="update" class="btn btn-success">Save changes</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
