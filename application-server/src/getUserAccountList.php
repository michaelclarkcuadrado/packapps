<?php
/**
 * Created by PhpStorm.
 * User: Michael Clark-Cuadrado
 * Date: 8/5/2016
 * Time: 2:35 PM
 */
//authentication
include 'config.php';
if (!isset($_COOKIE['auth']) || !isset($_COOKIE['username'])) {
    die("<script>window.location.replace('/')</script>");
} else if (!hash_equals($_COOKIE['auth'], crypt($_COOKIE['username'], $securityKey))) {
    die("<script>window.location.replace('/')</script>");
} else {
    $SecuredUserName = mysqli_real_escape_string($mysqli, $_COOKIE['username']);
    $checkAllowed = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT `Real Name`, username, isSystemAdministrator FROM packapps_master_users WHERE `username` = '$SecuredUserName'"));
    if ($checkAllowed['isSystemAdministrator'] == 0) {
        header($_SERVER['SERVER_PROTOCOL'] . ' 403 FORBIDDEN', true, 500);
        die();
    } else {
        // end authentication
        $userPrivileges = mysqli_query($mysqli, "SELECT concat(`Real Name`, CASE WHEN isSystemAdministrator > 0 THEN ' **' ELSE '' END) as `Real Name`,packapps_master_users.username, ifnull(DATE_FORMAT(lastLogin,'%b %d %Y %h:%i %p'), 'Never') as lastLogin, isSystemAdministrator, isDisabled, purchasing_UserData.Role as purchasingRole, production_UserData.Role as productionRole, quality_UserData.Role as qualityRole, maintenance_UserData.Role as maintenanceRole, storage_UserData.Role as storageRole, allowedProduction, allowedPurchasing, allowedQuality, allowedMaintenance, allowedStorage FROM packapps_master_users LEFT JOIN purchasing_UserData ON packapps_master_users.username=purchasing_UserData.Username LEFT JOIN quality_UserData ON packapps_master_users.username = quality_UserData.UserName LEFT JOIN production_UserData ON packapps_master_users.username = production_UserData.UserName LEFT JOIN maintenance_UserData ON packapps_master_users.username = maintenance_UserData.username LEFT JOIN storage_UserData ON packapps_master_users.username = storage_UserData.username");
        $arrayToReturn = array();
        while ($user = mysqli_fetch_assoc($userPrivileges)){
            //convert purchasing
            $user['purchasingRole'] += 1;
            //convert production
            if($user['productionRole'] == 'Production') {
                $user['productionRole'] = 2;
            } else if ($user['productionRole'] == 'ReadOnly'){
                $user['productionRole'] = 1;
            } else {
                $user['productionRole'] = 1;
            }
            //convert quality
            if ($user['qualityRole'] == 'QA') {
                $user['qualityRole'] = 3;
            } elseif ($user['qualityRole'] == 'INS') {
                $user['qualityRole'] = 2;
            } elseif ($user['qualityRole'] == 'Weight') {
                $user['qualityRole'] = 1;
            } else {
                $user['qualityRole'] = 1;
            }
            //convert maintenance
            if ($user['maintenanceRole' == 'readwrite']){
                $user['maintenanceRole'] = 3;
            } elseif ($user['maintenanceRole' == 'worker']) {
                $user['maintenanceRole'] = 2;
            } else {
                $user['maintenanceRole'] = 1;
            }
            //convert storage
            if ($user['storageRole'] == 'full'){
                $user['storageRole'] = 2;
            } else {
                $user['storageRole'] = 1;
            }
            array_push($arrayToReturn, $user);
        }
        echo json_encode($arrayToReturn);
    }
}
