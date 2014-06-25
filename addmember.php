<?php
// adds an existing user to a group
include './config/db.php';
session_start();

$UID = $_SESSION['id'];
$gid = $_POST['gid'];
$count = $_POST['count'];

for ($i = 1; $i <= $count; $i++) {
    $email = $_POST['p_scnt_' . $i];
    if (!empty($email)) {
        $getuser = mysql_query("SELECT * FROM user WHERE email='$email' AND admin IS NULL");
        $usernum = mysql_num_rows($getuser);
        if ($usernum > 0) {
            echo 'ok';
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
}else{
   header("Location: group.php?gid=$gid&ver=$error");
}
?>
