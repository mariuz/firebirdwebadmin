<?php
// File           inc/functions.inc.php / FirebirdWebAdmin
// Purpose        collection of little helpers etc.
// Author         Lutz Brueckner <irie@gmx.de>
// Copyright      (c) 2000-2006 by Lutz Brueckner,
//                published under the terms of the GNU General Public Licence v.2,
//                see file LICENCE for details
// Created        <00/09/09 19:09:16 lb>       
//
// $Id: functions.inc.php,v 1.86 2006/07/08 17:26:59 lbrueckner Exp $


//
// supported blob types
//
$blob_types = array('png',
                    'jpg',
                    'gif',
                    'text',
                    'html',
                    'hex');

//
// the names of the session variables holding the panel informations
//
function panel_array_names() {

    $panel_arrays = array('s_database_panels',
                          's_tables_panels',
                          's_accessories_panels',
                          's_sql_panels',
                          's_data_panels',
                          's_users_panels',
                          's_admin_panels');
    return $panel_arrays;
}


//
// returns a string for the usage between the html <title> tags
//
function build_title($str, $showdb=TRUE) {
    global $s_connected, $s_login;
    
    $title = 'FirebirdWebAdmin '.VERSION.' *** '.$str;
    if ($s_connected == TRUE  &&  $showdb)
        $title .= ': '.$s_login['database'];

    return $title;
}


//
// return the path to th navigation icons
//
function get_icon_path($datapath, $iconsize) {

    return $datapath . (BG_TRANSPARENT == TRUE ? 'transparent/' : 'opaque/') . strtolower($iconsize) . '/';
}


//
//  set an new $title for the panel $name
//
function set_panel_title($name, $title) {

    foreach (panel_array_names() as $key => $parray) {
        global $$parray;
        foreach ($$parray as $idx => $panel) {
            if ($panel[0] == $name) {
                ${$parray}[$idx][1] = $title;
            }
        }
    }
}


//
// returns a string containing strlen($pw) stars;
// this strings are used as value in the form password fields
// to notify that there was already a password entered
//
function password_stars($pw) {

    $length = strlen($pw);
    if ($length > 0)
        return str_repeat('*', $length);
    else
        return '';
}


//
// determine the InterBase datatype from the given $type/$subtype
//
function get_datatype($type, $subtype) {

    $datatypes = get_datatypes(SERVER_FAMILY, SERVER_VERSION);

    if ($type == 16  &&  empty($subtype)  &&  !isset($datatypes[16])) {
        return 'INT64';
    }

    if ($subtype == 0 or $subtype == NULL or $type == 261 or $type == 14) {
        return $datatypes[$type];
    }

    elseif ($subtype == 1) {
        return 'NUMERIC';
    }

    elseif ($subtype == 2) {
        return 'DECIMAL';
    }

    else {
        return 'unknown';
    }
}


//
// return a string representing the dataype for the field described in the $field array
//
function get_type_string($field) {

    if (in_array($field['type'], array_keys($GLOBALS['s_domains']))) {
        $field = $GLOBALS['s_domains'][$field['type']];
    }

    $str = $field['type'];

    switch($field['type']) {
    case 'CHARACTER' :
    case 'VARCHAR'   :
        if ($field['size'] > 0) {
	    $str .= '('.$field['size'].')';
        }
        break;
    case 'DECIMAL':
    case 'NUMERIC':
        if ($field['prec'] > 0) {
	    $str .= '('.$field['prec'];
	    if ($field['scale'] > 0)
	        $str .= ','.$field['scale'];
	    $str .= ')';
        }
        break;
    }
 
    return $str;
}


//
// return an array with the column names of $table
//
function table_columns($table) {
    global $s_fields;

    $columns = array();
    foreach ($s_fields[$table] as $field) {
        $columns[] = $field['name'];
    }

    return $columns;
}
        

//
// return TRUE if the table $tablename contains a blob column
//
function have_blob($tablename) {
    global $s_fields;
    
    foreach($s_fields[$tablename] as $field) {
        if ($field['type'] == 'BLOB') {

            return TRUE;
        }
    }

    return FALSE;
}


//
// determine if $field is the field definition for a numeric field
//
function is_number($field) {

    return is_numeric($field['type']);
}

function is_number_type($type) {

    $numerics = array('SMALLINT', 'INTEGER', 'BIGINT', 'FLOAT', 'DOUBLE', 'DECIMAL', 'NUMERIC');

    return in_array($type, $numerics);
}

//
// create a temporary file which contains '$sql_string'
// return the filename
//
function build_sql_file($sql){

    $sql = str_replace("\r\n", "\n", $sql);
    $sql .= "\n";
    $tmp_name = TMPPATH.uniqid('').'.sql';

    if ($fp = fopen ($tmp_name, 'a')) {
        fwrite($fp, $sql);
        fclose($fp); 
    }

    return $tmp_name;
}


//
// execute some sql via the isql command line tool
//
function isql_execute($sql, $user=NULL, $pw=NULL, $db=NULL, $host=NULL) {

    $sql_file = build_sql_file($sql);
    $u_str = ($user <> NULL) ? '-u ' . ibwa_escapeshellarg($user) : '';
    $p_str = ($pw <> NULL)   ? '-p ' . ibwa_escapeshellarg($pw)   : '';
    $d_str = ($db <> NULL)   ? $db : '';
    $d_str = ($host <> NULL) ? ibwa_escapeshellarg($host.':'.$d_str) : ibwa_escapeshellarg($d_str);

    $parameters =  sprintf(' -m %s %s -i %s %s', $u_str, $p_str, ibwa_escapeshellarg($sql_file), $d_str);

    $result = exec_command('isql-fb', $parameters);

    if (DEBUG_FILES !== TRUE) {
        unlink($sql_file);
    }


    return $result;
}


