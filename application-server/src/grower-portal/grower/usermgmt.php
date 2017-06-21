<!DOCTYPE html>
<title>Grower Control Panel Control Panel</title>
<meta name="viewport" content="width=device-width, initial-scale=1 user-scalable=yes">
<style type="text/css">
    html {
        text-align: center;
        overflow-x: scroll;
        background-color: #ecf1f1;
    }

    td, th, tr {
        border: solid black 1px;
        text-align: center;
    }
</style>
<h1>Admin Panel</h1>
<?
include_once 'incrementYearInDB.php';
include '../config_grower.php';
$year = new Year();
if (!$year->isCurrent($mysqli)) {
    $year->increment($mysqli);
}
//security check
$namecnct = mysqli_query($mysqli, "SELECT isAdmin FROM `GrowerData` WHERE GrowerCode='" . $_SERVER['PHP_AUTH_USER'] . "'");
$isAllowed = mysqli_fetch_array($namecnct);
if ($isAllowed == 0) {
    die ("Unauthorized.");
}

//delete existing user
if ($_GET['del']) {
    if ($_GET['del'] == $_SERVER['PHP_AUTH_USER']) {
        die("<br>Don't delete yourself.<br><br><button onclick='window.history.back()'>Go Back</button>");
    }
    exec("htpasswd -D /etc/apache2/.growerpasswds " . escapeshellarg($_GET['del']));
    mysqli_query($mysqli, "DELETE FROM GrowerData WHERE GrowerCode='" . mysqli_real_escape_string($mysqli, $_GET['del']) . "'") or die(mysqli_error($mysqli));
    echo "<mark>User Deleted!</mark>";
}

//create new user
if ($_POST['GrowerName']) {
    exec("htpasswd -b /etc/apache2/.growerpasswds " . escapeshellarg($_POST['GrowerCode']) . " " . escapeshellarg($_POST['grwrpasswd']));
    mysqli_query($mysqli, "INSERT INTO GrowerData (GrowerCode, GrowerName, Password, isAdmin) VALUES ('" . mysqli_real_escape_string($mysqli, $_POST['GrowerCode']) . "', '" . mysqli_real_escape_string($mysqli, $_POST['GrowerName']) . "', '" . mysqli_real_escape_string($mysqli, $_POST['grwrpasswd']) . "', '" . ($_POST['newisadmin'] == 'Yes' ? '1' : '0') . "'); ") or die("Could not add grower! " . mysqli_error($mysqli));
    echo "<br><mark>New User Created!</mark>";
}

//Force Re-sync of BULKRT
if ($_GET['sync']) {
    mysqli_query($mysqli, "TRUNCATE TABLE BULKRTCSV;") or die("Could not force sync! " . mysqli_error($mysqli));
    echo "<br><mark>BULKRT was cleared. Allow 1-2 minutes to re-sync.</mark>";
}

//change password
if ($_POST['password']) {
    mysqli_query($mysqli, "UPDATE GrowerData SET `Password`='" . mysqli_real_escape_string($mysqli, $_POST['password']) . "' WHERE GrowerCode='" . mysqli_real_escape_string($mysqli, $_POST['username']) . "'");
    exec("htpasswd -b /etc/apache2/.growerpasswds " . escapeshellarg($_POST['username']) . " " . escapeshellarg($_POST['password']));
    echo "<br><mark>Password Change Successful!</mark>";
}

//print for user
$userlist = mysqli_query($mysqli, "SELECT GrowerName, GrowerCode, ifnull(Password, 'Unknown') as Password, isAdmin FROM GrowerData ORDER BY isAdmin DESC, GrowerCode");
?>

<!-- New Features list -->
<div
    style="font-size: small;border: solid black 1px; width: 35%; margin-left: auto; margin-right: auto; background-color: lemonchiffon">
    <b>New Features 2/18/17</b>
    <ul>
        <li>Fixed bugs for new year rollover.</li>
    </ul>
