<?php
// Purpose        functions for the data export panel
// Author         Lutz Brueckner <irie@gmx.de>
// Copyright      (c) 2000-2006 by Lutz Brueckner,
//                published under the terms of the GNU General Public Licence v.2,
//                see file LICENCE for details


//
//  default values for the export form and for $s_export
//
function get_export_defaults()
{

    $date_default = ini_get('ibase.dateformat');
    $time_default = ini_get('ibase.timeformat');

    $export = array('format' => 'csv',
        'source' => array('option' => 'table',
            'table' => '',
            'dbtables' => array_keys($GLOBALS['s_tables']),
            'query' => '',
        ),
        'target' => array('option' => 'screen',
            'filename' => '__TABLE__-%Y%m%d.csv'
        ),
        'general' => array('replnull' => 'NULL',                       // replace NULL values by
            'date' => ini_get('ibase.dateformat'),  // date format
            'time' => ini_get('ibase.timeformat')   // time format
        ),
        'csv' => array('fterm' => ';',        // fields terminated by
            'fencl' => '"',        // fields enclosed by
            'ftencl' => 'nonnum',   // field types to enclose
            'fesc' => '"',        // escape character
            'lterm' => '\n',       // lines terminated by
            'fnamesf' => TRUE        // field names at first row
        ),
        'sql' => array('cnames' => TRUE,       // column names
            'qnames' => FALSE,      // quote column names
            'cfields' => FALSE,      // computed fields
            'info' => TRUE,       // export info
            'lineend' => '\n',       // line ending
            'ttable' => '__TABLE__' // target table
        )
    );

    return $export;
}


//
// prepare the posted data from the exort form for $s_export
//
function get_export_form_data($old)
{

    $export = array('format' => get_request_data('dt_export_format'),

        'source' => array('option' => get_request_data('dt_export_source'),
            'table' => get_request_data('dt_export_source_table'),
            'dbtables' => get_request_data('dt_export_source_dbtables'),
            'query' => get_request_data('dt_export_query')
        ),
        'target' => array('option' => get_request_data('dt_export_target'),
            'filename' => get_request_data('dt_export_target_filename')
        ),
        'general' => array('replnull' => get_request_data('dt_export_replnull'),    // replace NULL values by
            'date' => get_request_data('dt_export_date'),        // date format
            'time' => get_request_data('dt_export_time'),        // time format
        ),
        'csv' => array('fterm' => get_request_data('dt_export_csv_fterm'),    // fields terminated by
            'fencl' => get_request_data('dt_export_csv_fencl'),    // fields enclosed by
            'ftencl' => get_request_data('dt_export_csv_ftencl'),   // field types to enclose
            'fesc' => get_request_data('dt_export_csv_fesc'),     // escape character
            'lterm' => get_request_data('dt_export_csv_lterm'),    // lines terminated by
            'fnamesf' => (boolean)get_request_data('dt_export_csv_fnamesf')   // field names at first row
        ),
        'sql' => array('cnames' => (boolean)get_request_data('dt_export_sql_cnames'),   // column names
            'qnames' => (boolean)get_request_data('dt_export_sql_qnames'),   // quote column names
            'cfields' => (boolean)get_request_data('dt_export_sql_cfields'),  // computed fields
            'info' => (boolean)get_request_data('dt_export_sql_info'),     // export info
            'lineend' => get_request_data('dt_export_sql_lineend'),           // line ending
            'ttable' => get_request_data('dt_export_sql_ttable')             // target table
        )
    );

    foreach (array_keys(get_export_formats()) as $format) {
        if ($format != $export['format'] && !empty($old[$format])) {
            $export[$format] = $old[$format];
        }
    }

    return $export;
}


//
// set default values for general options and selected format options
//
function set_export_defaults($format, $export)
{

    $defaults = get_export_defaults();

    $export['general'] = $defaults['general'];
    $export[$format] = $defaults[$format];

    if ($export['target']['option'] == 'file') {
        $export['target']['filename'] = fix_export_filename_suffix($defaults['target']['filename'], $format);
    }

    return $export;
}


