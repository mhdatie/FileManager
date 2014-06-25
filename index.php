<!doctype html>
<!--[if lt IE 7 ]><html class="ie ie6" lang="en"> <![endif]-->
<!--[if IE 7 ]><html class="ie ie7" lang="en"> <![endif]-->
<!--[if IE 8 ]><html class="ie ie8" lang="en"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--> 	<html lang="en"> <!--<![endif]-->
    <head>

        <!-- General Metas -->
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">	<!-- Force Latest IE rendering engine -->
        <title>iPload - Log In</title>
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
            <p class="warn">Please enter your whole information!</p>
        </div>

        <!-- Primary Page Layout -->
        
        <div class="container">

            <div class="form-bg">
                <form action="loginvalidate.php" method="post" id="login">
                    <h2>Welcome to iPloud - Login </h2>
                    <p><input type="text" placeholder="Enter your email" name="username0" id="username0"></p>
                    <p><input type="password" placeholder="Password" name="password0" id="password0"></p>
                    <label for="remember">
                    </label>
                    <button type="button" id="button">Log In</button>
                </form>
            </div>

            <p class="forgot">Forgot your password? <a href="changepass.php">Click here to reset it</a></p>
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
                    var fpswrd = $.trim($('#password0').val());
                    if (fusrnm.length < 1 || fusrnm === "" || fpswrd.length < 1 || fpswrd === "") {
                        $('.warn').html("Enter Your User Information");
                        $('#notice1').show();
                    } else {
                        if (checkEmail(fusrnm) === true) {
                            $('#login').submit();
                        } else {
                            $('#username0').empty();
                            $('#password0').empty();
                            $('.warn').html("Enter A Valid Email");
                            $('#notice1').show();
                        }
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
                if (ffail === "fail") {
                    $('.warn').html("Login Failed");
                    $('#notice1').show();
                }
                if(ffail=="dis"){
                    $('.warn').html("Account has been disabled!");
                    $('#notice1').show();
                }
            }
        </script>

    </body>
</html>