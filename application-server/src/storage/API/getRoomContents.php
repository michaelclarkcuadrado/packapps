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
  growerName,
  grower_farms.farmID,
  farmName,
  PK as blockID,
  BlockDesc,
  VarietyID,
  VarietyName,
  strain_ID,
  strainName,
  SUM(bushelsInBin) AS bushels
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
WHERE curRoom LIKE '$room_id'
GROUP BY grower_farms.growerID, grower_farms.farmID, blockID, VarietyID, strain_ID
");

//Sort into hierarchical JSON

//Ordering is determined by an array of the names of the id fields, which match up to human-readable names in these tuples.
//These fields are then sorted into json to draw the sunburst graph.
$fieldTuple = array(
    'growerID' => 'growerName',
    'farmID' => 'farmName',
    'blockID' => 'BlockDesc',
    'VarietyID' => 'VarietyName',
    'strain_ID' => 'strainName'
);

//choose ordering to pivot on
$defaultOrdering = array('VarietyID', 'strain_ID', 'growerID', 'farmID', 'blockID');
if(isset($_GET['ordering'])){
    $decodedOrdering = json_decode($_GET['ordering']);
    //not so impressive input validation
    if(in_array('VarietyID', $decodedOrdering) &&
        in_array('strain_ID', $decodedOrdering) &&
        in_array('growerID', $decodedOrdering) &&
        in_array('farmID', $decodedOrdering) &&
        in_array('blockID', $decodedOrdering)){
        $ordering = $decodedOrdering;
    } else {
        $ordering = $defaultOrdering;
    }
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
        if (!isset($array[$fruitRow[$fieldID]])) {
            $array[$fruitRow[$fieldID]] = array('name' => $fruitRow[$fieldName], 'fieldID' => $fieldID, 'IDvalue' => $fruitRow[$fieldID], 'children' => array());
        }
        $array = &$array[$fruitRow[$fieldID]];
    }
    unset($array['children']);
    $array['size'] = $fruitRow['bushels'];
}

echo json_encode($output);