<?php
// File           inc/handle_watchtable.inc.php / FirebirdWebAdmin
// Purpose        provides the watch table handling for sql.php and data.php
// Author         Lutz Brueckner <irie@gmx.de>
// Copyright      (c) 2000-2006 by Lutz Brueckner,
//                published under the terms of the GNU General Public Licence v.2,
//                see file LICENCE for details

// initialize $s_tables[] and $s_fields[] if necessary
$idx = get_panel_index($s_sql_panels, 'tb_watch');
$parray = get_panel_array($_SERVER['SCRIPT_NAME']);
if (${$parray}[$idx][2] == 'open' && $s_connected == true && $s_tables_valid == false) {
    include './inc/get_tables.inc.php';
    if (get_tables()) {
        $s_tables_valid = true;
    }
}

set_watch_table_title($s_wt['table']);

//
// the Config link on the Watch Table panel was clicked
//
if (isset($_GET['wcfg'])) {
    $tb_watch_cfg_flag = true;
}

//
// the Select button on the Watch Table panel was clicked
//
if (isset($_POST['tb_watch_select'])
    && $_POST['tb_watch_table'] != ''
    && $_POST['tb_watch_table'] != $s_wt['table']
) {
    $s_wt['table'] = get_request_data('tb_watch_table');
    $s_wt['columns'] = set_watch_all();
    $s_wt['blob_links'] = set_watch_blinks();
    $s_wt['blob_as'] = set_watch_blobas();
    $s_wt['start'] = 1;
    $s_wt['order'] = '';
    $s_wt['direction'] = 'ASC';
    $s_wt['delete'] = ($s_login['user'] == 'SYSDBA' || in_array('R', $s_tables[$s_wt['table']]['privileges'])) ? true : false;
    $s_wt['edit'] = ($s_login['user'] == 'SYSDBA' || in_array('U', $s_tables[$s_wt['table']]['privileges'])) ? true : false;
    $s_wt['condition'] = '';

    set_watch_table_title($s_wt['table']);
    $wt_changed = true;
}

//
// some attributes are restored from $s_cust['wt']
//
if (!empty($s_wt['table']) && $s_wt['columns'] == false && is_array($s_fields[$s_wt['table']])) {
    $s_wt['columns'] = set_watch_all();
    $s_wt['blob_links'] = set_watch_blinks();
    $s_wt['blob_as'] = set_watch_blobas();
    $s_wt['delete'] = ($s_login['user'] == 'SYSDBA' || in_array('R', $s_tables[$s_wt['table']]['privileges'])) ? true : false;
    $s_wt['edit'] = ($s_login['user'] == 'SYSDBA' || in_array('U', $s_tables[$s_wt['table']]['privileges'])) ? true : false;
}

//
// the Done button on Config Watch Table panel was clicked
//
if (isset($_POST['tb_watch_cfg_doit'])) {
    if (isset($_POST['columns']) && count($_POST['columns']) > 0) {
        $s_wt['columns'] = $_POST['columns'];
    } else {
        $s_wt['columns'] = set_watch_all();
    }

    if (isset($_POST['bloblinks'])) {
        $s_wt['blob_links'] = $_POST['bloblinks'];
    } else {
        $s_wt['blob_links'] = array();
    }

    if (isset($_POST['blobas'])) {
        $s_wt['blob_as'] = $_POST['blobas'];
    } else {
        $s_wt['blob_as'] = array();
    }

    if ((int) $_POST['tb_watch_rows'] != 0) {
        $s_wt['rows'] = abs($_POST['tb_watch_rows']);
    }
    if ((int) $_POST['tb_watch_start'] != 0) {
        $s_wt['start'] = abs($_POST['tb_watch_start']);
    }
    if (!empty($_POST['radiobox'])) {
        $s_wt['order'] = $_POST['radiobox'];
    } else {
        $s_wt['order'] = '';
    }
    $s_wt['direction'] = $_POST['tb_watch_direction'] == $sql_strings['Asc'] ? 'ASC' : 'DESC';
    $s_wt['delete'] = $_POST['tb_watch_del'] == 'Yes' ? true : false;
    $s_wt['edit'] = $_POST['tb_watch_edit'] == 'Yes' ? true : false;
    $s_wt['tblob_inline'] = $_POST['tb_watch_tblob_inline'] == 'Yes' ? true : false;
    $s_wt['tblob_chars'] = abs($_POST['tb_watch_tblob_chars']);

    if (isset($_POST['tb_watch_condition'])) {
        $s_wt['condition'] = get_request_data('tb_watch_condition');
    }
    set_watch_table_title($s_wt['table']);
    $wt_changed = true;
}

