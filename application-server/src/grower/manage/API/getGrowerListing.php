<?php
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 10/31/17
 * Time: 10:27 AM
 */
require_once '../../../config.php';
packapps_authenticate_user('grower');

//get initial listing
$growerListing = mysqli_query($mysqli, "
SELECT
  growerName,
  GrowerCode,
  IFNULL(UNIX_TIMESTAMP(lastLogin), 0) AS lastLogin,
  IFNULL(login_email, 'Not Yet Set') as login_email
FROM grower_GrowerLogins
");

//get current year variety estimates
$growerBushelsByVarietyThisYear = mysqli_query($mysqli, "
SELECT
  GrowerCode,
  VarietyName,
  SUM(bushel_value) as bushels
FROM grower_GrowerLogins
  JOIN grower_farms ON grower_GrowerLogins.GrowerID = grower_farms.growerID
  JOIN `grower_crop-estimates` ON grower_farms.farmID = `grower_crop-estimates`.farmID
JOIN grower_block_bushel_history ON `grower_crop-estimates`.PK = grower_block_bushel_history.block_PK
JOIN grower_strains ON `grower_crop-estimates`.strainID = grower_strains.strain_ID
JOIN grower_varieties ON grower_strains.variety_ID = grower_varieties.VarietyID
WHERE ((isFinished > 0 AND value_type = 'act') OR (isFinished = 0 AND value_type = 'est')) AND year = YEAR(NOW())
GROUP BY GrowerCode, VarietyName
ORDER BY bushels DESC
");

//get historical growth data
$growerTotalOutputGrowth = mysqli_query($mysqli, "
SELECT
  GrowerCode,
  year,
  SUM(bushel_value) as bushels,
  SUM(bushel_value)/sumTable.sum * 100 as yearPercent /*Year percent is ONLYYYY valid for current year. Other rows have trash data*/
FROM grower_GrowerLogins
  JOIN grower_farms ON grower_GrowerLogins.GrowerID = grower_farms.growerID
  JOIN `grower_crop-estimates` ON grower_farms.farmID = `grower_crop-estimates`.farmID
  JOIN grower_block_bushel_history ON `grower_crop-estimates`.PK = grower_block_bushel_history.block_PK
  CROSS JOIN (
               SELECT SUM(bushel_value) as sum
               FROM grower_block_bushel_history
                 JOIN `grower_crop-estimates` ON grower_block_bushel_history.block_PK = `grower_crop-estimates`.PK
                WHERE ((isFinished > 0 AND value_type = 'act') OR (isFinished = 0 AND value_type = 'est')) AND year = YEAR(NOW())
             ) sumTable
WHERE (((isFinished > 0 AND value_type = 'act') OR (isFinished = 0 AND value_type = 'est')) AND year = YEAR(NOW()) OR (value_type = 'act' AND year != YEAR(NOW())))
GROUP BY GrowerCode, year
");

$output = array();
//turn growerListing into an array
while($growerRow = mysqli_fetch_assoc($growerListing)){
    $growerRow['isMetaDataOpen'] = false;
    $growerRow['bushelEstimates'] = array();
    $output[$growerRow['GrowerCode']] = $growerRow;
}

//add bushelsByVariety
while($bushelRow = mysqli_fetch_assoc($growerBushelsByVarietyThisYear)){
    $output[$bushelRow['GrowerCode']]['bushelEstimates'][$bushelRow['VarietyName']]['value'] = $bushelRow['bushels'];
    $output[$bushelRow['GrowerCode']]['bushelEstimates'][$bushelRow['VarietyName']]['color'] = '#'.substr(md5($bushelRow['VarietyName']), 0, 6);
}

//add growth history
while($growthRow = mysqli_fetch_assoc($growerTotalOutputGrowth)){
    $output[$growthRow['GrowerCode']]['growthHistory'][$growthRow['year']] = $growthRow['bushels'];
    if($growthRow['year'] == date('Y')){
        $output[$growthRow['GrowerCode']]['percentOfThisYear'] = $growthRow['yearPercent'];
    }
}

echo json_encode($output);