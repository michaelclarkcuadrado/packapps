<?php
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 1/19/18
 * Time: 1:36 PM
 */
require_once '../../../config.php';
$userinfo = packapps_authenticate_grower();
if(isset($_GET['deletedFlag']) && isset($_GET['PK'])){
    $growerCode = $userinfo['GrowerCode'];
    $PK = intval(mysqli_real_escape_string($mysqli, $_GET['PK']));
    $deletedFlag = 0;
    if($_GET['deletedFlag'] > 0){
        $deletedFlag = 1;
    }
    mysqli_query($mysqli, "
        UPDATE `grower_crop-estimates`
        JOIN grower_farms g ON `grower_crop-estimates`.farmID = g.farmID
        JOIN grower_GrowerLogins L ON g.growerID = L.GrowerID
        SET isDeleted = '$deletedFlag'
          WHERE PK = '$PK' AND GrowerCode = '$growerCode'
    ") or APIFail('Could not set block retirement.');
}