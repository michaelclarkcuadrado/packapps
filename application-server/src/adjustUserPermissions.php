<?php
/**
 * Created by PhpStorm.
 * User: Michael Clark-Cuadrado
 * Date: 8/3/2016
 * Time: 10:33 AM
 */
include 'config.php';
//this file responds to requests from controlPanel.php

//authentication
if (!isset($_COOKIE['auth']) || !isset($_COOKIE['username'])) {
    die("<script>window.location.replace('/')</script>");
} else if (!hash_equals($_COOKIE['auth'], crypt($_COOKIE['username'], $securityKey))) {
    die("<script>window.location.replace('/')</script>");
} else {
    $SecuredUserName = mysqli_real_escape_string($mysqli, $_COOKIE['username']);
    $checkAllowed = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT `Real Name`, isSystemAdministrator, purchasing_UserData.isAuthorizedForPurchases as purchasingRole, production_UserData.Role as productionRole, quality_UserData.Role as qualityRole, allowedProduction, allowedPurchasing, allowedQuality FROM packapps_master_users LEFT JOIN purchasing_UserData ON packapps_master_users.username=purchasing_UserData.Username LEFT JOIN quality_UserData ON packapps_master_users.username = quality_UserData.UserName LEFT JOIN production_UserData ON packapps_master_users.username = production_UserData.UserName WHERE packapps_master_users.username = '$SecuredUserName'"));
    //Only admins can edit
    if ($checkAllowed['isSystemAdministrator'] == 0) {
        header($_SERVER['SERVER_PROTOCOL'] . '500 Internal Server Error', true, 500);
        die();
    }
}
// end authentication

//disable user
if (isset($_GET['disableToggle'])) {
    $user = mysqli_real_escape_string($mysqli, $_GET['disableToggle']);
    mysqli_query($mysqli, "UPDATE packapps_master_users SET isDisabled = !isDisabled WHERE username = '$user'") or die(header($_SERVER['SERVER_PROTOCOL'] . '500 Internal Server Error', true, 500));
} else {

//change packapps permissions per user
    if (isset($_POST['packapp']) && isset($_POST['userUnderEdit']) && isset($_POST['propUnderEdit']) && isset($_POST['propValue'])) {
        $userUnderEdit = mysqli_real_escape_string($mysqli, $_POST['userUnderEdit']);
        $packapp = mysqli_real_escape_string($mysqli, $_POST['packapp']);
        $tablename = $packapp . "_UserData";
        $userData = mysqli_fetch_array(mysqli_query($mysqli, "SELECT * FROM packapps_master_users LEFT JOIN `$tablename` ON packapps_master_users.`username`=`$tablename`.UserName WHERE packapps_master_users.`username` = '$userUnderEdit'"));

        //check if user making edits is either the section manager or admin
        if ($checkAllowed['isSystemAdministrator'] > 0) {
            if ($_POST['propUnderEdit'] == 'Status') {
                //update permissions to enter app
                $packapp = ucfirst($packapp);
                $propValue = (mysqli_real_escape_string($mysqli, $_POST['propValue']) == 'true' ? '1' : '0');
                //unlock app in menu
                mysqli_query($mysqli, "UPDATE packapps_master_users SET allowed$packapp='$propValue' WHERE `username`='$userUnderEdit'") or die(header($_SERVER['SERVER_PROTOCOL'] . ' 502 Internal Server Error', true, 500));
                //auto-grant lowest permissions
                //adjust for purchasing packapp with different permissions name
                $columnName = ($packapp == 'Purchasing' ? 'isAuthorizedForPurchases' : 'Role');
                mysqli_query($mysqli, "UPDATE $tablename SET $columnName = DEFAULT WHERE UserName='$userUnderEdit'") or die(mysqli_error($mysqli));
            } elseif ($_POST['propUnderEdit'] == 'AccessLevel') {
                $columnName = ($packapp == 'purchasing' ? 'isAuthorizedForPurchases' : 'Role');
                //update internal app access level
                $propValue = mysqli_real_escape_string($mysqli, $_POST['propValue']);
                mysqli_query($mysqli, "UPDATE $tablename SET `$columnName`='$propValue' WHERE UserName='$userUnderEdit'") or die(header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500));
            } else {
                die ('error');
            }
        } else {
            header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
            die();
        }
    } else {
        header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
        die();
    }
}