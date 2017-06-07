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
    $checkAllowed = mysqli_fetch_array(mysqli_query($mysqli, "SELECT `Real Name`, allowedQuality FROM packapps_master_users JOIN quality_UserData ON packapps_master_users.username=quality_UserData.UserName WHERE packapps_master_users.username = '$SecuredUserName'"));
    if (!$checkAllowed['allowedQuality'] > 0) {
        die ("<script>window.location.replace('/')</script>");
    } else {
        $RealName = $checkAllowed;
        $Role = $checkAllowed['Role'];
    }
}
// end authentication
$Filename = "assets/uploadedimages/" . $_POST['RT'] . ".jpg";

//RT check
if ($_POST['RT'] != $_POST['RT2']) {
    die("<html style='text-align: center'><meta name='viewport' content='width=device-width, initial-scale=1'><h1>The RTs did not match. Please check your RTs against the ticket.</h1> <br> <a href='' onclick='window.history.back();'> Go Back</a></html>");
} else {
    $RT = $_POST['RT'];
};

//move uploaded bin photo
if (file_exists($Filename)) {
    die("<html style='text-align: center'><meta name='viewport' content='width=device-width, initial-scale=1'><h1 style='color: red'>NOT RECEIVED.</h1><h3>This RT already exists. Contact the QA lab for details.</h3><br> <a href='' onclick='window.history.back();'><-- Go Back</a></html>");
};
if ($_FILES["binpicupload"]["size"] > 10000000) {
    die("<html style='text-align: center'><meta name='viewport' content='width=device-width, initial-scale=1'><h1>The photo is too large to be uploaded. If it keeps happening, turn down the quality of the camera.</h1><br> <a href='' onclick='window.history.back();'><-- Go Back</a></html>");
};
move_uploaded_file($_FILES['binpicupload']['tmp_name'], $Filename);
//POSTed data
$Color = $_POST['color'];
if ($_POST['isBruisedSevere'] == 'Yes') {
    $bruise = 'Severe';
} else {
    $bruise = $_POST['isBruised'];
}
$numSamples = $_POST['NumSamples'];
$bitterpit = $_POST['isBitterPitPresent'];
//blush, sunburn, san jose scale
$blush = ($_POST['blushcolor'] ? $_POST['blushcolor'] : '0');
$scale = $_POST['SJScalepercent'];
$sunburn = $_POST['sunBurnpercent'];
$russet = ($_POST['russetpercent'] ? $_POST['russetpercent'] : 'None');
$scab = $_POST['scabpercent'];
$stinkbug = $_POST['stbugpercent'];
$Notes = $_POST['notes'];


