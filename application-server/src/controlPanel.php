<?php
include 'user_api.php';


//authentication
if (!isset($_COOKIE['auth']) || !isset($_COOKIE['username'])) {
    die("<script>window.location.replace('/')</script>");
} else if (!hash_equals($_COOKIE['auth'], crypt($_COOKIE['username'], $securityKey))) {
    die("<script>window.location.replace('/')</script>");
} else {
    $SecuredUserName = mysqli_real_escape_string($mysqli, $_COOKIE['username']);
    $checkAllowed = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT `Real Name`, isSystemAdministrator, purchasing_UserData.isAuthorizedForPurchases as purchasingRole, production_UserData.Role as productionRole, quality_UserData.Role as qualityRole, allowedProduction, allowedPurchasing, allowedQuality FROM master_users LEFT JOIN purchasing_UserData ON master_users.username=purchasing_UserData.Username LEFT JOIN quality_UserData ON master_users.username = quality_UserData.UserName LEFT JOIN production_UserData ON master_users.username = production_UserData.UserName WHERE master_users.username = '$SecuredUserName'"));
}
// end authentication

//create new User account
if($checkAllowed['isSystemAdministrator'] > 0 && isset($_POST['newUserName']) && isset($_POST['newRealName']) && isset($_POST['newPassword'])) {
    $isAdministrator = (isset($_POST['newAdministrator']) ? 1 : 0);
    createNewPackappsUser($mysqli, $_POST['newRealName'], $_POST['newUserName'], $_POST['newPassword'], $isAdministrator);
}

//process password changes
if (isset($_POST['password0']) && isset($_POST['password1']) && isset($_POST['password2'])) {
    $passwdChangeMsg = changePassword($mysqli, $SecuredUserName, $_POST['password0'], $_POST['password1'], $_POST['password2']);
} elseif (isset($_GET['passwordReset']) && $checkAllowed['isSystemAdministrator'] > 0) {

}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content='Purchasing dashboard'>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0">
    <title>System Settings - PackApps</title>

    <meta name="mobile-web-app-capable" content="yes">
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

    <link rel="stylesheet"
          href="https://fonts.googleapis.com/css?family=Roboto:regular,bold,italic,thin,light,bolditalic,black,medium&amp;lang=en">
    <link rel="stylesheet" href="styles/materialIcons/material-icons.css">
    <link rel="stylesheet" href="styles/material.min.css">
    <link rel="stylesheet" href="styles/styles.css">
