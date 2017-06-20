<?php
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 6/20/17
 * Time: 12:59 PM
 */
include '../../config.php';

$userInfo = packapps_authenticate_user('production');
if($userInfo['isSystemAdministrator'] > 0){
    $lineNames = json_decode($_POST['lineNames']);
    foreach($lineNames as $id => $line){
        $id = mysqli_real_escape_string($mysqli, $id) + 1;
        $line = mysqli_real_escape_string($mysqli, $line);
        mysqli_query($mysqli, "UPDATE production_lineNames SET lineName = '".$line."' WHERE lineID = '".$id."'")
            or die(header('401 Unauthorized', true, 401));
    }
} else {
    die(header('401 Unauthorized', true, 401));
}

