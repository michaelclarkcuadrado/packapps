<?
require '../config.php';

$RT = mysqli_real_escape_string($mysqli,$_POST['RT']);
if($RT == ''){
    die("<html style='text-align: center'><meta name='viewport' content='width=device-width, initial-scale=1'><h1 style='color: red'>NOT RECEIVED.</h1><h3>This is not a valid RT.</h3><br> <a href='' onclick='window.history.back();'><-- Go Back</a></html>");

}
$check_exists_query = mysqli_query($mysqli, "SELECT `StarchFinished` FROM `quality_InsepctedRTs` WHERE RTNum='". $RT ."'");
if(mysqli_fetch_assoc($check_exists_query)['StarchFinished'] > 0){
    die("<html style='text-align: center'><meta name='viewport' content='width=device-width, initial-scale=1'><h1 style='color: red'>NOT RECEIVED.</h1><h3>This RT has already received starch testing.</h3><br> <a href='' onclick='window.history.back();'><-- Go Back</a></html>");
}
mysqli_query($mysqli, "UPDATE quality_InspectedRTs SET `StarchFinished` = 1 WHERE RTNum='" . $RT . "'");

//move starch photo
$Filename = "RT-" . $RT . "-starch.jpg";
packapps_uploadToS3($availableBuckets['quality'], $_FILES['starchupload']['tmp_name'], $Filename);

//Prepare Statement
$stmt = mysqli_prepare($mysqli, "UPDATE `quality_AppleSamples` SET `Starch`=? WHERE `RT#`=? AND SampleNum=?");
mysqli_stmt_bind_param($stmt, 'sii', $Starch, $RT, $SampleNum);


//always execute first 5

for($i = 1; $i <= 10; $i++){
    $SampleNum = $i;
    $Starch = $_POST['Starch'.$i];
    mysqli_stmt_execute($stmt);
}

echo "<script>location.replace('mobilestarch.php?ph=$RT')</script>";
