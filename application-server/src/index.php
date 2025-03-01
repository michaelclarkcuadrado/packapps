<?php
/**
 * Created by PhpStorm.
 * User: Michael Clark-Cuadrado
 * Date: 7/6/2016
 * Time: 8:55 AM
 */
require_once('scripts-common/Mobile_Detect.php');
$detect = new Mobile_Detect();
//stop IE from loading
if ($detect->is('IE')) {
    die("<div style='height: 100%;text-align: center; background-color: white;'><div style='top:20%;position:relative;font-size:25px'>Sorry, Internet Explorer is not supported. Try again with a newer browser, such as Firefox or Chrome.</div></div>");
}

require 'config.php';

use WhiteHat101\Crypt\APR1_MD5;

//try to perform login, or redirect if already logged in
$errormsg = "";
if (isset($_COOKIE['auth']) && isset($_COOKIE['username'])) { //do redirect
    if (isset($_COOKIE['grower']) && $_COOKIE['grower'] == 'true') {
        if (hash_equals($_COOKIE['auth'], crypt($_COOKIE['username'], $growerSecurityKey))) {
            $check_grower_onramped = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT email_confirmed FROM grower_GrowerLogins"));
            die(header('Location: grower/portal'));
        } else {
            //Hash mismatch, clear all cookies and try to login again
            echo "<script>document.cookie = \"auth=; expires=Thu, 01 Jan 1970 00:00:01 GMT;\";
                document.cookie = \"username=; expires=Thu, 01 Jan 1970 00:00:01 GMT;\";
                document.cookie = \"grower=; expires=Thu, 01 Jan 1970 00:00:01 GMT;\";
                window.location.replace('/');</script>";
        }
    } else {
        if (hash_equals($_COOKIE['auth'], crypt($_COOKIE['username'], $securityKey))) {
            die(header('Location: appMenu.php'));
        } else {
            //Hash mismatch, clear all cookies and try to login again
            echo "<script>document.cookie = \"auth=; expires=Thu, 01 Jan 1970 00:00:01 GMT;\";
                document.cookie = \"username=; expires=Thu, 01 Jan 1970 00:00:01 GMT;\";
                document.cookie = \"grower=; expires=Thu, 01 Jan 1970 00:00:01 GMT;\";
                window.location.replace('/');</script>";
        }
    }
} else if (isset($_POST['username']) && isset($_POST['password'])) { //do login
    if (isset($_POST['grower']) && $_POST['grower'] == 'true') {
        //do grower account login
        //inputted username may be username or email, so check
        $username = mysqli_real_escape_string($mysqli, $_POST['username']);
        $growercodeAndHash = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT GrowerCode, `Password`, `isLoginDisabled` FROM grower_GrowerLogins WHERE GrowerCode = '$username' OR login_email = '$username'"));
        $hash = $growercodeAndHash['Password'];
        $username = $growercodeAndHash['GrowerCode'];
        if (APR1_MD5::check($_POST['password'], $hash)) {
            if ($growercodeAndHash['isLoginDisabled'] > 0) {
                $errormsg = "Your account is currently pending. Please contact your packhouse for more information.";
            } else {
                setcookie('username', $username);
                setcookie('auth', crypt($username, $growerSecurityKey));
                setcookie('grower', 'true');
                mysqli_query($mysqli, "UPDATE grower_GrowerLogins SET `lastLogin`=NOW() WHERE GrowerCode = '$username'");
                $check_grower_onramped = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT email_confirmed FROM grower_GrowerLogins"));
                if ($check_grower_onramped['email_confirmed'] > 1) {
                    die(header('Location: grower/portal'));
                } else {
                    die(header('Location: grower/portal/onramp'));
                }
            }
        } else {
            $errormsg = "That don't seem match our records. Please try again.";
        }
    } else {
        //Do packhouse account login    window.location = "portal/"
        $username = mysqli_real_escape_string($mysqli, $_POST['username']);
        $hash = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT `Password`, isDisabled FROM packapps_master_users WHERE username = '" . $username . "'"));
        if (APR1_MD5::check($_POST['password'], $hash['Password'])) {
            if ($hash['isDisabled'] > 0) {
                $errormsg = "Your account is currently pending. Please contact your packhouse.";
            } else {
                setcookie('username', $username);
                setcookie('auth', crypt($username, $securityKey));
                mysqli_query($mysqli, "UPDATE packapps_master_users SET `lastLogin`=NOW() WHERE username = '$username'");
                die(header('Location: appMenu.php'));
            }
        } else {
            $errormsg = "That don't seem to match our records. Please try again.";
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content='Packapps by Packercloud'>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0">
    <title>Log In - PackApps</title>

    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="apple-mobile-web-app-title" content="PackApps">

    <link rel="stylesheet"
          href="https://fonts.googleapis.com/css?family=Roboto:regular,bold,italic,thin,light,bolditalic,black,medium&amp;lang=en">
    <link rel="stylesheet" href="styles/materialIcons/material-icons.css">
    <link rel="stylesheet" href="styles/material.min.css">
    <link rel="stylesheet" href="styles/styles.css">
</head>
<body class='mdl-color--primary-contrast mdl-layout__container' style="padding: 0">
<div class="mdl-layout mdl-js-layout" style="align-items: center;justify-content: center">
    <div class="mdl-layout__content" style="padding:24px;flex-grow:0">
        <div style=' display: none; width:initial; max-width: 1150px'
             class="mdl-card appmenu-background-color mdl-shadow--16dp">
            <div class="mdl-card__title">
                <h2 style="color: white" class="mdl-card__title-text"><i style='margin-right: 5px' class="material-icons">dashboard</i> <? echo $companyName ?> PackApps</h2>
            </div>
            <p id="errorBox" style="margin: 0 15px 0 15px; text-align: center; color: white; font-size: 16px; font-weight: 500"><? echo $errormsg ?></p>
            <div style="text-align: center" class="mdl-card__supporting-text">
                <div id="loginTypeChooser">
                    <table style="width:100%;height:100%">
                        <tr>
                            <td>
                                <button onclick="showLoginForm(false)" style="width:100%; min-height: 60px; margin: 3px" class="mdl-button mdl-js-button mdl-button--raised appmenu-foreground-color">
                                    Packhouse Login
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <button onclick="showLoginForm(true)" style="width:100%; min-height: 60px; margin: 3px" class="mdl-button mdl-js-button mdl-button--raised appmenu-foreground-color">
                                    Grower Login
                                </button>
                            </td>
                        </tr>
                    </table>
                </div>
                <form style="display:none" id="loginForm" action="index.php" method="post">
                    <div class="mdl-color--grey-200" style="margin: 5px;  border-radius: 15px">
                        <div style="width: 90%" class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                            <input autocomplete="off" autocapitalize="none" class="mdl-textfield__input" type="text"
                                   name='username' id="username" autofocus>
                            <label id="usernamefield" class="mdl-textfield__label" for="username">Username</label>
                        </div>
                    </div>
                    <div class="mdl-color--grey-200" style="margin: 5px; margin-top: 15px; border-radius: 15px">
                        <div style="width: 90%" class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                            <input autocomplete="off" class="mdl-textfield__input" type="password" name='password'
                                   id="password">
                            <label class="mdl-textfield__label" for="password">Password</label>
                        </div>
                    </div>
                    <input id="growerInputSend" type="hidden" name="grower" value="true" disabled>
                    <button onClick="$('.mdl-card').fadeOut('fast');" style="color: white; margin-top: 15px; width: 100%"
                            class="mdl-button appmenu-foreground-color mdl-button--raised">
                        Log In to Packapps
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
    });

    function showLoginForm(isGrower) {
        $('#loginTypeChooser').hide();
        $('#loginForm').slideDown();
        if (isGrower) {
            $("#errorBox").html("Grower Portal Login");
            $('#usernamefield').html("Username or Email");
            $("#growerInputSend").prop('disabled', false);
        } else {
            $("#errorBox").html("Packhouse Login");
        }
        $('#username').focus();
    }
</script>
<div id="about" style="text-align: right; position: fixed; right: 4px; bottom:0; font-size: smaller;">PackApps is powered by the <a target="_blank" href="https://packercloud.com">PackerCloud</a>
    Platform<br>Copyright 2015-<? echo date('Y') ?>, PackerCloud LLC.
</div>
</body>
</html>