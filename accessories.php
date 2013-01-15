<?php
// File           accessories.php / FirebirdWebAdmin
// Purpose        working with generators, triggers, domains, indexes, views,
//                stored procedures, user defined functions and exceptions
// Author         Lutz Brueckner <irie@gmx.de>
// Copyright      (c) 2000-2006 by Lutz Brueckner,
//                published under the terms of the GNU General Public Licence v.2,
//                see file LICENCE for details
// Created        <00/10/02 08:47:06 lb>
//
// $Id: accessories.php,v 1.50 2006/03/22 21:26:43 lbrueckner Exp $


require('./inc/script_start.inc.php');

// setup $s_tables[] and $s_fields[] if necessary 
if ($s_connected == TRUE  &&  $s_tables_valid == FALSE) {
    include_once('./inc/get_tables.inc.php');
    if (get_tables()){
        $s_tables_valid = TRUE;
    }
}


//
// index stuff
//
if (have_panel_permissions($s_login['user'], 'acc_index', TRUE)) {

    include('./inc/indices.inc.php');

    // ordering the index details table
    if (isset($_GET['idxorder'])) {
        if ($s_index_order == $_GET['order']) {
            $s_index_dir = ($s_index_dir == 'ASC') ? 'DESC' : 'ASC';
        }
        else {
            $s_index_order = $_GET['order'];
            $s_index_dir = 'ASC';
        }
    }

    // init the array indices[]
    $indices = get_indices($s_index_order, $s_index_dir);

    // delete the selected index
    if (isset($_POST['acc_index_del'])
    && isset($_POST['acc_index_dname'])
    && $_POST['acc_index_dname'] != '') {

        $dname = $_POST['acc_index_dname'];

        $deps = get_dependencies(OT_INDEX, $dname);
        if (count($deps) > 0) {
            $message = sprintf($MESSAGES['HAVE_DEPENDENCIES'], $acc_strings['Index'], $dname, dependencies_string($deps));
        }

        else {

            if ($s_cust['askdel'] == TRUE) {
                $s_confirmations['index'] = 
                    array('msg' => sprintf($MESSAGES['CONFIRM_INDEX_DELETE'], $dname),
                          'obj' => $dname);
            }
            else {
                drop_index($dname);
            }
        }
    }

    // the Create button on the Index panel was pushed
    if (isset($_POST['acc_index_create'])){
        $index_add_flag = TRUE;
    }

    // create the index from the form values
    if (isset($_POST['acc_ind_create_doit'])) {
        if (!create_index()) {
           // show the create index form again
           $index_add_flag = TRUE;
        }
    }

    // the Modify button on the Index panel was pushed
    if (isset($_POST['acc_index_mod'])  
    &&  !empty($_POST['acc_index_mname'])) {
        $s_mod_index = $_POST['acc_index_mname'];
    }

    // modify the index from the form values
    if (isset($_POST['acc_modind_doit'])) {
        if (modify_index($s_mod_index)) {
           // on success don't show the modify index form again
           unset($s_mod_index);
        }        
    }

    // modifying an index was canceled
    if (isset($_POST['acc_modind_cancel'])) {
        unset($s_mod_index);
    }
}


