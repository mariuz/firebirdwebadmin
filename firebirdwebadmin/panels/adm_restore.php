<?php
// File           adm_restore.php / ibWebAdmin
// Purpose        restore a database from a backup file
// Author         Lutz Brueckner <irie@gmx.de>
// Copyright      (c) 2000, 2001, 2002, 2003, 2004, 2005 by Lutz Brueckner,
//                published under the terms of the GNU General Public Licence v.2,
//                see file LICENCE for details
// Created        <02/12/26 12:59:02 lb>
//
// $Id: adm_restore.php,v 1.9 2006/03/14 21:09:23 lbrueckner Exp $

if ($s_connected == TRUE):

?>
<form method="post" action="<?php echo url_session($_SERVER['PHP_SELF']); ?>" name="adm_restore">
<table border cellpadding="3" cellspacing="0">
<tr>
   <th colspan="2" align="left"><?php echo $adm_strings['RSource']; ?></th>
</tr>
<tr>
   <td>
      <?php echo $adm_strings['FDName']; ?>
   </td>
   <td>
      <?php if (defined('BACKUP_DIR')  &&  BACKUP_DIR !== '') echo BACKUP_DIR.'<br>'; ?>
      <?php echo get_textfield('adm_re_source', 50, 256, $s_restore['source']); ?>
   </td>
</tr>
</table>

<table border cellpadding="3" cellspacing="0">
<tr>
   <th colspan="2" align="left"><?php echo $adm_strings['RTarget']; ?></th>
</tr>
<tr>
   <td>
      <?php echo $adm_strings['TargetDB']; ?>
   </td>
   <td>
      <?php echo get_textfield('adm_re_target', 50, 256, $s_restore['target']); ?>
   </td>
</tr>
<tr>
   <td>
      <input type="radio" name="adm_re_overwrite" value="0"<?php if ($s_restore['overwrite'] == FALSE) echo ' checked'; ?>>&nbsp;
      <?php echo  $adm_strings['NewFile']; ?>
   </td>
   <td>
      <input type="radio" name="adm_re_overwrite" value="1"<?php if ($s_restore['overwrite'] == TRUE) echo ' checked'; ?>>&nbsp;
      <?php echo  $adm_strings['RestFile']; ?>
   </td>
</tr>
</table>

<table border cellpadding="3" cellspacing="0">
<tr>
   <th colspan="2" align="left"><?php echo $adm_strings['Options']; ?></th>
</tr>
<tr>
   <td>
      <input type="checkbox" name="adm_re_inactive" value="1"<?php if ($s_restore['inactive'] == TRUE) echo ' checked'; ?>>
      <?php echo $adm_strings['IdxInact']; ?>&nbsp;
   </td>
   <td>
      <input type="checkbox" name="adm_re_novalidity" value="1"<?php if ($s_restore['novalidity'] == TRUE) echo ' checked'; ?>>
      <?php echo $adm_strings['NoValidity']; ?>&nbsp;
   </td>
</tr>
<tr>
   <td>
      <input type="checkbox" name="adm_re_oneattime" value="1"<?php if ($s_restore['oneattime'] == TRUE) echo ' checked'; ?>>
      <?php echo $adm_strings['OneAtTime']; ?>&nbsp;
   </td>
   <td>
      <input type="checkbox" name="adm_re_kill" value="1"<?php if ($s_restore['kill'] == TRUE) echo ' checked'; ?>>
      <?php echo $adm_strings['KillShad']; ?>&nbsp;
   </td>
</tr>
<tr>
   <td>
      <input type="checkbox" name="adm_re_useall" value="1"<?php if ($s_restore['useall'] == TRUE) echo ' checked'; ?>>
      <?php echo $adm_strings['UseAll']; ?>&nbsp;
   </td>
   <td>
      <input type="checkbox" name="adm_re_verbose" value="1"<?php if ($s_restore['verbose'] == TRUE) echo ' checked'; ?>>
      <?php echo $adm_strings['Verbose']; ?>&nbsp;
   </td>
</tr>
</table>
<input type="checkbox" name="adm_re_connect" value="1"<?php if ($s_restore['connect'] == TRUE) echo ' checked'; ?>>
<?php echo $adm_strings['ConnAfter']; ?><br>
<?php

    if (isset($iframekey_restore)):
?>

<br />
<div class="if">
  <iframe src="<?php echo url_session('./iframe_content.php?key='.$iframekey_restore); ?>" width="98%" height="<?php echo $s_cust['iframeheight']; ?>" name="adm_restore_iframe"></iframe>
</div>
<br />
<?php

    endif;
?>
<input type="submit" name="adm_restore_doit" value="<?php echo $button_strings['Restore']; ?>">
</form>
<?php

endif;

?>