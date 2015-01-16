<?php
// File           session.inc.php / FirebirdWebAdmin
// Purpose        session and fallback related functions, define all session variables
// Author         Lutz Brueckner <irie@gmx.de>
// Copyright      (c) 2000-2006 by Lutz Brueckner,
//                published under the terms of the GNU General Public Licence v.2,
//                see file LICENCE for details


//
// fallback to get-/post-session-mode if the client accept no cookies
// set $s_cookies = TRUE if the client accept cookies
//
function fallback_session() {

    // check if we got a valid session-id, redirect if not
    // and force ssl usage if configured
    if ((!isset($_COOKIE[SESSION_NAME])  &&
         !isset($_POST[SESSION_NAME])  &&
         !isset($_GET[SESSION_NAME])
         )  ||
        (PROTOCOL == 'https'  &&  !isset($_SERVER['HTTPS'])  &&
         isset($_SERVER['PORT'])  &&  $_SERVER['PORT'] != 443)
        ) {

        // this is thought to work around a xitami webserver bug
        $script = !empty($_SERVER['PHP_SELF']) ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME'];

        // take care for non-standard http ports
        $port_str = isset($_SERVER['SERVER_PORT']) ? ':' . $_SERVER['SERVER_PORT'] : '';

        // no valid id, fallback
        redirect(PROTOCOL . '://' . $_SERVER['SERVER_NAME'] . $port_str . $script . '?' . SESSION_NAME . '=' . session_id());
        exit;
    }

    $_SESSION['s_cookies'] = isset($_COOKIE[SESSION_NAME]) ? TRUE : FALSE;
    $GLOBALS['s_cookies'] = $_SESSION['s_cookies'];
}


//
// add the session_id to url if necessary
//
function url_session($url) {
    global $s_cookies;

    // peephole optimation, saves up to three function calls per url_session() call
    // and up to 1% script execution time :-)
    static $add_id = FALSE;

    if ($add_id ||
        (!$s_cookies  &&
         !ini_get('session.use_trans_sid') &&
         strstr($url, SESSION_NAME.'='.session_id()) === FALSE)) {

        $url .= (strchr($url, '?') === FALSE) ? '?' : '&';
        $url .= SESSION_NAME."=".session_id();
        $add_id = TRUE;
    }

    return str_replace('&', '&amp;', $url);
}


