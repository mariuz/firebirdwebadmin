<?php

// Purpose        do all the tasks concerning databases
// Author         Lutz Brueckner <irie@gmx.de>
// Copyright      (c) 2000-2006 by Lutz Brueckner,
//                published under the terms of the GNU General Public Licence v.2,
//                see file LICENCE for details

require('./inc/script_start.inc.php');

//
// script is called from login form
//
if (isset($_POST['db_login_doit'])){
    // close existing connection, if any
    if (!empty($dbhandle)) {
        fbird_close($dbhandle);
    }
    $s_login['database'] = $_POST['db_login_database'];
    $s_login['user']     = strtoupper($_POST['db_login_user']);
    $s_login['host']     = $_POST['db_login_host'];

    // don't set s_login['password'] if it contains only stars
    // (from function password_stars())
    $pw = $_POST['db_login_password'];
    if (strspn($pw, '*') != strlen($pw)) {
    	$s_login['password'] = $pw;
    }

    $s_login['role']   = !empty($_POST['db_login_role'])  ? $_POST['db_login_role']  : '';
    $s_login['cache']  = !empty($_POST['db_login_cache']) ? $_POST['db_login_cache'] : '';
    if ($s_login['cache'] != NULL  &&  $s_login['cache'] < 10) {
        $s_login['cache'] = 10;
    }
    $s_login['charset'] = !empty($_POST['db_login_charset']) ? $_POST['db_login_charset'] : '';
    $s_login['dialect'] = !empty($_POST['db_login_dialect']) ? $_POST['db_login_dialect'] : '';
    $s_login['server']  = !empty($_POST['db_login_server'])  ? $_POST['db_login_server']  : '';

    if ($s_login['database'] == '') {
        $error = $ERRORS['NO_DB_SELECTED'];
    }
    elseif (!have_db_suffix($s_login['database'])) {
        $error = sprintf($ERRORS['WRONG_DB_SUFFIX'], "'".implode("', '", $DATABASE_SUFFIXES)."'");
    }
    elseif (!is_allowed_db($s_login['database'])) {
        $error = sprintf($ERRORS['DB_NOT_ALLOWED'], $s_login['database']);
    }

    if (empty($error)) {
        if ($dbhandle = db_connect()) {

            // connected successfully
            $s_connected = TRUE;

            remove_edit_panels();
            $s_charsets = get_charsets(SERVER_FAMILY, SERVER_VERSION);
        } else {
            // connect failed
            $ib_error = fbird_errmsg();
            $s_login['password'] = '';
            $s_connected = FALSE;
        }
    }
    cleanup_session();
}


//
// the Logout-Button from the login-panel
//
if (isset($_POST['db_logout_doit'])){

    if (!empty($dbhandle)) {
        fbird_close($dbhandle);
    }
    remove_edit_panels();
    cleanup_session();
    $s_login['password'] = '';

    $s_connected = FALSE;
}


