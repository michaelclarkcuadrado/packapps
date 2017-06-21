<html style="text-align: center">
<script type = "text/javascript" >
    //disable going back
    history.pushState(null, null, 'logoutheader.php');
    window.addEventListener('popstate', function(event) {
        history.pushState(null, null, 'logoutheader.php');
    });
</script>
<title>Grower Portal</title>
<h1>You are now logged out.</h1>
<p><a href="../">Log back in</a></p>
</html>