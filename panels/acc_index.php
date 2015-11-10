<?php
// File           acc_index.php / FirebirdWebAdmin
// Purpose        html sequence for the indexes-panel in accessories.php
// Author         Lutz Brueckner <irie@gmx.de>
// Copyright      (c) 2000, 2001, 2002, 2003, 2004 by Lutz Brueckner,
//                published under the terms of the GNU General Public Licence v.2,
//                see file LICENCE for details

if (isset($s_confirmations['index'])):
    $subject = 'index';
    include('./panels/confirm.php');

elseif (isset($index_add_flag)):

    ?>
    <form method="post" action="<?php echo url_session($_SERVER['PHP_SELF']); ?>" name="acc_index_form">
        <?php

        index_definition(NULL, $acc_strings['CreateIdx']);
        ?>
        <input type="submit" class="btn btn-success" name="acc_ind_create_doit" value="<?php echo $button_strings['Create']; ?>" class="bgrp">
        <input type="reset" class="btn btn-default" name="acc_ind_create_clear" value="<?php echo $button_strings['Reset']; ?>" class="bgrp">
        <input type="submit" class="btn btn-danger" name="acc_ind_create_cancel" value="<?php echo $button_strings['Cancel']; ?>" class="bgrp">
    </form>
<?php

elseif (!empty($s_mod_index)):

    ?>
    <form method="post" action="<?php echo url_session($_SERVER['PHP_SELF']); ?>" name="acc_indmod_form">
        <?php

        index_definition($s_mod_index, sprintf($acc_strings['ModIdx'], $s_mod_index));
        ?>
        <input type="submit" class="btn btn-primary" name="acc_modind_doit" value="<?php echo $button_strings['Modify']; ?>" class="bgrp">
        <input type="submit" class="btn btn-danger" name="acc_modind_cancel" value="<?php echo $button_strings['Cancel']; ?>" class="bgrp">
    </form>

<?php

elseif ($s_connected):

    if (!empty($indices)) {

        echo get_index_table($indices, $s_index_order, $s_index_dir);
    }
    ?>
    <form method="post" action="<?php echo url_session($_SERVER['PHP_SELF']); ?>" name="acc_index_form">
        <table class="table table-bordered">
            <tr>
                <th colspan="2" align="left"><?php echo $acc_strings['CreateIdx']; ?></th>
                <td><input type="submit" class="btn btn-success" name="acc_index_create" value="<?php echo $button_strings['Create']; ?>"></td>
            </tr>
            <tr>
                <td>
                    <b><?php echo $acc_strings['SelIdxMod']; ?></b>
                </td>
                <td>
                    <select class="form-control" name="acc_index_mname">
                        <?php build_index_options(); ?>
                    </select>
                </td>
                <td align="left">
                    <input type="submit" class="btn btn-primary" name="acc_index_mod" value="<?php echo $button_strings['Modify']; ?>">
                </td>
            </tr>
            <tr>
                <td>
                    <b><?php echo $acc_strings['SelIdxDel']; ?></b>
                </td>
                <td>
                    <select class="form-control" name="acc_index_dname">
                        <?php build_index_options(); ?>
                    </select>
                </td>
                <td align="left">
                    <input type="submit" class="btn btn-danger" name="acc_index_del" value="<?php echo $button_strings['Delete']; ?>">
                </td>
            </tr>
        </table>
    </form>
<?php

endif;

?>
