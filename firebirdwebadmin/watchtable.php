<?php
// File           watchtable.php / ibWebAdmin
// Purpose        handling for the navigation elements on the watchtable-panel
// Author         Lutz Brueckner <irie@gmx.de>
// Copyright      (c) 2000, 2001, 2002, 2003, 2004, 2005 by Lutz Brueckner,
//                published under the terms of the GNU General Public Licence v.2,
//                see file LICENCE for details
// Created        <01/02/03 19:58:28 lb>
//
// $Id: watchtable.php,v 1.23 2005/10/01 08:18:05 lbrueckner Exp $


// do not overwrite $s_referer in script_start.inc.php
$no_session_referer = TRUE;

require('./inc/script_start.inc.php');
require('./inc/array_functions.inc.php');

if ($s_connected) {
    $dbhandle = db_connect()
         or ib_error();
}


// handle the paging navigation
if (isset($_GET['go'])) {
    switch ($_GET['go']) {
        case 'start' :
            $s_wt['start'] = 1;
            break;
        case 'prev' :
            $s_wt['start'] -= $s_wt['rows'];
            break;
        case 'next' :
            $s_wt['start'] += $s_wt['rows'];
            break;
        default :
            $s_wt['start'] = $_GET['go'];
    }
}

// ordering by the column headlines
elseif (isset($_GET['order'])) {
    if ($s_wt['order'] == $_GET['order']) {
        $s_wt['direction'] = ($s_wt['direction'] == 'ASC') ? 'DESC' : 'ASC';
    }
    else {
        $s_wt['order'] = $_GET['order'];
        $s_wt['direction'] = 'ASC';
    }
    $s_wt['start'] = 1;
}

// editing of a dataset is requested
elseif (isset($_GET['edit'])) {
    $s_edit_idx = ($s_edit_idx > 0) ? get_max_key($s_edit_where) + 1 : 1;
    $target_panels = get_panel_array($s_referer);
    $pname = 'dt_edit'.$s_edit_idx;
    $instance = ($s_edit_idx > 1) ? "($s_edit_idx) " : '';
    $ptitle = sprintf($dt_strings['EditFrom'], $instance, $s_wt['table']);
    ${$target_panels}[] = array($pname, $ptitle, 'open');
    $pos = get_panel_index($$target_panels, $pname);
    $$target_panels = array_moveto_top($$target_panels, $pos);

    $s_edit_where[$s_edit_idx] = array('where' => get_request_data('edit', 'GET'),
                                       'table' => $s_wt['table']);
    $s_fields = get_table_computed_sources($s_wt['table'], $s_fields);
    $s_edit_values[$s_edit_idx] = init_edit_values($s_edit_where[$s_edit_idx], $s_fields[$s_wt['table']]);
}

// deleting of a dataset is requested
elseif (isset($_GET['del'])) {

    $where = get_request_data('del', 'GET');
    $quote = identifier_quote($s_login['dialect']);
    $sql   = 'DELETE FROM ' . $quote . $s_wt['table'] . $quote . ' ' . $where;

    if ($s_cust['askdel'] == TRUE) {
        $s_delete_idx = ($s_delete_idx > 0) ? get_max_key($s_confirmations['row']) + 1 : 1;
        $target_panels = get_panel_array($s_referer);
        $pname = 'dt_delete'.$s_delete_idx;
        $ptitle = 'Delete';
        $ptitle .= ($s_delete_idx > 1) ? " ($s_delete_idx) " : ' ';
        $ptitle .= 'from table '.$s_wt['table'];
        ${$target_panels}[] = array($pname, $ptitle, 'open');
        $pos = get_panel_index($$target_panels, $pname);
        $$target_panels = array_moveto_top($$target_panels, $pos);

        $s_confirmations['row'][$s_delete_idx] =
             array('msg' => sprintf($MESSAGES['CONFIRM_ROW_DELETE'], $s_wt['table'], $where),
                   'sql' => $sql);
    }

    else {  
        ibase_query($dbhandle, $sql)
            or $ib_error = ibase_errmsg();

        // cleanup the watchtable output buffer
        $s_watch_buffer = '';
    }
}


if (WATCHTABLE_METHOD == WT_STORED_PROCEDURE
||  (WATCHTABLE_METHOD == WT_BEST_GUESS  &&  guess_watchtable_method(SERVER_FAMILY, SERVER_VERSION) == WT_STORED_PROCEDURE)) {
    include('./inc/stored_procedures.inc.php');

    sp_limit_create($s_wt['table'],
                    $s_wt['columns'],
                    $s_wt['order'],
                    $s_wt['direction'],
                    $s_wt['condition'],
                    $s_wt['start'],
                    $s_wt['rows']);
}


// cleanup the watchtable output buffer
if (isset($_GET['go'])  ||  isset($_GET['order'])) {
    $s_watch_buffer = '';

    $s_cust['wt'][$s_login['database']] = array('table' => $s_wt['table'],
                                                'start' => $s_wt['start'],
                                                'order' => $s_wt['order'],
                                                'dir'   => $s_wt['direction']);
    set_customize_cookie($s_cust);
}


globalize_session_vars();

if (!empty($dbhandle)) {
    ibase_close($dbhandle);
}

header ('Location: '.url_session($s_referer));
exit;


//
// return the initial field values when editing a dataset
//
function init_edit_values($edit_where, $fields) {

    $values = array();

    $quote = identifier_quote($GLOBALS['s_login']['dialect']);
    $sql = 'SELECT * FROM ' . $quote . $edit_where['table'] . $quote . ' ' . $edit_where['where'];
    $res = ibase_query($GLOBALS['dbhandle'], $sql) or ib_error();
    if ($row = ibase_fetch_assoc($res, IBASE_TEXT)) {
        ibase_free_result($res);
        foreach ($fields as $field) {
            if (isset($field['comp'])) {
                $values[] = $field['csource'] ;
            }
            else {
                $values[] = $row[$field['name']];
            }
        }
    }
    else {
        $GLOBALS['ib_error'] = "Query didn't return a result: ".$sql;
    }

    return $values;
}

?>