//check for damage photos and send alerts
//sends alerts for any bitterpit, severe bruising, severe sunburn
if ($bitterpit == '1') {
    if (!file_exists($_FILES['bitterPitDamageCloseUp']['tmp_name'])) {
        unlink($Filename);
        die ("<html style='text-align: center'><meta name='viewport' content='width=device-width, initial-scale=1'><h1 style='color: red'>NOT RECEIVED.</h1><h3>There was an error uploading the photo. We did not insert that RT into the QA lab system. Try again.</h3><br>Attempted RT: " . $RT . ", Error listed as: " . mysqli_connect_error() . mysqli_errno($mysqli) . "<br> <a href='' onclick='window.history.back();'><-- Go Back</a></html>");
    }

    move_uploaded_file($_FILES['bitterPitDamageCloseUp']['tmp_name'], "assets/uploadedimages/" . $RT . "bitterpit.jpg");
    $alert = new EmergencyAlert();
    $mail = $alert->prepareMail();
    $alert->setSubject($mail, "Bitter Pit detected on newly-received RT!");
    $RTinfo = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT rtrim(GrowerName) AS 'GrowerName', rtrim(FarmDesc) AS FarmDesc, rtrim(Farm) AS Farm, rtrim(BlockDesc) AS 'BlockDesc', rtrim(Block) AS Block, rtrim(VarDesc) AS 'VarDesc', rtrim(StrDesc) AS 'StrDesc', rtrim(LocationDesc) AS 'LocationDesc', rtrim(RoomNum) AS RoomNum FROM BULKOHCSV WHERE `RT#`='" . $RT . "'"));
    $cropYear = substr(date('Y'), -1);
    $RTstats = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT count(`RT#`) AS numRTs, sum(BuOnHand) AS sumReceived FROM BULKOHCSV WHERE rtrim(CropYear) = '$cropYear' AND rtrim(GrowerName)='" . $RTinfo['GrowerName'] . "' AND rtrim(Farm)='" . $RTinfo['Farm'] . "' AND rtrim(BLOCK)='" . $RTinfo['Block'] . "' AND rtrim(VarDesc)='" . $RTinfo['VarDesc'] . "' AND rtrim(StrDesc)='" . $RTinfo['StrDesc'] . "'"));
    $warnCount = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT (count(*)+1) AS Count FROM quality_InspectedRTs JOIN BULKOHCSV ON `RTNum`=`RT#` WHERE `BitterPit`=1 AND rtrim(GrowerName)='" . $RTinfo['GrowerName'] . "' AND rtrim(Farm)='" . $RTinfo['Farm'] . "' AND rtrim(BLOCK)='" . $RTinfo['Block'] . "' AND rtrim(VarDesc)='" . $RTinfo['VarDesc'] . "' AND rtrim(StrDesc)='" . $RTinfo['StrDesc'] . "'"));
    $alert->setBody($mail, "<html><p>Bitter pit was found on a newly received RT.</p><br><table border='1' cellpadding='3' cellspacing='0'><thead><th>Time</th><th>RT #</th><th>Grower</th><th>Farm</th><th>Block</th><th>Variety</th><th>Strain</th><th>Headed to</th></thead><tr><td>" . date('Y-m-d H:m:s') . "</td><td>" . $RT . "</td><td>" . $RTinfo['GrowerName'] . "</td><td>" . $RTinfo['Farm'] . ", " . $RTinfo['FarmDesc'] . "</td><td>" . $RTinfo['Block'] . ", " . $RTinfo['BlockDesc'] . "</td><td>" . $RTinfo['VarDesc'] . "</td><td>" . $RTinfo['StrDesc'] . "</td><td>" . $RTinfo['LocationDesc'] . ", " . $RTinfo['RoomNum'] . "</td></tr></table><br><p>Year to date, we've received " . $RTstats['numRTs'] . " RTs from this block, a total of " . $RTstats['sumReceived'] . " bushels. <br>This is bitterpit warning number " . $warnCount['Count'] . " for this block. Photos of the damage are below.</p><br><img width='65%' src='cid:attach-bitterpit'><br><img width='65%' src='cid:attach-bin'</html>");
    $mail->AddEmbeddedImage("assets/uploadedimages/" . $RT . "bitterpit.jpg", "attach-bitterpit", $RT . "Bitterpit.jpg");
    $mail->AddEmbeddedImage("assets/uploadedimages/" . $RT . ".jpg", "attach-bin", $RT . ".jpg");
    $alert->sendMail($mail);
}

