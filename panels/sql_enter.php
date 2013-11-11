<?php
// File           sql_enter.php / FirebirdWebAdmin
// Purpose        html sequence for the sql_enter-panel in sql.php
// Author         Lutz Brueckner <irie@gmx.de>
// Copyright      (c) 2000-2006 by Lutz Brueckner,
//                published under the terms of the GNU General Public Licence v.2,
//                see file LICENCE for details
// Created        <00/09/12 09:10:18 lb>
//
// $Id: sql_enter.php,v 1.17 2006/07/08 17:23:27 lbrueckner Exp $


$js_stack .= js_giveFocus('sql_enter_form', 'sql_script');

?>
<form method="post" action="<?php echo url_session($_SERVER['PHP_SELF']); ?>" name="sql_enter_form" enctype="multipart/form-data">
   <table cellpadding="3" cellspacing="0">
      <tr>
         <td colspan="5">
            <textarea name="sql_script" id="sql_script" rows="<?php echo $s_cust['textarea']['rows']; ?>" cols="<?php echo $s_cust['textarea']['cols']; ?>" wrap="virtual"><?php echo htmlspecialchars($sql_script); ?></textarea>
         </td>
      </tr>
      <tr>
         <td colspan="5" align="right">
            <input name="sql_file" type="file" size="50" maxlength="100000" accept="text/*" class="bgrp">
            <input type="submit" name="sql_load" value="<?php echo $button_strings['Load']; ?>" class="bgrp">
            <input type="submit" name="sql_execute" value="<?php echo $button_strings['Execute']; ?>" class="bgrp">
         </td>
      </tr>
      <tr>
         <td>
            <input type="submit" name="sql_run" value="<?php echo $button_strings['DoQuery']; ?>" class="bgrp">
            <input type="reset" name="sql_reset" value="<?php echo $button_strings['Reset']; ?>" class="bgrp">
            <input type="button" name="sql_clear" value="<?php echo $button_strings['Clear']; ?>" onClick="document.sql_enter_form.sql_script.value=''" class="bgrp">
         </td>
         <td>&nbsp;</td>
         <td>
            <input type="submit" name="sql_plan" value="<?php echo $button_strings['QueryPlan']; ?>">
         </td>
         <td>&nbsp;</td>
         <td align="right" valign="center">
<?php
            $next_url = "javascript:requestSqlBuffer('".($s_sql_pointer < SQL_HISTORY_SIZE -1 ? $s_sql_pointer +1 : 0)."')";
            $prev_url = "javascript:requestSqlBuffer('".($s_sql_pointer == 0 ? SQL_HISTORY_SIZE -1 : $s_sql_pointer -1)."')";
            $go_event = " onClick=\"requestSqlBuffer(selectedElement($('sql_pointer')))\";";
            $button_type = 'button';

            echo '&nbsp;<a href="'.$prev_url.'" id="sql_prev"><img src="'.DATAPATH . (BG_TRANSPARENT == TRUE ? 'transparent/' : 'opaque/') . "left_arrow.png\" border=\"0\"></a>\n";
            echo get_selectlist('sql_pointer', range(0, SQL_HISTORY_SIZE -1), $s_sql_pointer, FALSE, array('id' => 'sql_pointer'));
            echo '<input type="'.$button_type.'" name="sql_go" value="'.$button_strings['Go'].'"'.$go_event.'>';
            echo '&nbsp;<a href="'.$next_url.'" id="sql_next"><img src="'.DATAPATH . (BG_TRANSPARENT == TRUE ? 'transparent/' : 'opaque/') . "right_arrow.png\" border=\"0\"></a>\n";
?>
         </td>
      </tr>
   </table>   
</form>