//
// get the database metadata via the isql command line tool
//
function isql_get_metadata($user, $pw, $db, $host) {

    $db_str =   (!empty($host)) ? $host . ':' . $db : $db;
    $user_str = (getenv('ISC_USER'))     ? '' : ' -u ' . ibwa_escapeshellarg($user);
    $pw_str   = (getenv('ISC_PASSWORD')) ? '' : ' -p ' . ibwa_escapeshellarg($pw);

    $parameters = ' -m -x' . $user_str . $pw_str . ' '. ibwa_escapeshellarg($db_str);

    return exec_command('isql-fb', $parameters);
}


function exec_command($cmd, $parameters, $stderr=FALSE) {

    $is_windows = (stristr(php_uname(), 'wind') == TRUE) ? TRUE : FALSE;

    if (($is_windows  &&  !is_file(BINPATH.$cmd.'.exe'))  &&  !is_file(BINPATH.$cmd)) {

        return array(array(), sprintf($GLOBALS['ERRORS']['BAD_BINPATH'], BINPATH.$cmd));
    }

    $fcmd = $cmd . $parameters . (($stderr  &&  !$is_windows) ? ' 2>&1' : '');


    if (DEBUG_COMMANDS === TRUE) {
        $GLOBALS['externcmd'] .= ($GLOBALS['externcmd'] != '') ? '<br>'.BINPATH.$fcmd : BINPATH.$fcmd;
    }

    $err = '';
    $out = array();
    $olddir = getcwd();
    if (!chdir(BINPATH)) {
        $err = $GLOBALS['WARNINGS']['CAN_NOT_ACCESS_DIR'];
    }
    else {
        $path_prefix = ($is_windows) ? '' : './';

        exec($path_prefix.$fcmd, $out, $retval);

        if ($retval != 0  && $retval != 1) {
            $err = BINPATH.$cmd.' returned error code '.$retval;
        }
        chdir($olddir);
    }

    return array($out, $err);
}


//
// return the index for the panel $name in the $panelarray[], 
// which is one of the $s_xyz_panels[]
//
function get_panel_index($panelarray, $name) {

    foreach($panelarray as $index => $panel) {
        if ($panel[0] == $name) {
            return $index;
        }
    }
    return FALSE;
}


function remove_edit_panels() {
    global $s_edit_where, $s_edit_values, $s_edit_idx;
    global $s_data_panels, $s_sql_panels;

    for ($i=1; $i<=$s_edit_idx; $i++) {
        $idx = get_panel_index($s_data_panels, 'dt_edit'.$i);
        if ($idx !== FALSE) {
            array_splice($s_data_panels, $idx, 1); 
        }
        $idx = get_panel_index($s_sql_panels, 'dt_edit'.$i);
        if ($idx !== FALSE) {
            array_splice($s_sql_panels, $idx, 1); 
        }
    }
    $s_edit_where = array();
    $s_edit_values = array();
    $s_edit_idx = 0;
}


// determine the name of the panel_array by inquiring the scripts name
function get_panel_array($script) {

    if (strncmp(basename($script), 'data.php', strlen('data.php')) == 0) {
        $name = 's_data_panels';
    }
    elseif (strncmp(basename($script), 'sql.php', strlen('sql.php')) == 0) {
        $name = 's_sql_panels';
    }

    return $name;
}


//
// save the form vars we got from coldef_definition() in $s_coldefs[$idx]
//
function save_coldef($idx) {
    global $s_coldefs;

    $s_coldefs[$idx] = save_datatype($idx);
    $s_coldefs[$idx]['comp']     = isset($_POST['cd_def_comp'.$idx])       ? $_POST['cd_def_comp'.$idx]      : NULL;
    $s_coldefs[$idx]['domain']   = !empty($_POST['cd_def_domain'.$idx])    ? $_POST['cd_def_domain'.$idx]    : NULL;
    $s_coldefs[$idx]['default']  = get_request_data('cd_def_default'.$idx);
    $s_coldefs[$idx]['check']    = get_request_data('cd_def_check'.$idx);
    $s_coldefs[$idx]['notnull']  = !empty($_POST['cd_def_notnull'.$idx])   ? $_POST['cd_def_notnull'.$idx]   : NULL;
    $s_coldefs[$idx]['unique']   = !empty($_POST['cd_def_unique'.$idx])    ? $_POST['cd_def_unique'.$idx]    : NULL;
    $s_coldefs[$idx]['primary']  = !empty($_POST['cd_def_primary'.$idx])   ? $_POST['cd_def_primary'.$idx]   : NULL;
    $s_coldefs[$idx]['fk_name']  = !empty($_POST['cd_def_fk_name_'.$idx])  ? $_POST['cd_def_fk_name_'.$idx]  : NULL;
    $s_coldefs[$idx]['fk_table'] = !empty($_POST['cd_def_fk_table_'.$idx]) ? $_POST['cd_def_fk_table_'.$idx] : NULL;
    $s_coldefs[$idx]['fk_column']= !empty($_POST['cd_def_fk_col_'.$idx])   ? $_POST['cd_def_fk_col_'.$idx]   : NULL;
    $s_coldefs[$idx]['on_update']= !empty($_POST['cd_def_ou_'.$idx])       ? $_POST['cd_def_ou_'.$idx]       : NULL;
    $s_coldefs[$idx]['on_delete']= !empty($_POST['cd_def_od_'.$idx])       ? $_POST['cd_def_od_'.$idx]       : NULL;

    if ($idx == 'mod') {
        $s_coldefs['mod']['fk_del'] = isset($_POST['cd_def_fk_del_mod']) ? TRUE : FALSE;
        $s_coldefs['mod']['pk_del'] = isset($_POST['cd_def_pk_del_mod']) ? TRUE : FALSE;
        $s_coldefs['mod']['uq_del'] = isset($_POST['cd_def_uq_del_mod']) ? TRUE : FALSE;
    }
}


