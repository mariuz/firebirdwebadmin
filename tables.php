<?php
// File           tables.php / FirebirdWebAdmin
// Purpose        acting with the tables of the selected database
// Author         Lutz Brueckner <irie@gmx.de>
// Copyright      (c) 2000-2006 by Lutz Brueckner,
//                published under the terms of the GNU General Public Licence v.2,
//                see file LICENCE for details


require './inc/script_start.inc.php';

//
// script is called from the create table form
//
if (have_panel_permissions($s_login['user'], 'tb_create', true)) {
    if (isset($_POST['tb_create_doit'])) {
        $s_create_table = $_POST['tb_create_table'];

        // this is the first step, $s_create_num is still unset
        if (!isset($s_create_num) || empty($s_create_num)) {
            if (isset($_POST['tb_create_num'])  &&  (int) $_POST['tb_create_num'] > 0) {
                $s_create_num = $_POST['tb_create_num'];
                $s_coldefs = array();
            }
        }

        // second step, get the column definitions,
        //              build the sql-statement and
        //              create the table 
        else {
            $rwords = array();
            for ($idx = 0; $idx < $s_create_num; ++$idx) {
                // save the form values into $s_coldefs
                save_coldef($idx);

                // interbase keywords are not allowed as column names 
                if (in_array(strtoupper($s_coldefs[$idx]['name']), get_reserved_words(SERVER_FAMILY, SERVER_VERSION))) {
                    $rwords[] = strtoupper($s_coldefs[$idx]['name']);
                }
            }

            if (count($rwords) > 0) {
                $cnt = count($rwords);
                $warning = ($cnt == 1) ? sprintf($WARNINGS['NAME_IS_KEYWORD'], $rwords[0])
                                       : sprintf($WARNINGS['NAMES_ARE_KEYWORDS'], implode(', ', $rwords));
            } else {
                // build the CREATE TABLE sql-statement
                $sql = "CREATE TABLE $s_create_table (\n";

                // loop over the number of columns 
                // and build the <col_def> parts of the query
                for ($idx = 0; $idx < $s_create_num; ++$idx) {
                    $sql .= build_coldef($idx);

                    if (isset($s_coldefs[$idx]['primary'])) {   // collect fieldnames for the PRIMARY KEY
                        $pkeys[] = $s_coldefs[$idx]['name'];
                    }
                    $sql .= ",\n";
                }
                if (isset($pkeys)) {
                    $sql .= "\tPRIMARY KEY\t(".implode(', ', $pkeys)."),\n";
                }
                $sql = substr($sql, 0, -2);      // remove the last ',\n'
                $sql .= "\n);";
            }
        }
    }
}

//
// cancel button on the create table panel was pressed
//
if (isset($_POST['tb_create_cancel'])) {
    $s_create_num = '';
    $s_coldefs = array();
}

//
// script is called from the modify table form
//
if (isset($_POST['tb_modify_doit'])) {
    if ($_POST['tb_modify_name'] != '') {
        $s_modify_name = $_POST['tb_modify_name'];

        set_panel_title('tb_modify', $ptitle_strings['tb_modify'].': '.$s_modify_name);
    }
}

//
// prevent bypassing of the $HIDE_PANEL setting for the modify column actions
//
if ((!have_panel_permissions($s_login['user'], 'tb_modify'))
&&  (isset($_POST['tb_modify_del'])  ||  isset($_POST['tb_modadd_doit'])  ||  isset($_POST['tb_modcol_doit']))) {
    die('bad boy');
}

//
// script is called via the Ready button on the modify table form
//
if (isset($_POST['tb_modify_ready'])) {
    $s_modify_name = '';
    set_panel_title('tb_modify', $ptitle_strings['tb_modify']);
}

//
// script is called via the Add Column button on the modify table form
//
if (isset($_POST['tb_modify_add'])) {
    $s_coldefs['add'] = array();
    $col_add_flag = true;
}

//
// add the new column to the table
//
if (isset($_POST['tb_modadd_doit'])) {
    save_coldef('add');

    // interbase keywords are not allowed as column names     
    if (in_array(strtoupper($s_coldefs['add']['name']), get_reserved_words(SERVER_FAMILY, SERVER_VERSION))) {
        $warning = sprintf($WARNINGS['NAME_IS_KEYWORD'], strtoupper($s_coldefs['add']['name']));

        // show the add-column form again
        $col_add_flag = true;
    } else {
        $sql = "ALTER TABLE $s_modify_name ADD \n";
        $sql .= build_coldef('add', 'alter');
        $sql .= ';';

        $add_flag = true;
    }
}

