<?php
// Purpose        working with data (import/export, type in, ...)
// Author         Lutz Brueckner <irie@gmx.de>
// Copyright      (c) 2000-2006 by Lutz Brueckner,
//                published under the terms of the GNU General Public Licence v.2,
//                see file LICENCE for details

require './inc/script_start.inc.php';
require './inc/foreign_keys.inc.php';
require './inc/DataForm.php';

//
// setup $s_tables[] and $s_fields[] if necessary 
//
if ($s_connected && $s_tables_valid == false) {
    include_once './inc/get_tables.inc.php';
    if (get_tables($dbhandle)) {
        $s_tables_valid = true;
    }
}

require './inc/handle_watchtable.inc.php';

//
// handle foreign key lookup configuration
//
$customize_changed = false;
if (isset($_POST['dt_column_config_save'])) {
    $column = get_request_data('dt_column_config_column');
    $table = get_request_data('dt_column_config_table');
    $fk_column = get_request_data('dt_column_config_fk_column');
    if ($fk_column == '') {
        unset($s_cust['fk_lookups'][$table][$column]);
        if (empty($s_cust['fk_lookups'][$table])) {
            unset($s_cust['fk_lookups'][$table]);
        }
    } else {
        $s_cust['fk_lookups'][$table][$column] = $fk_column;
    }
    $customize_changed = true;
}

// 
// handle the customize cookie settings
// when 'dt_(enter|edit)_(insert|ready|save|cancel)'-button was pushed
if (array_filter(array_keys($_POST), create_function('$a', 'return preg_match("/dt_(enter|edit)_(insert|ready|save|cancel)/", $a);'))) {
    if ((isset($_POST['dt_config_fk_lookup'])  &&  $s_cust['enter']['fk_lookup'] == false)  ||
        (!isset($_POST['dt_config_fk_lookup'])  &&  $s_cust['enter']['fk_lookup'] == true)) {

        // 'foreign key lookup'-setting is changed
        $s_cust['enter']['fk_lookup'] = isset($_POST['dt_config_fk_lookup']);
        $customize_changed = true;
    }

    if (isset($_POST['dt_enter_insert'])  ||  isset($_POST['dt_enter_ready'])) {
        if ((isset($_POST['dt_config_more'])  &&  $s_cust['enter']['another_row'] == false)  ||
            (!isset($_POST['dt_config_more'])  &&  $s_cust['enter']['another_row'] == true)) {

            // 'insert another row'-setting is changed
            $s_cust['enter']['another_row'] = isset($_POST['dt_config_more']);
            $customize_changed = true;
        }
    } else {
        if ((isset($_POST['dt_config_as_new'])  &&  $s_cust['enter']['as_new'] == false)  ||
            (!isset($_POST['dt_config_as_new'])  &&  $s_cust['enter']['as_new'] == true)) {

            // 'foreign key lookup'-setting is changed
            $s_cust['enter']['as_new'] = isset($_POST['dt_config_as_new']);
            $customize_changed = true;
        }
    }
}
if ($customize_changed == true) {
    set_customize_cookie($s_cust);
}

if (isset($s_edit_where)  && count($s_edit_where) > 0) {
    include './inc/handle_editdata.inc.php';
}

//
// select on the dt_enter-panel was pushed
//
if (isset($_POST['dt_enter_select'])) {
    $s_enter_name = get_request_data('dt_enter_name');

    if (is_array($s_fields[$s_enter_name])) {
        $s_fields = get_table_defaults_sources($s_enter_name, $s_fields);
        $s_fields = get_table_computed_sources($s_enter_name, $s_fields);

        $s_enter_values = init_enter_values($s_fields[$s_enter_name]);
    }
}