if (isset($wt_changed) && $s_connected == true) {

    // editing/deleting from views is not supported now
    if ($s_tables[$s_wt['table']]['is_view']) {
        $s_wt['edit'] = false;
        $s_wt['delete'] = false;
        $message = $MESSAGES['NO_VIEW_SUPPORT'];
    }

    // disable the 'del' and 'edit' links if the user have no remove/update permissions
    // for the selected table
    if ($s_wt['delete'] && $s_login['user'] != 'SYSDBA' && !in_array('R', $s_tables[$s_wt['table']]['privileges'])) {
        $warning = sprintf($WARNINGS['DEL_NO_PERMISSON'], $s_wt['table']);
        $s_wt['delete'] = false;
    }
    if ($s_wt['edit'] && $s_login['user'] != 'SYSDBA' && !in_array('U', $s_tables[$s_wt['table']]['privileges'])) {
        $warning = sprintf($WARNINGS['EDIT_NO_PERMISSON'], $s_wt['table']);
        $s_wt['edit'] = false;
    }

    if ($warning == '') {
        // for editing or deleting the table must have a primary key or an unique key
        $have_primary = false;
        if ($s_wt['edit'] || $s_wt['delete']) {
            foreach ($s_fields[$s_wt['table']] as $field) {
                if ((isset($field['primary']) && !empty($field['primary'])) ||
                    (isset($field['unique']) && !empty($field['unique']))
                ) {
                    $have_primary = true;
                    break;
                }
            }
        }

        // avoid editing of tables without a primary key
        if (!$have_primary && $s_wt['edit']) {
            $s_wt['edit'] = false;
            $warning .= $WARNINGS['CAN_NOT_EDIT_TABLE'];
        }

        // avoid deleting of tables without a primary key
        if (!$have_primary && $s_wt['delete']) {
            $s_wt['delete'] = false;
            $warning .= $WARNINGS['CAN_NOT_DEL_TABLE'];
        }
    }

    // for editing make sure that $s_wt[columns] contains the primary key fields
    if ($s_wt['edit']) {
        $add_primary = false;
        foreach ($s_fields[$s_wt['table']] as $field) {
            if ((isset($field['primary']) && $field['primary'] == 'Yes') &&
                (!in_array($field['name'], $wt['columns']))
            ) {
                $s_wt['columns'][] = $field['name'];
                $add_primary = true;
            }
        }
        if ($add_primary) {
            $message .= $MESSAGES['EDIT_ADD_PRIMARY'];
        }
    }

    // get foreign key definititions
    $s_wt['fks'] = get_foreignkeys($s_wt['table'], 'S');

    // update the customize cookie
    $s_cust['wt'][$s_login['database']] = array('table' => $s_wt['table'],
        'start' => $s_wt['start'],
        'order' => $s_wt['order'],
        'dir' => $s_wt['direction'], );
    set_customize_cookie($s_cust);

    // cleanup the watchtable output buffer
    $s_watch_buffer = '';
}

// deleting of a row is confirmed
if (isset($_POST['confirm_yes'])) {
    if (preg_match('/row([0-9]+)/', $_POST['confirm_subject'], $matches)) {
        $instance = $matches[1];
        $sql = $s_confirmations['row'][$instance]['sql'];
        @fbird_query($dbhandle, $sql)
        or $fb_error = fbird_errmsg();
        remove_confirm($instance);

        // cleanup the watchtable output buffer
        $s_watch_buffer = '';
    }
}