</head>
<body>
<div class="demo-layout mdl-layout mdl-js-layout mdl-layout--fixed-drawer mdl-layout--fixed-header">
    <header class="demo-header mdl-layout__header mdl-color--grey-100 mdl-color-text--grey-600">
        <div class="mdl-layout__header-row">
            <span
                class="mdl-layout-title">PackApps System Settings<?php echo($checkAllowed['isSystemAdministrator'] > 0 ? ': <mark>Administrator Mode</mark>' : ""); ?></span>
            <div class="mdl-layout-spacer"></div>
        </div>
    </header>
    <div class="demo-drawer mdl-layout__drawer mdl-color--blue-grey-900 mdl-color-text--blue-grey-50">
        <header class="demo-drawer-header">
            <div class="demo-avatar-dropdown">
                <i style="margin: 2px" class="material-icons">account_circle</i>
                <span style='text-align: center;'> <? echo $checkAllowed['Real Name'] ?></span>
            </div>
        </header>
        <nav class="demo-navigation mdl-navigation mdl-color--blue-grey-800">
            <a class="mdl-navigation__link" onClick="$('.mdl-card').fadeOut('fast');" href="index.php"><i
                    class="mdl-color-text--teal-400 material-icons"
                    role="presentation">dashboard</i>Return to Menu</a>
            <a class="mdl-navigation__link" <?php echo($checkAllowed['isSystemAdministrator'] > 0 ? '' : "style='display: none !important'"); ?>
               href="#"
               onclick="$.get('/production/API/rebootDisplays.php'), $(this).children('i').addClass('spin').parent().children('span').text('Broadcasting reboot signal...')"><i
                    class="mdl-color-text--pink-300 material-icons"
                    role="presentation">cached</i><span>Reboot Displays</span></a>
            <a class="mdl-navigation__link" <?php echo($checkAllowed['isSystemAdministrator'] > 0 ? '' : "style='display: none !important'"); ?>
               href="quality/usermgmt.php"><i
                    class="mdl-color-text--cyan-300 material-icons"
                    role="presentation">mail_outline</i>Email Alerts</a>

        </nav>
    </div>
    <main class="mdl-layout__content mdl-color--grey-400">
        <div class="mdl-grid widthfixer demo-cards" id="injectUsersHere">
            <div id='newUser_Card'
                 class="mdl-cell mdl-cell--12-col-desktop mdl-cell--8-col-tablet mdl-cell--4-col-phone mdl-card mdl-shadow--4dp" style='display: none'>
                <div class="mdl-color--yellow-300 mdl-card__title">
                    <h2 class="mdl-card__title-text">New Packapps User</h2>
                    <div class="mdl-layout-spacer"><i style="cursor: pointer; float: right"
                                                      onclick="$('#newUser_Card').slideUp(), $('#addButton').fadeIn()"
                                                      class="material-icons">close</i></div>
                </div>
                <div class="mdl-card__supporting-text">
                    <form action="controlPanel.php" method="post" class="mdl-grid">
                        <div class='mdl-cell mdl-cell--12-col-desktop mdl-cell--8-col-tablet mdl-cell--4-col-phone mdl-grid' style="width: 100%">
                            <div class="mdl-cell mdl-cell--6-col mdl-cell--4-col-phone mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                                <input class="mdl-textfield__input" type="text" id="newUserName" name="newUserName"
                                       required>
                                <label class="mdl-textfield__label" for="newUserName">User Name</label>
                            </div>
                            <div class="mdl-cell mdl-cell--6-col mdl-cell--4-col-phone mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                                <input class="mdl-textfield__input" type="text" id="newRealName" name="newRealName"
                                       required>
                                <label class="mdl-textfield__label" for="newRealName">Real Name</label>
                            </div>
                            <div class="mdl-cell mdl-cell--6-col mdl-cell--4-col-phone mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                                <input class="mdl-textfield__input" type="password" id="newPassword" name="newPassword"
                                       required>
                                <label class="mdl-textfield__label" for="newPassword">Password</label>
                            </div>
                            <label class="mdl-cell mdl-cell--4-col mdl-switch mdl-js-switch mdl-js-ripple-effect" for="switch-1">
                                <input type="checkbox" id="switch-1" value="yes" name='newAdministrator' class="mdl-switch__input">
                                <span class="mdl-switch__label">New Administrator Account</span>
                            </label>
                            <button
                                class="mdl-cell mdl-cell--6-col-desktop mdl-cell--4-col-phone mdl-button mdl-js-ripple-effect mdl-js-button mdl-button--raised mdl-button--colored">
                                Create New Account
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="mdl-cell--4-col mdl-cell mdl-card mdl-shadow--4dp">
                <div class="mdl-card__title mdl-color--teal-300">
                    <h2 class="mdl-color-text--white mdl-card__title-text">Your Account Privileges</h2>
                </div>
                <table style="width: 100%" class="mdl-data-table mdl-js-data-table mdl-card__supporting-text">
                    <thead>
                    <tr>
                        <th class="mdl-data-table__cell--non-numeric">App</th>
                        <th class="mdl-data-table__cell--non-numeric">Status</th>
                        <th class="mdl-data-table__cell--non-numeric">Access Level</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td class="mdl-data-table__cell--non-numeric">Quality Panel</td>
                        <td id="qualityEnabled" class="mdl-data-table__cell--non-numeric"></td>
                        <td id="qualityRole" class="mdl-data-table__cell--non-numeric"></td>
                    </tr>
                    <tr>
                        <td class="mdl-data-table__cell--non-numeric">Production</td>
                        <td id="productionEnabled" class="mdl-data-table__cell--non-numeric"></td>
                        <td id="productionRole" class="mdl-data-table__cell--non-numeric"></td>
                    </tr>
                    <tr>
                        <td class="mdl-data-table__cell--non-numeric">Purchasing</td>
                        <td id="purchasingEnabled" class="mdl-data-table__cell--non-numeric"></td>
                        <td id="purchasingRole" class="mdl-data-table__cell--non-numeric"></td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div id='changeOwnPasswdCard'
                 class="mdl-card mdl-shadow--4dp mdl-cell mdl-cell--8-col-desktop mdl-cell--4-col-tablet mdl-cell--4-col-phone">
                <div class="mdl-card__title mdl-color--teal-300">
                    <h2 class="mdl-color-text--white mdl-card__title-text">Change your password</h2>
                </div>
                <div class="mdl-card__supporting-text">
                    <? if (isset($passwdChangeMsg)) {
                        echo "<span style='color: red; text-align: center'>" . $passwdChangeMsg . "</span>";
                    } ?>
                    <form action="controlPanel.php" method="post" class="mdl-grid">
                        <div
                            class="mdl-cell mdl-cell--6-col-desktop mdl-cell--4-col-phone mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                            <input class="mdl-textfield__input" type="password" name='password0' id="password0">
                            <label class="mdl-textfield__label" for="password0">Current Password</label>
                        </div>
                        <div
                            class="mdl-cell mdl-cell--6-col-desktop mdl-cell--4-col-phone mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                            <input class="mdl-textfield__input" type="password" name='password1' id="password1">
                            <label class="mdl-textfield__label" for="password1">New password</label>
                        </div>
                        <div
                            class="mdl-cell mdl-cell--6-col-desktop mdl-cell--4-col-phone mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                            <input class="mdl-textfield__input" type="password" name='password2' id="password2">
                            <label class="mdl-textfield__label" for="password2">Confirm new password</label>
                        </div>
                        <button
                            class="mdl-cell mdl-cell--6-col-desktop mdl-cell--4-col-phone mdl-button mdl-js-ripple-effect mdl-js-button mdl-button--raised mdl-button--colored">
                            Change Password
                        </button>
                    </form>
                </div>
            </div>
            <h2 style="text-align: center; width: 100%; <?php echo($checkAllowed['isSystemAdministrator'] > 0 ? '' : "display: none;"); ?>"
                class="mdl-cell mdl-cell--12-col-desktop mdl-cell--8-col-tablet mdl-cell--4-col-phone">Users on
                Packapps</h2>
            <hr>
            <!--Inject user cards here-->
        </div>
    </main>
