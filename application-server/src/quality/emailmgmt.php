<?php
require '../config.php';
$userData = packapps_authenticate_user();
if($userData['isSystemAdministrator'] == 0){
    die("<script>window.location.replace('/')</script>");
}
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
<?php
//delete email from list
if ($_GET['delemail'] <> '') {
    mysqli_query($mysqli, "DELETE FROM quality_AlertEmails WHERE ID='" . mysqli_real_escape_string($mysqli, $_GET['delemail']) . "'");
    echo "<mark>Email Removed!</mark>";
}

//subscribe new email
if ($_POST['newEmailAddress'] <> '') {
    mysqli_query($mysqli, "INSERT INTO quality_AlertEmails (FullName, EmailAddress) VALUES ('" . mysqli_real_escape_string($mysqli, $_POST['newFullName']) . "','" . mysqli_real_escape_string($mysqli, $_POST['newEmailAddress']) . "'); ") or error_log(mysqli_error($mysqli));
    echo "<br><mark>New User Created!</mark>";
}

$email_list = mysqli_query($mysqli, "SELECT ID, FullName, EmailAddress FROM quality_AlertEmails");
?>
<a href='/controlPanel.php'>
    <button>Go back</button>
</a>
<hr>
<h2>Email Alert Subscription List</h2>
<h3>Subscribe new email to alerts</h3>
<table style="margin-left: auto; margin-right: auto; border: dotted black 1px">
    <form method="post" action="emailmgmt.php">
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
        echo "<tr><td><b>" . $emaildata['FullName'] . "</b></td><td><b>" . $emaildata['EmailAddress'] . "</b></td><td><a href='emailmgmt.php?delemail=" . $emaildata['ID'] . "'>Unsubscribe</a></td></tr>";
    } ?>
</table>
<hr>
<a href='/controlPanel.php'>
    <button>Go back</button>
</a>
</html>
