<?php
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 7/10/17
 * Time: 10:12 AM
 */
require '../../config.php';
$userinfo = packapps_authenticate_user('maintenance');
if($userinfo['permissionLevel'] > 2){
    $maintenanceusers = mysqli_query($mysqli, "SELECT username, `Real Name` FROM packapps_master_users WHERE allowedMaintenance = 1");
    $outputUsers = array();
    while($user = mysqli_fetch_assoc($maintenanceusers)){
        array_push($outputUsers, array('id' => $user['username'], 'text' => $user['Real Name']));
    }
    echo json_encode($outputUsers);
} else {
    die(header($_SERVER['SERVER_PROTOCOL'] . ' 403 UNAUTHORIZED', true, 403));
}