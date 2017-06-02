<?
include_once("../Classes/Mobile_Detect.php");
$Detect=new Mobile_Detect();
include '../../config.php';
$mysqli2 = mysqli_connect($dbhost,$dbusername,$dbpassword,$growerDB);
if (mysqli_connect_errno($mysqli))
{
    die("Failed to connect to MySQL: " . mysqli_connect_error(). "<br>Try again. If it keeps happening, reboot the server.");}

//pull info
$array=explode('*', $_POST['ID']);
$PK=$array[0];
$Date=$array[1];

//move starch photo
if($_FILES['starchupload']['tmp_name'] != ""){
$Filename= "../assets/uploadedimages/preharvest/ID".$PK."--".date('j-M-Y')."--starch.jpg";
if (file_exists($Filename)) {die("This block has already been done today!");};
if ($_FILES["starchupload"]["size"] > 5000000){die('File is too large.');};
move_uploaded_file($_FILES['starchupload']['tmp_name'], $Filename);}


//Prepare Statement
$stmt=mysqli_prepare($mysqli2, "update `Preharvest_Samples` set `isStarchInspected`=1, `Starch`=? where `PK`=? and date(`Date`)=? and SampleNum=?");
mysqli_stmt_bind_param($stmt, 'siss', $Starch, $PK, $Date, $SampleNum);

//always execute first 5
$SampleNum=1;
$Starch = $_POST['Starch1'];
mysqli_stmt_execute($stmt);

$SampleNum=2;
$Starch = $_POST['Starch2'];
mysqli_stmt_execute($stmt);

$SampleNum=3;
$Starch = $_POST['Starch3'];
mysqli_stmt_execute($stmt);

$SampleNum=4;
$Starch = $_POST['Starch4'];
mysqli_stmt_execute($stmt);

$SampleNum=5;
$Starch = $_POST['Starch5'];
mysqli_stmt_execute($stmt);


if ($_POST['moreappel'] == 'yes') {

    $SampleNum=6;
    $Starch = $_POST['Starch6'];
    mysqli_stmt_execute($stmt);

    $SampleNum=7;
    $Starch = $_POST['Starch7'];
    mysqli_stmt_execute($stmt);

    $SampleNum=8;
    $Starch = $_POST['Starch8'];
    mysqli_stmt_execute($stmt);

    $SampleNum=9;
    $Starch = $_POST['Starch9'];
    mysqli_stmt_execute($stmt);

    $SampleNum=10;
    $Starch = $_POST['Starch10'];
    mysqli_stmt_execute($stmt);
}
    echo "<script>location.replace('mobilestarch.php?ph=$PK')</script>";

