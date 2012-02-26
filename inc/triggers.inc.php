<?php
// File           triggers.inc.php / ibWebAdmin
// Purpose        functions working with triggerss, included from accessories.php
// Author         Lutz Brueckner <irie@gmx.de>
// Copyright      (c) 2000, 2001, 2002, 2003, 2004, 2005 by Lutz Brueckner,
//                published under the terms of the GNU General Public Licence v.2,
//                see file LICENCE for details
// Created        <03/01/19 15:52:24 lb>
//
// $Id: triggers.inc.php,v 1.23 2005/08/27 21:07:40 lbrueckner Exp $


//
// get the properties for all defined triggers 
//
function get_triggers($oldtriggers) {
    global $dbhandle;

    $lsql = 'SELECT RDB$TRIGGER_NAME AS NAME,'
                 .' RDB$RELATION_NAME AS TNAME,'
                 .' RDB$TRIGGER_SEQUENCE AS POS,'
                 .' RDB$TRIGGER_TYPE AS TTYPE,'
                 .' RDB$TRIGGER_SOURCE AS TSOURCE,'
                 .' RDB$TRIGGER_INACTIVE AS STATUS'
            .' FROM RDB$TRIGGERS'
           .' WHERE (RDB$SYSTEM_FLAG IS NULL  OR  RDB$SYSTEM_FLAG=0)'
             .' AND RDB$TRIGGER_NAME NOT IN (SELECT RDB$TRIGGER_NAME FROM RDB$CHECK_CONSTRAINTS)'
           .' ORDER BY RDB$TRIGGER_NAME';
    $res = fbird_query($dbhandle, $lsql) or ib_error(__FILE__, __LINE__, $lsql);

    $triggers = array();
    while ($obj = fbird_fetch_object($res)) {
        $name = trim($obj->NAME);
        $display = (isset($oldtriggers[$name])) ? $oldtriggers[$name]['display'] : 'close';

        // get the source code for the open triggers 
        $tsource = '';
        if ((isset($oldtriggers[$name]) &&  $display == 'open')
        ||  isset($_POST['acc_trigger_mod'])) {

            $bid = fbird_blob_open($obj->TSOURCE);
            $arr = fbird_blob_info($obj->TSOURCE);

            // $arr[0] holds the blob length
            $tsource = fbird_blob_get($bid, $arr[0]);
            fbird_blob_close($bid);

            // discard the 'AS ' from the source-string
            $tsource = substr(trim($tsource), 3);
        }
        $triggers[$name] = array('table'   => trim($obj->TNAME),
                                 'phase'   => get_trigger_phase($obj->TTYPE),
                                 'type'    => get_trigger_type($obj->TTYPE),
                                 'pos'     => $obj->POS,
                                 'status'  => get_trigger_status($obj->STATUS),
                                 'source'  => $tsource,
                                 'display' => $display);
    }

    return $triggers;
}


//
// create trigger from the definitions in $triggerdefs
//
function create_trigger($triggerdefs) {
    global $s_login, $isql, $binary_output, $binary_error;
    
    $isql = trigger_create_source($triggerdefs);

    if (DEBUG) add_debug('isql', __FILE__, __LINE__);

    // this must be done by isql because 'create trigger' is not supported from within php
    list($binary_output, $binary_error) = isql_execute($isql, $s_login['user'], $s_login['password'], $s_login['database'], $s_login['host']);

    return ($binary_error != ''  ||  count($binary_output) > 0) ? FALSE : TRUE;
}


function trigger_create_source($triggerdefs) {

    $isql  = "SET TERM !! ;\n"
           . 'CREATE TRIGGER '.$triggerdefs['name'].' FOR '.$triggerdefs['table']
            .' '.$triggerdefs['status'].' '.$triggerdefs['phase'].' '.implode(' OR ', $triggerdefs['type']);
    if ($triggerdefs['pos'] != 0) {
        $isql .= ' POSITION '.$triggerdefs['pos'];
    }
    
    $isql .= " AS\n".$triggerdefs['source']."\n"
             ."SET TERM ; !!\n";

    return $isql;
}


function modify_trigger($name, $triggerdefs) {
    global $s_login, $isql, $binary_output, $binary_error;

    $isql = 'DROP TRIGGER '.$name.";\n"
            .trigger_create_source($triggerdefs);

    if (DEBUG) add_debug('isql', __FILE__, __LINE__);

    list($binary_output, $binary_error) = isql_execute($isql, $s_login['user'], $s_login['password'], $s_login['database'], $s_login['host']);

    return ($binary_error != ''  ||  count($binary_output) > 0) ? FALSE : TRUE;
}


