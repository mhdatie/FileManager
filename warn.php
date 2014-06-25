<?php

include './config/db.php';

session_start();
if (!isset($_GET['uid']) || !isset($_GET['warn']) || $_GET['warn'] == 3 || $_SESSION['admin'] == FALSE) {
    echo 'errorrrr';
    exit();
}

$uid = $_GET['uid'];
$new_warn = $_GET['warn'];
$warn = $new_warn + 1;
if ($new_warn < 3) {
    $query_string = "UPDATE `user` 
             SET 
               `warning`='$warn',
                DeletedFiles = NULL
           WHERE `user_id` = '$uid'";
} else {
    echo 'error';
    exit();
}
$query = mysql_query($query_string);
mysql_close();
header("Location: profile.php?uid=$uid");
?>