//
// script is called via the Delete Column button from the modify table form
//
if (isset($_POST['tb_modify_del'])
&&  isset($_POST['tb_modify_dname'])  &&  !empty($_POST['tb_modify_dname'])) {
    $cname = $_POST['tb_modify_dname'];

    $deps = get_dependencies(OT_RELATION, $s_modify_name, $cname);
    if (count($deps) > 0) {
        $message = sprintf($MESSAGES['HAVE_DEPENDENCIES'], $tb_strings['Column'], $cname, dependencies_string($deps));
    } else {
        $drop_statement = count(table_columns($s_modify_name)) > 1
                            ? 'ALTER TABLE '.$s_modify_name.' DROP '.$cname
                            : 'DROP TABLE '.$s_modify_name;

        if ($s_cust['askdel'] == true) {
            $s_confirmations['column'] =
                array('msg' => sprintf($MESSAGES['CONFIRM_COLUMN_DELETE'], $cname, $s_modify_name),
                      'sql' => $drop_statement, );
        } else {
            $sql = $drop_statement;
        }
    }
}

//
// script is called via the Modify Column button from the modify table form
//
if (isset($_POST['tb_modify_col'])
&&  isset($_POST['tb_modify_mname'])  &&  !empty($_POST['tb_modify_mname'])) {
    $s_modify_col = $_POST['tb_modify_mname'];
    foreach ($s_fields[$s_modify_name] as $field) {
        if ($field['name'] == $s_modify_col) {
            $s_coldefs['mod'] = $field;

            $s_coldefs['mod']['foreign_cols'] = $s_coldefs['mod']['primary_cols'] = $s_coldefs['mod']['unique_cols'] = 0;
            if (isset($field['foreign'])) {
                $s_coldefs['mod']
                    = array_merge($s_coldefs['mod'],
                                  get_column_fk_defs($field['foreign'], $s_foreigns[$field['foreign']]['index']));
                $s_coldefs['mod']['foreign_cols'] = $s_foreigns[$field['foreign']]['cols'];
            }

            if (isset($field['primary'])) {
                $s_coldefs['mod']['primary_cols'] = $s_primaries[$field['primary']]['cols'];
            }

            if (isset($field['unique'])) {
                $s_coldefs['mod']['unique_cols'] = $s_uniques[$field['unique']]['cols'];
            }

            $s_coldefs['old'] = $s_coldefs['mod'];
            break;
        }
    }
    if (isset($s_coldefs['mod']['domain'])) {
        $warning = $WARNINGS['CAN_NOT_ALTER_DOMAINS'];
        $s_coldefs = array();
    } else {
        $s_coldefs['mod']['pk_del'] = $s_coldefs['mod']['fk_del'] = $s_coldefs['mod']['uq_del'] = false;
        $col_mod_flag = true;

        $js_stack .= js_request_table_columns();
    }
}

