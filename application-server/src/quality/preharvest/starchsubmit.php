<?
include '../../config.php';
$mysqli2 = mysqli_connect($dbhost,$dbusername,$dbpassword,$growerDB);
if (mysqli_connect_errno($mysqli))
{
    die("Failed to connect to MySQL: " . mysqli_connect_error(). "<br>Try again. If it keeps happening, reboot the server.");}

//pull info
$array=explode('*', $_POST['ID']);
$PK=mysqli_real_escape_string($mysqli2,$array[0]);
$Date=mysqli_real_escape_string($mysqli2, $array[1]);

//move starch photo
if($_FILES['starchupload']['tmp_name'] != "") {
    $check_uploaded = mysqli_query($mysqli2, "SELECT * FROM `Preharvest_Samples` WHERE `isStarchInspected`=1 AND `PK`='$PK' AND date(`Date`)='$Date'");
    if(mysqli_num_rows($check_uploaded) > 0){
        error_log("Failure, that preharvest test already has starch data.");
        echo "<script>location.replace('mobilestarch.php')</script>";
    }
    $Filename= "quality-preharvest-ID".$PK."-".date('j-M-Y')."-starch.jpg";
    packapps_uploadToS3($availableBuckets['quality'], $_FILES['starchupload']['tmp_name'], $Filename);
} else {
    error_log("Failure, no preharvest starch photo uploaded");
    echo "<script>location.replace('mobilestarch.php')</script>";
}


//Prepare Statement
$stmt=mysqli_prepare($mysqli2, "update `Preharvest_Samples` set `isStarchInspected`=1, `Starch`=? where `PK`=? and date(`Date`)=? and SampleNum=?");
mysqli_stmt_bind_param($stmt, 'siss', $Starch, $PK, $Date, $SampleNum);

//always insert the first 5, execute 10 on conditional
$cap = 5;
if($_POST['moreappel'] == 'yes'){
    $cap += 5;
}

for($i = 1; $i <= $cap; $i++){
    $SampleNum=$i;
    $Starch = $_POST['Starch'.$i];
    mysqli_stmt_execute($stmt);
}

echo "<script>location.replace('mobilestarch.php?ph=$PK')</script>";

