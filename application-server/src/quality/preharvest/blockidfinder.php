<html>
<title>Block Identifier</title>
<table id="IDFinder" class="display" cellspacing="0" width="100%">
    <thead>
    <tr>
        <th>Block ID</th>
        <th>Commodity</th>
        <th>Grower</th>
        <th>Farm</th>
        <th>Block</th>
        <th>Variety</th>
        <th>Strain</th>
    </tr>
    </thead>

    <tfoot>
    <tr>
        <th>Block ID</th>
        <th>Commodity</th>
        <th>Grower</th>
        <th>Farm</th>
        <th>Block</th>
        <th>Variety</th>
        <th>Strain</th>
    </tr>
    </tfoot>
</table>
<link rel="stylesheet" href="../assets/css/jquery.dataTables.min.css">
<script src="../assets/js/jquery.min.js"></script>
<script src="../assets/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function() {
        $('#IDFinder').dataTable( {
            "processing": true,
            "serverSide": true,
            "ajax": "../API/blockiddata.php"
        } );
    } );
</script>
</html>