//
// altering the column definitions 
//
if (isset($_POST['tb_modcol_doit'])) {
    $mod_flag = false;
    $sql = 'ALTER TABLE '.$s_modify_name.' ';

    save_coldef('mod');

    if (!isset($s_coldefs['mod']['domain'])  ||  $s_coldefs['mod']['domain'] != 'Yes') {
        if (datatype_is_modified($s_coldefs['old'], $s_coldefs['mod'])) {

            // build sql for altering datatype definition
            $sql .= 'ALTER '.$s_modify_col.' TYPE '.build_datatype($s_coldefs['mod']).', ';
            $mod_flag = true;
        }

        // the delete primary key checkbox is checked
        if ($s_coldefs['mod']['pk_del'] == true) {
            $sql .= 'DROP CONSTRAINT '.$s_coldefs['old']['primary'].', ';
            $mod_flag = true;
        }

        // the delete foreign key checkbox is checked
        if ($s_coldefs['mod']['fk_del'] == true) {
            $sql .= 'DROP CONSTRAINT '.$s_coldefs['old']['fk_name'].', ';
            $mod_flag = true;
        }

        // the delete unique constraint checkbox is checked
        if ($s_coldefs['mod']['uq_del'] == true) {
            $sql .= 'DROP CONSTRAINT '.$s_coldefs['old']['unique'].', ';
            $mod_flag = true;
        }

        // delete and recreate the foreign key constraint
        elseif (column_fk_is_modified($s_coldefs['old'], $s_coldefs['mod'])) {
            if (isset($s_coldefs['old']['fk_name'])  &&  !empty($s_coldefs['old']['fk_name'])) {
                $sql .= 'DROP CONSTRAINT '.$s_coldefs['old']['fk_name'].', ';
            }
            $sql .= 'ADD';
            if (!empty($s_coldefs['mod']['fk_name'])) {
                $sql .= ' CONSTRAINT '.$s_coldefs['mod']['fk_name'];
            }
            $sql .= ' FOREIGN KEY ('.$s_coldefs['mod']['name'].')'
                   .' REFERENCES '.$s_coldefs['mod']['fk_table'].' ';
            if (isset($s_coldefs['mod']['fk_column'])  &&  $s_coldefs['mod']['fk_column'] != '') {
                $sql .= '('.$s_coldefs['mod']['fk_column'].') ';
            }
            if (isset($s_coldefs['mod']['on_update'])  &&  $s_coldefs['mod']['on_update'] != '') {
                $sql .= ' ON UPDATE '.$s_coldefs['mod']['on_update'];
            }
            if (isset($s_coldefs['mod']['on_delete'])  &&  $s_coldefs['mod']['on_delete'] != '') {
                $sql .= ' ON DELETE '.$s_coldefs['mod']['on_delete'].', ';
            }
            $mod_flag = true;
        }
    }

    // build sql for altering column domain
    if ((isset($s_coldefs['mod']['domain'])  &&  $s_coldefs['mod']['domain'] == 'Yes')
    && $_POST['cd_def_domainmod'] != $s_coldefs['mod']['type']) {
        $s_coldefs['mod']['type'] = $_POST['cd_def_domainmod'];
        $sql .= 'ALTER '.$s_modify_col.' '.$s_coldefs['mod']['type'].', ';
        $mod_flag = true;
    }

    // build sql for changing column position
    if ($_POST['tb_modcol_pos'] != '') {
        $sql .= 'ALTER '.$s_modify_col.' POSITION '.$_POST['tb_modcol_pos'].', ';
        $mod_flag = true;
    }

    // build sql for renaming of the column
    if ($_POST['cd_def_namemod'] != $s_modify_col) {
        // interbase keywords are not allowed as column names     
        if (in_array(strtoupper($s_coldefs['mod']['name']), get_reserved_words(SERVER_FAMILY, SERVER_VERSION))) {
            $warning = sprintf($WARNINGS['NAME_IS_KEYWORD'], strtoupper($s_coldefs['mod']['name']));
        } else {
            $sql .= 'ALTER '.$s_modify_col.' TO '.$_POST['cd_def_namemod'].', ';
            $mod_flag = true;
        }
    }

    if ($mod_flag) {
        $sql = substr($sql, 0, -2);      // remove the trailing ', '
        $sql .= ';';
    } else {
        $sql = '';
    }

    if ($s_wt['table'] == $s_modify_name) {
        $s_watch_buffer = '';
    }
}

//
// script is called from the delete table form
//
if (have_panel_permissions($s_login['user'], 'tb_delete')) {
    if (isset($_POST['tb_delete_doit'])
    &&  isset($_POST['tb_delete_name'])  &&  !empty($_POST['tb_delete_name'])) {
        $tname = $_POST['tb_delete_name'];

        $deps = get_dependencies(OT_RELATION, $tname);
        if (count($deps) > 0) {
            $message = sprintf($MESSAGES['HAVE_DEPENDENCIES'], $tb_strings['Table'], $tname, dependencies_string($deps));
        } else {
            $quote = identifier_quote($s_login['dialect']);
            $tstr = $quote.$tname.$quote;
            $drop_statement = ($s_tables[$tname]['is_view'] == true) ? 'DROP VIEW '.$tstr : 'DROP TABLE '.$tstr;

            if ($s_cust['askdel'] == true) {
                $s_confirmations['table'] =
                    array('msg' => sprintf($MESSAGES['CONFIRM_TABLE_DELETE'], $tname),
                          'sql' => $drop_statement, );
            } else {
                $sql = $drop_statement;
            }

            if ($s_modify_name == $_POST['tb_delete_name']) {
                $s_modify_name = '';
            }
            if ($s_wt['table'] == $_POST['tb_delete_name']) {
                $s_wt['table'] = '';
                $s_watch_buffer = '';
            }
            if ($s_enter_name == $_POST['tb_delete_name']) {
                $s_enter_name = '';
                $s_enter_values = array();
            }
        }
    }
}

// 'Open All' button
if (isset($_POST['tb_table_open'])) {
    $s_tables = toggle_all_tables($s_tables, 'open');
}

// 'Close All' button
if (isset($_POST['tb_table_close'])) {
    $s_tables = toggle_all_tables($s_tables, 'close');
}

// deleting a subject is confirmed
if (isset($_POST['confirm_yes'])
&&  isset($s_confirmations[$_POST['confirm_subject']])) {
    $sql = $s_confirmations[$_POST['confirm_subject']]['sql'];
    unset($s_confirmations[$_POST['confirm_subject']]);
}

