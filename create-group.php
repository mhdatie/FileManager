<?php

include './config/db.php';
session_start();

$UID = $_SESSION['id'];

$name = $_POST['groupname'];
$description = $_POST['groupdesc'];
$count = $_POST['count'];

$query = "INSERT INTO `group` (group_name, g_description, user_id) VALUES ('$name','$description','$UID')";
$result = mysql_query($query) or die($result . "<br/><br/>" . mysql_error());
$gid = mysql_insert_id();

mkdir("./private/group_$gid");

mkdir("./private/group_$gid/user_$UID");
$link = "./private/group_$gid/user_$UID";
$query2 = "INSERT INTO `group_has_members` (group_id, user_id, link) VALUES ('$gid','$UID','$link')";
$result2 = mysql_query($query2) or die($result2 . "<br/><br/>" . mysql_error());

for ($i = 1; $i <= $count; $i++) {
    $email = $_POST['p_scnt_' . $i];
    if (!empty($email)) {
        $getuser = mysql_query("SELECT * FROM user WHERE email='$email' AND admin IS NULL");
        $usernum = mysql_num_rows($getuser);
        if ($usernum > 0) {
            $user = mysql_fetch_array($getuser);
            $memberId = $user['user_id'];
            $check_query = mysql_query("SELECT * FROM group_has_members WHERE user_id='$memberId' AND group_id='$gid'");
            $checknum = mysql_num_rows($check_query);
            if ($memberId != $UID && $checknum == 0) {
                $memberlink = "./private/group_$gid/user_$memberId";
                mkdir($memberlink);
                $adduser = "INSERT INTO `group_has_members` (group_id, user_id, link) VALUES ('$gid','$memberId','$memberlink')";
                $added = mysql_query($adduser) or die($result2 . "<br/><br/>" . mysql_error());
            }
        } else {
            $error = 'nf';
        }
    }
}
mysql_close();
if (empty($error)) {
    header("Location: group.php?gid=$gid");
} else {
    header("Location: group.php?gid=$gid&ver=$error");
}
?>
