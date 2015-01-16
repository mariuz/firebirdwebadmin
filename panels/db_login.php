<?php
// File           db_login.php / FirebirdWebAdmin
// Purpose        html sequence for the db_login-panel in database.php
// Author         Lutz Brueckner <irie@gmx.de>
// Copyright      (c) 2000, 2001, 2002, 2003, 2004, 2005 by Lutz Brueckner,
//                published under the terms of the GNU General Public Licence v.2,
//                see file LICENCE for details

?>
<form method="post" action="<?php echo url_session($_SERVER['PHP_SELF']); ?>" name="db_login_form">
<table cellpadding="3" cellspacing="0">
<tr>
   <td colspan="2"><b><?php echo $db_strings['Database']; ?></b><br>
<?php

if (count($dbfiles) == 0) {

    echo get_textfield('db_login_database', '35', '128', $s_login['database']);

} else {

    echo get_selectlist('db_login_database', $dbfiles, $s_login['database'], TRUE);
}

?>
   </td>
   <td colspan="3"><b><?php echo $db_strings['Host']; ?></b><br>
      <?php echo get_textfield('db_login_host', '35', '128', $s_login['host']); ?>
   </td>

</tr>
<tr>
   <td colspan="2"><b><?php echo $db_strings['Username']; ?></b><br>
      <?php echo get_textfield('db_login_user', 35, 31, $s_login['user']); ?>
   </td>
   <td colspan="3"><b><?php echo $db_strings['Password']; ?></b><br>
      <?php echo get_textfield('db_login_password', 35, 32, $s_login['password'], 'password'); ?>
   </td>
</tr>
<tr>
   <td><b><?php echo $db_strings['Role']; ?></b><br>
      <?php echo get_textfield('db_login_role', 28, 32, $s_login['role']); ?>
   </td>
   <td><b><?php echo $db_strings['Cache']; ?></b><br>
      <?php echo get_textfield('db_login_cache', 5, 5, $s_login['cache']); ?>
   </td>
   <td><b><?php echo $db_strings['Charset']; ?></b><br>
      <?php echo get_charset_select('db_login_charset', $s_login['charset']); ?>
    </td>
   <td><b><?php echo $db_strings['Dialect']; ?></b><br>
      <?php echo get_selectlist('db_login_dialect', array(1, 2, 3), $s_login['dialect'], TRUE); ?>
   </td>
   <td><b><?php echo $db_strings['Server']; ?></b><br>
      <?php echo get_selectlist('db_login_server', $server_types, $s_login['server'], TRUE); ?>
   </td>
</tr>
<tr>
   <td>
      <input class="btn btn-default" type="submit" name="db_login_doit" value="<?php echo $button_strings['Login']; ?>">
   </td>
   <td colspan="3">
<?php

if ($s_connected == TRUE) {
    echo '      <input class="btn btn-default" type="submit" name="db_logout_doit" value="'.$button_strings['Logout']."\">\n";
}
else {
    echo "      &nbsp;\n";
}
?>
   </td>
</tr>
</table>
</form>
