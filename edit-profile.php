<?php

include './config/db.php';
session_start();

$first = $_POST['first'];
$last = $_POST['last'];
$birth = $_POST['birth'];
$bio = $_POST['bio'];

$UID = $_SESSION['id'];

$get_user = mysql_query("SELECT * FROM user WHERE user_id ='$UID'");
$user = mysql_fetch_array($get_user);

if(empty($first)){
  $first = $user['firstname'];  
}

if(empty($last)){
  $last = $user['lastname'];  
}

if(empty($birth)){
  $birth = $user['birthday'];  
}

if(empty($bio)){
  $bio = $user['biography'];  
}

$query = "UPDATE `user` 
             SET 
               `firstname`='$first',
               `lastname`='$last',
               `birthday`='$birth', 
               `biography`='$bio' 
           WHERE `user_id` = '$UID'";

$result = mysql_query($query) or die($result . "<br/><br/>" . mysql_error());

mysql_close();
header("Location: profile.php?uid=$UID");
?>
