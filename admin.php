<?php
// Purpose        panels for database administration tasks, gsec, gbak etc.
// Author         Lutz Brueckner <irie@gmx.de>
// Copyright      (c) 2000-2006 by Lutz Brueckner,
//                published under the terms of the GNU General Public Licence v.2,
//                see file LICENCE for details

require './inc/script_start.inc.php';

//
// interface for the gfix command
//
if (have_panel_permissions($s_login['user'], 'adm_gfix')) {
    $action = $argument = '';
    $logout = $redirect = false;

    if (isset($_POST['gfix_doit'])) {
        $s_sysdba_pw = get_sysdba_pw();

        $s_gfix = array('buffers' => get_request_data('adm_buffers'),
                        'dialect' => get_request_data('adm_sql_dialect'),
                        'access_mode' => get_request_data('adm_access_mode'),
                        'write_mode' => get_request_data('adm_write_mode'),
                        'use_space' => get_request_data('adm_use_space'),
                        'sweep_interval' => (int) get_request_data('adm_housekeeping'),
                        'sweep_ignore' => (boolean) get_request_data('adm_sweep_ignore'),
                        'repair' => get_request_data('adm_repair'),
                        'repair_ignore' => (boolean) get_request_data('adm_repair_ignore'),
                        'shutdown' => get_request_data('adm_shutdown'),
                        'shutdown_seconds' => (int) get_request_data('adm_shut_secs'),
                        'reconnect' => (boolean) get_request_data('adm_shut_reconnect'),
                        );
    }

    // set cache buffers
    if (isset($_POST['adm_gfix_buffers'])  &&  !empty($s_gfix['buffers'])) {
        $action = IBASE_PRP_PAGE_BUFFERS;
        $argument = $s_gfix['buffers'];
    }

    // set dialect
    if (isset($_POST['adm_gfix_dialect'])  &&  !empty($s_gfix['dialect'])) {
        $action = IBASE_PRP_SET_SQL_DIALECT;
        $argument = $s_gfix['dialect'];
    }

    // set access mode
    if (isset($_POST['adm_gfix_access_mode'])  &&  !empty($s_gfix['access_mode'])) {
        $action = IBASE_PRP_ACCESS_MODE;
        $argument = $s_gfix['access_mode'] == $adm_strings['ReadWrite'] ? IBASE_PRP_AM_READWRITE : IBASE_PRP_AM_READONLY;
    }

    // set write mode
    if (isset($_POST['adm_gfix_write_mode'])  &&  !empty($s_gfix['write_mode'])) {
        $action = IBASE_PRP_WRITE_MODE;
        $argument = $s_gfix['write_mode'] == $adm_strings['Sync'] ? IBASE_PRP_WM_SYNC : IBASE_PRP_WM_ASYNC;
    }

    // set space usage
    if (isset($_POST['adm_gfix_use_space'])  &&  !empty($s_gfix['use_space'])) {
        $action = IBASE_PRP_RESERVE_SPACE;
        $argument = $s_gfix['use_space'] == $adm_strings['SmallFull'] ? IBASE_PRP_RES_USE_FULL : IBASE_PRP_RES;
    }

    // set housekeeping interval
    if (isset($_POST['adm_gfix_housekeeping'])) {
        $action = IBASE_PRP_SWEEP_INTERVAL;
        $argument = $s_gfix['sweep_interval'];
    }

    // execute database sweep
    if (isset($_POST['adm_gfix_sweep'])) {
        $action = IBASE_RPR_SWEEP_DB;
        $argument = $s_gfix['sweep_ignore'] ? IBASE_RPR_IGNORE_CHECKSUM : '';
    }

    // perform data repair
    if (isset($_POST['adm_gfix_repair'])  &&  !empty($s_gfix['repair'])) {
        switch ($s_gfix['repair']) {
        case 'mend':
            $action = IBASE_RPR_MEND_DB;
            break;
        case 'validate':
            $action = IBASE_RPR_VALIDATE_DB;
            break;
        case 'full':
            $action = IBASE_RPR_FULL;
            break;
        case 'no_update':
            $action = IBASE_RPR_CHECK_DB;
            break;
        }
        $argument = $s_gfix['repair_ignore'] ? IBASE_RPR_IGNORE_CHECKSUM : '';
    }

    // execute shutdown
    if (isset($_POST['adm_gfix_shutdown'])  && !empty($s_gfix['shutdown'])) {
        switch ($s_gfix['shutdown']) {
        case 'noconns':
            $action = IBASE_PRP_DENY_NEW_ATTACHMENTS;
            break;
        case 'notrans':
            $action = IBASE_PRP_DENY_NEW_TRANSACTIONS;
            break;
        case 'force':
            $action = IBASE_PRP_SHUTDOWN_DB;
            break;
        }
        $argument = $s_gfix['shutdown_seconds'];
        $logout = $s_gfix['reconnect'] ? false : true;
    }

    // rescind shutdown
    if (isset($_POST['adm_gfix_rescind'])) {
        $action = IBASE_PRP_DB_ONLINE;
    }

    if (!empty($action)) {
        if (($service = fbird_service_attach($s_login['host'], $s_login['user'], $s_login['password'])) != false) {
            if (empty($argument)) {
                $result = fbird_maintain_db($service, $s_login['database'], $action);
            } else {
                $result = fbird_maintain_db($service, $s_login['database'], $action, $argument);
            }
            fbird_service_detach($service);
            if (!$result) {
                $ib_error = fbird_errmsg();
            }
        } else {
            $ib_error = fbird_errmsg();
        }

        if ($logout == true) {
            remove_edit_panels();
            cleanup_session();
            $s_connected = false;
        }
    }
}

