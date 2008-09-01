<?php
// File           procedures.inc.php / ibWebAdmin
// Purpose        functions working with stored procedures, included from accessories.php
// Author         Lutz Brueckner <irie@gmx.de>
// Copyright      (c) 2000, 2001,2002, 2003, 2004, 2005 by Lutz Brueckner,
//                published under the terms of the GNU General Public Licence v.2,
//                see file LICENCE for details
// Created        <02/10/16 15:53:01 lb>
//
// $Id: procedures.inc.php,v 1.17 2005/08/27 21:07:40 lbrueckner Exp $


//
// create a stored procedure from the values in the procedure form
//
function create_procedure($proceduredefs) {
    global $s_login, $isql, $binary_output, $binary_error;

    if (empty($proceduredefs['source'])) {
        return FALSE;
    }

    $isql  = "SET TERM !! ;\n"
            . $proceduredefs['source']."\n"
            ."SET TERM ; !!\n";
    if (DEBUG) add_debug('isql', __FILE__, __LINE__);

    // this must be done by isql because 'create procedure' is not supported from within php
    list($binary_output, $binary_error) = isql_execute($isql, $s_login['user'], $s_login['password'], $s_login['database'], $s_login['host']);

    return ($binary_error != ''  ||  count($binary_output) > 0) ? FALSE : TRUE;
}


//
// drop the named stored procedure
//
function drop_procedure($name) {
    global $dbhandle, $ib_error, $s_procedures;

    $lsql = 'DROP PROCEDURE '.$name;
    if (!@ibase_query($dbhandle, $lsql)) {
        $ib_error = ibase_errmsg();
    }
    else {
         unset($s_procedures[$name]);
    }
}


//
// return an array with the properties of the defined procedures 
//
function get_procedures($oldprocedures) {
    global $dbhandle;

    $sql = 'SELECT P.RDB$PROCEDURE_NAME PNAME,'
                .' RDB$OWNER_NAME OWNER'
           .' FROM RDB$PROCEDURES P'
         .'  WHERE P.RDB$SYSTEM_FLAG IS NULL'
             .' OR P.RDB$SYSTEM_FLAG=0'
          .' ORDER BY RDB$PROCEDURE_NAME';
    $res = ibase_query($dbhandle, $sql) or ib_error(__FILE__, __LINE__, $sql);

    $procs = array();
    while ($obj = ibase_fetch_object($res)) {
        $pname = trim($obj->PNAME);

        $in = $out = array();
        $status = 'close';
        $source = '';
        if (isset($oldprocedures[$pname])  &&  $oldprocedures[$pname]['status'] == 'open') {
            $source = get_procedure_source($pname);
            list($in, $out) = get_procedure_parameters($pname);
            $status = 'open';
        }

        $procs[trim($obj->PNAME)] = array('name'  => trim($obj->PNAME),
                                          'owner' => trim($obj->OWNER),
                                          'source'=> $source,
                                          'in'    => $in,
                                          'out'   => $out,
                                          'status'=> $status);
    }
    ibase_free_result($res);

    return $procs;
}


//
// return the sourcecode of the stored procedure $name
//
function get_procedure_source($name) {
    global $dbhandle;

    $psource = '';
    $sql = 'SELECT P.RDB$PROCEDURE_SOURCE PSOURCE'
           .' FROM RDB$PROCEDURES P'
          ." WHERE P.RDB\$PROCEDURE_NAME='".$name."'";
    $res = ibase_query($dbhandle, $sql) or ib_error(__FILE__, __LINE__, $sql);
    $obj = ibase_fetch_object($res);

    if (is_object($obj)) {
        $bid = ibase_blob_open($obj->PSOURCE);
        $arr = ibase_blob_info($obj->PSOURCE);
        // $arr[2] holds the blob length
        $psource = trim(ibase_blob_get($bid, $arr[0]));
        ibase_blob_close($bid);
    }
    ibase_free_result($res);

    return $psource;
}


