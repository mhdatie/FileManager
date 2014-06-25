<?php

include './config/db.php';

session_start();
$uid = $_GET['uid'];
echo 'You have received a warning from uploading files that are either corrupted or inappropriate, any other attempt and your account will be disabled.';
echo '<br><br><span><a href="confirmwarning.php?runFunction=updateWarning&uid=' . $uid . '">I understand</a></span>';


if (isset($_GET['runFunction']) && function_exists($_GET['runFunction']))
    call_user_func($_GET['runFunction']);

function updateWarning() {
    $uid = $_GET['uid'];
    $query_string = "UPDATE `user` 
             SET 
               `warning`=2,
               DeletedFiles = NULL
           WHERE `user_id` = '$uid'";
    $query = mysql_query($query_string);
    mysql_close();
    header("Location: homepage.php");
    echo("works");
}

?>
