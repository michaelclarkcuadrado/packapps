<?php
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 12/23/17
 * Time: 6:21 PM
 */
require_once '../../../config.php';
$userinfo = packapps_authenticate_grower();

if(isset($_GET['newFarmName']) && $_GET['newFarmName'] !== ""){
    $farmName = mysqli_real_escape_string($mysqli, $_GET['newFarmName']);
    mysqli_query($mysqli, "
    INSERT INTO grower_farms (growerID, farmName)
    VALUES ('".$userinfo['GrowerID']."', '$farmName')
    ");
    echo json_encode(array('ID' => mysqli_insert_id($mysqli)));
} else {
    APIFail("Invalid farm name");
}