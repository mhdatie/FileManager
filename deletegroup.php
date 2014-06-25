<?php

include './config/db.php';
session_start();
$gid = $_GET['gid'];
$uid = $_SESSION['id'];

$movefiles = mysql_query("SELECT link FROM `files` WHERE group_id=$gid");
while ($movefile = mysql_fetch_array($movefiles)) {
    copy($movefile['link'], './deleted_files/' . basename($movefile['link']));
    if (copy($movefile['link'], './deleted_files/' . basename($movefile['link']))) {
        unlink($movefile['link']);
    }
}
$movedirs = mysql_query("SELECT link FROM `group_has_members` WHERE group_id=$gid");
while ($movefile = mysql_fetch_array($movedirs)) {
    rmdir($movefile['link']);
}
rmdir("./private/group_" . $gid);

$query = mysql_query("DELETE FROM `group` WHERE group_id = $gid");
$dg = mysql_query("UPDATE `files` 
             SET 
               `deleted`='1' 
           WHERE `group_id` = '$gid'");


mysql_close();
header("Location: profile.php?uid=$uid");
?>
