<?
include '../config.php';
$count_total = mysqli_query($mysqli, "SELECT COUNT(*) AS countRT, (SELECT count(*) FROM quality_AppleSamples) AS countSamp FROM quality_InspectedRTs");
$total_count = mysqli_fetch_assoc($count_total);
//authentication
if (!isset($_COOKIE['auth']) || !isset($_COOKIE['username'])) {
    die("<script>window.location.replace('/')</script>");
} else if (!hash_equals($_COOKIE['auth'], crypt($_COOKIE['username'], $securityKey))) {
    die("<script>window.location.replace('/')</script>");
} else {
    $SecuredUserName = mysqli_real_escape_string($mysqli, $_COOKIE['username']);
    $checkAllowed = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT `Real Name` AS 'UserRealName', Role, allowedQuality FROM packapps_master_users JOIN quality_UserData ON packapps_master_users.username=quality_UserData.UserName WHERE packapps_master_users.username = '$SecuredUserName'"));
    if (!$checkAllowed['allowedQuality'] > 0) {
        die ("<script>window.location.replace('/')</script>");
    } else {
        $RealName = $checkAllowed;
    }
}
// end authentication
if ($RealName['Role'] <> 'QA') {
    echo "<script>window.location.href='index.php'</script>";
} ?>
<!DOCTYPE html>
<link rel="manifest" href="manifest.json">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-title" content="PackApps">
<link rel="apple-touch-icon" href="apple-touch-icon.png">
<link rel="icon" sizes="196x196" href="apple-touch-icon.png">
<meta name="mobile-web-app-capable" content="yes">
<meta name="theme-color" content="#e2eef4">
<meta name="viewport" content="width=device-width, initial-scale=1 user-scalable=no">
<link rel="stylesheet" type="text/css" media="all" href="assets/css/inspector.css">
<title>Mobile QA</title>
<body style="padding-top: 15px">
<h1>Mobile QA Lab</h1>
<h3 style="text-align: center"><?echo $companyName?> Quality Assurance Lab</h3>
<br>
<h2>Welcome back, <strong><? echo $RealName['UserRealName'] ?></strong></h2>
<br>
<h2>This Year: <? echo $total_count['countSamp'] ?> individual samples across <? echo $total_count['countRT'] ?>
    RTs!</h2>
<br><br>
<a href="Inspector.php">
    <button>Create a New RT Report</button>
</a>
<br>
<button onclick="location.replace('mobileAlert.php')">Create a New Inventory Alert</button>
<br>
<hr>
<h2 style="text-align: center">Available Tests</h2><br>
<button onclick="location.replace('WeightSamples.php')">RT Report >> Weighing</button>
<br>
<button onclick="location.replace('runPhoto.php')">Run Report >> Take Photo</button>
<br>
<button onclick="location.replace('DA.php')">RT Report >> DA Test</button>
<br>
<button onclick="location.replace('mobilestarch.php')">RT Report >> Starch Test</button>
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


<script>
    function logout() {
        var xmlhttp;
        if (window.XMLHttpRequest) {
            xmlhttp = new XMLHttpRequest();
        }
        // code for IE
        else if (window.ActiveXObject) {
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        if (window.ActiveXObject) {
            // IE clear HTTP Authentication
            document.execCommand("ClearAuthenticationCache", false);
            window.location.href = 'logout/logoutheader.php';
        } else {
            xmlhttp.open("GET", 'logout/logoutheader.php', true, "User Name", "logout");
            xmlhttp.send("");
            xmlhttp.onreadystatechange = function () {
                if (xmlhttp.readyState == 4) {
                    window.location.href = 'logout/logoutheader.php';
                }
            }
        }
        return false;
    }
</script>
</body>
</html>