//
// save the form vars we got from coldef_definition() in $s_coldefs[$idx]
//
function save_datatype($idx) {

    $coldef['name']    = strtoupper(get_request_data('cd_def_name'.$idx));
    $coldef['type']    = $_POST['cd_def_type'.$idx];
    $coldef['size']    = trim($_POST['cd_def_size'.$idx]);
    $coldef['charset'] = $_POST['cd_def_charset'.$idx];
    $coldef['collate'] = (isset($_POST['cd_def_collate'.$idx])) ? $_POST['cd_def_collate'.$idx] : NULL;
    $coldef['prec']    = trim($_POST['cd_def_prec'.$idx]);
    $coldef['scale']   = trim($_POST['cd_def_scale'.$idx]);
    $coldef['stype']   = trim($_POST['cd_def_stype'.$idx]);
    $coldef['segsize'] = trim($_POST['cd_def_segsize'.$idx]);

    // domains only
    $coldef['default'] =  get_request_data('cd_def_default');
    $coldef['check']   =  get_request_data('cd_def_check');
    $coldef['notnull'] = (isset($_POST['cd_def_notnull'])) ? TRUE : FALSE;

    return $coldef;
}


//
// return TRUE, if the datatype for $s_coldefs[$idx] was changed in a modify panel
//
function datatype_is_modified($olddef, $coldef) {

    $varnames = array('type', 'size', 'charset', 'prec', 'scale', 'stype', 'segsize');

    foreach ($varnames as $name) {
        if ((isset($olddef[$name])  &&  $olddef[$name]  !=  $coldef[$name])
        ||  (!isset($coldef[$name]) && !empty($olddef[$name]))
        ||  (!isset($olddef[$name]) && !empty($coldef[$name]))) {

            return TRUE;
        }
    }

    return FALSE;
}


//
// return TRUE, if the foreign key constraint of a column was changed in a modify panel
//
function column_fk_is_modified($olddef, $coldef) {

    $varnames = array('fk_name', 'on_update', 'on_delete', 'fk_table', 'fk_column');
    foreach ($varnames as $name) { 
        if ((isset($olddef[$name])  &&  $olddef[$name]  !=  $coldef[$name])
        ||  (!isset($coldef[$name]) && !empty($olddef[$name]))
        ||  (!isset($olddef[$name]) && !empty($coldef[$name]))) {

            return TRUE;
        }
    }

    return FALSE;
}


//
// return the <col_def> string for an CREATE/ALTER sql-statement
// build from $s_coldefs[$idx]
//
function build_coldef($idx, $mode='create') {
    global $s_coldefs;

    if (!isset($s_coldefs[$idx]['name']))
        return '';
    $sql = "\t".$s_coldefs[$idx]['name']."\t";

    if (isset($s_coldefs[$idx]['domain'])  &&  $s_coldefs[$idx]['domain'] != '' ) {
        $sql .= $s_coldefs[$idx]['domain'];
    }
    elseif (isset($s_coldefs[$idx]['comp'])  &&  $s_coldefs[$idx]['comp'] != '') {
        $sql .= 'COMPUTED BY ('.$s_coldefs[$idx]['comp'].')';
    }
    elseif (isset($s_coldefs[$idx]['type'])) {
        $sql .= build_datatype($s_coldefs[$idx], 'column', $mode);
    }
    else {
        return '';
    }

    if (isset($s_coldefs[$idx]['default'])  &&  $s_coldefs[$idx]['default'] != '') {
	$sql .= " DEFAULT ".$s_coldefs[$idx]['default'];
    }

    if (isset($s_coldefs[$idx]['notnull'])  &&  $s_coldefs[$idx]['notnull'] != '') {
	$sql .= ' NOT NULL';
    }

    if (isset($s_coldefs[$idx]['unique'])  &&  $s_coldefs[$idx]['unique'] != '') {
	$sql .= ' UNIQUE';
    }

    if (isset($s_coldefs[$idx]['check'])  &&  $s_coldefs[$idx]['check'] != '') {
	$sql .= ' CHECK ('.$s_coldefs[$idx]['check'].')';
    }

    if ('alter' == $mode  &&  isset($s_coldefs[$idx]['primary'])) {
        $sql .= ' PRIMARY KEY';
    }

    if (isset($s_coldefs[$idx]['fk_table'])  &&  $s_coldefs[$idx]['fk_table'] != '') {

        if (isset($s_coldefs[$idx]['fk_name'])  &&  $s_coldefs[$idx]['fk_name'] != '') {
            $sql .= ' CONSTRAINT '.$s_coldefs[$idx]['fk_name'];
        }

        $sql .= ' REFERENCES '.$s_coldefs[$idx]['fk_table'];

        if (isset($s_coldefs[$idx]['fk_column'])  &&  $s_coldefs[$idx]['fk_column'] != '') {
            $sql .= ' ('.$s_coldefs[$idx]['fk_column'].')';
        }

        if (isset($s_coldefs[$idx]['on_update'])  &&  $s_coldefs[$idx]['on_update'] != '') {
            $sql .= ' ON UPDATE '.$s_coldefs[$idx]['on_update'];
        }

        if (isset($s_coldefs[$idx]['on_delete'])  &&  $s_coldefs[$idx]['on_delete'] != '') {
            $sql .= ' ON DELETE '.$s_coldefs[$idx]['on_delete'];
        }
    }

    if (isset($s_coldefs[$idx]['collate'])  &&  $s_coldefs[$idx]['collate'] != '') {
	$sql .= ' COLLATE '.$s_coldefs[$idx]['collate'];
    }

    return $sql;
}


