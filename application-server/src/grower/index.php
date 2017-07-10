<script type="text/javascript">
    <?php
    if(isset($_COOKIE['grower']) && $_COOKIE['grower'] == 'true'){
        echo "window.location = \"portal/\"";
    } else {
        echo "window.location = \"manage/\"";
    }
    ?>
</script>