//
// script is called from create database form
//
if (have_panel_permissions($s_login['user'], 'db_create')
&&  isset($_POST['db_create_doit'])
&&  isset($_POST['db_create_database'])
&&  !empty($_POST['db_create_database'])) {

    $s_create_db   = trim($_POST['db_create_database']);
    $s_create_host = trim($_POST['db_create_host']);
    $s_create_user = trim($_POST['db_create_user']);

    // dont set $s_create_pw if it contains only stars
    // (from function password_stars() )
    $pw = $_POST['db_create_password'];
    if (strspn($pw, '*') != strlen($pw))
        $s_create_pw = $pw;
    $s_create_pagesize = $_POST['db_create_pagesize'];
    $s_create_charset = $_POST['db_create_charset'];


    if (!have_db_suffix($s_create_db)) {
        $error = sprintf($ERRORS['WRONG_DB_SUFFIX'], "'".implode("', '", $DATABASE_SUFFIXES)."'");
    }
    elseif (!is_allowed_db($s_create_db)) {
        $error = sprintf($ERRORS['DB_NOT_ALLOWED'], $s_create_db);
    }

    // close existing connection, if any
    if (!empty($dbhandle)  &&  empty($error)) {
        fbird_close($dbhandle);
    }

    // build a sql statement from the values
    // we received from the db_create_form
    if (empty($error)) {

        $db_str = (!empty($s_create_host)) ? $s_create_host.':'.$s_create_db : $s_create_db;

        $sql = 'CREATE DATABASE "'.$db_str.'"';
        if (strlen($s_create_user) > 0) {
            $sql .= ' USER "'.$s_create_user.'"';
            if (strlen($s_create_pw) > 0)
                $sql .= ' PASSWORD "'.$s_create_pw.'"';
        }
        if ($s_create_pagesize !=   4096) {
            //  4096 is the default page size
            $sql .= ' PAGE_SIZE = '.$s_create_pagesize;
        }

        if ($s_create_charset != 'NONE') {
            // NONE is the default character set
            $sql .= ' DEFAULT CHARACTER SET '.$s_create_charset;
        }

        $sql .= ';';
        list($binary_output, $binary_error) = isql_execute($sql);

        if (empty($binary_error)  &&  !is_file($s_create_db)) {
            $error = sprintf($ERRORS['CREATE_DB_FAILED'], $s_create_db);

        } else { // connect the new created database

            $s_login['charset']  = $s_create_charset;
            $s_login['database'] = $s_create_db;
            $s_login['host']     = $s_create_host;
            $s_login['user']     = strtoupper($s_create_user);
            $s_login['password'] = $s_create_pw;
            if ($dbhandle = db_connect()) {
                $s_connected = TRUE;
                remove_edit_panels();
                cleanup_session();
                $message = sprintf($MESSAGES['CREATE_DB_SUCCESS'], $s_create_db);
            } else {
                $ib_error = fbird_errmsg();
            }
        }
    }
}

//
// script is called from delete database form
//
if (have_panel_permissions($s_login['user'], 'db_delete')
&&  isset($_POST['db_delete_doit'])
&&  isset($_POST['db_delete_database'])
&&  !empty($_POST['db_delete_database'])) {

    $pw = get_request_data('db_delete_password');
    if (strspn($pw, '*') == strlen($pw)) {
        $pw = $s_delete_db['password'];
    }
    $s_delete_db = array('database' => get_request_data('db_delete_database'),
                         'user'     => get_request_data('db_delete_user'),
                         'host'     => get_request_data('db_delete_host'),
                         'password' => $pw
                         );

    // cannot delete the current database
    if ($s_login['database'] == $s_delete_db) {
        $message = sprintf($MESSAGES['DELETE_CON_DB'], $s_delete_db);
        $s_login['database'] = '';
	$s_connected = FALSE;
        remove_edit_panels();
        cleanup_session();
    }

   if (!have_db_suffix($s_delete_db['database'])) {
        $error = sprintf($ERRORS['WRONG_DB_SUFFIX'], implode("', '", "'".$DATABASE_SUFFIXES)."'");
    }
    elseif (!is_allowed_db($s_delete_db['database'])) {
        $error = sprintf($ERRORS['DB_NOT_ALLOWED'], $s_delete_db['database']);
    }

    elseif ($s_cust['askdel'] == TRUE) {
        $s_confirmations['database'] =
            array('msg' => sprintf($MESSAGES['CONFIRM_DB_DELETE'], $s_delete_db['database']));
    }
}

// deleting a database is confirmed
if (isset($_POST['confirm_yes'])  ||
    (isset($_POST['db_delete_doit'])  &&  $s_cust['askdel'] == FALSE  &&  empty($error))) {

    $ib_error = drop_database($s_delete_db, $s_login);
    unset($s_confirmations['database']);

    if (empty($ib_error)) {

        if ($s_login['database'] == $s_delete_db['database']) {
            $s_login['database'] = '';
            $s_connected = FALSE;
            remove_edit_panels();
            cleanup_session();
        }

        $s_delete_db['database'] = '';
    }
}

// deleting a database is canceled
if (isset($_POST['confirm_no'])) {
    unset($s_confirmations['database']);
    $s_delete_db['database'] = '';
}