// deleting a subject is canceled
if (isset($_POST['confirm_no'])) {
    if (preg_match('/row([0-9]+)/', $_POST['confirm_subject'], $matches)) {
        $instance = $matches[1];
        remove_confirm($instance);
    }
}

if (!empty($s_wt['table'])) {

    if (!empty($s_wt['fks'])) {
        $js_stack .= js_request_fk();
    }
}

// remove the confirm panel
function remove_confirm($instance)
{
    global $s_confirmations, $s_delete_idx;

    $panels_arrayname = get_panel_array($_SERVER['SCRIPT_NAME']);
    $name = 'dt_delete'.$instance;
    $idx = get_panel_index($GLOBALS[$panels_arrayname], $name);
    array_splice($GLOBALS[$panels_arrayname], $idx, 1);
    unset($s_confirmations['row'][$instance]);

    if (count($s_confirmations['row']) == 0) {
        unset($s_confirmations['row']);
        $s_delete_idx = 0;
    }
}

//
// preselect all fields from $s_wt[table]
//
function set_watch_all()
{
    $columns = array();
    foreach ($GLOBALS['s_fields'][$GLOBALS['s_wt']['table']] as $field) {
        $columns[] = $field['name'];
    }

    return $columns;
}

//
// preselect 'Blob As Link' for all blob fields
//
function set_watch_blinks()
{
    $blinks = array();
    foreach ($GLOBALS['s_fields'][$GLOBALS['s_wt']['table']] as $field) {
        if ($field['type'] == 'BLOB') {
            $blinks[] = $field['name'];
        }
    }

    return $blinks;
}

//
// preselect blob type 'text' if subtype is 1, 'hex' for all other blob fields
//
function set_watch_blobas()
{
    $blobas = array();
    foreach ($GLOBALS['s_fields'][$GLOBALS['s_wt']['table']] as $field) {
        if ($field['type'] == 'BLOB') {
            $blobas[$field['name']] = $field['stype'] == 1 ? 'text' : 'hex';
        }
    }

    return $blobas;
}

//
// set the title for the Watch Table panel regarding $s_wt[table]
//
function set_watch_table_title($table)
{
    global $ptitle_strings;

    $title = (!isset($table) or $table == '') ? $ptitle_strings['tb_watch'] : $ptitle_strings['tb_watch'].': '.$table;
    set_panel_title('tb_watch', $title);
}

