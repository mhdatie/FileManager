<?php
include './config/db.php';
session_start();
if (!isset($_SESSION['status']) || $_SESSION['status'] == FALSE) {
    header("Location: index.php");
    exit();
}
$uid = $_SESSION['id'];

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
        <title>iPload - Search Result</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <link rel="stylesheet" type="text/css" href="css/stylesheet.css" media="screen" />
    </head>

    <body onload="infoNotFound();">
        <div id="wrapper">
            <div id="logo">
                <?php
                echo '<h3>Search Result</h3>';
                ?>
            </div>
            <p id="top">
            <form id="srchbx" action="search.php" method="post" align="right">
                <input type="text" name="search" id="search" placeholder="Enter you search here"/>
                <button type="button" id="searchbutton">Search</button>  
                <input type="radio" id="searchall" name="searchbx" value="all" checked>All</input> 
                <input type="radio" id="filesearch" name="searchbx" value="file">File</input>
                <input type="radio" id="usersearch" name="searchbx" value="user">User</input>
            </form>

            <ul id="menu">
                <li><a class="current" href="homepage.php">Home</a></li>
                <li><a href="profile.php?uid=<?php echo $_SESSION['id'] ?>">Profile</a></li>
                <li><a href="logout.php">Log Out</a></li>
            </ul>

            <div id="searchresult" style="padding:50px;margin:0px auto">
                <?php
                $searchkey = trim($_POST['search']);
                if ($_POST['searchbx'] == 'all') {
                    $srchquery = mysql_query("SELECT  f.name, f.file_id, f.user_id, u.firstname, u.lastname FROM files f, user u
                        WHERE f.group_id=0 AND f.deleted=0 AND f.user_id=u.user_id AND
                        (f.name LIKE '%$searchkey%') ORDER BY f.rate DESC") or die(mysql_error());
                    $srchquery2 = mysql_query("SELECT user_id, firstname, lastname FROM user WHERE admin IS NULL AND disabled=0 AND ((CONCAT(firstname, ' ' ,lastname) LIKE '%$searchkey%') OR (email = '$searchkey'))") or die(mysql_error());
                    if (mysql_num_rows($srchquery) > 0 && $searchkey != "") {
                        echo '<h1 style="color:grey">Your search result:</h1>';
                        echo '<div class="column"><h1 style="color:grey">Files</h1>';
                        while ($row = mysql_fetch_array($srchquery)) {
                            echo '<h3 class="allfiles"><span><a class="allfiles" href="download.php?fid=' . $row["file_id"] . '">' . $row["name"] . '</a></span></h3> 
                           <span> <a class="allnames" href="profile.php?uid=' . $row["user_id"] . '">' . ucfirst($row['firstname']).' '.ucfirst($row['lastname']). '</a></span><br>';
                        }
                        echo "</div>";
                    } else {
                        echo '<h1>No files matched!</h1>';
                    }
                    if (mysql_num_rows($srchquery2) > 0 && $searchkey != "") {
                        echo '<div class="column"><h1 style="color:grey">Users</h1>';
                        while ($row2 = mysql_fetch_array($srchquery2)) {
                            echo '<h3 class="allfiles"><span><a class="allfiles" href="profile.php?uid=' . $row2['user_id'] . '">' . ucfirst($row2["firstname"]) . ' ' . ucfirst($row2['lastname']) .'</a></span></h3><br>';
                        }
                        echo '</div>';
                    } else {
                        echo '<h1 style="color:grey">No users matched!</h1>';
                    }
                } elseif ($_POST['searchbx'] == 'file') {
                    $srchquery = mysql_query("SELECT  f.name, f.file_id, f.user_id, u.firstname, u.lastname FROM files f, user u
                        WHERE f.group_id=0 AND f.deleted=0 AND f.user_id=u.user_id AND
                        (f.name LIKE '%$searchkey%')ORDER BY f.rate DESC") or die(mysql_error());
                    if (mysql_num_rows($srchquery) > 0 && $searchkey != "") {
                        echo '<h1 style="color:grey">Your search result:</h1>';
                        echo '<div class="column" scrolling = "yes"><h1 style="color:grey">Files</h1>';
                        while ($row = mysql_fetch_array($srchquery)) {
                            echo '<h3 class="allfiles"><span><a class="allfiles" href="download.php?fid=' . $row["file_id"] . '">' . $row["name"] . '</a></span></h3> 
                           <span> <a class="allnames" href="profile.php?uid=' . $row["user_id"] . '">' . ucfirst($row['firstname']).' '.ucfirst($row['lastname']). '</a></span><br>';
                        }
                        echo '</div>';
                    } else {
                        echo "<h4>Sorry! We couldn't find anything!</h4>";
                    }
                } elseif ($_POST['searchbx'] == 'user') {
                    $srchquery = mysql_query("SELECT user_id, firstname, lastname FROM user
                        WHERE admin IS NULL AND disabled=0 AND ((CONCAT(firstname, ' ' ,lastname) LIKE '%$searchkey%') OR (email = '$searchkey'))") or die(mysql_error());
                    if (mysql_num_rows($srchquery) > 0 && $searchkey != "") {
                        echo '<h1 style="color:grey">Your search result:</h1>';
                        echo '<div class="column"><h1 style="color:grey">Users<h1>';
                        while ($row2 = mysql_fetch_array($srchquery)) {
                            echo '<h3 class="allfiles"><span><a class="allfiles" href="profile.php?uid=' . $row2['user_id'] . '">' . ucfirst($row2["firstname"]) . ' ' . ucfirst($row2['lastname']) .'</a></span></h3><br>';
                        }
                        echo '</div>';
                    } else {
                        echo"<h4>Sorry! We couldn't find anything!</h4>";
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
        $(document).ready(function() {
            $('#searchbutton').click(function() {
                $('#srchbx').submit();
            })
        })
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