<?php
// Purpose        html sequence for the sql_enter-panel in sql.php
// Author         Lutz Brueckner <irie@gmx.de>
// Copyright      (c) 2000-2006 by Lutz Brueckner,
//                published under the terms of the GNU General Public Licence v.2,
//                see file LICENCE for details
$js_stack .= js_giveFocus('sql_enter_form', 'sql_script');

?>
<form method="post" action="<?php echo url_session($_SERVER['PHP_SELF']); ?>" name="sql_enter_form" enctype="multipart/form-data">
	<textarea class="form-control" name="sql_script" id="sql_script" wrap="virtual" rows="10"><?php echo htmlspecialchars($sql_script); ?></textarea>
	<table class="table">
      <tr>
         <td>
            <input class="btn btn-success" type="submit" name="sql_run" value="<?php echo $button_strings['DoQuery']; ?>">
         </td>
         <td>
            <input class="btn btn-deafult" type="reset" name="sql_reset" value="<?php echo $button_strings['Reset']; ?>">
         </td>
         <td>
            <input class="btn btn-link" type="button" name="sql_clear" value="<?php echo $button_strings['Clear']; ?>" onClick="document.sql_enter_form.sql_script.value=''">
         </td>
         <td>
			 <div style=" border-right: 1px dashed #333; padding-top: 10px">&nbsp;</div>
         </td>
         <td>
            <input class="btn btn-info" type="submit" name="sql_plan" value="<?php echo $button_strings['QueryPlan']; ?>">
         </td>
         <td width="100%">
		 
         </td>
         <td>
            <input name="sql_file" type="file" size="50" maxlength="100000" accept="text/*">
         </td>
         <td>
            <input class="btn btn-primary" type="submit" name="sql_load" value="<?php echo $button_strings['Load']; ?>">
         </td>
         <td>
            <input class="btn btn-primary" type="submit" name="sql_execute" value="<?php echo $button_strings['Execute']; ?>">
         </td>
      </tr>
   </table>
</form>
