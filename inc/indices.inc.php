<?php
// File           indices.inc.php / FirebirdWebAdmin
// Purpose        functions working with indices, included from accessories.php
// Author         Lutz Brueckner <irie@gmx.de>
// Copyright      (c) 2000, 2001, 2002, 2003, 2004, 2005 by Lutz Brueckner,
//                published under the terms of the GNU General Public Licence v.2,
//                see file LICENCE for details
// Created        <01/10/10 20:52:58 lb>
//
// $Id: indices.inc.php,v 1.16 2005/08/27 16:38:31 lbrueckner Exp $


//
// create an index from the values in the index form
//
function create_index() {
    global $dbhandle, $indices, $ib_error, $lsql;

    $lsql = 'CREATE ';
    if (isset($_POST['def_index_uniq'])) {
        $lsql .= 'UNIQUE ';
    }
    $lsql .= $_POST['def_index_dir'].' INDEX '.$_POST['def_index_name'].' ';
    $lsql .= 'ON '.$_POST['def_index_table'].' ('.$_POST['def_index_segs'].')';

    if (DEBUG) add_debug('lsql', __FILE__, __LINE__);

    if (!@fbird_query($dbhandle, $lsql)) {
        $ib_error = fbird_errmsg();
    }

    if ((!isset($_POST['def_index_activ']) ||  $_POST['def_index_activ'] == FALSE)  &&
        (empty($ib_error))) {

        alter_index($_POST['def_index_name'], 'INACTIVE');
    }

    if (empty($ib_error)) {
        $iname = strtoupper($_POST['def_index_name']);
        $indices[$iname]['table'] = $_POST['def_index_table'];
        $indices[$iname]['dir']   = $_POST['def_index_dir'];
        $indices[$iname]['uniq']  = (isset($_POST['def_index_uniq']))  ? TRUE : FALSE;
        $indices[$iname]['active']= (isset($_POST['def_index_activ'])) ? TRUE : FALSE;
        $segs = explode(',', strtoupper($_POST['def_index_segs']));
        $indices[$iname]['seg'] = array();
        foreach ($segs as $seg) {
            $indices[$iname]['seg'][] = $seg;
        } 
        return TRUE;
    }
    else {
       return FALSE;
    }
}


//
// try to modify the index $iname by recreate the index with the new settings
//
function modify_index($iname) {
    global $dbhandle, $indices, $ib_error, $lsql;

    // alter the active/inactive status if the change was selected
    if (isset($_POST['def_index_activ'])  && $indices[$iname]['active'] == FALSE) {
        if (alter_index($iname, 'ACTIVE')) {
            $indices[$iname]['active'] = TRUE;
        } 
        else {
            return FALSE;
        }
    }
    elseif (!isset($_POST['def_index_activ'])  && $indices[$iname]['active'] == TRUE) {
        if (alter_index($iname, 'INACTIVE')) {
            $indices[$iname]['active'] = FALSE;
        }
        else {
            return FALSE;
        }
    }

    // check if the index properties are modified
    $uniq_flag = (isset($_POST['def_index_uniq']))  ? TRUE : FALSE;
    $acti_flag = (isset($_POST['def_index_activ'])) ? TRUE : FALSE;
    if ($indices[$iname]['table'] != $_POST['def_index_table']
    ||  $iname != $_POST['def_index_name']
    ||  $indices[$iname]['dir']   != $_POST['def_index_dir']
    ||  $indices[$iname]['uniq']  != $uniq_flag 
    ||  $indices[$iname]['active']!= $acti_flag
    ||  implode(',', $indices[$iname]['seg']) !=  strtoupper($_POST['def_index_segs'])) {

        // drop the old index
        $lsql = 'DROP INDEX '.$iname;
        if (DEBUG) add_debug('lsql', __FILE__, __LINE__);
        if (!@fbird_query($dbhandle, $lsql)) {

            $ib_error = fbird_errmsg();
            return FALSE;
        }

        // try to recreate with the new properties        
        if (create_index()) {

            return TRUE;
        }

        // try to recreate the old one
        else {
            $lsql = 'CREATE ';
            if (isset($indices[$iname]['uniq'])) {
                $lsql .= 'UNIQUE ';
            }
            $lsql .= $indices[$iname]['dir']." INDEX $iname ON "
                    .$indices[$iname]['table'].' ('.implode(',', $indices[$iname]['seg']).')';
            if (DEBUG) add_debug('lsql', __FILE__, __LINE__);
            if (!@fbird_query($trans, $lsql)) {
                fbird_rollback($trans);
                $ib_error = fbird_errmsg();
            }

            return FALSE;
        }
    }

    return TRUE;
}


