<?php
/**
 * Created by PhpStorm
 * User: Michael Clark-Cuadrado
 * Date: 6/9/2016
 * Time: 12:04 PM
 */
include '../../config.php';
if (isset($_GET['q'])) {
    $result = mysqli_real_escape_string($mysqli, $_GET['q']);
} else if (isset($_FILES['0'])) {
    $result = exec("zbarimg -q --raw " . $_FILES['0']['tmp_name']);
    if (strlen($result) != 12) {
        die(json_encode(array('error' => "1")));
    }
    $result = substr($result, 0, 6);
} else {
    die(http_response_code(400));
}
header('Content-type: application/json');
//test if it is presized
$info = mysqli_query($mysqli, "SELECT rtrim(`Commodity Desc`) AS CommDesc, rtrim(`Run#`) as `Run`, concat('PS SubGw: ',rtrim(`Grower Desc`)) AS GrowerName, rtrim(`Grade Desc`) AS FarmDesc, rtrim(`Size Desc`) AS BlockDesc, rtrim(`Var Desc`) AS VarDesc FROM PSOHCSV WHERE `Ticket#` = " . $result);
if (mysqli_num_rows($info) == 0) {
    //try bulk instead
    $info = mysqli_query($mysqli, "SELECT rtrim(`Comm Desc`) AS CommDesc, rtrim(`RT#`) as `Run`, rtrim(`Grower Name`) AS GrowerName, rtrim(`Farm Desc`) AS FarmDesc, rtrim(`Block Desc`) AS BlockDesc, rtrim(`Var Desc`) AS VarDesc FROM BULKTKCSV WHERE `Ticket#` = " . $result) or die(mysqli_error($mysqli));
    $array = mysqli_fetch_assoc($info);
    $array = array_merge($array, mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT sum(`Qty On Hand`) as QtyOnHand FROM BULKTKCSV WHERE rtrim(`RT#`)=".$array['Run'])));
    array_push($array, 'bulk');
    echo json_encode($array);
} else {
    //resume presized
    $array = mysqli_fetch_assoc($info);
    $array = array_merge($array, mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT sum(`On Hand`) as QtyOnHand FROM PSOHCSV WHERE rtrim(`Run#`)=".$array['Run'])));
    array_push($array, 'presized');
    echo json_encode($array);
}