//
// drop the trigger $name off the database
//
function drop_trigger($name) {
    global $s_triggers, $dbhandle, $ib_error;
    global $lsql;

    $lsql = 'DROP TRIGGER '.$name;
    if (DEBUG) add_debug('lsql', __FILE__, __LINE__);
    if (!@fbird_query($dbhandle, $lsql)) {
        $ib_error = fbird_errmsg();
    }
    else {
        unset($s_triggers[$name]);
    }
}


//
// deliver the html for an opened trigger on the triggers panel
//
function get_opened_trigger($name, $trigger, $url) {
    global $dbhandle, $acc_strings, $ptitle_strings;

    $type_str     = implode('<br>', $trigger['type']);
    $red_triangle = get_icon_path(DATAPATH, ICON_SIZE) . 'red_triangle.png';

    $html = <<<EOT
        <nobr>
          <a href="$url" class="dtitle"><img src="$red_triangle" alt="${ptitle_strings['Close']}" title="${ptitle_strings['Close']}" border="0" hspace="7"><b>$name</b></a>
        </nobr>
        <nobr>
        <table cellpadding="0" cellspacing="0">
          <tr>
            <td width="26">
            </td>
            <td>
              <table border cellpadding="3" cellspacing="0">
                <tr>
                  <th>${acc_strings['Table']}</th>
                  <th>${acc_strings['Phase']}</th>
                  <th>${acc_strings['Type']}</th>
                  <th>${acc_strings['Pos']}</th>
                  <th>${acc_strings['Status']}</th>
                  <th>${acc_strings['Source']}</th>
                </tr>
                <tr>
	          <td valign="top">${trigger['table']}</td>
	          <td valign="top">${trigger['phase']}</td>
	          <td valign="top">$type_str</td>
	          <td valign="top">${trigger['pos']}</td>
	          <td valign="top">${trigger['status']}</td>
	          <td valign="top"><pre>${trigger['source']}</pre></td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
      </nobr>

EOT;

    return $html;
}


//
// return the definition sourcecode for a trigger
//
function get_trigger_source($name) {
    global $dbhandle;

    $tsource = '';
    $lsql = 'SELECT RDB$TRIGGER_SOURCE AS TSOURCE'
            .' FROM RDB$TRIGGERS'
           ." WHERE RDB\$TRIGGER_NAME='".$name."'";
    $res = fbird_query($dbhandle, $lsql) or ib_error(__FILE__, __LINE__, $lsql);
    $obj = fbird_fetch_object($res);

    if (is_object($obj)) {
        $bid = fbird_blob_open($obj->TSOURCE);
        $arr = fbird_blob_info($obj->TSOURCE);

        // $arr[0] holds the blob length
        $tsource = trim(fbird_blob_get($bid, $arr[0]));
        fbird_blob_close($bid);

        // discard the 'AS ' from the source-string
        $tsource = substr($tsource, 3);
    }
    fbird_free_result($res);

    return $tsource;
}


//
// return the string equivalent for the trigger-status $int
//
function get_trigger_status($int) {

    if ($int == 0) {
        return 'Active';
    } elseif ($int == 1) {
        return 'Inactive';
    }
    die('Error: get_trigger_status() bad parameter');
}


//
// return the string equivalent for the trigger-type $int
//
function get_trigger_type($int) {

    // skip the phase bit
    $int++;
    $int = $int >> 1;

    $types = array();
    while ($int != 0) {
        $slot = $int & 0x03;
        switch ($slot) {
        case '1':
            $types[] = 'insert';
            break;
        case '2':
            $types[] = 'update';
            break;
        case '3':
            $types[] = 'delete';
            break;
        }
        // next type slot
        $int = $int >> 2;
    }

    return $types;
}


//
// return the phase fpr the trigger-type $int
//
function get_trigger_phase($int) {

    // before-triggers have bit one set
    return  $int & 0x01 ? 'before' : 'after';
}


