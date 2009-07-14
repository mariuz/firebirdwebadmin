<?php
// File           stored_procedures.inc.php / ibWebAdmin
// Purpose        handle the stored procedures ibWebAdmin is using for its own needs
// Author         Lutz Brueckner <irie@gmx.de>
// Copyright      (c) 2000, 2001, 2002, 2003, 2004, 2005 by Lutz Brueckner,
//                published under the terms of the GNU General Public Licence v.2,
//                see file LICENCE for details
// Created        <00/12/22 17:08:38 lb>
//
// $Id: stored_procedures.inc.php,v 1.11 2005/08/27 20:04:44 lbrueckner Exp $


//
// create the stored procedure for the WT_STORED_PROCEDURE mode
//
function sp_limit_create($table, $cols, $order, $dir, $condition, $start, $num) {
    global $s_login, $s_fields, $message, $MESSAGES, $binary_output, $binary_error;

    if (sp_exist(SP_LIMIT_NAME)) {
        sp_remove(SP_LIMIT_NAME);
    }
    else {
        $GLOBALS['message'] .= $MESSAGES['SP_CREATE_INFO'];
    }

    $cstr = ($condition != '') ? 'WHERE '.$condition : '';

    $istr = '';
    $k = 0;
    $sp  = 'CREATE PROCEDURE '.SP_LIMIT_NAME."\n";
    $sp .= 'RETURNS (';
    foreach ($s_fields[$table] as $field) {
        if (in_array($field['name'] , $cols)) {
            $rvar = 'C'.$k;
            $sp .= $rvar . ' ' . get_type_string($field).', ';
            $istr .= ":$rvar, ";
            $k++;
        }
    }
    $istr = substr($istr, 0, -2);
    $sp  = substr($sp, 0, -2);
    $sp .= ")\nAS\n";
    $sp .= "DECLARE VARIABLE cnt INTEGER;\n";
    if ($start < 0) {
        $sp .= "DECLARE VARIABLE nr INTEGER;\n";
    }
    $sp .= "BEGIN\n";
    $sp .= "  cnt  = 0;\n";
    if ($start < 0) {
        $sp .= "  SELECT COUNT(*) FROM $table $cstr INTO :nr;\n";
    }
    $sp .= '  FOR SELECT '.implode(', ', $cols)."\n";
    $sp .= "    FROM  $table $cstr\n";
    if (!empty($order)) {
        $sp .= "    ORDER BY $order $dir\n";
    }
    $sp .= "    INTO  $istr\n";
    $sp .= "  DO\n";
    $sp .= "    BEGIN\n";
    $sp .= "      cnt = cnt + 1;\n";
    if ($start < 0) {
        $sp .= "      IF ((cnt > nr + $start) AND (cnt < nr + $start + $num + 1)) THEN\n";
        $sp .= "        SUSPEND;\n";
    } else {
        $end = $start + $num;
        $sp .= "      IF ((cnt >= $start) AND (cnt < $end)) THEN\n";
        $sp .= "        SUSPEND;\n";
        $sp .= "      if (cnt = $end) THEN\n";
        $sp .= "        EXIT;\n";
    }
    $sp .= "    END\n";
    $sp .= "END !!\n";

    $sp = prepare_for_isql($sp);

    list($binary_output, $binary_error) = isql_execute($sp, $s_login['user'], $s_login['password'], $s_login['database'], $s_login['host']);

    return empty($binary_output)  &&  empty($binary_error);
}


//
// check wether the stored procedure $name exists
//
function sp_exist($name) {
    global $dbhandle;

    $sql = 'SELECT RDB$PROCEDURE_NAME'
           .' FROM RDB$PROCEDURES'
          ." WHERE RDB\$PROCEDURE_NAME='".$name."'";
    $res = ibase_query($dbhandle, $sql) or ib_error();
    if (ibase_fetch_row($res)) {
        ibase_free_result($res);

        return TRUE;
    } else {
        ibase_free_result($res);

        return FALSE;
    }
}


//
// remove the stored procedure $name from database
//
function sp_remove($name) {
    global $dbhandle, $ib_error;
    
    $sql = 'DROP PROCEDURE '.$name;
    $trans =  ibase_trans(TRANS_WRITE, $dbhandle);
    $res = ibase_query($trans, $sql) 
         or die(ibase_errmsg());
    ibase_commit($trans);
}


function prepare_for_isql($cmd) {

    $cmd = "SET TERM !! ;\n".$cmd."SET TERM ; !!\n";

    return $cmd;
}

?>