//
// return the <datatype> string for an CREATE/ALTER sql-statement
// build from $s_coldefs[$idx]
//
function build_datatype($defs, $type='column', $mode='create') {

    $datatype = $defs['type'];
    $sql = '';

    switch($datatype) {
    case 'CHARACTER' :
    case 'VARCHAR'   :
	$sql .= $datatype;
        if ($defs['size'] > 0) {
	    $sql .= ' ('.$defs['size'].')';
        }
        if (!empty($defs['charset'])  &&  $defs['charset'] != 'NONE') {
	    $sql .= ' CHARACTER SET '.$defs['charset'];
        }
        break;
    case 'DECIMAL':
    case 'NUMERIC':
	$sql .= $datatype;
        if ($defs['prec'] > 0) {
	    $sql .= ' ('.$defs['prec'];
	    if ($defs['scale'] > 0)
	        $sql .= ','.$defs['scale'];
	    $sql .= ')';
        }
        break;
    case 'BLOB':
	$sql .= $datatype;
        if ($defs['stype'] != '')
	    $sql .= ' SUB_TYPE '.$defs['stype'];
        if ($defs['segsize'] != '')
	    $sql .= ' SEGMENT SIZE '.$defs['segsize'];
        if (!empty($defs['charset'])  &&  $defs['charset'] != 'NONE')
	    $sql .= ' CHARACTER SET '.$defs['charset'];
        break;
    case 'DOUBLE' :
	$sql .= 'DOUBLE PRECISION';
        break;
    default:
	$sql .= $datatype;
    }

    return $sql;
}


//
// return the interbase charactersets in an array
//
function get_charsets() {
    global $dbhandle;

    $sql = 'SELECT CS.RDB$CHARACTER_SET_NAME AS NAME,'
                .' CS.RDB$CHARACTER_SET_ID AS ID,'
                .' CO.RDB$COLLATION_NAME AS CNAME,'
                .' CO.RDB$COLLATION_ID AS CID'
           .' FROM RDB$CHARACTER_SETS CS'
          .' INNER JOIN RDB$COLLATIONS CO'
             .' ON CS.RDB$CHARACTER_SET_ID=CO.RDB$CHARACTER_SET_ID'
          .' ORDER BY CS.RDB$CHARACTER_SET_NAME, CO.RDB$COLLATION_NAME';
    $res = fbird_query($dbhandle, $sql) or ib_error(__FILE__, __LINE__, $sql);

    $charsets = array();
    while ($obj = fbird_fetch_object($res)) {
        $charsets[$obj->ID]['name'] = trim($obj->NAME);
        $charsets[$obj->ID]['collations'][$obj->CID] = trim($obj->CNAME);
    }
    fbird_free_result($res);

    return $charsets;
}


//
// check for existing dependecies of a db object which will cause dropping the object to fail
//
// Parameters: $type   see the object-type definitions in firebird.inc.php
//             $name   object name in RDB$DEPENEDENCIES.RDB$DEPENDED_ON_NAME
//             $fname  optional fieldname in  RDB$DEPENEDENCIES.RDB$FIELD_NAME
//
// Result: array  with one entry for every existing dependency
//                the entries are array with a 'type' and a 'name' index
function get_dependencies($type, $name, $fname=NULL) {
    global $dbhandle;
    
    switch ($type) {
    case OT_RELATION:
        $ignore = array(OT_COMPUTED_FIELD);
        break;
    default:
        $ignore = array();
    }
    $ignore_str = !empty($ignore) ? ' AND D.RDB$DEPENDENT_TYPE NOT IN ('.implode(',', $ignore).')' : '';

    $field_str = ($fname != NULL) ? " AND D.RDB\$FIELD_NAME='".$fname."'" : '';

    $sql = 'SELECT DISTINCT D.RDB$DEPENDENT_NAME DNAME,'
                .' T.RDB$TYPE_NAME DTYPE'
           .' FROM RDB$DEPENDENCIES D'
          .' INNER JOIN RDB$TYPES T'
             .' ON D.RDB$DEPENDENT_TYPE=T.RDB$TYPE'
          ." WHERE D.RDB\$DEPENDED_ON_NAME='".$name."'"
                 . $field_str
                 . $ignore_str
            .' AND D.RDB$DEPENDED_ON_TYPE='.$type
            ." AND T.RDB\$FIELD_NAME='RDB\$OBJECT_TYPE'"
            .' AND NOT EXISTS (SELECT RDB$TRIGGER_NAME '
                              .' FROM RDB$CHECK_CONSTRAINTS C'
                             .' WHERE C.RDB$TRIGGER_NAME=D.RDB$DEPENDENT_NAME)';
    $res = fbird_query($dbhandle, $sql)
        or ib_error(__FILE__, __LINE__, $sql);
    $dependencies = array();
    while ($row = fbird_fetch_object($res)) {
        $dependencies[] = array('type' => $row->DTYPE,
                                'name' => $row->DNAME);
    }
    fbird_free_result($res);

    return $dependencies;
}


