<?php
/**
 * Created by PhpStorm.
 * User: Michael Clark-Cuadrado
 * Date: 7/27/2016
 * Time: 8:55 AM
 */
include 'config.php';

$userinfo = packapps_authenticate_user();
$SecuredUserName = $userinfo['username'];

//enumerate packapps
$packapps_query = mysqli_query($mysqli, "SELECT short_app_name, long_app_name, isEnabled, material_icon_name FROM packapps_appProperties");
$installedPackapps = array();
while($packapp = mysqli_fetch_assoc($packapps_query)){
    array_push($installedPackapps, $packapp);
}

//create CheckAllowed query
$checkAllowedQuery = "SELECT `isSystemAdministrator`";
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
$allowedItems = mysqli_fetch_assoc(mysqli_query($mysqli, $checkAllowedQuery." WHERE packapps_master_users.username = '".$SecuredUserName."'"));

$errormsg = "DEVELOPMENT ENVIRONMENT - DEVELOPMENT ENVIRONMENT - DEVELOPMENT ENVIRONMENT - DEVELOPMENT ENVIRONMENT";
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
<body class='mdl-color--primary-contrast mdl-layout__container' style="padding: 0">
<div class="mdl-layout mdl-js-layout" style="align-items: center;justify-content: center">
    <div class="mdl-layout__content" style="padding:24px;flex-grow:0">
        <div
                style='display: none; width: initial; max-width: 1150px'
                class="mdl-card mdl-color--primary mdl-shadow--8dp">
            <div class="mdl-card__title">
                <h2 style="color: white" class="mdl-card__title-text"><i style='margin-right: 5px' class="material-icons">dashboard</i> <?echo $companyName?> PackApps</h2>
            </div>
            <p style="margin: 0; text-align: center; color: #e91e63; font-weight: 900; font-size larger"><? echo $errormsg ?></p>
            <div style="text-align: center" class="mdl-grid mdl-card__supporting-text">
                <?php
                foreach($installedPackapps as $row){
                    //determine if app is locked to user or system
                    $allowed = false;
                    if($allowedItems['allowed'.ucfirst($row['short_app_name'])] > 0 && $row['isEnabled'] > 0){
                        $allowed = true;
                    }
                    echo "<button ".($allowed ? '' : 'disabled')." id='".$row['short_app_name']."button' onclick=\"location.href = '/".$row['short_app_name']."'\" class=\"mdl-button mdl-js-button mdl-color--pink-500 mdl-color-text--white mdl-js-ripple-effect mdl-shadow--6dp mdl-cell mdl-cell--4-col\" style=\"display: initial; height: 200px; float: left; border-radius: 12px; text-align: center; font-size: x-large; vertical-align: middle\"><i style=\"font-size:45px\" class=\"material-icons\">".$row['material_icon_name']."</i><br>".$row['long_app_name']."<p style='".($allowed ? 'display: none;' : '')." font-size: small;position: absolute; width: 100%; left: 0; color: white'>(This app is locked)</p></button>";
                }
                ?>
            </div>
            <div class='mdl-card__actions mdl-card__border'>
                <a onclick="logout()" class="mdl-button mdl-js-button mdl-js-ripple-effect">Log out</a>
                <a id="settings" href="controlPanel.php" class="mdl-button mdl-js-button mdl-js-ripple-effect">System Settings</a>
            </div>
        </div>
    </div>
</div>
<script src="scripts/material.min.js"></script>
<script src="scripts/jquery.min.js"></script>
<script>
    $(document).ready(function() {
        $('.mdl-card').fadeIn('slow');
    });

    function logout() {
        document.cookie = "auth=; expires=Thu, 01 Jan 1970 00:00:01 GMT;";
        document.cookie = "username=; expires=Thu, 01 Jan 1970 00:00:01 GMT;";
        document.cookie = "grower=; expires=Thu, 01 Jan 1970 00:00:01 GMT;";
        window.location.replace('/');
    }
</script>
<i style='position: absolute;cursor: pointer; right: 0; bottom:0;color: white; font-size: larger' class="material-icons mdl-cell--hide-phone" onclick="$(this).hide();$('#about').slideDown()">info_outline</i>
<div id="about" style="display: none;text-align: right;  position: fixed; right: 4px; bottom:0;color: white; font-size: smaller;">PackApps is powered by the <a style="color:white" target="_blank" href="https://packercloud.com">PackerCloud</a> Platform<br>Copyright 2015-<?echo date('Y')?>, PackerCloud LLC.</div></body>
</body>
</html>