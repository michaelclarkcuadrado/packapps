<?php
/**
 * Created by PhpStorm.
 * User: mac
 * Date: 5/25/2016
 * Time: 11:24 AM
 */
include '../config.php';
//authentication
if (!isset($_COOKIE['auth']) || !isset($_COOKIE['username'])) {
    die("<script>window.location.replace('/')</script>");
} else if (!hash_equals($_COOKIE['auth'], crypt($_COOKIE['username'], $securityKey))) {
    die("<script>window.location.replace('/')</script>");
} else {
    $SecuredUserName = mysqli_real_escape_string($mysqli, $_COOKIE['username']);
    $checkAllowed = mysqli_fetch_array(mysqli_query($mysqli, "SELECT `Real Name` as UserRealName, Role, isSectionManager as isAdmin, allowedProduction FROM master_users JOIN production_UserData ON master_users.username=production_UserData.UserName WHERE master_users.username = '$SecuredUserName'"));
    if (!$checkAllowed['allowedProduction'] > 0) {
        die ("<script>window.location.replace('/')</script>");
    } else {
        $RealName = $checkAllowed;
    }
}
// end authentication
if ($RealName[1] != 'Production')
{
    die();
}

if($_GET['delete'])
{
    mysqli_query($mysqli, "DELETE FROM production_runs WHERE RunID='".$_GET['delete']."'");
    mysqli_query($mysqli, "DELETE FROM run_inspections WHERE RunID='".$_GET['delete']."'");
    unlink("../quality/assets/uploadedimages/runs/".$_GET['delete'].".jpg");
}
else if($_GET['finish'])
{
    mysqli_query($mysqli, "UPDATE `production_runs` SET `isCompleted` = NOT isCompleted WHERE RunID='".$_GET['finish']."'") or die(mysqli_error($mysqli));
    $line = mysqli_fetch_array(mysqli_query($mysqli, "SELECT `Line` from `production_runs` where RunID='".$_GET['finish']."'"));
    mysqli_query($mysqli, "INSERT INTO production_chat VALUES ('', '".$line[0]."', '".mysqli_real_escape_string($mysqli, $SecuredUserName)."', 'Run change @ ".date('g:ia').".')");
}