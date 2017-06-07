<?
include '../config.php';


$RT = mysqli_real_escape_string($mysqli,$_POST['RT']);
if($RT == ''){
    die("<html style='text-align: center'><meta name='viewport' content='width=device-width, initial-scale=1'><h1 style='color: red'>NOT RECEIVED.</h1><h3>This is not a valid RT.</h3><br> <a href='' onclick='window.history.back();'><-- Go Back</a></html>");

}
mysqli_query($mysqli, "UPDATE quality_InspectedRTs SET `StarchFinished` = 1 WHERE RTNum='" . $RT . "'");

//move starch photo
if ($_FILES['starchupload']['tmp_name'] != "") {
    $Filename = "/var/www/quality/assets/uploadedimages/" . $RT . "starch.jpg";
    if (file_exists($Filename)) {
        die("<html style='text-align: center'><meta name='viewport' content='width=device-width, initial-scale=1'><h1 style='color: red'>NOT RECEIVED.</h1><h3>This RT has already received starch testing.</h3><br> <a href='' onclick='window.history.back();'><-- Go Back</a></html>");
    };
    move_uploaded_file($_FILES['starchupload']['tmp_name'], $Filename);
}

//Prepare Statement
$stmt = mysqli_prepare($mysqli, "UPDATE `quality_AppleSamples` SET `Starch`=? WHERE `RT#`=? AND SampleNum=?");
mysqli_stmt_bind_param($stmt, 'sii', $Starch, $RT, $SampleNum);


//always execute first 5
$SampleNum = 1;
$Starch = $_POST['Starch1'];
mysqli_stmt_execute($stmt);

$SampleNum = 2;
$Starch = $_POST['Starch2'];
mysqli_stmt_execute($stmt);

$SampleNum = 3;
$Starch = $_POST['Starch3'];
mysqli_stmt_execute($stmt);

$SampleNum = 4;
$Starch = $_POST['Starch4'];
mysqli_stmt_execute($stmt);

$SampleNum = 5;
$Starch = $_POST['Starch5'];
mysqli_stmt_execute($stmt);

$SampleNum = 6;
$Starch = $_POST['Starch6'];
mysqli_stmt_execute($stmt);

$SampleNum = 7;
$Starch = $_POST['Starch7'];
mysqli_stmt_execute($stmt);

$SampleNum = 8;
$Starch = $_POST['Starch8'];
mysqli_stmt_execute($stmt);

$SampleNum = 9;
$Starch = $_POST['Starch9'];
mysqli_stmt_execute($stmt);

$SampleNum = 10;
$Starch = $_POST['Starch10'];
mysqli_stmt_execute($stmt);

echo "<script>location.replace('mobilestarch.php?ph=$RT')</script>";
