<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<?php
include './config/db.php';
include 'class/confirm.php';

$category = new Category();

session_start();

if (!isset($_SESSION['status']) || $_SESSION['status'] == FALSE || $_SESSION['reset']) {
    header("Location: index.php");
    exit();
}
if (isset($_SESSION['admin']) && $_SESSION['admin'] == TRUE) {
    header("Location: admin.php");
    exit();
}

$uid = $_SESSION['id'];


$query = mysql_query("SELECT * FROM files WHERE group_id=0 AND deleted=0 ORDER BY file_id DESC LIMIT 7");

$queryname = mysql_query("SELECT * FROM user WHERE user_id ='$uid'");
$user = mysql_fetch_array($queryname);
$first2 = strtolower($user['firstname']);
$firstu = ucfirst($first2);
$last2 = strtolower($user['lastname']);
$lastu = ucfirst($last2);
$fullname = $firstu . " " . $lastu;

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
        <title>iPload - Home</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <link rel="stylesheet" type="text/css" href="css/stylesheet.css" media="screen" />

    </head>
    <body onload="infoNotFound()">
        <div id="wrapper">
            <div id="logo">
                <h3>Welcome <?php echo $fullname ?>!</h3>
            </div>
            <p id="top">
                <form id="srchbx" action="search.php" method="post" align="right">
                    <input type="text" name="search" id="search" placeholder="Enter you search here"/>
                    <button type="button" id="searchbutton">Search</button>  
                    <input type="radio" id="searchall" name="searchbx" value="all" checked>All</input> 
                    <input type="radio" id="filesearch" name="searchbx" value="file">File</input>
                    <input type="radio" id="usersearch" name="searchbx" value="user">User</input>
                </form>
            </p>
            <ul id="menu">
                <li><a class="current" href="homepage.php">Home</a></li>
                <li><a href="profile.php?uid=<?php echo $_SESSION['id'] ?>">Profile</a></li>
                <li><a href="#" id="upload">Upload</a></li>
                <li><a href="logout.php">Log Out</a></li>
            </ul>

            <div class="news" id="upload-block" style = "display:none">
                <form action="upload.php" method="post" id="upload_form" enctype="multipart/form-data">
                    <input type="file" name="file" id="file"></input>
                    <span id="nofile" style="color: red"></span><br>
                        <input type="text" name="filename" id="filename" placeholder="File Name"></input>
                        <span id="noname" style="color: red"></span><br>
                            <textarea type="text" cols="50" rows="5" name="desc" id="desc" placeholder="Description (optional)"></textarea><br>
                                <input type="hidden" id="from" name="from" value="fromhome"/>
                                <div name="groupchoice" id="groupdrop" style = "display:none"> 
                                    <?php
                                    $member_query = mysql_query("SELECT * FROM `group_has_members` WHERE user_id = $uid");
                                    if (mysql_num_rows($member_query) > 0) {
                                        echo "<select name='gid'>";
                                        while ($allgroups = mysql_fetch_array($member_query)) {
                                            $mgi = $allgroups['group_id'];
                                            $gnames = mysql_query("SELECT group_id, group_name FROM `group` WHERE group_id = $mgi");
                                            $ginfo = mysql_fetch_array($gnames);
                                            echo "<option value='" . $ginfo['group_id'] . "'>" . $ginfo['group_name'] . "</option>";
                                        }
                                        echo "</select>";
                                    } else {
                                        echo "<span id='nogroups'>No Groups</span>";
                                    }
                                    ?>
                                </div>  
                                <input type="radio" id="pub" name="group" value="public" checked>Public</input>
                                <input type="radio" id="grup" name="group" value="group">Group</input>
                                <button type="button" id="fbutton">Upload</button> 
                                <span id="error" style="color: red"></span>
                                </form>
                                </div>

                                <div class="column">
                                    <h1 style="color:gray">Recent Document Files</h1><br>
                                        <?php
                                        $data_array = array();
                                        $name_array = array();
                                        if (mysql_num_rows($query) > 0) {
                                            $thereis = false;
                                            while ($files = mysql_fetch_array($query)) {
                                                $data_array[] = $files;
                                                $query2 = mysql_query("SELECT firstname FROM user WHERE user_id='" . $files['user_id'] . "'");
                                                $name = mysql_fetch_array($query2);
                                                $name_array[] = $name;
                                                $first2 = strtolower($name['firstname']);
                                                $firstu = ucfirst($first2);

                                                $type = $category->get_category($files["link"]);
                                                if ($type == "document" || $type == "simple-document") {
                                                    $thereis = true;
                                                    echo '<h3 class="allfiles"><a class="allfiles" href="download.php?fid=' . $files["file_id"] . '">' . $files["name"] . '</a></h3>
                                                 <a class="allnames" href="profile.php?uid=' . $files["user_id"] . '">' . $firstu . '</a><br>';
                                                }
                                            }
                                            if (!$thereis) {
                                                echo '<span>No document files</span>';
                                            }
                                        } else {
                                            echo '<span>No files</span>';
                                        }
                                        ?>               
                                </div>
                                <div class="column">
                                    <h1 style="color:gray">Recent Audio Files</h1><br>
                                        <?php
                                        if (empty($data_array)) {
                                            echo '<span>No files</span>';
                                        } else {
                                            $thereis = false;
                                            for ($i = 0; $i < sizeof($data_array); $i++) {
                                                $first2 = strtolower($name_array[$i]['firstname']);
                                                $firstu = ucfirst($first2);
                                                $type = $category->get_category($data_array[$i]["link"]);
                                                if ($type == "audio") {
                                                    $thereis = true;
                                                    echo '<h3 class="allfiles"><a class="allfiles" href="download.php?fid=' . $data_array[$i]["file_id"] . '">' . $data_array[$i]["name"] . '</a></h3>
                                                 <a class="allnames" href="profile.php?uid=' . $data_array[$i]["user_id"] . '">' . $firstu . '</a><br>';
                                                }
                                            }
                                            if (!$thereis) {
                                                echo '<span>No audio files</span>';
                                            }
                                        }
                                        ?>
                                </div>
                                <div class="column">
                                    <h1 style="color:gray">Recent Video Files</h1><br>
                                        <?php
                                        if (empty($data_array)) {
                                            echo '<span>No files</span>';
                                        } else {
                                            $thereis = false;
                                            for ($i = 0; $i < sizeof($data_array); $i++) {
                                                $first2 = strtolower($name_array[$i]['firstname']);
                                                $firstu = ucfirst($first2);
                                                $type = $category->get_category($data_array[$i]["link"]);
                                                if ($type == "video") {
                                                    $thereis = true;
                                                    echo '<h3 class="allfiles"><a class="allfiles" href="download.php?fid=' . $data_array[$i]["file_id"] . '">' . $data_array[$i]["name"] . '</a></h3>
                                                 <a class="allnames" href="profile.php?uid=' . $data_array[$i]["user_id"] . '">' . $firstu . '</a><br>';
                                                }
                                            }
                                            if (!$thereis) {
                                                echo '<span>No video files</span>';
                                            }
                                        }
                                        ?>
                                </div>
                                <div class="clear"></div>
                                <div class="column">
                                    <h1 style="color:gray">Recent Image Files</h1><br>
                                        <?php
                                        if (empty($data_array)) {
                                            echo '<span>No files</span>';
                                        } else {
                                            $thereis = false;
                                            for ($i = 0; $i < sizeof($data_array); $i++) {
                                                $first2 = strtolower($name_array[$i]['firstname']);
                                                $firstu = ucfirst($first2);
                                                $type = $category->get_category($data_array[$i]["link"]);
                                                if ($type == "image") {
                                                    $thereis = true;
                                                    echo '<h3 class="allfiles"><a class="allfiles" href="download.php?fid=' . $data_array[$i]["file_id"] . '">' . $data_array[$i]["name"] . '</a></h3>
                                                 <a class="allnames" href="profile.php?uid=' . $data_array[$i]["user_id"] . '">' . $firstu . '</a><br>';
                                                }
                                            }
                                            if (!$thereis) {
                                                echo '<span>No image files</span>';
                                            }
                                        }
                                        ?>
                                </div>
                                <div class="column">
                                    <h1 style="color:gray">Recent Applications</h1><br>
                                        <?php
                                        if (empty($data_array)) {
                                            echo '<span>No files</span>';
                                        } else {
                                            $thereis = false;
                                            for ($i = 0; $i < sizeof($data_array); $i++) {
                                                $first2 = strtolower($name_array[$i]['firstname']);
                                                $firstu = ucfirst($first2);
                                                $type = $category->get_category($data_array[$i]["link"]);
                                                if ($type == "application") {
                                                    $thereis = true;
                                                    echo '<h3 class="allfiles"><a class="allfiles" href="download.php?fid=' . $data_array[$i]["file_id"] . '">' . $data_array[$i]["name"] . '</a></h3>
                                                 <a class="allnames" href="profile.php?uid=' . $data_array[$i]["user_id"] . '">' . $firstu . '</a><br>';
                                                }
                                            }
                                            if (!$thereis) {
                                                echo '<span>No applications</span>';
                                            }
                                        }
                                        ?>
                                </div>
                                <div class="clear"></div>

                                <div class="clear"></div>

                                </div>
                                <script src="js/jquery.validate.min.js"></script>
                                <script src="js/jquery-1.7.2.min.js"></script>

                                </body>

                                <script>
                                    $(document).ready(function(){
                                        $('#upload').click(function() {
                                            $("#upload-block").fadeIn(600)();
                                        });
                                    });
      
                                </script>
                                <script>
                                    $(document).ready(function(){
                                        $('#searchbutton').click(function(){
                                            $('#srchbx').submit();
                                        })
                                    })
                                </script>
                                <script>
                                    $(document).ready(function(){
                                        $('#pub').click(function() {
                                            $("#groupdrop").hide();
                                        });
                                        $('#grup').click(function() {
                                            $("#groupdrop").fadeIn(400);
                                        });
            
                                    });
                                </script>

                                <script>
                                    $(document).ready(function(){  
                                        $('#fbutton').click(function(){
                                            var has_selected_file = $.trim($('input[type=file]').val());
                                            var filename = $.trim($('#filename').val());
                                            if (has_selected_file == "") {
                                                $('#nofile').html("Select a file");
                                            } else if(filename == ""){
                                                $('#noname').html("Choose a name");
                                                $('#nofile').html("");
                                            }
                                            
                                            if(has_selected_file != "" && filename != "" ){
                                                if($('#grup').is(':checked')){
                                                    if($('#nogroups').html() == "No Groups"){
                                                        // alert('yes2');
                                                        $('#nofile').html("");
                                                        $('#noname').html("");
                                                        $('#error').html("You have no groups to upload to");
                                                    }else{
                                                        $('#from').val("fromgroup");
                                                        $('#upload_form').submit();
                                                    }
                                                }else{
                                                    $('#upload_form').submit();
                                                }
                                            }
                                        });
                                    });
       
                                </script>

                                <script>
                                    function infoNotFound() {
                                        var fail = "<?php echo $_GET["ver"] ?>";
                                        if (fail === "limit") {
                                            $('#error').html("Upload limit reached");
                                            $("#upload-block").fadeIn(0)();
                                        }else if(fail === "err"){
                                            $('#error').html("Error in upload");
                                            $("#upload-block").fadeIn(0)();
                                        }else if(fail === "exist"){
                                            $('#error').html("File already exists");
                                            $("#upload-block").fadeIn(0)();
                                        }else if (fail==="ns"){
                                            $('#error').html("File type not allowed");
                                            $("#upload-block").fadeIn(0)();
                                        }
                                    };
                                </script>

                                <?php mysql_close(); ?>
                                </html>