// deleting a subject is canceled
if (isset($_POST['confirm_no'])
&&  isset($s_confirmations[$_POST['confirm_subject']])) {
    unset($s_confirmations[$_POST['confirm_subject']]);
}

// 
// perform the sql-statement in $sql
//
if ($sql != '') {
    if (DEBUG) {
        add_debug('$sql: '.$sql, __FILE__, __LINE__);
    }
    $trans = fbird_trans(TRANS_WRITE, $dbhandle);
    if (fbird_query($trans, $sql)) {
        fbird_commit($trans);
        $s_tables_valid = false;
        $s_create_table = '';
        $s_create_num = 0;
        $s_coldefs = array();
        $s_modify_col = '';
    } else {
        $ib_error = fbird_errmsg();
        fbird_rollback($trans);
        if (isset($mod_flag)  &&  $mod_flag == true) {
            $col_mod_flag = true;
        }
        if (isset($add_flag) &&  $add_flag == true) {
            $col_add_flag = true;
        }
    }
}

if (have_panel_permissions($s_login['user'], 'tb_show')) {

    // include the javascript for detail requests
    $js_stack .= js_request_details();

    //
    // Reload button from the tb_show panel
    //
    if (isset($_POST['tb_show_reload'])) {
        $s_tables_counts = (boolean) get_request_data('tb_show_counts');
        $s_tables_cnames = (boolean) get_request_data('tb_show_cnames');
        $s_tables_def = (boolean) get_request_data('tb_show_def');
        $s_tables_comp = (boolean) get_request_data('tb_show_comp');
        $s_tables_comment = (boolean) get_request_data('tb_show_comments');
        $s_tables_valid = false;
    }

    $js_stack .= js_request_comment_area();
}

// init $s_domain for the columns form if necessary
if ($s_domains_valid == false  &&
    ($s_create_num > 0  ||  isset($col_add_flag))) {
    include_once './inc/domains.inc.php';

    $s_domains = get_domain_definitions($s_domains);
    $s_domains_valid = true;
}

// add javascript for the columns form
if ($s_connected === true  &&
    ($s_create_num > 0  ||  isset($col_add_flag))) {
    $js_stack .= js_collations($s_charsets)
               .js_request_table_columns();
}

//
// setup $s_tables[] and $s_fields[] if necessary 
//
if (($s_connected) && ($s_tables_valid == false)) {
    include_once './inc/get_tables.inc.php';
    if (get_tables($dbhandle)) {
        $s_tables_valid = true;
    }
}

//
// print out all the panels
//
$s_page = 'Tables';
$panels = $s_tables_panels;

require './inc/script_end.inc.php';

//
// mark all tables as opened or closed in $s_tables
//
function toggle_all_tables($tables, $status)
{
    foreach (array_keys($tables) as $name) {
        if (!$tables[$name]['is_view']) {
            $tables[$name]['status'] = $status;
        }
    }

    return $tables;
}

function get_column_fk_defs($cname, $iname)
{
    global $dbhandle;

    $defs = array('fk_name' => $cname);

    $trans = fbird_trans(TRANS_READ, $dbhandle);
    $sql = 'SELECT RDB$UPDATE_RULE,'
                .' RDB$DELETE_RULE'
           .' FROM RDB$REF_CONSTRAINTS'
          ." WHERE RDB\$CONSTRAINT_NAME='".$cname."'";
    $res = @fbird_query($trans, $sql) or ib_error(__FILE__, __LINE__, $sql);
    if ($res  && $row = fbird_fetch_row($res)) {
        fbird_free_result($res);
    }
    $defs['on_update'] = trim($row[0]);
    $defs['on_delete'] = trim($row[1]);

    $sql = 'SELECT I2.RDB$RELATION_NAME,'
                .' SE.RDB$FIELD_NAME'
           .' FROM RDB$INDICES I1'
     .' INNER JOIN RDB$INDICES I2 ON I1.RDB$FOREIGN_KEY=I2.RDB$INDEX_NAME'
     .' INNER JOIN RDB$INDEX_SEGMENTS SE ON I2.RDB$INDEX_NAME=SE.RDB$INDEX_NAME'
          ." WHERE I1.RDB\$INDEX_NAME='".$iname."'";
    $res = @fbird_query($trans, $sql) or ib_error(__FILE__, __LINE__, $sql);
    if ($res  && $row = fbird_fetch_row($res)) {
        fbird_free_result($res);
    }
    $defs['fk_table'] = trim($row[0]);
    $defs['fk_column'] = trim($row[1]);

    fbird_commit($trans);

    return $defs;
}
