<?php
// File           iframe_content.php / ibWebAdmin
// Purpose        display output for backup, restore, metadata and dbstats processes
//                ubside of an iframe
// Author         Lutz Brueckner <irie@gmx.de>
// Copyright      (c) 2000-2006 by Lutz Brueckner,
//                published under the terms of the GNU General Public Licence v.2,
//                see file LICENCE for details
// Created        <05/09/25 10:38:17 lb>
//
// $Id: iframe_content.php,v 1.3 2006/03/12 21:38:55 lbrueckner Exp $

// do not overwrite $s_referer in script_start.inc.php
$no_session_referer = TRUE;

require('./inc/script_start.inc.php');

$key = get_request_data('key', 'GET');

if ($job = get_iframejob($s_iframejobs, $key)) {
    switch ($job['job']) {
    case 'metadata':
        list($content, $error) = isql_get_metadata($s_login['user'], $s_login['password'], $s_login['database'], $s_login['host']);
        $content = implode("\n", $content);
        break;

    case 'dbstat':
        if (($service = ibase_service_attach($s_login['host'], $s_login['user'], $s_login['password'])) != FALSE) {
            $content  = ibase_db_info($service, $s_login['database'], $job['option']);
            $content  = trim(str_replace(array(chr(0x01), "\n\n"), array('', "\n"), $content));
            ibase_service_detach($service);
        }
        else {
            $error = ibase_errmsg();
        }
        break;

    case 'backup':
        if (($service = ibase_service_attach($s_login['host'], $s_login['user'], $s_login['password'])) != FALSE) {

            $content = ibase_backup($service, $job['source'], $job['target'], $job['options'], TRUE);
            $content = str_replace(array(chr(0x01).chr(0x0a), 'gbak: '), '', $content);
            ibase_service_detach($service);
        }
        else {
            $error = ibase_errmsg();
        }
        break;

    case 'restore':
        if (($service = ibase_service_attach($s_login['host'], $s_login['user'], $s_login['password'])) != FALSE) {

            $content = ibase_restore($service, $job['source'], $job['target'], $job['options'], TRUE);
            $content = str_replace(array(chr(0x01).chr(0x0a), 'gbak: '), '', $content);
            ibase_service_detach($service);

            // try to connect the restored database
            if ($job['connect']) {
                $s_login['database'] = $job['target'];
                if (!empty($s_sysdba_pw)) {
                    $s_login['user'] = 'SYSDBA';
                    $s_login['password'] = $s_sysdba_pw;
                }

                if ($dbhandle = db_connect()) {
                    // connected successfully  
                    $s_connected = TRUE;
                    remove_edit_panels();
                }
                else {
                    // connect failed 
                    $content .= '<p><span class="err">' . $info_strings['IBError'] . ':</span>' . ibase_errmsg()."</p>\n";
                    $s_login['password'] = '';
                    $s_connected = FALSE;
                }
                cleanup_session();
            }
        }
        else {
            $error = ibase_errmsg();
        }
        break;

    case 'export':

        include('./inc/export.inc.php');

        ob_start();
        export_data($job['data']);
        $content = ob_get_contents();
        ob_end_clean();
        break;
    }

    echo iframe_content($content, $error);

    unset($s_iframejobs[$key]);
    globalize_session_vars();
}


function get_iframejob($iframejobs, $key) {

    if (isset($iframejobs[$key])) {

        return $iframejobs[$key];
    }

    return  FALSE;
}

function iframe_content($content, $error) {

    return html_head('ibWebAdmin ' . VERSION)
         . "<body class=\"if\">\n"
         . ($error ? '<p><span class="err">'.$GLOBALS['info_strings']['Error'].':</span> '.$error."</p>\n" : '')
         . "<pre>\n"
         . htmlspecialchars($content)."\n"
         . "</pre>\n"
         . "</body>\n"
         . "</html>\n";
}

?>
