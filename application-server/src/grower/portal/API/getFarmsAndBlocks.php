<?php
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 10/26/17
 * Time: 9:49 AM
 */

require_once '../../../config.php';
$userinfo = packapps_authenticate_grower();

$curYear = date('Y');
//get List of all blocks with bushel history
$query = mysqli_query($mysqli, "
SELECT
  PK,
  GrowerCode,
  VarietyName,
  farmName,
  grower_farms.farmID
  BlockDesc,
  strainName,
  CONCAT(grower_block_bushel_history.year,  grower_block_bushel_history.value_type) AS year,
  grower_block_bushel_history.bushel_value
FROM `grower_crop-estimates`
JOIN grower_strains ON `grower_crop-estimates`.strainID = grower_strains.strain_ID
JOIN grower_varieties ON grower_strains.variety_ID = grower_varieties.VarietyID
JOIN grower_farms ON `grower_crop-estimates`.farmID = grower_farms.farmID
JOIN grower_GrowerLogins ON grower_farms.growerID = grower_GrowerLogins.GrowerID
 LEFT JOIN grower_block_bushel_history ON `grower_crop-estimates`.PK = grower_block_bushel_history.block_PK
WHERE GrowerCode = '" . $userinfo['GrowerCode'] . "' AND grower_block_bushel_history.year >= ($curYear - 3)
");

$blocks = array();
/*
 * Page needs:
 * Current year's estimate
 * Year -1 Actual and estimate
 * Year -2 Actual
 * Year -3 Actual
 */
while ($blockRow = mysqli_fetch_assoc($query)) {
    if (isset($blocks[$blockRow['PK']])){
        $blocks[$blockRow['PK']][$blockRow['year']] = $blockRow['bushel_value'];
    } else {
        $blockRow[$blockRow['year']] = $blockRow['bushel_value'];
        unset($blockRow['year'], $blockRow['bushel_value']);
        $blocks[$blockRow['PK']] = $blockRow;
    }
}

echo json_encode($blocks);