</div>
<button id="addButton" onclick="showNewForm()"
        style="position: fixed; right: 24px; bottom: 24px; padding-top: 24px; margin-bottom: 0; z-index: 90; <?php echo($checkAllowed['isSystemAdministrator'] > 0 ? '' : "display: none;"); ?>"
        class="mdl-button mdl-shadow--8dp mdl-js-button mdl-button--fab mdl-js-ripple-effect mdl-button--colored mdl-color--yellow-300">
    <i class="material-icons">add</i>
</button>
<script src="scripts/material.min.js"></script>
<script src="scripts/jquery.min.js"></script>
<script>
    $(document).ready(function () {
        //set self privileges box to correct values
        var privilegeBlob = "<?echo addslashes(json_encode($checkAllowed))?>";
        privilegeBlob = JSON.parse(privilegeBlob);
        if (privilegeBlob.allowedProduction > 0) {
            $('#productionEnabled').html("<span style='color: green'>Enabled</span>");
            $('#productionRole').html(getPrivilegeDescriptionHTML('production', privilegeBlob.productionRole));
        } else {
            $('#productionEnabled').html("<span style='color: red'>Disabled</span>");
            $('#productionRole').html(getPrivilegeDescriptionHTML('production', 'Disabled'));
        }

        if (privilegeBlob.allowedPurchasing > 0) {
            $('#purchasingEnabled').html("<span style='color: green'>Enabled</span>");
            $('#purchasingRole').html(getPrivilegeDescriptionHTML('purchasing', privilegeBlob.purchasingRole));
        } else {
            $('#purchasingEnabled').html("<span style='color: red'>Disabled</span>");
            $('#purchasingRole').html(getPrivilegeDescriptionHTML('purchasing', 'Disabled'));
        }

        if (privilegeBlob.allowedQuality > 0) {
            $('#qualityEnabled').html("<span style='color: green'>Enabled</span>");
            $('#qualityRole').html(getPrivilegeDescriptionHTML('quality', privilegeBlob.qualityRole));
        } else {
            $('#qualityEnabled').html("<span style='color: red'>Disabled</span>");
            $('#qualityRole').html(getPrivilegeDescriptionHTML('quality', 'Disabled'));
        }

        if (<?echo($checkAllowed['isSystemAdministrator'] > 0 ? 'true' : 'false')?>) {
            setupUserCards();
        }
    });

    function setupUserCards() {
        //fetch users, create cards here
        $.getJSON('getUserAccountList.php', function (data) {
            var stringToInject = "";
            for (var user in data) {
                stringToInject += "<div class='mdl-cell mdl-cell--4-col mdl-card mdl-shadow--4dp'"
                    + (data[user]['isDisabled'] > 0 ? "style='opacity: .5'" : '')
                    + "><div class='mdl-card__title mdl-color--yellow-300'><h2 class='mdl-card__title-text'>"
                    + data[user]['Real Name']
                    + "</h2></div><table style='width:100%;' class='mdl-card__supporting-text mdl-data-table mdl-js-data-table'><thead><tr><th class='mdl-data-table__cell--non-numeric'>App</th><th class='mdl-data-table__cell--non-numeric'>Status</th><th class='mdl-data-table__cell--non-numeric'>Access Level</th></tr></thead><tbody><tr><td class='mdl-data-table__cell--non-numeric'>Quality</td><td class='mdl-data-table__cell--non-numeric'><label class='mdl-switch mdl-js-switch mdl-js-ripple-effect' for='switch-quality-"
                    + data[user]['username']
                    + "'><input type='checkbox' id='switch-quality-"
                    + data[user]['username']
                    + "' name='quality_"
                    + data[user]['username']
                    + "_switch' class='enableSwitch mdl-switch__input' "
                    + (data[user]['allowedQuality'] > 0 ? 'checked' : '')
                    + "></label></td><td style='padding-left:0; padding-right: 0'><p style='width:100%; margin-bottom: 0; text-align: center'><input class='quality_slider mdl-slider mdl-js-slider' id='"
                    + data[user]['username']
                    + "_quality_slider' name='"
                    + data[user]['username']
                    + "_quality_slider' type='range' min='1' max='3' value='"
                    + data[user]['qualityRole']
                    + "'><div style='text-align: center' id='"
                    + data[user]['username']
                    + "_quality_accesslevel'></div></p></td></tr><tr><td class='mdl-data-table__cell--non-numeric'>Production</td><td class='mdl-data-table__cell--non-numeric'><label class='mdl-switch mdl-js-switch mdl-js-ripple-effect' for='switch-production-"
                    + data[user]['username']
                    + "'><input type='checkbox' id='switch-production-"
                    + data[user]['username']
                    + "' name='production_"
                    + data[user]['username']
                    + "_switch' class='enableSwitch mdl-switch__input' "
                    + (data[user]['allowedProduction'] > 0 ? 'checked' : '')
                    + "></label></td><td style='padding-left:0; padding-right: 0'><p style='width:100%; margin-bottom: 0; text-align: center'><input class='production_slider mdl-slider mdl-js-slider' id='"
                    + data[user]['username']
                    + "_production_slider' name='"
                    + data[user]['username']
                    + "_production_slider' type='range' min='1' max='2' value='"
                    + data[user]['productionRole']
                    + "'><div style='text-align: center' id='"
                    + data[user]['username']
                    + "_production_accesslevel'></div></p></td></tr><tr><td class='mdl-data-table__cell--non-numeric'>Purchasing</td><td class='mdl-data-table__cell--non-numeric'><label class='mdl-switch mdl-js-switch mdl-js-ripple-effect' for='switch-purchasing-"
                    + data[user]['username']
                    + "'><input type='checkbox' id='switch-purchasing-"
                    + data[user]['username']
                    + "' name='purchasing_"
                    + data[user]['username']
                    + "_switch' class='enableSwitch mdl-switch__input' "
                    + (data[user]['allowedPurchasing'] > 0 ? 'checked' : '')
                    + "></label></td><td style='padding-left:0; padding-right: 0'><p style='width:100%; margin-bottom: 0; text-align: center'><input class='purchasing_slider mdl-slider mdl-js-slider' id='"
                    + data[user]['username']
                    + "_purchasing_slider' name='"
                    + data[user]['username']
                    + "_purchasing_slider' type='range' min='1' max='2' value='"
                    + data[user]['purchasingRole']
                    + "'><div style='text-align: center' id='"
                    + data[user]['username']
                    + "_purchasing_accesslevel'></div></p></td></tr></tbody></table><div style='margin-left: 17px;' class='mdl-card__subtitle-text'>User Name: "
                    + data[user]['username']
                    + "<br>Last Login: "
                    + data[user]['lastLogin']
                    + "</div><div class='mdl-card--border mdl-card__actions'><a data-username='"
                    + data[user]['username']
                    + "' class='mdl-button mdl-button--colored mdl-js-button mdl-js-ripple-effect password-resetter'>Reset Password </a></div><div class='mdl-card__menu'><button id='toggle_disable_"
                    + data[user]['username']
                    + "' class='mdl-button mdl-button--icon mdl-js-button mdl-js-ripple-effect user-toggle'><i class='material-icons'>"
                    + (data[user]['isDisabled'] > 0 ? 'lock' : 'lock_open')
                    + "</i></button><div class='mdl-tooltip' for='toggle_disable_"
                    + data[user]['username']
                    + "'>"
                    + (data[user]['isDisabled'] > 0 ? 'Unlock User' : 'Lock User')
                    + "</div></div></div>";
            }
            $('#injectUsersHere').append(stringToInject);
            componentHandler.upgradeDom();
            //label access levels on usercards, plus attach listeners
            $('.enableSwitch').each(function (index) {
                var curElemData = $(this).attr('name').split('_');
                if ($(this).is(':checked')) {
                    var level = $('#' + curElemData[1] + '_' + curElemData[0] + '_slider').val()
                    $('#' + curElemData[1] + '_' + curElemData[0] + '_accesslevel').html(getPrivilegeDescriptionHTML(curElemData[0], numericAccessLeveltoDescription(curElemData[0], level)));
                } else {
                    $('#' + curElemData[1] + '_' + curElemData[0] + '_accesslevel').html(getPrivilegeDescriptionHTML(curElemData[0], 'Disabled'));
                    $('#' + curElemData[1] + '_' + curElemData[0] + '_slider').attr('disabled', true);
                }
            }).on('change', function () {
                var infoArray = $(this).attr('name').split('_');
                var checkboxValue = $(this).is(':checked');
                $.ajax({
                    type: 'POST',
                    url: 'adjustUserPermissions.php',
                    data: {
                        'packapp': infoArray[0],
                        'userUnderEdit': infoArray[1],
                        'propUnderEdit': 'Status',
                        'propValue': checkboxValue
                    },
                    success: function () {
                        if (checkboxValue) {
                            $('#' + infoArray[1] + '_' + infoArray[0] + '_slider').val(1).attr('disabled', false);
                            $('#' + infoArray[1] + '_' + infoArray[0] + '_accesslevel').html(getPrivilegeDescriptionHTML(infoArray[0], numericAccessLeveltoDescription(infoArray[0], 1)));
                        } else {
                            $('#' + infoArray[1] + '_' + infoArray[0] + '_slider').val(1).attr('disabled', true);
                            $('#' + infoArray[1] + '_' + infoArray[0] + '_accesslevel').html("<span style='color: red'>Disabled</span>");
                        }
                    },
                    error: function () {
                        $('#' + infoArray[0] + '_' + infoArray[1] + '_accesslevel').html("<span style='color: red'>ERROR</span>");
                    }
                });
            });

            $('.quality_slider, .production_slider, .purchasing_slider').on('input', function () {
                var infoArray = $(this).attr('name').split('_');
                var numericLevel = $(this).val();
                $.ajax({
                    type: 'POST',
                    url: 'adjustUserPermissions.php',
                    data: {
                        'packapp': infoArray[1],
                        'userUnderEdit': infoArray[0],
                        'propUnderEdit': 'AccessLevel',
                        'propValue': numericAccessLeveltoDescription(infoArray[1], numericLevel)
                    },
                    success: function () {
                        $('#' + infoArray[0] + '_' + infoArray[1] + '_accesslevel').html(getPrivilegeDescriptionHTML(infoArray[1], numericAccessLeveltoDescription(infoArray[1], numericLevel)));
                    },
                    error: function () {
                        $('#' + infoArray[0] + '_' + infoArray[1] + '_accesslevel').html("<span style='color: red'>ERROR</span>");
                    }
                });
            });

            $('.user-toggle').on('click', function () {
                var user = $(this).attr('id').split('_')[2];
                var elem = $(this);
                $.get('adjustUserPermissions.php?disableToggle=' + user, function () {
                    if (elem.children('i').text() == 'lock') {
                        elem.children('i').text('lock_open').parent().parent().parent().css('opacity', '1').find('.enableSwitch').attr('disabled', false);
                    } else {
                        elem.children('i').text('lock').parent().parent().parent().css('opacity', '.5').find('.enableSwitch').attr('disabled', true);
                    }
                });
            });

            $('.password-resetter').on('click', function () {
                var elem = $(this);
                var username = $(this).attr('data-username');
                $.get('controlPanel.php?passwordReset=' + username, function () {
                    elem.text('New PW: ' + username);
                })
            });
        });
    }

    //converts slider value into a level that can be passed to getPrivilegeDescriptionHTML() or inserted into database
    function numericAccessLeveltoDescription(packApp, numericLevel) {
        if (packApp == 'quality') {
            if (numericLevel == '1') {
                return 'Weight';
            } else if (numericLevel == '2') {
                return 'INS';
            } else if (numericLevel == '3') {
                return 'QA';
            } else {
                return 'error';
            }
        } else if (packApp == 'production') {
            if (numericLevel == '1') {
                return 'ReadOnly';
            } else if (numericLevel == '2') {
                return 'Production';
            } else {
                return 'error';
            }
        } else if (packApp == 'purchasing') {
            //purchasing uses a boolean 1 or 0 as access level
            return numericLevel - 1;
        } else {
            return 'error';
        }
    }

    function getPrivilegeDescriptionHTML(packApp, level) {
        if (level == 'Disabled') {
            return "<span style='color: red'>Disabled</span>";
        }
        if (packApp == 'quality') {
            if (level == "QA") {
                return "<span style='color: green'>Full</span>";
            } else if (level == "INS") {
                return "<span style='color: orange'>Receipt Inspector</span>";
            } else if (level == "Weight") {
                return "<span style='color: red'>Weight Input Only</span>";
            } else {
                return "error";
            }
        } else if (packApp == 'production') {
            if (level == 'Production') {
                return "<span style='color: green'>Full</span>";
            } else if (level == "ReadOnly") {
                return "<span style='color: orange'>Read-Only</span>";
            } else {
                return "error";
            }
        } else if (packApp == 'purchasing') {
            if (level == 1) {
                return "<span style='color: green'>Full</span>";
            } else if (level == 0) {
                return "<span style='color: orange'>No Purchases</span>";
            } else {
                return "error";
            }
        }
    }

    function showNewForm() {
        $("main").animate({scrollTop: 0}, "fast", "swing", function () {
            $('#newUser_Card').slideDown();
            $('#addButton').fadeOut();
        });
    }
</script>
</body>
</html>