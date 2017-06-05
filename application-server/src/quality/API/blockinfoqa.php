<?php
//fetches block info by Block ID
include '../../config.php';
$mysqli2 = mysqli_connect($dbhost, $dbusername, $dbpassword, $growerDB);
$pk = mysqli_real_escape_string($mysqli,$_GET['q']);
$query=mysqli_query($mysqli2,"select Grower as grower, VarDesc as variety, `Str Desc` as strain, ifnull(FarmDesc,'Unknown') as farm, ifnull(BlockDesc,'Unknown') as block from `crop-estimates` where isDeleted <> 1 and `PK`=$pk");
if(mysqli_num_rows($query)== '0'){die(json_encode(array('Error' => 'NULL')));}
echo json_encode(mysqli_fetch_assoc($query));