//
// build the part of a message string from a dependecies array
//
function dependencies_string($dependencies) {
    
    $str = '<br>';
    foreach ($dependencies as $dep) {
        $str .= $dep['type'] .' : '. $dep['name'] ."<br>\n";
    }

    return $str;
}


//
// connect the database using the values from the login panel
//
function db_connect() {
    global $s_login;

    $db_path = ($s_login['host'] == '') ? $s_login['database'] : $s_login['host'].':'.$s_login['database'];
    $cfunc = (PERSISTANT_CONNECTIONS === TRUE) ? 'fbird_pconnect' : 'fbird_connect';


    if ($dbh = $cfunc($db_path, $s_login['user'], $s_login['password'], $s_login['charset'], $s_login['cache'], $s_login['dialect'], $s_login['role'])) {

        return $dbh;
    }

    return FALSE;
}


//
// check if $filename have the extension DATABASE_SUFFIX
//
function have_db_suffix($filename) {
    global $DATABASE_SUFFIXES;

    if (is_array($DATABASE_SUFFIXES)  &&  count($DATABASE_SUFFIXES) > 0) {
        $fileend = strtoupper(substr(strrchr($filename, '.'), 1));
        foreach ($DATABASE_SUFFIXES as $suffix) {
            if ($fileend == strtoupper($suffix)) {

                return TRUE;
            }
        }

        return FALSE;
    }
    
    return TRUE;
}


//
// check if $filename is allowed by $ALLOWED_FILES OR $ALLOWED_DIRS
//
function is_allowed_db($filename) {
    global $ALLOWED_FILES, $ALLOWED_DIRS;

    $cmp_func = (stristr(php_uname(), 'wind') !== FALSE) ? 'strcasecmp' : 'strcmp';

    if (isset($ALLOWED_FILES)  &&  count($ALLOWED_FILES) > 0) {
        foreach ($ALLOWED_FILES as $file) {
            if ($cmp_func($filename, $file) == 0) {

                return TRUE;
            }
        }
        return FALSE;
    }

    $dirname = dirname($filename);
    if (isset($ALLOWED_DIRS)  &&  count($ALLOWED_DIRS) > 0) {
        foreach ($ALLOWED_DIRS as $dir) {
            if ($cmp_func($dirname, substr($dir, 0 ,-1)) == 0) {

                return TRUE;
            }
        }
        return FALSE;
    }

    return TRUE;
}


//
// pull the content of a blob out of the database
//
function get_blob_content($sql) {
    global $dbhandle;

    $res = fbird_query($dbhandle, $sql) or ib_error(__FILE__, __LINE__, $sql);
    $row = fbird_fetch_row($res);
    if ($blob_handle = @fbird_blob_open($row[0])) {
        $blob_info   = fbird_blob_info($row[0]);
        $blob_length = $blob_info[0];
        $blob = fbird_blob_get($blob_handle, $blob_length);
        fbird_blob_close($blob_handle);
    }
    else {
        $blob = 'not a blob!';
    }

    return $blob;
}


//
// query a fields default value and return it as a string
//
function get_field_default($tablename, $fieldname) {

    $dsource = get_blob_content('SELECT RDB$DEFAULT_SOURCE'
                                .' FROM RDB$RELATION_FIELDS'
                               ." WHERE RDB\$FIELD_NAME='".$fieldname."'"
                                 ." AND RDB\$RELATION_NAME='".$tablename."'");

    preg_match("/DEFAULT\s+'?(([^']*)|(\d*))'?/", $dsource, $matches);
    $default = ifsetor($matches[1]);

    return $default;
}


//
// query a fields computed source value and return it as a string
//
function get_field_computed_source($tablename, $fieldname) {

    $csource = get_blob_content('SELECT F.RDB$COMPUTED_SOURCE'
                                .' FROM RDB$RELATION_FIELDS R'
                               .' INNER JOIN RDB$FIELDS F'
                                  .' ON F.RDB$FIELD_NAME=R.RDB$FIELD_SOURCE'
                               ." WHERE R.RDB\$FIELD_NAME='".$fieldname."'"
                                 ." AND R.RDB\$RELATION_NAME='".$tablename."'");
    return $csource;
}


//
// complete the fields properties array ($s_fields) for the fields with default values
//
function get_table_defaults_sources($tablename, $fields) {

    foreach ($fields[$tablename] as $idx => $field) {
        if (!isset($field['default'])  ||  !empty($field['dsource'])) {
            continue;
        }
        $fields[$tablename][$idx]['dsource'] = get_field_default($tablename, $field['name']);
    }

    return $fields;
}


//
// complete the fields properties array ($s_fields) for computed fields
//
function get_table_computed_sources($tablename, $fields) {

    foreach ($fields[$tablename] as $idx => $field) {
        if (!isset($field['comp'])  ||  !empty($field['csource'])) {
            continue;
        }
        $fields[$tablename][$idx]['csource'] = get_field_computed_source($tablename, $field['name']);
    }

    return $fields;
}


//
// return the column names of a table in an array
//
function get_table_fields($name) {
    global $dbhandle;

    // get the field names and types
    $sql  = 'SELECT RDB$FIELD_NAME AS FNAME'
            .' FROM RDB$RELATION_FIELDS'
           .' WHERE RDB$RELATION_NAME=\''.$name.'\''
       .' ORDER BY RDB$FIELD_NAME';

    $res = fbird_query($dbhandle, $sql) or ib_error(__FILE__, __LINE__, $sql);

    $columns = array();
    while ($row = fbird_fetch_object($res)) {
        $columns[] = trim($row->FNAME);
    }
    fbird_free_result($res);

    return $columns;
}


