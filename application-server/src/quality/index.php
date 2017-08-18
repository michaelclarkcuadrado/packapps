<!DOCTYPE HTML>
<?php
require '../config.php';
$userData = packapps_authenticate_user('quality');
$Role = $userData['Role'];

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
