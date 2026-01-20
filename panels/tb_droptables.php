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

if (isset($_POST['drop_tables'])):
        
    if (!isset($_POST['tb_selected_tables'])){
        $message = $WARNINGS['SELECT_TABLE_FIRST'];
    } else {
        $tableList = ($_POST['tb_selected_tables']);
?>
   <form method="post" action="<?php echo url_session($_SERVER['PHP_SELF']); ?>" name="tb_droptables">
        <h4><?=$tb_strings['DropManyTables']?></h4>
        <table class="table table-bordered">
            <?php
            
            ?>
            <input type="hidden" name="tb_table_name" value="<?php echo htmlspecialchars($_POST['drop_tables'], ENT_QUOTES, 'UTF-8');?>">
            <?php
            foreach($tableList as $table){
               ?>
               <input name="tb_checked_tables[]" value="<?php echo htmlspecialchars($table, ENT_QUOTES, 'UTF-8'); ?>" readonly/></br>
               <?php
            }
            ?>
        </table>
        <p><?=$tb_strings['SQLCommand']?></p>
<pre>
<?php foreach($tableList as $table){ ?>
<?php echo 'DROP TABLE ' . htmlspecialchars($table, ENT_QUOTES, 'UTF-8') . ';'; ?>

        <?php } ?></pre>
        <?=$MESSAGES['CONFIRM_MANY_TABLES_DELETE']?>
        <input type="submit" class="btn btn-success" name="tb_droptables_doit" value="<?php echo $button_strings['Yes']; ?>" class="bgrp">
        <input type="submit" class="btn btn-danger" name="tb_droptables_cancel" value="<?php echo $button_strings['Cancel']; ?>" class="bgrp">
    </form>
<?php
    }
endif


?>