<?php
include '../config.php';
?>
<html>
<title>User Management</title>
<meta name="viewport" content="width=device-width, initial-scale=1 user-scalable=yes">
<style type="text/css">
    html {
        text-align: center;
        overflow-x: scroll;
    }

    td, th, tr {
        border: solid black 1px;
        text-align: center;
    }
</style>
<h1>Admin Panel</h1>
<hr>
<?

//security check
//authentication
if (!isset($_COOKIE['auth']) || !isset($_COOKIE['username'])) {
    die("<script>window.location.replace('/')</script>");
} else if (!hash_equals($_COOKIE['auth'], crypt($_COOKIE['username'], $securityKey))) {
    die("<script>window.location.replace('/')</script>");
} else {
    $SecuredUserName = mysqli_real_escape_string($mysqli, $_COOKIE['username']);
    $checkAllowed = mysqli_fetch_array(mysqli_query($mysqli, "SELECT `Real Name`, Role, isSystemAdministrator as isAdmin, allowedQuality FROM master_users JOIN quality_UserData ON master_users.username=quality_UserData.UserName WHERE master_users.username = '$SecuredUserName'"));
    if (!$checkAllowed['allowedQuality'] > 0 || !$checkAllowed['isAdmin'] > 0) {
        die ("<script>window.location.replace('/')</script>");
    } else {
        $RealName = $checkAllowed;
        $Role = $checkAllowed['Role'];
    }
}
// end authentication

?>
<?
//delete email from list
if ($_GET['delemail'] <> '') {
    mysqli_query($mysqli, "DELETE FROM AlertEmails WHERE ID='" . mysqli_real_escape_string($mysqli, $_GET['delemail']) . "'");
    echo "<mark>Email Removed!</mark>";
}

//subscribe new email
if ($_POST['newEmailAddress'] <> '') {
    mysqli_query($mysqli, "INSERT INTO AlertEmails (FullName, EmailAddress) VALUES ('" . mysqli_real_escape_string($mysqli, $_POST['newFullName']) . "','" . mysqli_real_escape_string($mysqli, $_POST['newEmailAddress']) . "'); ") or error_log(mysqli_error($mysqli));
    echo "<br><mark>New User Created!</mark>";
}

$email_list = mysqli_query($mysqli, "SELECT ID, FullName, EmailAddress FROM AlertEmails");
?>
<h2>Email Alert Subscription List</h2>
<h3>Subscribe new email to alerts</h3>
<table style="margin-left: auto; margin-right: auto; border: dotted black 1px">
    <form method="post" action="usermgmt.php">
        <tr>
            <td>Name</td>
            <td>Email Address</td>
            <td></td>
        </tr>
        <tr>
            <td><input type="text" name="newFullName" required></td>
            <td><input type="email" name="newEmailAddress" required></td>
            <td colspan="5"><input type="submit"></td>
        </tr>
    </form>
</table>
<br>
<h3>Current Email List</h3>
<table style="margin-left: auto; margin-right: auto; border: solid black 1px">
    <thead>
    <tr>
        <th> Name</th>
        <th> Email</th>
        <th> Delete from List</th>
    </thead>
    <? while ($emaildata = mysqli_fetch_assoc($email_list)) {
        echo "<tr><td><b>" . $emaildata['FullName'] . "</b></td><td><b>" . $emaildata['EmailAddress'] . "</b></td><td><a href='usermgmt.php?delemail=" . $emaildata['ID'] . "'>Unsubscribe</a></td></tr>";
    } ?>
</table>
<a href='/controlPanel.php'>
    <button>Go back</button>
</a>
</html>
