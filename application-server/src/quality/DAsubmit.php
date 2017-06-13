<?php
require '../config.php';

$DA = 0;
$DA2 = 0;

$NumSamples = substr($_POST['RT'], strpos($_POST['RT'], ":")+1);
$RT = substr($_POST['RT'],0, strpos($_POST['RT'], ":"));

mysqli_query($mysqli, "UPDATE quality_InspectedRTs SET `DAFinished` = '1' WHERE RTNum='" . $RT . "'");

//Prepare Statements and execute queries
$stmt = mysqli_prepare($mysqli, "UPDATE `quality_AppleSamples` SET DA=?, DA2=? WHERE `RT#`=? AND SampleNum=?");
mysqli_stmt_bind_param($stmt, 'ddii', $DA, $DA2, $RT, $SampleNum);

for($i = 1; $i <= $NumSamples; $i++)
{
    $SampleNum=$i;
    $DA = $_POST[$i.'A'];
    $DA2 = $_POST[$i.'B'];
    mysqli_stmt_execute($stmt);
}
echo "<script>location.replace('DA.php?da=" . $RT . "')</script>";