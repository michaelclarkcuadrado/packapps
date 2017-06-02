<?php
//These are the settings for the packapps server. Any setting in quotes may be changed to reflect the current operating environment.
//MYSQL Server Details
$dbhost = "localhost";
$dbusername = "ricefruit";
$dbpassword = "ricefruit";
$operationsDatabase = 'operationsData';
$growerDB = 'growerReporting';
$companyName = 'Rice Fruit Company';

//QA SMTP Server Details
$smtpHost = 'smtp.office365.com';
$smtpUser = 'qa@ricefruit.com';
$smtpPassword = 'Peaches13';
$smtpPort = '587';

//Line names
$Line1Name = "Blue Line";
$Line2Name = "Gray Line";
$Line3Name = "Presizer";

//security key to encrypt cookies with, if changed everyone will log out
$securityKey = "vQSrLcADNgwtyG20dxiwHmw0PmtGK4XNHgdci8pUAB5pDU";

//DO NOT EDIT BELOW THIS LINE
$mysqli = mysqli_connect($dbhost, $dbusername, $dbpassword, $operationsDatabase);


if (mysqli_connect_errno($mysqli)) {
    die("The Packapps server has experienced an internal issue.<br> Wait a few minutes and try again. If it keeps happening, reboot the server or contact the administrator. the error was: <br><br><b>Failed to connect to MySQL: " . mysqli_connect_error());
}