//
// check plausibility for $s_export and the form values
function check_export_form_data($export)
{
    global $ERRORS, $WARNINGS;

    $error = $warning = '';

    if ($export['source']['option'] == 'table' && empty($export['source']['table'])) {
        $warning .= $WARNINGS['SELECT_TABLE_FIRST'];
    }

    return array($error, $warning);
}


//
// try to be clever about the suffix of the export filename
//
function fix_export_filename_suffix($filename, $format)
{

    if (substr($filename, strlen($filename) - 4, 1) == '.') {
        $filename = substr($filename, 0, -3) . $format;
    }

    return $filename;
}


//
// return an array containing the available export formats
//
function get_export_formats()
{
    global $dt_strings;

    return array('csv' => $dt_strings['ExpOptCsv'],
        'ext' => $dt_strings['ExpOptExt'],
        'sql' => $dt_strings['ExpOptSql']
    );
}


//
// return an array containing the available export sources
//
function get_export_sources()
{
    global $dt_strings;

    return array('table' => $dt_strings['ExpFmTbl'],
        'db' => $dt_strings['ExpFmDb'],
        'query' => $dt_strings['ExpFmQry']
    );
}


//
// return an array containing the available export targets
//
function get_export_targets()
{
    global $dt_strings;

    return array('file' => $dt_strings['ExpTgFile'],
        'screen' => $dt_strings['ExpTgScr'],
    );
}


//
// return the applicable mimetype for the selected export format
//
function get_export_mimetype($format)
{

    switch ($format) {
        case 'csv':
            $mimetype = 'text/csv';
            break;
        case 'sql':
            $mimetype = 'text/sql';
            break;
        case 'external table':
        default:
            $mimetype = 'application/octet-stream';
    }

    return $mimetype;
}


//
// prepare for export and call the format specific export function
//
function export_data($export)
{
    global $s_fields, $warning;

    ini_set('ibase.dateformat', $export['general']['date']);
    ini_set('ibase.timeformat', $export['general']['time']);
    ini_set('ibase.timestampformat', $export['general']['date'] . ' ' . $export['general']['time']);

    if ($export['format'] == 'sql' && $export['sql']['info']) {
        echo sql_export_info($export, replace_escape_sequences($export['sql']['lineend']));
    }

    foreach (export_queries($export) as $query) {

        if (DEBUG) add_debug($query, __FILE__, __LINE__);
        $trans = fbird_trans(TRANS_READ, $dbhandle);
        $res = @fbird_query($trans, $query);
        if ($res === FALSE) {
            $ib_error = fbird_errmsg();
            $warning = '';

            return FALSE;
        }

        $columns = $col_types = $num_fields = array();
        $num = fbird_num_fields($res);
        for ($idx = 0; $idx < $num; $idx++) {
            $info = fbird_field_info($res, $idx);
            $columns[] = $info['name'];
            $col_types[] = $info['type'];
            $num_fields[] = is_number_type(substr($info['type'], 0, strcspn($info['type'], '(')));
        }
        $tablename = $info['relation'];

        $export['query'] = array('source' => $query,
            'columns' => $columns,
            'col_types' => $col_types,
            'num_fields' => $num_fields,
            'result' => $res);

        switch ($export['format']) {
            case 'csv':
                export_csv_data($export);
                break;
            case 'sql':
                export_sql_data($export, $tablename);
                break;
            case 'ext':
                printf('Export data to %s is still not implemented!', $export['format']);
                break;
            default:
                echo 'Unsupported export format!';
        }

        fbird_free_result($res);
        fbird_commit($trans);
    }
}


