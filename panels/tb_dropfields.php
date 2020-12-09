
<?php 
/*
// File           tb_dropfields.php / FirebirdWebAdmin
// Purpose        html sequence for the tb_dropcols-panel in table.php
// Author         Lutz Brueckner <irie@gmx.de>
// Copyright      (c) 2000, 2001, 2002, 2003, 2004 by Lutz Brueckner,
//                published under the terms of the GNU General Public Licence v.2,
//                see file LICENCE for details

*/
//echo 'Drop fields loaded';
if (isset($_POST['drop_cols'])):
?>
   <form method="post" action="<?php echo url_session($_SERVER['PHP_SELF']); ?>" name="tb_dropfields">
        <h3>Drop columns from table: <?php echo $_POST['drop_cols']; ?></h3>
        <table class="table table-bordered">
            <?php
            // from tb_dropcols_form
            $columns = ($_POST['tb_selected_fields']);
            ?>
            <input type="hidden" name="tb_table_name" value="<?php echo $_POST['drop_cols'];?>">
            <?php
            foreach($columns as $col){
               ?>
               <input name="tb_checked_fields[]" value="<?php echo $col ?>"/></br>
               <?php
            }
            ?>
        </table>
        Do you want to permanently remove these columns from the table?
        <input type="submit" class="btn btn-success" name="tb_dropfields_doit" value="<?php echo $button_strings['Yes']; ?>" class="bgrp">
        <input type="submit" class="btn btn-danger" name="tb_dropfields_cancel" value="<?php echo $button_strings['Cancel']; ?>" class="bgrp">
    </form>
<?php

endif




?>