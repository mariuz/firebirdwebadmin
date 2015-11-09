<?php
// File           panel_elements.inc.php / FirebirdWebAdmin
// Purpose        functions for generating html-code that is needed in various panels
// Author         Lutz Brueckner <irie@gmx.de>
// Copyright      (c) 2000, 2001, 2002, 2003, 2004, 2005 by Lutz Brueckner,
//                published under the terms of the GNU General Public Licence v.2,
//                see file LICENCE for details


// output the html for the part of a table/form where the definitions
// for a <datatype> statement can be entered; used for columns and domains
//
// Variables:     $i        index in $s_coldefs[] for this coldef
//                $title    headline string
//                $rowspan  span the name field over so much rows
//                $collate  display the collation element if TRUE
//
function get_datatype_definition($idx, $title, $rowspan = 1, $collate=FALSE) {
    global $s_coldefs, $tb_strings;

    // preselect values for the form elements
    $name_value     = isset($s_coldefs[$idx]['name'])        ? $s_coldefs[$idx]['name']         : '';
    $colpos_value   = isset($_POST['tb_modcol_pos'])? $_POST['tb_modcol_pos'] : '';
    $datatype_pre   = isset($s_coldefs[$idx]['type'])        ? $s_coldefs[$idx]['type']         : NULL;
    $size_value     = isset($s_coldefs[$idx]['size'])        ? $s_coldefs[$idx]['size']         : '';
    $charset_pre    = isset($s_coldefs[$idx]['charset'])     ? $s_coldefs[$idx]['charset']      : NULL;
    $collate_pre    = isset($s_coldefs[$idx]['collate'])     ? $s_coldefs[$idx]['collate']      : NULL;
    $prec_value     = isset($s_coldefs[$idx]['prec'])        ? $s_coldefs[$idx]['prec']         : '';
    $scale_value    = isset($s_coldefs[$idx]['scale'])       ? $s_coldefs[$idx]['scale']        : '';
    $stype_value    = isset($s_coldefs[$idx]['stype'])       ? $s_coldefs[$idx]['stype']        : '';
    $segsize_value  = isset($s_coldefs[$idx]['segsize'])     ? $s_coldefs[$idx]['segsize']      : '';

    // colspan attribute for the charset cell
    $charspan = ($collate == FALSE) ? 2 : 1;

    // javascript event-handler to adjust the collation accordingly to the selected charset
    $charset_tags = array();
    if ($collate == TRUE) {
        $form_name = get_form_name($idx);
        $charset_tags = array('onChange' => 'adjustCollation(document.'.$form_name.'.cd_def_charset'.$idx.', document.'.$form_name.'.cd_def_collate'.$idx.')');
    }

    $html = "  <tr>\n"
          . '    <th colspan="9" align="left"><b>'.$title."</b></th>\n"
          . "  </tr>\n"

          . "  <tr>\n"
          . '    <td rowspan="'.$rowspan."\" valign=\"top\" height=\"100%\">\n"
          . "      <table border=\"0\" cellpadding=\"0\" cellspacing=\"0\">\n"
          . "        <tr>\n"
          . "          <td>\n"
          . '            <b>'.$tb_strings['Name']."</b><br>\n"
          . '            '.get_textfield('cd_def_name'.$idx, 20, 31, $name_value)
          . "          </td>\n"
          . "        </tr>\n"
          . "      </table>\n";

    if ($idx === 'mod') {
        $html .= "      <table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" valign=\"bottom\" height=\"100%\">\n"
               . "        <tr>\n"
               . "          <td>\n"
               . '            <b>'.$tb_strings['NewColPos']."</b><br>\n"
               . '            '.get_textfield('tb_modcol_pos', 4, 4, $colpos_value)
               . "          </td>\n"
               . "        </tr>\n"
               . "      </table>\n";
    }

    $html .= "    </td>\n"
           ."     <td>\n"
           . '      <b>'.$tb_strings['Datatype']."</b><br>\n"
           . '      '.get_selectlist('cd_def_type'.$idx, get_datatypes(SERVER_FAMILY, SERVER_VERSION), $datatype_pre, TRUE)
           . "    </td>\n"
           . "    <td>\n"
           . '      <b>'.$tb_strings['Size']."</b><br>\n"
           . '      '.get_textfield('cd_def_size'.$idx, 5, 5, $size_value)
           . "    </td>\n"
           . '    <td colspan="'.$charspan."\">\n"
           . '      <b>'.$tb_strings['Charset']."</b><br>\n"
           . '      '.get_charset_select('cd_def_charset'.$idx, $charset_pre, TRUE, $charset_tags)
           . "    </td>\n";

    if ($collate == TRUE) {
        $html .= "    <td>\n"
               . '      <b>'.$tb_strings['Collation']."</b><br>\n"
               . '      '.get_collation_select('cd_def_collate'.$idx, $collate_pre, TRUE)
               . "    </td>\n";
    }

    $html .= "    <td align=\"center\">\n"
           . '      <b>'.$tb_strings['PrecShort']."</b><br>\n"
           . '      '.get_textfield('cd_def_prec'.$idx, 2, 2, $prec_value)
           . "    </td>\n"
           . "    <td align=\"center\">\n"
           . '      <b>'.$tb_strings['Scale']."</b><br>\n"
           . '      '.get_textfield('cd_def_scale'.$idx, 2, 2, $scale_value)
           . "    </td>\n"
           . "    <td align=\"center\">\n"
           . '      <b>'.$tb_strings['Subtype']."</b><br>\n"
           . '      '.get_textfield('cd_def_stype'.$idx, 3, 3, $stype_value)
           . "    </td>\n"
           . "    <td align=\"center\">\n"
           . '      <b>'.$tb_strings['SegSiShort']."</b><br>\n"
           . '      '.get_textfield('cd_def_segsize'.$idx, 5, 5, $segsize_value)
           . "    </td>\n"
           . "  </tr>\n";

    echo $html;
}


