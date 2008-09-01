<?php
// File           get_tables.inc.php / ibWebAdmin
// Purpose        function that gets the table properties for all tables in the database
// Author         Lutz Brueckner <irie@gmx.de>
// Copyright      (c) 2000, 2001, 2002, 2003, 2004, 2005 by Lutz Brueckner,
//                published under the terms of the GNU General Public Licence v.2,
//                see file LICENCE for details
// Created        <00/10/06 08:34:43 lb>
//
// $Id: get_tables.inc.php,v 1.33 2005/09/10 18:26:21 lbrueckner Exp $


//
// set the session variables $s_tables[], $s_fields[]
// for the database $dbhandle
//
function get_tables() {
    global $dbhandle, $ib_error, $s_tables, $s_fields, $s_foreigns, $s_primaries, $s_uniques, $s_login;
    global $s_charsets, $s_tables_counts, $s_views_counts, $s_tables_def, $s_tables_comp;

    $previous = $s_tables;
    $s_tables = array();
    $s_fields = array();

    // get the tablenames, owner and view flag
    $sql  = 'SELECT RDB$RELATION_NAME AS RNAME,'
                 .' RDB$VIEW_BLR AS VBLR,'
                 .' RDB$OWNER_NAME AS OWNER'
            .' FROM RDB$RELATIONS'
           .' WHERE RDB$SYSTEM_FLAG=0'
        .' ORDER BY RDB$RELATION_NAME';
    $res = @ibase_query($dbhandle, $sql) or ib_error(__FILE__, __LINE__, $sql);
    if (!is_resource($res)) {
       return FALSE;
    }

    // initialize $s_tables[]
    while ($row = ibase_fetch_object($res)) {

        $tablename = trim($row->RNAME);

        $s_tables[$tablename]['status']     = (isset($previous[$tablename])) ? $previous[$tablename]['status'] : 'close';
        $s_tables[$tablename]['is_view']    = (isset($row->VBLR)  &&  $row->VBLR !== NULL) ? TRUE : FALSE;
        $s_tables[$tablename]['owner']      = trim($row->OWNER);
        $s_tables[$tablename]['privileges'] = array();
    }
    ibase_free_result($res);
    unset($previous);

    // get privileges on tables for the current user and for the role used at login
    $sql = 'SELECT R.RDB$RELATION_NAME AS RNAME,'
                 .' P1.RDB$PRIVILEGE AS PRIV'
            .' FROM RDB$RELATIONS R'
           .' INNER JOIN RDB$USER_PRIVILEGES P1'
              .' ON R.RDB$RELATION_NAME=P1.RDB$RELATION_NAME'
           .' WHERE R.RDB$SYSTEM_FLAG=0'
             ." AND (P1.RDB\$USER='".$s_login['user']."' OR P1.RDB\$USER='PUBLIC')";
    if (!empty($s_login['role'])) {
        $sql .= ' UNION'
              .' SELECT R.RDB$RELATION_NAME AS RNAME,'
                     .' P2.RDB$PRIVILEGE AS PRIV'
                .' FROM RDB$USER_PRIVILEGES P1'
               .' INNER JOIN RDB$USER_PRIVILEGES P2 ON P1.RDB$RELATION_NAME=P2.RDB$USER'
               .' INNER JOIN RDB$RELATIONS R ON R.RDB$RELATION_NAME=P2.RDB$RELATION_NAME'
               ." WHERE P1.RDB\$PRIVILEGE='M'"
                 .' AND R.RDB$SYSTEM_FLAG=0'
                 ." AND P1.RDB\$RELATION_NAME='".$s_login['role']."'"
                 ." AND (P1.RDB\$USER='".$s_login['user']."' OR P1.RDB\$USER='PUBLIC')";
    }
    $res = @ibase_query($dbhandle, $sql) or ib_error(__FILE__, __LINE__, $sql);

    while ($row = ibase_fetch_object($res)) {
        $s_tables[trim($row->RNAME)]['privileges'][] =  trim($row->PRIV);
    }
    ibase_free_result($res);


    // find the check, not null, unique, pk and fk and  constraints
    $sql ='SELECT RC.RDB$RELATION_NAME TNAME,'
               .' RC.RDB$CONSTRAINT_TYPE RTYPE,'
               .' RC.RDB$CONSTRAINT_NAME CNAME,'
               .' RC.RDB$INDEX_NAME INAME,'
               .' CC.RDB$TRIGGER_NAME TRIGNAME,'
               .' SE.RDB$FIELD_NAME SENAME,'
               .' SE.RDB$FIELD_POSITION POS,'
               .' DP.RDB$FIELD_NAME DPNAME'
          .' FROM RDB$RELATION_CONSTRAINTS RC'
     .' LEFT JOIN RDB$CHECK_CONSTRAINTS CC'
            .' ON RC.RDB$CONSTRAINT_NAME=CC.RDB$CONSTRAINT_NAME'
           ." AND RC.RDB\$CONSTRAINT_TYPE='CHECK'"
     .' LEFT JOIN RDB$INDEX_SEGMENTS SE'
            .' ON RC.RDB$INDEX_NAME=SE.RDB$INDEX_NAME'
     .' LEFT JOIN RDB$DEPENDENCIES DP'
            .' ON CC.RDB$TRIGGER_NAME=DP.RDB$DEPENDENT_NAME'
         .' ORDER BY RC.RDB$RELATION_NAME';
    $res = @ibase_query($dbhandle, $sql) or ib_error(__FILE__, __LINE__, $sql);

    // reset the index infos
    $s_foreigns  = array();
    $s_primaries = array();
    $s_uniques   = array();

    $constraints = array();
    while ($row = ibase_fetch_object($res)) {
        $cname = trim($row->CNAME);
        switch (trim($row->RTYPE)) {
            case 'CHECK':
                $constraints[trim($row->TNAME)][trim($row->DPNAME)]['check'] = $cname;
                break;
            case 'UNIQUE':
                $constraints[trim($row->TNAME)][trim($row->SENAME)]['unique'] = $cname;
                $s_uniques[$cname]['index'] = trim($row->INAME);
                $s_uniques[$cname]['cols']  = isset($s_uniques[$cname]['cols']) ? $s_uniques[$cname]['cols']++ : 1;
                break;
            case 'FOREIGN KEY':
                $constraints[trim($row->TNAME)][trim($row->SENAME)]['foreign'] = $cname;
                $s_foreigns[$cname]['index'] = trim($row->INAME);
                $s_foreigns[$cname]['cols']  = isset($s_foreigns[$cname]['cols']) ? $s_foreigns[$cname]['cols']++ : 1;
                break;
            case 'PRIMARY KEY':
                $constraints[trim($row->TNAME)][trim($row->SENAME)]['primary'] = $cname;
                $s_primaries[$cname]['index'] = trim($row->INAME);
                $s_primaries[$cname]['cols']  = isset($s_primaries[$cname]['cols']) ? $s_primaries[$cname]['cols']++ : 1;
                break;
        }
    }
    ibase_free_result($res);
    
//     debug_var($sql);
//     debug_var($constraints);
//     debug_var($s_foreigns);
//     debug_var($s_primaries);

    // find the field properties for all non-system tables
    $sql  = 'SELECT DISTINCT R.RDB$FIELD_NAME AS FNAME,'
                 .' R.RDB$NULL_FLAG AS NFLAG,'
                 .' R.RDB$DEFAULT_SOURCE AS DSOURCE,'
                 .' R.RDB$FIELD_POSITION,'
                 .' R.RDB$RELATION_NAME AS TNAME,'
                 .' R.RDB$COLLATION_ID AS COLLID,'
                 .' F.RDB$FIELD_NAME AS DNAME,'
                 .' F.RDB$FIELD_TYPE AS FTYPE,'
                 .' F.RDB$FIELD_SUB_TYPE AS STYPE,'
                 .' F.RDB$FIELD_LENGTH AS FLEN,'
                 .' F.RDB$COMPUTED_SOURCE AS CSOURCE,'
                 .' F.RDB$FIELD_PRECISION AS FPREC,'
                 .' F.RDB$FIELD_SCALE AS FSCALE,'
                 .' F.RDB$SEGMENT_LENGTH AS SEGLEN,'
                 .' F.RDB$CHARACTER_SET_ID AS CHARID,'
                 .' D.RDB$LOWER_BOUND AS LBOUND,'
                 .' D.RDB$UPPER_BOUND AS UBOUND'
            .' FROM RDB$RELATION_FIELDS R '
            .' JOIN RDB$FIELDS F ON R.RDB$FIELD_SOURCE=F.RDB$FIELD_NAME'
       .' LEFT JOIN RDB$FIELD_DIMENSIONS D ON R.RDB$FIELD_SOURCE=D.RDB$FIELD_NAME'
           .' WHERE F.RDB$SYSTEM_FLAG=0'
       . ' ORDER BY R.RDB$FIELD_POSITION';
    $res = @ibase_query($dbhandle, $sql) or ib_error(__FILE__, __LINE__, $sql);

    //initialize $s_fields[]
    $idx = 0;
    while ($row = ibase_fetch_object($res)) {
        $tname = trim($row->TNAME);
        $field = $s_fields[$tname][$idx]['name']  = trim($row->FNAME);
        if (strpos($row->DNAME, 'RDB$') !== 0){
            $s_fields[$tname][$idx]['domain'] = 'Yes';
            $s_fields[$tname][$idx]['type'] = trim($row->DNAME);
        } else {
            $s_fields[$tname][$idx]['stype'] = (isset($row->STYPE)) ? $row->STYPE : NULL; 
            $s_fields[$tname][$idx]['type']  = get_datatype($row->FTYPE, $s_fields[$tname][$idx]['stype']);
        }
	if ($s_fields[$tname][$idx]['type'] == 'VARCHAR' || $s_fields[$tname][$idx]['type'] == 'CHARACTER') {
	    $s_fields[$tname][$idx]['size']    = $row->FLEN;
	}

        // field is defined as NOT NULL
        if (!empty($row->NFLAG)) {
            $s_fields[$tname][$idx]['notnull'] = 'Yes';
        }

        // this field is computed
	if (isset($row->CSOURCE)) {
            $s_fields[$tname][$idx]['comp']   = 'Yes';
            $s_fields[$tname][$idx]['csource'] = FALSE;
        }

        // this field has a default value
	if (isset($row->DSOURCE)) {
            $s_fields[$tname][$idx]['default']= 'Yes';
            $s_fields[$tname][$idx]['dsource'] = FALSE;
        }

    	if (($s_fields[$tname][$idx]['type'] == 'DECIMAL')  or  ($s_fields[$tname][$idx]['type'] == 'NUMERIC')) {
	    $s_fields[$tname][$idx]['prec']   = $row->FPREC;
	    $s_fields[$tname][$idx]['scale']  = -($row->FSCALE);
	}

	if ($s_fields[$tname][$idx]['type'] == 'BLOB') {
            $s_fields[$tname][$idx]['segsize'] = $row->SEGLEN;
        }

	$s_fields[$tname][$idx]['charset'] = isset($row->CHARID) ? $s_charsets[$row->CHARID]['name'] : NULL;
        $s_fields[$tname][$idx]['collate'] = (isset($row->COLLID)  &&  $row->COLLID != 0  &&  isset($s_charsets[$row->CHARID]['collations'][$row->COLLID]))
                                 ? $s_charsets[$row->CHARID]['collations'][$row->COLLID] 
                                 : NULL;

        // optional array dimensions
        if (isset($row->LBOUND)) {
            $s_fields[$tname][$idx]['lower_bound'] = $row->LBOUND;
            $s_fields[$tname][$idx]['upper_bound'] = $row->UBOUND;
        }

        // column constraints
        foreach (array('check', 'unique', 'foreign', 'primary') as $ctype) {
            if (isset($constraints[$tname][$field][$ctype])) {
                $s_fields[$tname][$idx][$ctype] = $constraints[$tname][$field][$ctype];
            }
        }
        $idx++;
    }
//     debug_var($s_fields);

    $quote = identifier_quote($s_login['dialect']);
    foreach ($s_tables as $name => $properties) {

        if ($s_tables_def == TRUE) {
            $s_fields = get_table_defaults_sources($name, $s_fields);
        }

        if ($s_tables_comp == TRUE) {
            $s_fields = get_table_computed_sources($name, $s_fields);
        }

        if (!in_array('S', $properties['privileges'])) {
            continue;
        }

        if (($properties['is_view'] == FALSE  &&  $s_tables_counts == TRUE)
        ||  ($properties['is_view'] == TRUE   &&  $s_views_counts  == TRUE)) {

            $sql = 'SELECT COUNT(*) AS CNT FROM ' . $quote . $name . $quote;
            $res = ibase_query($dbhandle, $sql)
                or $ib_error .= ibase_errmsg()."<br>\n";
            if (is_resource($res)) {
                $row = ibase_fetch_object($res);
                $s_tables[$name]['count'] = $row->CNT;
                ibase_free_result($res);
            }
        }
    }

    return TRUE;
}

?>
