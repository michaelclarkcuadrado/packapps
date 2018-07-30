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
//disable reporting of NOTICE errors, this script generates thousands of them per second
error_reporting(E_ALL & ~E_NOTICE);

$query = mysqli_query($mysqli, "
SELECT
  grower_farms.farmID,
  IFNULL(NULLIF(farmName, ''), '[UNNAMED FARM]') AS farmName,
  PK,
  IFNULL(NULLIF(BlockDesc, ''), '[UNNAMED BLOCK]') AS BlockDesc,
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
  WHERE ((year >= YEAR(NOW()) - 3) OR year IS NULL) AND grower_GrowerLogins.GrowerID = " . $userinfo['GrowerID'] . "
GROUP BY farmID, PK, year, value_type
ORDER BY isDeleted
");

$blockOrganizationTree = array();
$blocksBushelsDeliveredSummed = array(); //or bushelsReceived
while ($row = mysqli_fetch_assoc($query)) {
    if ($row['PK'] == null) {    //handle farm with no blocks
        $blockOrganizationTree['farms'][$row['farmID']] = array(
            'ID' => intval($row['farmID']),
            'name' => $row['farmName'],
            'commodities' => array(),
            'bushelsReceived' => 0
        );
    } else {
        //prevent bushelhistory getting overwritten
        $bushelHistory = $blockOrganizationTree['farms'][$row['farmID']]['commodities'][$row['commodity_ID']]['varieties'][$row['variety_ID']]['blocks'][$row['PK']]['bushelHistory'] ?: array();
        //Create tree's branch, set bushel history
        $year = $row['year'];
        $value_type = $row['value_type'];
        $bushel_value = $row['bushel_value'];
        unset($row['year'], $row['value_type'], $row['bushel_value']);
        $blockOrganizationTree['farms'][$row['farmID']]['commodities'][$row['commodity_ID']]['varieties'][$row['variety_ID']]['blocks'][$row['PK']] = $row;
        $blockOrganizationTree['farms'][$row['farmID']]['commodities'][$row['commodity_ID']]['varieties'][$row['variety_ID']]['blocks'][$row['PK']]['bushelHistory'] = $bushelHistory;
        $blockOrganizationTree['farms'][$row['farmID']]['commodities'][$row['commodity_ID']]['varieties'][$row['variety_ID']]['blocks'][$row['PK']]['bushelHistory'][$year][$value_type] = $bushel_value;

        //set branch names and IDs
        $blockOrganizationTree['farms'][$row['farmID']]['name'] = $row['farmName'];
        $blockOrganizationTree['farms'][$row['farmID']]['ID'] = intval($row['farmID']);
        $blockOrganizationTree['farms'][$row['farmID']]['commodities'][$row['commodity_ID']]['name'] = $row['commodity_name'];
        $blockOrganizationTree['farms'][$row['farmID']]['commodities'][$row['commodity_ID']]['ID'] = intval($row['commodity_ID']);
        $blockOrganizationTree['farms'][$row['farmID']]['commodities'][$row['commodity_ID']]['varieties'][$row['variety_ID']]['name'] = $row['VarietyName'];
        $blockOrganizationTree['farms'][$row['farmID']]['commodities'][$row['commodity_ID']]['varieties'][$row['variety_ID']]['ID'] = intval($row['variety_ID']);

        //sum bushels received for farms, commodities, and varieties.
        //Because multiple rows (that share a PK) have the same bushel delivery number repeated, only sum the
        // first encounter of a PK. Use $blocksBushelsDeliveredSummed map to keep track of which they are
        if (!array_key_exists($row['PK'], $blocksBushelsDeliveredSummed)) {
            $blockOrganizationTree['farms'][$row['farmID']]['bushelsReceived'] += $row['bushelsReceived'];
            $blockOrganizationTree['farms'][$row['farmID']]['commodities'][$row['commodity_ID']]['bushelsReceived'] += $row['bushelsReceived'];
            $blockOrganizationTree['farms'][$row['farmID']]['commodities'][$row['commodity_ID']]['varieties'][$row['variety_ID']]['bushelsReceived'] += $row['bushelsReceived'];
            $blocksBushelsDeliveredSummed[$row['PK']] = true;
        }
    }
}

//sum anticipated bushels and number of pending estimates
// an estimate is pending if the estimate is equal to last year's delivered (system default) and the 'sameAsLastYear' flag is not up
$curYear = date('Y');
foreach ($blockOrganizationTree['farms'] as $farmID => &$farmObj) {
    $farmEstimatesNeeded = 0;
    $farmBushelsAnticipated = 0;
    $numBlocksFarm = 0;
    foreach ($farmObj['commodities'] as $commodityID => &$commodityObj) {
        $commEstimatesNeeded = 0;
        $commBushelsAnticipated = 0;
        $numBlocksComm = 0;
        foreach ($commodityObj['varieties'] as $varietyID => &$varietyObj) {
            $varEstimatesNeeded = 0;
            $varBushelsAnticipated = 0;
            $numBlocksVar = 0;
            foreach ($varietyObj['blocks'] as $PK => &$blockObj) {
                if ($blockObj['isDeleted'] == 0) {
                    //count block quantities
                    $numBlocksFarm += 1;
                    $numBlocksComm += 1;
                    $numBlocksVar += 1;

                    //add estimates needed
                    $isConfirmedEstimate = ((count($blockObj['bushelHistory']) === 1) || ($blockObj['bushelHistory'][$curYear]['est'] !== $blockObj['bushelHistory'][$curYear - 1]['act']) || ($blockObj['isSameAsLastYear'] > 0));
                    if (!$isConfirmedEstimate) {
                        $farmEstimatesNeeded += 1;
                        $commEstimatesNeeded += 1;
                        $varEstimatesNeeded += 1;
                    }
                    //add bushels anticipated number -
                    if ($blockObj['isFinished'] > 0) {
                        $farmBushelsAnticipated += $blockObj['bushelsReceived'];
                        $commBushelsAnticipated += $blockObj['bushelsReceived'];
                        $varBushelsAnticipated += $blockObj['bushelsReceived'];
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
            }
            $varietyObj['blocks'] = array_values($varietyObj['blocks']);
            usort($varietyObj['blocks'], function ($obj1, $obj2) {
                if ($obj2['isDeleted'] == $obj1['isDeleted']) {
                    return $obj2['bushelsAnticipated'] - $obj1['bushelsAnticipated'];
                } else {
                    //push deleted objects to end of list
                    return $obj1['isDeleted'] - $obj2['isDeleted'];
                }
            });
            $varietyObj['estimatesNeeded'] = $varEstimatesNeeded;
            $varietyObj['bushelsAnticipated'] = $varBushelsAnticipated;
            $varietyObj['blockQuantity'] = $numBlocksVar;
        }
        $commodityObj['varieties'] = array_values($commodityObj['varieties']);
        usort($commodityObj['varieties'], function ($obj1, $obj2) {
            return $obj2['bushelsAnticipated'] - $obj1['bushelsAnticipated'];
        });
        $commodityObj['estimatesNeeded'] = $commEstimatesNeeded;
        $commodityObj['bushelsAnticipated'] = $commBushelsAnticipated;
        $commodityObj['blockQuantity'] = $numBlocksComm;
    }
    $farmObj['commodities'] = array_values($farmObj['commodities']);
    usort($farmObj['commodities'], function ($obj1, $obj2) {
        return $obj2['bushelsAnticipated'] - $obj1['bushelsAnticipated'];
    });
    $farmObj['estimatesNeeded'] = $farmEstimatesNeeded;
    $farmObj['bushelsAnticipated'] = $farmBushelsAnticipated;
    $farmObj['blockQuantity'] = $numBlocksFarm;
}
$blockOrganizationTree['farms'] = array_values($blockOrganizationTree['farms']);
usort($blockOrganizationTree['farms'], function ($obj1, $obj2) {
    return $obj2['bushelsAnticipated'] - $obj1['bushelsAnticipated'];
});

echo json_encode($blockOrganizationTree);
