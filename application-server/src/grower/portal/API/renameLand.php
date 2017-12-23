<?php
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 12/23/17
 * Time: 11:23 AM
 */
/*
 * Renames either a farm or a block, the only two user-nameable data structs.
 * Returns json of block's details if renaming block.
 * */

require_once '../../../config.php';
$userinfo = packapps_authenticate_grower();

if (isset($_GET['landType']) && isset($_GET['landID']) && isset($_GET['newName'])) {
    $landType = mysqli_real_escape_string($mysqli, $_GET['landType']);
    $landID = mysqli_real_escape_string($mysqli, $_GET['landID']);
    $newName = mysqli_real_escape_string($mysqli, $_GET['newName']);
    if($newName == null || $newName == ""){
        APIFail("Invalid land name.");
    }
    if ($landType === "farm") {
        mysqli_query($mysqli, "
            UPDATE grower_farms SET farmName = '" . $newName . "' 
            WHERE farmID = '".$landID."'
                AND growerID = '".$userinfo['GrowerID']."'
        ") or APIFail("Could not rename.");
        echo json_encode(array()); //must return valid json
    } elseif ($landType === "block") {
        mysqli_query($mysqli, "
            UPDATE `grower_crop-estimates` 
            JOIN grower_farms g ON `grower_crop-estimates`.farmID = g.farmID 
            SET BlockDesc = '" . $newName . "' 
            WHERE PK = '".$landID."'
                AND g.growerID = '".$userinfo['GrowerID']."'
        ") or APIFail("Could not rename.");
        $block_details = mysqli_query($mysqli, "
            SELECT PK, `grower_crop-estimates`.farmID, variety_ID, commodityID
            FROM `grower_crop-estimates`
            JOIN grower_strains g ON `grower_crop-estimates`.strainID = g.strain_ID
            JOIN grower_varieties v ON g.variety_ID = v.VarietyID
            WHERE PK = '".$landID."'
        ");
        echo json_encode(mysqli_fetch_assoc($block_details));
    } else {
        APIFail("Valid landTypes are 'farm' and 'block'");
    }
} else {
    APIFail("Required params not sent.");
}
