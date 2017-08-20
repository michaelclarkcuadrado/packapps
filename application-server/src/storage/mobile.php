<?php
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 8/18/17
 * Time: 2:39 PM
 */
require '../config.php';
$userData = packapps_authenticate_user('storage');

include_once("../scripts-common/Mobile_Detect.php");
$detect=new Mobile_Detect();
if(!$detect->isMobile()){
    die("<script>location.replace('index.php')</script>");
}
?>

<!DOCTYPE html>
<meta name="theme-color" content="#e2eef4">
<meta name="viewport" content="width=device-width, initial-scale=1 user-scalable=no">
<link rel="stylesheet" type="text/css" media="all" href="assets/css/inspector.css">
<title>Mobile QA</title>
<body style="padding-top: 15px">
<h1>Mobile QA Lab</h1>
<h3 style="text-align: center"><?echo $companyName?> Quality Assurance Lab</h3>
<br>
<h2>Welcome back, <strong><? echo $userData['Real Name'] ?></strong></h2>
<br>
<h2>This Year: <? echo $total_count['countSamp'] ?> individual samples weighing a total <? echo $total_count['countWeight'] ?> pounds!</h2>
<br><br>
<a href="Inspector.php">
    <button>Create a New Delivery Report</button>
</a>
<br>
<hr>
<h2 style="text-align: center">Available Tests</h2><br>
<button onclick="location.replace('WeightSamples.php')">Delivery Report >> Weighing</button>
<br>
<button onclick="location.replace('runPhoto.php')">Run Report >> Take Photo</button>
<br>
<button onclick="location.replace('DA.php')">Delivery Report >> DA Test</button>
<br>
<button onclick="location.replace('mobilestarch.php')">Delivery Report >> Starch Test</button>
<br>
<button onclick="location.replace('preharvest/mobilestarch.php')">Pre-Harvest Grower Report >> Starch Test</button>
<br>
<hr>
<h2 style="text-align: center">Options</h2>
<br>
<button value="Logout" onclick="location.href = '/'">Main Menu</button>
<br>
<button style='height: 25px; width: 48%; margin-left: auto; display: block; margin-right: auto'
        onclick="location.replace('QA.php')">View Desktop Version
</button>

</body>
</html>