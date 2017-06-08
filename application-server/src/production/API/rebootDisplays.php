<?php
/**
 * Created by PhpStorm.
 * User: Michael Clark-Cuadrado
 * Date: 8/1/2016
 * Time: 10:57 AM
 */
include '../../config.php';
//sends reboot signal to every display. Assumes they are raspberry pis running raspbian and sshd on port 22
$connectedDisplays = mysqli_query($mysqli, "SELECT IP_addr FROM production_ConnectedDisplays WHERE User_agent LIKE '%Linux%'");

while($IPs = mysqli_fetch_assoc($connectedDisplays)){
    $conn = ssh2_connect($IPs['IP_addr'], 22);
    ssh2_auth_password($conn, 'pi', 'raspberry');
    ssh2_exec($conn, 'sudo reboot');
    unset($conn);
}