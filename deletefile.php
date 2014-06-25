<?php

include './config/db.php';
session_start();
$uid = $_SESSION['id'];
$fid = $_GET['fid'];
$file = $_GET['link'];

copy($file, './deleted_files/' . basename($file));
if (copy($file, './deleted_files/' . basename($file))) {
    unlink($file);
}
if ($_SESSION['admin'] == TRUE) {
    $deletequery = mysql_query("UPDATE files SET deleted='1' WHERE file_id='$fid'") or die(mysql_error());
    $fileownerquery = mysql_query("SELECT * FROM files WHERE file_id ='$fid'");
    $fileowner = mysql_fetch_array($fileownerquery);
    $fileownerID = $fileowner['user_id'];
    $queryname = mysql_query("SELECT * FROM user WHERE user_id ='$fileownerID'");
    $user = mysql_fetch_array($queryname);
    $deletenum = $user['DeletedFiles'];
    if($deletenum == NULL){
        $deleteinc = mysql_query("UPDATE user SET DeletedFiles='1' WHERE user_id='$fileownerID'") or die(mysql_error());
    }else{
        $newnum = $deletenum+1;
        $deleteinc = mysql_query("UPDATE user SET DeletedFiles='$newnum' WHERE user_id='$fileownerID'") or die(mysql_error());
    }
    
} else {
    $deletequery = mysql_query("UPDATE files SET deleted='1' WHERE user_id='$uid' AND file_id='$fid'") or die(mysql_error());
}

header("Location: homepage.php");
?>