//
// html sequence for part of a table/form to define a <col_def> statement
//
// Variables:     $idx      index in $s_coldefs[] for this coldef
//                $title  headline string
//                $rowspan  span the name field over so much rows
//
function get_coldef_definition($idx, $title, $rowspan, $collate=FALSE) {
    global $s_coldefs, $tb_strings, $s_domains;

    $coldefs = isset($s_coldefs[$idx]) ? $s_coldefs[$idx] : array();

    $domain_names = array_keys($s_domains);
    $rowspan = !empty($domain_names) ? $rowspan -1 : $rowspan;

    // preselect values for the form elements
    $domain_pre    = isset($coldefs['domain'])  ? $coldefs['domain']  : NULL;
    $comp_value    = isset($coldefs['comp'])    ? $coldefs['comp']    : '';
    $default_value = isset($coldefs['default']) ? $coldefs['default'] : '';
    $check_value   = isset($coldefs['check'])   ? $coldefs['check']   : '';

    $ehandler_str = ' onClick="checkColConstraint('.'document.'.get_form_name($idx).", this.name, '".$idx."')";

    $html = get_datatype_definition($idx, $title, $rowspan, $collate)
          . "  <tr>\n"
          . "    <td colspan=\"4\">\n";

    if (!empty($domain_names)) {
        $html .= '      <b>'.$tb_strings['Domain']."</b><br>\n"
               . '      '.get_selectlist('cd_def_domain'.$idx, $domain_names, $domain_pre, TRUE);
    }
    else {
        $html .= "&nbsp;\n";
    }

    $html .= "    </td>\n"
           . "    <td colspan=\"2\" align=\"center\">\n"
           . '      <label><b>'.$tb_strings['NotNull']."</b><br>\n"
           . '      <input type="checkbox" name="cd_def_notnull'.$idx.'"'.$ehandler_str.'"'.(!empty($coldefs['notnull']) ? ' checked' : '').">\n</label>"
           . "    </td>\n"
           . "    <td align=\"center\">\n"
           . '      <label><b>'.$tb_strings['Unique']."</b><br>\n"
           . '      <input type="checkbox" name="cd_def_unique'.$idx.'"'.$ehandler_str.'"'.(!empty($coldefs['unique']) ? ' checked' : '').">\n</label>"
           . "    </td>\n"
           . "    <td align=\"center\">\n"
           . '      <label><b>'.$tb_strings['Primary']."</b><br>\n"
           . '      <input type="checkbox" name="cd_def_primary'.$idx.'"'.$ehandler_str.'"'.(!empty($coldefs['primary']) ? ' checked' : '').">\n</label>"
           . "    </td>\n"
           . "  </tr>\n"

           . "  <tr>\n"
           . "    <td colspan=\"2\">\n"
           . '      <b>'. $tb_strings['CompBy']."</b><br>\n"
           . '      '.get_textfield('cd_def_comp'.$idx, 27, 512, $comp_value)
           . "    </td>\n"
           . "    <td colspan=\"2\">\n"
           . '      <b>'.$tb_strings['Default']."</b><br>\n"
           . '      '.get_textfield('cd_def_default'.$idx, 27, 256, $default_value)
           . "    </td>\n"
           . "    <td colspan=\"4\">\n"
           . '      <b>'.$tb_strings['Check']."</b><br>\n"
           . '      '.get_textfield('cd_def_check'.$idx, 27, 256, $check_value)
           . "    </td>\n"
           . "  </tr>\n"

           . get_column_constraint_definition($coldefs, $idx);

    return $html;
}


