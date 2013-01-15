<?php
// File           db_create.php / FirebirdWebAdmin
// Purpose        html sequence for the db_create-panel in database.php
// Author         Lutz Brueckner <irie@gmx.de>
// Copyright      (c) 2000, 2001, 2002, 2003, 2004 by Lutz Brueckner,
//                published under the terms of the GNU General Public Licence v.2,
//                see file LICENCE for details
// Created        <00/09/17 16:23:01 lb>
//
// $Id: db_create.php,v 1.9 2004/10/08 20:36:55 lbrueckner Exp $

?>
<form method="post" action="<?php echo url_session($_SERVER['PHP_SELF']); ?>" name="db_create_form">
<table cellpadding="3" cellspacing="0">
<tr>
   <td><b><?php echo $db_strings['NewDB']; ?></b><br>
       <input type="text" size="35" maxlength="255" name="db_create_database" value="<?php echo $s_create_db; ?>">
   </td>
   <td><b><?php echo $db_strings['Host']; ?></b><br>
       <input type="text" size="35" maxlength="255" name="db_create_host" value="<?php echo $s_create_host; ?>">
   </td>
</tr>
<tr>
  <td>
      <b><?php echo $db_strings['Username']; ?></b><br>
      <input type="text" size="35" maxlength="32" name="db_create_user" value="<?php echo $s_create_user; ?>">
  </td>
  <td>
      <b><?php echo $db_strings['Password']; ?></b><br>
      <input type="password" size="35" maxlength="32" name="db_create_password" value="<?php echo password_stars($s_create_pw); ?>">
  </td>
</tr>
<tr>
  <td>
      <b><?php echo $db_strings['PageSize']; ?></b><br>
<?php
      echo get_selectlist('db_create_pagesize', $pagesizes, $s_create_pagesize, TRUE);
?>
  </td>
  <td>
      <b><?php echo $db_strings['Charset']; ?></b><br>
        <?php echo get_charset_select('db_create_charset', $s_create_charset); ?>
  </td>
</tr>
</table>
<input type="submit" name="db_create_doit" value="<?php echo $button_strings['Create']; ?>">
</form>
