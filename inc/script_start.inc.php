<?php
// File           script_start.inc.php
// Purpose        includes and initialisations needed in every main script
// Author         Lutz Brueckner <irie@gmx.de>
// Copyright      (c) 2000, 2001, 2002, 2003, 2004, 2005 by Lutz Brueckner,
//                published under the terms of the GNU General Public Licence v.2,
//                see file LICENCE for details

//apd_set_pprof_trace();

require('./inc/configuration.inc.php');

if (DEBUG) $start_time = @microtime();

require('./inc/functions.inc.php');

session_start();
set_error_handler('error_handler');

    require('./lang/' . (isset($_SESSION['s_cust']['language']) ? $_SESSION['s_cust']['language'] : LANGUAGE) . '.inc.php');
    require('./inc/session.inc.php');
    require('./inc/firebird.inc.php');
    require('./inc/panel_elements.inc.php');
    require('./inc/javascript.inc.php');

    if (DEBUG  ||  DEBUG_HTML) {
        include('./inc/debug_funcs.inc.php');
    }


    if (!extension_loaded('interbase')) {
        die($ERRORS['NO_IBASE_MODULE']);
	}

if (!isset($_SESSION['s_init'])
||  ($_SESSION['s_cookies'] === 'untested')) {
    initialize_session();
    fallback_session();
}
else {
    localize_session_vars();
}

if (!isset($no_session_referer)  ||  $no_session_referer !== TRUE) {
    // save referer in the session, $_SERVER['HTTP_REFERER'] is not always set
    $s_referer = !empty($_SERVER['PHP_SELF']) ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME'];
}

send_http_headers();


// warnings and messages are collected in this strings, the output happens in panels/info.php
$message   = '';
$warning   = '';
$error     = '';
$ib_error  = '';
$php_error = '';
$debug     = array();
$externcmd = '';


// this string is filled in the panels and echoed in script_end.inc.php
// to avoid problems ns4.7 has with javascript inside of tables
$js_stack = '';

// the different tasks storing their sql-statements in this string
// for joint execution just before the panel-output
$sql =  '';


// connecting the database, the handle is used as a global variable,
// the connection is closed in inc/script_end.inc.php
if ($s_connected == TRUE  &&  !isset($_GET['unconnected'])) {

    $dbhandle = db_connect();

    if ($dbhandle === FALSE) {
        $ib_error       = fbird_errmsg();
        $s_connected    = FALSE;
        $s_tables_valid = FALSE;
        $s_wt['table']  = '';
    }

    if (empty($s_charsets)) {
        $s_charsets = get_charsets();
    }
}

// determine server family and version
list($family, $version) = server_info($s_login['server']);
define('SERVER_FAMILY', $family);
define('SERVER_VERSION', $version);

if ($s_binpath != BINPATH) {

    // check the availabillity of the isql binary
    if (!is_dir(BINPATH)
    ||  (!is_file(BINPATH.'isql')  &&  !is_file(BINPATH.'isql.exe'))) {

        $warning = sprintf($WARNINGS['BAD_ISQLPATH'], BINPATH);
    }

    // check if TMPPATH is an existing, writeable directory
    if (!is_dir(TMPPATH)  ||  !is_writeable(TMPPATH)) {

        $warning .= sprintf($WARNINGS['BAD_TMPPATH'], TMPPATH);
    }

    $s_binpath = BINPATH;
}

if (DEBUG_HTML) ob_start();

?>