//
// html for foreign key definitions and dropping column constraints
//
function get_column_constraint_definition($coldefs, $idx) {
    global $fk_actions, $tb_strings;

    $fk_name   = isset($coldefs['fk_name'])   ? $coldefs['fk_name']   : '';
    $fk_table  = isset($coldefs['fk_table'])  ? $coldefs['fk_table']  : '';
    $fk_column = isset($coldefs['fk_column']) ? $coldefs['fk_column'] : '';
    $on_update = isset($coldefs['on_update']) ? $coldefs['on_update'] : '';
    $on_delete = isset($coldefs['on_delete']) ? $coldefs['on_delete'] : '';

    $table_element = get_table_selectlist('cd_def_fk_table_'.$idx,
                                          array('no_views', 'references'),
                                          $fk_table,
                                          TRUE,
                                          array('onChange' => "requestTableColumns(selectedElement(this), 'cd_def_fk_col_".$idx."', 'fk');")
                                          );

    $drop_element = '';
    if ($idx == 'mod' &&
        ((isset($coldefs['primary'])  &&  $coldefs['primary_cols'] == 1) ||
         (isset($coldefs['unique'])   &&  $coldefs['unique_cols']  == 1) ||
         (isset($coldefs['foreign'])  &&  $coldefs['foreign_cols'] == 1)
         )
        ) {
        $checked_str = $coldefs['fk_del'] == TRUE ? ' checked' : '';
        $drop_element =  "        <tr>\n"
                        ."          <td colspan=\"7\">\n"
                        ."            <table style=\"border-bottom: 1px solid black;\" width=\"100%\">\n"
                        ."              <tr>\n";

        if (isset($coldefs['primary'])  &&  $coldefs['primary_cols'] == 1) {
            $checked_str = $coldefs['pk_del'] == TRUE ? ' checked' : '';
            $drop_element .= "                <td>\n"
                            .'                  <label><input type="checkbox" name="cd_def_pk_del_'.$idx.'"'.$checked_str.'> <b>'.$tb_strings['DropPK']."</b>\n</label>"
                            ."                </td>\n";
        }
        if (isset($coldefs['unique'])  &&  $coldefs['unique_cols'] == 1) {
            $checked_str = $coldefs['uq_del'] == TRUE ? ' checked' : '';
            $drop_element .= "                <td>\n"
                            .'                  <label><input type="checkbox" name="cd_def_uq_del_'.$idx.'"'.$checked_str.'> <b>'.$tb_strings['DropUq']."</b>\n</label>"
                            ."                </td>\n";
        }
        if (isset($coldefs['foreign'])  &&  $coldefs['foreign_cols'] == 1) {
            $checked_str = $coldefs['fk_del'] == TRUE ? ' checked' : '';
            $drop_element .= "                <td>\n"
                            .'                  <label><input type="checkbox" name="cd_def_fk_del_'.$idx.'"'.$checked_str.'> <b>'.$tb_strings['DropFK']."</b>\n</label>"
                            ."                </td>\n";
        }

        $drop_element .= "              </tr>\n"
                        ."            </table>\n"
                        ."          <td>\n"
                        ."        </tr>\n";
    }

    $html = "  <tr>\n"
           ."    <td colspan=\"8\">\n"
           ."      <table class=\"table table-bordered\">\n"
                    .$drop_element
           ."        <tr>\n"
           ."          <td colspan=\"7\">\n"
           .'            <b>'.$tb_strings['FKName']."</b><br>\n"
           .'            <input type="text" size="27" maxlength="31" name="cd_def_fk_name_'.$idx.'" value="'.$fk_name."\" class=\"form-control\">\n"
           ."          </td>\n"
           ."        </tr>\n"
           ."        <tr>\n"
           ."          <td>\n"
           .'            <b>'.$tb_strings['Table1']."</b><br>\n"
           .'            '.$table_element."\n"
           ."          </td>\n"
           ."          <td>&nbsp;&nbsp;</td>\n"
           ."          <td>\n"
           .'            <b>'.$tb_strings['Column1']."</b><br>\n"
           .'            <span id="cd_def_fk_col_'.$idx."\">\n"
           .'              <input type="text" size="20" maxlength="31" name="cd_def_fk_col_'.$idx.'" value="'.$fk_column."\" class=\"form-control\">\n"
           ."            </span>\n"
           ."          </td>\n"
           ."          <td>&nbsp;&nbsp;</td>\n"
           ."          <td>\n"
           .'            <b>'.$tb_strings['OnUpdate']."</b><br>\n"
           ."            ".get_selectlist('cd_def_ou_'.$idx, $fk_actions, $on_update, TRUE)."\n"
           ."          </td>\n"
           ."          <td>&nbsp;&nbsp;</td>\n"
           ."          <td>\n"
           .'            <b>'.$tb_strings['OnDelete']."</b><br>\n"
           ."            ".get_selectlist('cd_def_od_'.$idx, $fk_actions, $on_delete, TRUE)."\n"
           ."          </td>\n"
           ."        </tr>\n"
           ."      </table>\n"
           ."    </td>\n"
           ."  </tr>\n";

    return $html;
}


