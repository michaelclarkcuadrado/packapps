<?php
include_once("../scripts-common/Mobile_Detect.php");
$detect = new Mobile_Detect();
require '../config.php';

$userData = packapps_authenticate_user('quality');

$runsAvailable = mysqli_query($mysqli, "SELECT Line, RunNumber, `production_runs`.RunID, Variety, Quality, Size FROM production_runs LEFT JOIN production_dumped_fruit ON `production_runs`.RunID=`production_dumped_fruit`.RunID LEFT JOIN quality_run_inspections ON `quality_run_inspections`.`RunID`=`production_runs`.RunID WHERE isQA > 0 AND isPhotoGraphed = 0 AND lastEdited >= NOW() - INTERVAL 5 DAY GROUP BY RunID ");

?>
<!doctype html>

<html lang="en-US">
<head>
    <meta charset="utf-8">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-title" content="PackApps">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="viewport" content="width=device-width, initial-scale=1 user-scalable=no">
    <meta http-equiv="Content-Type" content="text/html">
    <meta name="theme-color" content="#e2eef4">
    <title>Mobile QA</title>
    <link rel="stylesheet" type="text/css" media="all" href="assets/css/inspector.css">
    <script src="assets/js/jquery.min.js"></script>
</head>

<body>
<div id="wrapper">
    <? if ($UserData['Role'] == 'QA' && $detect->isMobile()) {
        echo "<p style='position: fixed; top: 0; width: 100%'><button onclick=\"location.replace('/quality')\"><<< Go back</button></p>";
    } ?>
    <h1>Run Sample Photos</h1>
    <br>
    <? if (isset($_GET['success'])) {
        echo "<h1><mark>&#x2713; Photo archived.</mark></h1>";
    } ?>
    <h2><?echo $companyName?> Quality Assurance Lab</h2>
    <form action="runPhotoSubmit.php" method="post" enctype="multipart/form-data">
        <div class="col-4">
            <label>
                Run #
                <select onchange="$('.submitbtn').prop('disabled', false)" name="RunID" required>
                    <option disabled selected>Select a run</option>
                    <?
                    while ($dataArray = mysqli_fetch_array($runsAvailable)) {
                        echo "<option value='" . $dataArray['RunID'] . "'>" . $dataArray['Line'] . " Line #" . $dataArray['RunNumber'] . " " . $dataArray['Variety'] . "</option>";
                    }
                    ?>
                </select>
            </label>
        </div>
        <div class="col-4">
            <label>Fruit Sample photo
                <input type="file" name="photo" accept="image/jpeg" required></label>
        </div>
        <div class="col-submit">
            <button class="submitbtn" disabled>Submit photo to QA Lab</button>
            <br>
            <label style="border: dashed black 1px; vertical-align: middle">Inspected
                by <? echo $userData['Real Name'] . " on " . date('l, F jS Y') ?></label>
        </div>
    </form>
</div>
</body>
</html>