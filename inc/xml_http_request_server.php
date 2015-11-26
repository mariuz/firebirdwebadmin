<?php
// File           inc/xml_http_request_server.php / FirebirdWebAdmin
// Purpose        answers to the XMLHttpRequests
// Author         Lutz Brueckner <irie@gmx.de>
// Copyright      (c) 2000-2006 by Lutz Brueckner,
//                published under the terms of the GNU General Public Licence v.2,
//                see file LICENCE for details


require './configuration.inc.php';
require './session.inc.php';
require './functions.inc.php';
include './panel_elements.inc.php';

if (DEBUG === true) {
    include './debug_funcs.inc.php';
}

session_start();
localize_session_vars();

require '../lang/'.(isset($s_cust['language']) && !empty($s_cust['language']) && ($s_cust['language'] != 1) ? $s_cust['language'] : LANGUAGE).'.inc.php';

$dbhandle = db_connect();

// guess the server
list($family, $version) = server_info($s_login['server']);
define('SERVER_FAMILY', $family);
define('SERVER_VERSION', $version);

// names of authorized server functions
$server_functions = array('column_config_form',
                          'closed_panel',
                          'detail_view',
                          'detail_close',
                          'fk_values',
                          'systable_filter_fields',
                          'systable_filter_values',
                          'table_columns_selectlist',
                          'sql_buffer',
                          'data_export_format_options',
                          'set_export_target',
                          'set_export_source',
                          'comment_area',
                          'markable_watchtable_report',
                          );

$func = get_request_data('f', 'GET');
if (in_array($func, $server_functions)) {
    $func_args = array();
    foreach ($_GET as $name => $value) {
        if (preg_match('/^p[0-9]+$/', $name)) {
            $func_args[] = get_request_data($name, 'GET');
        }
    }

    call_user_func_array($func, $func_args);
} else {
    echo 'bad request!';
}

globalize_session_vars();

//
// return the html with the form elements required for the column configuration
// called from the Enter Data and the Edit Data panels
//
function column_config_form($fk_table, $table, $column)
{
    global $button_strings, $dt_strings;

    $fk_columns = array();
    foreach ($GLOBALS['s_fields'][$fk_table] as $field) {
        if ($field['type'] == 'BLOB') {
            continue;
        }
        $fk_columns[] = $field['name'];
    }

    $pre = ifsetor($GLOBALS['s_cust']['fk_lookups'][$table][$column]);

    $html = "<table border=\"1\" cellpadding=\"3\" cellspacing=\"0\">\n"
          ."  <tr>\n"
          ."    <th>\n"
          .'      '.sprintf($dt_strings['ColConf'], $column)."\n"
          ."    </th>\n"
          ."  </tr>\n"
          ."  <tr>\n"
          ."    <td>\n"
          .'      <b>'.$dt_strings['ColFKLook']."</b><br>\n"
          .'      '.get_selectlist('dt_column_config_fk_column', $fk_columns, $pre, true)."\n"
          ."    </td>\n"
          ."  </tr>\n"
          ."</table>\n"
          .hidden_field('dt_column_config_table', $table)."\n"
          .hidden_field('dt_column_config_column', $column)."\n"
          .'<input type="submit" name="dt_column_config_save" value="'.$button_strings['Save']."\" class=\"bgrp\">\n"
          .'<input type="button" name="dt_column_config_cancel" onClick="javascript:hide(this.parentNode.id);" value="'.$button_strings['Cancel']."\" class=\"bgrp\">\n";

    header('Content-Type: text/html;charset='.$GLOBALS['charset']);

    echo $html;
}

//
// mark a panel as closed in the session and deliver the html for the closed panel
//
function closed_panel($idx)
{

    //calculate the panel name
    $pvar = 's_'.strtolower($GLOBALS['s_page']).'_panels';

    $GLOBALS[$pvar][$idx][2] = 'close';

    $html = get_closed_panel($GLOBALS[$pvar][$idx][1], $idx);

    set_customize_cookie($GLOBALS['s_cust']);
    header('Content-Type: text/html;charset='.$GLOBALS['charset']);

    echo $html;
}

