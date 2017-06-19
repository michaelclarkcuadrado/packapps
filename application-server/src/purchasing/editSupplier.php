<?php
/**
 * Created by PhpStorm.
 * User: Michael Clark-Cuadrado
 * Date: 6/30/2016
 * Time: 9:43 AM
 */
require '../config.php';

//authentication
if (!isset($_COOKIE['auth']) || !isset($_COOKIE['username'])) {
    die("<script>window.location.replace('/')</script>");
} else if (!hash_equals($_COOKIE['auth'], crypt($_COOKIE['username'], $securityKey))) {
    die("<script>window.location.replace('/')</script>");
} else {
    $SecuredUserName = mysqli_real_escape_string($mysqli, $_COOKIE['username']);
}

if (isset($_GET['supplier'])) {
    $supplierInfo = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT * FROM purchasing_Suppliers WHERE SupplierID = '" . mysqli_real_escape_string($mysqli, $_GET['supplier']) . "'"));
} else {
    die("<script>window.close();</script>");
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0">
    <title>Edit Supplier</title>
</head>
<link rel="stylesheet"
      href="https://fonts.googleapis.com/css?family=Roboto:regular,bold,italic,thin,light,bolditalic,black,medium&amp;lang=en">
<link rel="stylesheet" href="../styles-common/materialIcons/material-icons.css">
<link rel="stylesheet" href="../styles-common/material.min.css">
<link rel="stylesheet" href="../styles-common/styles.css">
<div class="widthfixer mdl-grid demo-cards">
            <div id="newSupplierForm" class="mdl-cell mdl-cell--12-col mdl-card mdl-shadow--4dp">
                <div class="mdl-card__title mdl-color--deep-purple-300">
                    <h2 style="color:white" class="mdl-card__title-text">Edit Supplier</h2>
                </div>
                <div class="mdl-card__supporting-text">
                    <form id="newSupplierSubmitter" class='mdl-grid'>
                        <input type="hidden" value="" name='editID' id="SupplierID">
                        <div
                            class="mdl-cell mdl-cell--4-col mdl-textfield--floating-label mdl-textfield mdl-js-textfield">
                            <input required class="mdl-textfield__input" type="text" name="newCompanyName"
                                   id="newCompanyName">
                            <label class="mdl-textfield__label" for="newCompanyName">Company Name</label>
                        </div>
                        <div
                            class="mdl-cell mdl-cell--4-col mdl-textfield--floating-label mdl-textfield mdl-js-textfield">
                            <input required class="mdl-textfield__input" type="text" name="newContactName"
                                   id="newContactName">
                            <label class="mdl-textfield__label" for="newContactName">Contact Name</label>
                        </div>
                        <div
                            class="mdl-cell mdl-cell--4-col mdl-textfield--floating-label mdl-textfield mdl-js-textfield">
                            <input required class="mdl-textfield__input" type="text" name="newContactPhone"
                                   id="newContactPhone">
                            <label class="mdl-textfield__label" for="newContactPhone">Contact Phone Number</label>
                        </div>
                        <div
                            class="mdl-cell mdl-cell--4-col mdl-textfield--floating-label mdl-textfield mdl-js-textfield">
                            <input required class="mdl-textfield__input" type="text" name="newContactEmail"
                                   id="newContactEmail">
                            <label class="mdl-textfield__label" for="newContactEmail">Contact Email</label>
                        </div>
                        <div
                            class="mdl-cell mdl-cell--4-col mdl-textfield--floating-label mdl-textfield mdl-js-textfield">
                            <input required class="mdl-textfield__input" type="text" name="newInternalContact"
                                   id="newInternalContact">
                            <label class="mdl-textfield__label" for="newInternalContact">Internal Contact</label>
                        </div>
                        <button class="mdl-cell mdl-cell--4-col mdl-button mdl-js-button mdl-button--raised">
Submit Edits
</button>
                    </form>
                </div>
            </div>
        </div>
<script src="../scripts-common/material.min.js"></script>
<script src="../scripts-common/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#newSupplierSubmitter').submit(function(e) {
                $.post('API/editSupplierSubmit.php', $('#newSupplierSubmitter').serialize(), function() {
                    $('#newSupplierSubmitter')[0].reset();
                    window.close();
                });
                e.preventDefault();
            });
            $('#SupplierID').val(<?echo $supplierInfo['SupplierID']?>);
            $('#newCompanyName').val("<?echo $supplierInfo['Name']?>");
            $('#newContactName').val("<?echo $supplierInfo['ContactName']?>");
            $('#newContactEmail').val("<?echo $supplierInfo['ContactEmail']?>");
            $('#newContactPhone').val("<?echo $supplierInfo['ContactPhone']?>");
            $('#newInternalContact').val("<?echo $supplierInfo['InternalContact']?>");

        });
    </script>
</html>