//
// the Insert button on the dt_enter-panel was pushed
//
if (have_panel_permissions($s_login['user'], 'dt_enter', true)
&&  isset($_POST['dt_enter_insert'])) {

    // the origin types of domain-based columns are needed
    if (!$s_domains_valid) {
        include_once './inc/domains.inc.php';

        $s_domains = get_domain_definitions($s_domains);
        $s_domains_valid = true;
    }

    // needed for the have_active_trigger() check
    include_once './inc/triggers.inc.php';
    if ($s_triggers_valid == false) {
        $s_triggers = get_triggers($s_triggers);
        $s_triggers_valid = true;
    }

    $idx = 0;
    $bindargs = $cols = $s_enter_values = array();
    foreach ($s_fields[$s_enter_name] as $field) {
        if (isset($field['comp'])) {
            $s_enter_values[] = $field['csource'];
            ++$idx;
            continue;
        }

        if (isset($_FILES['dt_enter_file_'.$idx])  &&
            !empty($_FILES['dt_enter_file_'.$idx]['name'])) {
            $value = $_FILES['dt_enter_file_'.$idx];
            $s_enter_values[] = $value;
        } else {
            $value = get_request_data('dt_enter_field_'.$idx);
            $s_enter_values[] = $value;
        }

        // type of the field or the origin type of a domain-based field
        $type = !isset($field['domain']) ? $field['type'] : $s_domains[$field['type']]['type'];

        // take care for autoincrement fields implemented with before insert trigger and generator
        if ($idx == 0  &&  $value === ''  &&
            in_array($type, array('INTEGER', 'BIGINT', 'SMALLINT'))  &&
            isset($field['notnull'])  &&  $field['notnull'] == 'Yes'  &&
            have_active_trigger($s_triggers, $s_enter_name, 'before', 'insert')) {
            ++$idx;
            continue;
        }

        switch ($type) {
        case 'CHARACTER':
        case 'VARCHAR':
        case 'DATE':
        case 'TIME':
        case 'TIMESTAMP':
            $bindargs[] = empty($field['notnull'])  &&  empty($value) ? null : "$value";
            break;
        case 'BLOB' :
            // blob from file-upload
            if (is_array($value)  &&  !empty($value['name'])) {
                $bfname = $value['tmp_name'];
                $bfhandle = fopen($bfname, 'r') or die('cannot open file '.$bfname);
                $bstr = fbird_blob_import($dbhandle, $bfhandle);
                fclose($bfhandle);
                $bindargs[] = $bstr;
            }
            // blob from textarea
            elseif (!empty($value)) {
                $bhandle = fbird_blob_create($dbhandle) or die('cannot create blob: '.__FILE__.', '.__LINE__);
                fbird_blob_add($bhandle, $value);
                $bstr = fbird_blob_close($bhandle);
                $bindargs[] = $bstr;
            } else {
                $bindargs[] = null;
            }
            break;
        default:
            if ($value === '') {
                $value = null;
            }

            $bindargs[] = $value;
        }
        $cols[] = $field['name'];
        ++$idx;
    }

    if (count($cols) > 0) {
        $ib_error = insert_row($s_enter_name, $cols, $bindargs);

        if (empty($ib_error)) {
            $s_watch_buffer = '';
            $s_enter_values = $s_cust['enter']['another_row'] == false
                ? array()
                : init_enter_values($s_fields[$s_enter_name]);
        }
    }
}

//
// the Ready button on the dt_enter-panel was pushed
//
if (isset($_POST['dt_enter_ready'])  ||
    (isset($_POST['dt_enter_insert'])  &&  $s_cust['enter']['another_row'] == false  &&  empty($ib_error))) {
    $s_enter_name = '';
    $s_enter_values = array();
}

//
// the Export button on the csv-panel was pushed
//
if (have_panel_permissions($s_login['user'], 'dt_export', true)) {
    include './inc/export.inc.php';

    if (empty($s_export)) {
        $s_export = get_export_defaults();
    }

    // set default values for general options and selected format options
    if (isset($_POST['dt_export_defaults'])) {
        $s_export = set_export_defaults($s_export['format'], $s_export);
    }

    if (isset($_POST['dt_export_doit'])) {
        $s_export = get_export_form_data($s_export);
        list($warning, $error) = check_export_form_data($s_export);

        if (empty($error)  &&  empty($warning)) {

            // display result in an iframe by iframe_content.php
            if ($s_export['target']['option'] == 'screen') {

                // remove pending dbstat-jobs from session
                $s_iframejobs = array_filter($s_iframejobs, create_function('$a', '$a["job"]!="export";'));

                $iframekey_export = md5(uniqid('export'));
                $s_iframejobs[$iframekey_export] = array('job' => 'export',
                                                         'data' => $s_export,
                                                         'timestamp' => time(), );
            }

            // write result into a file
            else {
                $filename = export_filename($s_export);
                send_export_headers(get_export_mimetype($s_export['format']), $filename);

                export_data($s_export);

                // if we don't stop the execution, the client will download 
                // all the html from the panels ...
                globalize_session_vars();
                exit();
            }
        }
    }

    $js_stack .= js_data_export();
}

