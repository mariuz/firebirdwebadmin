<?php
// File           system_table.inc.php / FirebirdWebAdmin
// Purpose        functions concerning $s_system_table, the table to display
//                on the System Table panel
// Author         Lutz Brueckner <irie@gmx.de>
// Copyright      (c) 2001-2006 by Lutz Brueckner,
//                published under the terms of the GNU General Public Licence v.2,
//                see file LICENCE for details

//
// return an array to display an the System Table panel
//
function get_systable($s_systable)
{
    global $dbhandle;

    // get the field names and types
    $sql = 'SELECT RDB$RELATION_FIELDS.RDB$FIELD_NAME AS FNAME,'
                 .' RDB$RELATION_FIELDS.RDB$FIELD_POSITION,'
                 .' RDB$FIELD_TYPE AS FTYPE,'
                 .' RDB$FIELD_SUB_TYPE AS STYPE'
            .' FROM RDB$RELATION_FIELDS, RDB$FIELDS'
           .' WHERE RDB$RELATION_NAME=\''.$s_systable['table'].'\''
             .' AND RDB$FIELD_SOURCE=RDB$FIELDS.RDB$FIELD_NAME'
       .' ORDER BY RDB$FIELD_POSITION';

    $res = fbird_query($dbhandle, $sql) or ib_error(__FILE__, __LINE__, $sql);

    $table = array();
    while ($row = fbird_fetch_object($res)) {
        $type = (isset($row->FTYPE)) ? $row->FTYPE : null;
        $stype = (isset($row->STYPE)) ? $row->STYPE : null;
        $table[trim($row->FNAME)]['type'] = get_datatype($type, $stype);
    }
    fbird_free_result($res);

    // get the table content
    $sql = 'SELECT *' //.implode(',', array_keys($table))
.' FROM '.$s_systable['table'];
    if ($s_systable['sysdata'] == false) {
        $fields = array_keys($table);
        $sql .= ' WHERE '.pos($fields)." NOT LIKE 'RDB\$%'"
                 .' AND '.pos($fields)." NOT LIKE 'TMP\$%'";
    }

    // handle the filter
    if (!empty($s_systable['ffield'])  &&  in_array($s_systable['ffield'], array_keys($table))) {
        $sql .= $s_systable['sysdata'] == true ? ' WHERE ' : ' AND ';
        switch ($s_systable['fvalue']) {
            case '':
                $sql .= $s_systable['ffield'].' IS NULL';
                break;
            case 'BLOB':
                if ($table[$s_systable['ffield']]['type'] == 'BLOB') {
                    $sql .= $s_systable['ffield'].' IS NOT NULL';
                    break;
                }
            default:
                $sql .= $s_systable['ffield']."='".$s_systable['fvalue']."'";
        }
    }

    if (!empty($s_systable['order'])) {
        $sql .= ' ORDER BY '.$s_systable['order'].' '.$s_systable['dir'];
    }
    $res = fbird_query($dbhandle, $sql) or ib_error(__FILE__, __LINE__, $sql);

    while ($row = fbird_fetch_object($res)) {
        foreach (array_keys($table) as $fname) {
            if ($row->$fname === 0) {
                $table[$fname]['col'][] = '0';
            } elseif (!isset($row->$fname)  || empty($row->$fname)) {
                $table[$fname]['col'][] = '&nbsp;';
            } elseif ($table[$fname]['type'] == 'BLOB') {
                $table[$fname]['col'][] = '<i>BLOB</i>';
            } else {
                $table[$fname]['col'][] = trim($row->$fname);
            }
        }
    }
    fbird_free_result($res);

    return $table;
}

//
// display the system table onto the System Table panel
//
function get_systable_html($table, $s_systable)
{
    $html = "<table id=\"systable\" class=\"table table-bordered table-hover tsep\" cellpadding=\"2\">\n<thead><tr>\n";
    foreach (array_keys($table) as $colname) {
        if ($s_systable['order'] == $colname) {
            $headstr = ($s_systable['dir'] == 'ASC') ? '*&nbsp;'.$colname : $colname.'&nbsp;*';
        } else {
            $headstr = $colname;
        }
        $url = url_session('database.php?order='.$colname);
        $html .= '<th><a href="'.$url.'">'.$headstr."</a></th>\n";
    }
    $html .= "</tr></thead>\n";

    $systable_textblobs = systable_textblobs();
    if (isset($systable_textblobs[$s_systable['table']])) {
        foreach ($systable_textblobs[$s_systable['table']]['columns'] as $col) {
            $GLOBALS['s_wt']['blob_as'][$col] = 'text';
        }
        $where_str = 'WHERE ';
        foreach ($systable_textblobs[$s_systable['table']]['indices'] as $idx) {
            $where_str .= $idx."='%s' AND ";
        }
        $where_str = substr($where_str, 0, -4);
    }

    if (isset($table[$colname]['col'])) {
        $rows = count($table[$colname]['col']);
        $class = 'wttr2';

        // loop the rows
        for ($i = 0; $i < $rows; ++$i) {
            $class = ($class == 'wttr1') ? 'wttr2' : 'wttr1';
            $html .= '<tr class="'.$class."\">\n";

            // loop the columns
            foreach ($table as $colname => $colarr) {
                $align = ($colarr['type'] == 'BLOB') ? 'center' : 'right';
                $val = $colarr['col'][$i];

                if (isset($systable_textblobs[$s_systable['table']])
                &&  $val != '&nbsp;'
                &&  in_array($colname, $systable_textblobs[$s_systable['table']]['columns'])) {
                    if (count($systable_textblobs[$s_systable['table']]['indices']) == 1) {
                        $where = urlencode(sprintf($where_str,
                                                   $table[$systable_textblobs[$s_systable['table']]['indices'][0]]['col'][$i]));
                    } else {
                        $where = urlencode(sprintf($where_str,
                                                   $table[$systable_textblobs[$s_systable['table']]['indices'][0]]['col'][$i],
                                                   $table[$systable_textblobs[$s_systable['table']]['indices'][1]]['col'][$i]));
                    }
                    $blob_url = 'showblob.php?where='.$where.'&table='.$s_systable['table'].'&col='.$colname;
                    $val = '<a href="'.$blob_url.'" target="_blank">'.$val.'</a>';
                }

                $html .= '<td align="'.$align.'">'.$val."</td>\n";
            }
            $html .= "</tr>\n";
        }
    }
    $html .= "</table>\n";

    return $html;
}

