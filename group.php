<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<?php
include './config/db.php';
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
$gid = $_GET['gid'];
$query = mysql_query("SELECT * FROM user WHERE user_id ='$uid'");
$user = mysql_fetch_array($query);

if (($user['warning'] == 1 && $user['DeletedFiles'] == NULL)) {
    header("Location: confirmwarning.php?uid=$uid");
    exit();
}
if ($user['disabled'] == 1 && $_SESSION['id'] == $uid) {
    header("Location: logout.php?disabled=yes");
    exit();
}

$morequery = mysql_query("SELECT * FROM files WHERE deleted=0 AND group_id = $gid");
if (!empty($_GET['load'])) {
    $more = $_GET['load'];
    $file_query = mysql_query("SELECT * FROM files WHERE deleted=0 AND group_id = " . $gid . " ORDER BY file_id DESC LIMIT $more");
} else {
    $file_query = mysql_query("SELECT * FROM files WHERE deleted=0 AND group_id = " . $gid . " ORDER BY file_id DESC LIMIT 6");
}

$group_query = mysql_query("SELECT * FROM `group` WHERE group_id = $gid");
$member_query = mysql_query("SELECT * FROM `group_has_members` WHERE group_id = $gid");

$totalfiles = mysql_num_rows($file_query);
$totalmembers = mysql_num_rows($member_query);