//
// return an array containing the sql-statements to query the data to export
//
function export_queries($export)
{

    switch ($export['source']['option']) {
        case 'table':
            $queries = array(export_table_query($export['source']['table']));
            break;
        case 'db':
            foreach ($export['source']['dbtables'] as $table) {
                $queries[] = export_table_query($table, $export);
            }
            break;
        case 'query':
            $queries = array($export['source']['query']);
            break;
        default:
            $queries = array();
    }

    return $queries;
}


//
// return the sql-statement to query the data to export from a table
//
function export_table_query($table, $export)
{
    global $warning;

    $columns = array();
    foreach ($GLOBALS['s_fields'][$table] as $field) {
        // only text-blobs are handled
        if ($field['type'] == 'BLOB' && $field['stype'] != 1) {
            $warning .= $WARNINGS['CAN_NOT_EXPORT_BLOBS'];
            continue;
        }

        // for sql-export include computed fields only when requested
        if ($export['format'] == 'sql' && $field['comp'] && !$export['sql']['cfields']) {
            continue;
        }

        $columns[] = $field['name'];
    }

    $quote = identifier_quote($GLOBALS['s_login']['dialect']);
    $query = 'SELECT ' . $quote . implode($quote . ',' . $quote, $columns) . $quote . ' FROM ' . $table;

    return $query;
}


//
// perform the data export in csv-format
//
function export_csv_data($export)
{
    global $ib_error, $warning, $dbhandle;

    $fields_terminator = replace_escape_sequences($export['csv']['fterm']);
    $enclose_type = $export['csv']['ftencl'];
    $fields_enclosed = replace_escape_sequences($export['csv']['fencl']);
    $escape_character = replace_escape_sequences($export['csv']['fesc']);
    $line_terminator = replace_escape_sequences($export['csv']['lterm']);
    $replace_null = $export['general']['replnull'];

    if ($export['csv']['fnamesf']) {
        $headline = '';
        foreach ($export['query']['columns'] as $column) {
            $headline .= csv_value($column, FALSE, $enclose_type, $fields_enclosed, $escape_character, $replace_null, $fields_terminator);
        }
        echo csv_line($headline, $fields_terminator, $line_terminator);
    }

    $num = fbird_num_fields($export['query']['result']);

    // build one line for the csv file from every result object
    while ($row = @fbird_fetch_row($export['query']['result'], IBASE_TEXT)) {
        $line = '';
        for ($idx = 0; $idx < $num; $idx++) {
            $value = prepare_export_value($row[$idx], $export['query']['col_types'][$idx]);
            $line .= csv_value($value, $export['query']['num_fields'][$idx], $enclose_type, $fields_enclosed, $escape_character, $replace_null, $fields_terminator);
        }

        // send line to client
        echo csv_line($line, $fields_terminator, $line_terminator);
    }
    echo $line_terminator;
}


//
// apply the export settings to a value for csv export
//
function csv_value($value, $num_field, $enclose_type, $fields_enclosed, $escape_character, $replace_null, $fields_terminator)
{

    if ($value === NULL) {
        $value = $replace_null;
    }

    $value = str_replace($fields_enclosed, $escape_character . $fields_enclosed, $value);

    $value = $enclose_type == 'all' || !$num_field
        ? $fields_enclosed . $value . $fields_enclosed
        : $value;

    $value .= $fields_terminator;

    return $value;
}


//
//  apply the export settings to a csv-line to export
//
function csv_line($line, $fields_terminator, $line_terminator)
{

    if (!empty($fields_terminator)) {
        $line = substr($line, 0, -strlen($fields_terminator));
    }
    $line .= $line_terminator;

    return $line;
}


