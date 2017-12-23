<?php
/**
 * Created by PhpStorm.
 * User: MAC
 * Date: 5/11/2015
 * Time: 12:36 PM
 *
 * Fetch all blocks from grower, with historical deliveries and put into csv
 */
include '../../config.php';
$userinfo = packapps_authenticate_grower();
$blockdata = mysqli_query($mysqli, "
SELECT
  `grower_gfbvs-listing`.PK AS PackApps_ID,
  commodity_name AS `Commodity`,
  `grower_gfbvs-listing`.farmName AS `Farm`,
  `grower_gfbvs-listing`.BlockDesc AS `Block`,
  VarietyName AS `Variety`,
  strainName AS `Strain`,
  year AS `Bushel Year`,
  bushel_value AS `Bushels`
FROM `grower_gfbvs-listing`
  JOIN `grower_crop-estimates` ON `grower_gfbvs-listing`.PK = `grower_crop-estimates`.PK
  JOIN `grower_block_bushel_history` ON block_PK  = `grower_crop-estimates`.PK
  JOIN grower_farms ON `grower_crop-estimates`.farmID = grower_farms.farmID
  JOIN grower_GrowerLogins ON grower_farms.growerID = grower_GrowerLogins.GrowerID
  WHERE ( isDeleted = 0 AND (value_type = 'act'
    OR (value_type = 'est' AND `year` = '" . date('Y') ."'))
    AND GrowerCode = '" . $userinfo['GrowerCode'] . "')
");
$blockdata_years_pivoted = array();
$earliestYear = PHP_INT_MAX;
//collapse all year histories into one row per block
while($row = mysqli_fetch_assoc($blockdata)){
    if($row['Bushel Year'] < $earliestYear){
        $earliestYear = $row['Bushel Year'];
    }
    if(isset($blockdata_years_pivoted[$row['PackApps_ID'] ])){
        $blockdata_years_pivoted[$row['PackApps_ID']][$row['Bushel Year']] = $row['Bushels'];
    } else {
        $row[$row['Bushel Year']] = $row['Bushels'];
        unset($row['Bushel Year'], $row['Bushels']);
        $blockdata_years_pivoted[$row['PackApps_ID']] = $row;
    }
}
//account for blocks with different length histories
foreach($blockdata_years_pivoted as $index => $row){
    if(!isset($row['earliestYear'])){
        for($i = $earliestYear; $i <= date('Y'); $i++){
            if(isset($row[$i])){
                break;
            } else {
                $blockdata_years_pivoted[$index][$i] = "0";
            }
        }
    }
}
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=Orchard_Report_' . $userinfo['GrowerName'] . '_' . date("m-d-Y") . '.csv');
$output = fopen('php://output', 'w');
//set column headers
$headers = array('PackApps_ID', 'Commodity', 'Farm', 'Block', 'Variety', 'Strain');
for($i = $earliestYear; $i <= date('Y'); $i++){
    array_push($headers, $i . " Output");
};
// TODO - If blocks have different history lengths, the order is incorrect. FIXME
fputcsv($output, $headers);
foreach($blockdata_years_pivoted as $row){
    fputcsv($output, $row);
}