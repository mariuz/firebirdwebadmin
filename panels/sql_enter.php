<?php
// File           sql_enter.php / FirebirdWebAdmin
// Purpose        html sequence for the sql_enter-panel in sql.php
// Author         Lutz Brueckner <irie@gmx.de>
// Copyright      (c) 2000-2006 by Lutz Brueckner,
//                published under the terms of the GNU General Public Licence v.2,
//                see file LICENCE for details
// Created        <00/09/12 09:10:18 lb>


$js_stack .= js_giveFocus('sql_enter_form', 'sql_script');

?>
<form method="post" action="<?php echo url_session($_SERVER['PHP_SELF']); ?>" name="sql_enter_form" enctype="multipart/form-data">
   <table class="table" cellpadding="3" cellspacing="0">
      <tr>
         <td colspan="5">
            <textarea class="form-control" name="sql_script" id="sql_script" wrap="virtual"><?php echo htmlspecialchars($sql_script); ?></textarea>
         </td>
      </tr>
      <tr>
         <td colspan="5" align="right">
            <input name="sql_file" type="file" size="50" maxlength="100000" accept="text/*">
            <input class="btn btn-primary" type="submit" name="sql_load" value="<?php echo $button_strings['Load']; ?>">
            <input class="btn btn-primary" type="submit" name="sql_execute" value="<?php echo $button_strings['Execute']; ?>">
         </td>
      </tr>
      <tr>
         <td>
            <input class="btn btn-success" type="submit" name="sql_run" value="<?php echo $button_strings['DoQuery']; ?>">
            <input class="btn" type="reset" name="sql_reset" value="<?php echo $button_strings['Reset']; ?>">
            <input class="btn" type="button" name="sql_clear" value="<?php echo $button_strings['Clear']; ?>" onClick="document.sql_enter_form.sql_script.value=''">
            <input class="btn btn-info" type="submit" name="sql_plan" value="<?php echo $button_strings['QueryPlan']; ?>">
         </td>
         </td>
      </tr>
   </table>   
</form>
