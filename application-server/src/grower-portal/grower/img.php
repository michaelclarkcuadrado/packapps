<?php
/**
 * Created by PhpStorm.
 * User: MAC
 * Date: 8/13/2015
 * Time: 2:48 PM
 */
// define absolute path to image folder
if (isset($_GET['q'])) {
    $pic = 'http://192.168.1.61/quality/assets/uploadedimages/preharvest/' . $_GET['q'];
    header('Content-type: image/jpeg');
    fpassthru(fopen($pic, 'rb'));
    exit;
} else {
    die ("No image specified.");
}
