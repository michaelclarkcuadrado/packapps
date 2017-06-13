<?php
//uploads an edited run
require '../config.php';
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
// end authentication

if ($_POST['RunID'] != 'DEFAULT')
{
mysqli_query($mysqli, "DELETE FROM production_runs WHERE RunID='" . $_POST['RunID'] . "'") or die(mysqli_error($mysqli));
$ID = mysqli_real_escape_string($mysqli, $_POST['RunID']);
} else {
    $ID = $_POST['RunID'];
}

$insert = mysqli_query($mysqli, "INSERT INTO production_runs VALUES (" . $ID . ", '" . mysqli_real_escape_string($mysqli, $_POST['runNum']) . "', '" . $_POST['Line'] . "', 0, ".$_POST['isPreInspected'].", ".$_POST['isQA'].", DEFAULT, '" . mysqli_real_escape_string($mysqli, $RealName['UserRealName']) . "')") or die(mysqli_error($mysqli));
$ID = mysqli_insert_id($mysqli);
//dumped fruit
for ($i = 1; $_POST['growerCode' . $i]; $i++) {
    mysqli_query($mysqli, "INSERT INTO production_dumped_fruit VALUES ('" . mysqli_real_escape_string($mysqli, $_POST['Not'. $i]) . "', " . $ID . ", '" . mysqli_real_escape_string($mysqli, $_POST['growerCode' . $i]) . "', '" . mysqli_real_escape_string($mysqli, $_POST['Variety' . $i]) . "', '" . mysqli_real_escape_string($mysqli, $_POST['Quality' . $i]) . "', '" . mysqli_real_escape_string($mysqli, $_POST['Size' . $i]) ."', '" . mysqli_real_escape_string($mysqli, $_POST['Lot' . $i]) . "', '" . mysqli_real_escape_string($mysqli, $_POST['Location' . $i]) . "', '" . mysqli_real_escape_string($mysqli, $_POST['Amount' . $i]) . "')");
}
//products
for ($i = 1; $_POST['productMade' . $i]; $i++) {
    mysqli_query($mysqli, "INSERT INTO production_product_needed VALUES ('" . $ID . "', '" . mysqli_real_escape_string($mysqli, $_POST['productMade' . $i]) . "', '" . mysqli_real_escape_string($mysqli, $_POST['packSize' . $i]) . "', '" . mysqli_real_escape_string($mysqli, $_POST['madeAmount' . $i]) . "', '" . mysqli_real_escape_string($mysqli, $_POST['amountType' . $i]) . "')") or die(mysqli_error($mysqli));
}
if(!isset($_GET['duplicate'])) {
    mysqli_query($mysqli, "INSERT INTO production_chat VALUES ('', '" . $_POST['Line'] . "', '" . mysqli_real_escape_string($mysqli, $SecuredUserName) . "', 'Run #" . mysqli_real_escape_string($mysqli, $_POST['runNum']) . " added to schedule.')");
}
echo "<script>window.close()</script>";