<?php
// File           acc_exc.php / FirebirdWebAdmin
// Purpose        html sequence for the exceptions panel in accessories.php
// Author         Lutz Brueckner <irie@gmx.de>
// Copyright      (c) 2000, 2001, 2002, 2003, 2004 by Lutz Brueckner,
//                published under the terms of the GNU General Public Licence v.2,
//                see file LICENCE for details


if (isset($s_confirmations['exc'])) {
    $subject = 'exc';
    include './panels/confirm.php';
} elseif (isset($exc_add_flag)) {
    ?>
    <form method="post" action="<?php echo url_session($_SERVER['PHP_SELF']); ?>" name="create_exc_form">
        <h4> <?php echo $acc_strings['CreateExc']; ?> </h4>

        <div class="form-group">
            <label for="def_exc_name"><?php echo $acc_strings['Name']; ?></label>
            <input type="text" size="20" maxlength="31" name="def_exc_name" id="def_exc_name" value="<?php echo htmlspecialchars($s_exception_defs['name']); ?>" class="form-control">
        </div>
        <div class="form-group">
            <label for="def_exc_msg"><?php echo $acc_strings['Message']; ?></label>
            <input type="text" size="78" maxlength="78" name="def_exc_msg" id="def_exc_msg" value="<?php echo htmlspecialchars($s_exception_defs['msg']); ?>" class="form-control">
        </div>
        <input type="submit" name="acc_exc_create_doit" value="<?php echo $button_strings['Create']; ?>" class="btn btn-success">
        <input type="reset" name="acc_exc_create_clear" value="<?php echo $button_strings['Reset']; ?>" class="btn btn-default">
        <input type="submit" name="acc_exc_create_cancel" value="<?php echo $button_strings['Cancel']; ?>" class="btn btn-default">
    </form>
    <?php

} elseif (isset($exc_mod_flag)) {
    ?>
    <form method="post" action="<?php echo url_session($_SERVER['PHP_SELF']); ?>" name="modify_exc_form">
        <h4><?php echo sprintf($acc_strings['ModExc'], $s_exception_defs['name']); ?></h4>

        <div class="form-group">
            <label for="def_exc_msg"><?php echo $acc_strings['Message']; ?></label>
            <input type="text" size="78" maxlength="78" name="def_exc_msg" value="<?php echo htmlspecialchars($s_exception_defs['msg']); ?>" class="form-control">
        </div>
        <input type="submit" name="acc_exc_mod_doit" value="<?php echo $button_strings['Save']; ?>" class="btn btn-success">
        <input type="reset" name="acc_exc_mod_clear" value="<?php echo $button_strings['Reset']; ?>" class="btn btn-default">
        <input type="submit" name="acc_exc_mod_cancel" value="<?php echo $button_strings['Cancel']; ?>" class="btn btn-default">
    </form>
    <?php

} elseif ($s_connected == true) {
    ?>
    <form method="post" action="<?php echo url_session($_SERVER['PHP_SELF']); ?>" name="acc_exc_form">
        <?php

        if (!empty($s_exceptions)) {
            echo get_exceptions_table($s_exceptions, $s_exceptions_order, $s_exceptions_dir);
        }

        echo '<input type="submit" class="btn btn-default btn-xs" name="acc_exc_reload" value="' . $button_strings['Reload'] . "\">\n";

        ?>
        <br><br>
        <table>
            <tr>
                <td align="left" colspan="2"><label><?php echo $acc_strings['CreateExc']; ?></label></td>
                <td><input type="submit" name="acc_exc_create" value="<?php echo $button_strings['Create']; ?>"
                           class="btn btn-primary"></td>
            </tr>
            <tr>
                <td>
                    <label for="acc_exc_mod_name"><?php echo $acc_strings['SelExcMod']; ?></label>
                </td>
                <td>
                    <?php echo get_exception_select('acc_exc_mod_name') ?>
                </td>
                <td align="left">
                    <input type="submit" name="acc_exc_mod" value="<?php echo $button_strings['Modify']; ?>" class="btn btn-success">
                </td>
            </tr>
            <tr>
                <td>
                    <label for="acc_exc_del_name"><?php echo $acc_strings['SelExcDel']; ?></label>
                </td>
                <td>
                    <?php echo get_exception_select('acc_exc_del_name');
                    ?>
                </td>
                <td align="left">
                    <input type="submit" name="acc_exc_del" value="<?php echo $button_strings['Delete']; ?>" class="btn btn-danger">
                </td>
            </tr>

        </table>
    </form>
    <?php

}

?>
