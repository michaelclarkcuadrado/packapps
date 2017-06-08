<?php
/**
 * Created by PhpStorm.
 * User: Michael Clark-Cuadrado
 * Date: 6/10/2016
 * Time: 10:16 AM
 */

include '../config.php';
require_once('emailAlerts/EmergencyAlert.php');

//authentication
if (!isset($_COOKIE['auth']) || !isset($_COOKIE['username'])) {
    die("<script>window.location.replace('/')</script>");
} else if (!hash_equals($_COOKIE['auth'], crypt($_COOKIE['username'], $securityKey))) {
    die("<script>window.location.replace('/')</script>");
} else {
    $SecuredUserName = mysqli_real_escape_string($mysqli, $_COOKIE['username']);
    $checkAllowed = mysqli_fetch_array(mysqli_query($mysqli, "SELECT `Real Name`, allowedQuality FROM packapps_master_users WHERE packapps_master_users.username = '$SecuredUserName'"));
    if (!$checkAllowed['allowedQuality'] > 0) {
        die ("<script>window.location.replace('/')</script>");
    } else {
        $name= $checkAllowed['Real Name'];
        $RealName = $checkAllowed;
    }
}
// end authentication
if(isset($_POST['markRunAsBad']) && $_POST['markRunAsBad'] == 1)
{
    mysqli_query($mysqli, "INSERT INTO PSOHCSV_flagged_bad_runs VALUES ('".mysqli_real_escape_string($mysqli, $_POST['RunNum'])."', DEFAULT, DEFAULT)");
}

$photo = false;
if(isset($_FILES['binPicUpload']))
{
    if (file_exists("assets/uploadedimages/alerts/" . escapeshellcmd($_POST['RunNum']) . ".jpg"))
    {
        unlink("assets/uploadedimages/alerts/" . escapeshellcmd($_POST['RunNum']) . ".jpg");
    }
    move_uploaded_file($_FILES['binPicUpload']['tmp_name'], "assets/uploadedimages/alerts/" . escapeshellcmd($_POST['RunNum']) . ".jpg");
    $photo = true;
}

$alert = new EmergencyAlert();
$mail = $alert->prepareMail();
$alert->setSubject($mail, "Inventory Alert from ".$name);
$alert->setBody($mail, "<html><p>An alert was made for ".($_POST['BinType'] == 'presized' ? 'a presized run in inventory.' : 'an RT in inventory.')."</p><br><table border='1' cellpadding='3' cellspacing='0'><thead><th>Time</th><th>".($_POST['BinType'] == 'presized' ? 'Run#' : 'RT#')."</th><th>Grower</th><th>".($_POST['BinType'] == 'presized' ? 'Grade' : 'Farm')."</th><th>".($_POST['BinType'] == 'presized' ? 'Size' : 'Block')."</th><th>Variety</th></thead><tr><td>" . date('Y-m-d H:s') . "</td><td>" . $_POST['RunNum'] . "</td><td>" . $_POST['GrowerName'] . "</td><td>" . $_POST['FarmDesc'] . "</td><td>" . $_POST['BlockDesc'] . "</td><td>" . $_POST['VarDesc'] . "</td></tr></table><br><p>The note given was: ".$_POST['notes']."</p><br>".($photo ? "<img width=\'65%\' src=\'cid:attach-bin\'></html>" : "</html>"));
if($photo) {
    $mail->AddEmbeddedImage("assets/uploadedimages/alerts/" . escapeshellcmd($_POST['RunNum']) . ".jpg", "attach-bin", escapeshellcmd($_POST['RunNum']) . ".jpg");
}
$alert->sendMail($mail);

echo "<script>location.replace('mobileAlert.php?success=1')</script>";