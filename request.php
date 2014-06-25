<?php

include './config/db.php';
session_start();

$sender_id = $_SESSION['id'];
$receiver_id = $_GET['uid'];
$subject = $_POST['subject'];
$body = $_POST['rbody'];

$sender_query = mysql_query("SELECT email FROM user WHERE user_id = $sender_id");
$sender_fetch = mysql_fetch_array($sender_query);
$sender_email = $sender_fetch['email'];

$receiver_query = mysql_query("SELECT email FROM user WHERE user_id = $receiver_id");
$receiver_fetch = mysql_fetch_array($receiver_query);
$receiver_email = $receiver_fetch['email'];

$body .= PHP_EOL.PHP_EOL.'This request is sent by: ' . $sender_email . PHP_EOL.'Profile: http://localhost/FileManager/profile.php?uid=' . $sender_id;

mail($receiver_email, $subject, $body);

mysql_close();
header("Location: profile.php?uid=$receiver_id");
?>
