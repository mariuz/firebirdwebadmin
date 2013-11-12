<?php
// File           sql.php / FirebirdWebAdmin
// Purpose        perform sql commands/scripts on the selected database
// Author         Lutz Brueckner <irie@gmx.de>
// Copyright      (c) 2000-2006 by Lutz Brueckner,
//                published under the terms of the GNU General Public Licence v.2,
//                see file LICENCE for details
// Created        <00/09/12 08:59:22 lb>
//
// $Id: sql.php,v 1.38 2006/03/22 21:26:43 lbrueckner Exp $


require('./inc/script_start.inc.php');
require('./inc/foreign_keys.inc.php');
require('./inc/handle_watchtable.inc.php');
require('./inc/DataForm.php');


if (isset($s_edit_where)  &&  count($s_edit_where) > 0) {
    include('./inc/handle_editdata.inc.php');
}

//
// if the sql_enter-panel is open, get the content for the textarea
//
if (have_panel_permissions($s_login['user'], 'sql_enter')) {

    $sql_script = isset($s_sql_buffer[$s_sql_pointer]) ? $s_sql_buffer[$s_sql_pointer] : '';

    if (isset($_POST['sql_script'])) {

        $sql_script = get_request_data('sql_script');

        if (SQL_MAXSAVE == 0 || substr_count($sql_script, "\n") <= SQL_MAXSAVE) {
            $s_sql_buffer[$s_sql_pointer] = $sql_script;
        }
        else {
            unset($s_sql_buffer[$s_sql_pointer]);
        }
    }

    // load file into the textarea
    if (isset($_POST['sql_load'])  &&
        is_uploaded_file($_FILES['sql_file']['tmp_name'])) {

        $sql_script = implode('',file($_FILES['sql_file']['tmp_name']));

        if (SQL_MAXSAVE == 0 || substr_count($sql_script, "\n") <= SQL_MAXSAVE) {
            $s_sql_buffer[$s_sql_pointer] = $sql_script;
        }
        else {
            unset($s_sql_buffer[$s_sql_pointer]);
        }
    }

    // read and execute a sql file
    if (isset($_POST['sql_execute'])  &&
        is_uploaded_file($_FILES['sql_file']['tmp_name'])) {

        $sql_script = implode('',file($_FILES['sql_file']['tmp_name']));
    }

    // get the query plan
    if (isset($_POST['sql_plan'])) {
        $sql = "SET PLAN ON;\n".$sql_script.";\n";
        list($binary_output, $binary_error) = isql_execute($sql, $s_login['user'], $s_login['password'], $s_login['database'], $s_login['host']);

        if (isset($binary_output[1])  &&  substr($binary_output[1], 0, 4) == 'PLAN') {
            $binary_output = array_slice($binary_output, 0, 2);
        }
        $plan_flag = TRUE;
    }


    if (isset($_GET['buf'])) {
        if ($_GET['buf'] == 'next') {
            $s_sql_pointer = ($s_sql_pointer < SQL_HISTORY_SIZE -1) ? $s_sql_pointer +1 : 0;
        }
        elseif ($_GET['buf'] == 'prev') {
            $s_sql_pointer = ($s_sql_pointer == 0) ? SQL_HISTORY_SIZE -1 : $s_sql_pointer -1;
        }
        $s_sql['buffer'] = '';
        $s_sql['more'] = FALSE;
        $sql_script = isset($s_sql_buffer[$s_sql_pointer]) ? $s_sql_buffer[$s_sql_pointer] : '';
    }

    if (isset($_POST['sql_go'])) {
        if (isset($_POST['sql_pointer'])
        &&  abs($_POST['sql_pointer'] < SQL_HISTORY_SIZE)) {
            $s_sql_pointer = abs($_POST['sql_pointer']);
        }
        else {
            $s_sql_pointer = 0;
        }
        $s_sql['buffer'] = '';
        $sql_script = isset($s_sql_buffer[$s_sql_pointer]) ? $s_sql_buffer[$s_sql_pointer] : '';
    }

    // include the javascript for sql-buffer requests
    $js_stack .= js_request_sql_buffer();
}