//
// return the html to visualize the details of a database object (table, view, trigger or stored procedure)
//
function detail_view($type, $name, $title)
{
    $url = fold_detail_url($type, 'open', $name, $title);
    $comment_url = "javascript:requestCommentArea('".$type."', '".$name."');";
    $comment_div = detail_div_prefix($type).'c_'.$name;

    switch ($type) {
        case 'table':
            if ($GLOBALS['s_tables_def'] == true) {
                $GLOBALS['s_fields'] = get_table_defaults_sources($name, $GLOBALS['s_fields']);
            }
            $html = get_opened_table($name, $title, $url, $comment_url, $comment_div);

            $GLOBALS['s_tables'][$name]['status'] = 'open';
            break;

        case 'view':

            include '../inc/views.inc.php';

            $html = get_opened_view($name, $title, $url);

            $GLOBALS['s_tables'][$name]['status'] = 'open';
            break;

        case 'trigger':

            include '../inc/triggers.inc.php';

            if (empty($GLOBALS['s_triggers'][$name]['source'])) {
                $GLOBALS['s_triggers'][$name]['source'] = get_trigger_source($name);
            }

            $html = get_opened_trigger($name, $GLOBALS['s_triggers'][$name], $url);

            $GLOBALS['s_triggers'][$name]['display'] = 'open';
            break;

        case 'procedure':

            include '../inc/procedures.inc.php';
            include '../inc/firebird.inc.php';

            if (empty($GLOBALS['s_procedures'][$name]['source'])) {
                $GLOBALS['s_procedures'][$name]['source'] = get_procedure_source($name);

                list($in, $out) = get_procedure_parameters($name);
                $GLOBALS['s_procedures'][$name]['in'] = $in;
                $GLOBALS['s_procedures'][$name]['out'] = $out;
            }

            $html = get_opened_procedure($name, $GLOBALS['s_procedures'][$name], $url);

            $GLOBALS['s_procedures'][$name]['status'] = 'open';
            break;
    }

    header('Content-Type: text/html;charset='.$GLOBALS['charset']);

    echo $html;
}

//
// return the html for a closed database objects view
//
function detail_close($type, $name, $title)
{
    switch ($type) {
        case 'table':
        case 'view':
            $GLOBALS['s_tables'][$name]['status'] = 'close';
            break;
        case 'trigger':
            $GLOBALS['s_triggers'][$name]['display'] = 'close';
            break;
        case 'procedure':
            $GLOBALS['s_procedures'][$name]['status'] = 'close';
            break;
    }

    $url = fold_detail_url($type, 'close', $name, $title);
    $comment_url = "javascript:requestCommentArea('".$type."', '".$name."');";
    $comment_div = detail_div_prefix($type).'c_'.$name;

    $html = get_closed_detail($title, $url,  $comment_url, $comment_div);

    header('Content-Type: text/html;charset='.$GLOBALS['charset']);

    echo $html;
}

//
// return the html to display the foreign key values in a table below the watchtable
//
function fk_values($table, $column, $value)
{
    $sql = sprintf("SELECT * FROM %s WHERE %s='%s'", $table, $column, $value);
    $res = fbird_query($GLOBALS['dbhandle'], $sql)
        or ib_error(__FILE__, __LINE__, $sql);

    if ($row = fbird_fetch_object($res)) {
        $close = "<a href='javascript:hide(\"fk\");'>[C]</a>";
        $html = "<table class=\"table table-bordered tsep\">\n<tr align=\"left\">\n<th colspan=\"2\"><nobr>".$close.'&nbsp;&nbsp;'.$sql."</nobr></th></tr>\n";
        foreach ($GLOBALS['s_fields'][$table] as $field) {
            $value = ($field['type'] == 'BLOB') ? '<i>BLOB</i>' : trim($row->$field['name']);
            $html .= sprintf("<tr>\n<td class=\"wttr wttr1\">%s:</td><td class=\"wttr2\"><nobr>%s</nobr></td>\n</tr>\n", $field['name'], $value);
        }
        $html .= "</table>\n";
    } else {
        $html = "Error!\n";
    }
    fbird_free_result($res);

    header('Content-Type: text/html;charset='.$GLOBALS['charset']);

    echo $html;
}

