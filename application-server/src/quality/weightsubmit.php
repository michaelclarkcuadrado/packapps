<?php
include '../config.php';

//authentication
if (!isset($_COOKIE['auth']) || !isset($_COOKIE['username'])) {
    die("<script>window.location.replace('/')</script>");
} else if (!hash_equals($_COOKIE['auth'], crypt($_COOKIE['username'], $securityKey))) {
    die("<script>window.location.replace('/')</script>");
} else {
    $SecuredUserName = mysqli_real_escape_string($mysqli, $_COOKIE['username']);
    $checkAllowed = mysqli_fetch_array(mysqli_query($mysqli, "SELECT `Real Name`, allowedQuality FROM master_users JOIN quality_UserData ON master_users.username=quality_UserData.UserName WHERE master_users.username = '$SecuredUserName'"));
    if (!$checkAllowed['allowedQuality'] > 0) {
        die ("<script>window.location.replace('/')</script>");
    } else {
        $RealName = $checkAllowed['Real Name'];
    }
}
// end authentication
if($_POST['weight']*1 == 0) {
    die("<html style='text-align: center'><meta name='viewport' content='width=device-width, initial-scale=1'><h1 style='color: red'>NOT RECEIVED.</h1><h3>There was a Wifi error. We did not insert that RT into the QA lab system. Try again.</h3><br>Attempted RT: " . $RT . ", Error listed as: No Weight data received.<br> <a href='' onclick='window.history.back();'><-- Go Back</a></html>");
}
$weightinlb = ($_POST['weight']*2.2);
$stmt = mysqli_prepare($mysqli, "INSERT INTO AggregateWeightSamples (`RT#`, `Weight`, InspectorName) VALUES (?, ?, ?)") or error_log(mysqli_error($mysqli));
mysqli_stmt_bind_param($stmt, "ids", $_POST['RT'], $weightinlb, $RealName);
if ($_POST['RT'] == $_POST['RT2'] && mysqli_stmt_execute($stmt)) {
    echo "<script>location.replace('WeightSamples.php?ins=".$_POST['RT']."')</script>";
} else {
    die ("<html style='text-align: center'><meta name='viewport' content='width=device-width, initial-scale=1'><h1 style='color: red'>NOT RECEIVED.</h1><h3>There was a database error. We did not insert that RT into the QA lab system. Try again.</h3><br>Attempted RT: " . $RT . ", Error listed as: " . mysqli_stmt_error($stmt) . "<br> <a href='' onclick='window.history.back();'><-- Go Back</a></html>");
}