//
// script is called from the enter command form
//
if (isset($_POST['sql_run'])  ||
    isset($_POST['sql_display_all'])  ||
    isset($_POST['sql_execute'])) {

    if (!have_panel_permissions($s_login['user'], 'sql_enter')) {
        exit();
    }

    // remove empty lines from userinput and put the statements into $lines[]
    if (isset($_POST['sql_run'])  ||
        isset($_POST['sql_execute'])) {

        $lines = explode(';', $sql_script);

        //remove whitespace and empty lines
        foreach($lines as $idx => $cmd){
            $cmd = trim($cmd);
            if ($cmd == '') {
                array_splice($lines, $idx, 1); 
                continue;
            }
            $lines[$idx] = $cmd;

            // execute the whole script through isql, if it contains a CREATE DATABASE, PROCEDURE or TRIGGER
            if (preg_match('/^CREATE(\s)+(DATABASE|SCHEMA|PROCEDURE|TRIGGER)/i', $cmd)) {
                $isql_flag = TRUE;

                if ($cmd{(strlen($cmd) -1)} != ';') {
                    $lines[$idx] .= ';';
                }
            }

            // empty the sql buffer if the script contains one or more select statements
            if (strncasecmp('select', $cmd, 6) == 0) {
                $s_sql['buffer'] = '';
            }
        }

        // make sure that there are no disabled commands in the script
        if (is_array($SQL_DISABLE)  &&  count($SQL_DISABLE) > 0
        &&  ($s_login['user'] != 'SYSDBA'  || SYSDBA_GET_ALL === FALSE)) {
            foreach ($SQL_DISABLE as $disable) {
                $len = strlen($disable);
                foreach ($lines as $line) {
                    if (strncasecmp($disable, $line, $len) == 0) {
                        $error = sprintf($ERRORS['DISABLED_CMD'], $disable);
                        break 2;
                    }
                }
            }
        }
        $s_sql['queries'] = $lines;
    }

    // 'display all'
    else {
        $lines = $s_sql['queries'];
    }

    // execute command/script by isql
    if (isset($isql_flag)  &&  empty($error)) {
        
        list($binary_output, $binary_error) = isql_execute($sql_script);

        $s_sql['buffer'] = '';
        array_shift($binary_output);      // discard the first line
        foreach($binary_output as $line) {
            $s_sql['buffer'] .= nl2br(str_replace(' ', '&nbsp;', $line)) . "<br>\n";
        }
    }

    // perform the query(s) by fbird_query()
    elseif ($s_connected == TRUE  &&  empty($error)) {

        $s_sql['more'] = FALSE;
        $results = array();
        foreach ($lines as $lnr => $cmd) {
            $cnt = 0;
            $trans =  fbird_trans(TRANS_WRITE, $dbhandle);
            $res = @fbird_query($trans, $cmd)
                or $ib_error = fbird_errmsg();

            // if sql_output-panel is open         
            $idx = get_panel_index($s_sql_panels, 'sql_output');
            if ($s_sql_panels[$idx][2] == 'open') {     

                // if the query have result rows
                if (is_resource($res) && @fbird_num_fields($res) > 0) {

                    $fieldinfo[$lnr] = get_field_info($res);

                    // save the rows for the output in the sql_output panel
                    while ($row = fbird_fetch_object($res)) {
                        $results[$lnr][] = get_object_vars ($row);
                        $cnt++;
                        if ($cnt >= SHOW_OUTPUT_ROWS
                        &&  !isset($_POST['sql_display_all'])) {
                            $s_sql['more'] = TRUE;
                            break;
                        }
                    }
                }
            }
            fbird_commit($trans);
        }
        if (!empty($results)) {
            $js_stack .= js_markable_table();
        }
    }

    // cleanup the watchtable output buffer
    $s_watch_buffer = '';
}


//
// process the export buttons from the sql_enter panel
//
if ($_SERVER['REQUEST_METHOD'] == 'POST'  &&  !empty($_POST)) {
    foreach (array_keys($_POST) as $name) {
        if (preg_match('/sql_export_([0-9]+)/', $name, $matches)  
        &&  isset($s_sql['queries'][$matches[1]])) {

            // set export parameters
            $s_export['source'] = array('option' => 'query',
                                        'query'  => $s_sql['queries'][$matches[1]]);

            // make sure the export-panel is open
            $idx = get_panel_index($s_data_panels, 'dt_export');
            $s_data_panels[$idx][2] = 'open';
            
            // goto the export-panel
            globalize_session_vars();
            redirect(url_session('data.php#dt_export'));
        }
    }
}


//
// print out all the panels
//
$s_page = 'SQL';
$panels = $s_sql_panels;

require('./inc/script_end.inc.php');



//
// build a result table for an 'select' from the sql_enter panel
//
function get_result_table($result, $fieldinfo, $idx) {

    $table = '<table class="table" id="resulttable_'.$idx."\" border=\"1\" cellspacing=\"0\">\n"
            ."   <tr align=\"left\">\n"
            .'      <th>'.implode('</th><th>', array_keys($result[0]))."</th>\n"
            ."   </tr>\n";

    $cnt = count($result[0]);
    foreach ($result as $row) {
        $table .= "   <tr>\n";
        $nr = 0;
        foreach ($row as $val) {
            if ($val === NULL) {
                $val = '<i>NULL</i>';
            }
            elseif ($fieldinfo[$nr]['type'] == 'BLOB' && !empty($val)) {
                $val = '<i>BLOB</i>';
            }
            else {
                $val = trim($val);
            }
            $table .= "      <td>&nbsp;".$val."</td>\n";
            $nr++;
        }
        $table .= "   </tr>\n";
    }
    
    $table .= "</table>\n"
            . js_javascript("new markableIbwaTable('resulttable_" . $idx ."')");

    return $table;
}


//
// build an export button for the sql_output panel
//
function sql_export_button($idx) {
    global $button_strings;

    $name = 'sql_export_'.$idx;

    return sprintf('<input type="submit" name="%s" value="%s"><br>', $name, $button_strings['Export'])."\n";
}


//
// get informations for the fields of a result set
//
function get_field_info($res) {

    $info = array();
    $num = fbird_num_fields($res);
    for ($i=0; $i < $num; $i++) {
        $info[] = fbird_field_info($res, $i); 
    }

    return $info;
}

?>
