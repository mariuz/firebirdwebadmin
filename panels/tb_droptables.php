<?php 
/*
// File           tb_droptables.php / FirebirdWebAdmin
// Purpose        html sequence for the tb_dropcols-panel in table.php
// Author         Lutz Brueckner <irie@gmx.de> 
//                Valmor Flores <valmorflores@gmail.com>
// Copyright      (c) 2000, 2001, 2002, 2003, 2004 by Lutz Brueckner,
//                published under the terms of the GNU General Public Licence v.2,
//                see file LICENCE for details

*/
//echo 'Drop tables loaded';
if (isset($_POST['drop_tables'])):
?>
   <form method="post" action="<?php echo url_session($_SERVER['PHP_SELF']); ?>" name="tb_dropfields">
        <h3>Drop tables from database</h3>
        <table class="table table-bordered">
            <?php
            // from tb_dropcols_form - 
            $tableList = ($_POST['tb_selected_tables']);
            ?>
            <input type="hidden" name="tb_table_name" value="<?php echo $_POST['drop_tables'];?>">
            <?php
            foreach($tableList as $table){
               ?>
               <input name="tb_checked_tables[]" value="<?php echo $table ?>"/></br>
               <?php
            }
            ?>
        </table>
        <p>SQL Command:</p>
<pre>
        <?php foreach($tableList as $table){ ?>
        <?php echo 'DROP TABLE ' . $table . ';'; ?>
        
        <?php } ?></pre>
        Do you want to permanently remove these tables?
        <input type="submit" class="btn btn-success" name="tb_droptables_doit" value="<?php echo $button_strings['Yes']; ?>" class="bgrp">
        <input type="submit" class="btn btn-danger" name="tb_droptables_cancel" value="<?php echo $button_strings['Cancel']; ?>" class="bgrp">
    </form>
<?php

endif


?>