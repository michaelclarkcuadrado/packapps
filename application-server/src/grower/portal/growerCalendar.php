<!DOCTYPE HTML>
<html>
<head>
    <?php
    include '../../config.php';
    $adminauth = mysqli_query($mysqli, "SELECT isAdmin FROM grower_growerLogins WHERE GrowerCode='" . $_SERVER['PHP_AUTH_USER'] . "'");
    $admin = mysqli_fetch_array($adminauth);
    $namecnct = mysqli_query($mysqli, "SELECT GrowerName FROM `grower_growerLogins` WHERE GrowerCode='" . ($admin[0] == 1 && ($_GET['pretend']) ? $_GET['pretend'] : (isset($_GET['alt_acc']) ? base64_decode($_GET['alt_acc']) : $_SERVER['PHP_AUTH_USER'])) . "' LIMIT 1");
    $growername = mysqli_fetch_array($namecnct);

    echo "<title>Grower Calendar: " . $growername[0] . "</title>";
    ?>
    <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
    <meta name="description" content="<?echo $companyName?> Grower Control Panel"/>
    <meta name="keywords" content=""/>
    <!--[if lte IE 8]>
    <script src="css/ie/html5shiv.js"></script><![endif]-->
    <link rel="apple-touch-icon" href="apple-touch-icon.png">
    <link rel="icon" sizes="196x196" href="apple-touch-icon.png">
    <script src="js/jquery.min.js"></script>
    <script src="js/jquery.scrolly.min.js"></script>
    <script src="js/jquery.scrollzer.min.js"></script>
    <script src="js/moment.min.js"></script>
    <script src="js/fullcalendar.js"></script>
    <script src="js/skel.min.js"></script>
    <script src="js/skel-layers.min.js"></script>
    <script src="js/init.js"></script>
    <link rel="stylesheet" href="css/fullcalendar.css"/>
    <style>
        table{
            margin: 0
        }
    </style>
    <noscript>
        <link rel="stylesheet" href="css/skel.css"/>
        <link rel="stylesheet" href="css/style.css"/>
        <link rel="stylesheet" href="css/style-wide.css"/>
    </noscript>
    <!--[if lte IE 9]>
    <link rel="stylesheet" href="css/ie/v9.css"/><![endif]-->
    <!--[if lte IE 8]>
    <link rel="stylesheet" href="css/ie/v8.css"/><![endif]-->
</head>
<body>

<!-- Piwik -->
<script type="text/javascript">
    var _paq = _paq || [];
    _paq.push(['trackPageView']);
    _paq.push(['enableLinkTracking']);
    (function () {
        var u = "<?php echo $piwikHost?>/";
        _paq.push(['setTrackerUrl', u + 'piwik.php']);
        _paq.push(['setSiteId', 1]);
        _paq.push(['setUserId', '<?echo ($admin[0] == 1 && $_GET['pretend']) ? "Admin: " . $_SERVER['PHP_AUTH_USER'] . " logged in as " . addcslashes($growername[0], "'") : addcslashes($growername[0], "'")?>']);
        var d = document, g = d.createElement('script'), s = d.getElementsByTagName('script')[0];
        g.type = 'text/javascript';
        g.async = true;
        g.defer = true;
        g.src = u + 'piwik.js';
        s.parentNode.insertBefore(g, s);
    })();
</script>

<noscript><p><img src="<?php echo $piwikHost?>/piwik.php?idsite=1" style="border:0;" alt=""/></p></noscript>
<!-- End Piwik Code -->
<!-- Header -->
<div id="header" class="skel-layers-fixed" style="z-index: 0">

    <div class="top">

        <!-- Logo -->
        <div id="logo">
            <span class="image"><img src="images/avatar.png" alt=""/></span>
            <h1 id="title"><? echo $growername[0] ?></h1>
            <p><?echo $companyName?> Grower</p>
        </div>

        <!-- Nav -->
        <nav id="nav">
            <ul>
                <li><a href="#top" id="top-link" class="skel-layers-ignoreHref"><span class="icon fa-calendar">Picking Calendar</span></a>
                </li>
                <li><a href="index.php<? if ($admin[0] == 1 && ($_GET['pretend'])) {
                        echo '?pretend=' . $_GET['pretend'];
                    } else if (isset($_GET['alt_acc'])){
                        echo '?alt_acc=' . $_GET['alt_acc'];
                    }?>" id="top-link"><span class="icon fa-arrow-left">Back</span></a></li>
            </ul>
        </nav>

    </div>