//
// register all sessionvars and assign default values
//
function initialize_session() {
    global $ptitle_strings, $adm_strings;

    $useragent = guess_useragent();

    $session_vars =
        array('s_init' => TRUE,                           // indicates that the session is already initialized
              's_cookies' => 'untested',
              's_stylesheet_etag' => '',
              's_connected' => FALSE,                     // TRUE if successfilly connected toa database
              's_binpath' => FALSE,                       // becomes TRUE if isql was found in BINPATH
              's_useragent' => $useragent,                // see comments at function guess_useragent()
              's_referer' => '',                          // replacement for $_SERVER['HTTP_REFERER']
              's_page'    => '',                          // indicator for the active page

              's_cust' => get_customize_defaults($useragent), // user specific customization values

              's_login' => array('database' => DEFAULT_PATH.DEFAULT_DB,    // set by the db_login panel
                                 'user'     => DEFAULT_USER,
                                 'host'     => DEFAULT_HOST,
                                 'password' => '',
                                 'role'     => DEFAULT_ROLE,
                                 'cache'    => DEFAULT_CACHE,
                                 'charset'  => DEFAULT_CHARSET,
                                 'dialect'  => DEFAULT_DIALECT,
                                 'server'   => DEFAULT_SERVER),

              's_create_db' => '',                        // set by the db_create panel
              's_create_user' => '',
              's_create_pw' => '',
              's_create_host' => '',
              's_create_pagesize' => 1024,
              's_create_charset' => 'NONE',

              's_delete_db' => array('database' => '',    // set by the db_delete panel
                                     'user'     => '',
                                     'host'     => '',
                                     'password' => ''),

              's_systable' => array('table'   => '',      // show this table on the System Tables panel
                                    'order'   => '',      // order the system table by this column
                                    'dir'     => 'ASC',   // order direction for the system table, 'ASC' or 'DESC'
                                    'ffield'  => '',      // filter field
                                    'fvalue'  => '',      // filter value
                                    'sysdata' => TRUE,    // show system data in the system tables if TRUE
                                    'refresh' => 15),
              's_system_table' => '',
              's_system_data' => TRUE,
              's_systbl_order' => '',
              's_systbl_dir' => 'ASC',

              's_tables' => array(),         // set by the tb_show panel
              's_fields' => array(),
              's_foreigns' => array(),
              's_primaries' => array(),
              's_uniques'   => array(),

              's_tables_valid'  => FALSE,    // indicates that $s_tables[]['name'] is setup properly

              's_tables_counts' => FALSE,    // whether to display the record counts    on the tb_show panel
              's_tables_cnames' => FALSE,    //           "            constraint names         "
              's_tables_def'    => FALSE,    //           "            default values           "
              's_tables_comp'   => FALSE,    //           "            computed values          "
              's_tables_comment'=> FALSE,

              's_charsets' => array(),       // charset names and associated collations

              's_create_table' => '',        // set by the tb_create panel
              's_create_num' => '',

              's_coldefs' => array(),

              's_modify_name' => '',         // set by the tb_modify panel
              's_modify_col'  => '',

              's_enter_name' => '',          // set by the dt_enter-panel
              's_enter_values' => array(),

              's_domains' => array(),        // $s_domains properties
              's_domains_valid' => FALSE,
              's_mod_domain' => '',          // set by the acc_domain-panel

              's_triggers' => array(),       // triggers properties, set by the acc_triggers-panel
              's_triggers_valid' => FALSE,
              's_triggerdefs' => array(),

              's_viewdefs' => array('name'   => '',
                                    'source' => '',
                                    'check'  => 'no'),
              's_views_counts' => FALSE,      // whether to display the record counts on th tb_show panel

              's_procedures' => array(),     // stored procedures
              's_proceduredefs' => array(),
              's_procedures_valid' => FALSE,

              's_udfs' => array(),           // user defined functions
              's_udfs_valid' => FALSE,
              's_udfs_order' => 1,
              's_udfs_dir' => 'ASC',

              's_exceptions' => array(),     // exceptions
              's_exceptions_valid' => FALSE,
              's_exceptions_order' => 1,
              's_exceptions_dir' => 'ASC',
              's_exception_defs' => array(),

              's_indexes' => array(),        // set by the acc_indexes-panel
              's_mod_index' => '',
              's_index_order' => 'name',
              's_index_dir' => 'ASC',

              // watchtable configuration
              's_wt' => array('table'      => '',
                              'columns'    => array(),
                              'blob_links' => array(),
                              'blob_as'    => array(),
                              'rows'       => DEFAULT_ROWS,
                              'start'      => 1,
                              'order'      => '',
                              'direction'  => 'ASC',
                              'edit'         => TRUE,
                              'delete'       => TRUE,
                              'tblob_inline' => TRUE,
                              'tblob_chars'  => 50,
                              'condition'    => '',
                              'fks'          => array()   // foreign key definitions for the watchtable
                              ),

              's_watch_buffer' => '',         // holds the html source of the watchtable output

              // variables for the sql_output panel
              's_sql' => array('queries' => array(),    // select statements
                               'buffer'  => '',         // holds the html source of the sql output
                               'more'    => FALSE),     // TRUE if not all lines of the result are displayed

              's_edit_idx' => 0,              // counter for open edit panels, and idx for s_edit_where
              's_edit_where' => array(),      // sql where-clauses for the data in the edit panels
              's_edit_values' => array(),     // values edited in the dt_edit-panels
              's_delete_idx' => 0,            // counter for the open row delete confirmation panels

              's_confirmations' => array(),   // this gets an array-entry for every panel in confirmation-state,
                                              // possible indices are 'table', 'column';
                                              // the array-elements carrying the elements
                                              // 'msg' which appears on the confirm-panel and
                                              // 'sql' the sql-statement to evaluate when confirmed

              's_sysdba_pw' => '',            // set by the users-panel
              's_user_name' => '',

              's_sql_buffer' => array(),      // place for the history of the enter-sql-panel
              's_sql_pointer' => 0,           // the actual buffer position


              's_gfix' => array('buffers'          => 75,    // for the values and settings on the Database Maintenance panel
                                'dialect'          => '',
                                'access_mode'      => '',
                                'write_mode'       => '',
                                'use_space'        => '',
                                'sweep_interval'   => 20000,
                                'sweep_ignore'     => FALSE,
                                'repair'           => '',
                                'repair_ignore'    => FALSE,
                                'shutdown'         => '',
                                'shutdown_seconds' => 3,
                                'reconnect'        => TRUE),

              's_dbstat_option' => IBASE_STS_HDR_PAGES,

              's_backup' => array('target'    => '', // for the values on the Database Backup panel
                                  'servicemgr'=> '',
                                  'bfactor'   => 0,
                                  'mdonly'    => '',
                                  'mdoldstyle'=> '',
                                  'create'    => '',
                                  'transport' => '',
                                  'convert'   => '',
                                  'nogc'      => '',
                                  'ignorecs'  => '',
                                  'ignorelt'  => '',
                                  'verbose'   => TRUE),

              's_restore' => array('source'   => '', // for the values on the Database Restore panel
                                   'servicemgr'=> '',
                                   'target'   => '',
                                   'overwrite'=> 'no',
                                   'pagesize' => '8192',
                                   'buffers'  => '',
                                   'amode'    => $adm_strings['ReadWrite'],
                                   'inactive' => '',
                                   'oneattime'=> '',
                                   'useall'   => '',
                                   'novalidity'=> '',
                                   'kill'     => '',
                                   'verbose'  => TRUE,
                                   'connect'  => 'no'),

              's_csv' => array('import_null' => FALSE), // options for csv import/export
              's_export' => array(),

              's_iframejobs' => array(),      //informations about what to execute and display in iframe_content.php

              's_POST' => array(),            // if DEBUG = TRUE the post and get variables are
              's_GET'  => array(),            // stored here for the inc/display_variable.php script


              // the $s_xyz_panels are arrays containing one array per panel
              // on the page it describes
              // $array[0] : panel name
              // $array[1] : panel title
              // $array[2] : panel status ['open'|'close']

              // panels on the Database page
              's_database_panels' => array(array('info',       $ptitle_strings['info'],       'open'),
                                           array('db_login',   $ptitle_strings['db_login'],   'open'),
                                           array('db_create',  $ptitle_strings['db_create'],  'close'),
                                           array('db_delete',  $ptitle_strings['db_delete'],  'close'),
                                           array('db_systable',$ptitle_strings['db_systable'],'close'),
                                           array('db_meta',    $ptitle_strings['db_meta'],    'close')),

              // panels on the Tables page
              's_tables_panels'   => array(array('info',       $ptitle_strings['info'],       'open'),
                                           array('tb_show',    $ptitle_strings['tb_show'],    'close'),
                                           array('tb_create',  $ptitle_strings['tb_create'],  'close'),
                                           array('tb_modify',  $ptitle_strings['tb_modify'],  'close'),
                                           array('tb_delete',  $ptitle_strings['tb_delete'],  'close')),
              // panels on the Accessories page
              's_accessories_panels'=>array(array('info',      $ptitle_strings['info'],       'open'),
                                            array('acc_index', $ptitle_strings['acc_index'],  'close'),
                                            array('acc_gen',   $ptitle_strings['acc_gen'],    'close'),
                                            array('acc_trigger',$ptitle_strings['acc_trigger'],'close'),
                                            array('acc_proc',  $ptitle_strings['acc_proc'],   'close'),
                                            array('acc_domain',$ptitle_strings['acc_domain'], 'close'),
                                            array('acc_view',  $ptitle_strings['acc_view'],   'close'),
                                            array('acc_exc',   $ptitle_strings['acc_exc'],    'close'),
                                            array('acc_udf',   $ptitle_strings['acc_udf'],    'close')),
              // panels on the SQL page
              's_sql_panels'       => array(array('info',      $ptitle_strings['info'],       'open'),
                                            array('sql_enter', $ptitle_strings['sql_enter'],  'close'),
                                            array('sql_output',$ptitle_strings['sql_output'], 'close'),
                                            array('tb_watch',  $ptitle_strings['tb_watch'],   'close')),

              // panels on the Data page
              's_data_panels'      => array(array('info',      $ptitle_strings['info'],       'open'),
                                            array('dt_enter',  $ptitle_strings['dt_enter'],   'close'),
                                            array('tb_watch',  $ptitle_strings['tb_watch'],   'close'),
                                            array('dt_export', $ptitle_strings['dt_export'],  'close'),
                                            array('dt_import', $ptitle_strings['dt_import'],  'close')),
              // panels on the User page
              's_users_panels'      => array(array('info',     $ptitle_strings['info'],       'open'),
                                             array('usr_user', $ptitle_strings['usr_user'],   'close'),
                                             array('usr_role', $ptitle_strings['usr_role'],   'close'),
//                                              array('usr_grant',$ptitle_strings['usr_grant'],'close')),
                                             array('usr_cust', $ptitle_strings['usr_cust'],   'close')),
              // panels on the Admin page
              's_admin_panels'      => array(array('info',      $ptitle_strings['info'],      'open'),
                                             array('adm_server',$ptitle_strings['adm_server'],'close'),
                                             array('adm_dbstat',$ptitle_strings['adm_dbstat'],'close'),
                                             array('adm_gfix',  $ptitle_strings['adm_gfix'],  'close'),
                                             array('adm_backup',$ptitle_strings['adm_backup'],'close'),
                                             array('adm_restore',$ptitle_strings['adm_restore'],'close'))
              );

    $cookie = get_customize_cookie_name();
    if (isset($_COOKIE[$cookie])) {
        $session_vars['s_cust'] = set_customize_settings($_COOKIE[$cookie]);
        $session_vars = rearrange_panels($session_vars, $_COOKIE[$cookie]);
    }

    // take care for the $HIDE_PANELS config setting
    foreach (array('database', 'tables', 'accessories', 'sql', 'data', 'users', 'admin') as $topic) {
        foreach ($session_vars['s_'.$topic.'_panels'] as $pidx => $parray) {
            if (in_array($parray[0], $GLOBALS['HIDE_PANELS'])) {
                unset($session_vars['s_'.$topic.'_panels'][$pidx]);
            }
        }
    }

    foreach ($session_vars as $key => $val) {
        $_SESSION[$key] = $val;
    }

    localize_session_vars();
}