//
// delivers the definitions for the blob links on the systemtables panel
//
function systable_textblobs()
{
    return
        array('RDB$CHARACTER_SETS' => array('columns' => array('RDB$DESCRIPTON'),
                                                  'indices' => array('RDB$RDB$CHARACTER_SET_NAME'),
                                                  ),
              'RDB$COLLATIONS' => array('columns' => array('RDB$DESCRIPTION'),
                                                  'indices' => array('RDB$COLLATION_NAME'),
                                                  ),
              'RDB$DATABASE' => array('columns' => array('RDB$DESCRIPTION'),
                                                  'indices' => array('RDB$RELATION_ID'),
                                                  ),
              'RDB$EXCEPTIONS' => array('columns' => array('RDB$DESCRIPTION'),
                                                  'indices' => array('RDB$EXCEPTION_NAME'),
                                                  ),
              'RDB$FIELDS' => array('columns' => array('RDB$VALIDATION_SOURCE', 'RDB$COMPUTED_SOURCE',
                                                                     'RDB$DEFAULT_SOURCE', 'RDB$DESCRIPTION', ),
                                                  'indices' => array('RDB$FIELD_NAME'),
                                                  ),
              'RDB$FILTERS' => array('columns' => array('RDB$DESCRIPTION'),
                                                  'indices' => array('RDB$FUNCTION_NAME'),
                                                  ),
              'RDB$FUNCTIONS' => array('columns' => array('RDB$DESCRIPTION'),
                                                  'indices' => array('RDB$FUNCTION_NAME'),
                                                  ),
              'RDB$INDICES' => array('columns' => array('RDB$DESCRIPTION', 'RDB$EXPRESSION_SOURCE'),
                                                  'indices' => array('RDB$INDEX_NAME'),
                                                  ),
              'RDB$PROCEDURE_PARAMETERS' => array('columns' => array('RDB$DESCRIPTION'),
                                                  'indices' => array('RDB$PROCEDURE_NAME', 'RDB$PARAMETER_NAME'),
                                                  ),
              'RDB$PROCEDURES' => array('columns' => array('RDB$DESCRIPTION', 'RDB$PROCEDURE_SOURCE'),
                                                  'indices' => array('RDB$PROCEDURE_NAME'),
                                                  ),
              'RDB$RELATION_FIELDS' => array('columns' => array('RDB$DESCRIPTION', 'RDB$DEFAULT_SOURCE'),
                                                  'indices' => array('RDB$FIELD_SOURCE', 'RDB$RELATION_NAME'),
                                                  ),
              'RDB$RELATIONS' => array('columns' => array('RDB$VIEW_SOURCE', 'RDB$DESCRIPTION'),
                                                  'indices' => array('RDB$RELATION_ID'),
                                                  ),
              'RDB$SECURITY_CLASSES' => array('columns' => array('RDB$DESCRIPTION'),
                                                   'indices' => array('RDB$SECURITY_CLASS'),
                                                   ),
              'RDB$TRIGGERS' => array('columns' => array('RDB$DESCRIPTION', 'RDB$TRIGGER_SOURCE'),
                                                  'indices' => array('RDB$TRIGGER_NAME'),
                                                  ),
              'RDB$TYPES' => array('columns' => array('RDB$DESCRIPTION'),
                                                  'indices' => array('RDB$TYPE_NAME'),
                                                  ),
              );
}

function systable_field_select($table, $field = null)
{
    global $db_strings;

    $cols = get_table_fields($table);

    return '<label for="db_sysfield">'.$db_strings['FField']."</label><br>\n"
         .get_selectlist('db_sysfield', $cols, $field, true,
                           array('onChange' => "getFilterValues('".$table."' ,selectedElement(this))",
                                 'id' => 'db_sysvalues_list', )
                           );
}

function systable_value_select($table, $field, $value = null)
{
    global $dbhandle, $db_strings;

    $sql = 'SELECT DISTINCT '.$field.' AS FNAME FROM '.$table;
    $res = fbird_query($dbhandle, $sql) or ib_error(__FILE__, __LINE__, $sql);

    $values = array();
    while ($row = fbird_fetch_object($res)) {
        $values[] = trim($row->FNAME);
    }
    fbird_free_result($res);

    return '<label for="db_sysvalue">'.$db_strings['FValue']."</label><br>\n"
         .get_selectlist('db_sysvalue', $values, $value, true);
}