//
// print the watch table
//
function display_table($wt)
{
    global $dbhandle, $sql_strings, $button_strings, $s_watch_buffer, $s_cust;

    if ($wt['table'] == '' || !is_array($wt['columns'])) {
        return;
    }

    // if the buffer is filled, just display its content
    if (!empty($s_watch_buffer)) {
        echo $s_watch_buffer;
        echo '('.$sql_strings['DisplBuf'].')';

        return;
    }

    $quote = identifier_quote($GLOBALS['s_login']['dialect']);
    $sql = 'SELECT COUNT(*) FROM '.$quote.$wt['table'].$quote;
    $sql .= $wt['condition'] != '' ? ' WHERE '.$wt['condition'] : '';

    if (!($res = @fbird_query($dbhandle, $sql))) {
        echo '<br><b>Error: '.fbird_errmsg().'</b><br>';

        return;
    }
    $row = fbird_fetch_row($res);
    $rowcount = $row[0];
    if ($rowcount < $wt['start']) {
        $wt['start'] = $rowcount;
    }

    ob_start();

    // navigation
    echo "<p><table>\n<tr>\n";
    if ($wt['start'] > 1) {
        echo '<td><a href="'.url_session('watchtable.php?go=start').'" class="act">&lt;&lt; '.$sql_strings['Start']."</a></td>\n";
        echo '<td>&nbsp;<a href="'.url_session('watchtable.php?go=prev').'" class="act">&lt; '.$sql_strings['Prev']."</a></td>\n";
    }
    $end = (($wt['start'] + $wt['rows'] >= $rowcount)) ? $rowcount : $wt['start'] + $wt['rows'] - 1;
    $cinfo = sprintf('<b>%d - %d (%d %s)</b>', $wt['start'], $end, $rowcount, $sql_strings['Total']);
    echo '<td>&nbsp;</td><td>'.$cinfo."</td><td>&nbsp;</td>\n";
    if ($rowcount >= $wt['start'] + $wt['rows']) {
        echo '<td><a href="'.url_session('watchtable.php?go=next').'" class="act">'.$sql_strings['Next']." &gt;</a></td>\n";
        $laststart = floor(($rowcount - 1) / $wt['rows']) * $wt['rows'] + 1;
        echo '<td>&nbsp;<a href="'.url_session('watchtable.php?go='.$laststart).'" class="act">'.$sql_strings['End']." &gt;&gt;</a></td>\n";
    }
    echo "</tr>\n</table></p>\n";

    // table head
    echo '<table id="watchtable" class="table table-bordered table-hover">'."\n"
        ."  <thead>\n"
        ."    <tr>\n";
    foreach ($wt['columns'] as $col) {
        $url = url_session('watchtable.php?order='.$col);
        if ($col == $wt['order']) {
            $col = $wt['direction'] == 'ASC' ? '*&nbsp;'.$col : $col.'&nbsp;*';
        }
        echo '      <th><a href="'.$url.'">'.$col."</a></th>\n";
    }
    if ($wt['edit'] == true) {
        echo '      <th style="background-color: '.$s_cust['color']['area']."\">&nbsp;</th>\n";
    }
    if ($wt['delete'] == true) {
        echo '      <th style="background-color: '.$s_cust['color']['area']."\">&nbsp;</th>\n";
    }
    echo "    </tr>\n"
        ."  </thead>\n";

    // rows
    if ($rowcount > 0) {
        print_rows_nosp($wt);
    }
    echo "</table>\n"
        .'<span id="tb_watch_mark_buttons" style="display:none;">'."\n"
        .'<input type="submit" name="tb_watch_export" id="tb_watch_export" value="'.$button_strings['Export']." (0)\" class=\"bgrp\">\n"
        .'<input type="button" name="tb_watch_unmark" id="tb_watch_unmark" value="'.$button_strings['Unmark']."\" onClick=\"mwt.unmarkAll();\">\n"
        ."</span>\n"
        .js_javascript("mwt = new markableWatchtable('watchtable',".wt_leave_columns($wt).');');

    // save the resulting table in the session
    $s_watch_buffer = ob_get_contents();
    ob_end_flush();
}

//
// return a js array containing the column indices which shouldn't be markeable by the markableWatchtable
//
function wt_leave_columns($wt)
{
    $cols = count($wt['columns']);
    $str = '['
        .($wt['edit'] ? $cols++ : '')
        .($wt['edit'] && $wt['delete'] ? ',' : '')
        .($wt['delete'] ? $cols : '')
        .']';

    return $str;
}

//
// output the table rows, use the stored procedure generated by sp_limit_create()
//
function print_rows_sp($wt)
{
    global $dbhandle, $fb_error;

    $types = get_column_types($wt['table'], $wt['columns']);
    $col_count = count($wt['columns']);
    $class = 'wttr2';

    $sql = 'SELECT * FROM '.SP_LIMIT_NAME;
    $res = fbird_query($dbhandle, $sql)
    or $fb_error = fbird_errmsg();

    while ($row = fbird_fetch_row($res)) {
        unset($obj);
        foreach ($wt['columns'] as $idx => $colname) {
            $obj[$colname] = (isset($row[$idx])) ? $row[$idx] : '';
        }
        settype($obj, 'object');

        $class = $class == 'wttr1' ? 'wttr2' : 'wttr1';
        echo '<tr class="wttr '.$class.'">';
        for ($k = 0; $k < $col_count; ++$k) {
            if (!isset($row[$k])) {
                print_value($wt, null, null);
            } else {
                print_value($wt, $row[$k], $types[$wt['columns'][$k]], $wt['columns'][$k], $obj);
            }
        }

        // get parameter for the edit and/or del link
        if ($wt['edit'] == true || $wt['delete'] == true) {
            build_editdel_links($obj, $wt['edit'], $wt['delete']);
            echo "</tr>\n";
        }
    }
    fbird_free_result($res);
}

