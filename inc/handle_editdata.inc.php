<?php
// File           inc/handle_editdata.inc.php / FirebirdWebAdmin
// Purpose        provides the handling of the dt_edit-panel for sql.php and data.php
// Author         Lutz Brueckner <irie@gmx.de>
// Copyright      (c) 2000, 2001, 2002, 2003, 2004, 2005 by Lutz Brueckner,
//                published under the terms of the GNU General Public Licence v.2,
//                see file LICENCE for details

//
// check if and which 'done' or 'cancel' button on which dt_edit panel was clicked
//

foreach ($_POST as $name => $value) {
    if (preg_match('/dt_edit_(cancel|save)_([0-9]+)/', $name, $matches)) {

        // index for array $s_edit_where[]
        $instance = $matches[2];
        $table = $s_edit_where[$instance]['table'];
        $job = $matches[1];
        $success = false;
        if ($job == 'save') {

            // the origin types of domain-based columns are needed
            if (!$s_domains_valid) {
                include_once './inc/domains.inc.php';

                $s_domains = get_domain_definitions($s_domains);
                $s_domains_valid = true;
            }

            $bindargs = $cols = $s_edit_values[$instance] = array();
            $k = 0;
            foreach ($s_fields[$table] as $field) {
                if (isset($field['comp'])) {
                    $s_edit_values[$instance][] = $field['csource'];
                    ++$k;
                    continue;
                }

                if (isset($_FILES['dt_edit_file_'.$instance.'_'.$k])  &&
                    !empty($_FILES['dt_edit_file_'.$instance.'_'.$k]['name'])) {
                    $value = $_FILES['dt_edit_file_'.$instance.'_'.$k];
                    $s_edit_values[$instance][] = $value;
                } else {
                    $value = get_request_data('dt_edit_field_'.$instance.'_'.$k);
                    $s_edit_values[$instance][] = $value;
                }

                // type of the field or the origin type of a domain-based field
                $type = !isset($field['domain']) ? $field['type'] : $s_domains[$field['type']]['type'];

                switch ($type) {
                    case 'CHARACTER' :
                    case 'VARCHAR'   :
                    case 'DATE'      :
                    case 'TIME'      :
                    case 'TIMESTAMP' :
                        $bindargs[] = empty($field['notnull'])  &&  empty($value) ? null : $value;
                        break;
                    case 'BLOB' :
                        // blob from file-upload
                        if (is_array($value)  &&  strlen(trim($value['name'])) > 0) {
                            $bfname = $value['tmp_name'];
                            $bfhandle = fopen($bfname, 'r') or die('cannot open file '.$bfname);
                            $bstr = fbird_blob_import($dbhandle, $bfhandle);
                            fclose($bfhandle);
                            $bindargs[] = $bstr;
                        }
                        // drop blob checkbox
                        elseif (isset($_POST['dt_edit_drop_blob_'.$instance.'_'.$k])
                                && empty($field['notnull'])) {
                            $bindargs[] = null;
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
                        if ($value == '') {
                            $value = null;
                        }
                        $bindargs[] = empty($field['notnull'])  &&  strlen($value) == 0 ? null : $value;
                }
                $cols[] = $field['name'];
                ++$k;
            }

            if (count($bindargs) > 0) {
                $fb_error = $s_cust['enter']['as_new'] == true
                    ? insert_row($table, $cols, $bindargs)
                    : update_row($table, $cols, $bindargs, substr($s_edit_where[$instance]['where'], 6));

                if (empty($fb_error)) {
                    $success = true;
                    $s_enter_values = array();
                    $s_watch_buffer = '';

                    // cleanup the watchtable output buffer
                    $s_watch_buffer = '';
                }
            }
        }

        $panels_arrayname = get_panel_array($_SERVER['SCRIPT_NAME']);

        if ($success  ||  $job == 'cancel') {
            // remove the dt_edit panel
            $name = 'dt_edit'.$instance;
            $idx = get_panel_index($$panels_arrayname, $name);
            array_splice($$panels_arrayname, $idx, 1);
            unset($s_edit_where[$instance]);
            unset($s_edit_values[$instance]);
            if (count($s_edit_where) == 0) {
                $s_edit_idx = 0;
            }
        }
    }
}