//
// generator stuff
//
if (have_panel_permissions($s_login['user'], 'acc_gen', TRUE)) {

    $quote = identifier_quote($s_login['dialect']);

    // init array generators[]
    $lsql = 'SELECT RDB$GENERATOR_NAME AS GNAME FROM RDB$GENERATORS '
            .'WHERE RDB$SYSTEM_FLAG IS NULL OR RDB$SYSTEM_FLAG = 0';
    $res = fbird_query($dbhandle, $lsql) or ib_error();

    while ($row = fbird_fetch_object($res)) {
        $lsql = 'SELECT gen_id(' . $quote . fb_escape_string($row->GNAME) . $quote . ', 0) AS VAL FROM RDB$DATABASE';
        $res1 = fbird_query($dbhandle, $lsql) or ib_error();
        $row1 = fbird_fetch_object($res1);
        $generators[] = array('name' => trim($row->GNAME),
                              'value' => (int)$row1->VAL);
        fbird_free_result($res1);
    }

    // one of the Drop buttons on the generator panel was pushed
    if ($name = drop_generator_pushed()) {

        $deps = get_dependencies(OT_GENERATOR, $name);
        if (count($deps) > 0) {
            $message = sprintf($MESSAGES['HAVE_DEPENDENCIES'], $acc_strings['Generator'], $name, dependencies_string($deps));
        }

        else {
            if ($s_cust['askdel'] == TRUE) {
                $s_confirmations['generator'] = 
                    array('msg' => sprintf($MESSAGES['CONFIRM_GEN_DELETE'], $name),
                          'obj' => $name);
            }
            else {
                drop_generator($name);
            }
        }
    }

    
    // one of the Set buttons on the generator panel was pushed   
    if ($name = set_generator_pushed()) {
    	$idx = get_generator_idx($name);
        $newvalue = intval($_POST['acc_gen_val_'.$idx]);
        $generators[$idx]['value'] = $newvalue;
    	$sql = 'SET GENERATOR ' . $quote . fb_escape_string($name) . $quote . ' TO ' . $newvalue;
    }

    // the Create button on the generators panel was pushed
    if (isset($_POST['acc_gen_create']) && $_POST['acc_gen_name'] != '') {
        $start = (!empty($_POST['acc_gen_start'])) ? intval($_POST['acc_gen_start']) : 0;
        $newname = strtoupper($_POST['acc_gen_name']);

    	$sql = array('CREATE GENERATOR ' . $quote . fb_escape_string($newname) .  $quote,
                     'SET GENERATOR ' . $quote . fb_escape_string($newname) . $quote . ' TO ' . $start);

        if (get_generator_idx($newname) === FALSE) {
            $generators[] = array('name' => $newname,
                                  'value'=> $start);
        }
    }
}
    

//
// domain stuff
//
if (have_panel_permissions($s_login['user'], 'acc_domain', TRUE)) {

    include('./inc/domains.inc.php');

    // include the javascript for detail requests
    $js_stack .= js_request_details(); 

    if (!isset($s_coldefs['dom'])) {
        $s_coldefs['mod'] = array();
    }

    // init the array $s_domains[]
    if ($s_domains_valid == FALSE  ||  isset($_POST['acc_domain_reload'])) {
        $s_domains = get_domain_definitions($s_domains);
        $s_domains_valid = TRUE;
    }

    // the Create button on the Domains panel was pushed
    if (isset($_POST['acc_domain_create'])){
        $s_coldefs['dom'] = array('default' => '',
                                  'check'   => '',
                                  'notnull' =>'no');
        $dom_add_flag = TRUE;
    }

    // create the domain
    if (isset($_POST['acc_dom_create_doit'])) {

        $s_coldefs['dom'] = save_datatype('dom');
        if (create_domain($s_coldefs['dom'])) {
            $s_domains[$s_coldefs['dom']['name']] = $s_coldefs['dom'];
            $s_domains[$s_coldefs['dom']['name']]['status'] = 'open';
            $s_domains_valid = FALSE;
        } 
        else {
            $dom_add_flag = TRUE;
        }
    }

    // the Modify button on the Domains panel was pushed
    if (isset($_POST['acc_domain_mod'])
    &&  $_POST['acc_domain_mname'] != '') {
        $s_mod_domain = $_POST['acc_domain_mname'];
        $s_coldefs['dom'] = $s_domains[$s_mod_domain];
        $s_coldefs['dom']['name'] = $s_mod_domain;
        $s_coldefs['old'] = $s_coldefs['dom'];
        $dom_mod_flag = TRUE;
    }

    // modifying the Domain was canceled
    if (isset($_POST['acc_moddom_cancel'])) {
        $s_mod_domain = '';
    }

    // build the sql-statement for altering the domain
    if (isset($_POST['acc_moddom_doit'])) {

        $s_coldefs['dom'] = save_datatype('dom');

        if (modify_domain($s_coldefs['old'], save_datatype('dom'))) {
            $s_domains = get_domain_definitions($s_domains);
        }
        else {
            $dom_mod_flag = TRUE;
        }
    }

    // the Delete button on the Domains panel was pushed
    if (isset($_POST['acc_domain_del'])
    && isset($_POST['acc_domain_dname'])
    && $_POST['acc_domain_dname'] != '') {

        $dname = $_POST['acc_domain_dname'];

        $deps = get_dependencies(OT_FIELD, $dname);
        if (count($deps) > 0) {
            $message = sprintf($MESSAGES['HAVE_DEPENDENCIES'], $acc_strings['Domain'], $dname, dependencies_string($deps));
        }

        else {

            if ($s_cust['askdel'] == TRUE) {
                $s_confirmations['domain'] = 
                    array('msg' => sprintf($MESSAGES['CONFIRM_DOMAIN_DELETE'], $dname),
                          'obj' => $dname);
            }
            else {
                drop_domain($dname);
                $s_domains_valid = FALSE;
            }
        }
    }
}


