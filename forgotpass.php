<?php

include './config/db.php';

function randomPassword() {
    $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
    $pass = array(); //remember to declare $pass as an array
    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
    for ($i = 0; $i < 8; $i++) {
        $n = rand(0, $alphaLength);
        $pass[] = $alphabet[$n];
    }
    return implode($pass); //turn the array into a string
}

$email = $_POST['username0'];

$findemail = mysql_query("SELECT email FROM user WHERE email='$email' AND admin is NULL") or die($findemail . "<br/><br/>" . mysql_error());;

if (mysql_num_rows($findemail) > 0) {
    $newpass = randomPassword();
    $query = "UPDATE `user` 
             SET 
                password='$newpass',
               `temppass`='$newpass'
           WHERE `email` = '$email'";

    $result = mysql_query($query) or die($result . "<br/><br/>" . mysql_error());

    $subject = "iPload: New Password";
    $body = "Your temporary password is: " . $newpass . PHP_EOL . "You will have to change your password next time you log in.";
    
    mail($email, $subject, $body);

    mysql_close();
    echo 'success';
    header("Location: changepass.php?ver=success");
    exit();
    
} else {
    mysql_close();
    echo 'fail';
    header("Location: changepass.php?ver=fail");
    exit();
}
?>