//
// outputs a html-table with a form to define/modify a trigger 
//
// Variables:    $title     headline-string for the table
function get_trigger_definition($title) {
    global $s_triggerdefs, $acc_strings, $s_cust;

    $trigger_source = htmlspecialchars($s_triggerdefs['source']);

    $html = "<table border cellpadding=\"3\" cellspacing=\"0\">\n"
          . "  <tr>\n"
          . '    <th colspan="6" align="left">'.$title."</th>\n"
          . "  </tr>\n"
          . "  <tr>\n"
          . '    <td valign="top"><b>'.$acc_strings['Name']."</b><br>\n"
          . '      <input type="text" size="20" maxlength="31" name="def_trigger_name" value="'.ifsetor($s_triggerdefs['name'])."\">\n"
          . "    </td>\n"
          . "    <td valign=\"top\">\n"
          . '      <b>'.$acc_strings['Table']."</b><br>\n"
          . '      '.get_table_selectlist('def_trigger_table', array('owner'), $s_triggerdefs['table'], TRUE)
          . "    </td>\n"
          . "    <td valign=\"top\">\n"
          . '      <b>'.$acc_strings['Phase']."</b><br>\n"
          . '      '.get_selectlist('def_trigger_phase', array('before', 'after'), $s_triggerdefs['phase'], TRUE)
          . "    </td>\n"
          . "    <td>\n"
          . '      <b>'.$acc_strings['Type']."</b><br>\n"
          . '      '.get_triggertype_selectlist('def_trigger_type', $s_triggerdefs['type'], TRUE)
          . "    </td>\n"
          . "    <td valign=\"top\">\n"
          . '      <b>'.$acc_strings['Position']."</b><br>\n"
          . '      <input type="text" size="2" maxlength="2" name="def_trigger_pos" value="'.ifsetor($s_triggerdefs['pos'])."\">\n"
          . "    </td>\n"
          . "    <td valign=\"top\">\n"
          . '      <b>'.$acc_strings['Status']."</b><br>\n"
          . '      '.get_selectlist('def_trigger_status', array('Active', 'Inactive'), $s_triggerdefs['status'], FALSE)
          . "    </td>\n"
          . "  </tr>\n"
          . "  <tr>\n"
          . "    <td colspan=\"6\">\n"
          . '      <b>'.$acc_strings['Source']."</b><br>\n"
          . '      <textarea name="def_trigger_source" rows="'.$s_cust['textarea']['rows'].'" cols="'.$s_cust['textarea']['cols'].'" wrap="virtual">'.$trigger_source."</textarea>\n"
          . "    </td>\n"
          . "  </tr>\n"
          . "</table>\n";

    return $html;
}


//
// return the html for a triggertypes selectlist
//
function get_triggertype_selectlist($name, $sel=NULL, $empty=FALSE) {

    $types = array('insert', 'update', 'delete');
    $html = '';
    if (SERVER_FAMILY == 'FB'  &&  SERVER_VERSION >= 15) {
        if (!is_array($sel)) {
            $sel = array($sel);
        }
        $html = '<select name="'.$name."[]\" size=\"3\" multiple>\n";
        foreach ($types as $type) {
            $html .= '<option value="'.$type.'"';
            if (in_array($type, $sel)) {
                $html .= ' selected';
            }
            $html .= '>'.$type."</option>\n";
        }
        $html .= "</select>\n";
    }

    else {
        $html = get_selectlist($name.'[]', $types, pos($sel), $empty);
    }

    return $html;
}


//
// save the form vars we got from trigger_definition()
//
function save_triggerdefs() {
    global $s_triggerdefs;

    $s_triggerdefs['name']   = strtoupper(get_request_data('def_trigger_name'));
    $s_triggerdefs['table']  = $_POST['def_trigger_table'];
    $s_triggerdefs['phase']  = $_POST['def_trigger_phase'];
    $s_triggerdefs['type']   = ifsetor($_POST['def_trigger_type']);
    $s_triggerdefs['pos']    = $_POST['def_trigger_pos'];
    $s_triggerdefs['status'] = $_POST['def_trigger_status'];
    $s_triggerdefs['source'] = get_request_data('def_trigger_source');
}


//
// mark all triggers as opened or closed in $s_triggers
//
function toggle_all_triggers($triggers, $status) {

    foreach (array_keys($triggers) as $name) {
        $triggers[$name]['display'] = $status;

        if ($status == 'open'  &&  empty($triggers[$name]['source'])) {
            $triggers[$name]['source'] = get_trigger_source($name);
        }
    }

    return $triggers;
}


//
// check if an active trigger is defined for $table
//
function have_active_trigger($triggers, $table, $phase, $type=NULL) {

    $func = 'return $a["table"]=="' . $table . '"  && $a["status"] == "Active" && $a["phase"]=="' . $phase . '"' 
          . ($type != NULL ? ' && in_array("'.$type.'", $a["type"])' : '')
          . ';';

    $trigger = array_filter($triggers, create_function('$a', $func));

    return !empty($trigger);
}

?>
