<?php
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 7/26/17
 * Time: 3:40 PM
 */
require '../../config.php';
packapps_authenticate_user('storage');

//get buildings
$buildings = mysqli_query($mysqli, 'SELECT building_id, building_name FROM storage_buildings');
$tempBuildings = array();
while($building = mysqli_fetch_assoc($buildings)){
    $tempBuildings[$building['building_id']] = array('building_name' => $building['building_name'], 'rooms' => array());
}
$buildings = $tempBuildings;

//get rooms
$rooms = mysqli_query($mysqli, "SELECT building, room_id, room_name, isAvailable, DATEDIFF(CURDATE(), lastAvailabilityChange) as lastAvailabilityChange FROM storage_rooms ORDER BY room_name");
while($room = mysqli_fetch_assoc($rooms)){
    $buildings[$room['building']]['rooms'][$room['room_id']] = $room;
}

echo json_encode($buildings);