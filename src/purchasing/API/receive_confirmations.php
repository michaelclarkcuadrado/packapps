<?php
/**
 * Created by PhpStorm.
 * User: mac
 * Date: 7/14/2016
 * Time: 11:53 AM
 */
include '../../config.php';
if (isset($_FILES[0]) && isset($_POST['ID'])) {
    if (strpos($_POST['ID'], 'confirmation_') !== false) {
        if (!file_exists('../assets/Order_Confirmations/'. str_replace('confirmation_', '', $_POST['ID']) . '_confirm.pdf') && strpos($_FILES[0]['name'], '.pdf') !== false) {
            move_uploaded_file($_FILES[0]['tmp_name'], '../assets/Order_Confirmations/' . str_replace('confirmation_', '', $_POST['ID']) . '_confirm.pdf');
            mysqli_query($mysqli, "UPDATE operationsData.purchasing_purchase_history SET invoice_attached=1, DateReceived=DEFAULT WHERE Purchase_ID=".mysqli_real_escape_string($mysqli, str_replace('confirmation_', '', $_POST['ID'])));
        }
    }
}