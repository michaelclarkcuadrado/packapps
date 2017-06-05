<?php
include '../config.php';
require_once('emailAlerts/EmergencyAlert.php');

//get real name for logging accountability
//authentication
if (!isset($_COOKIE['auth']) || !isset($_COOKIE['username'])) {
    die("<script>window.location.replace('/')</script>");
} else if (!hash_equals($_COOKIE['auth'], crypt($_COOKIE['username'], $securityKey))) {
    die("<script>window.location.replace('/')</script>");
} else {
    $SecuredUserName = mysqli_real_escape_string($mysqli, $_COOKIE['username']);
    $checkAllowed = mysqli_fetch_array(mysqli_query($mysqli, "SELECT `Real Name`, Role, isSectionManager as isAdmin, allowedQuality FROM master_users JOIN quality_UserData ON master_users.username=quality_UserData.UserName WHERE master_users.username = '$SecuredUserName'"));
    if (!$checkAllowed['allowedQuality'] > 0) {
        die ("<script>window.location.replace('/')</script>");
    } else {
        $RealName = $checkAllowed;
        $Role = $checkAllowed['Role'];
    }
}
// end authentication
if ($RealName[1] !== "QA") {
    die("UNAUTHORIZED");
};
$Note = $_POST['Notes'];
$RT = $_POST['RT'];

