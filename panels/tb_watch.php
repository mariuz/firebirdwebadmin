<?php
// File           tb_watch.php / FirebirdWebAdmin
// Purpose        html sequence for the table-watch-panel in sql.php and data.php
// Author         Lutz Brueckner <irie@gmx.de>
// Copyright      (c) 2000, 2001, 2002, 2003, 2004, 2005 by Lutz Brueckner,
//                published under the terms of the GNU General Public Licence v.2,
//                see file LICENCE for details
// Created        <00/11/29 16:12:21 lb>
//
// $Id: tb_watch.php,v 1.19 2005/02/24 20:34:12 lbrueckner Exp $


if (!isset($tb_watch_cfg_flag)  &&  $s_connected):

?>
<form method="post" action="<?php echo url_session($_SERVER['PHP_SELF']); ?>" name="tb_watch_form">
<table>
  <tr>
    <td colspan="1">
<?php

    echo '<b>' . $sql_strings['SelTable'] . "</b><br>\n"
       . get_table_selectlist('tb_watch_table', array('select'), $s_wt['table'], TRUE);
?>
      <input type="submit" name="tb_watch_select" value="<?php echo $button_strings['Select']; ?>">
    </td>
<?php

    if (isset($s_wt['table'])  &&  $s_wt['table'] != '') {
        echo "<td width=\"100\">&nbsp;</td>\n";
        $url = url_session($_SERVER['PHP_SELF'].'?wcfg=true');
        echo '<td><a href="'.$url.'" class="act">['.$sql_strings['Config']."]</a></td>\n";
    }
?>
  </tr>
</table> 
<table>
  <tr>
    <td>
      <?php
           display_table($s_wt);
      ?>
    </td>
  </tr>
</table>
</form>
<div id="fk" class="fk"></div>
<?php

//
// Configuration panel
//
elseif ($s_connected):

?>
<form method="post" action="<?php echo url_session($_SERVER['PHP_SELF']); ?>" name="tb_watch_form">
<table>
  <tr>
    <td colspan="5">
      <?php watchtable_column_options($s_wt['table'],
                                      $s_wt['columns'],
                                      $s_wt['order'],
                                      $s_wt['blob_links'],
                                      $s_wt['blob_as']
                                      );
       ?>
    </td>
  <tr>
</table>
<table>
  <tr>
    <th><?php echo $sql_strings['Rows']; ?></th>
    <th><?php echo $sql_strings['Start']; ?></th>
    <th><?php echo $sql_strings['Dir']; ?></th>
    <th><?php echo $sql_strings['ELinks']; ?></th>
    <th><?php echo $sql_strings['DLinks']; ?></th>
  </tr>
  <tr>
    <td align="center">
      <input type="text" size="4" maxlength="4" name="tb_watch_rows" value="<?php echo $s_wt['rows']; ?>">
    </td>
    <td align="center">
      <input type="text" size="8" maxlength="8" name="tb_watch_start" value="<?php echo $s_wt['start']; ?>">
    </td>
    <td align="center">
       <?php echo get_selectlist('tb_watch_direction',
                                 array($sql_strings['Asc'], $sql_strings['Desc']),
                                 $s_wt['direction'] == 'ASC' ? $sql_strings['Asc'] : $sql_strings['Desc']);
       ?>
    </td>
    <td align="center">
       <?php echo get_yesno_selectlist('tb_watch_edit', $s_wt['edit'] ? 'Yes' : 'No'); ?>
    </td>
    <td align="center">
       <?php echo get_yesno_selectlist('tb_watch_del', $s_wt['delete'] ? 'Yes' : 'No'); ?>
    </td>
  </tr>
</table>
<table>
  <tr>
    <th><?php echo $sql_strings['TBInline']; ?></th>
    <th><?php echo $sql_strings['TBChars']; ?></th>
  <tr>
    <td align="center">
       <?php echo get_yesno_selectlist('tb_watch_tblob_inline', $s_wt['delete'] ? 'Yes' : 'No'); ?>
    </td>
    <td align="center">
      <input type="text" size="4" maxlength="4" name="tb_watch_tblob_chars" value="<?php echo $s_wt['tblob_chars']; ?>">
    </td>
  </tr>
</table>
<table>
  <tr>
    <th colspan="5" align="left"><?php echo $sql_strings['Restrict']; ?></th>
  </tr>
  <tr>
    <td colspan="5">
      <input type="text" size="60" maxlength="256" name="tb_watch_condition" value="<?php echo $s_wt['condition']; ?>">
    <td>
  </tr>
</table>
<input type="submit" name="tb_watch_cfg_doit" value="<?php echo $button_strings['Ready']; ?>" class="bgrp">
<input type="submit" name="tb_watch_cfg_cancel" value="<?php echo $button_strings['Cancel']; ?>" class="bgrp">
</form>
<?php

endif;

?>
