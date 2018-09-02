<?php
/**
 * Created by PhpStorm.
 * User: Michael Clark-Cuadrado
 * Date: 5/25/2016
 * Time: 11:24 AM
 */
require '../../config.php';
//authentication
if (!isset($_COOKIE['auth']) || !isset($_COOKIE['username'])) {
    die("<script>window.location.replace('/')</script>");
} else if (!hash_equals($_COOKIE['auth'], crypt($_COOKIE['username'], $securityKey))) {
    die("<script>window.location.replace('/')</script>");
} else {
    $SecuredUserName = mysqli_real_escape_string($mysqli, $_COOKIE['username']);
    $checkAllowed = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT `Real Name` as UserRealName, Role, allowedProduction FROM packapps_master_users JOIN production_UserData ON packapps_master_users.username=production_UserData.UserName WHERE packapps_master_users.username = '$SecuredUserName'"));
    if (!$checkAllowed['allowedProduction'] > 0) {
        die ("<script>window.location.replace('/')</script>");
    } else {
        $RealName = $checkAllowed;
    }
}
if ($RealName['Role'] != 'Production') {
    echo "<script>window.close()</script>";
    die();
}
// end authentication


$insert = mysqli_query($mysqli, "INSERT INTO production_runs VALUES (DEFAULT , '" . mysqli_real_escape_string($mysqli, $_POST['runNum']) . "', '" . $_POST['options'] . "', 0, 0, 0, DEFAULT, '" . mysqli_real_escape_string($mysqli, $RealName['UserRealName']) . "')");
$ID = mysqli_insert_id($mysqli);
//dumped fruit
for ($i = 1; $_POST['Variety' . $i]; $i++) {
    mysqli_query($mysqli, "INSERT INTO production_dumped_fruit VALUES ('" . mysqli_real_escape_string($mysqli, $_POST['Not'. $i]) . "', " . $ID . ", '" . mysqli_real_escape_string($mysqli, $_POST['growerCode' . $i]) . "', '" . mysqli_real_escape_string($mysqli, $_POST['Variety' . $i]) . "', '" . mysqli_real_escape_string($mysqli, $_POST['Quality' . $i]) . "', '" . mysqli_real_escape_string($mysqli, $_POST['Size' . $i]) ."', '" . mysqli_real_escape_string($mysqli, $_POST['Lot' . $i]) . "', '" . mysqli_real_escape_string($mysqli, $_POST['Location' . $i]) . "', '" . mysqli_real_escape_string($mysqli, $_POST['Amount' . $i]) . "')");
}
//product
for ($i = 1; $_POST['productMade' . $i]; $i++) {
    mysqli_query($mysqli, "INSERT INTO production_product_needed VALUES ('" . $ID . "', '" . mysqli_real_escape_string($mysqli, $_POST['productMade' . $i]) . "', '" . mysqli_real_escape_string($mysqli, $_POST['packSize' . $i]) . "', '" . mysqli_real_escape_string($mysqli, $_POST['madeAmount' . $i]) . "', '" . mysqli_real_escape_string($mysqli, $_POST['amountType'.$i]) . "')") or die(mysqli_error($mysqli));
}

mysqli_query($mysqli, "INSERT INTO production_chat VALUES ('', '" . $_POST['Line'] . "', '" . mysqli_real_escape_string($mysqli, $SecuredUserName) . "', 'New run #" . mysqli_real_escape_string($mysqli, $_POST['runNum']) . " added to schedule.')");


if (strpos($_SERVER['HTTP_REFERER'], "newRun") === false) {
    echo "<script>location.replace('/')</script>";
} else {
    echo "<script>window.close()</script>";
}