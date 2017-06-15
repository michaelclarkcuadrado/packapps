<?php
require '../config.php';
require_once('emailAlerts/EmergencyAlert.php');

//get real name for logging accountability
//authentication
if (!isset($_COOKIE['auth']) || !isset($_COOKIE['username'])) {
    die("<script>window.location.replace('/')</script>");
} else if (!hash_equals($_COOKIE['auth'], crypt($_COOKIE['username'], $securityKey))) {
    die("<script>window.location.replace('/')</script>");
} else {
    $SecuredUserName = mysqli_real_escape_string($mysqli, $_COOKIE['username']);
    $checkAllowed = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT `Real Name` AS 'UserRealName', Role, allowedQuality FROM packapps_master_users JOIN quality_UserData ON packapps_master_users.username=quality_UserData.UserName WHERE packapps_master_users.username = '$SecuredUserName'"));
    if (!$checkAllowed['allowedQuality'] > 0) {
        die ("<script>window.location.replace('/')</script>");
    } else {
        $RealName = $checkAllowed;
    }
}
// end authentication
if ($RealName['Role'] !== "QA") {
    die("UNAUTHORIZED");
};
$Note = mysqli_real_escape_string($mysqli, $_POST['Notes']);
$RT = mysqli_real_escape_string($mysqli, $_POST['RT']);

//void RT
if (isset($_GET['del'])) {
    $_GET['del'] = mysqli_real_escape_string($mysqli, $_GET['del']);
    mysqli_query($mysqli, "DELETE FROM quality_InspectedRTs WHERE RTNum='" . $_GET['del'] . "'");
    packapps_deleteFromS3($availableBuckets['quality'], 'quality-rtnum-'.$_GET['del'].'.jpg');
    echo "<script>location.replace('QA.php?qa=" . $_GET['del'] . " has been <mark>voided</mark> and not #QA')</script>";
} else {
    //insert final inspection info
    mysqli_query($mysqli, "UPDATE `quality_InspectedRTs` SET `Note`='" . $Note . "', `isFinalInspected`='1' WHERE RTNum='" . $RT . "'");

    //Prepare Statement
    $stmt = mysqli_prepare($mysqli, "UPDATE `quality_AppleSamples` SET `Pressure1`=?, `Pressure2`=?, `Brix`=?, `Weight`=?,`FinalTestedBy`=? WHERE `RT#`=? AND SampleNum=?");
    mysqli_stmt_bind_param($stmt, 'ddddsii', $Pressure1, $Pressure2, $Brix, $Weight, $RealName['UserRealName'], $RT, $Num);
    for ($i = 1; $i < $_POST['NumSamples'] + 1; $i++) {
        $Num = $i;
        $Pressure1 = $_POST['pressure' . $i . '-1'];
        $Pressure2 = $_POST['pressure' . $i . '-2'];
        $Weight = $_POST['weight' . $i];
        if ($_POST['NumSamples'] > 5) {
            $Brix = $_POST['brix' . $i];
        } else {
            $Brix = null;
        }
        mysqli_stmt_execute($stmt);
    }
    echo "<script>location.replace('QA.php?qa=$RT#QA')</script>";
}