//void RT
if (isset($_GET['del'])) {
    mysqli_query($mysqli, "DELETE FROM InspectedRTs WHERE RTNum='" . $_GET['del'] . "'");
    exec("rm assets/uploadedimages/" . $_GET['del'] . ".jpg assets/uploadedimages/" . $_GET['del'] . "starch.jpg assets/uploadedimages/" . $_GET['del'] . "bitterpit.jpg ../assets/uploadedimages/" . $_GET['del'] . "bruising.jpg");
    echo "<script>location.replace('QA.php?qa=" . $_GET['del'] . " has been <mark>voided</mark> and not #QA')</script>";
} else {

    //insert final inspection info
    mysqli_query($mysqli, "UPDATE `InspectedRTs` SET `Note`='" . $Note . "', `isFinalInspected`='1' WHERE RTNum='" . $RT . "'");

    //Prepare Statement
    $stmt = mysqli_prepare($mysqli, "UPDATE `AppleSamples` SET `Pressure1`=?, `Pressure2`=?, `Brix`=?, `Weight`=?,`FinalTestedBy`=? WHERE `RT#`=? AND SampleNum=?");
    mysqli_stmt_bind_param($stmt, 'ddddsii', $Pressure1, $Pressure2, $Brix, $Weight, $RealName[0], $RT, $Num);
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

//    //works but is disabled because annoying and largely useless
//    //test to see if fruit is large to send warning
//    $variety = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT rtrim(VarDesc) AS VarDesc, rtrim(`CommDesc`) FROM BULKOHCSV WHERE `RT#`='" . $RT . "'"));
//    $size = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT SizefromAverage FROM RTsWQuality WHERE `RT#`='" . $RT . "'"));
//    if ($variety['VarDesc'] == 'Jonagold' || $variety['VarDesc'] == 'Golden Delicious' || $variety['VarDesc'] == 'Red Delicious') {
//        if ($size['SizefromAverage'] <= 64) {
//            $alert = new EmergencyAlert();
//            $mail = $alert->prepareMail();
//            $alert->setSubject($mail, "Extremely large fruit detected on newly-received RT!");
//            $RTinfo = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT rtrim(GrowerName) AS 'GrowerName', rtrim(FarmDesc) AS FarmDesc, rtrim(Farm) AS Farm, rtrim(BlockDesc) AS 'BlockDesc', rtrim(Block) AS Block, rtrim(VarDesc) AS 'VarDesc', rtrim(StrDesc) AS 'StrDesc', rtrim(LocationDesc) AS 'LocationDesc', rtrim(RoomNum) AS RoomNum FROM BULKOHCSV WHERE `RT#`='" . $RT . "'"));
//            $RTstats = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT count(`RT#`) AS numRTs, sum(BuOnHand) AS sumReceived FROM BULKOHCSV WHERE rtrim(GrowerName)='" . $RTinfo['GrowerName'] . "' AND rtrim(Farm)='" . $RTinfo['Farm'] . "' AND rtrim(BLOCK)='" . $RTinfo['Block'] . "' AND rtrim(VarDesc)='" . $RTinfo['VarDesc'] . "' AND rtrim(StrDesc)='" . $RTinfo['StrDesc'] . "'"));
//            $alert->setBody($mail, "<html><p>Extremely large fruit was found on a newly received RT.</p><br><table border='1' cellpadding='3' cellspacing='0'><thead><th>Time</th><th>RT #</th><th>Grower</th><th>Farm</th><th>Block</th><th>Variety</th><th>Strain</th><th>Headed to</th></thead><tr><td>" . date('Y-m-d H:m:s') . "</td><td>" . $RT . "</td><td>" . $RTinfo['GrowerName'] . "</td><td>" . $RTinfo['Farm'] . ", " . $RTinfo['FarmDesc'] . "</td><td>" . $RTinfo['Block'] . ", " . $RTinfo['BlockDesc'] . "</td><td>" . $RTinfo['VarDesc'] . "</td><td>" . $RTinfo['StrDesc'] . "</td><td>" . $RTinfo['LocationDesc'] . ", " . $RTinfo['RoomNum'] . "</td></tr></table><br><p>Year to date, we've received " . $RTstats['numRTs'] . " RTs from this block, a total of " . $RTstats['sumReceived'] . " bushels. A photo of the bin is below.</p><br><img width='65%' src='cid:attach-bin'</html>");
//            $mail->AddEmbeddedImage("assets/uploadedimages/" . $RT . ".jpg", "attach-bin", $RT . ".jpg");
//            $alert->sendMail($mail);
//        }
//    } else if ($size['SizefromAverage'] <= 72 && $variety['CommDesc'] != 'Peach' && $variety['CommDesc'] != 'Nectarine') {
//        $alert = new EmergencyAlert();
//        $mail = $alert->prepareMail();
//        $alert->setSubject($mail, "Extremely large fruit detected on newly-received RT!");
//        $RTinfo = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT rtrim(GrowerName) AS 'GrowerName', rtrim(FarmDesc) AS FarmDesc, rtrim(Farm) AS Farm, rtrim(BlockDesc) AS 'BlockDesc', rtrim(Block) AS Block, rtrim(VarDesc) AS 'VarDesc', rtrim(StrDesc) AS 'StrDesc', rtrim(LocationDesc) AS 'LocationDesc', rtrim(RoomNum) AS RoomNum FROM BULKOHCSV WHERE `RT#`='" . $RT . "'"));
//        $RTstats = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT count(`RT#`) AS numRTs, sum(BuOnHand) AS sumReceived FROM BULKOHCSV WHERE rtrim(GrowerName)='" . $RTinfo['GrowerName'] . "' AND rtrim(Farm)='" . $RTinfo['Farm'] . "' AND rtrim(BLOCK)='" . $RTinfo['Block'] . "' AND rtrim(VarDesc)='" . $RTinfo['VarDesc'] . "' AND rtrim(StrDesc)='" . $RTinfo['StrDesc'] . "'"));
//        $alert->setBody($mail, "<html><p>Extremely large fruit was found on a newly received RT.</p><br><table border='1' cellpadding='3' cellspacing='0'><thead><th>Time</th><th>RT #</th><th>Grower</th><th>Farm</th><th>Block</th><th>Variety</th><th>Strain</th><th>Headed to</th></thead><tr><td>" . date('Y-m-d H:m:s') . "</td><td>" . $RT . "</td><td>" . $RTinfo['GrowerName'] . "</td><td>" . $RTinfo['Farm'] . ", " . $RTinfo['FarmDesc'] . "</td><td>" . $RTinfo['Block'] . ", " . $RTinfo['BlockDesc'] . "</td><td>" . $RTinfo['VarDesc'] . "</td><td>" . $RTinfo['StrDesc'] . "</td><td>" . $RTinfo['LocationDesc'] . ", " . $RTinfo['RoomNum'] . "</td></tr></table><br><p>Year to date, we've received " . $RTstats['numRTs'] . " RTs from this block, a total of " . $RTstats['sumReceived'] . " bushels. A photo of the bin is below.</p><br><img width='65%' src='cid:attach-bin'</html>");
//        $mail->AddEmbeddedImage("assets/uploadedimages/" . $RT . ".jpg", "attach-bin", $RT . ".jpg");
//        $alert->sendMail($mail);
//    }

    echo "<script>location.replace('QA.php?qa=$RT#QA')</script>";
}