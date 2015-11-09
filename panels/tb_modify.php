<?php
// File           tb_modify.php / FirebirdWebAdmin
// Purpose        html sequence for the tb_modify-panel in table.php
// Author         Lutz Brueckner <irie@gmx.de>
// Copyright      (c) 2000, 2001, 2002, 2003, 2004 by Lutz Brueckner,
//                published under the terms of the GNU General Public Licence v.2,
//                see file LICENCE for details


if (isset($s_confirmations['column'])):
    $subject = 'column';
    include('./panels/confirm.php');

elseif ($s_connected && $s_modify_name == ''):

    ?>
    <form method="post" action="<?php echo url_session($_SERVER['PHP_SELF']); ?>" name="tb_modify_form" class="form-inline">
		<div class="form-group">
			<label for="tb_modify_name"><?php echo $tb_strings['SelTbMod']; ?></label>
			<?php echo get_table_selectlist('tb_modify_name', array('owner', 'noviews'), NULL, TRUE) ?>
		</div>
		<input type="submit" class="btn btn-success" name="tb_modify_doit" value="<?php echo $button_strings['Modify']; ?>">
    </form>
<?php

elseif (isset($col_add_flag)):
    js_checkColConstraint();
    ?>
    <form method="post" action="<?php echo url_session($_SERVER['PHP_SELF']); ?>" name="tb_modadd_form">
        <table class="table table-bordered">
            <?php
            echo get_coldef_definition('add', $tb_strings['DefNewCol'], 5, TRUE);
            ?>
        </table>
        <input type="submit" class="btn btn-success" name="tb_modadd_doit" value="<?php echo $button_strings['Add']; ?>" class="bgrp">
        <input type="reset" class="btn btn-default" name="tb_modadd_clear" value="<?php echo $button_strings['Reset']; ?>" class="bgrp">
        <input type="submit" class="btn btn-danger" name="tb_modadd_cancel" value="<?php echo $button_strings['Cancel']; ?>" class="bgrp">
    </form>
<?php

elseif (isset($col_mod_flag)):

    ?>
    <form method="post" action="<?php echo url_session($_SERVER['PHP_SELF']); ?>" name="tb_modcol_form">
        <table class="table table-bordered">
            <?php

            echo get_datatype_definition('mod', 'Change the Definitions for Column ' . $s_modify_col, 2);
            echo get_column_constraint_definition($s_coldefs['mod'], 'mod');
            ?>
        </table>
        <input type="submit" class="btn btn-success" name="tb_modcol_doit" value="<?php echo $button_strings['Save']; ?>" class="bgrp">
        <input type="reset" class="btn btn-default" name="tb_modcol_clear" value="<?php echo $button_strings['Reset']; ?>" class="bgrp">
        <input type="submit" class="btn btn-danger" name="tb_modcol_cancel" value="<?php echo $button_strings['Cancel']; ?>" class="bgrp">
    </form>

<?php

elseif ($s_connected && isset($s_modify_name)):

    ?>
    <form method="post" action="<?php echo url_session($_SERVER['PHP_SELF']); ?>" name="tb_modify_form">
        <table class="table table-bordered">
            <tr>
                <th colspan="2" align="left">
                    <label><?php echo $tb_strings['AddCol']; ?></label>
                </th>
                <td>
                    <input type="submit" class="btn btn-success" name="tb_modify_add" value="<?php echo $button_strings['Add']; ?>">
                </td>
            </tr>
            <tr>
                <td>
                    <label for="tb_modify_mname"><?php echo $tb_strings['SelColMod']; ?></label>
                </td>
                <td>
                    <select class="form-control" id="tb_modify_mname" name="tb_modify_mname">
                        <?php build_column_options($s_modify_name); ?>
                    </select>
                </td>
                <td>
                    <input type="submit" class="btn btn-primary" name="tb_modify_col" value="<?php echo $button_strings['Modify']; ?>">
                </td>
            </tr>
            <tr>
                <td>
                    <label for="tb_modify_dname"><?php echo $tb_strings['SelColDel']; ?></label>
                </td>
                <td>
                    <select class="form-control" id="tb_modify_dname" name="tb_modify_dname">
                        <?php build_column_options($s_modify_name); ?>
                    </select>
                </td>
                <td>
                    <input type="submit" class="btn btn-danger" name="tb_modify_del" value="<?php echo $button_strings['Delete']; ?>">
                </td>
            </tr>
        </table>
        <input type="submit" class="btn btn-default" name="tb_modify_ready" value="<?php echo $button_strings['Ready']; ?>">
    </form>

<?php

endif;

?>