//
// return the input- and result-parameters of the stored procedure $name
//
// Result: array containing two arrays with the datatype properties of
//               the stored procedures parameters and return values
function get_procedure_parameters($name) {
    global $dbhandle, $s_charsets;

    $sql = 'SELECT P.RDB$PARAMETER_NAME PNAME,'
                .' P.RDB$PARAMETER_TYPE PTYPE,'
                .' F.RDB$FIELD_NAME AS DNAME,'
                .' F.RDB$FIELD_TYPE AS FTYPE,'
                .' F.RDB$FIELD_SUB_TYPE AS STYPE,'
                .' F.RDB$FIELD_LENGTH AS FLEN,'
                .' F.RDB$FIELD_PRECISION AS FPREC,'
                .' F.RDB$FIELD_SCALE AS FSCALE,'
                .' F.RDB$SEGMENT_LENGTH AS SEGLEN,'
                .' F.RDB$CHARACTER_SET_ID AS CHARID,'
                .' F.RDB$COLLATION_ID AS COLLID'
           .' FROM RDB$PROCEDURE_PARAMETERS P'
          .' INNER JOIN RDB$FIELDS F ON P.RDB$FIELD_SOURCE=F.RDB$FIELD_NAME'
          ." WHERE P.RDB\$PROCEDURE_NAME='".$name."'";

    $res = ibase_query($dbhandle, $sql) or ib_error(__FILE__, __LINE__, $sql);
    $in = $out = array();
    while  ($obj = ibase_fetch_object($res)) {
        $ptype = ($obj->PTYPE == 0) ? 'in' : 'out';

        $stype = (isset($obj->STYPE)) ? $obj->STYPE : NULL;
        $type  = get_datatype($obj->FTYPE, $stype);

        if (in_array($type, array('DECIMAL', 'NUMERIC'))) {
            $prec  = $obj->FPREC;
            $scale = -$obj->FSCALE;
            $stype = NULL;
        }
        else {
            $prec = $scale = NULL;
        }

        ${$ptype}[] = array('name'    => trim($obj->PNAME),
                          'type'    => $type,
                          'stype'   => $stype,
                          'size'    => (in_array($type, array('VARCHAR', 'CHARACTER'))) ? $obj->FLEN : NULL,
                          'charset' => (isset($obj->CHARID)) ? $s_charsets[$obj->CHARID]['name'] : NULL,
                          'collate' => (isset($obj->COLLID)  &&  $obj->COLLID != 0) 
                                            ? $s_charsets[$obj->CHARID]['collations'][$obj->COLLID] : NULL,
                          'prec'    => $prec,
                          'scale'   => $scale,
                          'segsize' => ($type == 'BLOB') ? $obj->SEGLEN : NULL);
    }

    return array($in, $out);
}


//
// find the name of a procedure in its source code
// 
function get_procedure_name($source) {

    $chunks = preg_split("/[\s]+/", $source, 4);

    return $chunks[2];
}


//
// returns the html for a table displaying the stored procedures parameters or result values
//
// Paremters:  array typedefs   one of the arrays returned by get_procedure_parameters()
//
function procedure_parameters($typedefs) {
    global $acc_strings;

    $str = "<table border cellpadding=\"0\" cellspacing=\"0\">\n"
          .'  <tr align="left">'
          .'    <th class="detail">'.$acc_strings['Name']."</th>\n"
          .'    <th class="detail">'.$acc_strings['Type']."</th>\n"
          .'    <th class="detail">'.$acc_strings['Size']."</th>\n"
          .'    <th class="detail">'.$acc_strings['Charset']."</th>\n"
          .'    <th class="detail">'.$acc_strings['Collate']."</th>\n"
          .'    <th class="detail">'.$acc_strings['PrecShort']."</th>\n"
          .'    <th class="detail">'.$acc_strings['Scale']."</th>\n"
          .'    <th class="detail">'.$acc_strings['Subtype']."</th>\n"
          .'    <th class="detail">'.$acc_strings['SegSiShort']."</th>\n"
         ."  </tr>\n";

    foreach ($typedefs as $def) {
        $str .=  "  <tr>\n"
                .'    <td class="detail">' . $def['name'] ."</td>\n"
                .'    <td class="detail">' . $def['type'] ."</td>\n"
                .'    <td class="detail">' . ((isset($def['size'])) ? $def['size'] : '&nbsp;') ."</td>\n"
                .'    <td class="detail">' . ((isset($def['charset'])) ? $def['charset'] : '&nbsp;') ."</td>\n"
                .'    <td class="detail">' . ((isset($def['collate'])) ? $def['collate'] : '&nbsp;') ."</td>\n"
                .'    <td class="detail">' . ((isset($def['prec'])) ? $def['prec'] : '&nbsp;') ."</td>\n"
                .'    <td class="detail">' . ((isset($def['scale'])) ? $def['scale'] : '&nbsp;') ."</td>\n"
                .'    <td class="detail">' . ((isset($def['stype'])) ? $def['stype'] : '&nbsp;') ."</td>\n"
                .'    <td class="detail">' . ((isset($def['segsize'])) ? $def['segsize'] : '&nbsp;') ."</td>\n"
                ."  </tr>\n";
    }

    $str .= "</table>\n";

    return $str;
}