$groupinfo = mysql_fetch_array($group_query);
?>
<html>
    <head>
        <title>iPload - Group</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <link rel="stylesheet" type="text/css" href="css/stylesheet.css" media="screen" />

    </head>
    <body onload="infoNotFound()">
        <div id="wrapper">
            <div id="logo">
                <h3>Group: <span style="color:purple"><?php echo $groupinfo['group_name'] ?></span></h3>
            </div>

            <ul id="menu">
                <li><a class="current" href="homepage.php">Home</a></li>
                <li><a href="profile.php?uid=<?php echo $uid ?>">Profile</a></li>
                <li><a href="#" id="upload" style="color: green">Upload to Group</a></li>
                <?php if ($groupinfo['user_id'] == $uid) { ?>
                    <li><a href="#" id="addm" style="color: green">Add Member</a></li>
                    <li><a href="deletegroup.php?gid=<?php echo $gid ?>" style="color: red">Delete Group</a></li>
                <?php } ?>
                <li><a href="logout.php">Log Out</a></li>
            </ul>

            <div class="news" id="member-block" style = "display:none">
                <form action="addmember.php" method="post" id="member_form" enctype="multipart/form-data">
                    <a href="#" id="addScnt">Add Member</a><nb>
                        <div id="p_scents">
                            <p>
                                <label for="p_scnts"><input type="text" id="p_scnt" size="20" name="p_scnt_1" value="" placeholder="Member Email" /></label>
                            </p>
                        </div>
                        <input type="hidden" id="count" name="count" value="1"/>
                        <input type="hidden" name="gid" value="<?php echo $gid ?>"/>
                        <button type="button" id="ambutton">Add</button>
                        <span id="amerror" style="color: red"></span>
                </form>

            </div>
            <div class="news" id="upload-block" style = "display:none">
                <form action="upload.php" method="post" id="upload_form" enctype="multipart/form-data">
                    <input type="file" name="file" id="file"/>
                    <span id="nofile" style="color: red"></span><br>
                        <input type="text" name="filename" id="filename" placeholder="File Name"/>
                        <span id="noname" style="color: red"></span><br>
                            <textarea type="text" cols="50" rows="5" name="desc" id="desc" placeholder="Description (optional)"></textarea><br>
                                <input type="hidden" name="from" value="fromgroup"/>
                                <input type="hidden" name="group" value="group"/>
                                <input type="hidden" name="gid" value="<?php echo $gid ?>"/>
                                <button type="button" id="fbutton">Upload</button> 
                                <span id="error" style="color: red"></span>
                                </form>

                                </div>
                                <?php if (trim($groupinfo['g_description']) != "") {
                                    ?>
                                    <div class="news">
                                        <p>Group Description: &nbsp;&nbsp;&nbsp;<?php echo $groupinfo['g_description'] ?></p>
                                    </div>
                                <?php } ?>
                                <div class="half">
                                    <h2 style="color:gray;display: inline">Group Uploads</h2><br></br>
                                    <?php
                                    if ($totalfiles > 0) {
                                        echo '<div style="overflow: auto;height: 150px">';
                                        while ($files = mysql_fetch_array($file_query)) {
                                            $file_owner_id = $files['user_id'];
                                            $get_user = mysql_query("SELECT firstname FROM user WHERE user_id = $file_owner_id");
                                            $user = mysql_fetch_array($get_user);
                                            $userName = strtolower($user['firstname']);
                                            $userfinal = ucfirst($userName);
                                            echo '<h3 class="allfiles"><span><a class="allfiles" href="download.php?fid=' . $files["file_id"] . '">' . $files["name"] . '</a></span></h3> 
                                          <span> <a class="allnames" href="profile.php?uid=' . $files["user_id"] . '">' . $userfinal . '</a></span><br>';
                                        }
                                        if (mysql_num_rows($morequery) > 6) {
                                            if (mysql_num_rows($morequery) != $totalfiles) {
                                                $loadnum = $totalfiles + 6;
                                                echo '<span><a href="group.php?gid=' . $gid . '&load=' . $loadnum . '">Load More</a></span>';
                                            }
                                        }
                                        echo '</div>';
                                    } else {
                                        echo 'No files <a href="#" id="noupload">Upload a file</a>';
                                    }
                                    ?>                          
                                </div>
                                <div class="half">
                                    <h2 style="color:gray;display: inline">Members </h2><span id="nf" style="color:red"></span><br></br>
                                    <?php
                                    $ismember = FALSE;
                                    if ($totalmembers > 0) {
                                        while ($memberinfo = mysql_fetch_array($member_query)) {
                                            $member_id = $memberinfo["user_id"];
                                            if ($uid == $member_id) {
                                                $ismember = TRUE;
                                            }
                                            $get_member = mysql_query("SELECT firstname FROM user WHERE user_id = $member_id");
                                            $member_name = mysql_fetch_array($get_member);
                                            $memberName = strtolower($member_name['firstname']);
                                            $memberfinal = ucfirst($memberName);
                                            echo '<h3 class="allfiles"><span><a class="allfiles" href="profile.php?uid=' . $memberinfo["user_id"] . '">' . $memberfinal . '</a></span></h3>';
                                            if ($member_id == $groupinfo['user_id']) {
                                                echo '<span> (Admin)</span><br>';
                                            } else if ($groupinfo['user_id'] == $uid) {
                                                echo '<span><a class="allnames" href="deletemember.php?gid=' . $gid . '&uid=' . $member_id . '">Delete</a></span><br>';
                                            } else {
                                                echo '<br>';
                                            }
                                        }
                                        if ($ismember == FALSE) {
                                            header("Location: homepage.php");
                                            exit();
                                        }
                                    }
                                    ?>
                                </div>
                                <div class="clear"></div>
                                </div>

                                <div align=center></div>

                                <script src="js/jquery.validate.min.js"></script>
                                <script src="js/jquery-1.7.2.min.js"></script>

                                </body>

                                <script>
                                    $(document).ready(function(){
                                        $('#upload').click(function() {
                                            $("#upload-block").fadeIn(600);
                                            $("#member-block").hide();
                                        });
                                    });
          
                                </script>
                                <script>
                                    $(document).ready(function(){
                                        $('#noupload').click(function() {
                                            $("#member-block").hide();
                                            $("#upload-block").fadeIn(600)();
                                              
                                        });
                                    });
          
                                </script>

                                <script>
                                    $(document).ready(function(){
                                        $('#addm').click(function() {
                                            $("#member-block").fadeIn(600);
                                            $("#upload-block").hide();
                                        });
                                    });
          
                                </script>
                                <script>
                                    $(document).ready(function(){
                                        $('#ambutton').click(function() {
                                            $("#member_form").submit();
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
                                                $('#upload_form').submit();
                                            }
                                        });
                                    });
           
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
                                        
                                        if(fail==="nf"){
                                            $('#nf').html("Some members were not found");
                                        }
                                    };
                                </script>


                                </html>
