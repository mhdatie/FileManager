<?php
include './config/db.php';
session_start();
if (!isset($_SESSION['status']) || $_SESSION['status'] == FALSE) {
    header("Location: index.php");
    exit();
}

$uid = $_SESSION["id"];
$fid = $_GET["fid"];

$getfilequery = mysql_query("SELECT * FROM files WHERE file_id = '$fid'");
$query4rate = mysql_query("SELECT * FROM rate WHERE user_id='$uid' AND file_id='$fid'");
$repqury = mysql_query("SELECT * FROM report WHERE user_id='$uid' AND file_id='$fid'");

$queryname = mysql_query("SELECT * FROM user WHERE user_id ='$uid'");
$user = mysql_fetch_array($queryname);

if (($user['warning'] == 1 && $user['DeletedFiles'] == NULL)) {
    header("Location: confirmwarning.php?uid=$uid");
    exit();
}
if ($user['disabled'] == 1 && $_SESSION['id'] == $uid) {
    header("Location: logout.php?disabled=yes");
    exit();
}
?>
<html>
    <head>
        <title>iPload - Download</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <link rel="stylesheet" type="text/css" href="css/stylesheet.css" media="screen" />
        <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>


    </head>

    <body onload="Rated();
        Reported();
        infoNotFound();">
        <div id="wrapper">
            <div id="logo">
                <h3>Download Page</h3>

            </div>
            <ul id="menu">
                <li><a class="current" href="homepage.php">Home</a></li>
                <li><a href="profile.php?uid=<?php echo $_SESSION['id'] ?>">Profile</a></li>
                <li><a href="logout.php">Log Out</a></li>

            </ul>
            <div class="addthis_toolbox addthis_default_style addthis_32x32_style">
                <a class="addthis_button_preferred_1"></a>
                <a class="addthis_button_preferred_2"></a>
                <a class="addthis_button_preferred_3"></a>
                <a class="addthis_button_preferred_4"></a>
                <a class="addthis_button_compact"></a>
                <a class="addthis_counter addthis_bubble_style"></a>
            </div>
            <div class="news" id="view-block" style="padding:70px;margin:0px auto">
                <!-- AddThis Button BEGIN -->


                <!-- AddThis Button END -->
                <?php
                if (mysql_num_rows($getfilequery) > 0) {
                    $file = mysql_fetch_array($getfilequery);
                    $therate = $file["rate"];
                    $usfid = $file["user_id"];
                    $linkk = $file["link"];
                    $descr = $file["description"];
                    $ingroup = $file['group_id'];
                    if ($ingroup > 0) {
                        $ingroupquery = mysql_query("SELECT * FROM group_has_members WHERE group_id=$ingroup AND user_id =" . $_SESSION['id']);
                        if (mysql_num_rows($ingroupquery) <= 0 && $_SESSION['admin'] == FALSE) {
                            header("Location: homepage.php");
                            exit();
                        }
                    }
                    include './class/confirm.php';
                    $category = new Category();
                    $type = $category->get_category($linkk);
                    if ($type == "image") {
                        echo '<table style="margin:0px auto; position:relative">
                             <tr><td><img src="' . $linkk . '" width="400" height="400"/></td></tr>';
                    } elseif ($type == "document" || $type == "simple-document") {
                        echo '<table style="margin:0px auto; position:relative">
                            <tr><td>
                            <iframe src = "' . $linkk . '" scrolling = "yes" width = "500" height = "450" margin = "auto" display = "block">
                                <p>Microsoft documents are currently unviewable</p>
                                </iframe>                            
                            </td></tr>';
                    } elseif ($type == "audio") {
                        echo'<table style="margin:0px auto; position:relative">
                            <tr><td><iframe id = "ifrm" name = "ifrm" src = "' . $linkk . '" scrolling = "no" 
                            width = "500" height = "100" frameborder = "0">
                            <p> Audio file can\'t be played!</p>
                            </iframe></td></tr>';
                    } elseif ($type == "video") {
                        echo'<table style="margin:0px auto; position:relative">
                            <tr><td>
                            <video src="' . $linkk . '" height="360" width="540" controls></video>
                            </td></tr>';
                    } elseif ($type == "application") {
                        $var = substr($linkk, strrpos($linkk, '.') + 1); //checking if pdf
                        if ($var == "pdf") {
                            echo'<table style="margin:0px auto; position:relative">
                                <tr><td><iframe src = "' . $linkk . '" scrolling = "yes" width = "500" height = "450" margin = "auto" display = "block">
                                <p>Your browser does not support iframes</p>
                                </iframe></td></tr>';
                        }else{
                            echo 'Cannot view this type of file';
                        }
                    }
                    if ($_SESSION['admin'] == FALSE) {
                        echo '<tr><table style="margin:0px auto; position:relative"><tr><td style="text-align:center">
                        <form id="downloadfrm" action="download2.php?fid=' . $fid . '&link=' . $linkk . '" method="post"><button type="button" id="dwnbutton" >Download</button></form></td>
                        <td><form><a href="' . $linkk . '"><button type = "button" id = "pbutton">Larger view</button></a></td></form>
                         ';
                        if (mysql_num_rows($query4rate) <= 0 && $usfid != $uid) {
                            echo ' <td><form action="rate.php?fid=' . $fid . '" method="post" id="rateform" >
                        <select id="rateval" name="rateval">
                        <option value="1"> 1 </option>
                        <option value="2"> 2 </option>
                        <option value="3"> 3 </option>
                        <option value="4"> 4 </option>
                        <option value="5"> 5 </option>
                        <option value="6"> 6 </option>
                        <option value="7"> 7 </option>
                        <option value="8"> 8 </option>
                        <option value="9"> 9 </option>
                        <option value="10"> 10 </option>
                        </select>
                        <input type="hidden" id="rateinput" name="rateinput"/>
                        <button type="button" id="rbutton" method="post">Rate</button>
                        </form></td>';
                        }
                        if ($therate != NULL) {
                            echo '<td> Rate:' . $therate . '</td>';
                        }
                        if (mysql_num_rows($repqury) <= 0 && $usfid != $uid) {
                            echo '<td><form id="reportform" method="post" action="report.php?fid='.$fid.'"><button type="button" id="repbutton" >Report</button></form></td>';
                        }
                    }
                    if ($usfid === $uid || $_SESSION['admin'] == TRUE) {
                        echo'<td><form id="deletefrm" action="deletefile.php?fid=' . $fid . '&link=' . $linkk . '" method="post"><input type="button" id="deletebtn" value="Delete"/></form></td>';
                    }
                    echo'</tr>
                        </table>
                        </td></tr>';

                    if (trim($descr) != "") {
                        echo'<tr><td align="center"><h3><u>Description:</u>
                        ' . $descr . '</td></tr></table>';
                    }
                } else {
                    echo"File Not Found!";
                }
                ?>
            </div>

            <div class="clear"></div>



            <div align=center></div>


            <script src="js/jquery.validate.min.js"></script>
            <script src="js/jquery-1.7.2.min.js"></script>
            <script type="text/javascript">var addthis_config = {"data_track_addressbar":false};</script>
            <script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-52a3a86c37f309ab"></script>
    </body>

    <script>
        document.ready(function(){
            $(".flowplayer").bind("unload", function (e, api) {
                
            });
        })
    </script>

    <script>
        $(document).ready(function() {
            $('#deletebtn').click(function() {
                if (confirm("Are you sure you want to delete this file?"))
                {
                    $('#deletefrm').submit();
                } else {
                    window.reload();
                }
            })
        })
    </script>

    <script>
        $(document).ready(function() {
            $('#dwnbutton').click(function() {
                $('#downloadfrm').submit();
            })
        })
    </script>

    <script>
        $(document).ready(function() {
            $('#rbutton').click(function() {
                var e = $("#rateval option:selected").text();
                $('#rateinput').val(e);
                $('#rateform').submit();
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            $('#repbutton').click(function() {
                $('#reportform').submit();
            })
        });
    </script>



    <script>
        function Rated() {
            var fflag = "<?php echo $flag ?>";
            if (fflag === "rated")
            {
                $('#rbutton').attr("disabled",true);
                // document.getElementById("rbutton").disabled = true;
            }
        }
    </script>
    <script>
        function Reported() {
            var fflag2 = "<?php echo $flag2 ?>";
            if (fflag2 === "reported")
            {
                document.getElementById("repbutton").disabled = true;
            }
        }
    </script>


    <script>
        function infoNotFound() {
            var fail = "<?php echo $_GET["ver"] ?>";
            if (fail === "limit") {
                $('#error').html("Upload limit reached");
                $("#upload-block").fadeIn(0)();
            } else if (fail === "err") {
                $('#error').html("Error in upload");
                $("#upload-block").fadeIn(0)();
            } else if (fail === "exist") {
                $('#error').html("File already exists");
                $("#upload-block").fadeIn(0)();
            } else if (fail === "ns") {
                $('#error').html("File type not allowed");
                $("#upload-block").fadeIn(0)();
            }
            if (fail === "old") {
                $("#nperror").html("Check your password");
                $("#pass-block").fadeIn(0)();
            }

            if (fail === "fail")
            {
                $('#error').html("An error occured, your rate wasn't sent!");
                $("#upload-block").fadeIn(0)();
            }
        }
    </script>

</html>