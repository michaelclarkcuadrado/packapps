<?php
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 8/2/17
 * Time: 11:04 AM
 */
require '../../config.php';
packapps_authenticate_user('storage');

$pivotLists = array();

//grower-received fruit
$growerFruitFieldNames = array(
    'VarietyID' => 'Variety',
    'strain_ID' => 'Strain',
    'growerID' => 'Grower',
    'farmID' => 'Farm',
    'blockID' => 'Block'
);

$pivotLists['Delivered'] = $growerFruitFieldNames;

//TODO 'non-grower' fruit, this will require DB queries and nonsense

echo json_encode($pivotLists);