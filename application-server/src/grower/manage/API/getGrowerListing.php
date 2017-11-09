<?php
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 10/31/17
 * Time: 10:27 AM
 */
require_once '../../../config.php';
packapps_authenticate_user('grower');
$growerListing = mysqli_query($mysqli, "
SELECT
  growerName,
  GrowerCode,
  IFNULL(UNIX_TIMESTAMP(lastLogin), 0) AS lastLogin,
  IFNULL(login_email, 'Not Yet Set') as login_email
FROM grower_GrowerLogins
");

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

$output = array();
//turn growerListing into an array
while($growerRow = mysqli_fetch_assoc($growerListing)){
    $growerRow['isMetaDataOpen'] = false;
    $growerRow['bushelEstimates'] = array();
    $output[$growerRow['GrowerCode']] = $growerRow;
}

//add bushelsByVariety
while($bushelRow = mysqli_fetch_assoc($growerBushelsByVarietyThisYear)){
    $output[$bushelRow['GrowerCode']]['bushelEstimates'][$bushelRow['VarietyName']] = $bushelRow['bushels'];
}

echo json_encode($output);