<?php

include './config/db.php';
session_start();

$fid = $_GET["fid"];
$uid = $_SESSION["id"];

$getrepcount = mysql_query("SELECT reportcount FROM files WHERE file_id='$fid'");
$reparray = mysql_fetch_array($getrepcount);
$repcount = $reparray['reportcount'];
$checkrep = mysql_query("SELECT * FROM report WHERE user_id = $uid AND file_id = $fid");
if (mysql_num_rows($checkrep) > 0) {
    header("Location: download.php?fid=$fid");
    exit();
} else {
    $addrep = mysql_query("INSERT INTO report (user_id, file_id) VALUES ('$uid','$fid')");
    if ($repcount === null) {
        $repcount = 1;
        $updatereport = mysql_query("UPDATE files SET reportcount='$repcount' WHERE file_id='$fid'");
    } else {
        $repcount = $repcount + 1;
        $updatereport = mysql_query("UPDATE files SET reportcount='$repcount' WHERE file_id='$fid'");
    }
    header("Location: download.php?fid=$fid");
}
?>