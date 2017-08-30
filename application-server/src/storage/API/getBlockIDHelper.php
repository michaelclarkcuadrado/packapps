<?php
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 8/23/17
 * Time: 10:25 AM
 */
//helper to narrow down a block ID - no arguments gives growers, specified grower gives varieties, specified grower + varieties gives matching blocks
require '../../config.php';
packapps_authenticate_user('storage');

if (isset($_GET['growerID'])) {
    $grower = mysqli_real_escape_string($mysqli, $_GET['growerID']);
    if (isset($_GET['VarietyID'])) {
        $variety = mysqli_real_escape_string($mysqli, $_GET['VarietyID']);
        $blocks = mysqli_query($mysqli, "
          SELECT
          PK,
          isFinished,
          isDeleted,
          COALESCE(NULLIF(strainName, ''), '[No Strain]') AS strainName,
          COALESCE(NULLIF(farmName, ''), '[No Farm]') AS farmName,
          COALESCE(NULLIF(BlockDesc, ''), '[No Block]') AS BlockDesc
          FROM `grower_crop-estimates`
          JOIN grower_farms ON `grower_crop-estimates`.farmID = grower_farms.farmID
          JOIN grower_GrowerLogins ON grower_farms.growerID = grower_GrowerLogins.GrowerID
          JOIN grower_strains ON `grower_crop-estimates`.strainID = grower_strains.strain_ID
          JOIN grower_varieties ON grower_strains.variety_ID = grower_varieties.VarietyID
          WHERE GrowerCode = '$grower' AND VarietyID = '$variety'
          ORDER BY isDeleted ASC, BlockDesc DESC 
        ");
        $output = array();
        while($row = mysqli_fetch_assoc($blocks)){
            $text = ($row['isDeleted'] > 0 ? '[Retired] ' : '') . ($row['isFinished'] > 0 ? '[Finished] ' : '') . "Block:  " . $row['BlockDesc'] . " - Farm: " . $row['farmName'] . " - Strain: " . $row['strainName'] ;
            array_push($output, array('id' => $row['PK'], 'text' => $text));
        }
        echo json_encode($output);
    } else {
        $varieties = mysqli_query($mysqli, "
          SELECT
          VarietyID,
          VarietyName
          FROM grower_varieties
          JOIN grower_strains ON grower_varieties.VarietyID = grower_strains.variety_ID
          JOIN `grower_crop-estimates` ON grower_strains.strain_ID = `grower_crop-estimates`.strainID
          JOIN grower_farms ON `grower_crop-estimates`.farmID = grower_farms.farmID
          JOIN grower_GrowerLogins ON grower_farms.growerID = grower_GrowerLogins.GrowerID
          WHERE GrowerCode = '$grower' GROUP BY VarietyID
        ");
        $output = array();
        while($row = mysqli_fetch_assoc($varieties)){
            array_push($output, array('id' => $row['VarietyID'], 'text' => $row['VarietyName']));
        }
        echo json_encode($output);
    }
} else {
    $growers = mysqli_query($mysqli, "SELECT GrowerCode, GrowerName FROM grower_GrowerLogins ORDER BY GrowerCode");
    $output = array();
    while ($row = mysqli_fetch_assoc($growers)) {
        array_push($output, array('id' => $row['GrowerCode'], 'text' => $row['GrowerCode'] . " - " . $row['GrowerName']));
    }
    echo json_encode($output);
}
