<?php
// File           acc_exc.php / FirebirdWebAdmin
// Purpose        html sequence for the exceptions panel in accessories.php
// Author         Lutz Brueckner <irie@gmx.de>
// Copyright      (c) 2000, 2001, 2002, 2003, 2004 by Lutz Brueckner,
//                published under the terms of the GNU General Public Licence v.2,
//                see file LICENCE for details


if (isset($s_confirmations['exc'])) {
    $subject = 'exc';
    include('./panels/confirm.php');
}

elseif (isset($exc_add_flag)) {

?>
<form method="post" action="<?php echo url_session($_SERVER['PHP_SELF']); ?>" name="create_exc_form">
<table border cellpadding="3" cellspacing="0">
  <tr>
    <th align="left"><?php echo $acc_strings['CreateExc']; ?></th>
  </tr>
  <tr>
    <td>
      <b><?php echo $acc_strings['Name']; ?></b><br>
      <input type="text" size="20" maxlength="31" name="def_exc_name" value="<?php echo htmlspecialchars($s_exception_defs['name']); ?>">
    </td>
  </tr>
  <tr>
    <td>
      <b><?php echo $acc_strings['Message']; ?></b><br>
      <input type="text" size="78" maxlength="78" name="def_exc_msg" value="<?php echo htmlspecialchars($s_exception_defs['msg']); ?>">
    </td>
  </tr>
</table>
<input type="submit" name="acc_exc_create_doit" value="<?php echo $button_strings['Create']; ?>" class="bgrp">
<input type="reset" name="acc_exc_create_clear" value="<?php echo $button_strings['Reset']; ?>" class="bgrp">
<input type="submit" name="acc_exc_create_cancel" value="<?php echo $button_strings['Cancel']; ?>" class="bgrp">
</form>
<?php

} elseif (isset($exc_mod_flag)) {

?>
<form method="post" action="<?php echo url_session($_SERVER['PHP_SELF']); ?>" name="modify_exc_form">
<table border cellpadding="3" cellspacing="0">
  <tr>
    <th align="left"><?php echo  sprintf($acc_strings['ModExc'], $s_exception_defs['name']); ?></th>
  </tr>
  <tr>
    <td>
      <b><?php echo $acc_strings['Message']; ?></b><br>
      <input type="text" size="78" maxlength="78" name="def_exc_msg" value="<?php echo htmlspecialchars($s_exception_defs['msg']); ?>">
    </td>
  </tr>
</table>
<input type="submit" name="acc_exc_mod_doit" value="<?php echo $button_strings['Save']; ?>" class="bgrp">
<input type="reset" name="acc_exc_mod_clear" value="<?php echo $button_strings['Reset']; ?>" class="bgrp">
<input type="submit" name="acc_exc_mod_cancel" value="<?php echo $button_strings['Cancel']; ?>" class="bgrp">
</form>
<?php

} elseif ($s_connected == TRUE) {

?>
<form method="post" action="<?php echo url_session($_SERVER['PHP_SELF']); ?>" name="acc_exc_form">
<?php

    if (!empty($s_exceptions)) {

        echo get_exceptions_table($s_exceptions, $s_exceptions_order, $s_exceptions_dir);
    }

    echo '<input type="submit" name="acc_exc_reload" value="' . $button_strings['Reload'] ."\">\n";

?>
<br><br>
<table border cellpadding="3" cellspacing="0">
<tr>
  <th colspan="2" align="left"><?php echo $acc_strings['CreateExc']; ?></th>
  <td><input type="submit" name="acc_exc_create" value="<?php echo $button_strings['Create']; ?>"></td>
</tr>
<tr>
  <td>
    <b><?php echo $acc_strings['SelExcMod']; ?></b>
  </td>
  <td>
    <?php echo get_exception_select('acc_exc_mod_name') ?>
  </td>
  <td align="left">
    <input type="submit" name="acc_exc_mod" value="<?php echo $button_strings['Modify']; ?>">
  </td>
</tr>
<tr>
  <td>
    <b><?php echo $acc_strings['SelExcDel']; ?></b>
  </td>
  <td>
    <?php echo get_exception_select('acc_exc_del_name'); ?>
  </td>
  <td align="left">
    <input type="submit" name="acc_exc_del" value="<?php echo $button_strings['Delete']; ?>">
  </td>
</tr>

</table>
</form>
<?php

}

?>