//
// copy all sessionvars from $_SESSION[] into the local scope
//
function localize_session_vars() {

    foreach($_SESSION as $sname => $svar) {
        $GLOBALS[$sname] = $svar;
    }
}


//
// store the local vars into the session
//
function globalize_session_vars() {

    $session_var_names =
        array('s_init',
              's_cookies',
              's_stylesheet_etag',
              's_connected',
              's_binpath',
              's_useragent',
              's_referer',
              's_page',
              's_cust',
              's_login',
              's_create_db',
              's_create_user',
              's_create_pw',
              's_create_host',
              's_create_pagesize',
              's_create_charset',
              's_delete_db',
              's_systable',
              's_tables',
              's_fields',
              's_foreigns',
              's_primaries',
              's_uniques',
              's_tables_valid',
              's_tables_counts',
              's_tables_cnames',
              's_tables_def',
              's_tables_comp',
              's_tables_comment',
              's_charsets',
              's_create_table',
              's_create_num',
              's_coldefs',
              's_modify_name',
              's_modify_col',
              's_enter_name',
              's_enter_values',
              's_mod_domain',
              's_domains',
              's_domains_valid',
              's_triggers',
              's_triggers_valid',
              's_triggerdefs',
              's_viewdefs',
              's_views_counts',
              's_procedures',
              's_proceduredefs',
              's_procedures_valid',
              's_indexes',
              's_mod_index',
              's_index_order',
              's_index_dir',
              's_udfs',
              's_udfs_valid',
              's_udfs_order',
              's_udfs_dir',
              's_exceptions',
              's_exceptions_valid',
              's_exceptions_order',
              's_exceptions_dir',
              's_exception_defs',
              's_wt',
              's_watch_buffer',
              's_sql',
              's_edit_idx',
              's_edit_where',
              's_edit_values',
              's_delete_idx',
              's_confirmations',
              's_sysdba_pw',
              's_user_name',
              's_sql_buffer',
              's_sql_pointer',
              's_gfix',
              's_dbstat_option',
              's_backup',
              's_restore',
              's_csv',
              's_export',
              's_iframejobs',
              's_POST',
              's_GET',
              's_database_panels',
              's_tables_panels',
              's_accessories_panels',
              's_sql_panels',
              's_data_panels',
              's_users_panels',
              's_admin_panels'
              );

    foreach ($session_var_names as $sname) {
        if (isset($GLOBALS[$sname])) {
            $_SESSION[$sname] = $GLOBALS[$sname];
        } else {
            unset($_SESSION[$sname]);
        }
    }
}