//
// get the data for the metadata panel
//
if (have_panel_permissions($s_login['user'], 'db_meta', TRUE)) {

    // remove pending metadata-jobs from session
    $s_iframejobs = array_filter($s_iframejobs, create_function('$a', '$a["job"]!="metadata";'));

    $iframekey_meta = md5(uniqid('meta'));
    $s_iframejobs[$iframekey_meta] = array('job'       => 'metadata',
                                           'timestamp' => time());

    //
    // Save to File on the Metadata panel was selected
    //
    if (isset($_POST['db_meta_save'])) {

        list($metadata, $binary_error) = isql_get_metadata($s_login['user'], $s_login['password'], $s_login['database'], $s_login['host']);

        if (empty($binary_error)  &&  count($metadata) > 0) {

            send_export_headers('application/octet-stream', 'meta.sql');
            print (implode("\n", $metadata));
            exit();
        }
    }
}


//
// something happened on the System Tables panel
//
if (isset($_POST['db_systable_select'])) {

    if ($s_systable['table'] != $_POST['db_systable']) {
        $s_systable['order']  = '';
        $s_systable['dir']    = 'ASC';
    }

    $s_systable['table']  = $_POST['db_systable'];
    $s_systable['ffield'] = $_POST['db_sysfield'];
    $s_systable['fvalue'] = $_POST['db_sysvalue'];

    $s_systable['sysdata'] = (isset($_POST['db_sysdata']) ? TRUE : FALSE);
}

if (isset($_GET['order'])) {

    $s_systable['dir'] = ($_GET['order'] == $s_systable['order']  &&  $s_systable['dir'] == 'ASC')
        ? 'DESC'
        : 'ASC';
    $s_systable['order'] = $_GET['order'];
}

// determine the informations for the selected system table
if (have_panel_permissions($s_login['user'], 'db_systable', TRUE)) {

    $js_stack .= js_request_filter_fields();

    if (FALSE  &&  strpos($s_systable['table'], 'MON$') === 0) {
        // DISABLED !

        $have_refresh =  TRUE;
        // TODO: replace with XMLHttpRequest and markableTable()
        $js_stack .= js_jsrs_refresh_systable();
    }
    else {
        $have_refresh = FALSE;
    }

    if ($s_connected   &&  !empty($s_systable['table'])) {

        include ('./inc/system_table.inc.php');

        $systable = get_systable($s_systable);
        $js_stack .= js_markable_table();
    }
}


//
// determine the accessible databases for the login panel
//
$dbfiles = array();
if (isset($ALLOWED_FILES)  && count($ALLOWED_FILES) > 0) {
    foreach ($ALLOWED_FILES AS $file) {
        if ((strpos($file, '/') === FALSE  &&  strpos($file, '\\') === FALSE)  ||
            (is_file($file)  &&  have_db_suffix($file))) {

            $dbfiles[] = $file;
        }
    }
}
elseif (isset($ALLOWED_DIRS)  &&  count($ALLOWED_DIRS) > 0) {
    foreach ($ALLOWED_DIRS as $dir) {
        if (!@is_readable($dir)) {
            $warning .= sprintf($WARNINGS['CAN_NOT_ACCESS_DIR'], $dir);
        }
        else {
            $dirhandle = opendir($dir);
            while ($filename = readdir($dirhandle)){
                if (have_db_suffix($filename)) {
                    $dbfiles[] = $dir.$filename;
                }
            }
            closedir($dirhandle);
        }
    }
}
sort($dbfiles);


//
// print out all the panels
//
$s_page = 'Database';
$panels = $s_database_panels;

require('./inc/script_end.inc.php');



//
// drop the database specified in the 'delete database' panel;
// return an empty string on success and an error message on failure
//
function drop_database($db, $login) {

    $success = '';

    // make a connection to the selected database
    // or use the global dbhandle if it is the one firebirdwebadmin is currently connected to
    if ($db['database'] == $login['database']  &&
        $db['host']  == $login['host']) {
        $dbh = $GLOBALS['dbhandle'];
    }
    else {
        $db_path = ($db['host'] == '') ? $db['database'] : $db['host'].':'.$db['database'];
        if (($dbh = fbird_connect($db_path, $db['user'], $db['password'])) == FALSE) {

            $success = fbird_errmsg();
        }
    }

    // drop it if we got a handle
    if (is_resource($dbh)  &&
        fbird_drop_db($dbh) == FALSE) {

        $success = fbird_errmsg();
        fbird_close($dbh);
    }

    return $success;
}

?>
