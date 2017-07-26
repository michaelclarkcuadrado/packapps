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
$rooms = mysqli_query($mysqli, "SELECT building, room_id, room_name FROM storage_rooms");
while($room = mysqli_fetch_assoc($rooms)){

}