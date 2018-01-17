<?php
//TODO DEPRECATE
include '../../config.php';
$userinfo = packapps_authenticate_grower();
$cropyear = date('Y');
foreach ($_POST as $pk => $posteddata) {
    $pk = mysqli_real_escape_string($mysqli, $pk);
    $posteddata = mysqli_real_escape_string($mysqli, $posteddata);
    //bn == blockname
    //TODO FIXME: get rid of all this rename and estimate in the same API call nonsense
    //TODO deprecate: renaming taken over by API/renameLand.php call
    if (strpos($pk, 'bn') !== false) {
        mysqli_query($mysqli, "UPDATE `grower_crop-estimates` SET `BlockDesc` ='" . $posteddata . "' WHERE PK='" . str_replace("bn", "", $pk) . "' AND Grower='" . $userinfo['GrowerCode'] . "'");

    } else {
        mysqli_query($mysqli, "UPDATE `grower_crop-estimates` SET `" . $cropyear . "est` ='" . $posteddata . "' WHERE PK='" . $pk . "' AND Grower='" . $userinfo['GrowerCode'] . "'");
        mysqli_query($mysqli, "INSERT INTO grower_crop_estimates_changes_timeseries (`block_PK`, `date_Changed`, `cropYear`, `belongs_to_Grower`, `changed_by`, `new_bushel_value`) VALUES ('$pk', default, '$cropyear', '" . $userinfo['GrowerCode'] . "', '" . $userinfo['GrowerCode']  . "', '$posteddata')");
    }
}