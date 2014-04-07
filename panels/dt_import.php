<?php
// Purpose        html sequence for the data-import panel in data.php
// Author         Lutz Brueckner <irie@gmx.de>
// Copyright      (c) 2000, 2001, 2002, 2003, 2004, 2005 by Lutz Brueckner,
//                published under the terms of the GNU General Public Licence v.2,
//                see file LICENCE for details

if ($s_connected):

?>
<form method="post" action="<?php echo url_session($_SERVER['PHP_SELF']); ?>" name="dt_import_form" enctype="multipart/form-data">
<table border cellpadding="3" cellspacing="0">
  <tr>
    <th><?php echo $dt_strings['SelTable']; ?></th><th><?php echo $dt_strings['FileName']; ?></th><th>&nbsp;</th>
  <tr>
  <tr>
    <td>
      <?php echo get_table_selectlist('dt_import_table', array('insert'), NULL, TRUE); ?>
    </td>
    <td>
      <input type="file" size="30" name="dt_import_file">
    </td>
    <td>
      <input type="submit" name="dt_import_doit" value="<?php echo $button_strings['Import']; ?>">
     </td>
  </tr>
</table>
<input type="checkbox" name="dt_import_null" value="yes"<?php if ($s_csv['import_null']) echo ' checked'; ?>>&nbsp;<?php echo $dt_strings['ConvEmpty']; ?>
</form>
<b><?php echo $dt_strings['FileForm'].':'; ?></b>
<ul type="circle">
   <li><?php echo $dt_strings['CsvForm1']; ?></li>
   <li><?php echo $dt_strings['CsvForm2']; ?></li>
   <li><?php echo $dt_strings['CsvForm3']; ?></li>
</ul>
<?php

endif;

?>
