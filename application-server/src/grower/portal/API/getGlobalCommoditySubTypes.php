<?php
/**
 * Returns a json tree of Commodities -> Varieties -> Strains
 *
 * Created by PhpStorm.
 * User: mike
 * Date: 10/25/17
 * Time: 3:28 AM
 */
require_once '../../../config.php';
packapps_authenticate_grower();

//Get commodities
$commodityQuery = mysqli_query($mysqli, "SELECT commodity_ID, commodity_name FROM grower_commodities");
$commodities = array();
while ($commodityRow = mysqli_fetch_assoc($commodityQuery)){
    $commodityRow['Varieties'] = array();
    $commodities[$commodityRow['commodity_ID']] = $commodityRow;
}

//get Varieties
$varietyQuery = mysqli_query($mysqli, "SELECT VarietyID as id, VarietyName as text, commodityID FROM grower_varieties");
while($varietyRow = mysqli_fetch_assoc($varietyQuery)){
    $varietyRow['Strains'] = array();
    $commodities[$varietyRow['commodityID']]['Varieties'][$varietyRow['id']] = $varietyRow;
}

//get Strains

$strainQuery = mysqli_query($mysqli, "SELECT strain_ID as id, variety_ID, commodityID, strainName as text FROM grower_strains JOIN grower_varieties ON grower_strains.variety_ID = grower_varieties.VarietyID");
while($strainRow = mysqli_fetch_assoc($strainQuery)){
    $commodities[$strainRow['commodityID']]['Varieties'][$strainRow['variety_ID']]['Strains'][$strainRow['id']] = $strainRow;
}

//dump it
echo json_encode($commodities);