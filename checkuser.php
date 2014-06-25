<?php

include './config/db.php';


$email = $_POST['username0'];
$pass = $_POST['password0'];
$query = mysql_query("SELECT * FROM user WHERE email='$email'");
$row = mysql_fetch_array($query);

if ($row > 0) {
    $fail = "fail";
    mysql_close();
    header("Location: register.php?ver=" . $fail);
} else {
    $first = $_POST['first'];
    $last = $_POST['last'];

    if (isset($_POST['birth'])) {
        $birth = $_POST['birth'];
     }

    if (isset($_POST['bio'])) {
        $bio = $_POST['bio'];
    }
    $query = "INSERT INTO user(email, password, firstname, lastname, birthday, biography, warning, disabled) VALUES ('$email','$pass','$first','$last','$birth','$bio',0,0)";

    $result = mysql_query($query);

    $fetch = mysql_query("SELECT * FROM user WHERE email='$email'");
    $row = mysql_fetch_array($fetch);

    $first2 = strtolower($first);
    $firstu = ucfirst($first2);
    $last2 = strtolower($last);
    $lastu = ucfirst($last2);
    $fullname = $firstu . " " . $lastu;

    session_start();
    $_SESSION['name'] = $fullname;

    $ID = $row['user_id'];
    $_SESSION['id'] = $ID;

    mkdir("./public/user_" . $ID);
    mysql_close();
    header("Location: homepage.php");
}
?>
