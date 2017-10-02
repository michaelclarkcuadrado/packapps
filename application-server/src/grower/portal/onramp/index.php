<?php
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 6/5/17
 * Time: 1:22 PM
 */

require '../../../config.php';
$userData = packapps_authenticate_grower(true);

//determine registration state
if ($userData['login_email'] == null && $userData['confirm_email_sent'] == 0) {
    $statusMessage = "Welcome to PackApps, " . $userData['GrowerName'] . "! <br><br> To finish setting up your grower account, we need to confirm an email address.";
    if ($userData['password_change_required'] > 0) {
        $passwordChange = true;
    }
} elseif (!isset($_GET['key']) && $userData['confirm_email_sent'] > 0 && $userData['email_confirmed'] == 0) {
    $statusMessage = "We've sent you a confirmation email at " . $userData['login_email'] . ". <br><br> Check your inbox for the link.";
} elseif ($userData['email_confirm_key'] !== null && hash_equals($_GET['key'], $userData['email_confirm_key']) && $userData['email_confirmed'] == 0) {
    mysqli_query($mysqli, "UPDATE grower_GrowerLogins SET email_confirmed = 1 WHERE GrowerCode = '" . $userData['GrowerCode'] . "'");
    $statusMessage = "Email Confirmed. <br><br> Your account is now activated. Enjoy!";
} else {
    die ("<script>window.location.replace('/grower')</script>");
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
    <link rel="stylesheet" href="../../../styles/materialIcons/material-icons.css">
    <link rel="stylesheet" href="../../../styles/material.min.css">
    <link rel="stylesheet" href="../../../styles/styles.css">
</head>
<body class='mdl-color--primary-contrast mdl-layout__container' style="padding: 0">
<div class="mdl-layout mdl-js-layout" style="align-items: center;justify-content: center">
    <div class="mdl-layout__content" style="padding:24px;flex-grow:0">
        <div style=' display: none;'
             class="mdl-card appmenu-background-color mdl-shadow--8dp">
            <div class="mdl-card__title">
                <h2 style="color: white" class="mdl-card__title-text"><i style='margin-right: 5px' class="material-icons">dashboard</i> <? echo $companyName ?> PackApps</h2>
            </div>
            <p style="background-color: rgba(255, 255, 255, 0.5); margin: 10px; text-align: center; padding: 5px;"><? echo $statusMessage ?></p>
            <div style="text-align: center;" class="mdl-card__supporting-text">

                <form action="installer.php" method="post">
                    <div class="mdl-color--grey-200" style="margin: 5px; margin-top: 15px;  border-radius: 15px">
                        <div style="width: 90%" class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                            <input autocomplete="off" autocapitalize="none" data-required class="mdl-textfield__input" type="email"
                                   name='username' id="username">
                            <label class="mdl-textfield__label" for="username">Email</label>
                        </div>
                    </div>
                    <div class="mdl-color--grey-200" style="margin: 5px; margin-top: 15px; border-radius: 15px">
                        <div style="width: 90%" class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                            <input autocomplete="off" class="mdl-textfield__input" data-required type="password" name='password'
                                   id="password">
                            <label class="mdl-textfield__label" for="password">New Password</label>
                            <span class="mdl-textfield__error">This password is too weak.</span>
                        </div>
                    </div>
                    <div class="mdl-color--grey-200" style="margin: 5px; margin-top: 15px; border-radius: 15px">
                        <div style="width: 90%" class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                            <input autocomplete="off" class="mdl-textfield__input" data-required type="password" name='password_confirm'
                                   id="password_confirm">
                            <label class="mdl-textfield__label" for="password">Reconfirm password</label>
                            <span class="mdl-textfield__error">This doesn't match.</span>
                        </div>
                    </div>
                    <button id="submitbtn" onClick="$('.mdl-card').fadeOut('fast');"  style="color: white; margin-top: 15px; width: 100%"
                            class="mdl-button appmenu-foreground-color mdl-button--raised">
                        Confirm my email
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
<script src="../../../scripts/material.min.js"></script>
<script src="../../../scripts/jquery.min.js"></script>
<script src="../../../scripts-common/zxcvbn.js"></script>
<script>
    $(document).ready(function () {
        $('.mdl-card').fadeIn('slow');

        $('input[data-required=true]').attr('required', true);

        $('#password').on('keyup', function () {
            var strength = zxcvbn($('#password').val()).score;
            console.log(strength);
            if (strength < 3) {
                $('#password').parent().addClass('is-invalid');
                $('submitbtn').attr('disabled', true);
            } else {
                $('#password').parent().removeClass('is-invalid');
                $('submitbtn').attr('disabled', false);
            }
        });

        $('#password_confirm').on('keyup', function() {
            var val1 = ;
            var val2 = ;
            if () {
                $('#password').parent().addClass('is-invalid');
                $('submitbtn').attr('disabled', true);
            } else {
                $('#password').parent().removeClass('is-invalid');
                $('submitbtn').attr('disabled', false);
            }
        });
    });
</script>
<i style='position: absolute;cursor: pointer; right: 0; bottom:0; font-size: larger' class="material-icons mdl-cell--hide-phone" onclick="$(this).hide();$('#about').slideDown()">info_outline</i>
<div id="about" style="display: none;text-align: right;  position: fixed; right: 4px; bottom:0; font-size: smaller;">PackApps is powered by the <a href="https://packercloud.com">PackerCloud</a>
    Platform<br>Copyright 2015-<? echo date('Y') ?></div>
</body>
</html>