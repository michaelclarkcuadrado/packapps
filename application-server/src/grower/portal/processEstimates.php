<?php
include '../../config.php';
$adminauth = mysqli_query($mysqli, "SELECT isAdmin FROM grower_growerLogins WHERE GrowerCode='" . $_SERVER['PHP_AUTH_USER'] . "'");
$admin = mysqli_fetch_array($adminauth);
$cropyear = date('Y');
foreach ($_POST as $pk => $posteddata) {
    $pk = mysqli_real_escape_string($mysqli, $pk);
    $posteddata = mysqli_real_escape_string($mysqli, $posteddata);
    //bn == blockname

    if (strpos($pk, 'bn') !== false) {
        mysqli_query($mysqli, "UPDATE `grower_crop-estimates` SET `BlockDesc` ='" . $posteddata . "' WHERE PK='" . str_replace("bn", "", $pk) . "' AND Grower='" . ($admin[0] == 1 && ($_GET['pretend']) ? $_GET['pretend'] : (isset($_GET['alt_acc']) ? base64_decode($_GET['alt_acc']) : $_SERVER['PHP_AUTH_USER'])) . "'");

    } else {
        mysqli_query($mysqli, "UPDATE `grower_crop-estimates` SET `" . $cropyear . "est` ='" . $posteddata . "' WHERE PK='" . $pk . "' AND Grower='" . ($admin[0] == 1 && ($_GET['pretend']) ? $_GET['pretend'] : (isset($_GET['alt_acc']) ? base64_decode($_GET['alt_acc']) : $_SERVER['PHP_AUTH_USER'])) . "'");
        mysqli_query($mysqli, "INSERT INTO grower_crop_estimates_changes_timeseries (`block_PK`, `date_Changed`, `cropYear`, `belongs_to_Grower`, `changed_by`, `new_bushel_value`) VALUES ('$pk', default, '$cropyear', '" . ($admin[0] == 1 && ($_GET['pretend']) ? $_GET['pretend'] : (isset($_GET['alt_acc']) ? base64_decode($_GET['alt_acc']) : $_SERVER['PHP_AUTH_USER'])) . "', '" . $_SERVER['PHP_AUTH_USER'] . "', '$posteddata')");
    }
}