//
// reset the session variables which depending on the connected database
//
function cleanup_session() {

    $GLOBALS['s_modify_table'] = '';
    $GLOBALS['s_enter_name'] = '';
    $GLOBALS['s_tables'] = array();
    $GLOBALS['s_fields'] = array();
    $GLOBALS['s_foreigns'] = array();
    $GLOBALS['s_primaries'] = array();
    $GLOBALS['s_uniques'] = array();
    $GLOBALS['s_tables_valid'] = FALSE;
    $GLOBALS['s_create_table'] = '';
    $GLOBALS['s_create_num'] = '';
    $GLOBALS['s_coldefs'] = array();
    $GLOBALS['s_modify_name'] = '';
    $GLOBALS['s_modify_col']  = '';
    $GLOBALS['s_enter_name'] = '';
    $GLOBALS['s_enter_values'] =  array();
    $GLOBALS['s_mod_domain'] = '';
    $GLOBALS['s_domains'] = array();
    $GLOBALS['s_domains_valid'] = FALSE;
    $GLOBALS['s_triggers'] = array();
    $GLOBALS['s_triggers_valid'] = FALSE;
    $GLOBALS['s_triggerdefs'] = array();
    $GLOBALS['s_indexes'] = array();
    $GLOBALS['s_udfs'] = array();
    $GLOBALS['s_udfs_valid'] = FALSE;
    $GLOBALS['s_exceptions'] = array();
    $GLOBALS['s_exceptions_valid'] = FALSE;
    $GLOBALS['s_exception_defs'] = array();
    $GLOBALS['s_mod_index'] = '';
    $GLOBALS['s_wt'] = array('table'      => '',
                             'columns'    => array(),
                             'blob_links' => array(),
                             'blob_as'    => array(),
                             'rows'       => DEFAULT_ROWS,
                             'start'      => 1,
                             'order'      => '',
                             'direction'  => 'ASC',
                             'edit'         => TRUE,
                             'delete'       => TRUE,
                             'tblob_inline' => TRUE,
                             'tblob_chars'  => 50,
                             'condition'    => '',
                             'fks'          => array()
                             );
    $GLOBALS['s_watch_buffer'] = '';
    $GLOBALS['s_sql'] = array('queries' => array(),
                              'buffer'  => '',
                              'more'    => FALSE);
    $GLOBALS['s_edit_idx'] = 0;
    $GLOBALS['s_edit_where'] = array();
    $GLOBALS['s_edit_values'] = array();
    $GLOBALS['s_confirm_message'] = '';
    $GLOBALS['s_confirm_return'] = '';
    $GLOBALS['s_sysdba_pw'] = '';
    $GLOBALS['s_user_name'] = '';
    $GLOBALS['s_procedures'] = array();
    $GLOBALS['s_proceduredefs'] = array();
    $GLOBALS['s_procedures_valid'] = FALSE;
    $GLOBALS['s_viewdefs'] = array('name'   => '',
                                   'source' => '',
                                   'check'  => 'no');
    $GLOBALS['s_iframejobs'] = array();

    if ($GLOBALS['s_login']['database']  &&  isset($GLOBALS['s_cust']['wt'][$GLOBALS['s_login']['database']])) {
        $wt = $GLOBALS['s_cust']['wt'][$GLOBALS['s_login']['database']];
        $GLOBALS['s_wt']['table']     = $wt['table'];
        $GLOBALS['s_wt']['start']     = $wt['start'];
        $GLOBALS['s_wt']['order']     = $wt['order'];
        $GLOBALS['s_wt']['direction'] = $wt['dir'];
        $GLOBALS['s_wt']['columns']   = FALSE;
    }
}


