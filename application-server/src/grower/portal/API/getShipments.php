<?php
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 9/19/17
 * Time: 4:30 AM
 */

require_once '../../../config.php';
$userinfo = packapps_authenticate_grower();


$querydata = mysqli_query($mysqli, "
SELECT
  id AS delivery_ID,
  external_reference_num,
  bins_quantity,
  date,
  CASE WHEN receiptNum IS NULL THEN FALSE ELSE TRUE END AS isQATested,
  SUM(bushelsInBin) AS bushelsTotal,
  IFNULL(NULLIF(BlockDesc, ''), '[Unnamed]') AS BlockDesc,
  isDeleted AS BlockIsDeleted,
  commodity_name,
  IFNULL(NULLIF(strainName, ''), '[Unnamed]') AS strainName,
  IFNULL(NULLIF(VarietyName, ''), '[Unnamed]') AS VarietyName,
  IFNULL(NULLIF(farmName, ''), '[Unnamed]') AS farmName
FROM storage_grower_receipts
LEFT JOIN quality_InspectedRTs ON storage_grower_receipts.id = quality_InspectedRTs.receiptNum
JOIN storage_grower_fruit_bins ON storage_grower_receipts.id = storage_grower_fruit_bins.grower_receipt_id
JOIN `grower_crop-estimates` ON storage_grower_receipts.grower_block = `grower_crop-estimates`.PK
JOIN grower_strains ON `grower_crop-estimates`.strainID = grower_strains.strain_ID
JOIN grower_varieties ON grower_strains.variety_ID = grower_varieties.VarietyID
JOIN grower_commodities ON grower_varieties.commodityID = grower_commodities.commodity_ID
JOIN grower_farms ON `grower_crop-estimates`.farmID = grower_farms.farmID
JOIN grower_GrowerLogins ON grower_farms.growerID = grower_GrowerLogins.GrowerID
WHERE GrowerCode = '" . $userinfo['GrowerCode'] . "'
    OR GrowerCode LIKE '%'
GROUP BY delivery_ID
ORDER BY date
LIMIT 75
");

$output = array();
while($row = mysqli_fetch_assoc($querydata)){
    array_push($output, $row);
}
header('Content-type: application/json');
echo json_encode($output);