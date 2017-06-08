<?php
include_once("Classes/Mobile_Detect.php");
$detect=new Mobile_Detect();
include '../config.php';

//authentication
if (!isset($_COOKIE['auth']) || !isset($_COOKIE['username'])) {
    die("<script>window.location.replace('/')</script>");
} else if (!hash_equals($_COOKIE['auth'], crypt($_COOKIE['username'], $securityKey))) {
    die("<script>window.location.replace('/')</script>");
} else {
    $SecuredUserName = mysqli_real_escape_string($mysqli, $_COOKIE['username']);
    $checkAllowed = mysqli_fetch_array(mysqli_query($mysqli, "SELECT `Real Name` AS 'UserRealName', Role, allowedQuality FROM packapps_master_users JOIN quality_UserData ON packapps_master_users.username=quality_UserData.UserName WHERE packapps_master_users.username = '$SecuredUserName'"));
    if (!$checkAllowed['allowedQuality'] > 0) {
        die ("<script>window.location.replace('/')</script>");
    } else {
        $RealName = $checkAllowed;
    }
}
// end authentication
?>
<!doctype html>

<html lang="en-US">
<head>
    <meta charset="utf-8">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-title" content="PackApps">
    <link rel="apple-touch-icon" href="apple-touch-icon.png">
    <link rel="icon" sizes="196x196" href="apple-touch-icon.png">
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
    <? if ($RealName['Role'] == 'QA' && $detect->isMobile()) {
        echo "<p style='position: fixed; top: 0; width: 100%'><button onclick=\"location.replace('/quality')\"><<< Go back</button></p>";
    } ?>
    <h1>Weight Samples</h1>
    <br>
    <? if (isset($_GET['ins'])) {
        echo "<h1><mark>&#x2713; Data for RT#" . $_GET['ins'] . " received.</mark></h1>";
    } ?>

    <h2>Rice Fruit Company Quality Assurance Lab</h2>
    <form action="weightsubmit.php" method="post" enctype="multipart/form-data">
        <div class="col-2">
            <label>
                RT Number
                <input id="RT" type=number placeholder="RT" name="RT" required>
            </label>
        </div>
        <div class="col-2">
            <label>
                Confirm RT Number
                <input id='RT2' type=number placeholder="Confirm RT" name="RT2" required>
            </label>
        </div>
        <div class="col-2" id="RTinfo">
        </div>
        <div class="col-4">
            <label>Weight of 20 fruit in kg
                <input type="number" step="any" min="2.2" max="10" name="weight" placeholder="0.00" autocomplete="off" required></label>
        </div>
        <div class="col-submit">
            <button class="submitbtn">Submit weight to QA Lab</button>
            <br>
            <label style="border: dashed black 1px; vertical-align: middle">Inspected
                by <? echo $RealName['UserRealName'] . " on " . date('l, F jS Y') ?></label>
        </div>
    </form>
</div>
</body>
<script>

    $("#RT2, #RT").on("change", function () {
        if ($("#RT").val() == $("#RT2").val()) {
            $.ajax({
                type: 'GET',
                url: "API/rtinfoins.php?q=" + $("#RT").val(),
                dataType: 'json',
                tryCount: 0,
                retryLimit: 3,
                success: function (data) {
                    $("#RTinfo").replaceWith("<div class='col-1' id='RTinfo'><label style='text-align: center'><img src=images/" + data.CommDesc + ".png> " + data.CommDesc + "<br>Grower: " + data.GrowerName + "<br>Farm: " + data.FarmDesc + "<br>Block: " + data.BlockDesc + "<br>Variety: " + data.VarDesc + "<br>Strain: " + data.StrDesc + "<br>Bins/Units: " + data.QtyOnHand + "<br>Headed to: " + data.Location + "<br><hr style='opacity: 0'></label>" + (data.Today == 0 ? "<label style='border: dashed red;text-align: center;vertical-align: middle;color: red'>Warning: This RT is <u>NOT</u> from today.</label>" : "") + "<hr style='width: 100%; color: grey'></div>");
                    $("#numSamples").val(data.NumSamplesRequired);
                },
                error: function () {
                    this.tryCount++;
                    if (this.tryCount <= this.retryLimit) {
                        $("#RTinfo").replaceWith("<div class='col-1' id='RTinfo'><label style='text-align: center; color: red'>No match found. Please double check the number or wait a minute for the list to update.</label></div>");
                        $.ajax(this);
                    }
                }
            });
        } else {
            if ($("#RT2").val() != "") {
                $("#RTinfo").replaceWith("<div class='col-1' id='RTinfo'><label style='text-align: center; color: red'>Those RTs Don't Match!</label></div>");
                navigator.vibrate(400);
            }
        }
    });
    <?php
    if(isset($_GET['autofill'])) {
        echo "$('#RT').val(".$_GET['autofill'].");$('#RT2').val(".$_GET['autofill'].").change();";
    }
    ?>
</script>
</html>