//
//  return the number of rows in $tablename
//
function get_table_count($tablename) {

    $quote = identifier_quote($GLOBALS['s_login']['dialect']);

    $sql = 'SELECT COUNT(*) AS CNT FROM ' . $quote . $tablename . $quote;
    $res = fbird_query($GLOBALS['dbhandle'], $sql)
        or $ib_error .= fbird_errmsg()."<br>\n";
    $count = FALSE;
    if (is_resource($res)) {
        $row = fbird_fetch_row($res);
        $count = $row[0];
        fbird_free_result($res);
    }

    return $count;
}


//
// update the specified dataset
//
function update_row($table, $cols, $values, $condition) {

    $quote = identifier_quote($GLOBALS['s_login']['dialect']);

    $sql = 'UPDATE ' . $quote . $table . $quote
            .' SET ' . $quote . implode($quote.'=?, '.$quote, $cols) . $quote . '=?'
          .' WHERE ' . $condition;
    if (DEBUG) add_debug('$sql: '.$sql, __FILE__, __LINE__);

    $query = fbird_prepare($GLOBALS['dbhandle'], $sql) or ib_error(__FILE__, __LINE__, $sql);
    $ib_error = '';
    call_user_func_array('fbird_execute', array_merge(array($query), $values))
        or $ib_error = fbird_errmsg();
    fbird_free_query($query);

    return $ib_error;
}


//
// insert a dataset init a table
//
function insert_row($table, $cols, $values) {

    $quote = identifier_quote($GLOBALS['s_login']['dialect']);

    $sql = 'INSERT INTO ' . $quote . $table . $quote . ' (' . $quote . implode($quote . ', ' . $quote, $cols) . $quote . ')'
         .' VALUES ('.substr(str_repeat('?, ', count($values)), 0, -2).')';
    if (DEBUG) add_debug('$sql: '.$sql, __FILE__, __LINE__);

    $query = fbird_prepare($GLOBALS['dbhandle'], $sql) or ib_error(__FILE__, __LINE__, $sql);
    $ib_error = '';
    call_user_func_array('fbird_execute', array_merge(array($query), $values))
        or $ib_error = fbird_errmsg();
    fbird_free_query($query);

    return $ib_error;
}


//
// determine the server type and version from the server setting from the login panel
//
function server_info($server_string) {
    
    preg_match('/([A-Z]+)_([0-9]+).([0-9]+)/', $server_string , $matches);
    $family  = count($matches) == 4 ? $matches[1] : '';
    $version = count($matches) == 4 ? $matches[2].$matches[3] : 0; 

    return array($family, $version);
}


//
// guess and return the fastest method for browsing tables for a given database server
//
function guess_watchtable_method($server_family, $server_version) {

    if ($server_family == 'FB') {
        return WT_FIREBIRD_SKIP;
    }
    return WT_SKIP_ROWS;
}


//
// send the http headers for a file download
//
function send_export_headers($mimetype, $filename) {

    header('Content-Type: '.$mimetype);
    header('Content-Disposition: inline; filename="'.$filename.'"');
    header('Pragma: no-cache');
    header('Expires: 0');
}


//
// send http-headers to prevent browser-caching
// and set the charset for the content
//
function send_http_headers() {

    $now = gmdate('D, d M Y H:i:s') . ' GMT';
    header('Expires: 0');
    header('Last-Modified: '.$now);
    header('Cache-Control: no-store, no-cache, must-revalidate');
    header('Cache-Control: pre-check=0, post-check=0, max-age=0');
    header('Pragma: no-cache');

    header('Content-Type: text/html; charset='.$GLOBALS['charset']);
}


// starting sequence foe all html pages
function html_head($title) {

    return "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">\n"
        ."<html>\n"
        ."<head>\n"
        .'  <title>'.$title."</title>\n"
	.'  <meta http-equiv="Content-type" content="text/html; charset='.$GLOBALS['charset']."\">\n"
	.'  <meta name="viewport" content="width=device-width, initial-scale=1.0">'
	.'  <link href="//netdna.bootstrapcdn.com/bootstrap/3.0.2/css/bootstrap.css"" rel="stylesheet">'
	.'  <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.2/css/bootstrap-theme.css">'
	.'  <link rel="stylesheet" type="text/css" href="'.url_session('./stylesheet.php')."\">\n"
	.'  <script src="https://code.jquery.com/jquery.js"></script>'
	.'  <script src="//netdna.bootstrapcdn.com/bootstrap/3.0.2/js/bootstrap.js"></script>'
        ."  <script src=\"./js/miscellaneous.js\" type=\"text/javascript\"></script>\n"
	."</head>\n";
}


//
// return the html for the tabmenu
//
function get_tabmenu($page) {

    $menuentries = array('Database'    => url_session('database.php'),
                         'Tables'      => url_session('tables.php'),
                         'Accessories' => url_session('accessories.php'),
                         'SQL'         => url_session('sql.php'),
                         'Data'        => url_session('data.php'),
                         'Users'       => url_session('user.php'),
                         'Admin'       => url_session('admin.php'));

    $html = "<ul class=\"nav nav-pills nav-justified\">\n";

    foreach ($menuentries as $item => $script) {
        if (count($_SESSION['s_'.strtolower($item).'_panels']) == 1) {
            continue;
        }
        $class = $page == $item ? 'active' : '';

        $html .= '    <li class="'.$class."\">\n"
               . '      <a class="menu-link" href="'.$script.'">'.$GLOBALS['menu_strings'][$item]."</a>\n"
               . "    </li>\n";
    }
    
    $html .= "</ul>\n";

    return $html;
}