//
// perform the data export to sql
//
function export_sql_data($export, $table)
{
    global $ib_error, $warning, $dbhandle;

    $line_ending = replace_escape_sequences($export['sql']['lineend']);
    $replace_null = $export['general']['replnull'];
    $target_table = export_replace_placeholders($export['sql']['ttable'], $GLOBALS['s_login']['database'], $table);

    $columns_list = '';
    if ($export['sql']['cnames']) {
        $quote = $export['sql']['qnames'] ? '"' : '';
        $columns_list = '(' . $quote . implode($quote . ', ' . $quote, $export['query']['columns']) . $quote . ')';
    }

    $num = fbird_num_fields($export['query']['result']);

    // build one line for the csv file from every result object
    while ($row = @fbird_fetch_row($export['query']['result'], IBASE_TEXT)) {
        $values_list = '';
        for ($idx = 0; $idx < $num; $idx++) {
            if ($row[$idx] === NULL) {
                $value = $replace_null;
            } else {
                $value = prepare_export_value($row[$idx], $export['query']['col_types'][$idx]);
            }
            $values_list .= $export['query']['num_fields'][$idx] || $row[$idx] === NULL
                ? $value . ', '
                : "'" . $value . "', ";
        }
        $values_list = '(' . substr($values_list, 0, -2) . ')';

        echo 'INSERT INTO ' . $target_table . ' ' . $columns_list . ' VALUES ' . $values_list . ';' . $line_ending;
    }
    echo $line_ending;
}


//
// return the header information for a sql export
//
function sql_export_info($export, $lf)
{
    global $s_login;

    $db_str = ($s_login['host'] == '') ? $s_login['database'] : $s_login['host'] . ':' . $s_login['database'];

    $info = '-- FirebirdWebAdmin ' . VERSION . ' - SQL-Dump' . $lf
        . '-- Dump generated: ' . strftime('%Y-%m-%d %H:%M:%S') . $lf
        . '--' . $lf
        . '-- Database: ' . $db_str . $lf;

    switch ($export['source']['option']) {
        case 'table':
            $info .= '-- Source:   data from table ' . $export['source']['table'] . $lf . $lf;
            break;
        case 'db':
            $start = 'Source:   data from tables ';
            $info .= '-- ' . $start;
            foreach ($export['source']['dbtables'] as $table) {
                $info .= $table . $lf . '-- ' . str_repeat(' ', strlen($start));
            }
            $info = substr($info, 0, -(strlen($start) + 3)) . $lf;
            break;
        case 'query':
            $info .= '-- Source:    data from query ' . $lf
                . '--' . $lf
                . '-- ' . $export['source']['query'] . $lf . $lf;
    }

    return $info;
}


//
// replace valid escape sequences in export settings
//
function replace_escape_sequences($string)
{

    return str_replace(array('\n', '\r', '\t', '\s', '\\\\'), array("\n", "\r", "\t", " ", "\\"), $string);
}


//
// build the export-filename by replacing constants and strftime-parameters
//
function export_filename($export)
{

    if ($export['source']['option'] == 'query') {
        $filename = 'query';
    } else {
        $filename = export_replace_placeholders($export['target']['filename'], $GLOBALS['s_login']['database'], $export['source']['table']);
    }

    return $filename;
}


//
//  replace the __DB__, __TABLE__ and strftime constants with valid values
//
function export_replace_placeholders($value, $db, $table)
{

    $value = str_replace(array('__DB__', '__TABLE__'), array($db, $table), $value);
    $value = strftime($value);

    return $value;
}


//
// datatype-specific processing on the export values
//
function prepare_export_value($value, $col_type)
{

    switch ($col_type) {
        case 'CHAR':
            $value = rtrim($value);
            break;
    }

    return $value;
}


//
// return the form elements for the configuaration options of the selected export format
//
function export_format_options_table($export)
{

    $func = $export['format'] . '_format_options_table';
    if (function_exists($func)) {
        return $func($export);
    }
    return '';
}


