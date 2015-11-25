<?php
// File           views.inc.php / FirebirdWebAdmin
// Purpose        functions working with views, included from accessories.php
// Author         Lutz Brueckner <irie@gmx.de>
// Copyright      (c) 2000, 2001, 2002, 2003, 2004, 2005 by Lutz Brueckner,
//                published under the terms of the GNU General Public Licence v.2,
//                see file LICENCE for details

//
// create a view from the values in viewdefs array
//
function create_view($viewdefs)
{
    global $dbhandle;
    global $ib_error, $lsql;

    $lsql = $viewdefs['source'];
    if ($viewdefs['check'] == 'yes') {
        $lsql .= "\nWITH CHECK OPTION";
    }

    if (DEBUG) {
        add_debug('lsql', __FILE__, __LINE__);
    }

    if (!@fbird_query($dbhandle, $lsql)) {
        $ib_error = fbird_errmsg();
    }

    return (empty($ib_error)) ? get_viewname($viewdefs['source']) : false;
}

//
// drop the view $name off the database
//
function drop_view($name)
{
    global $dbhandle, $s_tables;
    global $ib_error, $lsql;

    $lsql = 'DROP VIEW '.$name;
    if (DEBUG) {
        add_debug('lsql', __FILE__, __LINE__);
    }
    if (!@fbird_query($dbhandle, $lsql)) {
        $ib_error = fbird_errmsg();

        return false;
    } else {
        unset($s_tables[$name]);

        return true;
    }
}

//
// return the html for the form elements to create or alter a view
//
function view_definition($title, $viewdefs)
{
    global $acc_strings, $s_cust;

    $html = <<<EOT
<table class="table table-hover">
  <tr>
    <th align="left">$title</th>
  </tr>
  <tr>
    <td>
      <b>${acc_strings['Source']}</b><br>
      <textarea name="def_view_source" rows="%1\$d" cols="%2\$d" wrap="virtual">${viewdefs['source']}</textarea>
    </td>
  </tr>
  <tr>
    <td>
      <input type="checkbox" name="def_view_check" value="yes"%3\$s>${acc_strings['CheckOpt']}
    </td>
  </tr>
</table>

EOT;

    return sprintf($html,
        $s_cust['textarea']['rows'],
        $s_cust['textarea']['cols'],
        ($viewdefs['check'] == 'yes' ? ' checked' : '')
    );
}

//
// find the name of a view in its source code
//
function get_viewname($viewsource)
{
    $chunks = preg_split("/[\s]+/", $viewsource, 4);

    return $chunks[2];
}

//
// deliver the html for an opened view on the views panel
//
function get_opened_view($name, $title, $url)
{
    global $dbhandle, $s_fields, $tb_strings, $acc_strings, $ptitle_strings;

    $source = get_view_source($name);

    $html = <<<EOT
        <nobr>
          <a href="$url" class="dtitle"><span class="glyphicon glyphicon-chevron-up" aria-hidden="true" alt="${ptitle_strings['Close']}" title="${ptitle_strings['Close']}"></span> $title</a>
        </nobr>
        <nobr>
        <table class="table table-hover">
          <tr>
            <td width="26">
            </td>
            <td valign="top">
              <table class="table table-bordered">
EOT;

    $cols = array('Name', 'Type', 'Length', 'Prec', 'Scale', 'Charset', 'Collate');
    $html .= "              <tr align=\"left\">\n";
    foreach ($cols as $idx) {
        $html .= '                <th class="detail"><nobr>'.$tb_strings[$idx]."</nobr></th>\n";
    }
    $html .= "              </tr>\n";

    foreach ($s_fields[$name] as $field) {
        $size_str = ($field['type'] == 'VARCHAR' || $field['type'] == 'CHARACTER') ? $field['size'] : '&nbsp;';
        $prec_str = (isset($field['prec'])) ? $field['prec'] : '&nbsp;';
        $scale_str = (isset($field['scale'])) ? $field['scale'] : '&nbsp;';
        $char_str = (isset($field['charset'])) ? $field['charset'] : '&nbsp;';
        $coll_str = (isset($field['collate'])) ? $field['collate'] : '&nbsp;';

        $html .= "              <tr>
                <td class=\"detail\">${field['name']}</td>
	        <td class=\"detail\">${field['type']}</td>
	        <td align=\"right\" class=\"detail\">$size_str</td>
	        <td align=\"right\" class=\"detail\">$prec_str</td>
	        <td align=\"right\" class=\"detail\">$scale_str</td>
    	        <td class=\"detail\">$char_str</td>
                <td class=\"detail\">$coll_str</td>
              </tr>\n";
    }
    $html .= "            </table>\n          </td>\n";

    $html .= "         <td>&nbsp;</td>\n"
        ."          <td valign=\"top\">\n"
        ."            <table border=\"1\" cellpadding=\"0\" cellspacing=\"0\">\n"
        ."              <tr align=\"left\">\n"
        .'                <th class="detail">'.$acc_strings['Source']."</th>\n"
        ."              </tr>\n"
        ."              <tr>\n"
        .'                <td class="detail"><pre>'.$source."</pre></td>\n"
        ."              </tr>\n"
        ."            </table>\n"
        ."          </tr>\n"
        ."        </td>\n"
        ."      </table>\n"
        ."    </nobr>\n";

    return $html;
}

//
// return the sourcecode of the definition for $view
//
function get_view_source($name)
{
    global $dbhandle;

    $vsource = '';
    $sql = 'SELECT R.RDB$VIEW_SOURCE VSOURCE'
        .' FROM RDB$RELATIONS R'
        ." WHERE R.RDB\$RELATION_NAME='".$name."'";
    $res = fbird_query($dbhandle, $sql) or ib_error(__FILE__, __LINE__, $sql);
    $obj = fbird_fetch_object($res);

    if (is_object($obj)) {
        $bid = fbird_blob_open($obj->VSOURCE);
        $arr = fbird_blob_info($obj->VSOURCE);
        // $arr[0] holds the blob length
        $vsource = trim(fbird_blob_get($bid, $arr[0]));
        fbird_blob_close($bid);
    }
    fbird_free_result($res);

    return $vsource;
}

//
// mark all views as opened or closed in $s_tables
//
function toggle_all_views($tables, $status)
{
    foreach (array_keys($tables) as $name) {
        if ($tables[$name]['is_view']) {
            $tables[$name]['status'] = $status;
        }
    }

    return $tables;
}