//
// find out the name of a datatype definition form
//
// Parameter: $idx    suffix, used for the form elements
//
function get_form_name($idx) {

    // yes, its ugly,
    // but I need the form name for the adjustCollation() javascript
    $idx = (string)$idx;
    switch ($idx) {
        case 'add':
            // modify table / add column
            $form_name = 'tb_modadd_form';
            break;
        case 'dom':
            // create domain
            $form_name = 'acc_domain_form';
            break;
        default:
            // create table
            $form_name = 'tb_create_col_form';
    }

    return $form_name;
}


//
// asks for SYSDBAs password
//
function sysdba_pw_textfield($caption, $explain, $pw) {

?>
<table class="table table-bordered">
<tr>
  <th align="left"><?php echo $caption; ?></th>
     <td><input type="password"  size="20" maxlength="32" name="sysdba_pw" value="<?php echo password_stars($pw); ?>">&nbsp;
        <?php echo $explain; ?>
     </td>
</tr>
</table>
<?php

}


//
// determine SYSDBAs password from the sysdba_pw_textfield()
//
function get_sysdba_pw() {

    if ($GLOBALS['s_login']['user'] == 'SYSDBA') {

        return $GLOBALS['s_login']['password'];
    }

    if (isset($_POST['sysdba_pw'])
    &&  strlen(trim($_POST['sysdba_pw'])) != 0) {
        $pw = trim($_POST['sysdba_pw']);
        if (strspn($pw, '*') != strlen($GLOBALS['s_sysdba_pw'])
        ||  strlen($GLOBALS['s_sysdba_pw']) == 0) {

            return $pw;
        }
    }

    return $GLOBALS['s_sysdba_pw'];
}


//
// return the html for a selectlist of the  FireBird character sets.
//
function get_charset_select($name, $sel=NULL, $empty=FALSE, $tags=array()) {

    $charset_names = array();
    if (!empty($GLOBALS['s_charsets'])) {
        foreach($GLOBALS['s_charsets'] as $cs) {
            $charset_names[] = $cs['name'];
        }
    }
    else {
        $charset_names = get_preset_charsets(SERVER_FAMILY, SERVER_VERSION);
    }

    return get_selectlist($name, $charset_names, $sel, $empty, $tags);
}


