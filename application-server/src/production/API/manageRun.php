<?php
/**
 * Created by PhpStorm.
 * User: Michael Clark-Cuadrado
 * Date: 5/25/2016
 * Time: 11:24 AM
 */
require '../../config.php';
//authentication
if (!isset($_COOKIE['auth']) || !isset($_COOKIE['username'])) {
    die("<script>window.location.replace('/')</script>");
} else if (!hash_equals($_COOKIE['auth'], crypt($_COOKIE['username'], $securityKey))) {
    die("<script>window.location.replace('/')</script>");
} else {
    $SecuredUserName = mysqli_real_escape_string($mysqli, $_COOKIE['username']);
    $checkAllowed = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT `Real Name` as UserRealName, Role, allowedProduction FROM packapps_master_users JOIN production_UserData ON packapps_master_users.username=production_UserData.UserName WHERE packapps_master_users.username = '$SecuredUserName'"));
    if (!$checkAllowed['allowedProduction'] > 0) {
        die ("<script>window.location.replace('/')</script>");
    } else {
        $RealName = $checkAllowed;
    }
}
if ($RealName['Role'] != 'Production') {
    die();
}
// end authentication

if($_GET['delete']) {
    //runinfo[0] = line, runinfo[1] = runnumber
    $_GET['delete'] = mysqli_real_escape_string($mysqli, $_GET['delete']);
    $runInfo = mysqli_fetch_array(mysqli_query($mysqli, "SELECT `Line`, RunNumber from `production_runs` where RunID='".$_GET['delete']."'"));
    mysqli_query($mysqli, "DELETE FROM production_runs WHERE RunID='".$_GET['delete']."'");
    mysqli_query($mysqli, "INSERT INTO production_chat VALUES ('', '" . $runInfo[0] . "', '" . mysqli_real_escape_string($mysqli, $SecuredUserName) . "', 'Run #" . $runInfo[1] . " deleted.')");
    packapps_deleteFromS3($availableBuckets['quality'], 'runPhoto-runid-'.$_GET['delete'].'.jpg');
}
else if($_GET['finish']) {
    mysqli_query($mysqli, "UPDATE `production_runs` SET `isCompleted` = NOT isCompleted WHERE RunID='".$_GET['finish']."'") or APIFail("Could not complete run");
    $line = mysqli_fetch_array(mysqli_query($mysqli, "SELECT `Line` from `production_runs` where RunID='".$_GET['finish']."'"));
    mysqli_query($mysqli, "INSERT INTO production_chat VALUES ('', '".$line[0]."', '".mysqli_real_escape_string($mysqli, $SecuredUserName)."', 'Run change @ ".date('g:ia').".')");
}