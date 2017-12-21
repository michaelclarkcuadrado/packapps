<?php
/**
 * Returns a JSON object of all blocks with their data, organized in a tree,
 * FARM > COMMODITY > VARIETY > BLOCK
 *
 * Created by PhpStorm.
 * User: mike
 * Date: 10/26/17
 * Time: 9:49 AM
 */

/*
 * Page needs:
 * Current year's estimate
 * isSameAsLastYear
 * Year -1 Actual and estimate
 * Year -2 Actual
 * Year -3 Actual
 * number of deliveries
 * total bushels delivered
 * Farm, block, variety, strain
 */
require_once '../../../config.php';
$userinfo = packapps_authenticate_grower();
$query = mysqli_query($mysqli, "
SELECT
  `gc-e`.farmID,
  IFNULL(NULLIF(farmName, ''), 'UNNAMED FARM') AS farmName,
  PK,
  IFNULL(NULLIF(BlockDesc, ''), 'UNNAMED BLOCK') AS BlockDesc,
  variety_ID,
  VarietyName,
  commodity_ID,
  commodity_name,
  strainID,
  strainName,
  isDeleted,
  isSameAsLastYear,
  `gc-e`.isFinished,
  year,
  value_type,
  bushel_value,
  IFNULL(SUM(bushelsInBin), 0) AS bushelsReceived,
  COUNT(DISTINCT receipt.id) AS deliveriesReceived
FROM
  grower_farms
  LEFT JOIN grower_GrowerLogins ON grower_farms.growerID = grower_GrowerLogins.GrowerID
LEFT JOIN `grower_crop-estimates` `gc-e` ON grower_farms.farmID = `gc-e`.farmID
LEFT JOIN grower_block_bushel_history h ON `gc-e`.PK = h.block_PK
LEFT JOIN grower_strains strain ON `gc-e`.strainID = strain.strain_ID
LEFT JOIN grower_varieties v ON strain.variety_ID = v.VarietyID
LEFT JOIN grower_commodities gc ON v.commodityID = gc.commodity_ID
LEFT JOIN storage_grower_receipts receipt ON `gc-e`.PK = receipt.grower_block
LEFT JOIN storage_grower_fruit_bins sgfb ON receipt.id = sgfb.grower_receipt_id
  WHERE (year >= YEAR(NOW()) - 3) AND grower_GrowerLogins.GrowerID = " . $userinfo['GrowerID'] . "
GROUP BY PK, year, value_type
ORDER BY isDeleted
");

$blockOrganizationTree = array();
$blocksBushelsExpectedSummed = array();
$blocksBushelsDeliveredSummed = array(); //or bushelsReceived
while ($row = mysqli_fetch_assoc($query)) {
    //prevent bushelhistory getting overwritten
    $bushelHistory = $blockOrganizationTree['farms'][$row['farmID']]['commodities'][$row['commodity_ID']]['varieties'][$row['variety_ID']][$row['PK']]['bushelHistory'] ?: array();
    //Create tree's branch, set bushel history
    $year = $row['year'];
    $value_type = $row['value_type'];
    $bushel_value = $row['bushel_value'];
    unset($row['year'], $row['value_type'], $row['bushel_value']);
    $blockOrganizationTree['farms'][$row['farmID']]['commodities'][$row['commodity_ID']]['varieties'][$row['variety_ID']][$row['PK']] = $row;
    $blockOrganizationTree['farms'][$row['farmID']]['commodities'][$row['commodity_ID']]['varieties'][$row['variety_ID']][$row['PK']]['bushelHistory'] = $bushelHistory;
    $blockOrganizationTree['farms'][$row['farmID']]['commodities'][$row['commodity_ID']]['varieties'][$row['variety_ID']][$row['PK']]['bushelHistory'][$year][$value_type] = $bushel_value;

    //set branch names
    $blockOrganizationTree['farms'][$row['farmID']]['name'] = $row['farmName'];
    $blockOrganizationTree['farms'][$row['farmID']]['commodities'][$row['commodity_ID']]['name'] = $row['commodity_name'];
    $blockOrganizationTree['farms'][$row['farmID']]['commodities'][$row['commodity_ID']]['varieties'][$row['variety_ID']]['name'] = $row['VarietyName'];


    if (!array_key_exists($row['PK'], $blocksBushelsDeliveredSummed)) {
        $blockOrganizationTree['farms'][$row['farmID']]['bushelsReceived'] += $row['bushelsReceived'];
        $blockOrganizationTree['farms'][$row['farmID']]['commodities'][$row['commodity_ID']]['bushelsReceived'] += $row['bushelsReceived'];
        $blockOrganizationTree['farms'][$row['farmID']]['commodities'][$row['commodity_ID']]['varieties'][$row['variety_ID']]['bushelsReceived'] += $row['bushelsReceived'];
        $blocksBushelsDeliveredSummed[$row['PK']] = true;
    }
}
$curYear = date('Y');
foreach($blockOrganizationTree['farms'] as $farmID => &$farmObj){
    $farmEstimatesNeeded = 0;
    $farmBushelsAnticipated = 0;
    foreach($farmObj['commodities'] as $commodityID => &$commodityObj){
        $commEstimatesNeeded = 0;
        $commBushelsAnticipated = 0;
        foreach($commodityObj['varieties'] as $varietyID => &$varietyObj){
            $varEstimatesNeeded = 0;
            $varBushelsAnticipated = 0;
            foreach($varietyObj as $PK => $blockObj){
                //add estimates needed
                $isConfirmedEstimate = ($blockObj['bushelHistory'][$curYear]['est'] == $blockObj['bushelHistory'][$curYear - 1]['act']) || ($blockObj['isSameAsLastYear'] > 0);
                if(!$isConfirmedEstimate){
                    $farmEstimatesNeeded += 1;
                    $commEstimatesNeeded += 1;
                    $varEstimatesNeeded += 1;
                }
                //add bushels anticipated number -
                if($blockObj['isFinished'] > 0){
                    $farmBushelsAnticipated += $blockObj['deliveriesReceived'];
                    $commBushelsAnticipated += $blockObj['deliveriesReceived'];
                    $varBushelsAnticipated += $blockObj['deliveriesReceived'];
                } elseif ($isConfirmedEstimate) {
                    $farmBushelsAnticipated += $blockObj['bushelHistory'][$curYear]['est'] ?: 0;
                    $commBushelsAnticipated += $blockObj['bushelHistory'][$curYear]['est'] ?: 0;
                    $varBushelsAnticipated += $blockObj['bushelHistory'][$curYear]['est'] ?: 0;
                } else {
                    $farmBushelsAnticipated += $blockObj['bushelHistory'][$curYear - 1]['act'] ?: 0;
                    $commBushelsAnticipated += $blockObj['bushelHistory'][$curYear - 1]['act'] ?: 0;
                    $varBushelsAnticipated += $blockObj['bushelHistory'][$curYear - 1]['act'] ?: 0;
                }
            }
            $varietyObj['estimatesNeeded'] = $varEstimatesNeeded;
            $varietyObj['bushelsAnticipated'] = $varBushelsAnticipated;
        }
        $commodityObj['estimatesNeeded'] = $commEstimatesNeeded;
        $commodityObj['bushelsAnticipated'] = $commBushelsAnticipated;
    }
    $farmObj['estimatesNeeded'] = $farmEstimatesNeeded;
    $farmObj['bushelsAnticipated'] = $farmBushelsAnticipated;
}

echo json_encode($blockOrganizationTree);