//
// redirect the client to $url
//
function redirect($url) {

    if (META_REDIRECT === TRUE) {
        echo "<head>\n"
            .'  <meta http-equiv="refresh" content="0; URL='.$url."\">\n"
            ."</head>\n";
    }
    else {
        header('Location: '.$url);
    }

    exit;
}


//
// print fbird_errmsg() and stop the script
//
function ib_error($file='', $line='', $sql='') {

    echo '<b>Firebird Error</b><br>'
        .'file: '.$file.', line: '.$line.'<br>'
        .'statement: '.$sql.'<br>'
        .'fbird_errmsg: '.fbird_errmsg();
    exit;
}


//
// examine the version of the running php interpreter
//
function get_php_version() {

    preg_match('/^([0-9]+).([0-9]+).([0-9]+)/', phpversion(), $matches);

    $vinfo = array ('VER' => $matches[1],
                    'SUB' => ((strlen($matches[2]) > 0) ? $matches[2] : 0),
                    'MIN' => ((strlen($matches[3]) > 0) ? $matches[3] : 0)
                    );
    return $vinfo;
}


//
// adjust language-specific items to a new LANGUAGE setting
//
function fix_language() {
    global $ptitle_strings;

    foreach (panel_array_names() as $key => $parray) {
        global $$parray;
        foreach ($$parray as $idx => $panel) {

            // skip edit- and confirmation-panels
            if (!isset($ptitle_strings[$panel[0]])) {
                continue;
            }

            ${$parray}[$idx][1] = $ptitle_strings[$panel[0]];
        }
    }
}


//
// handler for php errors, $php_error is displayed on the info-panel
//
function error_handler($errno, $errmsg, $file, $line, $errstack) {
    global $php_error, $warning;

    if (stristr($errmsg, 'ibase') == TRUE) {
        return;
    }

    if (!(error_reporting() & $errno)) {
        return;
    }
    
    if (E_ERROR & $errno) {
        $php_error .= "$errmsg<br>\n"
                    . "in file: $file, line $line<br>\n";
    }
    else {
        $warning .= "php: $errmsg<br>\n"
                  . "in file: $file, line $line<br>\n";
    }
}


//
// store the customizing datas in a cookie named 'ibwa_customize'
//
function set_customize_cookie($customize) {

    // don't save rubbish if the session is gone
    if (empty($customize['color'])) {
        return;
    }

    $settings = get_customize_cookie_version()."\n"
              . implode('|', $customize['color'])."\n"
              . $customize['language']."\n"
              . $customize['fontsize']."\n"
              . implode('|', $customize['textarea'])."\n"
              . $customize['iframeheight']."\n"
              . $customize['askdel']."\n"
              . implode('|', $customize['enter'])."\n";

    $fk_lookup = '';
    foreach ($customize['fk_lookups'] as $tablename => $table_lookups) {
        if (empty($tablename)  ||  empty($table_lookups)) {
            continue;
        }
        $fk_lookup .= $tablename . '|';
        foreach ($table_lookups as $fk_column => $lookup_column) {
            if (empty($fk_column)  ||  empty($lookup_column)) {
                continue;
            }
            $fk_lookup .= $fk_column . '|' . $lookup_column . '|';
        }
        $fk_lookup[(strlen($fk_lookup) -1)] = '#';
    }
    $fk_lookup = substr($fk_lookup, 0, -1);
    $settings .= $fk_lookup."\n";

    $wts = '';
    foreach ($customize['wt'] as $database => $wt) {
        if (empty($database)) {
            continue;
        }
        $wts .= $database.'|'.implode('|', $wt).'#';
    }
    $wts = substr($wts, 0, -1);
    $settings .= $wts."\n";

    $pstate = '';
    foreach (panel_array_names() as $pname) {
        foreach ($GLOBALS[$pname] as $panel) {
            $settings .= $panel[0]. '|';
            $pstate .= $panel[2] == 'open' ? '1' : '0';
        }
        $settings = substr($settings, 0, -1) ."\n";
    }
    $settings .= $pstate;

    setcookie(get_customize_cookie_name() , $settings, time() + 60*60*24*180, '/');
}


//
// restore the customizing from the string fetched out of the cookie 'ibwa_customize'
//
function set_customize_settings($cookie_string) {

    $settings = explode("\n", $cookie_string);

    if ($settings[0] != get_customize_cookie_version()) {

        return get_customize_defaults();
    }

    $colors = explode('|', $settings[1]);
    $cnames = get_colornames();
    foreach ($colors as $idx => $color) {
        $customize['color'][$cnames[$idx]] = $color;
    }
    
    $customize['language'] = $settings[2];
    $customize['fontsize'] = $settings[3];

    list ($cols, $rows) = explode('|', $settings[4]);;
    $customize['textarea']['cols'] = $cols;
    $customize['textarea']['rows'] = $rows;

    $customize['iframeheight'] = $settings[5];

    $customize['askdel'] = $settings[6];

    list($another_row, $fk_lookup, $as_new) = explode('|', $settings[7]);
    $customize['enter']['another_row'] = (bool)$another_row;
    $customize['enter']['fk_lookup']   = (bool)$fk_lookup;
    $customize['enter']['as_new']      = (bool)$as_new;

    $customize['fk_lookups'] = array();
    $tables = explode('#', $settings[8]);
    foreach ($tables as $tvalues) {
        $values = explode('|', $tvalues);
        if ((count($values) -1) % 2 != 0) {
            continue;
        }

        $tindex = array_shift($values);
        for ($cnt=0; $cnt<count($values); $cnt+=2) {
            $customize['fk_lookups'][$tindex][$values[$cnt]] = $values[($cnt +1)];
        }
    }

    $wts = explode('#', $settings[9]);
    foreach ($wts as $wt) {
        list($database, $table, $start, $order, $direction) = explode('|', $wt);
        $customize['wt'][$database] = array('table' => $table,
                                            'start' => $start,
                                            'order' => $order,
                                            'dir'   => $direction);
    }

    return $customize;
}


