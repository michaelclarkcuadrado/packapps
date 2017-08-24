<?php
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 8/18/17
 * Time: 2:39 PM
 */
require '../config.php';
$userData = packapps_authenticate_user('storage');

$binsReceived = mysqli_fetch_assoc(mysqli_query($mysqli, "
SELECT COUNT(*) AS bins
FROM storage_grower_fruit_bins
  JOIN storage_grower_receipts ON storage_grower_fruit_bins.grower_receipt_id = storage_grower_receipts.id
WHERE YEAR(date) = YEAR(CURDATE())"));
$binsReceived = $binsReceived['bins'];

include_once("../scripts-common/Mobile_Detect.php");
$detect=new Mobile_Detect();
if(!$detect->isMobile()){
    die("<script>location.replace('index.php')</script>");
}
?>

<html>
<meta name="theme-color" content="#e2eef4">
<meta name="viewport" content="width=device-width, initial-scale=1 user-scalable=no">
<link rel="stylesheet" type="text/css" media="all" href="../styles-common/inspector.css">
<title>Mobile Inventory</title>
<body style="padding-top: 15px">
<h1>Mobile Inventory</h1>
<h3 style="text-align: center"><?echo $companyName?> Storage Insights</h3>
<br>
<h2>Welcome back, <? echo $userData['Real Name'] ?>.</h2>
<br>
<h2>Year to Date: <? echo $binsReceived ?> Bins Received!</h2>
<br><br>
<a href="newReceipt.php">
    <button>Create New Receipt</button>
</a>
<br>
<hr>
<h2 style="text-align: center">Inventory Actions</h2><br>
<button onclick="location.replace('WeightSamples.php')">Assign Bins to Room</button>
<br>
<button onclick="location.replace('runPhoto.php')">Finish Bins</button>
<br>
<button onclick="location.replace('DA.php')">Move Bins</button>
<br>
<button onclick="location.replace('mobilestarch.php')">Log Room Event</button>
<br>
<button onclick="location.replace('preharvest/mobilestarch.php')">Open/Close Room</button>
<br>
<hr>
<br>
<button value="Logout" onclick="location.href = '/'"><< Main Menu</button>
</body>
</html>