//
// return the html for a selectlist of the available collation orders
//
function get_collation_select($name, $sel=NULL, $empty=FALSE, $tags=array()) {

    $collation_names = array();
    foreach ($GLOBALS['s_charsets'] as $charset) {
        foreach ($charset['collations'] as $collation) {
            $collation_names[] = $collation;
        }
    }
    sort($collation_names);

    return get_selectlist($name, $collation_names, $sel, $empty, $tags);
}


//
// return the html for a selectlist for the tables of the selected database
//
function get_table_selectlist($name, $restrictions=array(), $sel=NULL, $empty=FALSE, $tags=array(), $size=1) {
    global $s_tables, $s_login;

    $rights = array('S' => 'select',
                    'I' => 'insert',
                    'U' => 'update',
                    'D' => 'delete',
                    'R' => 'reference');

    $tables = array();
    foreach($s_tables as $tablename => $tarr) {

        if (in_array('noviews', $restrictions)
        &&  $tarr['is_view'] == TRUE) {
            continue;
        }

        if (in_array('views', $restrictions)
        &&  $tarr['is_view'] == FALSE) {
            continue;
        }

        if ($s_login['user'] != 'SYSDBA') {
            if (in_array('owner', $restrictions)
            &&  $s_login['user'] != $tarr['owner']) {
                continue;
            }

            foreach ($rights as $code => $val) {
                if (in_array($val, $restrictions)
                &&  !in_array($code, $tarr['privileges'])
                &&  $s_login['user'] != $tarr['owner']) {
                    continue 2;
                }
            }
        }

        $tables[] = $tablename;
    }

    return get_selectlist($name, $tables, $sel, $empty, $tags, $size);
}


//
// output the <option> list for a selectlist for the columns
// of the table $name
//
function build_column_options($table) {
    global $s_fields;

    echo "<option>\n";
    foreach($s_fields[$table] as $field) {
        echo '<option> '.$field['name']."\n";
    }
}


//
// output the <option> list for a selectlist for the indices
// of the selected database
//
function build_index_options() {
    global $indices;

    echo "<option>\n";
    if (is_array($indices)) {
        $inames = array_keys($indices);
        foreach($inames as $name) {
            echo '<option> '.$name."\n";
        }
    }
}


//
// output the <option> list for a selectlist
// for the firebird users in security db
// of the selected database
//
function build_user_options($with_sysdba=TRUE) {
    global $users;

    echo "<option>\n";
    if (is_array($users)) {
        $unames = array_keys($users);
        if ($with_sysdba == FALSE) {
            unset($unames[array_search('SYSDBA', $unames)]);
        }
        foreach($unames as $uname) {
            echo '<option> '.$uname."\n";
        }
    }
}


//
// return the html for a selectlist
//
function get_selectlist($name, $arr, $sel=NULL, $empty=FALSE, $tags=array(), $size=1) {

    $html = get_indexed_selectlist($name, array_combine($arr, $arr), $sel, $empty, $tags, $size);

    return $html;
}


//
// return the html for a selectlist
//
function get_indexed_selectlist($name, $arr, $sel=NULL, $empty=FALSE, $tags=array(), $size=1) {

    $sel = is_array($sel) ? array_map('htmlspecialchars', $sel) : htmlspecialchars($sel);

    $html = '<select class="form-control" id="' . $name . '" name="' . $name . '" size="' . $size . '"' . get_tags_string($tags) . ">\n";
    if ($empty == TRUE) {
        $html .= "<option />\n";
    }

    foreach ($arr as $idx => $opt) {
        $opt = htmlspecialchars($opt);
        $idx = htmlspecialchars($idx);
        $html .= '<option value="'.$idx.'"';
        if ((is_array($sel)  &&  in_array($idx, $sel))  ||  (is_string($sel)  &&  $idx == $sel)) {
            $html .= ' selected';
        }
        $html .= ">$opt</option>\n";
    }
    $html .= "</select>\n";

    return $html;
}


//
// return the html for a selectlist with 'Yes' and 'No' as options
//
function get_yesno_selectlist($name, $sel=NULL, $empty=FALSE, $tags=array()) {
    global $sql_strings;

    $arr = array('Yes' => $sql_strings['Yes'],
                 'No'  => $sql_strings['No']);

    $html = get_indexed_selectlist($name, $arr, $sel, $empty, $tags);

    return $html;
}