//
// database statistics
//
if (have_panel_permissions($s_login['user'], 'adm_dbstat', true)) {
    if (isset($_POST['db_stat_select'])) {
        $s_dbstat_option = get_request_data('db_stat_option');
    }

    if (!empty($s_dbstat_option)) {

        // remove pending dbstat-jobs from session
        $s_iframejobs = array_filter($s_iframejobs, function($a) {return '$a["job"]!="dbstat";';});

        $iframekey_dbstat = md5(uniqid('dbstat'));
        $s_iframejobs[$iframekey_dbstat] = array('job' => 'dbstat',
                                                 'option' => $s_dbstat_option,
                                                 'timestamp' => time(), );
    }
}

//
// get server statistics via fb_lock_print
//
if (have_panel_permissions($s_login['user'], 'adm_server')) {
    $exe = 'fb_lock_print';

    // get the LOCK_HEADER BLOCK 
    list($iblockpr_output, $binary_error) = exec_command($exe, ' -o');

    $lock_header = '';
    unset($iblockpr_output[0]);
    foreach ($iblockpr_output as $line) {
        if (strlen(trim($line)) == 0) {
            break;
        }
        $lock_header .= $line."<br>\n";
    }

    // get the server statistics
    list($iblockpr_output, $binray_error) = exec_command($exe, ' -i');

    if (count($iblockpr_output) > 3) {
        $iblock['names'] = preg_split('/[\s,]+/', $iblockpr_output[0]);
        $iblock['last'] = preg_split('/[\s,]+/', $iblockpr_output[1]);
        $iblock['avg'] = preg_split('/[\s,]+/', $iblockpr_output[3]);
    }

    // get server version and implementation strings
    if (($service = fbird_service_attach($s_login['host'], $s_login['user'], $s_login['password'])) != false) {
        $server_info = fbird_server_info($service, IBASE_SVC_SERVER_VERSION)
                      .' / '
                      .fbird_server_info($service, IBASE_SVC_IMPLEMENTATION);
        fbird_service_detach($service);
    } else {
        $ib_error = fbird_errmsg();
    }
}

//
// backup the current database
//
if (have_panel_permissions($s_login['user'], 'adm_backup')) {
    if (isset($_POST['adm_backup_doit'])) {
        $s_backup = array('target' => get_request_data('adm_bu_target'),
                          'mdonly' => (boolean) get_request_data('adm_bu_mdonly'),
                          'mdoldstyle' => (boolean) get_request_data('adm_bu_mdoldstyle'),
                          'transport' => (boolean) get_request_data('adm_bu_transport'),
                          'convert' => (boolean) get_request_data('adm_bu_convert'),
                          'nogc' => (boolean) get_request_data('adm_bu_nogc'),
                          'ignorecs' => (boolean) get_request_data('adm_bu_ignorecs'),
                          'ignorelt' => (boolean) get_request_data('adm_bu_ignorelt'),
                          'verbose' => (boolean) get_request_data('adm_bu_verbose'),
                          );
    }

    if (isset($_POST['adm_backup_doit'])  &&  !empty($s_backup['target'])) {
        $s_sysdba_pw = get_sysdba_pw();

        $backup_options = array('mdonly' => IBASE_BKP_METADATA_ONLY,
                                'mdoldstyle' => IBASE_BKP_OLD_DESCRIPTIONS,
                                'transport' => IBASE_BKP_NON_TRANSPORTABLE,
                                'convert' => IBASE_BKP_CONVERT,
                                'nogc' => IBASE_BKP_NO_GARBAGE_COLLECT,
                                'ignorecs' => IBASE_BKP_IGNORE_CHECKSUMS,
                                'ignorelt' => IBASE_BKP_IGNORE_LIMBO,
                                );
        $options = 0;
        foreach ($backup_options as $idx => $option) {
            if ($s_backup[$idx]) {
                $options |= $option;
            }
        }

        $source_db = !empty($s_login['host']) ? $s_login['host'].':'.$s_login['database'] : $s_login['database'];
        $target_file = get_backup_filename($s_backup['target']);

        if ($s_backup['verbose']) {
            // remove pending backup-jobs from session
            $s_iframejobs = array_filter($s_iframejobs, create_function('$a', '$a["job"]!="backup";'));

            $iframekey_backup = md5(uniqid('backup'));
            $s_iframejobs[$iframekey_backup] = array('job' => 'backup',
                                                     'source' => $source_db,
                                                     'target' => $target_file,
                                                     'options' => $options,
                                                     'timestamp' => time(), );
        } elseif (($service = fbird_service_attach($s_login['host'], $s_login['user'], $s_login['password'])) != false) {
            $result = fbird_backup($service, $source_db, $target_file, $options, $s_backup['verbose']);
            $message = nl2br(str_replace(array(chr(0x01).chr(0x0a), 'gbak: '), '', $result));
            fbird_service_detach($service);
        } else {
            $ib_error = fbird_errmsg();
        }
    }
}

