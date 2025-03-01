<?php
require 'config.php';
$userData = packapps_authenticate_user();

//enumerate packapps
$packapps_query = mysqli_query($mysqli, "SELECT short_app_name, long_app_name FROM packapps_appProperties WHERE isEnabled = 1");
$installedPackapps = array();
while($packapp = mysqli_fetch_assoc($packapps_query)){
    array_push($installedPackapps, $packapp);
}

//create CheckAllowed query
$checkAllowedQuery = "SELECT `Real Name`, isSystemAdministrator";
//add table fields to query
foreach($installedPackapps as $packapp){
    $checkAllowedQuery .= ", ".$packapp['short_app_name']."_UserData.Role+0 AS ".$packapp['short_app_name']."Role";
    $checkAllowedQuery .= ", allowed".ucfirst($packapp['short_app_name']);
}
$checkAllowedQuery .= " FROM packapps_master_users";
//add table joins to query
foreach($installedPackapps as $packapp){
    $checkAllowedQuery .= " LEFT JOIN ".$packapp['short_app_name']."_UserData ON packapps_master_users.username=".$packapp['short_app_name']."_UserData.UserName";
}
$checkAllowed = mysqli_fetch_assoc(mysqli_query($mysqli, $checkAllowedQuery." WHERE packapps_master_users.username = '".$userData['username']."'"));


//get permissions table
$permissionsQuery = mysqli_query($mysqli, "SELECT packapp, permissionLevel, Meaning, Color FROM packapps_app_permissions");
$permissionstable = array();
while ($row = mysqli_fetch_assoc($permissionsQuery)){
    if(isset($permissionstable[$row['packapp']])){
        $permissionstable[$row['packapp']][$row['permissionLevel']] = array('Color' => $row['Color'], 'Meaning' => $row['Meaning']);
    } else {
        $permissionstable[$row['packapp']] = array($row['permissionLevel'] =>  array('Color' => $row['Color'], 'Meaning' => $row['Meaning']));
    }
}
unset($permissionsQuery);

/*Handle form requests here*/

//create new User account
if($checkAllowed['isSystemAdministrator'] > 0 && isset($_POST['newUserName']) && isset($_POST['newRealName']) && isset($_POST['newPassword'])) {
    $isAdministrator = (isset($_POST['newAdministrator']) ? 1 : 0);
    $passwdChangeMsg = createNewPackappsUser($mysqli, $_POST['newRealName'], $_POST['newUserName'], $_POST['newPassword'], $isAdministrator);
}