if ($bruise == 'Severe') {
    if (!file_exists($_FILES['bruisingDamageCloseUp']['tmp_name'])) {
        unlink($Filename);
        unlink("assets/uploadedimages/" . $RT . "bitterpit.jpg");
        die ("<html style='text-align: center'><meta name='viewport' content='width=device-width, initial-scale=1'><h1 style='color: red'>NOT RECEIVED.</h1><h3>There was an uploading the photo. We did not insert that RT into the QA lab system. Try again.</h3><br>Attempted RT: " . $RT . ", Error listed as: " . mysqli_connect_error() . mysqli_errno($mysqli) . "<br> <a href='' onclick='window.history.back();'><-- Go Back</a></html>");
    }

    move_uploaded_file($_FILES['bruisingDamageCloseUp']['tmp_name'], "assets/uploadedimages/" . $RT . "bruising.jpg");
    $alert = new EmergencyAlert();
    $mail = $alert->prepareMail();
    $alert->setSubject($mail, "Severe bruising detected on newly-received RT!");
    $RTinfo = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT rtrim(GrowerName) AS 'GrowerName', rtrim(FarmDesc) AS FarmDesc, rtrim(Farm) AS Farm, rtrim(BlockDesc) AS 'BlockDesc', rtrim(Block) AS Block, rtrim(VarDesc) AS 'VarDesc', rtrim(StrDesc) AS 'StrDesc', rtrim(LocationDesc) AS 'LocationDesc', rtrim(RoomNum) AS RoomNum FROM BULKOHCSV WHERE `RT#`='" . $RT . "'"));
    $RTstats = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT count(`RT#`) AS numRTs, sum(BuOnHand) AS sumReceived FROM BULKOHCSV WHERE rtrim(GrowerName)='" . $RTinfo['GrowerName'] . "' AND rtrim(Farm)='" . $RTinfo['Farm'] . "' AND rtrim(BLOCK)='" . $RTinfo['Block'] . "' AND rtrim(VarDesc)='" . $RTinfo['VarDesc'] . "' AND rtrim(StrDesc)='" . $RTinfo['StrDesc'] . "'"));
    $warnCount = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT (count(*)+1) AS Count FROM quality_InspectedRTs JOIN BULKOHCSV ON `RTNum`=`RT#` WHERE `Bruise`='Severe' AND rtrim(GrowerName)='" . $RTinfo['GrowerName'] . "' AND rtrim(Farm)='" . $RTinfo['Farm'] . "' AND rtrim(BLOCK)='" . $RTinfo['Block'] . "' AND rtrim(VarDesc)='" . $RTinfo['VarDesc'] . "' AND rtrim(StrDesc)='" . $RTinfo['StrDesc'] . "'"));
    $alert->setBody($mail, "<html><p>Severe Bruising was found on a newly received RT.</p><br><table border='1' cellpadding='3' cellspacing='0'><thead><th>Time</th><th>RT #</th><th>Grower</th><th>Farm</th><th>Block</th><th>Variety</th><th>Strain</th><th>Headed to</th></thead><tr><td>" . date('Y-m-d H:m:s') . "</td><td>" . $RT . "</td><td>" . $RTinfo['GrowerName'] . "</td><td>" . $RTinfo['Farm'] . ", " . $RTinfo['FarmDesc'] . "</td><td>" . $RTinfo['Block'] . ", " . $RTinfo['BlockDesc'] . "</td><td>" . $RTinfo['VarDesc'] . "</td><td>" . $RTinfo['StrDesc'] . "</td><td>" . $RTinfo['LocationDesc'] . ", " . $RTinfo['RoomNum'] . "</td></tr></table><br><p>Year to date, we've received " . $RTstats['numRTs'] . " RTs from this block, a total of " . $RTstats['sumReceived'] . " bushels. <br>This is bruising warning number " . $warnCount['Count'] . " for this block. Photos of the damage are below.</p><br><img width='65%' src='cid:attach-bruising'><br><img width='65%' src='cid:attach-bin'</html>");
    $mail->AddEmbeddedImage("assets/uploadedimages/" . $RT . "bruising.jpg", "attach-bruising", $RT . "bruising.jpg");
    $mail->AddEmbeddedImage("assets/uploadedimages/" . $RT . ".jpg", "attach-bin", $RT . ".jpg");
    $alert->sendMail($mail);
}