//
// output the table rows, skip all rows<$start and rows>$start+$cols
//
function print_rows_nosp($wt)
{
    global $dbhandle;

    $types = get_column_types($wt['table'], $wt['columns']);
    $class = 'wttr2';

    $quote = identifier_quote($GLOBALS['s_login']['dialect']);

    $sql = 'SELECT ';
    $sql .= $quote.implode($quote.','.$quote, $wt['columns']).$quote.' FROM '.$quote.$wt['table'].$quote;
    $sql .= $wt['condition'] != '' ? ' WHERE '.$wt['condition'] : '';

    if (!empty($wt['order'])) {
        $sql .= ' ORDER BY '.$wt['order'].' '.$wt['direction'];
    }

    $sql .= ' ROWS '.$wt['start'].' TO '.($wt['start'] + $wt['rows'] - 1);

    $res = @fbird_query($dbhandle, $sql) or fb_error(__FILE__, __LINE__, $sql);

    $col_count = count($wt['columns']);
    echo "  <tbody>\n";
    for ($i = 0; $i < $wt['rows']; ++$i) {
        $obj = fbird_fetch_object($res);
        // stop, if there are no more rows
        if (!is_object($obj)) {
            break;
        }

        $class = ($class == 'wttr1') ? 'wttr2' : 'wttr1';
        echo '    <tr class="wttr '.$class.'">';
        $arr = get_object_vars($obj);
        for ($k = 0; $k < $col_count; ++$k) {
            if (!isset($arr[$wt['columns'][$k]])) {
                print_value($wt, null, null);
            } else {
                print_value($wt, $arr[$wt['columns'][$k]], $types[$wt['columns'][$k]], $wt['columns'][$k], $obj);
            }
        }

        // get parameter for the edit and/or del link
        if ($wt['edit'] == true || $wt['delete'] == true) {
            build_editdel_links($obj, $wt['edit'], $wt['delete']);
        }
        echo "    </tr>\n";
    }
    echo "  </tbody>\n";

    fbird_free_result($res);
}

function print_value($wt, $val, $type, $colname = null, $obj = null)
{
    if ($val === null) {
        $data = '<i>NULL</i>';
        $align = 'center';
    } elseif (strlen(trim($val)) == 0) {
        $data = '&nbsp;';
        $align = '';
    } elseif (in_array($type, array('CHARACTER', 'VARCHAR'))) {
        $data = htmlspecialchars(trim($val));
        $align = 'left';
    } elseif ($type != 'BLOB') {
        $data = trim($val);
        $align = 'right';
    } else {
        $inline_flag = false;
        $data = '';
        if ($wt['tblob_inline'] == true && $wt['blob_as'][$colname] == 'text') {
            $blob_handle = fbird_blob_open($val);
            $blob_info = fbird_blob_info($val);
            $blob_length = $blob_info[0];
            $data = htmlspecialchars(fbird_blob_get($blob_handle, $wt['tblob_chars']));
            fbird_blob_close($blob_handle);
            if ($blob_length > $wt['tblob_chars']) {
                $data .= ' ...&nbsp;';
            } else {
                $inline_flag = true;
                $align = 'left';
            }
        }
        if (in_array($colname, $wt['blob_links']) && !$inline_flag) {
            $align = empty($data) ? 'center' : 'left';
            $url = url_session('showblob.php?where='.get_where_str($obj).'&table='.$wt['table'].'&col='.$colname);
            $data .= '<i><a href="'.$url.'" target="_blank">BLOB</a></i>';
        }

        if ($data == '') {
            $align = 'center';
            $data = '<i>BLOB</i>';
        }
    }

    if (isset($wt['fks'][$colname])) {
        $link = sprintf("javascript:requestFKValues('%s', '%s', '%s')",
            $wt['fks'][$colname]['table'],
            $wt['fks'][$colname]['column'],
            $data);
        $data = '<a href="'.$link.'">'.$data.'</a>';
    }

    echo '<td'.(!empty($align) ? ' align="'.$align.'"' : '').'>'.$data.'</td>';
}

