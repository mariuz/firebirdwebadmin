<?php
// File           foreign_keys.inc.php / FirebirdWebAdmin
// Purpose        functions regarding foreign keys
// Author         Lutz Brueckner <irie@gmx.de>
// Copyright      (c) 2000, 2001, 2002, 2003, 2004, 2005 by Lutz Brueckner,
//                published under the terms of the GNU General Public Licence v.2,
//                see file LICENCE for details

//
// find the foreign keys defined for $table,
// only foreign keys over single columns are taken into consideration
//
function get_foreignkeys($tablename, $privilege = null)
{
    $sql = 'SELECT I2.RDB$RELATION_NAME FKTABLE,'
                .' IS1.RDB$FIELD_NAME FKFIELD,'
                .' IS2.RDB$FIELD_NAME TFIELD'
           .' FROM RDB$RELATION_CONSTRAINTS RC'
     .' INNER JOIN RDB$INDICES I1 ON RC.RDB$INDEX_NAME=I1.RDB$INDEX_NAME'
     .' INNER JOIN RDB$INDICES I2 ON I1.RDB$FOREIGN_KEY=I2.RDB$INDEX_NAME'
     .' INNER JOIN RDB$INDEX_SEGMENTS IS1 ON I2.RDB$INDEX_NAME=IS1.RDB$INDEX_NAME'
     .' INNER JOIN RDB$INDEX_SEGMENTS IS2 ON I1.RDB$INDEX_NAME=IS2.RDB$INDEX_NAME'
          ." WHERE RC.RDB\$RELATION_NAME='".$tablename."'"
            ." AND RC.RDB\$CONSTRAINT_TYPE='FOREIGN KEY'"
            .' AND I1.RDB$SEGMENT_COUNT=1';

    $res = @fbird_query($GLOBALS['dbhandle'], $sql) or ib_error(__FILE__, __LINE__, $sql);

    $fk = array();
    while ($row = fbird_fetch_object($res)) {
        $fktable = trim($row->FKTABLE);
        if (empty($privilege)  ||  in_array($privilege, $GLOBALS['s_tables'][$fktable]['privileges'])) {
            $fk[trim($row->TFIELD)] = array('table' => $fktable,
                                            'column' => trim($row->FKFIELD),
                                            );
        }
    }
    fbird_free_result($res);

    return $fk;
}

//
// return TRUE if the table $tablename contains a column with a foreign key definition
//
function have_fk($tablename)
{
    return count(array_filter($GLOBALS['s_fields'][$tablename], function($a) {return isset($a["foreign"]);} )) > 0;
}

//
// return infos about a tables foreign keys in an array
//
function get_fk_lookups_data($tablename, $fk_lookups)
{
    $lookups_data = array();
    $fk_defs = get_foreignkeys($tablename, 'S');
    foreach ($fk_defs as $colname => $defs) {

        // skip foreign keys with more than FKLOOKUP_ENTRIES values
        if (!isset($GLOBALS['s_tables'][$defs['table']]['count'])) {
            $GLOBALS['s_tables'][$defs['table']]['count'] = get_table_count($defs['table']);
        }
        if ($GLOBALS['s_tables'][$defs['table']]['count'] > FKLOOKUP_ENTRIES) {
            continue;
        }

        $value_field = ifsetor($fk_lookups[$colname], $defs['column']);
        if ($value_field != $defs['column']) {
            $value_field = 'COALESCE('.$value_field.", '')"." || ' - '".' || '.$defs['column'];
        }

        $sql = 'SELECT '.$defs['column'].', '.$value_field.' FROM '.$defs['table'].' ORDER BY '.$value_field.' ASC';
        $res = fbird_query($GLOBALS['dbhandle'], $sql) or ib_error(__FILE__, __LINE__, $sql);

        $data = array();
        while ($row = fbird_fetch_row($res)) {
            $data[trim($row[0])] = trim($row[1]);
        }
        fbird_free_result($res);

        $lookups_data[$colname] = array('table' => $defs['table'],
                                        'column' => $defs['column'],
                                        'data' => $data, );
    }

    return $lookups_data;
}
