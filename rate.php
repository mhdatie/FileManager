<?php

include './config/db.php';
session_start();

$fid = $_GET["fid"];
$uid = $_SESSION["id"];
$rate = $_POST["rateinput"];


$query2 = mysql_query("SELECT * FROM rate WHERE file_id='$fid' AND user_id='$uid'");
if (mysql_num_rows($query2) > 0) {
    header("Location: download.php?fid=$fid");
    exit();
} else {
    $addratequery2 = mysql_query("INSERT INTO rate ( user_id, file_id, indiv_rate ) VALUES ('$uid','$fid','$rate')") or die(mysql_error());
    $sum = 0;
    $query = mysql_query("SELECT rate FROM files WHERE file_id ='$fid'");
    $ratearray = mysql_fetch_array($query);
    $totalrate = $ratearray['rate'];
    if ($totalrate == NULL) {
        $finalrate = $rate;
    } else {
        $numratequery = mysql_query("SELECT * FROM rate WHERE file_id='$fid'");
        $num = mysql_num_rows($numratequery);
        while ($ratings = mysql_fetch_array($numratequery)) {
            $sum+=$ratings['indiv_rate'];
        }
        $finalrate = $sum / $num;
    }
    $addratequery = mysql_query("UPDATE files SET rate='$finalrate' WHERE file_id='$fid'") or die(mysql_error());
    header("Location: download.php?fid=$fid");
    exit();
}
?>