//
//  return the selectlist with the columnnames of a systemtable
//
function systable_filter_fields($table)
{
    require '../inc/system_table.inc.php';

    $html = systable_field_select($table);
    $s_systable['table'] = $table;
    $s_systable['ffield'] = '';
    $s_systable['fvalue'] = '';

    header('Content-Type: text/html;charset='.$GLOBALS['charset']);

    echo $html;
}

//
//  return the selectlist with the values of a systemtables column
//
function systable_filter_values($table, $field)
{
    require '../inc/system_table.inc.php';

    $html = systable_value_select($table, $field);
    $s_systable['ffield'] = $field;
    $s_systable['fvalue'] = '';

    header('Content-Type: text/html;charset='.$GLOBALS['charset']);

    echo $html;
}

//
// return a selectlist filled with the columns of a table
//
function table_columns_selectlist($table, $ename, $restriction)
{
    $columns = array();

    if (is_array($GLOBALS['s_fields'][$table])) {
        foreach ($GLOBALS['s_fields'][$table] as $field) {
            if ($restriction == 'fk'  &&
                !isset($field['primary'])  &&
                !isset($field['unique'])) {
                continue;
            }

            $columns[] = $field['name'];
        }
    }

    if (count($columns) > 0) {
        $html = get_selectlist($ename, $columns, null, true);
    } else {
        $html = get_textfield($ename, 20, 31);
    }

    header('Content-Type: text/html;charset='.$GLOBALS['charset']);

    echo $html;
}

//
// return the content of the specified sql buffer for the textarea  on the sql panel
//
function sql_buffer($idx)
{
    $GLOBALS['s_sql_pointer'] = $idx;
    $val = isset($GLOBALS['s_sql_buffer'][$GLOBALS['s_sql_pointer']]) ? $GLOBALS['s_sql_buffer'][$GLOBALS['s_sql_pointer']] : '';

    header('Content-Type: text/html;charset='.$GLOBALS['charset']);

    echo $val;
}

//
// return the formelements for the options for the selected export format
//
function data_export_format_options($format)
{
    require '../inc/export.inc.php';

    if (in_array($format, array_keys(get_export_formats()))) {
        $GLOBALS['s_export']['target']['filename'] = fix_export_filename_suffix($GLOBALS['s_export']['target']['filename'], $format);

        $GLOBALS['s_export']['format'] = $format;
        $html = export_format_options_table($GLOBALS['s_export']);

        header('Content-Type: text/html;charset='.$GLOBALS['charset']);

        echo $html;
    }
}

//
// save the selected export target in the session
//
function set_export_target($target)
{
    require '../inc/export.inc.php';

    if (in_array($target, array_keys(get_export_targets()))) {
        $GLOBALS['s_export']['target']['option'] = $target;
    }
}

//
// save the selected export source in the session
//
function set_export_source($source)
{
    require '../inc/export.inc.php';

    if (in_array($source, array_keys(get_export_sources()))) {
        $GLOBALS['s_export']['source']['option'] = $source;
    }
}

function comment_area($type, $name)
{
    switch ($type) {
        case 'table':
        case 'view':
            $html = 'foo';
            break;
        case 'trigger':
            break;
        case 'procedure':
            break;
    }

    header('Content-Type: text/html;charset='.$GLOBALS['charset']);

    echo $html;
}

function markable_watchtable_report($a, $b)
{
}

// build the answer for the jsrs requests
// TODO: this is left to be replaced with an implementation for XMLHttpRequests
function systable($seconds)
{
    global $s_systable, $s_login;

    list($family, $version) = server_info($s_login['server']);
    define('SERVER_FAMILY', $family);
    define('SERVER_VERSION', $version);
    $s_systable['refresh'] = $seconds;

    if ($seconds != 0) {
        $systable = get_systable($s_systable);
        $html = get_systable_html($systable, $s_systable);
    } else {
        $html = '';
    }

    globalize_session_vars();

    return jsrsArrayToString(array($html), $delim = '~');
}