//
// trigger stuff
//
if (have_panel_permissions($s_login['user'], 'acc_trigger', TRUE)) {

    include('./inc/triggers.inc.php');

    // include the javascript for detail requests
    $js_stack .= js_request_details(); 

    // init the array $triggers[]
    if ($s_triggers_valid == FALSE  ||  isset($_POST['acc_trigger_reload'])) {
        $s_triggers = get_triggers($s_triggers);
        $s_triggers_valid = TRUE;
    }

    // 'Open All' button
    if (isset($_POST['acc_trigger_open'])) {
        $s_triggers = toggle_all_triggers($s_triggers, 'open');
    }

    // 'Close All' button
    if (isset($_POST['acc_trigger_close'])) {
        $s_triggers = toggle_all_triggers($s_triggers, 'close');
    }

    // the Create button on the Triggers panel was pushed
    if (isset($_POST['acc_trigger_create'])){
        $trigger_add_flag = TRUE;
        $s_triggerdefs = array('table'  => NULL,
                               'type'   => NULL,
                               'status' => NULL,
                               'source' => "BEGIN\n\nEND !!");
    }

    // create the new trigger ...
    if (isset($_POST['acc_trigger_create_doit'])){
        save_triggerdefs();
        if (create_trigger($s_triggerdefs) == TRUE) {
            $s_triggers[$s_triggerdefs['name']] = $s_triggerdefs;
            $s_triggers[$s_triggerdefs['name']]['display'] = 'open';
            $s_triggerdefs = array();
            $s_triggers_valid = FALSE;
        } else {
            $trigger_add_flag = TRUE;
        }
    }

    // the Modify button on the Triggers panel was pushed
    if (isset($_POST['acc_trigger_mod'])
    && ($_POST['acc_trigger_mod_name'] != '')) {
        $mname = $_POST['acc_trigger_mod_name'];
        $s_triggerdefs = $s_triggers[$mname];
        $s_triggerdefs['name'] = $mname;
        if (empty($s_triggers[$s_triggerdefs['name']]['source'])) {
            $s_triggers[$s_triggerdefs['name']]['source'] = get_trigger_source($s_triggerdefs['name']);
        }
        $s_triggerdefs['source'] = $s_triggers[$s_triggerdefs['name']]['source']."!!";
        $trigger_mod_flag = TRUE;
    }

    // modify the trigger ...
    if (isset($_POST['acc_trigger_mod_doit'])){
        $oldname = $s_triggerdefs['name'];
        save_triggerdefs();

        if (modify_trigger($oldname, $s_triggerdefs) == TRUE) {
            unset($s_triggers[$oldname]);
            $s_triggers[$s_triggerdefs['name']] = $s_triggerdefs;
            $s_triggers[$s_triggerdefs['name']]['display'] = 'open';
            $s_triggerdefs = array();
            $s_triggers_valid = FALSE;
        } else {
            $trigger_mod_flag = TRUE;
        }
    }

    // creating or modifying trigger was canceld
    if (isset($_POST['acc_trigger_create_cancel'])
    ||  isset($_POST['acc_trigger_mod_cancel'])) {
        $s_triggerdefs = array();
    }

    // the Drop button on the Triggers panel was pushed
    if (isset($_POST['acc_trigger_del'])
    && isset($_POST['acc_trigger_del_name'])
    && $_POST['acc_trigger_del_name'] != ''){

        $dname = $_POST['acc_trigger_del_name'];

        $deps = get_dependencies(OT_TRIGGER, $dname);
        if (count($deps) > 0) {
            $message = sprintf($MESSAGES['HAVE_DEPENDENCIES'], $acc_strings['Trigger'], $dname, dependencies_string($deps));
        }

        else {
            if ($s_cust['askdel'] == TRUE) {
                $s_confirmations['trigger'] = 
                    array('msg' => sprintf($MESSAGES['CONFIRM_TRIGGER_DELETE'], $dname),
                          'obj' => $dname);
            }
            else {
                drop_trigger($dname);
                $s_triggers_valid = FALSE;
            }
        }
    }
}