if ($sunburn == 'Severe') {
    $alert = new EmergencyAlert();
    $mail = $alert->prepareMail();
    $alert->setSubject($mail, "Severe sunburn detected on newly-received RT!");
    $RTinfo = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT rtrim(GrowerName) AS 'GrowerName', rtrim(FarmDesc) AS FarmDesc, rtrim(Farm) AS Farm, rtrim(BlockDesc) AS 'BlockDesc', rtrim(Block) AS Block, rtrim(VarDesc) AS 'VarDesc', rtrim(StrDesc) AS 'StrDesc', rtrim(LocationDesc) AS 'LocationDesc', rtrim(RoomNum) AS RoomNum FROM BULKOHCSV WHERE `RT#`='" . $RT . "'"));
    $RTstats = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT count(`RT#`) AS numRTs, sum(BuOnHand) AS sumReceived FROM BULKOHCSV WHERE rtrim(GrowerName)='" . $RTinfo['GrowerName'] . "' AND rtrim(Farm)='" . $RTinfo['Farm'] . "' AND rtrim(BLOCK)='" . $RTinfo['Block'] . "' AND rtrim(VarDesc)='" . $RTinfo['VarDesc'] . "' AND rtrim(StrDesc)='" . $RTinfo['StrDesc'] . "'"));
    $warnCount = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT (count(*)+1) AS Count FROM quality_InspectedRTs JOIN BULKOHCSV ON `RTNum`=`RT#` WHERE `SunBurn`='Severe' AND rtrim(GrowerName)='" . $RTinfo['GrowerName'] . "' AND rtrim(Farm)='" . $RTinfo['Farm'] . "' AND rtrim(BLOCK)='" . $RTinfo['Block'] . "' AND rtrim(VarDesc)='" . $RTinfo['VarDesc'] . "' AND rtrim(StrDesc)='" . $RTinfo['StrDesc'] . "'"));
    $alert->setBody($mail, "<html><p>Severe sunburn was found on a newly received RT.</p><br><table border='1' cellpadding='3' cellspacing='0'><thead><th>Time</th><th>RT #</th><th>Grower</th><th>Farm</th><th>Block</th><th>Variety</th><th>Strain</th><th>Headed to</th></thead><tr><td>" . date('Y-m-d H:m:s') . "</td><td>" . $RT . "</td><td>" . $RTinfo['GrowerName'] . "</td><td>" . $RTinfo['Farm'] . ", " . $RTinfo['FarmDesc'] . "</td><td>" . $RTinfo['Block'] . ", " . $RTinfo['BlockDesc'] . "</td><td>" . $RTinfo['VarDesc'] . "</td><td>" . $RTinfo['StrDesc'] . "</td><td>" . $RTinfo['LocationDesc'] . ", " . $RTinfo['RoomNum'] . "</td></tr></table><br><p>Year to date, we've received " . $RTstats['numRTs'] . " RTs from this block, a total of " . $RTstats['sumReceived'] . " bushels. <br>This is sunburn warning number " . $warnCount['Count'] . " for this block. A photo of the damage is below.</p><br><img width='65%' src='cid:attach-bin'</html>");
    $mail->AddEmbeddedImage("assets/uploadedimages/" . $RT . ".jpg", "attach-bin", $RT . ".jpg");
    $alert->sendMail($mail);
}

