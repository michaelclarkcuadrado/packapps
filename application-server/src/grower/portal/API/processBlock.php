<?php
include '../../../config.php';
$userinfo = packapps_authenticate_grower();
if (isset($_GET['delete'])) {
    //delete or undelete block
    //TODO REWRITE QUERY
    $deletePK = mysqli_real_escape_string($mysqli, $_GET['delete']);
    mysqli_query($mysqli, "UPDATE `grower_crop-estimates` SET `isDeleted` = NOT `isDeleted` WHERE Grower='" . $userinfo['GrowerCode'] . "' AND PK='" . mysqli_real_escape_string($mysqli, $_GET['PK']) . "'");
} elseif (isset($_GET['finish'])) {
    //finish or unfinish block
    $finishPK = mysqli_real_escape_string($mysqli, $_GET['finish']);
    mysqli_query($mysqli, "
        UPDATE `grower_crop-estimates`
        JOIN grower_farms g ON `grower_crop-estimates`.farmID = g.farmID
        SET isFinished = NOT isFinished
        WHERE PK = '$finishPK' AND g.growerID = '".$userinfo['GrowerID']."'
    ");
} elseif (isset($_GET['sameEst'])) {
    //check same as last year or uncheck
    //TODO REWRITE QUERY
    mysqli_query($mysqli, "UPDATE `grower_crop-estimates` SET isSameAsLastYear = NOT `isSameAsLastYear` WHERE Grower='" . $userinfo['GrowerCode'] . "' AND PK='" . mysqli_real_escape_string($mysqli, $_GET['sameEst']) . "'");
}