//
// procedure stuff
//
if (have_panel_permissions($s_login['user'], 'acc_proc', TRUE)) {

    include('./inc/procedures.inc.php');

    // include the javascript for detail requests
    $js_stack .= js_request_details(); 

    // init the array s_procedures
    if ($s_procedures_valid == FALSE  ||  isset($_POST['acc_proc_reload'])) {
        $s_procedures = get_procedures($s_procedures);
        $s_procedures_valid = TRUE;
    }

    // 'Open All' button
    if (isset($_POST['acc_proc_open'])) {
        $s_procedures = toggle_all_procedures($s_procedures, 'open');
    }

    // 'Close All' button
    if (isset($_POST['acc_proc_close'])) {
        $s_procedures = toggle_all_procedures($s_procedures, 'close');
    }

    // Create button on the procedures panel
    if (isset($_POST['acc_proc_create'])){
        $proc_add_flag = TRUE;
        $s_proceduredefs = array('source' => "CREATE PROCEDURE name ()\nRETURNS ()\nAS\nBEGIN\n\nEND!!");
    }

    // create the new procedure
    if (isset($_POST['acc_proc_create_doit'])){
        $s_proceduredefs['source'] = get_request_data('def_proc_source');
        if (create_procedure($s_proceduredefs) == TRUE) {
            $pname = get_procedure_name($s_proceduredefs['source']);
            list($in, $out) = get_procedure_parameters($pname);
            $s_procedures[$pname] = array('name'  => $pname,
                                          'owner' => $s_login['user'],
                                          'source'=> $s_proceduredefs['source'],
                                          'in'    => $in,
                                          'out'   => $out,
                                          'status'=> 'open');
            $s_proceduredefs = array();
            $s_procedures_valid = FALSE;
        } else {
            $proc_add_flag = TRUE;
        }
    }

    // Modify button on the procedures panel
    if (isset($_POST['acc_proc_mod'])
    &&  $_POST['acc_proc_mod_name'] != '') {
        $pname = $_POST['acc_proc_mod_name'];
        if ($s_procedures[$pname]['status' ] == 'close') {
            $s_procedures[$pname]['source'] = get_procedure_source($pname);
            list($in, $out) = get_procedure_parameters($pname);
            $s_procedures[$pname]['in']  = $in;
            $s_procedures[$pname]['out'] = $out;
        }
        $s_proceduredefs = array('name'   => $pname,
                                 'source' => procedure_modify_source($s_procedures[$pname]));
        $proc_mod_flag = TRUE;
    }

    // modify the procedure
    if (isset($_POST['acc_proc_mod_doit'])) {
        $s_proceduredefs['source'] = get_request_data('def_proc_source');
        if (create_procedure($s_proceduredefs) == TRUE) {
            $pname = $s_proceduredefs['name'];
            list($in, $out) = get_procedure_parameters($pname);
            $s_procedures[$pname]['in']  = $in;
            $s_procedures[$pname]['out'] = $out;
            $s_procedures[$pname]['source'] = get_procedure_source($pname);
            $s_proceduredefs = array();
            $s_procedures_valid = FALSE;
        } else {
            $proc_mod_flag = TRUE;
        }
    }

    // creating or modifying a procedure was canceled
    if (isset($_POST['acc_proc_create_cancel'])
    ||  isset($_POST['acc_proc_mod_cancel'])) {
        $s_proceduredefs = array();
    }

    // the Drop button on the procedures panel was pushed
    if (isset($_POST['acc_proc_del'])
    &&  $_POST['acc_proc_del_name'] != ''){

        $pname = $_POST['acc_proc_del_name'];

        $deps = get_dependencies(OT_PROCEDURE, $pname);
        if (count($deps) > 0) {
            $message = sprintf($MESSAGES['HAVE_DEPENDENCIES'], $acc_strings['SP'], $pname, dependencies_string($deps));
        }

        else {
            if ($s_cust['askdel'] == TRUE) {
                $s_confirmations['procedure'] = 
                    array('msg' => sprintf($MESSAGES['CONFIRM_SP_DELETE'], $pname),
                          'obj' => $pname);
            }
            else {
                drop_procedure($pname);
            }
        }
    }
}


