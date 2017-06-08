<?php
/**
 * Created by PhpStorm.
 * User: Michael Clark-Cuadrado
 * Date: 6/29/2016
 * Time: 12:20 PM
 */
include '../../config.php';
if (isset($_GET['q']))
{
    $s = mysqli_real_escape_string($mysqli, $_GET['q']);
    echo json_encode(mysqli_fetch_all(mysqli_query($mysqli, "SELECT * FROM purchasing_Suppliers WHERE Name LIKE '%".$s."%' OR ContactName LIKE '%".$s."%' OR ContactEmail LIKE '%".$s."%' OR Name LIKE '%".$s."%' OR InternalContact LIKE '%".$s."%' ORDER BY Name ASC"), MYSQLI_ASSOC));
}
