<?php
include '../../config.php';
packapps_authenticate_user('quality');
/*
 * DataTables example server-side processing script.
 *
 * Please note that this script is intentionally extremely simply to show how
 * server-side processing can be implemented, and probably shouldn't be used as
 * the basis for a large complex system. It is suitable for simple use cases as
 * for learning.
 *
 * See http://datatables.net/usage/server-side for full details on the server-
 * side processing requirements of DataTables.
 *
 * @license MIT - http://datatables.net/license_mit
 */

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * Easy set variables
 */

// DB table to use
$table = 'grower_gfbvs-listing';

// Table's primary key
$primaryKey = 'PK';

// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier.
$columns = array(
    array( 'db' => 'PK', 'dt' => 0 ),
    array( 'db' => 'commodity_name', 'dt' => 1 ),
    array( 'db' => 'GrowerName',  'dt' => 2 ),
    array( 'db' => 'farmName',   'dt' => 3 ),
    array( 'db' => 'BlockDesc',     'dt' => 4 ),
    array( 'db' => 'VarietyName',     'dt' => 5 ),
    array( 'db' => 'strainName',     'dt' => 6 ),
);

// SQL server connection information
$sql_details = array(
    'user' => $dbusername,
    'pass' => $dbpassword,
    'db'   => $operationsDatabase,
    'host' => $dbhost
);

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * If you just want to use the basic configuration for DataTables with PHP
 * server-side, there is no need to edit below this line.
 */

require( '../Classes/ssp.php' );

echo json_encode(
    SSP::simple( $_GET, $sql_details, $table, $primaryKey, $columns )
);