//
// set the index $iname active or inactive
//
function alter_index($iname, $state) {
    global $dbhandle, $ib_error, $lsql;

    $lsql = "ALTER INDEX $iname $state";
    if (DEBUG) add_debug('lsql', __FILE__, __LINE__);
    if (!@fbird_query($dbhandle, $lsql)) {

        $ib_error = fbird_errmsg();
        return FALSE;
    }

    return TRUE;
}


//
// drop the index $name off the database
//
function drop_index($name) {
    global $indices, $dbhandle, $ib_error, $lsql;

    $lsql = 'DROP INDEX '.$name;
    if (DEBUG) add_debug('lsql', __FILE__, __LINE__);
    if (!@fbird_query($dbhandle, $lsql)) {

        $ib_error = fbird_errmsg();
        return TRUE;
    }
    else {

        unset($indices[$name]);
        return TRUE;
    }
}


//
// return an array with the properties of the defined indeces 
//
function get_indices($order, $dir) {
    global $dbhandle;

    $order_field = ($order == 'name') ? 'I.RDB$INDEX_NAME' : 'I.RDB$RELATION_NAME';

    $sql = 'SELECT I.RDB$INDEX_NAME AS INAME, '
                 .'I.RDB$RELATION_NAME AS RNAME, '
                 .'I.RDB$UNIQUE_FLAG AS UFLAG, '
                 .'I.RDB$INDEX_INACTIVE AS IFLAG, '
                 .'I.RDB$INDEX_TYPE AS ITYPE, '
                 .'S.RDB$FIELD_NAME AS FNAME, '
                 .'S.RDB$FIELD_POSITION AS POS '
            .'FROM RDB$INDICES I '
            .'JOIN RDB$INDEX_SEGMENTS S '
              .'ON S.RDB$INDEX_NAME=I.RDB$INDEX_NAME '
           .'WHERE (I.RDB$SYSTEM_FLAG IS NULL  OR  I.RDB$SYSTEM_FLAG=0)'
             .'AND I.RDB$FOREIGN_KEY IS NULL '
             ."AND I.RDB\$INDEX_NAME NOT STARTING WITH 'RDB\$' "
           .'ORDER BY '.$order_field.' '.$dir;
    $trans = fbird_trans(TRANS_READ, $dbhandle);
    $res = fbird_query($trans, $sql) or ib_error();

    $indices = array();
    while ($obj = fbird_fetch_object($res)) {
        if (!isset($indices[$obj->INAME])) {
            $iname = trim($obj->INAME);
            $indices[$iname]['table'] = trim($obj->RNAME);
            $indices[$iname]['dir']   = (isset($obj->ITYPE)  &&  $obj->ITYPE == 1) ? 'DESC' : 'ASC';
            $indices[$iname]['uniq']  = (isset($obj->UFLAG)) ? TRUE  : FALSE;
            $indices[$iname]['active']= (isset($obj->IFLAG)  && $obj->IFLAG == 1) ? FALSE : TRUE;
            $indices[$iname]['pos']   = $obj->POS;
        }
        $indices[$iname]['seg'][$obj->POS] = trim($obj->FNAME);
    }
    fbird_commit($trans);

    return $indices;
}


