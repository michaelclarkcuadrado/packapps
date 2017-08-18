<?php
require '../config.php';

$userData = packapps_authenticate_user('quality');

if($_POST['weight']*1 == 0) {
    die("<html style='text-align: center'><meta name='viewport' content='width=device-width, initial-scale=1'><h1 style='color: red'>NOT RECEIVED.</h1><h3>There was a Wifi error. We did not insert that RT into the QA lab system. Try again.</h3><br>Attempted receipt: " . $RT . ", Error listed as: No Weight data received.<br> <a href='' onclick='window.history.back();'><-- Go Back</a></html>");
}
$weightinlb = ($_POST['weight']*2.2);
$stmt = mysqli_prepare($mysqli, "INSERT INTO quality_AggregateWeightSamples (`receiptNum`, `Weight`, InspectorName) VALUES (?, ?, ?)") or error_log(mysqli_error($mysqli));
mysqli_stmt_bind_param($stmt, "ids", $_POST['RT'], $weightinlb, $userData['username']);
if ($_POST['RT'] == $_POST['RT2'] && mysqli_stmt_execute($stmt)) {
    echo "<script>location.replace('WeightSamples.php?ins=".$_POST['RT']."')</script>";
} else {
    die ("<html style='text-align: center'><meta name='viewport' content='width=device-width, initial-scale=1'><h1 style='color: red'>NOT RECEIVED.</h1><h3>There was a database error. We did not insert that RT into the QA lab system. Try again.</h3><br>Attempted receipt: " . $RT . ", Error listed as: " . mysqli_stmt_error($stmt) . "<br> <a href='' onclick='window.history.back();'><-- Go Back</a></html>");
}