//
// output a form textfield
//
function get_textfield($name, $size, $maxlength=NULL, $value=NULL, $type='text', $tags=array()) {

    $html = '<input class="form-control" type="' . $type . '" id="' . $name . '" name="' . $name . '" size="' . $size . '"';
    if ($maxlength !== NULL) {
        $html .= ' maxlength="' . $maxlength . '"';
    }
    if ($value !== NULL) {
        $html .= ' value="' . htmlspecialchars($value) . '"';
    }
    $html .= get_tags_string($tags) . ">\n";

    return $html;
}


//
// return the html for hidden field
//
function hidden_field($name, $value) {

    $html = '<input type="hidden" name="' . $name . '" value="' . htmlspecialchars($value) . "\">\n";

    return $html;
}


//
// return the html for a checkbox element
//
function get_checkbox($name, $value, $checked, $tags=array()) {

    $checked_str = $checked ?  ' checked' : '';
    $html = '<input type="checkbox" name="' . $name . '" id="' . $name . '" value="' . htmlspecialchars($value) . '"' . $checked_str . get_tags_string($tags) . '>';

    return $html;
}

//
// build a html tags string from an array using the indices as names and the values as values
//
function get_tags_string($tags) {

    $tags_str = '';
    foreach ($tags as $tag => $val) {
        $tags_str .= sprintf(' %s="%s"', $tag, $val);
    }

    return $tags_str;
}


//
// return the html for a closed detail
//
function get_closed_detail($title, $url, $curl='', $cdiv='') {
    global $ptitle_strings;

    $html= '<a href="'.$url.'" title="'.$ptitle_strings['Open']."\">"
         . '  <span class="glyphicon glyphicon-chevron-down" aria-hidden="true" alt="'.$ptitle_strings['Open'].'" title="'.$ptitle_strings['Open'].'" ></span> '.$title."</a> ";

    if (!empty($curl)) {
        $html .= '<a href="'.$curl.'" class="act" title="Edit comment">[C]</a>'."\n"
               . '<div id="'.$cdiv."\" class=\"cmt\">\n"
               . "</div>\n";
    }

    return $html;
}

//
// build the url for a link to open/close a detail
//
function fold_detail_url($type, $status, $name, $title) {

    if ($status == 'close') {
        $url = sprintf("javascript:requestDetail('%s', '%s', '%s')", $type, $name, $title);
    }
    else {
        $url = sprintf("javascript:closeDetail('%s', '%s_%s', '%s', '%s')", $type, detail_div_prefix($type), $name, $name, $title);
    }

    return $url;
}

function detail_div_prefix($type) {

        $div_prefixes = array('table'     => 't',
                              'view'      => 'v',
                              'trigger'   => 'r',
                              'procedure' => 'p');

        return $div_prefixes[$type];
}


//
// deliver the html for an opened table on the tb_show panel
//
function get_opened_table($name, $title, $url, $curl='', $cdiv='') {
    global $s_fields, $tb_strings, $ptitle_strings;

    $html = <<<EOT
          <a href="$url" class="dtitlex" title="${ptitle_strings['Close']}"><span class="glyphicon glyphicon-chevron-up" aria-hidden="true" alt="${ptitle_strings['Close']}" title="${ptitle_strings['Close']}" ></span> $title</a>
          <a href="$curl" class="act" title="Edit table comment">[C]</a>
          <div id="$cdiv" class="cmt">
          </div>
        <table class="table">
          <tr>
            <td width="26">
            </td>
            <td>
              <table class="table table-bordered table-hover table-condensed margin-bottom-0px">

EOT;
    $cols = array('Name', 'Type', 'Charset', 'Collate', 'Computed', 'Default', 'NotNull', 'Check', 'Unique', 'Primary', 'Foreign');
    $html .= " <thead><tr>\n";
    foreach ($cols as $idx) {
        $html .= ' <th>'.$tb_strings[$idx]."</th>\n";
    }
    $html .= " </tr></thead>\n";

    foreach($s_fields[$name] as $field) {
        $type_str = isset($field['domain']) ? $field['type'] : get_type_string($field);
        $type_str .=isset($field['lower_bound']) ? '['.$field['lower_bound'].':'.$field['upper_bound'].']' : '';
        $char_str = isset($field['charset']) ? $field['charset']  : '&nbsp;';
        $coll_str = isset($field['collate']) ? $field['collate']  : '&nbsp;';
        $comp_str = table_column_detail_string(ifsetor($field['comp']), ifsetor($field['csource']), $GLOBALS['s_tables_comp']);
        $def_str  = table_column_detail_string(ifsetor($field['default']), ifsetor($field['dsource']), $GLOBALS['s_tables_def']);
        $nn_str   = isset($field['notnull']) ? $tb_strings['Yes'] : '&nbsp;';
        $check_str= isset($field['check'])   ? $tb_strings['Yes'] : '&nbsp;';
        $uniq_str = table_detail_constraint_string(ifsetor($field['unique']), $GLOBALS['s_tables_cnames']);
        $prim_str = table_detail_constraint_string(ifsetor($field['primary']), $GLOBALS['s_tables_cnames']);
        $fk_str   = table_detail_constraint_string(ifsetor($field['foreign']), $GLOBALS['s_tables_cnames']);

        $html .= " <tr>
                  <td>${field['name']}</td>
	          <td>$type_str</td>
    	          <td>$char_str</td>
                  <td align=\"right\" >$coll_str</td>
                  <td align=\"center\">$comp_str</td>
                  <td align=\"center\">$def_str</td>
                  <td align=\"center\">$nn_str</td>
                  <td align=\"center\">$check_str</td>
                  <td align=\"center\">$uniq_str</td>
                  <td align=\"center\">$prim_str</td>
                  <td align=\"center\">$fk_str</td>
                </tr>\n";
    }

    $html .= "              </table>\n"
            ."            </td>\n"
            ."          </tr>\n"
            ."        </table>\n";

    return $html;
}

