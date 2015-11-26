<?php
// File           tb_delete.php / FirebirdWebAdmin
// Purpose        html sequence for the tb_delete-panel in table.php
// Author         Lutz Brueckner <irie@gmx.de>
// Copyright      (c) 2000, 2001, 2002, 2003, 2004 by Lutz Brueckner,
//                published under the terms of the GNU General Public Licence v.2,
//                see file LICENCE for details

if (isset($s_confirmations['table'])) {
    $subject = 'table';
    include './panels/confirm.php';
} elseif ($s_connected) {
    ?>
    <form method="post" action="<?php url_session($_SERVER['PHP_SELF']);
    ?>" name="tb_delete_form" class="form-inline">
		<div class="form-group">
			<label for="tb_modify_name"><?php echo $tb_strings['SelTbDel'];
    ?></label>
			<?php echo get_table_selectlist('tb_delete_name', array('owner', 'noviews'), null, true) ?>
		</div>
		<input type="submit" class="btn btn-danger" name="tb_delete_doit" value="<?php echo $button_strings['Delete'];
    ?>">
    </form>
<?php

}
?>
