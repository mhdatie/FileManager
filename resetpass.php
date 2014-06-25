<?php
include './config/db.php';
session_start();
if (!isset($_SESSION['status']) || $_SESSION['status'] == FALSE) {
    header("Location: index.php");
    exit();
}
?>

<!doctype html>

<!--[if (gte IE 9)|!(IE)]><!--> 	<html lang="en"> <!--<![endif]-->
    <head>

        <!-- General Metas -->
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">	<!-- Force Latest IE rendering engine -->
        <title>iPload - Reset Password</title>
        <meta name="description" content="">
        <meta name="author" content="">
        <!--[if lt IE 9]>
                <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
        <![endif]-->

        <!-- Mobile Specific Metas -->
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" /> 

        <!-- Stylesheets -->

        <link rel="stylesheet" href="css/base.css">
        <link rel="stylesheet" href="css/skeleton.css">
        <link rel="stylesheet" href="css/layout.css">

    </head>

    <body onload="infoNotFound()">

        <div id="notice1"class="notice" style = "display:none">
            <a href="#" class="close" id="close1">close</a>
            <p class="warn">Check Your Password</p>
        </div>

        <!-- Primary Page Layout -->

        <div class="container">

            <div class="form-bg">
                <form action="change-password.php" method="post" id="resetpass">
                    <h2>Enter your old and new password</h2>
                    <p><input type="password" placeholder="Password" name="oldpass" id="username0"></p>
                    <p><input type="password" placeholder="New Password" name="newpass" id="username1"></p>
                    <p><input type="hidden" name="reset" value="ok"></p>
                    <button type="button" id="button">Reset Password</button>
                </form>
            </div>

            <p class="forgot">Not Registered? <a href="register.php">Register Here</a></p>

        </div><!-- container -->

        <!-- JS -->
        <script src="js/jquery.validate.min.js"></script>
        <script src="js/jquery-1.7.2.min.js"></script>
        <script src="js/app.js"></script>


        <script>
            $(document).ready(function() {
                $('#button').click(function() {
                    var fusrnm = $.trim($('#username0').val());
                    var fpswrd = $.trim($('#username1').val());
                    if (fusrnm.length < 1 || fusrnm === "" || fpswrd.length < 1 || fpswrd === "") {
                        $('.warn').html("Enter old and new password");
                        $('#notice1').show();
                    } else {
                        $('#resetpass').submit();
                        
                    }
                });
            });

        </script>

        <script>
            $(document).ready(function() {
                $("#close1").click(function() {
                    $('#notice1').hide();
                });
            });</script>

        <script language = "Javascript">
            function checkEmail(emailId) {
                if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(emailId)) {
                    return true;
                }
                return false;
            }
        </script>

        <script>
            function infoNotFound() {
                var ffail = "<?php echo $_GET["ver"] ?>";
                if (ffail === "old") {
                    $('.warn').html("Check Your Password");
                    $('#notice1').show();
                }
            }
        </script>
    </body>
</html>