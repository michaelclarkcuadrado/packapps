<?php
require '../config.php';

require_once('emailAlerts/EmergencyAlert.php');

$userData = packapps_authenticate_user('quality');

//RT check
if ($_POST['RT'] != $_POST['RT2']) {
    die("<html style='text-align: center'><meta name='viewport' content='width=device-width, initial-scale=1'><h1>The Receipts did not match. Please check your Receipt against the ticket.</h1> <br> <a href='' onclick='window.history.back();'> Go Back</a></html>");
} else {
    $RT = mysqli_real_escape_string($mysqli, $_POST['RT']);
};

$Filename = "quality-rtnum-" . $RT . ".jpg";

//upload photo to archive
$check_exists = mysqli_query($mysqli, "SELECT * FROM quality_InspectedRTs WHERE receiptNum='$RT'");
if (mysqli_num_rows($check_exists) > 0) {
    die("<html style='text-align: center'><meta name='viewport' content='width=device-width, initial-scale=1'><h1 style='color: red'>NOT RECEIVED.</h1><h3>This RT already exists. Contact the QA lab for details.</h3><br> <a href='' onclick='window.history.back();'><-- Go Back</a></html>");
} else {
    packapps_uploadToS3($availableBuckets['quality'], $_FILES['binpicupload']['tmp_name'], $Filename);
}
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


//check for damage photos and send email alerts
//sends alerts for any bitterpit, severe bruising, severe sunburn