//
// try to identify the users browser;
// the expressions are stolen from pear::Net/UserAgent/Detect.php
// and http://www.mozilla.org/docs/web-developer/sniffer/browser_type.html
//
// returns an array with the found informations
//
function guess_useragent() {

    $agent = strtolower($_SERVER['HTTP_USER_AGENT']);
    preg_match(';^([[:alpha:]]+)[ /\(]*[[:alpha:]]*([\d]*)\.([\d\.]*);', $agent, $matches);
    list($null, $null, $majorversion, $subversion) = $matches;

    $ua = array('majorversion' => $majorversion,
                'subversion'   => $subversion);

    $ua['konq'] = strpos($agent, 'konqueror') !== false;
    $ua['text'] = strpos($agent, 'links') !== false || strpos($agent, 'lynx') !== false || strpos($agent, 'w3m') !== false;
    $ua['ns']   = strpos($agent, 'mozilla') !== false && !strpos($agent, 'spoofer') !== false && !strpos($agent, 'compatible') !== false
                  && !strpos($agent, 'hotjava') !== false && !strpos($agent, 'opera') !== false && !strpos($agent, 'webtv') !== false;
    $ua['ns2']  = $ua['ns'] && $majorversion == 2;
    $ua['ns3']  = $ua['ns'] && $majorversion == 3;
    $ua['ns4']  = $ua['ns'] && $majorversion == 4;
    $ua['ns4up']= $ua['ns'] && $majorversion >= 4;
    $ua['ns4down']= $ua['ns'] && $majorversion <= 4;
    $ua['nav']  = $ua['ns'] && (strpos($agent, ';nav') !== false || (strpos($agent, '; nav') !== false));
    $ua['ns6']  = !$ua['konq'] && $ua['ns'] && $majorversion == 5;
    $ua['ns6up']= $ua['ns6'] && $majorversion >= 5;
    $ua['gecko']= strpos($agent, 'gecko') !== false;
    $ua['ie']   = strpos($agent, 'msie') !== false && !strpos($agent, 'opera') !== false;
    $ua['ie3']  = $ua['ie'] && $majorversion < 4;
    $ua['ie4']  = $ua['ie'] && $majorversion == 4 && (strpos($agent, 'msie 4') !== false);
    $ua['ie4up']= $ua['ie'] && $majorversion >= 4;
    $ua['ie5']  = $ua['ie'] && $majorversion == 4 && (strpos($agent, 'msie 5.0') !== false);
    $ua['ie5_5']= $ua['ie'] && $majorversion == 4 && (strpos($agent, 'msie 5.5') !== false);
    $ua['ie5up']= $ua['ie'] && !$ua['ie3'] && !$ua['ie4'];
    $ua['ie5_5up']= $ua['ie'] && !$ua['ie3'] && !$ua['ie4'] && !$ua['ie5'];
    $ua['ie6']    = $ua['ie'] && $majorversion == 4 && (strpos($agent, 'msie 6.') !== false);
    $ua['ie6up']  = $ua['ie'] && !$ua['ie3'] && !$ua['ie4'] && !$ua['ie5'] && !$ua['ie5_5'];
    $ua['opera']  = strpos($agent, 'opera') !== false;
    $ua['opera2'] = strpos($agent, 'opera 2') !== false || strpos($agent, 'opera/2') !== false;
    $ua['opera3'] = strpos($agent, 'opera 3') !== false || strpos($agent, 'opera/3') !== false;
    $ua['opera4'] = strpos($agent, 'opera 4') !== false || strpos($agent, 'opera/4') !== false;
    $ua['opera5'] = strpos($agent, 'opera 5') !== false || strpos($agent, 'opera/5') !== false;
    $ua['opera5up'] = $ua['opera'] && !$ua['opera2'] && !$ua['opera3'] && !$ua['opera4'];
    $ua['aol']    = strpos($agent, 'aol') !== false;
    $ua['aol3']   = $ua['aol'] && $ua['ie3'];
    $ua['aol4']   = $ua['aol'] && $ua['ie4'];
    $ua['aol5']   = strpos($agent, 'aol 5') !== false;
    $ua['aol6']   = strpos($agent, 'aol 6') !== false;
    $ua['aol7']   = strpos($agent, 'aol 7') !== false;
    $ua['webtv']  = strpos($agent, 'webtv') !== false;
    $ua['tvnavigator'] = strpos($agent, 'navio') !== false || strpos($agent, 'navio_aoltv') !== false;
    $ua['aoltv']  = $ua['tvnavigator'];
    $ua['hotjava'] = strpos($agent, 'hotjava') !== false;
    $ua['hotjava3'] = $ua['hotjava'] && $majorversion == 3;
    $ua['hotjava3up'] = $ua['hotjava'] && $majorversion >= 3;

    return $ua;
}

?>
