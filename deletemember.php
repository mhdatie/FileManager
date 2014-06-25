<?php

include './config/db.php';
session_start();
$gid = $_GET['gid'];
$uid = $_GET['uid'];
$query = mysql_query("DELETE FROM `group_has_members` WHERE user_id = $uid");
$dm = mysql_query("UPDATE `files` 
             SET 
               `deleted`='1'
             WHERE `group_id` = '$gid' AND `user_id` = '$uid'");

$movefiles = mysql_query("SELECT link FROM `files` WHERE group_id=$gid AND user_id = $uid");
while ($movefile = mysql_fetch_array($movefiles)) {
    copy($movefile['link'], './deleted_files/'.basename($movefile['link']));
    if(copy($movefile['link'], './deleted_files/'.basename($movefile['link']))){
        unlink($movefile['link']);
    }
}
rmdir("./private/group_" . $gid . "/user_" . $uid);

mysql_close();
header("Location: group.php?gid=$gid");
?>