//process password changes
if (isset($_POST['password0']) && isset($_POST['password1']) && isset($_POST['password2'])) {
    $passwdChangeMsg = changePassword($mysqli, $userData['username'], $_POST['password0'], $_POST['password1'], $_POST['password2']);
} elseif (isset($_GET['passwordReset']) && $checkAllowed['isSystemAdministrator'] > 0) {
    $passwdChangeMsg = resetPassword($mysqli, $_GET['passwordReset']);
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
                class="mdl-layout-title">PackApps System Settings<?php echo($checkAllowed['isSystemAdministrator'] > 0 ? ': <mark>Admin</mark>' : ""); ?></span>
            <div class="mdl-layout-spacer"></div>
        </div>
    </header>
    <div class="demo-drawer mdl-layout__drawer mdl-color--blue-grey-900 mdl-color-text--blue-grey-50">
        <header class="demo-drawer-header">
            <div class="demo-avatar-dropdown">
                <i style="margin: 2px" class="material-icons">account_circle</i>
                <span style='text-align: center;width: 100%'> <? echo $checkAllowed['Real Name'] ?></span>
            </div>
        </header>
        <nav class="demo-navigation mdl-navigation mdl-color--blue-grey-800">
            <a class="mdl-navigation__link" onClick="$('.mdl-card').fadeOut('fast');" href="appMenu.php"><i
                    class="mdl-color-text--teal-400 material-icons"
                    role="presentation">dashboard</i>Return to Menu</a>
            <a class="mdl-navigation__link" <?php echo($checkAllowed['isSystemAdministrator'] > 0 ? '' : "style='display: none !important'"); ?>
               href="quality/emailmgmt.php"><i
                    class="mdl-color-text--cyan-300 material-icons"
                    role="presentation">mail_outline</i>Email Alert List</a>
            <a class="mdl-navigation__link" <?php echo($checkAllowed['isSystemAdministrator'] > 0 ? '' : "style='display: none !important'"); ?>
               href="#"
               onclick="$.get('/production/API/rebootDisplays.php'), $(this).children('i').addClass('spin').parent().children('span').text('Broadcasting reboot signal...')"><i
                        class="mdl-color-text--pink-300 material-icons"
                        role="presentation">cached</i><span>Reboot Displays</span></a>
            <a class="mdl-navigation__link" <?php echo($checkAllowed['isSystemAdministrator'] > 0 ? '' : "style='display: none !important'"); ?>
               href="mailto:support@packercloud.com?subject=Packapps Support Request&body=Issue:%0D%0A%0D%0A%0D%0AUser: <?echo $userData['username']?>%0D%0ASite: <?echo $companyShortName?>"><i
                        class="mdl-color-text--amber-300 material-icons"
                        role="presentation">live_help</i>Contact Support</a>
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
            <div class="mdl-cell--6-col mdl-cell mdl-cell--8-col-tablet mdl-card mdl-shadow--4dp">
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
                    <tbody id="selfAccountPrivilegesTable">
                    </tbody>
                </table>
            </div>
            <div id='changeOwnPasswdCard'
                 class="mdl-card mdl-shadow--4dp mdl-cell mdl-cell--6-col-desktop mdl-cell--8-col-tablet mdl-cell--4-col-phone">
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
                class="mdl-cell mdl-cell--12-col-desktop mdl-cell--8-col-tablet mdl-cell--4-col-phone">System Users</h2>
            <hr>
            <!--Inject user cards here-->
        </div>
    </main>
</div>
<div id="snackbar" class="mdl-js-snackbar mdl-snackbar">
    <div class="mdl-snackbar__text"></div>
    <button class="mdl-snackbar__action" type="button"></button>
</div>
<button id="addButton" onclick="showNewForm()"
        style="position: fixed; right: 24px; bottom: 24px; padding-top: 24px; margin-bottom: 0; z-index: 90; <?php echo($checkAllowed['isSystemAdministrator'] > 0 ? '' : "display: none;"); ?>"
        class="mdl-button mdl-shadow--8dp mdl-js-button mdl-button--fab mdl-js-ripple-effect mdl-button--colored mdl-color--yellow-300">
    <i class="material-icons">add</i>
</button>
<script src="scripts/material.min.js"></script>
<script src="scripts/jquery.min.js"></script>
<script>
    var installedPackapps;
    var privilegeBlob;
    var permissionsTable;
    $(document).ready(function () {
        //get self account info and server state
        installedPackapps = "<?echo addslashes(json_encode($installedPackapps))?>";
        privilegeBlob = "<?echo addslashes(json_encode($checkAllowed))?>";
        permissionsTable = "<?echo addslashes(json_encode($permissionstable))?>";
        privilegeBlob = JSON.parse(privilegeBlob);
        installedPackapps = JSON.parse(installedPackapps);
        permissionsTable = JSON.parse(permissionsTable);
        //create self account info box
        populateSelfInfoBox();

        if (<?echo($checkAllowed['isSystemAdministrator'] > 0 ? 'true' : 'false')?>) {
            setupUserCards();
        }
    });

    function setupUserCards() {
        //fetch users, create cards here
        $.getJSON('getUserAccountList.php', function (data) {
            var stringToInject = "";
            for (var user in data) {
                if(data.hasOwnProperty(user)){
                    stringToInject += "<div class='mdl-cell mdl-cell--4-col mdl-card mdl-shadow--4dp'"
                        + (data[user]['isDisabled'] > 0 ? "style='opacity: .5'" : '')
                        + "><div class='mdl-card__title mdl-color--yellow-300'><h2 class='mdl-card__title-text'>"
                        + data[user]['Real Name']
                        + "</h2></div><table style='width:100%;' class='mdl-card__supporting-text mdl-data-table mdl-js-data-table'><thead><tr><th class='mdl-data-table__cell--non-numeric'>App</th><th class='mdl-data-table__cell--non-numeric'>Status</th><th class='mdl-data-table__cell--non-numeric'>Access Level</th></tr></thead><tbody>"
                        + createUserCardAppRows(data[user])
                        + "</tbody></table><div style='margin-left: 17px;' class='mdl-card__subtitle-text'>User Name: "
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
            }
            $('#injectUsersHere').append(stringToInject);
            componentHandler.upgradeDom();
            //label access levels on usercards, plus attach listeners
            $('.enableSwitch').each(function (index) {
                //get info from switch, 0=packapp, 1=username, 2='switch'
                var curElemData = $(this).attr('name').split('_');
                if ($(this).is(':checked')) {
                    var level = $('#' + curElemData[1] + '_' + curElemData[0] + '_slider').val();
                    $('#' + curElemData[1] + '_' + curElemData[0] + '_accesslevel').html(getPrivilegeDescriptionHTML(curElemData[0], level));
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
                            $('#' + infoArray[1] + '_' + infoArray[0] + '_accesslevel').html(getPrivilegeDescriptionHTML(infoArray[0], 1));
                        } else {
                            $('#' + infoArray[1] + '_' + infoArray[0] + '_slider').val(1).attr('disabled', true);
                            $('#' + infoArray[1] + '_' + infoArray[0] + '_accesslevel').html("<span style='color: red'>Disabled</span>");
                        }
                    },
                    error: function () {
                        $('#' + infoArray[1] + '_' + infoArray[0] + '_accesslevel').html("<span style='color: red'>ERROR</span>");
                    }
                });
            });
            $('.slider').on('input', function () {
                var infoArray = $(this).attr('name').split('_');
                var numericLevel = $(this).val();
                $.ajax({
                    type: 'POST',
                    url: 'adjustUserPermissions.php',
                    data: {
                        'packapp': infoArray[1],
                        'userUnderEdit': infoArray[0],
                        'propUnderEdit': 'AccessLevel',
                        'propValue': numericLevel
                    },
                    success: function () {
                        $('#' + infoArray[0] + '_' + infoArray[1] + '_accesslevel').html(getPrivilegeDescriptionHTML(infoArray[1], numericLevel));
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
                }).fail(function(){
                    snack("Cannot disable the last admin user.", 8000);
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

    function createUserCardAppRows(userData){
        var userCardAppRowsToInject = "";
        for(var packapp in installedPackapps){
            userCardAppRowsToInject += "<tr><td class='mdl-data-table__cell--non-numeric'>"
            + capitalizeFirstLetter(installedPackapps[packapp]['short_app_name']) + "</td><td class='mdl-data-table__cell--non-numeric'><label class='mdl-switch mdl-js-switch mdl-js-ripple-effect' for='switch-"
            + installedPackapps[packapp]['short_app_name']+"-"
            + userData['username']
            + "'><input type='checkbox' id='switch-"+installedPackapps[packapp]['short_app_name']+"-"
            + userData['username']
            + "' name='"+installedPackapps[packapp]['short_app_name']+"_"
            + userData['username']
            + "_switch' class='enableSwitch mdl-switch__input' "
            + (userData['allowed'+capitalizeFirstLetter(installedPackapps[packapp]['short_app_name'])] > 0 ? 'checked' : '')
            + "></label></td><td style='padding-left:0; padding-right: 0'><p style='width:100%; margin-bottom: 0; text-align: center'><input class='slider mdl-slider mdl-js-slider' id='"
            + userData['username']
            + "_"+installedPackapps[packapp]['short_app_name']+"_slider' name='"
            + userData['username']
            + "_"+installedPackapps[packapp]['short_app_name']+"_slider' type='range' min='1' max='"
            + Object.keys(permissionsTable[installedPackapps[packapp]['short_app_name']]).length
            + "' value='"
            + userData[installedPackapps[packapp]['short_app_name']+'Role']
            + "'><div style='text-align: center' id='"
            + userData['username']
            + "_"+installedPackapps[packapp]['short_app_name']+"_accesslevel'></div></p></td></tr>";
        }
        return userCardAppRowsToInject;
    }

    function populateSelfInfoBox(){
        //create "Your account privileges rows"
        var stringToInsert = "";
        for(var packapp in installedPackapps){
            stringToInsert += "<tr><td class=\"mdl-data-table__cell--non-numeric\">"+installedPackapps[packapp]['long_app_name']+"</td><td id=\""+installedPackapps[packapp]['short_app_name']+"Enabled\" class=\"mdl-data-table__cell--non-numeric\"></td><td id=\""+installedPackapps[packapp]['short_app_name']+"Role\" class=\"mdl-data-table__cell--non-numeric\"></td></tr>";
        }
        $("#selfAccountPrivilegesTable").html(stringToInsert);

        //fill the rows
        for(var i = 0; i < installedPackapps.length; i++){
            var appAllowed = privilegeBlob['allowed'+capitalizeFirstLetter(installedPackapps[i]['short_app_name'])];
            if(appAllowed > 0){
                $('#'+installedPackapps[i]['short_app_name']+'Enabled').html("<span style='color: green'>Enabled</span>");
                $('#'+installedPackapps[i]['short_app_name']+'Role').html(getPrivilegeDescriptionHTML(installedPackapps[i]['short_app_name'], privilegeBlob[installedPackapps[i]['short_app_name']+'Role']));
            } else {
                $('#'+installedPackapps[i]['short_app_name']+'Enabled').html("<span style='color: red'>Disabled</span>");
                $('#'+installedPackapps[i]['short_app_name']+'Role').html(getPrivilegeDescriptionHTML(installedPackapps[i]['short_app_name'], 'Disabled'));
            }
        }
    }

    function snack(message, length) {
        var data = {
            message: message,
            timeout: length
        };
        document.querySelector('#snackbar').MaterialSnackbar.showSnackbar(data);
    }

    function getPrivilegeDescriptionHTML(packApp, level) {
        if (level == 'Disabled' || level == 0) {
            return "<span style='color: red'>Disabled</span>";
        }
        return "<span style='color: "+permissionsTable[packApp][level]['Color']+"'>"+permissionsTable[packApp][level]['Meaning']+"</span>";
    }

    function capitalizeFirstLetter(string) {
        return string.charAt(0).toUpperCase() + string.slice(1);
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