//
// view stuff
//
if (have_panel_permissions($s_login['user'], 'acc_view', TRUE)) {

    include('./inc/views.inc.php');

    // include the javascript for detail requests
    $js_stack .= js_request_details(); 

    // 'Open All' button
    if (isset($_POST['acc_view_open'])) {
        $s_tables = toggle_all_views($s_tables, 'open');
    }

    // 'Close All' button
    if (isset($_POST['acc_view_close'])) {
        $s_tables = toggle_all_views($s_tables, 'close');
    }

    // the Create button
    if (isset($_POST['acc_view_create'])){
        $s_viewdefs = array('name'  => '',
                            'source'=> "CREATE VIEW name ()\nAS\nSELECT \nFROM \nWHERE ",
                            'check' => 'no');
        $view_add_flag = TRUE;
    }

    if (isset($_POST['acc_view_create_doit'])) {

        $s_viewdefs['source'] = trim($_POST['def_view_source']);
        $s_viewdefs['check']  = (isset($_POST['def_view_check'])) ? 'yes' : 'no';

        if (($vname = create_view($s_viewdefs)) == TRUE) {
            $s_tables[$vname] = array('status' => 'close', 'is_view' => TRUE);
            $s_viewdefs = array('name' => '', 'source' => '', 'check' => 'no');
            $s_tables_valid = FALSE;
        } else {
            $view_add_flag = TRUE;
        }

    }

    // the Modify button
    if (isset($_POST['acc_view_mod'])
    &&  $_POST['acc_modview_name'] != '') {

        $vname = $_POST['acc_modview_name'];
        $vsource = get_view_source($vname);
        $s_viewdefs = array('name'   => $vname,
                            'source' => 'CREATE VIEW '.$vname.' ('.implode(', ', table_columns($vname)).")\nAS\n"
                                       .str_replace('WITH CHECK OPTION', '', $vsource),
                            'check'  => (stristr($vsource, 'WITH CHECK OPTION') !== FALSE ? 'yes' : 'no')
                            );
        $viewdefs = $s_viewdefs;
        $view_mod_flag = TRUE;
    }

    // modifying a View was canceled
    if (isset($_POST['acc_modview_cancel'])) {
        $s_viewdefs = array('name' => '', 'source' => '', 'check' => 'no');
    }

    // modify the View
    if (isset($_POST['acc_modview_doit'])) {

        $viewdefs['source'] = get_magic_quotes_gpc() 
            ? stripslashes(trim($_POST['def_view_source']))
            : $_POST['def_view_source'];
        $viewdefs['check']  = (isset($_POST['def_view_check'])) ? 'yes' : 'no';

        if (drop_view($s_viewdefs['name'])) {
            if (create_view($viewdefs)) {
                unset($s_tables[$s_viewdefs['name']]);
                $s_tables[get_viewname($viewdefs['source'])] = array('status' => 'close', 'is_view' => TRUE);
                $s_viewdefs = array('name' => '', 'source' => '', 'check' => 'no');
                $s_tables_valid = FALSE;
            }
            else {
                create_view($s_viewdefs);
                $view_mod_flag = TRUE;
            }
        }
    }

    // the Delete button
    if (isset($_POST['acc_view_del'])
    && isset($_POST['acc_delview_name'])
    && $_POST['acc_delview_name'] != '') {

        $dname = $_POST['acc_delview_name'];

        $deps = get_dependencies(OT_FIELD, $dname);
        if (count($deps) > 0) {
            $message = sprintf($MESSAGES['HAVE_DEPENDENCIES'], $acc_strings['View'], $dname, dependencies_string($deps));
        }

        else {

            if ($s_cust['askdel'] == TRUE) {
                $s_confirmations['view'] = 
                    array('msg' => sprintf($MESSAGES['CONFIRM_VIEW_DELETE'], $dname),
                          'obj' => $dname);
            }
            else {
                drop_view($dname);
                $s_tables_valid = FALSE;
            }
        }
    }

    // Reload button
    if (isset($_POST['acc_show_reload'])) {

        $s_views_counts = isset($_POST['acc_show_counts']) ? TRUE : FALSE;
        $s_tables_valid  = FALSE;
    }
}


