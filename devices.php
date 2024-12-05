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
<title>mjii | Manage Devices</title>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" type="text/css" href="css/devices.css"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
<script type="text/javascript" src="js/jquery-2.2.3.min.js"></script>
<script src="https://code.jquery.com/jquery-3.3.1.js"
        integrity="sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60="
        crossorigin="anonymous"></script>
<script type="text/javascript" src="js/bootbox.min.js"></script>
<script type="text/javascript" src="js/bootstrap.js"></script>
<script src="js/dev_config.js"></script>
<script>
    $(window).on("load resize", function() {
        var scrollWidth = $('.tbl-content').width() - $('.tbl-content table').width();
        $('.tbl-header').css({'padding-right':scrollWidth});
    }).resize();
</script>
<script>
    $(document).ready(function(){
        // Initially hide the device list
        $('#devices').hide();

        // Load devices data using AJAX
        $.ajax({
            url: "dev_up.php",
            type: 'POST',
            data: {
                'dev_up': 1,
            }
        }).done(function(data) {
            // Update the #devices content
            $('#devices').html(data);

            // Smoothly fade in the content with slideInRight animation
            $('#devices').fadeIn(500).addClass('animate__animated animate__slideInRight');
        });
    });
</script>
</head>
<body>
<?php include'header.php';?>
<main>
    <h1 class="animate__animated animate__slideInDown">Add a new Device and Remove</h1>

    <section class="container py-lg-5">
        <div class="alert_dev"></div>
        <!-- devices -->
        <div class="row">
            <div class="col-lg-12 mt-4">
                <div class="panel">
                  <div class="panel-heading" style="font-size: 19px;">Your Devices:
                    <button type="button" class="btn btn-success" data-toggle="modal" data-target="#new-device" style="font-size: 18px; float: right; margin-top: -6px;">New Device</button>
                  </div>
                  <div class="panel-body">
                        <div id="devices"></div>
                  </div>
                </div>
            </div>
        </div>
        <!-- \\devices -->
        <!-- New Devices -->
        <div class="modal fade" id="new-device" tabindex="-1" role="dialog" aria-labelledby="New Device" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h3 class="modal-title" id="exampleModalLongTitle">Add new device:</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <form action="" method="POST" enctype="multipart/form-data">
                  <div class="modal-body">
                    <label for="User-mail"><b>Device Name:</b></label>
                    <input type="text" name="dev_name" id="dev_name" placeholder="Device Name..." required/><br>
                    <label for="User-mail"><b>Device Department:</b></label>
                    <input type="text" name="dev_dep" id="dev_dep" placeholder="Device Department..." required/><br>
                  </div>
                  <div class="modal-footer">
                    <button type="button" name="dev_add" id="dev_add" class="btn btn-success">Create new Device</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                  </div>
              </form>
            </div>
          </div>
        </div>
        <!-- //New Devices -->
    </section>
</main>
</body>
</html>