function table_detail_constraint_string($cname, $shownames) {

    $str = '';
    if (isset($cname)) {
        if ($shownames == TRUE) {
            $str = $cname;
        }
        else {
            $str = $GLOBALS['tb_strings']['Yes'];
        }
    }
    else {
        $str = '&nbsp;';
    }

    return $str;
}

function table_column_detail_string($haveit, $source, $showit) {

    $str = '';
    if ($haveit == TRUE) {
        if ($showit == TRUE  &&  !empty($source)) {
            $str = $source;
        }
        else {
            $str = $GLOBALS['tb_strings']['Yes'];
        }
    }
    else {
        $str = '&nbsp;';
    }

    return $str;
}


//
// return the html for a closed panel
//
function get_closed_panel($title, $nr) {
    global $ptitle_strings;

    $fold_url = url_session('toggle_fold_panel.php?p='.$nr.'&d=open');

    return "<table class='table table-hover' width=\"100%\" cellpadding=\"5\" cellspacing=\"0\" border=\"0\">\n"
           ."  <tr class=\"panel\">\n"
           ."    <td width=\"25\" align=\"center\">\n"
           .'      '.sprintf('<a href="%1$s"><span class="glyphicon glyphicon-chevron-down" aria-hidden="true" alt="%2$s" title="%2$s"></span></a>'."\n", $fold_url, $ptitle_strings['Open'])
           .'    <td width="100%"><a class="ptitle" href="'.$fold_url.'">'.$title."</a></td>\n"
           ."    <td width=\"65\">\n"
           ."    </td>\n"
           ."  </tr>\n"
           ."</table>\n";
}


//
// return the html for an open panel
//
function get_open_panel_start($title, $nr) {
    global $ptitle_strings;

    $fold_url   = sprintf("javascript:requestClosedPanel('%d')", $nr);

    return '<table width="100%" class="table">'."\n"
         . "  <tr class=\"panel\">\n"
         . '    <td rowspan="2" width="25" align="center" valign="top">'."\n"
         . '      ' . sprintf('<a href="%1$s"><span class="glyphicon glyphicon-chevron-up" aria-hidden="true" alt="%2$s" title="%2$s"></span></a>'."\n", $fold_url, $ptitle_strings['Close'])."\n"
         . "    </td>\n"
         . '    <td width="100%"><a class="ptitle" href="' . $fold_url . '">' . $title . "</a></td>\n"
         . "    <td>\n"
         . "    </td>\n"
         . "  </tr>\n"
         . "  <tr>\n"
         . "    <td colspan=\"2\">\n";
}

function get_open_panel_end() {

    return "    </td>\n"
         . "  </tr>\n"
         . "</table>\n";
}

 ?>