//
// return the cookie version number
// increase if the format of the customize-cookie changes
//
function get_customize_cookie_version() {

    return 3;
}


//
// return the name for customiize cookie
//
function get_customize_cookie_name() {

    return 'ibwa_customize2';
}


//
// restore the panel states from the cookie values
//
function rearrange_panels($session_vars, $cookie_string) {

    // start index for the panel settings
    $offset = 10;

    $settings = explode("\n", $cookie_string);
    $settings = array_slice ($settings, $offset);

    $offset = $pstate_offset = 0;

    foreach (panel_array_names() as $aname) {
        $panels = array();
        $panelorder = explode('|', $settings[$offset++]);

        if (count($panelorder) != count($session_vars[$aname])) {
            // cookie is out of date
            continue;
        }

        foreach ($panelorder as $panelname) {
            $idx = get_panel_index($session_vars[$aname], $panelname);
            $panels[] = $session_vars[$aname][$idx];
            $panels[count($panels) -1][2] = $settings[7][$pstate_offset++] == 1 ? 'open' : 'close';
        }

        $session_vars[$aname] = $panels;
    }

    return $session_vars;
}


//
// return the default values for all customize properties as defined in inc/configuration.inc.php
//
function get_customize_defaults($useragent) {

    return array('color' => array('background'       => COLOR_BACKGROUND,
                                  'panel'            => COLOR_PANEL,
                                  'area'             => COLOR_AREA,    
                                  'headline'         => COLOR_HEADLINE,
                                  'menuborder'       => COLOR_MENUBORDER,
                                  'iframeborder'     => COLOR_IFRAMEBORDER,
                                  'iframebackground' => COLOR_IFRAMEBACKGROUND,
                                  'link'             => COLOR_LINK,
                                  'linkhover'        => COLOR_LINKHOVER,
                                  'selectedrow'      => COLOR_SELECTEDROW,
                                  'selectedinput'    => COLOR_SELECTEDINPUT,
                                  'firstrow'         => COLOR_FIRSTROW,
                                  'secondrow'        => COLOR_SECONDROW),
                 'language'     => LANGUAGE,
                 'fontsize'     => ($useragent['ie'] ? 8 : 11),
                 'textarea'     => array('cols' => SQL_AREA_COLS,
                                        'rows' => SQL_AREA_ROWS),
                 'iframeheight' => IFRAME_HEIGHT,

                 'askdel'   => (CONFIRM_DELETE ? 1 : 0),
                 'enter'    => array('another_row' => TRUE,
                                     'fk_lookup'   => TRUE,
                                     'as_new'      => FALSE),
                 'fk_lookups' => array(),
                 'wt'         => array(),
                 );

}


//
// return the indices for the color customize settings in $s_cust
//
function get_colornames() {

    return array('background',
                 'panel',
                 'area',
                 'headline',
                 'menuborder',
                 'iframeborder',
                 'iframebackground',
                 'link',
                 'linkhover',
                 'selectedrow',
                 'selectedinput',
                 'firstrow',
                 'secondrow');
}


//
// return the supported language names
//
function get_customize_languages() {

    return array('brazilian_portuguese', 'czech', 'dutch', 'english', 'hungarian', 'japanese', 'german', 'polish', 'russian-win1251', 'spanish');
}


// this replacement for php's escapeshellarg() is also working on windows
function ibwa_escapeshellarg($str) {

    return !empty($str) ? '"' . str_replace('"', '\"', $str) . '"' : '';
}


//
// prepare external data for further using
//
function get_request_data($name, $source='POST') {

    if (isset($GLOBALS['_'.$source][$name])) {
        if (is_array($GLOBALS['_'.$source][$name])) {
            return $GLOBALS['_'.$source][$name];
        }
        $data = trim($GLOBALS['_'.$source][$name]);
        if ($source == 'GET') {
            $data = urldecode($data);
        }
        if (get_magic_quotes_gpc()  ||
            ini_get('magic_quotes_sybase') == 1) {

            $data = stripslashes($data);
        }

        return $data;
    }
    else {

        return NULL;
    }
}


//
// return the variable value if the variable is set
// or the altenative value otherwise
//
function ifsetor(&$var, &$alt=NULL) {

    return isset($var) ? $var : $alt;
}


//
// return double quotes for dialect 3, an empty strings otherwise
//
function identifier_quote($dialect) {

    return $dialect == 3 ? '"' : '';
}


function fb_escape_string($str) {

    return str_replace("'", "''", $str);
}


//
// check if the $user is allowed to execute functions from the panel $pname
//
function have_panel_permissions($user, $pname, $connected=FALSE) {

    if ($connected  &&  !$GLOBALS['s_connected']) {
        return FALSE;
    }

    $is_open = FALSE;
    foreach (panel_array_names() as $paname) {
        foreach ($GLOBALS[$paname] as $idx => $panel) {
            if (in_array($pname, $panel)  && $panel[2] == 'open') {
                $is_open = TRUE;
                break 2;
            }
        }
    }
    if (!$is_open) {
        return FALSE;
    }

    if (in_array($pname, $GLOBALS['HIDE_PANELS'])  &&
        ($user != 'SYSDBA'  || SYSDBA_GET_ALL == FALSE)) {

        return FALSE;
    }

    return TRUE;
}

?>