//
// output a html-table with a form to define/modify a stored procedure 
//
// Parameters:  $indexname  name of the index to modify
//              $title      headline-string for the table
//
function get_procedure_definition($title, $source) {
    global $acc_strings, $s_cust;

    $rows = $s_cust['textarea']['rows'];
    $cols = $s_cust['textarea']['cols'];

    $source = htmlentities($source);

    $html = <<<EOT
<table border cellpadding="3" cellspacing="0">
  <tr>
    <th align="left">$title</th>
  </tr>
  <tr>
    <td>
        <b>${acc_strings['Source']}</b><br>
      <textarea name="def_proc_source" rows="$rows" cols="$cols" wrap="virtual">$source</textarea>
    </td>
</table>

EOT;

    return $html;
}


//
// deliver the html for an opened view on the views panel
//
function get_opened_procedure($name, $procedure, $url) {
    global $dbhandle, $tb_strings, $acc_strings, $ptitle_strings;

    $in = $out = '';
    $in_start = $out_start = $src_start = '';
    $rowspan = 1;
    if (count($procedure['in']) > 0) {
        $in = procedure_parameters($procedure['in']);
        $out_start = $src_start = "<tr>\n";
        $rowspan++;
    }
    if (count($procedure['out']) > 0) {
        $out = procedure_parameters($procedure['out']);
        $src_start = "<tr>\n";
        $rowspan++;
    }

    $red_triangle = get_icon_path(DATAPATH, ICON_SIZE) . 'red_triangle.png';

    $html = <<<EOT
        <nobr>
          <a href="$url" class="dtitle"><img src="$red_triangle" alt="${ptitle_strings['Close']}" title="${ptitle_strings['Close']}" border="0" hspace="7">$name</a>
        </nobr>
        <table cellpadding="0" cellspacing="0" border="0">
          <tr>
            <td width="26" rowspan="$rowspan">
            </td>

EOT;

    if (!empty($in)) {
        $html .=<<<EOT
          <td>
            <table border cellpadding="3" cellspacing="0">
              <tr>
                <th align="left">${acc_strings['Param']}</th>
              </tr>
              <tr>
	        <td valign="top">$in</td>
              </tr>
            </table>
          </td>
       </tr>

EOT;

    }

    if (!empty($out)) {
        $html .=<<<EOT
        $out_start
          <td>
            <table border cellpadding="3" cellspacing="0">
              <tr>
                <th align="left">${acc_strings['Return']}</th>
              </tr>
              <tr>
	        <td valign="top">$out</td>
              </tr>
            </table>
          </td>
        </tr>

EOT;

    }

    $html .= <<<EOT
        $src_start
          <td>
            <table border cellpadding="3" cellspacing="0">
              <tr>
                <th align="left">${acc_strings['Source']}</th>
              </tr>
              <tr>
	        <td valign="top"><pre>${procedure['source']}</pre></td>
              </tr>
            </table>
          </td>
        </tr>
        </table>

EOT;

    return $html;
}


//
// mark all procedures as opened or closed in $s_procedures
//
function toggle_all_procedures($procedures, $status) {

    foreach (array_keys($procedures) as $name) {
        $procedures[$name]['status'] = $status;

        if ($status == 'open'  &&  empty($procedures[$name]['source'])) {
            $procedures[$name]['source'] = get_procedure_source($name);
            list($in, $out) = get_procedure_parameters($name);
            $procedures[$name]['in']  = $in;
            $procedures[$name]['out'] = $out;
        }
    }

    return $procedures;
}


//
// build the source code for modifying the sp described in $procedure
//
function procedure_modify_source($procedure) {

    $source = 'ALTER PROCEDURE '.$procedure['name'].procedure_parameter_list($procedure['in'])."\n"
             .procedure_return_list($procedure['out'])
             ."AS\n"
             .$procedure['source'].' !!';
    
    return $source;
}

function procedure_parameter_list($in) {

    if (count($in) == 0) {
        return '';
    }

    $list = ' (';
    foreach ($in as $parameter) {
        $list .= $parameter['name'].' '.build_datatype($parameter).', ';
    }
    $list = substr($list, 0, -2).')';

    return $list;
}

function procedure_return_list($out) {

    if (count($out) == 0) {
        return '';
    }

    $list = 'RETURNS (';
    foreach ($out as $parameter) {
        $list .= $parameter['name'].' '.build_datatype($parameter).', ';
    }
    $list = substr($list, 0, -2).")\n";

    return $list;
}

?>
