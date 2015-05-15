<?php
// File           adm_backup.php / FirebirdWebAdmin
// Purpose        create a backup of the current database
// Author         Lutz Brueckner <irie@gmx.de>
// Copyright      (c) 2000, 2001, 2002, 2003, 2004, 2005 by Lutz Brueckner,
//                published under the terms of the GNU General Public Licence v.2,
//                see file LICENCE for details

if ($s_connected == TRUE):

?>
<form method="post" action="<?php echo url_session($_SERVER['PHP_SELF']); ?>" name="adm_backup">
<?php

    if ($s_login['user'] != 'SYSDBA') {
        sysdba_pw_textfield($adm_strings['SysdbaPW'], $adm_strings['Required'], $s_sysdba_pw);
    }

?>
<table class="table table-bordered">
<tr>
   <th colspan="2" align="left"><?php echo $adm_strings['BTarget']; ?></th>
</tr>
<tr>
   <td>
      <?php echo $adm_strings['FDName']; ?>
   </td>
   <td>
      <?php if (defined('BACKUP_DIR')  &&  BACKUP_DIR !== '') echo BACKUP_DIR.'<br>'; ?>
      <?php echo get_textfield('adm_bu_target', 50, 256, $s_backup['target']); ?>
   </td>
</tr>
</table>

<table class="table table-bordered">
<tr>
   <th colspan="2" align="left"><?php echo $adm_strings['Options']; ?></th>
</tr>
<tr>
   <td>
      <input type="checkbox" name="adm_bu_mdonly" value="1"<?php if ($s_backup['mdonly'] == TRUE) echo ' checked'; ?>>
      <?php echo $adm_strings['BMDOnly']; ?>&nbsp;
   </td>
   <td>
      <input type="checkbox" name="adm_bu_nogc" value="1"<?php if ($s_backup['nogc'] == TRUE) echo ' checked'; ?>>
      <?php echo $adm_strings['BNoGC']; ?>&nbsp;
   </td>
</tr>
<tr>
   <td>
      <input type="checkbox" name="adm_bu_mdoldstyle" value="1"<?php if ($s_backup['mdoldstyle'] == TRUE) echo ' checked'; ?>>
      <?php echo $adm_strings['BMDOStyle']; ?>&nbsp;
   </td>
   <td>
      <input type="checkbox" name="adm_bu_ignorecs" value="1"<?php if ($s_backup['ignorecs'] == TRUE) echo ' checked'; ?>>
      <?php echo $adm_strings['BIgnoreCS']; ?>&nbsp;
   </td>
</tr>
<tr>
   <td>
      <input type="checkbox" name="adm_bu_transport" value="1"<?php if ($s_backup['transport'] == TRUE) echo ' checked'; ?>>
      <?php echo $adm_strings['BTransport']; ?>&nbsp;
   </td>
   <td>
      <input type="checkbox" name="adm_bu_ignorelt" value="1"<?php if ($s_backup['ignorelt'] == TRUE) echo ' checked'; ?>>
      <?php echo $adm_strings['BIgnoreLT']; ?>&nbsp;
   </td>
</tr>
<tr>
   <td>
      <input type="checkbox" name="adm_bu_convert" value="1"<?php if ($s_backup['convert'] == TRUE) echo ' checked'; ?>>
      <?php echo $adm_strings['BConvert']; ?>&nbsp;
   </td>
   <td>
      <input type="checkbox" name="adm_bu_verbose" value="1"<?php if ($s_backup['verbose'] == TRUE) echo ' checked'; ?>>
      <?php echo $adm_strings['Verbose']; ?>&nbsp;
   </td>
</tr>
</table>
<?php

if (isset($iframekey_backup)):
?>

<br />
<div class="if">
  <iframe src="<?php echo url_session('./iframe_content.php?key='.$iframekey_backup); ?>" width="98%" height="<?php echo $s_cust['iframeheight']; ?>" name="adm_backup_iframe"></iframe>
</div>
<br />
<?php

    endif;
?>
<input type="submit" class="btn btn-default" name="adm_backup_doit" value="<?php echo $button_strings['Backup']; ?>">
</form>
<?php

endif;

?>
