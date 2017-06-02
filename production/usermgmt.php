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
<?
include '../config.php';
//security check
//authentication
if (!isset($_COOKIE['auth']) || !isset($_COOKIE['username'])) {
    die("<script>window.location.replace('/')</script>");
} else if (!hash_equals($_COOKIE['auth'], crypt($_COOKIE['username'], $securityKey))) {
    die("<script>window.location.replace('/')</script>");
} else {
    $SecuredUserName = mysqli_real_escape_string($mysqli, $_COOKIE['username']);
    $checkAllowed = mysqli_fetch_array(mysqli_query($mysqli, "SELECT `Real Name` as UserRealName, Role, isSectionManager as isAdmin, allowedProduction FROM master_users JOIN production_UserData ON master_users.username=production_UserData.UserName WHERE master_users.username = '$SecuredUserName'"));
    if (!$checkAllowed['allowedProduction'] > 0) {
        die ("<script>window.location.replace('/')</script>");
    } else {
        $isAllowed = $checkAllowed;
    }
}
// end authentication
if ($isAllowed[0] <> '1') {
    die("<br>UNAUTHORIZED. This page is for administrators only.");
}

?>
<h2>Production Users</h2><br>
<?php
//delete existing user
if ($_GET['del'] <> '') {
    if ($_GET['del'] == $_SERVER['PHP_AUTH_USER']) {
        die("<br>Don't delete yourself.");
    }
    exec("htpasswd -D /etc/apache2/.production_passwds " . escapeshellarg($_GET['del']));
    mysqli_query($mysqli, "DELETE FROM production_Users WHERE UserName='" . mysqli_real_escape_string($mysqli, $_GET['del']) . "'");
    echo "<mark>User Deleted!</mark>";
}

//create new user
if ($_POST['newusername'] <> '') {
    if ($_POST['newrole'] <> 'Production' and $_POST['newisadmin'] == 'Yes') {
        die("<br>All admins must have production privileges.");
    }
    exec("htpasswd -b /etc/apache2/.production_passwds " . escapeshellarg($_POST['newusername']) . " " . escapeshellarg($_POST['newpasswd']));
    mysqli_query($mysqli, "INSERT INTO production_Users (UserName, UserRealName, Role, isAdmin) VALUES ('" . mysqli_real_escape_string($mysqli, $_POST['newusername']) . "', '" . mysqli_real_escape_string($mysqli, $_POST['newuserrealname']) . "', '" . mysqli_real_escape_string($mysqli, $_POST['newrole']) . "', '" . ($_POST['newisadmin'] == 'Yes' ? '1' : '0') . "'); ");
    echo "<br><mark>New User Created!</mark>";
}

//manage existing user
if ($_POST['password'] <> '') {
    escapeshellarg($_POST['password']);
    exec("htpasswd -b /etc/apache2/.production_passwds " . $_POST['username'] . " " . $_POST['password']);
    echo "<br><mark>Password Change Successful!</mark>";
}
if ($_POST['Role'] <> '') {
    if ($_SERVER['PHP_AUTH_USER'] == $_POST['username']) {
        die("<br>Don't try to change your own role, you'll be locked out!");
    }
    mysqli_query($mysqli, "UPDATE production_Users SET Role=CASE WHEN isAdmin <> 1 THEN '" . mysqli_real_escape_string($mysqli, $_POST['Role']) . "' ELSE 'Production' END WHERE UserName='" . mysqli_real_escape_string($mysqli, $_POST['username']) . "'");
    echo "<br><mark>Role Change Successful</mark>";
}

//print for user
$userlist = mysqli_query($mysqli, "SELECT UserName, UserRealName, Role, isAdmin, date(DateCreated) AS DateCreated FROM production_Users ORDER BY isAdmin DESC, UserRealName ASC");
?>
<p>
<div style="border: solid black 1px; width: 45%; margin-left: auto; margin-right: auto; background-color: lemonchiffon">
    <b>Privileges Key:</b><br>
    <b>Admin</b> = Highest privilege and can see this screen. Have Production privilege by default.<br>
    <b>Production</b> = Can create, edit and delete runs.<br>
    <b>Read Only</b> = Can see live runs and participate in chat.<br>
    <b>Restricted</b> = 10 foot interface for the displays. No chat or QA.<br></div>
<br><br>
<a href='/'>
    <button>Go back</button>
</a>
<br><br>
<h3>New User</h3>
<table style="margin-left: auto; margin-right: auto; border: dotted black 1px">
    <form method="post" action="usermgmt.php">
        <tr>
            <td>Real Name</td>
            <td>User Name</td>
            <td>Password</td>
            <td>Privilege</td>
            <td>Admin?</td>
        </tr>
        <tr>
            <td><input type="text" name="newuserrealname"></td>
            <td><input type="text" name="newusername" required></td>
            <td><input type="password" name="newpasswd" required></td>
            <td><label><input type='radio' name='newrole' value='Production' required> Production</label>
                <label><input type='radio' name='newrole' value='ReadOnly'>Read Only</label>
                <label><input type='radio' name='newrole' value='Restricted'>Restricted</label>
            </td>
            <td><input type="checkbox" name="newisadmin" value="Yes"></td>
        </tr>
        <tr>
            <td colspan="5"><input type="submit"></td>
        </tr>
    </form>
</table>
<br><br>
<h3>Exisitng Users</h3>
<table style="margin-left: auto; margin-right: auto; border: solid black 1px">
    <thead>
    <tr>
        <th>Real Name</th>
        <th>User Name</th>
        <th>Privilege</th>
        <th>Admin</th>
        <th>Date Created</th>
        <th colspan="2">Actions</th>
    </tr>
    </thead>
    <? while ($userarray = mysqli_fetch_assoc($userlist)) {
        echo "<tr><td><b>" . $userarray['UserRealName'] . "</b></td><td><form action='usermgmt.php' method='post'><input type='text' name='username' value='" . $userarray['UserName'] . "' readonly></td><td>" . $userarray['Role'] . "</td><td>" . ($userarray['isAdmin'] == '1' ? '<mark>Yes</mark>' : 'No') . "</td><td>" . $userarray['DateCreated'] . "</td><td><a href='usermgmt.php?del=" . $userarray['UserName'] . "'>Delete</a></td><td>Switch to: <label><input type='radio' name='Role' value='Production'> Production </label><label><input type='radio' name='Role' value='Read Only'> Read Only </label><label><input type='radio' name='Role' value='Restricted'> Restricted </label><br><br> Change Password to: <input type='password' name='password' value=''><br><input type='submit'></form></td></tr>";
    } ?>
</table>
<br><br>
<a href='/'>
    <button>Go back</button>
</a>
</html>