<?php
include './config/db.php';
include 'class/confirm.php';

$category = new Category();

session_start();

if (!isset($_SESSION['status']) || $_SESSION['status'] == FALSE || $_SESSION['reset']) {
    header("Location: index.php");
    exit();
}

$uid = $_GET["uid"];

if (isset($_SESSION['admin']) && $_SESSION['admin'] == TRUE) {
    if ($_SESSION['id'] == $uid) {
        header("Location: admin.php");
        exit();
    }
}

$query = mysql_query("SELECT * FROM user WHERE user_id ='$uid'");
$user = mysql_fetch_array($query);
$first2 = strtolower($user['firstname']);
$firstu = ucfirst($first2);
$last2 = strtolower($user['lastname']);
$lastu = ucfirst($last2);
$fullname = $firstu . " " . $lastu;

if (($user['warning'] == 1 && $user['DeletedFiles'] == NULL && $_SESSION['id'] == $uid)) {
    header("Location: confirmwarning.php?uid=$uid");
    exit();
}
if ($user['disabled'] == 1 && $_SESSION['id'] == $uid) {
    header("Location: logout.php?disabled=yes");
    exit();
}


$fileq = mysql_query("SELECT * FROM files WHERE user_id = $uid AND group_id=0 AND deleted=0 ORDER BY file_id DESC");
?>

