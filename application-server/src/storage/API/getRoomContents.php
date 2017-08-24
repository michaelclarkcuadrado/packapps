<?php
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 7/27/17
 * Time: 12:40 PM
 */
require '../../config.php';
packapps_authenticate_user('storage');

if(isset($_GET['room_id'])){
    $room_id = mysqli_real_escape_string($mysqli, $_GET['room_id']);
} else {
    $room_id = '%';
}
$roomContentsQueryResult = mysqli_query($mysqli, "SELECT
`grower_farms`.growerID,
  COALESCE(NULLIF(growerName, ''), '[No Grower]') AS growerName,
  grower_farms.farmID,
  COALESCE(NULLIF(farmName, ''), '[No Farm]') as farmName,
  PK as blockID,
  COALESCE(NULLIF(BlockDesc, ''), '[No Block]') as BlockDesc,
  VarietyID,
  COALESCE(NULLIF(VarietyName, ''), '[No Variety]') AS VarietyName,
  strain_ID,
  COALESCE(NULLIF(strainName, ''), '[No Strain]') AS strainName,
  SUM(bushelsInBin) AS bushels,
  IFNULL(storage_rooms.room_id, -1) AS room_id,
  COALESCE(NULLIF(IFNULL(storage_rooms.room_name, 'Unassigned'), ''), '[Unnamed Room]') AS room_name,
  isAvailable,
  CASE WHEN isAvailable > 0 THEN 'Open' ELSE 'Closed' END AS availability
FROM storage_grower_fruit_bins
  JOIN storage_grower_receipts
    ON storage_grower_fruit_bins.grower_receipt_id = storage_grower_receipts.id
  JOIN `grower_crop-estimates`
    ON storage_grower_receipts.grower_block = `grower_crop-estimates`.PK
  JOIN grower_farms
    ON `grower_crop-estimates`.farmID = grower_farms.farmID
  JOIN grower_strains
    ON `grower_crop-estimates`.strainID = grower_strains.strain_ID
  JOIN grower_varieties
    ON grower_strains.variety_ID = grower_varieties.VarietyID
  JOIN grower_commodities
    ON grower_varieties.commodityID = grower_commodities.commodity_ID
  JOIN grower_GrowerLogins
    ON grower_farms.growerID = grower_GrowerLogins.GrowerID
  LEFT JOIN storage_rooms
    ON storage_grower_fruit_bins.curRoom = storage_rooms.room_id
WHERE IFNULL(curRoom, '') LIKE '$room_id' AND storage_grower_fruit_bins.isFinished = 0
GROUP BY grower_farms.growerID, grower_farms.farmID, blockID, VarietyID, strain_ID
");

//Sort into hierarchical JSON

//Ordering is determined by an array of the names of the id fields, which match up to human-readable names in these tuples.
//These fields are then sorted into json to draw the sunburst graph.
//any changes must be reflected in getPivotLists.php
$fieldTuple = array(
    'growerID' => 'growerName',
    'farmID' => 'farmName',
    'blockID' => 'BlockDesc',
    'VarietyID' => 'VarietyName',
    'strain_ID' => 'strainName',
    'room_id' => 'room_name',
    'isAvailable' => 'availability'
);

//choose ordering to pivot on

//default order, with room if in all room view
$defaultOrdering = array('VarietyID', 'strain_ID', 'growerID', 'farmID', 'blockID');
if($room_id == '%'){
    array_unshift($defaultOrdering, 'room_id');
    array_unshift($defaultOrdering, 'isAvailable');
}

if(isset($_GET['ordering_Delivered'])){
    $decodedOrdering = json_decode($_GET['ordering_Delivered']);
    $ordering = $decodedOrdering;
} else {
    $ordering = $defaultOrdering;
}

//do the pivot
$output = array('name' => 'root', 'children' => array());
while($fruitRow = mysqli_fetch_assoc($roomContentsQueryResult)) {
    $array = &$output;
    foreach ($ordering as $fieldID) {
        $array = &$array['children'];
        $fieldName = $fieldTuple[$fieldID];
        if (!isset($array[$fruitRow[$fieldID]])){
            $desc = $fruitRow[$fieldName];
            if(substr($desc, 0, 1) == '[' && substr($desc, -1) == ']'){
                $color = '#969e9b'; //Hardcoded gray for unknown values
            } elseif($desc == 'Closed'){
                $color = '#FF4136'; //room is closed and red
            } elseif($desc == 'Open') {
                $color = '#282'; // room is open and green
            } else {
//                $color = dechex(crc32($fruitRow[$fieldName]));
//                $color = '#'.substr($color, 0, 6);
                $color = '#'.substr(md5($desc), 0, 6);
            }
            $array[$fruitRow[$fieldID]] = array('name' => $fruitRow[$fieldName], 'color' => $color, 'fieldID' => $fieldID, 'IDvalue' => $fruitRow[$fieldID], 'children' => array());
        }
        $array = &$array[$fruitRow[$fieldID]];
    }
    unset($array['children']);
    $array['size'] = $fruitRow['bushels'];
}

echo json_encode($output);