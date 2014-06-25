
<?php
//admins view page-----------------------------------------------


include './config/db.php';
session_start();
if (!isset($_SESSION['status']) || $_SESSION['status'] == FALSE || $_SESSION['reset']) {
    header("Location: index.php");
    exit();
}
if (isset($_SESSION['admin']) && $_SESSION['admin'] == FALSE) {
    header("Location: homepage.php");
    exit();
}
$uid = $_SESSION['id'];

$file_query = mysql_query("SELECT * FROM files WHERE deleted=0 ORDER BY file_id DESC");
$delete_query = mysql_query("SELECT * FROM files WHERE deleted=0 AND reportcount >=1");
$warn_query = mysql_query("SELECT * FROM user WHERE DeletedFiles >=1 OR warning=3");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
    <head>
        <title>iPload - AdminHome</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <link rel="stylesheet" type="text/css" href="css/stylesheet.css" media="screen" />
    </head>
    <body>
        <div id="wrapper">
            <div id="logo">
                <h3><span style="color:palevioletred">Admin</span>: <?php echo $_SESSION['name'] ?></h3>
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
                <li><a class="current" href="admin.php">Home</a></li>
                <li><a href="logout.php">Log Out</a></li>
            </ul>
            <div class="column">
                <h1><span class="number">1</span> All Files</h1><br>
                    <?php
                    if (mysql_num_rows($file_query) > 0) {
                        while ($files = mysql_fetch_array($file_query)) {
                            $query = mysql_query("SELECT firstname FROM user WHERE user_id='" . $files['user_id'] . "'");
                            $name = mysql_fetch_array($query);
                            $first = strtolower($name['firstname']);
                            $firstu = ucfirst($first);
                            echo '<h3 class="allfiles"><a class="allfiles" href="download.php?fid=' . $files["file_id"] . '">' . $files["name"] . '</a></h3>
                <a class="allnames" href="profile.php?uid=' . $files["user_id"] . '">' . $firstu . '</a><br>';
                        }
                    } else {
                        echo '<span>No files</span>';
                    }
                    ?>
            </div>
            <div class="column">
                <h1><span class="number">2</span> Files to Delete</h1><br> 
                    <?php
                    if (mysql_num_rows($delete_query) > 0) {
                        while ($dfiles = mysql_fetch_array($delete_query)) {
                            echo '<h3 class="allfiles"><a class="allfiles" href="download.php?fid=' . $dfiles["file_id"] . '">' . $dfiles["name"] . '</a></h3><br>';
                        }
                    } else {
                        echo '<span>No files</span>';
                    }
                    ?>
            </div>
            <div class="column">
                <h1><span class="number">3</span> Users to Warn/Disable</h1> <br>
                    <?php
                    if (mysql_num_rows($warn_query) > 0) {
                        while ($users = mysql_fetch_array($warn_query)) {
                            $firstname = strtolower($users['firstname']);
                            $firstnameupper = ucfirst($firstname);
                            $lastname = strtolower($users['lastname']);
                            $lastnameupper = ucfirst($lastname);
                            echo '<a class="allfiles" href="profile.php?uid=' . $users["user_id"] . '">' . $firstnameupper . ' ' . $lastnameupper . '</a><br>';
                        }
                    } else {
                        echo '<span>No users, weird..</span>';
                    }
                    ?>
            </div>
            <div class="clear"></div>

            <div class="clear"></div>

        </div>
        <div align=center></div></body>
    <script src="js/jquery.validate.min.js"></script>
    <script src="js/jquery-1.7.2.min.js"></script>
   
    <script>
        $(document).ready(function(){
            $('#searchbutton').click(function(){
                $('#srchbx').submit();
            })
        })
    </script>

</html>




