<?php
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 9/21/17
 * Time: 6:55 AM
 */
require_once '../../../config.php';
$userdata = packapps_authenticate_grower();

$querydata = mysqli_query($mysqli, "
SELECT
  PK,
  IFNULL(NULLIF(farmName, ''), '[Unnamed]') AS farmName,
  IFNULL(NULLIF(BlockDesc, ''), '[Unnamed]') AS BlockDesc,
  IFNULL(NULLIF(VarietyName, ''), '[Unnamed]') AS VarietyName,
  commodity_name,
  IFNULL(NULLIF(strainName, ''), '[Unnamed]') AS strainName,
  IFNULL(SUM(bushelsInBin), 0)                                                                AS totalReceivedBushels,
  bushel_value                                                                                AS bushelEstimate,
  ifnull(round(((sum(bushelsInBin) / grower_block_bushel_history.bushel_value) * 100), 2), 0) AS percentDelivered,
  `grower_crop-estimates`.isFinished
FROM `grower_crop-estimates`
  JOIN grower_block_bushel_history ON `grower_crop-estimates`.PK = grower_block_bushel_history.block_PK
  JOIN grower_farms ON `grower_crop-estimates`.farmID = grower_farms.farmID
  JOIN grower_strains ON `grower_crop-estimates`.strainID = grower_strains.strain_ID
  JOIN grower_varieties ON grower_strains.variety_ID = grower_varieties.VarietyID
  JOIN grower_commodities ON grower_varieties.commodityID = grower_commodities.commodity_ID
  JOIN grower_GrowerLogins ON grower_farms.growerID = grower_GrowerLogins.GrowerID
  LEFT JOIN storage_grower_receipts ON `grower_crop-estimates`.PK = storage_grower_receipts.grower_block
  LEFT JOIN storage_grower_fruit_bins ON storage_grower_receipts.id = storage_grower_fruit_bins.grower_receipt_id
WHERE grower_block_bushel_history.year = ".date('Y')."
      AND isDeleted = 0 AND GrowerCode = '". $userdata['GrowerCode'] ."'
GROUP BY PK
ORDER BY isFinished, percentDelivered DESC, commodity_name, farmName
");

$output = array();
while($row = mysqli_fetch_assoc($querydata)){
    $row['isFinished'] = (int)$row['isFinished'];
    $output[$row['PK']] = $row;
}
header('Content-type: application/json');
echo json_encode($output);