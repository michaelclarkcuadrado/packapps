<?php
/**
 * Created by PhpStorm.
 * User: mac
 * Date: 8/18/2016
 * Time: 1:55 PM
 */
include '../../config.php';
if(isset($_GET['q'])){
    $searchQuery = mysqli_real_escape_string($mysqli, $_GET['q']);
} else {
    $searchQuery = '';
}
$itemquery = mysqli_query($mysqli, "SELECT Item_ID as id, concat(Type_Description, ': ', ItemDesc) as text FROM purchasing_Items JOIN purchasing_ItemTypes ON purchasing_Items.Type_ID = purchasing_ItemTypes.Type_ID WHERE ItemDesc LIKE '%$searchQuery%'");
$returnArray = array();
while($item = mysqli_fetch_assoc($itemquery)){

    array_push($returnArray, $item);
}
echo json_encode($returnArray);