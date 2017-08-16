<?
include '../../config.php';
$userdata = packapps_authenticate_user('quality');

//set constants
$Notes = mysqli_real_escape_string($mysqli, $_POST['notes']);
$PK = mysqli_real_escape_string($mysqli, $_POST['Block']);
$Inspector = $userdata['username'];
$Grower = mysqli_fetch_assoc(mysqli_query($mysqli, "
SELECT grower_GrowerLogins.growerID 
FROM grower_GrowerLogins 
JOIN grower_farms ON grower_GrowerLogins.GrowerID = grower_farms.growerID
JOIN `grower_crop-estimates` ON grower_farms.farmID = `grower_crop-estimates`.farmID
 WHERE PK = '$PK'
"))['growerID'];
$Retain = 0;
if (isset($_POST['Retain'])) {
    $Retain = 1;
}
$NumSamples = mysqli_real_escape_string($mysqli, $_POST['NumSamples']);

//create test entry
$testEntry = mysqli_query($mysqli, "
INSERT INTO `grower_Preharvest_tests` (grower, block_PK, NumSamples, Notes, Inspector)
VALUES ('$Grower', '$PK', '$NumSamples', '$Notes', '$Inspector')
");
$testID = mysqli_insert_id($mysqli);

//Prepare Statement
if (!($stmt = mysqli_prepare($mysqli, "INSERT INTO `grower_Preharvest_Samples` (test_id, Retain, SampleNum, Pressure1, Pressure2, Brix, Weight, DA, DA2) VALUES  (?, ?, ?, ?, ?, ?, ?, ?, ?)"))) {
    die ("Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error);
}

if (!mysqli_stmt_bind_param($stmt, 'iiissssss', $testID, $Retain, $Num, $Pressure1, $Pressure2, $Brix, $Weight, $DA, $DA2)) {
    die ("Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error);
}

// insert samples of the test entry
//Set Vars and Execute
for ($Num = 1; $Num <= $_POST['NumSamples']; $Num++) {
    $Pressure1 = $_POST['pressure' . $Num . '-1'];
    $Pressure2 = $_POST['pressure' . $Num . '-2'];
    $Weight = $_POST['weight' . $Num];
    $DA = $_POST['DA' . $Num . '-1'];
    $DA2 = $_POST['DA' . $Num . '-2'];
    if (($_POST['brix' . $Num])) {
        $Brix = $_POST['brix' . $Num];
    } else {
        $Brix = null;
    }
    if (!mysqli_stmt_execute($stmt)) {
        die ("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
    }
}
echo "<script>location.replace('index.php?ph-block=$PK#preharvest')</script>";