//
// return the form elements for the configuaration options for csv-export
//
function csv_format_options_table($export)
{
    global $dt_strings;

    return '
      <table class="table table-bordered">
        <tr>
          <th align="left" colspan="2">' . $dt_strings['CsvOpts'] . '</th>
        </tr>
        <tr>
          <td>
            <table class="table table-bordered">
              <tr>
                <td>
                  ' . $dt_strings['FTerm'] . '
                </td>
                <td>
                  ' . get_textfield('dt_export_csv_fterm', 2, 5, $export['csv']['fterm']) . '
                </td>
              </tr>
              <tr>
                <td>
                  ' . $dt_strings['FEncl'] . '
                </td>
                <td>
                  ' . get_textfield('dt_export_csv_fencl', 2, 5, $export['csv']['fencl']) . '
                </td>
              </tr>
              <tr>
                <td>
                  ' . $dt_strings['FTEncl'] . '
                </td>
                <td>
                  ' . get_indexed_selectlist('dt_export_csv_ftencl', array('all' => $dt_strings['All'], 'nonnum' => $dt_strings['NonNum']), $export['csv']['ftencl']) . '
                </td>
              </tr>
              <tr>
                <td>
                  ' . $dt_strings['FEsc'] . '
                </td>
                <td>
                  ' . get_textfield('dt_export_csv_fesc', 2, 5, $export['csv']['fesc']) . '
                </td>
              </tr>
              <tr>
                <td>
                  ' . $dt_strings['LTerm'] . '
                </td>
                <td>
                  ' . get_textfield('dt_export_csv_lterm', 2, 5, $export['csv']['lterm']) . '
                </td>
              </tr>
              <tr>
                <td>
                  ' . $dt_strings['FNamesF'] . '
                </td>
                <td>
                  ' . get_checkbox('dt_export_csv_fnamesf', '1', $export['csv']['fnamesf']) . '
                </td>
              </tr>
          </table>
          </td>
        </tr>
      </table>';
}


//
// return the form elements for the configuaration options for sql-export
//
function sql_format_options_table($export)
{
    global $dt_strings;

    return '
      <table class="table table-bordered">
        <tr>
          <th align="left" colspan="2">' . $dt_strings['SqlOpts'] . '</th>
        </tr>
        <tr>
          <td>
            <table class="table table-bordered">
              <tr>
                <td>
                  ' . $dt_strings['SqlCNames'] . '
                </td>
                <td>
                  ' . get_checkbox('dt_export_sql_cnames', '1', $export['sql']['cnames']) . '
                </td>
              </tr>
              <tr>
                <td>
                  ' . $dt_strings['SqlQNames'] . '
                </td>
                <td>
                  ' . get_checkbox('dt_export_sql_qnames', '1', $export['sql']['qnames']) . '
                </td>
              </tr>
              <tr>
                <td>
                  ' . $dt_strings['SqlCField'] . '
                </td>
                <td>
                  ' . get_checkbox('dt_export_sql_cfields', '1', $export['sql']['cfields']) . '
                </td>
              </tr>
              <tr>
                <td>
                  ' . $dt_strings['SqlInfo'] . '
                </td>
                <td>
                  ' . get_checkbox('dt_export_sql_info', '1', $export['sql']['info']) . '
                </td>
              </tr>
              <tr>
                <td>
                  ' . $dt_strings['SqlLE'] . '
                </td>
                <td>
                  ' . get_textfield('dt_export_sql_lineend', 2, 5, $export['sql']['lineend']) . '
                </td>
              </tr>
              <tr>
                <td>
                  ' . $dt_strings['SqlTTab'] . '
                </td>
                <td>
                  ' . get_textfield('dt_export_sql_ttable', 15, 31, $export['sql']['ttable']) . '
                </td>
              </tr>
            </table>
          </td>
        </tr>
      </table>';
}


//
// return the form elements for the configuaration options for external table-export
//
function ext_format_options_table($export)
{
    global $dt_strings;

    return '
      <table class="table table-bordered">
        <tr>
          <th align="left" colspan="2">' . $dt_strings['ExtOpts'] . '</th>
        </tr>
        <tr>
          <td>
            <table class="table table-bordered">
              <tr>
                <td>
                  foo
                </td>
              </tr>
            </table>
          </td>
        </tr>
      </table>';
}


?>
