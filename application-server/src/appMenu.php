<?php
/**
 * Created by PhpStorm.
 * User: mac
 * Date: 7/27/2016
 * Time: 8:55 AM
 */
include 'config.php';

//authentication
if (!isset($_COOKIE['auth']) || !isset($_COOKIE['username'])) {
    die("<script>window.location.replace('/')</script>");
} else if (!hash_equals($_COOKIE['auth'], crypt($_COOKIE['username'], $securityKey))) {
    die("<script>window.location.replace('/')</script>");
} else {
    $SecuredUserName = mysqli_real_escape_string($mysqli, $_COOKIE['username']);
}

$allowedItems = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT allowedQuality, allowedPurchasing, allowedProduction, isSystemAdministrator FROM master_users WHERE username = '$SecuredUserName'"));

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content='Purchasing dashboard'>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0">
    <title>Menu - PackApps</title>

    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="apple-mobile-web-app-title" content="PackApps">
    <link rel="apple-touch-icon" sizes="57x57" href="favicons/apple-touch-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="favicons/apple-touch-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="favicons/apple-touch-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="favicons/apple-touch-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="favicons/apple-touch-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="favicons/apple-touch-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="favicons/apple-touch-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="favicons/apple-touch-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="favicons/apple-touch-icon-180x180.png">
    <link rel="icon" type="image/png" href="favicons/favicon-32x32.png" sizes="32x32">
    <link rel="icon" type="image/png" href="favicons/android-chrome-192x192.png" sizes="192x192">
    <link rel="icon" type="image/png" href="favicons/favicon-96x96.png" sizes="96x96">
    <link rel="icon" type="image/png" href="favicons/favicon-16x16.png" sizes="16x16">
    <link rel="manifest" href="manifest.json">
    <link rel="mask-icon" href="favicons/safari-pinned-tab.svg" color="#5bbad5">
    <meta name="msapplication-TileColor" content="#da532c">
    <meta name="msapplication-TileImage" content="/mstile-144x144.png">

    <!-- Tile icon for Win8 (144x144 + tile color) -->
    <meta name="msapplication-TileImage" content="images/touch/ms-touch-icon-144x144-precomposed.png">
    <meta name="msapplication-TileColor" content="#3372DF">
    <link rel="stylesheet"
          href="https://fonts.googleapis.com/css?family=Roboto:regular,bold,italic,thin,light,bolditalic,black,medium&amp;lang=en">
    <link rel="stylesheet" href="styles/materialIcons/material-icons.css">
    <link rel="stylesheet" href="styles/material.min.css">
    <link rel="stylesheet" href="styles/styles.css">
</head>
<body class='mdl-color--primary-contrast mdl-grid' style="padding: 0">
<div
    style='display: none; margin: 15%; position: relative; top: 50%; -moz-transform: translateY(50%)'
    class="minHeightFix mdl-card mdl-cell mdl-cell--12-col mdl-color--primary mdl-shadow--8dp">
    <div class="mdl-card__title">
        <h2 style="color: white" class="mdl-card__title-text"><i style='margin-right: 5px' class="material-icons">dashboard</i> <?echo $companyName?> PackApps</h2>
    </div>
    <p style="margin: 0; text-align: center; color: #e91e63; font-weight: 900; font-size larger"><? echo $errormsg ?></p>
    <div style="text-align: center" class="mdl-gridmdl-card__supporting-text">
        <button id='QAbutton' onclick="location.href = '/quality'" class="mdl-button mdl-js-button mdl-color--pink-500 mdl-color-text--white mdl-js-ripple-effect mdl-shadow--6dp mdl-cell mdl-cell--4-col" style="display: initial; height: 200px; float: left; border-radius: 12px; text-align: center; font-size: x-large; vertical-align: middle"><i style="font-size:45px" class="material-icons">check_circle</i><Br>Quality Assurance Panel<p id="QAlock" style='display: none; font-size: small;position: absolute; width: 100%; left: 0; color: white'>(This app is locked.)</p></button>
        <button id='productionButton' onclick="location.href = '/production'" class="mdl-button mdl-js-button mdl-color--pink-500 mdl-color-text--white mdl-js-ripple-effect mdl-shadow--6dp mdl-cell mdl-cell--4-col" style="display: initial; height: 200px; float: left; border-radius: 12px; text-align: center; font-size: x-large; vertical-align: middle"><i style="font-size:45px" class="material-icons">list</i><Br>Production Coordinator<p id="Productionlock"  style='display: none; font-size: small;position: absolute; width: 100%; left: 0; color: white'>(This app is locked.)</p></button>
        <button id='purchasingButton' onclick="location.href = '/purchasing'" class="mdl-button mdl-js-button mdl-color--pink-500 mdl-color-text--white mdl-js-ripple-effect mdl-shadow--6dp mdl-cell mdl-cell--4-col" style="display: initial; height: 200px; float: left; border-radius: 12px; text-align: center; font-size: x-large; vertical-align: middle"><i style="font-size:45px" class="material-icons">dashboard</i><Br>Purchasing Dashboard<p id="Purchasinglock" style='display: none; font-size: small;position: absolute; width: 100%; left: 0; color: white'>(This app is locked.)</p></button>
    </div>
    <div class='mdl-card__actions mdl-card__border'>
        <a onclick="logout()" class="mdl-button mdl-js-button mdl-js-ripple-effect">Log out</a>
        <a id="settings" href="controlPanel.php" class="mdl-button mdl-js-button mdl-js-ripple-effect">System Settings</a>
    </div>
</div>
<script src="scripts/material.min.js"></script>
<script src="scripts/jquery.min.js"></script>
<script>
    $(document).ready(function() {
        var allowedQA = <?echo ($allowedItems['allowedQuality'] > 0 ? 'true' : 'false')?>;
        var allowedPurchasing = <?echo ($allowedItems['allowedPurchasing'] > 0 ? 'true' : 'false')?>;
        var allowedProduction = <?echo ($allowedItems['allowedProduction'] > 0 ? 'true' : 'false')?>;

        if (!allowedQA) {
            $('#QAbutton').attr('disabled', true);
            $('#QAlock').show();
        }
        if (!allowedPurchasing) {
            $('#purchasingButton').attr('disabled', true);
            $('#Purchasinglock').show();
        }
        if (!allowedProduction) {
            $('#productionButton').attr('disabled', true);
            $('#Productionlock').show();
        }
        $('.mdl-card').fadeIn('slow');
    });

    function logout() {
        document.cookie = "auth=; expires=Thu, 01 Jan 1970 00:00:01 GMT;";
        document.cookie = "username=; expires=Thu, 01 Jan 1970 00:00:01 GMT;";
        window.location.replace('/');
    }
</script>
<i style='position: absolute; cursor: pointer; right: 0; bottom:0;color: white; font-size: larger' class="material-icons mdl-cell--hide-phone" onclick="$(this).hide();$('#about').slideDown()">info_outline</i>
<div id="about" style="display: none; position: fixed; right: 4px; bottom:0;color: white; font-size: smaller;">Made with &#10084; by <a style="color:white" href="//michaelclarkcuadrado.com">Michael Clark-Cuadrado</a><br>PackApps is <a style="color: white" href="//github.com/michaelclarkcuadrado/packapps">open source</a> under a GPL license.</div>
</body>
</html>