//
// udf stuff
//
if (have_panel_permissions($s_login['user'], 'acc_udf', TRUE)) {

    include('./inc/udfs.inc.php');

    // reload button
    if (isset($_POST['acc_udf_reload'])) {
        $s_udfs_valid = FALSE;
    }

    // ordering the udfs table
    if (isset($_GET['udforder'])) {
        if ($s_udfs_order == $_GET['order']) {
            $s_udfs_dir = ($s_udfs_dir == 'ASC') ? 'DESC' : 'ASC';
        }
        else {
            $s_udfs_order = $_GET['order'];
            $s_udfs_dir = 'ASC';
        }

        $s_udfs_valid = FALSE;
    }

    // init the array s_udfs
    if ($s_udfs_valid == FALSE  ||  isset($_POST['acc_udf_reload'])) {
        $s_udfs = get_udfs($s_udfs_order, $s_udfs_dir);
        $s_udfs_valid = TRUE;
    }

    // the Drop button on the udf panel was pushed
    if (isset($_POST['acc_udf_del'])
    && isset($_POST['acc_udf_dname'])
    && $_POST['acc_udf_dname'] != ''){

        $dname = $_POST['acc_udf_dname'];

        $deps = get_dependencies(OT_UDF, $dname);
        if (count($deps) > 0) {
            $message = sprintf($MESSAGES['HAVE_DEPENDENCIES'], $acc_strings['UDF'], $dname, dependencies_string($deps));
        }

        else {
            if ($s_cust['askdel'] == TRUE) {
                $s_confirmations['udf'] = 
                    array('msg' => sprintf($MESSAGES['CONFIRM_UDF_DELETE'], $dname),
                          'obj' => $dname);
            }
            else {
                $dname == '-=ALL_DEFINED_UDFS=-' ? drop_all_udfs($s_udfs) : drop_udf($dname);
            }
        }
    }
}


//
// exception stuff
//
if (have_panel_permissions($s_login['user'], 'acc_exc', TRUE)) {

    include('./inc/exceptions.inc.php');

    // reload button
    if (isset($_POST['acc_exc_reload'])) {
        $s_udfs_valid = FALSE;
    }

    // ordering the udfs table
    if (isset($_GET['excorder'])) {
        if ($s_exceptions_order == $_GET['order']) {
            $s_exceptions_dir = ($s_exceptions_dir == 'ASC') ? 'DESC' : 'ASC';
        }
        else {
            $s_exceptions_order = $_GET['order'];
            $s_exceptions_dir = 'ASC';
        }

        $s_exceptions_valid = FALSE;
    }

    // the create button on the exceptions panel
    if (isset($_POST['acc_exc_create'])){
        $exc_add_flag = TRUE;
        $s_exception_defs = array('name' => '',
                                  'msg'  => '');
    }

    // create the new exception
    if (isset($_POST['acc_exc_create_doit'])){
        $s_exception_defs = array('name' => get_request_data('def_exc_name'),
                                  'msg'  => get_request_data('def_exc_msg'));
        if (create_exception($s_exception_defs) == TRUE) {
            $s_exception_defs = array();
            $s_exceptions_valid = FALSE;
        } else {
            $exc_add_flag = TRUE;
        }
    }

    // the modify button on the exceptions panel
    if (isset($_POST['acc_exc_mod'])
    && ($_POST['acc_exc_mod_name'] != '')) {
        $s_exception_defs = array('name' => $_POST['acc_exc_mod_name'],
                                  'msg'  => $s_exceptions[$_POST['acc_exc_mod_name']]);
        $exc_mod_flag = TRUE;
    }

    // modify the exception
    if (isset($_POST['acc_exc_mod_doit'])){
        $s_exception_defs['msg'] = get_request_data('def_exc_msg');
        if (modify_exception($s_exception_defs) == TRUE) {
            $s_exception_defs = array();
            $s_exceptions_valid = FALSE;
        } else {
            $exc_mod_flag = TRUE;
        }
    }

    // creating or modifying exception was canceled
    if (isset($_POST['acc_exc_create_cancel'])
    ||  isset($_POST['acc_exc_mod_cancel'])) {
        $s_exception_defs = array();
    }

    // init the array s_exceptions
    if ($s_exceptions_valid == FALSE  ||  isset($_POST['acc_exc_reload'])) {
        $s_exceptions = get_exceptions($s_exceptions_order, $s_exceptions_dir);
        $s_exceptions_valid = TRUE;
    }

    // the Drop button on the udf panel was pushed
    if (isset($_POST['acc_exc_del'])
    && isset($_POST['acc_exc_del_name'])
    && $_POST['acc_exc_del_name'] != ''){

        $dname = $_POST['acc_exc_del_name'];

        $deps = get_dependencies(OT_EXCEPTION, $dname);
        if (count($deps) > 0) {
            $message = sprintf($MESSAGES['HAVE_DEPENDENCIES'], $acc_strings['Exception'], $dname, dependencies_string($deps));
        }

        else {
            if ($s_cust['askdel'] == TRUE) {
                $s_confirmations['exc'] = 
                    array('msg' => sprintf($MESSAGES['CONFIRM_EXC_DELETE'], $dname),
                          'obj' => $dname);
            }
            else {
                drop_exception($dname);
            }
        }
    }
}