//
// the Import button on the csv-panel was pushed
//
if (have_panel_permissions($s_login['user'], 'dt_import', true)
&&  isset($_POST['dt_import_doit'])) {

    // import empty values as NULL option
    $s_csv['import_null'] = isset($_POST['dt_import_null']) ? true : false;

    if ($_POST['dt_import_table'] == '') {
        $warning .= $WARNINGS['SELECT_TABLE_FIRST'];
    } elseif (isset($_FILES['dt_import_file']['name'])
        &&  $_FILES['dt_import_file']['name'] == '') {
        $warning .= $WARNINGS['SELECT_FILE_FIRST'];
    } else {
        $ifile = $_FILES['dt_import_file']['tmp_name'];
        $itable = $_POST['dt_import_table'];
        $ihandle = fopen($ifile, 'r') or die('Error opening '.$ifile);

        // fill $columns[] with the $s_fields[] elements for $itable
        // but ignore blob fields and computed fields
        foreach ($s_fields[$itable] as $field) {
            if (($field['type'] == 'BLOB'  &&  $field['stype'] != 1)  ||        // only text-blobs are handled
                (isset($field['comp'])  &&  $field['comp'] == 'Yes')) {         // no computed columns please

                continue;
            }
            $col_names[] = $field['name'];
            $columns[] = $field;
        }

        $sql = 'INSERT INTO '.$itable.'('.implode(', ', $col_names).')'
                              .' VALUES ('.implode(', ', array_fill(0, count($col_names), '?')).')';
        $query = fbird_prepare($sql) or ib_error(__FILE__, __LINE__, $sql);

        // string of variablenames needed for fbird_execute()
        $var_string = '';
        foreach (array_keys($col_names) as $idx) {
            $var_string .= '$data['.$idx.'],';
        }
        $var_string = substr($var_string, 0, -1);

        // find indexes of blob fields and NULL-able fields
        $blob_fields = array();
        $null_fields = array();
        $idx = 0;
        foreach ($s_fields[$itable] as $field) {
            if ($field['type'] == 'BLOB') {
                $blob_fields[] = $idx;
            }

            if ($s_csv['import_null'] == true &&
                (!isset($field['notnull'])  || empty($field['notnull']))) {
                $null_fields[] = $idx;
            }
            ++$idx;
        }

        // assemble the INSERT-query for putting all values into the selected table,
        // but omit blob fields and computed fields
        $csv_cnt = 0;
        while ($data = fgetcsv($ihandle, MAX_CSV_LINE)) {

            // handle NULL values
            if (!empty($null_fields)) {
                foreach ($null_fields as $idx) {
                    if ($data[$idx] == '') {
                        $data[$idx] = null;
                    }
                }
            }

            // handle blobs
            if (!empty($blob_fields)) {
                foreach ($blob_fields as $idx) {
                    if (empty($data[$idx])) {
                        $data[$idx] = null;
                    } else {
                        $blob_handle = fbird_blob_create($dbhandle) or ib_error(__FILE__, __LINE__);
                        fbird_blob_add($blob_handle, $data[$idx]);
                        $data[$idx] = fbird_blob_close($blob_handle) or ib_error(__FILE__, __LINE__);
                    }
                }
            }

            call_user_func_array('fbird_execute', array_merge(array($query), $data))
                or $ib_error = ib_error(__FILE__, __LINE__, $query);

            // an error occurs during the import
            if (!empty($ib_error)) {
                break;
            }
            ++$csv_cnt;
        }
        fclose($ihandle);
        $sql = '';

        // cleanup the watchtable output buffer
        $s_watch_buffer = '';

        $message .= sprintf($MESSAGES['CSV_IMPORT_COUNT'], $csv_cnt, $itable);
    }
}

$js_stack .= js_request_column_config_form();

//
// print out all the panels
//
$s_page = 'Data';
$panels = $s_data_panels;

require './inc/script_end.inc.php';

function init_enter_values($fields)
{
    $values = array();
    foreach ($fields as $field) {
        if (isset($field['default'])) {
            $values[] = $field['dsource'];
        } elseif (isset($field['comp'])) {
            $values[] = $field['csource'];
        } else {
            $values[] = '';
        }
    }

    return $values;
}
