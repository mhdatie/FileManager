<?php
//script to change current password given old and new password---------------

include './config/db.php';
session_start();

$oldpass = $_POST['oldpass'];
$newpass = $_POST['newpass'];

$UID = $_SESSION['id'];

$get_user = mysql_query("SELECT password FROM user WHERE user_id ='$UID'");
$user = mysql_fetch_array($get_user);

if ($oldpass != $user['password']) {
    if (isset($_POST['reset'])) {
        header("Location: resetpass.php?ver=old");
        mysql_close();
        exit();
    } else {
        header("Location: profile.php?uid=$UID&ver=old");
        mysql_close();
        exit();
    }
} else {
    if (isset($_POST['reset'])) {
        $query = "UPDATE `user` 
             SET 
               `password`='$newpass',
                temppass = NULL
           WHERE `user_id` = '$UID'";
    } else {
        $query = "UPDATE `user` 
             SET 
               `password`='$newpass'
           WHERE `user_id` = '$UID'";
    }
    $result = mysql_query($query) or die($result . "<br/><br/>" . mysql_error());

    mysql_close();
    if (isset($_POST['reset'])) {
        $_SESSION['reset'] = FALSE;
        header("Location: homepage.php");
    } else {
        header("Location: profile.php?uid=$UID");
    }
}
?>
