<?php
// File           db_delete.php / ibWebAdmin
// Purpose        html sequence for the db_delete-panel in database.php
// Author         Lutz Brueckner <irie@gmx.de>
// Copyright      (c) 2000, 2001, 2002, 2003, 2004 by Lutz Brueckner,
//                published under the terms of the GNU General Public Licence v.2,
//                see file LICENCE for details
// Created        <00/09/17 16:23:44 lb>
//
// $Id: db_delete.php,v 1.9 2004/11/10 21:43:04 lbrueckner Exp $


if (isset($s_confirmations['database'])):
    $subject = 'database';
    include_once('./panels/confirm.php');

else:

?>
<form method="post" action="<?php echo url_session($_SERVER['PHP_SELF']); ?>" name="db_delete_form">
<table cellpadding="3" cellspacing="0">
<tr>
   <td><b><?php echo $db_strings['DelDB']; ?></b><br>
<?php

    if (count($dbfiles) == 0):
?>
       <input type="text" size="35" maxlength="255" name="db_delete_database" value="<?php echo $s_delete_db['database']; ?>">
<?php

    else:
        echo get_selectlist('db_delete_database', $dbfiles, $s_delete_db['database'], TRUE);
    endif;
?>
   </td>
   <td>
      <b><?php echo $db_strings['Host']; ?></b><br>
      <input type="text" size="35" maxlength="255" name="db_delete_host" value="<?php echo $s_delete_db['host']; ?>">
   </td>

</tr>
<tr>
  <td>
      <b><?php echo $db_strings['Username']; ?></b><br>
      <input type="text" size="35" maxlength="32" name="db_delete_user" value="<?php echo $s_delete_db['user']; ?>">
  </td>
  <td>
      <b><?php echo $db_strings['Password']; ?></b><br>
      <input type="password" size="35" maxlength="32" name="db_delete_password" value="<?php echo password_stars($s_delete_db['password']); ?>">
  </td>
</tr>
</table>
<input type="submit" name="db_delete_doit" value="<?php echo $button_strings['Delete']; ?>">
</form>
<?php

endif;

?>