//
// restore database
//
if (have_panel_permissions($s_login['user'], 'adm_restore')) {
    if (isset($_POST['adm_restore_doit'])) {
        $s_restore = array('source' => get_request_data('adm_re_source'),
                           'target' => get_request_data('adm_re_target'),
                           'overwrite' => (boolean) get_request_data('adm_re_overwrite'),
                           'inactive' => (boolean) get_request_data('adm_re_inactive'),
                           'oneattime' => (boolean) get_request_data('adm_re_oneattime'),
                           'useall' => (boolean) get_request_data('adm_re_useall'),
                           'novalidity' => (boolean) get_request_data('adm_re_novalidity'),
                           'kill' => (boolean) get_request_data('adm_re_kill'),
                           'verbose' => (boolean) get_request_data('adm_re_verbose'),
                           'connect' => (boolean) get_request_data('adm_re_connect'),
                           );

        if ($s_restore['connect']) {
            $s_restore['verbose'] = true;
        }
    }

    if (isset($_POST['adm_restore_doit'])  &&  !empty($s_restore['source'])  &&  !empty($s_restore['target'])) {
        $s_sysdba_pw = get_sysdba_pw();

        if (!is_allowed_db($s_restore['target'])) {
            $error = sprintf($ERRORS['DB_NOT_ALLOWED'], $s_restore['target']);
        }

        $restore_options = array('inactive' => IBASE_RES_DEACTIVATE_IDX,
                                 'oneattime' => IBASE_RES_ONE_AT_A_TIME,
                                 'useall' => IBASE_RES_USE_ALL_SPACE,
                                 'novalidity' => IBASE_RES_NO_VALIDATE,
                                 'kill' => IBASE_RES_NO_SHADOW,
                                 );
        $options = 0;
        foreach ($restore_options as $idx => $option) {
            if ($s_restore[$idx]) {
                $options |= $option;
            }
        }
        if ($s_restore['overwrite']) {
            $options |= IBASE_RES_REPLACE;
        } else {
            $options |= IBASE_RES_CREATE;
        }

        if (empty($error)) {
            $source_file = defined('BACKUP_DIR')  &&  BACKUP_DIR !== '' ? BACKUP_DIR.$s_restore['source'] : $s_restore['source'];

            if ($s_restore['verbose']) {
                // remove pending backup-jobs from session
                $s_iframejobs = array_filter($s_iframejobs, create_function('$a', '$a["job"]!="restore";'));

                $iframekey_restore = md5(uniqid('restore'));
                $s_iframejobs[$iframekey_restore] = array('job' => 'restore',
                                                      'source' => $source_file,
                                                      'target' => $s_restore['target'],
                                                      'options' => $options,
                                                      'connect' => $s_restore['connect'],
                                                      'timestamp' => time(), );
            } elseif (($service = fbird_service_attach($s_login['host'], $s_login['user'], $s_login['password'])) != false) {
                fbird_restore($service, $source_file, $s_restore['target'], $options, $s_restore['verbose']);
                $message = 'restore started';
                fbird_service_detach($service);
            } else {
                $ib_error = fbird_errmsg();
            }
        }
    }
}

// print out all the panels
//
$s_page = 'Admin';
$panels = $s_admin_panels;

require './inc/script_end.inc.php';

function get_backup_filename($pname)
{
    return (defined('BACKUP_DIR')  &&  BACKUP_DIR !== '')
        ?  BACKUP_DIR.basename($pname)
        : $pname;
}

//
// return the options for the database statistics selectlist
//
function database_statistic_options()
{
    $options = array(IBASE_STS_HDR_PAGES => 'header page',
                     IBASE_STS_DB_LOG => 'log page',
                     IBASE_STS_DATA_PAGES => 'data pages',
                     IBASE_STS_IDX_PAGES => 'index leaf pages',
                     IBASE_STS_SYS_RELATIONS => 'system relations',
                     );

    return $options;
}
