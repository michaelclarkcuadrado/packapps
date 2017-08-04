<?php
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 8/2/17
 * Time: 11:04 AM
 */
require '../../config.php';
packapps_authenticate_user('storage');

if(isset($_GET['room_id'])){
    $room_id = mysqli_real_escape_string($mysqli, $_GET['room_id']);
} else {
    $room_id = '%';
}

$pivotLists = array();
//Enumerate lists

//grower-received fruit
$growerFruitFieldNames = array(
    'VarietyID' => 'Variety',
    'strain_ID' => 'Strain',
    'growerID' => 'Grower',
    'farmID' => 'Farm',
    'blockID' => 'Block',
);
if($room_id == '%'){
    $growerFruitFieldNames = array('room_id' => 'Room') + $growerFruitFieldNames;
}


//TODO 'non-grower' fruit, this will require DB queries and nonsense

//add lists under human-readable titles
$pivotLists['Delivered'] = $growerFruitFieldNames;


echo json_encode($pivotLists);