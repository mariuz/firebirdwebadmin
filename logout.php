<?php
    require_once './inc/script_start.inc.php';
    //require('./inc/functions.inc.php');
    //require('./inc/session.inc.php');

    if (!empty($dbhandle)) {
        fbird_close($dbhandle);
    }

    remove_edit_panels();
    cleanup_session();
    $s_login['password'] = '';

    $s_connected = false;

    require_once './inc/script_end.inc.php';

    header('Location: database.php');
    exit();
