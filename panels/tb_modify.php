<?php
// File           tb_modify.php / FirebirdWebAdmin
// Purpose        html sequence for the tb_modify-panel in table.php
// Author         Lutz Brueckner <irie@gmx.de>
// Copyright      (c) 2000, 2001, 2002, 2003, 2004 by Lutz Brueckner,
//                published under the terms of the GNU General Public Licence v.2,
//                see file LICENCE for details
// Created        <00/09/13 16:52:51 lb>
//
// $Id: tb_modify.php,v 1.18 2004/11/09 17:16:16 lbrueckner Exp $


if (isset($s_confirmations['column'])):
    $subject = 'column';
    include('./panels/confirm.php');

elseif ($s_connected  &&  $s_modify_name == ''):

?>
<form method="post" action="<?php echo url_session($_SERVER['PHP_SELF']); ?>" name="tb_modify_form">
<table cellpadding="3" cellspacing="0">
  <tr>
    <td colspan="2"><b><?php echo $tb_strings['SelTbMod']; ?></b><br>
      <?php echo get_table_selectlist('tb_modify_name', array('owner', 'noviews'), NULL, TRUE) ?>
    </td>
    <td valign="bottom">
      <input type="submit" name="tb_modify_doit" value="<?php echo $button_strings['Modify']; ?>">
    </td>
  </tr>
</table>
</form>
<?php

elseif (isset($col_add_flag)):
    js_checkColConstraint();
?>
<form method="post" action="<?php echo url_session($_SERVER['PHP_SELF']); ?>" name="tb_modadd_form">
<table border cellpadding="3">
<?php

   echo get_coldef_definition('add', $tb_strings['DefNewCol'], 5, TRUE);
?>
</table>
<input type="submit" name="tb_modadd_doit" value="<?php echo $button_strings['Add']; ?>" class="bgrp">
<input type="reset" name="tb_modadd_clear" value="<?php echo $button_strings['Reset']; ?>" class="bgrp">
<input type="submit" name="tb_modadd_cancel" value="<?php echo $button_strings['Cancel']; ?>" class="bgrp">
</form>
<?php

elseif (isset($col_mod_flag)):

?>
<form method="post" action="<?php echo url_session($_SERVER['PHP_SELF']); ?>" name="tb_modcol_form">
<table border cellpadding="3">
<?php

   echo get_datatype_definition('mod', 'Change the Definitions for Column '.$s_modify_col, 2);
   echo get_column_constraint_definition($s_coldefs['mod'], 'mod');
?>
</table>
<input type="submit" name="tb_modcol_doit" value="<?php echo $button_strings['Save']; ?>" class="bgrp">
<input type="reset" name="tb_modcol_clear" value="<?php echo $button_strings['Reset']; ?>" class="bgrp">
<input type="submit" name="tb_modcol_cancel" value="<?php echo $button_strings['Cancel']; ?>" class="bgrp">
</form>

<?php

elseif ($s_connected && isset($s_modify_name)):

?>
<form method="post" action="<?php echo url_session($_SERVER['PHP_SELF']); ?>" name="tb_modify_form">
<table border cellpadding="3" cellspacing="0">
<tr>
  <th colspan="2" align="left"> 
    <b><?php echo $tb_strings['AddCol']; ?></b>
  </th>
  <td>
    <input type="submit" name="tb_modify_add" value="<?php echo $button_strings['Add']; ?>">
  </td>
</tr>
<tr>
  <td>
    <b><?php echo $tb_strings['SelColMod']; ?></b>
  </td>
  <td>
    <select name="tb_modify_mname">
    <?php build_column_options($s_modify_name); ?>
    </select>
  </td>
  <td>
    <input type="submit" name="tb_modify_col" value="<?php echo $button_strings['Modify']; ?>">
  </td>
</tr>
<tr>
  <td>
    <b><?php echo $tb_strings['SelColDel']; ?></b>
  </td>
  <td>
    <select name="tb_modify_dname">
    <?php build_column_options($s_modify_name); ?>
    </select>
  </td>
  <td>
    <input type="submit" name="tb_modify_del" value="<?php echo $button_strings['Delete']; ?>">
  </td>
</tr>
</table>
<input type="submit" name="tb_modify_ready" value="<?php echo $button_strings['Ready']; ?>">
</form>

<?php

endif;

?>