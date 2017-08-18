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
$checkAllowed = packapps_authenticate_user();
if ($checkAllowed['isSystemAdministrator'] == 0) {
    header($_SERVER['SERVER_PROTOCOL'] . '500 Internal Server Error', true, 500);
    die();
}
// end authentication

//disable user
if (isset($_GET['disableToggle'])) {
    $user = mysqli_real_escape_string($mysqli, $_GET['disableToggle']);

    //check if you are disabling the last admin account
    $checkIfDeletingAdmin = mysqli_query($mysqli, "SELECT isSystemAdministrator, isDisabled FROM packapps_master_users WHERE username = '$user'");
    $checkIfDeletingAdmin = mysqli_fetch_assoc($checkIfDeletingAdmin);
    if ($checkIfDeletingAdmin['isSystemAdministrator'] > 0 && $checkIfDeletingAdmin['isDisabled'] == 0) {
        $result = mysqli_query($mysqli, "SELECT username FROM packapps_master_users WHERE isDisabled=0 AND isSystemAdministrator=1");
        if (mysqli_num_rows($result) == 1) {
            header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
            die();
        }
    }
    mysqli_query($mysqli, "UPDATE packapps_master_users SET isDisabled = !isDisabled WHERE username = '$user'") or die(header($_SERVER['SERVER_PROTOCOL'] . '500 Internal Server Error', true, 500));
} else {

//change packapps permissions per user
    if (isset($_POST['packapp']) && isset($_POST['userUnderEdit']) && isset($_POST['propUnderEdit']) && isset($_POST['propValue'])) {
        $userUnderEdit = mysqli_real_escape_string($mysqli, $_POST['userUnderEdit']);
        $packapp = mysqli_real_escape_string($mysqli, $_POST['packapp']);
        $tablename = $packapp . "_UserData";
        $userData = mysqli_fetch_array(mysqli_query($mysqli, "SELECT * FROM packapps_master_users LEFT JOIN `$tablename` ON packapps_master_users.`username`=`$tablename`.UserName WHERE packapps_master_users.`username` = '$userUnderEdit'"));

        //check if user making edits is admin
        if ($checkAllowed['isSystemAdministrator'] > 0) {
            if ($_POST['propUnderEdit'] == 'Status') {
                //update permissions to enter app
                $packapp = ucfirst($packapp);
                $propValue = (mysqli_real_escape_string($mysqli, $_POST['propValue']) == 'true' ? '1' : '0');
                //unlock app in menu
                mysqli_query($mysqli, "UPDATE packapps_master_users SET allowed$packapp='$propValue' WHERE `username`='$userUnderEdit'") or die(header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500));
                //auto-grant lowest permissions
                //adjust for purchasing packapp with different permissions name
                mysqli_query($mysqli, "UPDATE $tablename SET Role = DEFAULT WHERE UserName='$userUnderEdit'") or die(mysqli_error($mysqli));
            } elseif ($_POST['propUnderEdit'] == 'AccessLevel') {
                //update internal app access level
                $propValue = mysqli_real_escape_string($mysqli, $_POST['propValue']);
                mysqli_query($mysqli, "UPDATE $tablename SET Role='$propValue' WHERE UserName='$userUnderEdit'") or die(header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500));
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