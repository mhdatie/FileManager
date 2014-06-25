<?php

include './config/db.php';

session_start();

$fuser = $_POST['username0'];
$fpass = $_POST['password0'];
$result00 = mysql_query("SELECT * FROM user WHERE email='$fuser' && password='$fpass'");
$row = mysql_fetch_array($result00);
#echo $row;

if ($row > 0) {
    if ($row['admin']) {
        $first = strtolower($row['firstname']);
        $firstu = ucfirst($first);
        $last = strtolower($row['lastname']);
        $lastu = ucfirst($last);
        $fullname = $firstu . " " . $lastu;
        $_SESSION['name'] = $fullname;
        $_SESSION['id'] = $row['user_id'];
        $_SESSION['status'] = TRUE;
        $_SESSION['reset'] = FALSE;
        
        $_SESSION['admin'] = TRUE;
        mysql_close();
        header("Location: admin.php");
    } else {
        $first = strtolower($row['firstname']);
        $firstu = ucfirst($first);
        $last = strtolower($row['lastname']);
        $lastu = ucfirst($last);
        $fullname = $firstu . " " . $lastu;
        $_SESSION['name'] = $fullname;
        $_SESSION['id'] = $row['user_id'];
        
        $_SESSION['status'] = TRUE;
        $_SESSION['admin'] = FALSE;
        mysql_close();
        if ($row['temppass']) {
            $_SESSION['reset'] = TRUE;
            header("Location: resetpass.php");
        } else {
            $_SESSION['reset'] = FALSE;
            header("Location: homepage.php");
        }
    }
} else {
    echo "false";
    $fail = "fail";
    mysql_close();
    header("Location: index.php?ver=" . $fail);
}
?>