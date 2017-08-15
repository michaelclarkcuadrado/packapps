<?
include '../../config.php';
$mysqli2 = mysqli_connect($dbhost, $dbusername, $dbpassword, $growerDB);


//get real name for logging accountability
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
if ($RealName['Role'] !== "QA") {
    die("UNAUTHORIZED");
};
// end authentication


//set constants
$Notes = $_POST['notes'];
$PK = $_POST['Block'];
$Inspector = $RealName['UserRealName'];
$Grower = $_POST['Grower'];
$Retain = 0;
if ($_POST['Retain']) {
    $Retain = 1;
}
$NumSamples = $_POST['NumSamples'];

//quick and dirty fix for a big bug -- fix me when you have proper keys for tests
$checkIfDone = mysqli_query($mysqli2, "SELECT * FROM `Preharvest_Samples` WHERE PK = $PK AND DATE(`Date`) = DATE(CURDATE())");
if(mysqli_num_rows($checkIfDone) > 0){
    die("This Block has already been done today. It cannot be done again until tomorrow.");
}

//Prepare Statement
if (!($stmt = mysqli_prepare($mysqli2, "Insert into `Preharvest_Samples` (Grower, PK, Retain, SampleNum, NumSamples, Pressure1, Pressure2, Brix, Weight, DA, DA2, Inspector, Notes) values  (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"))) {
    die ("Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error);
}

if (!mysqli_stmt_bind_param($stmt, 'siiisssssssss', $Grower, $PK, $Retain, $Num, $NumSamples, $Pressure1, $Pressure2, $Brix, $Weight, $DA, $DA2, $Inspector, $Notes)) {
    die ("Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error);
}


//Set Vars and Execute
if ($_POST['NumSamples']) {
    $Num = 1;
    $Pressure1 = $_POST['pressure1-1'];
    $Pressure2 = $_POST['pressure1-2'];
    $Weight = $_POST['weight1'];
    $DA = $_POST['DA1-1'];
    $DA2 = $_POST['DA1-2'];
    if (($_POST['brix1'])) {
        $Brix = $_POST['brix1'];
    } else {
        $Brix = null;
    }
    $Weight = $_POST['weight1'];
    if (!mysqli_stmt_execute($stmt)) {
        die ("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
    }

    $Num = 2;
    $Pressure1 = $_POST['pressure2-1'];
    $Pressure2 = $_POST['pressure2-2'];
    $Weight = $_POST['weight2'];
    $DA = $_POST['DA2-1'];
    $DA2 = $_POST['DA2-2'];
    if (($_POST['brix2'])) {
        $Brix = $_POST['brix2'];
    } else {
        $Brix = null;
    }
    $Weight = $_POST['weight2'];
    if (!mysqli_stmt_execute($stmt)) {
        die ("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
    }

    $Num = 3;
    $Pressure1 = $_POST['pressure3-1'];
    $Pressure2 = $_POST['pressure3-2'];
    $Weight = $_POST['weight3'];
    if (($_POST['brix3'])) {
        $Brix = $_POST['brix3'];
    } else {
        $Brix = null;
    }
    $Weight = $_POST['weight3'];
    $DA = $_POST['DA3-1'];
    $DA2 = $_POST['DA3-2'];
    if (!mysqli_stmt_execute($stmt)) {
        die ("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
    }

    $Num = 4;
    $Pressure1 = $_POST['pressure4-1'];
    $Pressure2 = $_POST['pressure4-2'];
    $Weight = $_POST['weight4'];
    $DA = $_POST['DA4-1'];
    $DA2 = $_POST['DA4-2'];
    if (($_POST['brix4'])) {
        $Brix = $_POST['brix4'];
    } else {
        $Brix = null;
    }
    $Weight = $_POST['weight4'];
    if (!mysqli_stmt_execute($stmt)) {
        die ("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
    }

    $Num = 5;
    $Pressure1 = $_POST['pressure5-1'];
    $Pressure2 = $_POST['pressure5-2'];
    $DA = $_POST['DA5-1'];
    $DA2 = $_POST['DA5-2'];
    if (($_POST['brix5'])) {
        $Brix = $_POST['brix5'];
    } else {
        $Brix = null;
    }
    $Weight = $_POST['weight5'];
    if (!mysqli_stmt_execute($stmt)) {
        die ("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
    }
}
if ($_POST['NumSamples'] == 10 or $_POST['NumSamples'] == 15) {

    $Num = 6;
    $Pressure1 = $_POST['pressure6-1'];
    $Pressure2 = $_POST['pressure6-2'];
    if (($_POST['brix6'])) {
        $Brix = $_POST['brix6'];
    } else {
        $Brix = null;
    }
    $Weight = $_POST['weight6'];
    $DA = $_POST['DA6-1'];
    $DA2 = $_POST['DA6-2'];
    if (!mysqli_stmt_execute($stmt)) {
        die ("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
    }

    $Num = 7;
    $Pressure1 = $_POST['pressure7-1'];
    $Pressure2 = $_POST['pressure7-2'];
    if (($_POST['brix7'])) {
        $Brix = $_POST['brix7'];
    } else {
        $Brix = null;
    }
    $Weight = $_POST['weight7'];
    $DA = $_POST['DA7-1'];
    $DA2 = $_POST['DA7-2'];
    if (!mysqli_stmt_execute($stmt)) {
        die ("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
    }

    $Num = 8;
    $Pressure1 = $_POST['pressure8-1'];
    $Pressure2 = $_POST['pressure8-2'];
    if (($_POST['brix8'])) {
        $Brix = $_POST['brix8'];
    } else {
        $Brix = null;
    }
    $Weight = $_POST['weight8'];
    $DA = $_POST['DA8-1'];
    $DA2 = $_POST['DA8-2'];
    if (!mysqli_stmt_execute($stmt)) {
        die ("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
    }

    $Num = 9;
    $Pressure1 = $_POST['pressure9-1'];
    $Pressure2 = $_POST['pressure9-2'];
    if (($_POST['brix9'])) {
        $Brix = $_POST['brix9'];
    } else {
        $Brix = null;
    }
    $Weight = $_POST['weight9'];
    $DA = $_POST['DA9-1'];
    $DA2 = $_POST['DA9-2'];
    if (!mysqli_stmt_execute($stmt)) {
        die ("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
    }

    $Num = 10;
    $Pressure1 = $_POST['pressure10-1'];
    $Pressure2 = $_POST['pressure10-2'];
    if (($_POST['brix10'])) {
        $Brix = $_POST['brix10'];
    } else {
        $Brix = null;
    }
    $Weight = $_POST['weight10'];
    $DA = $_POST['DA10-1'];
    $DA2 = $_POST['DA10-2'];
    if (!mysqli_stmt_execute($stmt)) {
        die ("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
    }
}


if ($_POST['NumSamples'] == 15) {

    $Num = 11;
    $Pressure1 = $_POST['pressure11-1'];
    $Pressure2 = $_POST['pressure11-2'];
    if (($_POST['brix11'])) {
        $Brix = $_POST['brix11'];
    } else {
        $Brix = null;
    }
    $Weight = $_POST['weight11'];
    $DA = $_POST['DA11-1'];
    $DA2 = $_POST['DA11-2'];
    if (!mysqli_stmt_execute($stmt)) {
        die ("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
    }

    $Num = 12;
    $Pressure1 = $_POST['pressure12-1'];
    $Pressure2 = $_POST['pressure12-2'];
    if (($_POST['brix12'])) {
        $Brix = $_POST['brix12'];
    } else {
        $Brix = null;
    }
    $Weight = $_POST['weight12'];
    $DA = $_POST['DA12-1'];
    $DA2 = $_POST['DA12-2'];
    if (!mysqli_stmt_execute($stmt)) {
        die ("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
    }

    $Num = 13;
    $Pressure1 = $_POST['pressure13-1'];
    $Pressure2 = $_POST['pressure13-2'];
    if (($_POST['brix13'])) {
        $Brix = $_POST['brix13'];
    } else {
        $Brix = null;
    }
    $Weight = $_POST['weight13'];

    $DA = $_POST['DA13-1'];
    $DA2 = $_POST['DA13-2'];
    if (!mysqli_stmt_execute($stmt)) {
        die ("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
    }

    $Num = 14;
    $Pressure1 = $_POST['pressure14-1'];
    $Pressure2 = $_POST['pressure14-2'];
    if (($_POST['brix14'])) {
        $Brix = $_POST['brix14'];
    } else {
        $Brix = null;
    }
    $Weight = $_POST['weight14'];

    $DA = $_POST['DA14-1'];
    $DA2 = $_POST['DA14-2'];
    if (!mysqli_stmt_execute($stmt)) {
        die ("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
    }

    $Num = 15;
    $Pressure1 = $_POST['pressure15-1'];
    $Pressure2 = $_POST['pressure15-2'];
    if (($_POST['brix15'])) {
        $Brix = $_POST['brix15'];
    } else {
        $Brix = null;
    }
    $Weight = $_POST['weight15'];

    $DA = $_POST['DA15-1'];
    $DA2 = $_POST['DA15-2'];
    if (!mysqli_stmt_execute($stmt)) {
        die ("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
    }
}
echo "<script>location.replace('index.php?ph-block=$PK#preharvest')</script>";
