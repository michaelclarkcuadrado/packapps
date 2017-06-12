<?php
//Internal packapps variables
$packapps_version = 2;
$isOnAWSBackend = false;
$isManagedByOrchestration = false;
$orchestraionServerURL = '';
$AWS_ACCESS_KEY_ID = 0;
$AWS_SECRET_ACCESS_KEY = 0;
//companyName is user presentable, companyShortName is internal and the deployment ID
$companyShortName = 'devenv';
$companyName = 'DEVELOPMENT ENV';


//MYSQL Server Details
$dbhost = "database-server";
$dbusername = "packapps";
$dbpassword = "packapps";
$dbport = "3306";
$operationsDatabase = 'operationsData';
$growerDB = 'growerReporting';

//QA SMTP Server Details
$smtpHost = '';
$smtpUser = '';
$smtpPassword = '';
$smtpPort = '';

//Line names
$Line1Name = "Blue Line";
$Line2Name = "Gray Line";
$Line3Name = "Presizer";

//security key to encrypt cookies with, if changed everyone will log out
$securityKey = "vQSrLcADNgwtyG20dxiwHmw0PmtGK4XNHgdci8pUAB5pDU";

//do db connection
$mysqli = mysqli_connect($dbhost, $dbusername, $dbpassword, $operationsDatabase, $dbport);
if (mysqli_connect_errno()) {
    die("The Packapps server has experienced an internal issue.<br> Wait a few minutes and try again. If it keeps happening, contact the administrator. the error was: <br><br><b>Failed to connect to MySQL: " . mysqli_connect_error());
}

//check if packapps has been set up
$systemRow = mysqli_query($mysqli, "SELECT packapps_version, systemInstalled FROM packapps_system_info");
$systemRow = mysqli_fetch_array($systemRow);
if($systemRow['packapps_version'] != $packapps_version){
    die("The Packapps server has experienced an internal issue.<br> If it keeps happening, contact the administrator. the error was: <br><br><b>Database schema does not match code schema. Broken Upgrade.");
}
if($_SERVER['SCRIPT_NAME'] != '/installer.php' && $systemRow['systemInstalled'] == 0){
    die("<script>window.location.replace('/installer.php')</script>");
}