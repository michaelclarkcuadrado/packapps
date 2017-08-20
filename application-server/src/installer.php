<?php
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 6/5/17
 * Time: 1:22 PM
 */

require_once('scripts-common/Mobile_Detect.php');
require 'config.php';

$errormsg = "Welcome to PackApps! <br><br> To finish setup, create the administrator account.";

//make sure it only runs once
$firstRunCheck = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT systemInstalled FROM packapps_system_info"));
if ($firstRunCheck['systemInstalled'] > 0) {
    die ("<script>window.location.replace('/')</script>");
}

//catch own form
if (isset($_POST['username']) && isset($_POST['realname']) && isset($_POST['password']) && isset($_POST['password_confirm'])) {
    if ($_POST['password'] != $_POST['password_confirm']) {
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
    <link rel="stylesheet"
          href="https://fonts.googleapis.com/css?family=Roboto:regular,bold,italic,thin,light,bolditalic,black,medium&amp;lang=en">
    <link rel="stylesheet" href="styles/materialIcons/material-icons.css">
    <link rel="stylesheet" href="styles/material.min.css">
    <link rel="stylesheet" href="styles/styles.css">
</head>
<body class='mdl-color--primary-contrast mdl-layout__container' style="padding: 0">
<div class="mdl-layout mdl-js-layout" style="align-items: center;justify-content: center">
    <div class="mdl-layout__content" style="padding:24px;flex-grow:0">
        <div style=' display: none;'
             class="mdl-card appmenu-background-color mdl-shadow--8dp">
            <div class="mdl-card__title">
                <h2 style="color: white" class="mdl-card__title-text"><i style='margin-right: 5px' class="material-icons">dashboard</i> <? echo $companyName ?> PackApps</h2>
            </div>
            <p style="background-color: rgba(255, 255, 255, 0.5); margin: 10px; text-align: center; padding: 5px;"><? echo $errormsg ?></p>
            <div style="text-align: center" class="mdl-card__supporting-text">

                <form action="installer.php" method="post">
                    <div class="mdl-color--grey-200" style="margin: 5px; margin-top: 15px; border-radius: 15px">
                        <div style="width: 90%" class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                            <input autocomplete="off" class="mdl-textfield__input" type="text" name='realname'
                                   id="realname" autofocus>
                            <label class="mdl-textfield__label" for="password">Real Name</label>
                        </div>
                    </div>
                    <div class="mdl-color--grey-200" style="margin: 5px; margin-top: 15px; border-radius: 15px">
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
                    <button id="submitbtn" style="color: white; margin-top: 15px; width: 100%"
                            class="mdl-button appmenu-foreground-color mdl-button--raised">
                        Finish Setup
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
<script src="scripts/material.min.js"></script>
<script src="scripts/jquery.min.js"></script>
<script>
    $(document).ready(function () {
        $('.mdl-card').fadeIn('slow');

        $('#submitbtn').submit(function () {
            document.cookie = "auth=; expires=Thu, 01 Jan 1970 00:00:01 GMT;";
            document.cookie = "username=; expires=Thu, 01 Jan 1970 00:00:01 GMT;";
            document.cookie = "grower=; expires=Thu, 01 Jan 1970 00:00:01 GMT;";
            $('.mdl-card').fadeOut('fast');
            return true;
        });
    });
</script>
<i style='position: absolute;cursor: pointer; right: 0; bottom:0; font-size: larger' class="material-icons mdl-cell--hide-phone" onclick="$(this).hide();$('#about').slideDown()">info_outline</i>
<div id="about" style="display: none;text-align: right;  position: fixed; right: 4px; bottom:0; font-size: smaller;">PackApps is powered by <a href="https://packercloud.com">PackerCloud</a>
    Platform<br>Copyright 2015-<? echo date('Y') ?></div>
</body>
</html>