//bitterpit presence email
if ($bitterpit == '1') {
    if (!file_exists($_FILES['bitterPitDamageCloseUp']['tmp_name'])) {
        packapps_deleteFromS3($availableBuckets['quality'], $Filename);
        die ("<html style='text-align: center'><meta name='viewport' content='width=device-width, initial-scale=1'><h1 style='color: red'>NOT RECEIVED.</h1><h3>There was an error uploading the photo. We did not insert that RT into the QA lab system. Try again.</h3><br>Attempted RT: " . $RT . ", Error listed as: " . mysqli_connect_error() . mysqli_errno($mysqli) . "<br> <a href='' onclick='window.history.back();'><-- Go Back</a></html>");
    }
    $alert = new EmergencyAlert();
    $mail = $alert->prepareMail();
    $alert->setSubject($mail, "Bitter Pit detected on a new delivery!");
    $RTinfo = mysqli_fetch_assoc(mysqli_query($mysqli, "
      SELECT
              GrowerName                    AS 'GrowerName',
              farmName                      AS FarmDesc,
              BlockDesc                     AS 'BlockDesc',
              VarietyName                   AS 'VarDesc',
              strainName                    AS 'StrDesc',
              IFNULL(room_name, 'Unassigned') AS 'LocationDesc',
              grower_block
            FROM storage_grower_receipts
              JOIN storage_grower_fruit_bins ON storage_grower_receipts.id = storage_grower_fruit_bins.grower_receipt_id
              JOIN `grower_gfbvs-listing` ON PK = storage_grower_receipts.grower_block
              LEFT JOIN storage_rooms ON storage_grower_fruit_bins.curRoom = storage_rooms.room_id
            WHERE id = '" . $RT . "' LIMIT 1
    "));
    $RTstats = mysqli_fetch_assoc(mysqli_query($mysqli, "
      SELECT
  count(`id`)  AS numRTs,
  IFNULL(sum(bushelsInBin), 0) AS sumReceived
FROM storage_grower_receipts
  JOIN storage_grower_fruit_bins ON storage_grower_receipts.id = storage_grower_fruit_bins.grower_receipt_id
WHERE grower_block = '" . $RTinfo['grower_block'] . "' AND YEAR(`date`) = YEAR(CURDATE());
    "));
    $warnCount = mysqli_fetch_assoc(mysqli_query($mysqli, "
        SELECT (count(*) + 1) AS Count
        FROM quality_InspectedRTs
          JOIN storage_grower_receipts ON quality_InspectedRTs.receiptNum = storage_grower_receipts.id
        WHERE `BitterPit` = 1 AND grower_block IN (SELECT grower_block
                                                   FROM storage_grower_receipts
                                                   WHERE id = '1')
    "));
    $alert->setBody($mail, "
       <html><p>Bitter pit was found on a newly received RT.</p><br>
    <table border='1' cellpadding='3' cellspacing='0'>
        <thead>
        <th>Time</th>
        <th>Delivery #</th>
        <th>Grower</th>
        <th>Farm</th>
        <th>Block</th>
        <th>Variety</th>
        <th>Strain</th>
        <th>Headed to</th>
        </thead>
        <tr>
            <td>" . date('Y-m-d H:m:s') . "</td>
            <td>" . $RT . "</td>
            <td>" . $RTinfo['GrowerName'] . "</td>
            <td>" . $RTinfo['FarmDesc'] . "</td>
            <td>" . $RTinfo['BlockDesc'] . "</td>
            <td>" . $RTinfo['VarDesc'] . "</td>
            <td>" . $RTinfo['StrDesc'] . "</td>
            <td>" . $RTinfo['LocationDesc'] . "</td>
        </tr>
    </table>
    <br>
    <p>Year to date, we've received " . $RTstats['numRTs'] . " deliveries from this block, a total of " . $RTstats['sumReceived'] . " bushels. <br>This is bitterpit warning number " . $warnCount['Count'] . " for
    this block. Photos of the damage are below.</p><br><img width='65%' src='cid:attach-bitterpit'><br><img width='65%' src='cid:attach-bin'></html>
    ");
    $mail->AddEmbeddedImage($_FILES['bitterPitDamageCloseUp']['tmp_name'], "attach-bitterpit", $RT . "Bitterpit.jpg");
    $mail->AddEmbeddedImage($_FILES['binpicupload']['tmp_name'], "attach-bin", $RT . ".jpg");
    $alert->sendMail($mail);
}

// Email bruising email
if ($bruise == 'Severe') {
    if (!file_exists($_FILES['bruisingDamageCloseUp']['tmp_name'])) {
        packapps_deleteFromS3($availableBuckets['quality'], $Filename);
        die ("<html style='text-align: center'><meta name='viewport' content='width=device-width, initial-scale=1'><h1 style='color: red'>NOT RECEIVED.</h1><h3>There was an error uploading the photo. We did not insert that RT into the QA lab system. Try again.</h3><br>Attempted RT: " . $RT . ", Error listed as: " . mysqli_connect_error() . mysqli_errno($mysqli) . "<br> <a href='' onclick='window.history.back();'><-- Go Back</a></html>");
    }
    $alert = new EmergencyAlert();
    $mail = $alert->prepareMail();
    $alert->setSubject($mail, "Severe bruising detected on a new delivery!");
    $RTinfo = mysqli_fetch_assoc(mysqli_query($mysqli, "
    SELECT
          GrowerName                    AS 'GrowerName',
          farmName                      AS FarmDesc,
          BlockDesc                     AS 'BlockDesc',
          VarietyName                   AS 'VarDesc',
          strainName                    AS 'StrDesc',
          IFNULL(room_name, 'Unassigned') AS 'LocationDesc',
              grower_block
        FROM storage_grower_receipts
          JOIN storage_grower_fruit_bins ON storage_grower_receipts.id = storage_grower_fruit_bins.grower_receipt_id
          JOIN `grower_gfbvs-listing` ON PK = storage_grower_receipts.grower_block
          LEFT JOIN storage_rooms ON storage_grower_fruit_bins.curRoom = storage_rooms.room_id
        WHERE id = '" . $RT . "' LIMIT 1
    "));
    $RTstats = mysqli_fetch_assoc(mysqli_query($mysqli, "
      SELECT
  count(`id`)  AS numRTs,
  IFNULL(sum(bushelsInBin), 0) AS sumReceived
FROM storage_grower_receipts
  JOIN storage_grower_fruit_bins ON storage_grower_receipts.id = storage_grower_fruit_bins.grower_receipt_id
WHERE grower_block = '" . $RTinfo['grower_block'] . "' AND YEAR(`date`) = YEAR(CURDATE());
    "));
    $warnCount = mysqli_fetch_assoc(mysqli_query($mysqli, "
            SELECT (count(*) + 1) AS Count
        FROM quality_InspectedRTs
          JOIN storage_grower_receipts ON quality_InspectedRTs.receiptNum = storage_grower_receipts.id
        WHERE `Bruise` = 'Severe' AND grower_block IN (SELECT grower_block
                                                   FROM storage_grower_receipts
                                                   WHERE id = '1')
    "));
    $alert->setBody($mail, "
     <html><p>Severe Bruising was found on a new delivery.</p><br>
        <table border='1' cellpadding='3' cellspacing='0'>
        <thead>
        <th>Time</th>
        <th>Delivery #</th>
        <th>Grower</th>
        <th>Farm</th>
        <th>Block</th>
        <th>Variety</th>
        <th>Strain</th>
        <th>Headed to</th>
        </thead>
        <tr>
            <td>" . date('Y-m-d H:m:s') . "</td>
            <td>" . $RT . "</td>
            <td>" . $RTinfo['GrowerName'] . "</td>
            <td>" . $RTinfo['FarmDesc'] . "</td>
            <td>" . $RTinfo['BlockDesc'] . "</td>
            <td>" . $RTinfo['VarDesc'] . "</td>
            <td>" . $RTinfo['StrDesc'] . "</td>
            <td>" . $RTinfo['LocationDesc'] . "</td>
        </tr>
        </table>
        <br>
        <p>Year to date, we've received " . $RTstats['numRTs'] . " deliveries from this block, a total of " . $RTstats['sumReceived'] . " bushels. <br>This is bruising warning number " . $warnCount['Count'] . " for
    this block. Photos of the damage are below.</p><br><img width='65%' src='cid:attach-bruising'><br><img width='65%' src='cid:attach-bin'></html>
    ");
    $mail->AddEmbeddedImage($_FILES['bruisingDamageCloseUp']['tmp_name'], "attach-bruising", $RT . "bruising.jpg");
    $mail->AddEmbeddedImage($_FILES['binpicupload']['tmp_name'], "attach-bin", $RT . ".jpg");
    $alert->sendMail($mail);
}

//sunburn severity email
if ($sunburn == 'Severe') {
    $alert = new EmergencyAlert();
    $mail = $alert->prepareMail();
    $alert->setSubject($mail, "Severe sunburn detected on a new delivery!");
    $RTinfo = mysqli_fetch_assoc(mysqli_query($mysqli, "
    SELECT
          GrowerName                    AS 'GrowerName',
          farmName                      AS FarmDesc,
          BlockDesc                     AS 'BlockDesc',
          VarietyName                   AS 'VarDesc',
          strainName                    AS 'StrDesc',
          IFNULL(room_name, 'Unassigned') AS 'LocationDesc',
              grower_block
        FROM storage_grower_receipts
          JOIN storage_grower_fruit_bins ON storage_grower_receipts.id = storage_grower_fruit_bins.grower_receipt_id
          JOIN `grower_gfbvs-listing` ON PK = storage_grower_receipts.grower_block
          LEFT JOIN storage_rooms ON storage_grower_fruit_bins.curRoom = storage_rooms.room_id
        WHERE id = '" . $RT . "' LIMIT 1
    "));
    $RTstats = mysqli_fetch_assoc(mysqli_query($mysqli, "
      SELECT
  count(`id`)  AS numRTs,
  IFNULL(sum(bushelsInBin), 0) AS sumReceived
FROM storage_grower_receipts
  JOIN storage_grower_fruit_bins ON storage_grower_receipts.id = storage_grower_fruit_bins.grower_receipt_id
WHERE grower_block = '" . $RTinfo['grower_block'] . "' AND YEAR(`date`) = YEAR(CURDATE());
    "));
    $warnCount = mysqli_fetch_assoc(mysqli_query($mysqli, "
        SELECT (count(*) + 1) AS Count
        FROM quality_InspectedRTs
          JOIN storage_grower_receipts ON quality_InspectedRTs.receiptNum = storage_grower_receipts.id
        WHERE `SunBurn` = 'Severe' AND grower_block IN (SELECT grower_block
                                                   FROM storage_grower_receipts
                                                   WHERE id = '1')
    "));
    $alert->setBody($mail, "
    <html><p>Severe sunburn was found on a newly received delivery.</p><br>
    <table border='1' cellpadding='3' cellspacing='0'>
        <thead>
        <th>Time</th>
        <th>Delivery #</th>
        <th>Grower</th>
        <th>Farm</th>
        <th>Block</th>
        <th>Variety</th>
        <th>Strain</th>
        <th>Headed to</th>
        </thead>
        <tr>
            <td>" . date('Y-m-d H:m:s') . "</td>
            <td>" . $RT . "</td>
            <td>" . $RTinfo['GrowerName'] . "</td>
            <td>" . $RTinfo['FarmDesc'] . "</td>
            <td>" . $RTinfo['BlockDesc'] . "</td>
            <td>" . $RTinfo['VarDesc'] . "</td>
            <td>" . $RTinfo['StrDesc'] . "</td>
            <td>" . $RTinfo['LocationDesc'] . "</td>
        </tr>
    </table>
    <br>
    <p>Year to date, we've received " . $RTstats['numRTs'] . " deliveries from this block, a total of " . $RTstats['sumReceived'] . " bushels. <br>This is sunburn warning number " . $warnCount['Count'] . " for
    this block. A photo of the damage is below.</p><br><img width='65%' src='cid:attach-bin'></html>
    ");
    $mail->AddEmbeddedImage($_FILES['binpicupload']['tmp_name'], "attach-bin", $RT . ".jpg");
    $alert->sendMail($mail);
}

//scab severity email
if ($scab == 'Severe' || $scab == 'Heavy') {
    $alert = new EmergencyAlert();
    $mail = $alert->prepareMail();
    $alert->setSubject($mail, "Severe scab detected on a new delivery!");
    $RTinfo = mysqli_fetch_assoc(mysqli_query($mysqli, "
    SELECT
          GrowerName                    AS 'GrowerName',
          farmName                      AS FarmDesc,
          BlockDesc                     AS 'BlockDesc',
          VarietyName                   AS 'VarDesc',
          strainName                    AS 'StrDesc',
          IFNULL(room_name, 'Unassigned') AS 'LocationDesc',
              grower_block
        FROM storage_grower_receipts
          JOIN storage_grower_fruit_bins ON storage_grower_receipts.id = storage_grower_fruit_bins.grower_receipt_id
          JOIN `grower_gfbvs-listing` ON PK = storage_grower_receipts.grower_block
          LEFT JOIN storage_rooms ON storage_grower_fruit_bins.curRoom = storage_rooms.room_id
        WHERE id = '" . $RT . "' LIMIT 1
    "));
    $RTstats = mysqli_fetch_assoc(mysqli_query($mysqli, "
            SELECT
  count(`id`)  AS numRTs,
  IFNULL(sum(bushelsInBin), 0) AS sumReceived
FROM storage_grower_receipts
  JOIN storage_grower_fruit_bins ON storage_grower_receipts.id = storage_grower_fruit_bins.grower_receipt_id
WHERE grower_block = '" . $RTinfo['grower_block'] . "' AND YEAR(`date`) = YEAR(CURDATE());
    "));
    $warnCount = mysqli_fetch_assoc(mysqli_query($mysqli, "
                SELECT (count(*) + 1) AS Count
        FROM quality_InspectedRTs
          JOIN storage_grower_receipts ON quality_InspectedRTs.receiptNum = storage_grower_receipts.id
        WHERE (`Scab` = 'Severe' OR `Scab` = 'Heavy') AND grower_block IN (SELECT grower_block
                                                   FROM storage_grower_receipts
                                                   WHERE id = '1')
    "));
    $alert->setBody($mail, "
       <html><p>Severe scab was found on a newly received delivery.</p><br>
        <table border='1' cellpadding='3' cellspacing='0'>
            <thead>
            <th>Time</th>
            <th>Delivery #</th>
            <th>Grower</th>
            <th>Farm</th>
            <th>Block</th>
            <th>Variety</th>
            <th>Strain</th>
            <th>Headed to</th>
            </thead>
            <tr>
                <td>" . date('Y-m-d H:m:s') . "</td>
                <td>" . $RT . "</td>
                <td>" . $RTinfo['GrowerName'] . "</td>
                <td>" . $RTinfo['FarmDesc'] . "</td>
                <td>" . $RTinfo['BlockDesc'] . "</td>
                <td>" . $RTinfo['VarDesc'] . "</td>
                <td>" . $RTinfo['StrDesc'] . "</td>
                <td>" . $RTinfo['LocationDesc'] . "</td>
            </tr>
        </table>
        <br>
<p>Year to date, we've received " . $RTstats['numRTs'] . " deliveries from this block, a total of " . $RTstats['sumReceived'] . " bushels. <br>This is scab warning number " . $warnCount['Count'] . " for this
    block. A photo of the damage is below.</p><br><img width='65%' src='cid:attach-bin'></html>
    ");
    $mail->AddEmbeddedImage($_FILES['binpicupload']['tmp_name'], "attach-bin", $RT . ".jpg");
    $alert->sendMail($mail);
}

//insert the data and return
$stmt = mysqli_prepare($mysqli, "INSERT INTO `quality_InspectedRTs` (`receiptNum`, `#Samples`, `Color Quality`, `Blush`, `Bruise`, `BitterPit`, `Russet`, `Scab`, `StinkBug`, `SanJoseScale`, `SunBurn`, `Note`, `InspectedBy`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
mysqli_stmt_bind_param($stmt, 'iisisisssssss', $RT, $numSamples, $Color, $blush, $bruise, $bitterpit, $russet, $scab, $stinkbug, $scale, $sunburn, $Notes, $userData['username']);
if (!mysqli_stmt_execute($stmt)) {
    packapps_deleteFromS3($availableBuckets['quality'], $Filename);
    die ("<html style='text-align: center'><meta name='viewport' content='width=device-width, initial-scale=1'><h1 style='color: red'>NOT RECEIVED.</h1><h3>There was a database error. We did not insert that RT into the QA lab system. Try again.</h3><br>Attempted Receipt: " . $RT . ", Error listed as: " . mysqli_connect_error() . mysqli_error($mysqli) . "<br> <a href='' onclick='window.history.back();'><-- Go Back</a></html>");
}

//Add quality_appleSamples rows for later
$SampleNum = 0;
$stmt = mysqli_prepare($mysqli, "INSERT INTO `quality_AppleSamples` (`receiptNum`, `SampleNum`) VALUES (?, ?)");
mysqli_stmt_bind_param($stmt, 'ii', $RT, $SampleNum);
for ($SampleNum = 1; $SampleNum < ($numSamples + 1); $SampleNum++) {
    mysqli_stmt_execute($stmt);
}

echo "<script>location.replace('Inspector.php?ins=$RT')</script>";