</div>

<hr><h2>Site Options</h2>
<a href="<? echo $piwikHost ?>">
    <button>Access Grower Portal Usage and Analytics</button>
</a><br>Username: <? echo $piwikUser ?>  || Password: <? echo $piwikPassword ?><br><br>
<a href="growerfileshare/">
    <button>Edit the Grower File Share</button>
</a><br><br>
<a href="growerCalendar.php">
    <button>Access the picking calendar</button>
</a><br><br>
<button onclick="location.search='?sync=1'">Force BULKRT to Re-sync</button>
<br><Br>
<button onclick="logout();">Log Out</button>
<br><br>
<hr>
<h2>Grower Account Management</h2><br>
<p>
<div style="border: solid black 1px; width: 45%; margin-left: auto; margin-right: auto; background-color: lemonchiffon">
    <b>Note:</b><br>
    If the user is marked as <? echo $companyName ?> Staff, they will not have access to the grower portal and instead
    will have
    access to this screen. Be careful not to assign this to a new grower.
</div>
<br><br>
<h3>New Grower</h3>
<table style="margin-left: auto; margin-right: auto; border: dotted black 1px; border-collapse: collapse">
    <form method="post" action="usermgmt.php">
        <tr>
            <td>Grower Name</td>
            <td>Grower Code</td>
            <td>Password</td>
            <td><? echo $companyName ?> Staff</td>
        </tr>
        <tr>
            <td><input type="text" name="GrowerName"></td>
            <td><input type="text" name="GrowerCode" required></td>
            <td><input type="password" name="grwrpasswd" required></td>
            <td><input type="checkbox" name="newisadmin" value="Yes"></td>
        </tr>
        <tr>
            <td colspan="5"><input type="submit"></td>
        </tr>
    </form>
</table>
<br><br>
<h3>Current Growers</h3>
<table style="margin-left: auto; margin-right: auto; border: solid black 1px">
    <thead>
    <tr>
        <th>Grower Name</th>
        <th>Grower Code</th>
        <th>Type</th>
        <th>View As</th>
        <th colspan="2">Actions</th>
    </tr>
    </thead>
    <? while ($userarray = mysqli_fetch_assoc($userlist)) {
        echo "<tr><td><b>" . $userarray['GrowerName'] . "</b></td><td><form action='usermgmt.php' method='post'><input type='text' name='username' value='" . $userarray['GrowerCode'] . "' readonly></td><td>" . ($userarray['isAdmin'] == '1' ? '<mark>Staff</mark>' : 'Grower') . "</td><td>" . ($userarray['isAdmin'] == '1' ? '' : '<a href=\'/grower?pretend=' . $userarray['GrowerCode'] . '\'>Log In</a>') . "</td><td>Current Password: <b>" . ($userarray['isAdmin'] == 1 ? '' : $userarray['Password']) . "</b><br><br>Change Password to: <input type='password' name='password' value=''><br><input type='submit'></form><br></td><td><a href='usermgmt.php?del=" . $userarray['GrowerCode'] . "'>Delete</a></td></tr>";
    } ?>
</table>
<script>
    function logout() {
        var xmlhttp;
        if (window.XMLHttpRequest) {
            xmlhttp = new XMLHttpRequest();
        }
        // code for IE
        else if (window.ActiveXObject) {
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        if (window.ActiveXObject) {
            // IE clear HTTP Authentication
            document.execCommand("ClearAuthenticationCache", false);
            window.location.href = 'logout/logoutheader.php';
        } else {
            xmlhttp.open("GET", 'logout/logoutheader.php', true, "User Name", "logout");
            xmlhttp.send("");
            xmlhttp.onreadystatechange = function () {
                if (xmlhttp.readyState == 4) {
                    window.location.href = 'logout/logoutheader.php';
                }
            }


        }


        return false;
    }
</script>
</html>