<html>
    <head>
        <title>iPload - Profile</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <link rel="stylesheet" type="text/css" href="css/stylesheet.css" media="screen" />

    </head>
    <body onload="infoNotFound()">
        <div id="wrapper">
            <div id="logo">
                <?php
                if ($uid == $_SESSION['id']) {
                    echo '<h3>Your Profile</h3>';
                } else {
                    echo '<h3>Profile: <span style="color:purple">' . $fullname . '</span></h3>';
                }
                ?>

            </div>

            <ul id="menu">
                <li><a class="current" href="homepage.php">Home</a></li>
                <?php
                if ($_SESSION['admin'] == FALSE) {
                    echo '<li><a href = "profile.php?uid=' . $_SESSION['id'] . '">Profile</a></li>';
                    echo '<li><a href = "#" id = "upload">Upload</a></li>';
                }
                if ($uid != $_SESSION['id'] && $_SESSION['admin'] == FALSE) {
                    echo '<li><a href="#" id="request" style="color:green">Request File</a></li>';
                }
                if ($uid == $_SESSION['id'] && $_SESSION['admin'] == FALSE) {
                    echo '<li><a href="#" id="editprofile" style="color:green">Edit Profile</a></li>';
                    echo '<li><a href="#" id="changepass" style="color:green">Change Password</a></li>';
                }
                ?>
                <li><a href="logout.php">Log Out</a></li>

            </ul>

            <div class="news" id="upload-block" style = "display:none">
                <form action="upload.php" method="post" id="upload_form" enctype="multipart/form-data">
                    <input type="file" name="file" id="file"></input>
                    <span id="nofile" style="color: red"></span><br>
                    <input type="text" name="filename" id="filename" placeholder="File Name"></input>
                    <span id="noname" style="color: red"></span><br>
                    <input type="hidden" id="from" name="from" value="fromprofile"/>
                    <textarea type="text" cols="50" rows="5" name="desc" id="desc" placeholder="Description (optional)"></textarea><br>
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

            <div class="news" id="request-block" style = "display:none">
                <form action="request.php?uid=<?php echo $uid ?>" method="post" id="request_form">
                    <p>Make sure to be concise in your form</p>
                    <input type="text" name="subject" id="subject" placeholder="Subject"></input>
                    <span id="nosub" style="color: red"></span><br>
                    <textarea type="text" cols="50" rows="5" name="rbody" id="rbody" placeholder="Body.."></textarea>
                    <span id="nobody" style="color: red"></span><br>             
                    <button type="button" id="rbutton">Request!</button> 
                    <span id="rerror" style="color: red"></span>
                </form>
            </div>

            <div class="news" id="profile-block" style = "display:none">
                <form action="edit-profile.php" method="post" id="profile_form">
                    <p>Edit your profile information</p>
                    <input type="text" name="first" id="first" placeholder="First Name"></input><br>
                    <input type="text" name="last" id="last" placeholder="Last Name"></input><br>
                    <input type="date" name="birth" id="birth" placeholder="Birth Date"></input><br>
                    <textarea type="text" cols="50" rows="5" name="bio" id="bio" placeholder="Biography"></textarea><br>
                    <button type="button" id="pbutton">Change</button> 
                    <span id="perror" style="color: red"></span>
                </form>
            </div>

            <div class="news" id="pass-block" style = "display:none">
                <form action="change-password.php" method="post" id="password_form">
                    <p>Enter current and new password</p>
                    <input type="password" name="oldpass" id="oldpass" placeholder="Password"></input>
                    <input type="password" name="newpass" id="newpass" placeholder="New Password"></input><br>          
                    <button type="button" id="npbutton">Change Password</button> 
                    <span id="nperror" style="color: red"></span>
                </form>
            </div>

            <div class="news">
                <h2 style="display: inline">Profile Information </h2><?php
                        if ($user['DeletedFiles'] >= 1 && $user['warning'] < 3 && $_SESSION['admin'] == TRUE) {
                            $warn = $user['warning'];
                            echo '<span><a id="warning" style="color:red;font-size: 20px; text-decoration:none" href="warn.php?uid=' . $uid . '&warn=' . $warn . '">> Warn <</a></span>';
                        } elseif ($user['warning'] == 3 && $_SESSION['admin'] == TRUE) {
                             $warn = $user['warning'];
                            echo '<span><a id="disable" style="color:red;font-size: 20px; text-decoration:none" href="disable.php?uid=' . $uid . '&warn=' . $warn . '">> DISABLE <</a></span>';
                        }
                        ?>
                <h4>First Name: &nbsp;&nbsp;&nbsp;<?php echo $firstu ?></h4>
                <h4>Last Name: &nbsp;&nbsp;&nbsp;&nbsp;<?php echo $lastu ?></h4>
                <?php
                if ($user['birthday'] != 0000 - 00 - 00) {
                    echo '<h4>Birth Date: &nbsp;&nbsp;&nbsp;' . $user['birthday'] . '</h4>';
                }

                if ($user['biography'] != "") {
                    echo '<h4>Biography: &nbsp;&nbsp;&nbsp;' . $user['biography'] . '</h4>';
                }
                ?>

            </div>
            <div class="clear"></div>
            <div class="column">
                <h1 style="color:gray">User Document Files</h1><br>
                <?php
                $data_array = array();
                if (mysql_num_rows($fileq) > 0) {
                    $isthere = false;
                    while ($files = mysql_fetch_array($fileq)) {
                        $data_array[] = $files;
                        $type = $category->get_category($files["link"]);
                        if ($type == "document" || $type == "simple-document") {
                            $isthere = true;
                            echo '<h3 class="allfiles"><span><a class="allfiles" href="download.php?fid=' . $files["file_id"] . '">' . $files["name"] . '</a></span></h3> 
                           <span> <a class="allnames" href="profile.php?uid=' . $files["user_id"] . '">' . $firstu . '</a></span><br>';
                        }
                    }
                    if (!$isthere) {
                        echo 'No document files';
                    }
                } else {
                    if ($uid == $_SESSION['id']) {
                        echo 'No files <a href="#" id="noupload">Upload a file</a>';
                    } else {
                        echo 'No files';
                    }
                }
                ?>               
            </div>
            <div class="column">
                <h1 style="color:gray">User Audio Files</h1><br>
                <?php
                if (empty($data_array)) {
                    echo '<span>No files</span>';
                } else {
                    $thereis = false;
                    for ($i = 0; $i < sizeof($data_array); $i++) {
                        $type = $category->get_category($data_array[$i]["link"]);
                        if ($type == "audio") {
                            $thereis = true;
                            echo '<h3 class="allfiles"><span><a class="allfiles" href="download.php?fid=' . $data_array[$i]["file_id"] . '">' . $data_array[$i]["name"] . '</a></span></h3>
                                                <span> <a class="allnames" href="profile.php?uid=' . $data_array[$i]["user_id"] . '">' . $firstu . '</a></span><br>';
                        }
                    }
                    if (!$thereis) {
                        echo '<span>No audio files</span>';
                    }
                }
                ?>
            </div>
            <div class="column">
                <h1 style="color:gray">User Video Files</h1><br>
                <?php
                if (empty($data_array)) {
                    echo '<span>No files</span>';
                } else {
                    $thereis = false;
                    for ($i = 0; $i < sizeof($data_array); $i++) {
                        $type = $category->get_category($data_array[$i]["link"]);
                        if ($type == "video") {
                            $thereis = true;
                            echo '<h3 class="allfiles"><span><a class="allfiles" href="download.php?fid=' . $data_array[$i]["file_id"] . '">' . $data_array[$i]["name"] . '</a></span></h3>
                                               <span><a class="allnames" href="profile.php?uid=' . $data_array[$i]["user_id"] . '">' . $firstu . '</a></span><br>';
                        }
                    }
                    if (!$thereis) {
                        echo '<span>No vidio files</span>';
                    }
                }
                ?>
            </div>
            <div class="clear"></div>
            <div class="column">
                <h1 style="color:gray">User Image Files</h1><br>
                <?php
                if (empty($data_array)) {
                    echo '<span>No files</span>';
                } else {
                    $thereis = false;
                    for ($i = 0; $i < sizeof($data_array); $i++) {
                        $type = $category->get_category($data_array[$i]["link"]);
                        if ($type == "image") {
                            $thereis = true;
                            echo '<h3 class="allfiles"><span><a class="allfiles" href="download.php?fid=' . $data_array[$i]["file_id"] . '">' . $data_array[$i]["name"] . '</a></span></h3>
                                                <span><a class="allnames" href="profile.php?uid=' . $data_array[$i]["user_id"] . '">' . $firstu . '</a></span><br>';
                        }
                    }
                    if (!$thereis) {
                        echo '<span>No image files</span>';
                    }
                }
                ?>
            </div>
            <div class="column">
                <h1 style="color:gray">User Application Files</h1><br>
                <?php
                if (empty($data_array)) {
                    echo '<span>No files</span>';
                } else {
                    $thereis = false;
                    for ($i = 0; $i < sizeof($data_array); $i++) {
                        $type = $category->get_category($data_array[$i]["link"]);
                        if ($type == "application") {
                            $thereis = true;
                            echo '<h3 class="allfiles"><span><a class="allfiles" href="download.php?fid=' . $data_array[$i]["file_id"] . '">' . $data_array[$i]["name"] . '</a></span></h3>
                                                 <span><a class="allnames" href="profile.php?uid=' . $data_array[$i]["user_id"] . '">' . $firstu . '</a></span><br>';
                        }
                    }
                    if (!$thereis) {
                        echo '<span>No application files</span>';
                    }
                }
                ?>
            </div>
            <div class="clear"></div>
            <?php
            $group_query = mysql_query("SELECT * FROM group_has_members WHERE user_id ='" . $_SESSION['id'] . "'");
            $totalgroups = mysql_num_rows($group_query);

            if ($uid == $_SESSION['id']) {
                ?>
                <div class="half">
                    <h1 style="color:gray">Groups</h1>
                    <?php
                    if ($totalgroups <= 0) {
                        echo '<p>No groups <a href="#" id="createg">Create a group</a></p>';
                    } else {
                        while ($member = mysql_fetch_array($group_query)) {
                            $get_groups = mysql_query("SELECT * FROM `group` WHERE group_id = '" . $member['group_id'] . "'") or die($get_groups . "<br/><br/>" . mysql_error());
                            ;
                            $all_groups = mysql_fetch_array($get_groups);
                            echo '<h3 class="allfiles"><span><a class="allfiles" href="group.php?gid=' . $all_groups["group_id"] . '">' . $all_groups["group_name"] . '</a></span></h3>';
                            $adminId = $all_groups["user_id"];
                            $findadmin = mysql_query("SELECT * FROM user WHERE user_id='$adminId'");
                            $admin = mysql_fetch_array($findadmin);
                            if ($adminId == $_SESSION['id']) {
                                echo '<span><a class="allnames">Admin: You</a></span><br>';
                            } else {
                                $adminName = strtolower($admin['firstname']);
                                $adminfinal = ucfirst($adminName);
                                echo '<span><a class="allnames" href="profile.php?uid=' . $adminId . '">Admin: ' . $adminfinal . '</a></span><br>';
                            }
                        }
                        echo '<br><a href="#" id="createg">Create a group</a>';
                    }
                    ?>
                </div>
            <?php } ?>
            <div class="half" id="group-block"style="display:none">
                <form action="create-group.php" method="post" id="group_form">
                    <p>Create a new group</p>
                    <input type="text" name="groupname" id="groupname" placeholder="Group Name"/>
                    <span id="gname" style="color: red"></span><br>
                    <textarea type="text" cols="50" rows="5" name="groupdesc" id="groupdesc" placeholder="Description (optional)"></textarea><br><br>
                    <a href="#" id="addScnt">Add Member</a><nb>
                        <div id="p_scents">
                            <p>
                                <label for="p_scnts"><input type="text" id="p_scnt" size="20" name="p_scnt_1" value="" placeholder="Member Email" /></label>
                            </p>
                        </div>
                        <input type="hidden" id="count" name="count" value="1"/>
                        <button type="button" id="gbutton">Create Group</button> 
                        <button type="button" id="cancelg">Cancel</button>
                        <span id="gerror" style="color: red"></span>
                </form>
            </div>

        </div>
        <div align=center></div>

        <script src="js/jquery.validate.min.js"></script>
        <script src="js/jquery-1.7.2.min.js"></script>

    </body>
    <script>
        $(document).ready(function(){
            $('#createg').click(function() {
                $("#group-block").show(600);
            });
            $('#cancelg').click(function() {
                $("#group-block").hide(600);
            });
                    
        });
              
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
            $('#upload').click(function() {
                $("#request-block").hide();
                $("#pass-block").hide();
                $("#profile-block").hide();
                $("#upload-block").fadeIn(700)();
            });
        });
              
    </script>
    <script>
        $(document).ready(function(){
            $('#upload').click(function() {
                $("#request-block").hide();
                $("#pass-block").hide();
                $("#profile-block").hide();
                $("#upload-block").fadeIn(700)();
            });
        });
              
    </script>
    <script>
        $(document).ready(function(){
            $('#noupload').click(function() {
                $("#request-block").hide();
                $("#pass-block").hide();
                $("#profile-block").hide();
                $("#upload-block").fadeIn(600)();
            });
        });
              
    </script>

    <script>
        $(document).ready(function(){
            $('#request').click(function() {
                $("#upload-block").hide();
                $("#pass-block").hide();
                $("#profile-block").hide();
                $("#request-block").fadeIn(600)();
            });
        });
              
    </script>

    <script>
        $(document).ready(function(){
            $('#editprofile').click(function() {
                $("#upload-block").hide();
                $("#request-block").hide();
                $("#pass-block").hide();
                $("#profile-block").fadeIn(600)();
            });
        });
              
    </script>

    <script>
        $(document).ready(function(){
            $('#changepass').click(function() {
                $("#upload-block").hide();
                $("#request-block").hide();
                $("#profile-block").hide();
                $("#pass-block").fadeIn(600)();
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
        $(document).ready(function(){  
            $('#rbutton').click(function(){
                var subject = $.trim($('#subject').val());
                var body = $.trim($('#rbody').val());
                        
                if(subject == "" && body == "" ){
                    $('#nosub').html("Subject required");
                    $('#nobody').html("Details required");
                }else if (subject == "") {
                    $('#nosub').html("Subject required");
                    $('#nobody').html("");
                } else if(body == ""){
                    $('#nobody').html("Details required");
                    $('#nosub').html("");
                }
                        
                if(subject != "" && body != "" ){
                    $('#request_form').submit();
                }
            });
        });
               
    </script>
    <script>
        $(document).ready(function(){  
            $('#pbutton').click(function(){
                var first = $.trim($('#first').val());
                var last = $.trim($('#last').val());
                var birth = $.trim($('#birth').val());
                var bio = $.trim($('#bio').val());
                if(first !="" || last !="" || birth != "" || bio != ""){
                    $('#profile_form').submit();
                }else{
                    $("#perror").html("Please fill one of the fields");
                }
            });
        });
    </script>

    <script>
        $(document).ready(function(){  
            $('#npbutton').click(function(){
                var oldpass = $.trim($('#oldpass').val());
                var newpass = $.trim($('#newpass').val());
                       
                if(oldpass != "" && newpass != ""){
                    $('#password_form').submit();
                }else{
                    $("#nperror").html("Please fill the old and new password");
                }
            });
        });
    </script>

    <script>
        $(document).ready(function(){  
            $('#gbutton').click(function(){
                var groupname = $.trim($('#groupname').val());
                if(groupname != ""){
                    $('#group_form').submit();
                }else{
                    $("#gname").html("Choose a name for the group");
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
            if(fail === "old"){
                $("#nperror").html("Check your password"); 
                $("#pass-block").fadeIn(0)();
            }
        };
    </script>

    <script>
        $(function() {
            var scntDiv = $('#p_scents');
            var i = $('#p_scents p').size() + 1;
        
            $('#addScnt').live('click', function() {
                $('<p><label for="p_scnts"><input type="text" id="p_scnt" size="20" name="p_scnt_' + i +'" value="" placeholder="Member Email" /></label> <a href="#" id="remScnt">Remove</a></p>').appendTo(scntDiv);
                $("#count").val(i);
                i++;
                
                return false;
            });
        
            $('#remScnt').live('click', function() { 
                if( i > 2 ) {
                    $(this).parents('p').remove();
                    i--;
                }
                return false;
            });
        });
    </script>

</html>