//
// output a html-table with a form to define/modify an index 
//
// Variables:  $indexname  name of the index to modify
//             $title      headline-string for the table
function index_definition($indexname, $title) {
    global $indices, $acc_strings;

    if ($indexname != NULL  &&  !isset($_POST['acc_modind_doit'])) {
        $name   = $indexname;
        $table  = $indices[$indexname]['table'];
        $dir    = $indices[$indexname]['dir'];
        $uniq   = $indices[$indexname]['uniq'];
        $active = $indices[$indexname]['active'];
        $segs   = implode(',', $indices[$indexname]['seg']);
    }
    else {
        $name   = (isset($_POST['def_index_name']))  ? $_POST['def_index_name']  : '';
        $table  = (isset($_POST['def_index_table'])) ? $_POST['def_index_table'] : '';
        $dir    = (isset($_POST['def_index_dir']))   ? $_POST['def_index_dir']   : 'ASC';
        $uniq   = (isset($_POST['def_index_uniq']))  ? TRUE : FALSE;
        $active = (isset($_POST['def_index_activ'])) ? TRUE : FALSE;
        $segs   = (isset($_POST['def_index_segs']))  ? $_POST['def_index_segs']  : '';
    }

?>
<table>
  <tr>
    <th colspan="6" align="left"><?php echo $title; ?></th>
  </tr>
  <tr>
    <td valign="top"><b><?php echo $acc_strings['Name']; ?></b><br>
        <input type="text" size="20" maxlength="31" name="def_index_name" value="<?php echo $name; ?>">
    </td>
    <td valign="top"><b><?php echo $acc_strings['Table']; ?></b><br>
      <?php echo get_table_selectlist('def_index_table', array('noviews', 'owner'), $table, TRUE); ?>
    </td>
    <td align="center" valign="top"><b><?php echo $acc_strings['Active']; ?></b><br>
      <input type="checkbox" name="def_index_activ" <?php if ($active) echo 'checked'; ?>> 
    </td>
    <td align="center" valign="top"><b><?php echo $acc_strings['Unique']; ?></b><br>
      <input type="checkbox" name="def_index_uniq" <?php if ($uniq) echo 'checked'; ?>> 
    </td>
    <td align="center" valign="top"><b><?php echo $acc_strings['Sort']; ?></b><br>
      <select name="def_index_dir">
        <option<?php if ($dir == 'ASC') echo ' selected'; ?>> ASC
        <option<?php if ($dir == 'DESC') echo ' selected'; ?>> DESC
      </select>
    </td>
    <td valign="top"><b><?php echo $acc_strings['ColExpl']; ?></b><br>
        <input type="text" size="30" maxlength="128" name="def_index_segs" value="<?php echo $segs; ?>">
    </td> 
  </tr>
</table>
<?php

}


//
// return the html displaying the index details in a table
//
function get_index_table($indices, $order, $dir) {
    global $acc_strings;

    $name_url  = url_session($_SERVER['PHP_SELF'].'?idxorder=1&order=name');
    $table_url = url_session($_SERVER['PHP_SELF'].'?idxorder=1&order=table');

    if ($order == 'name') {
        $name_title = ($dir == 'ASC') ? '*&nbsp;'.$acc_strings['Name'] : $acc_strings['Name'].'&nbsp;*';
        $table_title = $acc_strings['Table'];
    }
    else {
        $name_title  = $acc_strings['Name'];
        $table_title = ($dir == 'ASC') ? '*&nbsp;'.$acc_strings['Table'] : $acc_strings['Table'].'&nbsp;*';
    }

    $html = "<table class=\"table table-bordered\">\n"
           ."  <tr align=\"left\">\n"
           .'    <th class="detail"><a href="'.$name_url.'">'.$name_title."</a></th>\n"
           .'    <th class="detail">'.$acc_strings['Active']."</th>\n"
           .'    <th class="detail">'.$acc_strings['Unique']."</th>\n"
           .'    <th class="detail">'.$acc_strings['Sort']."</th>\n"
           .'    <th class="detail"><a href="'.$table_url.'">'.$table_title."</a></th>\n"
           .'    <th class="detail">'.$acc_strings['Columns']."</th>\n"
           ."  </tr>\n";

    foreach($indices as $iname => $index) {
        $uniq_flag   = ($index['uniq'] == TRUE)   ? $acc_strings['Yes'] : $acc_strings['No'];
        $active_flag = ($index['active'] == TRUE) ? $acc_strings['Yes'] : $acc_strings['No'];
        $segs = implode(',&nbsp;', $index['seg']);


        $html .= "  <tr>\n"
                .'    <td class="detail">'.$iname."</td>\n"
                .'    <td align="center" class="detail">'.$active_flag."</td>\n"
                .'    <td align="center" class="detail">'.$uniq_flag."</td>\n"
                .'    <td class="detail">'.$index['dir']."</td>\n"
                .'    <td class="detail">'.$index['table']."</td>\n"
                .'    <td class="detail">'.$segs."</td>\n"
                ."  </tr>\n";
    }

    $html .= "</table>\n";

    return $html;
}

?>
