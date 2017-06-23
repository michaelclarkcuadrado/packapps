<?php
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 6/5/17
 * Time: 1:22 PM
 */

require_once('scripts/Mobile_Detect.php');
require 'config.php';

$errormsg = "To finish setup, create the administrator account.";

//make sure it only runs once
$firstRunCheck = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT systemInstalled FROM packapps_system_info"));
if($firstRunCheck['systemInstalled'] > 0){
    die ("<script>window.location.replace('/')</script>");
}

//catch own form
if(isset($_POST['username']) && isset($_POST['realname']) && isset($_POST['password']) && isset($_POST['password_confirm'])){
    if($_POST['password'] != $_POST['password_confirm']){
        $errormsg = "Those passwords did not match. Please try again.";
    } else {
        initialize_packapps($mysqli, $companyShortName);
        createNewPackappsUser($mysqli, $_POST['realname'], $_POST['username'], $_POST['password'], 1);
        die ("<script>window.location.replace('/')</script>");
    }
}


?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content='Purchasing dashboard'>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0">
    <title>Log In - PackApps</title>

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
<div class="mdl-layout-spacer"></div>
<div style=' display: none; margin: 15%; '
    class="mdl-card mdl-cell mdl-cell--4-col mdl-color--primary mdl-shadow--8dp">
    <div class="mdl-card__title">
        <h2 style="color: white" class="mdl-card__title-text"><i style='margin-right: 5px' class="material-icons">dashboard</i> <?echo $companyName?> PackApps</h2>
    </div>
    <p style="margin: 0; text-align: center; color: #e91e63; font-weight: 900; font-size larger"><? echo $errormsg ?></p>
    <div style="text-align: center" class="mdl-card__supporting-text">

        <form action="installer.php" method="post">
            <div class="mdl-color--grey-200" style="margin: 5px; margin-top: 15px; border-radius: 15px">
                <div style="width: 90%" class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                    <input autocomplete="off" class="mdl-textfield__input" type="text" name='realname'
                           id="realname" autofocus>
                    <label class="mdl-textfield__label" for="password">Real Name</label>
                </div>
            </div>
            <div class="mdl-color--grey-200" style="margin: 5px;  border-radius: 15px">
                <div style="width: 90%" class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                    <input autocomplete="off" autocapitalize="none" class="mdl-textfield__input" type="text"
                           name='username' id="username">
                    <label class="mdl-textfield__label" for="username">Username</label>
                </div>
            </div>
            <div class="mdl-color--grey-200" style="margin: 5px; margin-top: 15px; border-radius: 15px">
                <div style="width: 90%" class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                    <input autocomplete="off" class="mdl-textfield__input" type="password" name='password'
                           id="password">
                    <label class="mdl-textfield__label" for="password">Password</label>
                </div>
            </div>
            <div class="mdl-color--grey-200" style="margin: 5px; margin-top: 15px; border-radius: 15px">
                <div style="width: 90%" class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                    <input autocomplete="off" class="mdl-textfield__input" type="password" name='password_confirm'
                           id="password_confirm">
                    <label class="mdl-textfield__label" for="password">Reconfirm password</label>
                </div>
            </div>
            <button onClick="$('.mdl-card').fadeOut('fast');" style="color: white; margin-top: 15px; width: 100%"
                    class="mdl-button mdl-color--pink-500 mdl-button--raised">
                Create new user and finish setup
            </button>
        </form>
    </div>
</div>
<div class="mdl-layout-spacer"></div>
<script src="scripts/material.min.js"></script>
<script src="scripts/jquery.min.js"></script>
<script>
    $(document).ready(function() {
        $('.mdl-card').fadeIn('slow');
    });
</script>
<i style='position: absolute; cursor: pointer; right: 0; bottom:0;color: white; font-size: larger' class="material-icons mdl-cell--hide-phone" onclick="$(this).hide();$('#about').slideDown()">info_outline</i>
<div id="about" style="display: none; position: fixed; right: 4px; bottom:0;color: white; font-size: smaller;">Made with &#10084; by <a style="color:white" href="//michaelclarkcuadrado.com">Michael Clark-Cuadrado</a><br>Copyright, 2015, 2016, 2017</div></body>
</html>