// setup $s_tables[] and $s_fields[] if necessary 
if ($s_connected == TRUE  &&  $s_tables_valid == FALSE) {

    include_once('./inc/get_tables.inc.php');

    if (get_tables()){
        $s_tables_valid = TRUE;
    }
}


//
// deleting of a subject is confirmed
//
if (isset($_POST['confirm_yes'])) {
    switch ($_POST['confirm_subject']) {
        case 'index':
            drop_index($s_confirmations['index']['obj']);
            break;
        case 'trigger':
            drop_trigger($s_confirmations['trigger']['obj']);
            break;
        case 'domain':
            drop_domain($s_confirmations['domain']['obj']);
            break;
        case 'generator':
            drop_generator($s_confirmations['generator']['obj']);
            break;
        case 'procedure':
            drop_procedure($s_confirmations['procedure']['obj']);
            break;
        case 'view':
            drop_view($s_confirmations['view']['obj']);
            break;
        case 'udf':
            $s_confirmations['udf']['obj'] == '-=ALL_DEFINED_UDFS=-' ? drop_all_udfs($s_udfs) : drop_udf($s_confirmations['udf']['obj']);
            break;
        case 'exc':
            drop_exception($s_confirmations['exc']['obj']);
            break;
    }

    unset($s_confirmations[$_POST['confirm_subject']]);
}


// deleting a subject is canceled
if (isset($_POST['confirm_no'])) {
    unset($s_confirmations[$_POST['confirm_subject']]);
}


// 
// perform the sql-statement in $sql
//
if ($sql != ''  &&  empty($ib_error)) {
    if (is_array($sql)) {
        foreach($sql as $idx => $cmd) {
            if (!@fbird_query($dbhandle, $sql[$idx])) {
                $ib_error .= fbird_errmsg()."<br>\n>";
            }
        }
    }
    else {
        if (!@fbird_query($dbhandle, $sql)) {
            $ib_error = fbird_errmsg();
        }
    }
}


//
// print out all the panels
//
$s_page = 'Accessories';
$panels = $s_accessories_panels;

require('./inc/script_end.inc.php');



//  *** end of script, some helper functions following  ***

//
// return TRUE if one of the Drop buttons on the generators panel was pushed
//
function drop_generator_pushed() {
    global $generators;

    if (!is_array($generators)) {
        return FALSE;
    }
    foreach ($generators as $idx => $gen) {
        if (isset($_POST['acc_gen_drop_'.$idx])) {
            return $gen['name'];
        }
    }
    return FALSE;
}


//
// return TRUE if one of the Set buttons on the generators panel was pushed
//
function set_generator_pushed() {
    global $generators;

    if (!is_array($generators)) {
        return FALSE;
    }
    foreach ($generators as $idx => $gen) {
        if (isset($_POST['acc_gen_set_'.$idx])) {
            return $gen['name'];
        }
    }
    return FALSE;
}


//
// return the index for the generator $name in  the array $generators[]
// or FALSE if not found
//
function get_generator_idx($name) {
    global $generators;

    if (!is_array($generators)) {
        return FALSE;
    }
    foreach ($generators as $idx => $gen) {
        if ($gen['name'] == $name) {
            return ($idx);
        }
    }
    return FALSE;
}


function drop_generator($name) {
    global $generators, $dbhandle, $ib_error;

    $lsql = 'DELETE FROM RDB$GENERATORS WHERE RDB$GENERATOR_NAME=\'' . fb_escape_string($name) . "'"; 
    if (!@fbird_query($dbhandle, $lsql)) {
        $ib_error = fbird_errmsg();
    }
    else {
        // remove the dropped generator from the array
        $idx = get_generator_idx($name);
        array_splice($generators, $idx, 1);
    }
}

?>
