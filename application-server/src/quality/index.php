<!DOCTYPE HTML>
<?php
include '../config.php';

//authentication
if (!isset($_COOKIE['auth']) || !isset($_COOKIE['username'])) {
    die("<script>window.location.replace('/')</script>");
} else if (!hash_equals($_COOKIE['auth'], crypt($_COOKIE['username'], $securityKey))) {
    die("<script>window.location.replace('/')</script>");
} else {
    $SecuredUserName = mysqli_real_escape_string($mysqli, $_COOKIE['username']);
    $checkAllowed = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT allowedQuality, Role FROM packapps_master_users JOIN quality_UserData ON packapps_master_users.username=quality_UserData.UserName WHERE packapps_master_users.username = '$SecuredUserName'"));
    if (!$checkAllowed['allowedQuality'] > 0) {
        die ("<script>window.location.replace('/')</script>");
    } else {
        $RealName = $checkAllowed;
        $Role = $checkAllowed['Role'];
    }
}
// end authentication

include_once("Classes/Mobile_Detect.php");
$detect = new Mobile_Detect;

if ($Role == "QA") {
    if ($detect->isMobile()) {
        echo "<script>location.replace('mobileQA.php')</script>";
    } else {
        echo "<script>location.replace('QA.php')</script>";
    }
} elseif ($Role == "INS") {
    echo "<script>location.replace('Inspector.php')</script>";
} elseif ($Role == "Weight") {
    echo "<script>location.replace('WeightSamples.php')</script>";
} else {
    die("Error. Please check your account.");
}