if ($scab == 'Severe' || $scab == 'Heavy') {
    $alert = new EmergencyAlert();
    $mail = $alert->prepareMail();
    $alert->setSubject($mail, "Severe scab detected on newly-received RT!");
    $RTinfo = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT rtrim(GrowerName) AS 'GrowerName', rtrim(FarmDesc) AS FarmDesc, rtrim(Farm) AS Farm, rtrim(BlockDesc) AS 'BlockDesc', rtrim(Block) AS Block, rtrim(VarDesc) AS 'VarDesc', rtrim(StrDesc) AS 'StrDesc', rtrim(LocationDesc) AS 'LocationDesc', rtrim(RoomNum) AS RoomNum FROM BULKOHCSV WHERE `RT#`='" . $RT . "'"));
    $RTstats = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT count(`RT#`) AS numRTs, sum(BuOnHand) AS sumReceived FROM BULKOHCSV WHERE rtrim(GrowerName)='" . $RTinfo['GrowerName'] . "' AND rtrim(Farm)='" . $RTinfo['Farm'] . "' AND rtrim(BLOCK)='" . $RTinfo['Block'] . "' AND rtrim(VarDesc)='" . $RTinfo['VarDesc'] . "' AND rtrim(StrDesc)='" . $RTinfo['StrDesc'] . "'"));
    $warnCount = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT (count(*)+1) AS Count FROM quality_InspectedRTs JOIN BULKOHCSV ON `RTNum`=`RT#` WHERE `Scsb`='Severe' AND rtrim(GrowerName)='" . $RTinfo['GrowerName'] . "' AND rtrim(Farm)='" . $RTinfo['Farm'] . "' AND rtrim(BLOCK)='" . $RTinfo['Block'] . "' AND rtrim(VarDesc)='" . $RTinfo['VarDesc'] . "' AND rtrim(StrDesc)='" . $RTinfo['StrDesc'] . "'"));
    $alert->setBody($mail, "<html><p>Severe scab was found on a newly received RT.</p><br><table border='1' cellpadding='3' cellspacing='0'><thead><th>Time</th><th>RT #</th><th>Grower</th><th>Farm</th><th>Block</th><th>Variety</th><th>Strain</th><th>Headed to</th></thead><tr><td>" . date('Y-m-d H:m:s') . "</td><td>" . $RT . "</td><td>" . $RTinfo['GrowerName'] . "</td><td>" . $RTinfo['Farm'] . ", " . $RTinfo['FarmDesc'] . "</td><td>" . $RTinfo['Block'] . ", " . $RTinfo['BlockDesc'] . "</td><td>" . $RTinfo['VarDesc'] . "</td><td>" . $RTinfo['StrDesc'] . "</td><td>" . $RTinfo['LocationDesc'] . ", " . $RTinfo['RoomNum'] . "</td></tr></table><br><p>Year to date, we've received " . $RTstats['numRTs'] . " RTs from this block, a total of " . $RTstats['sumReceived'] . " bushels. <br>This is scab warning number " . $warnCount['Count'] . " for this block. A photo of the damage is below.</p><br><img width='65%' src='cid:attach-bin'</html>");
    $mail->AddEmbeddedImage("assets/uploadedimages/" . $RT . ".jpg", "attach-bin", $RT . ".jpg");
    $alert->sendMail($mail);
}

//insert the data and return
$stmt = mysqli_prepare($mysqli, "INSERT INTO `quality_InspectedRTs` (`RTNum`, `#Samples`, `Color Quality`, `Blush`, `Bruise`, `BitterPit`, `Russet`, `Scab`, `StinkBug`, `SanJoseScale`, `SunBurn`, `Note`, `InspectedBy`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
mysqli_stmt_bind_param($stmt, 'iisisisssssss', $RT, $numSamples, $Color, $blush, $bruise, $bitterpit, $russet, $scab, $stinkbug, $scale, $sunburn, $Notes, $RealName['Real Name']);
if (!mysqli_stmt_execute($stmt)) {
    unlink("assets/uploadedimages/" . $RT . ".jpg");
    unlink("assets/uploadedimages/" . $RT . "bitterpit.jpg");
    unlink("assets/uploadedimages/" . $RT . "bruising.jpg");
    die ("<html style='text-align: center'><meta name='viewport' content='width=device-width, initial-scale=1'><h1 style='color: red'>NOT RECEIVED.</h1><h3>There was a database error. We did not insert that RT into the QA lab system. Try again.</h3><br>Attempted RT: " . $RT . ", Error listed as: " . mysqli_connect_error() . mysqli_error($mysqli) . "<br> <a href='' onclick='window.history.back();'><-- Go Back</a></html>");
}

//Add quality_appleSamples rows for later
$SampleNum = 0;
$stmt=mysqli_prepare($mysqli, "Insert into `quality_AppleSamples` (`RT#`, `SampleNum`) values (?, ?)");
mysqli_stmt_bind_param($stmt, 'ii', $RT, $SampleNum);
for($SampleNum = 1; $SampleNum < ($numSamples+1); $SampleNum++)
{
    mysqli_stmt_execute($stmt);
}

echo "<script>location.replace('Inspector.php?ins=$RT')</script>";