</div>

<!-- Main -->
<div id="main">
        <section <?echo ($admin[0] == 1 && !isset($_GET['pretend'])  ? "style='display: none'" : '') ?> id="estimates" class="one dark cover">
            <div>
                <h2>Your picking calendar</h2>
                <p style='width: 80%; margin: auto'>You can use this tool to share your picking plans with us and to organize them. This helps us plan for when deliveries will arrive and helps us sell your fruit.</p>
            </div>
</section>
    <section>
        <h2 style="cursor: pointer" onclick="$('#drop_down_add_event').slideToggle()"><i class="fa fa-plus"></i> Add Plans</h2>
        <div id="drop_down_add_event" style='display: none;margin: auto; width: 80%;'>
           <p>New plans appear under today's date when they are first added, but can be dragged to any day, and stretched to represent any period of time. Drag an item to the trash can to delete it.</p>
            <table border='1px'>
                <thead>
                <tr>
                    <th><b>Grower</th>
                    <th><b>Variety</th>
                    <th><b>Strain</th>
                </tr>
                </thead>
                <tr>
                    <form id="newForm">
                        <td><input required type="text" id="Grower" maxlength="2" <?echo ($admin[0] == 0 ? "value='".($admin[0] == 1 && ($_GET['pretend']) ? $_GET['pretend'] : (isset($_GET['alt_acc']) ? base64_decode($_GET['alt_acc']) : $_SERVER['PHP_AUTH_USER']))."' readonly" : '')?> style='width:50px;'></td>
                        <td><input required type="text" id="Variety" style='width:150px;'></td>
                        <td><input required type="text" id="Strain" style='width:110px;'></td>
                    </form>
                </tr>
                <tr>
                    <td colspan="3"><button id='eventSubmitter'>Post to schedule</button> </td>
                </tr>
            </table>
        </div>
        <hr>
    </section>
    <!-- Calendar -->
    <div>
        <div style="display: none;position: fixed; font-size: 75px; bottom:0; right: 0" id="calendarTrash" class="calendar-trash-o"><i class="fa fa-trash"></i></div>
        <div style="width:80%;margin-top:1%;margin-left:10%" id='calendar'>
        </div>
</div>

</div>

</body>
<script>
    $(document).ready(function() {
        $('#calendar').fullCalendar({
            events: 'getCalendarEvents.php',
            eventDrop: function(event, delta, revertFunc) {
                var data = {};
                data['operation'] = 'move';
                data['eventID'] = event['id'];
                data['deltaDays'] = delta['_days'];
                $.post('editCalendarEvent.php', data, function(){
                    $('#calendar').fullCalendar('refetchEvents');
                }).error(revertFunc);
            },
            eventResize: function(event, delta, revertFunc){
                var data = {};
                data['operation'] = 'resize';
                data['eventID'] = event['id'];
                data['deltaDays'] = delta['_days'];
                $.post('editCalendarEvent.php', data, function(){
                    $('#calendar').fullCalendar('refetchEvents');
                }).error(revertFunc);
            },
            eventDragStart: function() {
                $('#calendarTrash').fadeIn('fast');
            },
            eventDragStop: function(event,jsEvent) {
                $('#calendarTrash').fadeOut('fast');
                //trashcan drop check
                var trashEl = jQuery('#calendarTrash');
                var ofs = trashEl.offset();
                var x1 = ofs.left;
                var x2 = ofs.left + trashEl.outerWidth(true);
                var y1 = ofs.top;
                var y2 = ofs.top + trashEl.outerHeight(true);
                if (jsEvent.pageX >= x1 && jsEvent.pageX<= x2 &&
                    jsEvent.pageY>= y1 && jsEvent.pageY <= y2) {
                        var data = {};
                        data['operation'] = 'delete';
                        data['eventID'] = event['id'];
                        $.post('editCalendarEvent.php', data, function(){
                            $('#calendar').fullCalendar('refetchEvents');
                        })
                }
            }
        });

        $('#eventSubmitter').on("click submit", function() {
            if($('#Grower').val() != '' && $('#Variety').val() != '') {
                var data= {};
                data['grower'] = $('#Grower').val();
                data['variety'] = $('#Variety').val();
                data['strain'] = $('#Strain').val();
                data['operation'] = 'add';
                $.post('editCalendarEvent.php', data, function() {
                    document.getElementById('newForm').reset();
                    $('#calendar').fullCalendar('refetchEvents');
                });
            }
        });
    });
</script>
</html>
