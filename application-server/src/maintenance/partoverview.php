<?php
require '../config.php';
$userInfo = packapps_authenticate_user('maintenance');
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content='Purchasing dashboard'>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0">
    <title>Maintenance Dashboard</title>

    <link rel="stylesheet"
          href="https://fonts.googleapis.com/css?family=Roboto:regular,bold,italic,thin,light,bolditalic,black,medium&amp;lang=en">
    <link rel="stylesheet" href="../styles-common/materialIcons/material-icons.css">
    <link rel="stylesheet" href="../styles-common/material.min.css">
    <link rel="stylesheet" href="../styles-common/styles.css">
</head>
<body>
<div class="demo-layout mdl-layout mdl-js-layout mdl-layout--fixed-drawer mdl-layout--fixed-header">
    <header class="demo-header mdl-layout__header mdl-color--grey-100 mdl-color-text--grey-600">
        <div class="mdl-layout__header-row">
            <span class="mdl-layout-title">Parts Overview</span>
            <div class="mdl-layout-spacer"></div>
            <? echo $companyName ?>
        </div>
    </header>
    <div class="demo-drawer mdl-layout__drawer mdl-color--blue-grey-900 mdl-color-text--blue-grey-50">
        <header class="demo-drawer-header">
            <div class="demo-avatar-dropdown">
                <i style="margin: 2px" class="material-icons">account_circle</i>
                <span style='text-align: center;width:100%'><? echo $userInfo['Real Name'] ?></span>
                <div class="mdl-layout-spacer"></div>
                <button id="accbtn" class="mdl-button mdl-js-button mdl-js-ripple-effect mdl-button--icon">
                    <i class="material-icons" role="presentation">arrow_drop_down</i>
                    <span class="visuallyhidden">Accounts</span>
                </button>
                <ul class="mdl-menu mdl-menu--bottom-right mdl-js-menu mdl-js-ripple-effect" for="accbtn">
                    <li class="mdl-menu__item"><i class="material-icons">verified_user</i><?echo $userInfo['Meaning']?> Access</li>
                    <li onclick="location.href = '/appMenu.php'" class="mdl-menu__item"><i class="material-icons">exit_to_app</i>Exit
                        to menu
                    </li>
                </ul>
            </div>
        </header>
        <nav class="demo-navigation mdl-navigation mdl-color--blue-grey-800">
            <a class="mdl-navigation__link" onClick="$('.mdl-card').fadeOut('fast');" href="index.php"><i
                    class="mdl-color-text--teal-400 material-icons"
                    role="presentation">home</i>Home</a>
            <a class="mdl-navigation__link" onClick="$('.mdl-card').fadeOut('fast');" href="issueoverview.php"><i
                        class="mdl-color-text--amber-400 material-icons"
                        role="presentation">assignment_late</i>Issues</a>
            <a class="mdl-navigation__link" onClick="$('.mdl-card').fadeOut('fast');" href="partoverview.php"><i
                        class="mdl-color-text--green-400 material-icons"
                        role="presentation">build</i>Parts</a>
        </nav>
    </div>
    <main class="mdl-layout__content mdl-color--grey-400">
        <div class="widthfixer mdl-grid demo-cards">

        </div>
    </main>
</div>
<script src="../scripts-common/material.min.js"></script>
<script src="../scripts-common/jquery.min.js"></script>
<!--<script src='../scripts-common/Chart.js'></script>-->
<script>
    $(document).ready(function () {
        $('.mdl-card').fadeIn('fast');

    });
</script>
</body>
</html>