function build_editdel_links($obj, $edit, $delete)
{
    global $sql_strings;

    $where = get_where_str($obj);
    // build the Edit-Link
    if ($edit == true) {
        $url = url_session('watchtable.php?edit='.$where);
        echo '<td><a href="'.$url.'" class="act">'.$sql_strings['Edit'].'</a></td>';
    }
    // build the Del-link
    if ($delete == true) {
        $url = url_session('watchtable.php?del='.$where);
        echo '<td><a href="'.$url.'" class="act">'.$sql_strings['Delete'].'</a></td>';
    }
}

function get_where_str($obj)
{
    static $quote;
    if (!isset($quote)) {
        $quote = identifier_quote($GLOBALS['s_login']['dialect']);
    }

    $where = 'WHERE ';
    foreach ($GLOBALS['s_fields'][$GLOBALS['s_wt']['table']] as $field) {
        if ((isset($field['primary']) && !empty($field['primary'])) ||
            (isset($field['unique']) && !empty($field['unique']))
        ) {
            $where .= $quote.$field['name'].$quote.'=';
            $where .= (is_number($field))
                ? $obj->{$field['name']}
                : "'".str_replace("'", "''", $obj->{$field['name']})."'";
            $where .= ' AND ';
        }
    }
    $where = substr($where, 0, -5);
    $where = urlencode($where);

    return $where;
}

function get_column_types($table, $cols)
{
    $types = array();
    foreach ($GLOBALS['s_fields'][$table] as $field) {
        if (in_array($field['name'], $cols)) {
            $types[$field['name']] = $field['type'];
        }
    }

    return $types;
}

//
// display a table with the elements to configure the watchtable for $table
//
function watchtable_column_options($table, $show_cols, $sort_col, $bloblinks, $blobas)
{
    global $sql_strings;

    echo "<table class=\"table table-hover table-bordered\">\n";
    echo '<thead><tr><th>'.$sql_strings['Column'].'</th>'
        .'<th>'.$sql_strings['Show'].'</th>'
        .'<th>'.$sql_strings['Sort'].'</th>'
        .'<th>'.$sql_strings['BlobLink'].'</th>'
        .'<th>'.$sql_strings['BlobType'].'</th>'
        ."</tr></thead>\n";

    foreach ($GLOBALS['s_fields'][$table] as $field) {
        echo "<tr>\n";

        // column names
        echo '<td>'.$field['name']."</td>\n";

        // 'Show' checkboxes
        echo '<td align="center"><input type="checkbox" name="columns[]" value="'.$field['name'].'"';
        if (in_array($field['name'], $show_cols)) {
            echo ' checked';
        }
        echo "></td>\n";

        // 'Sort' radioboxes
        echo '<td align="center"><input type="radio" name="radiobox" value="'.$field['name'].'"';
        if ($field['name'] == $sort_col) {
            echo ' checked';
        }
        echo "></td>\n";

        // 'Blob as Link' checkboxes
        echo '<td align="center">';
        if ($field['type'] == 'BLOB') {
            echo '<input type="checkbox"  name="bloblinks[]" value="'.$field['name'].'"';
            if (in_array($field['name'], $bloblinks)) {
                echo ' checked';
            }
            echo '>';
        } else {
            echo '&nbsp;';
        }
        echo "</td>\n";

        // 'Blob Type' select lists
        echo '<td align="center">';
        if ($field['type'] == 'BLOB') {
            $sel = (isset($blobas[$field['name']])) ? $blobas[$field['name']] : null;
            echo get_selectlist('blobas['.$field['name'].']', $GLOBALS['blob_types'], $sel, true);
        } else {
            echo '&nbsp;';
        }
        echo "</td>\n";
        echo "</tr>\n";
    }
    echo "</table>\n";
}
