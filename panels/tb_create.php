<?php
// File           tb_create.php / FirebirdWebAdmin
// Purpose        html sequence for the tb_create-panel in tables.php
// Author         Lutz Brueckner <irie@gmx.de>
// Copyright      (c) 2000, 2001,2002, 2003, 2004 by Lutz Brueckner,
//                published under the terms of the GNU General Public Licence v.2,
//                see file LICENCE for details

if ($s_connected && (!isset($s_create_num) || empty($s_create_num))):

    ?>
    <form method="post" action="<?php echo url_session($_SERVER['PHP_SELF']); ?>" name="tb_create_form">
        <table class="table table-bordered">
            <tr>
                <td><b><?php echo $tb_strings['TbName']; ?></b><br>
                    <?php
                    $value = (isset($s_create_table)) ? $s_create_table : '';
                    echo get_textfield('tb_create_table', 30, 31, $value)
                    ?>
                </td>
                <td><b><?php echo $tb_strings['Fields']; ?></b><br>
                    <?php
                    $value = (isset($s_create_num)) ? $s_create_num : '';
                    echo get_textfield('tb_create_num', 4, 4, $value)
                    ?>
                </td>
            </tr>
        </table>
        <input type="submit" class="btn btn-primary" name="tb_create_doit" value="<?php echo $button_strings['Create']; ?>">
    </form>
<?php

elseif ($s_connected && $s_create_num > 0):     // $s_create_num > 0

    js_checkColConstraint();

    ?>
    <form method="post" action="<?php echo url_session($_SERVER['PHP_SELF']); ?>" name="tb_create_col_form">
        <table class="table table-bordered">
            <tr>
                <td colspan="9"><b><?php echo $tb_strings['TbName']; ?></b><br>
                    <input type="text" class="form-control" size="30" maxlength="31" name="tb_create_table" value="<?php echo $s_create_table; ?>">
                </td>
            </tr>
            <?php

            for ($i = 0; $i < $s_create_num; $i++) {
                $title = $tb_strings['DefColumn'] . ' ' . ($i + 1);
                echo get_coldef_definition($i, $title, 5, TRUE);
            }

            ?>
        </table>
        <br>
        <input type="submit" class="btn btn-success" name="tb_create_doit" value="<?php echo $button_strings['Create']; ?>" class="bgrp">
        <input type="reset" class="btn btn-default" name="tb_create_clear" value="<?php echo $button_strings['Reset']; ?>" class="bgrp">
        <input type="submit" class="btn btn-danger" name="tb_create_cancel" value="<?php echo $button_strings['Cancel']; ?>" class="bgrp">
    </form>
<?php

endif;

?>
