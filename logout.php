<?php

session_start();
$_SESSION['status'] = FALSE;
session_destroy();
if (isset($_GET['disabled']) && $_GET['disabled'] == "yes")
    header("Location:index.php?ver=dis");
else {
    header("Location:index.php");
}
?>
