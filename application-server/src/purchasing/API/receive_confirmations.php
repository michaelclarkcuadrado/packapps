<?php
/**
 * Created by PhpStorm.
 * User: Michael Clark-Cuadrado
 * Date: 7/14/2016
 * Time: 11:53 AM
 */

//Takes a file in $_FILES[0] and an ID in $_POST['ID'] in the form of confirmation_PURCHASEIDNUMBER
include '../../config.php';
packapps_authenticate_user('purchasing');
if (isset($_FILES[0]) && isset($_POST['ID'])) {
    if (strpos($_POST['ID'], 'confirmation_') !== false) {
        packapps_uploadToS3($availableBuckets['purchasing'], $_FILES[0]['tmp_name'], 'purchasing-purchaseid-'.str_replace('confirmation_', '', $_POST['ID']).'-confirmation.pdf');
        mysqli_query($mysqli, "UPDATE operationsData.purchasing_purchase_history SET invoice_attached=1, DateReceived=DEFAULT WHERE Purchase_ID=".mysqli_real_escape_string($mysqli, str_replace('confirmation_', '', $_POST['ID'])));
    }
}