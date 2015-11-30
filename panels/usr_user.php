<?php
// Purpose        html sequence for the users-panel in user.php
// Author         Lutz Brueckner <irie@gmx.de>
// Copyright      (c) 2000, 2001, 2002, 2003, 2004, 2005 by Lutz Brueckner,
//                published under the terms of the GNU General Public Licence v.2,
//                see file LICENCE for details

if (isset($s_confirmations['user'])):
    $subject = 'user';
    include_once './panels/confirm.php';

elseif (isset($user_add_flag)):

    ?>
    <form method="post" action="<?php echo url_session($_SERVER['PHP_SELF']); ?>" name="usr_user_form">
        <?php

        echo user_definition($udata, $usr_strings['CreateUsr']);
        ?>
        <input type="submit" name="usr_user_create_doit" value="<?php echo $button_strings['Create']; ?>" class="btn btn-primary">
        <input type="reset" name="usr_user_reset" value="<?php echo $button_strings['Reset']; ?>" class="btn btn-default">
        <input type="submit" name="usr_user_create_cancel" value="<?php echo $button_strings['Cancel']; ?>"
               class="btn btn-default">
    </form>
    <?php

elseif (!empty($s_user_name)):

    ?>
    <form method="post" action="<?php echo url_session($_SERVER['PHP_SELF']); ?>" name="usr_user_form">
        <?php

        echo user_definition($udata, sprintf($usr_strings['ModUser'], $s_user_name, true));
        ?>
        <input type="submit" name="usr_user_mod_doit" value="<?php echo $button_strings['Modify']; ?>" class="btn btn-success">
        <input type="reset" name="usr_user_mod_reset" value="<?php echo $button_strings['Reset']; ?>" class="btn btn-default">
        <input type="submit" name="usr_user_mod_cancel" value="<?php echo $button_strings['Cancel']; ?>" class="btn btn-default">
    </form>

    <?php

elseif ($s_connected):

    ?>
    <table class="table table-bordered table-hover">
        <thead>
        <tr align="left">
            <th><?php echo $usr_strings['UName']; ?></th>
            <th><?php echo $usr_strings['FName']; ?></th>
            <th><?php echo $usr_strings['MName']; ?></th>
            <th><?php echo $usr_strings['LName']; ?></th>
        </tr>
        </thead>
        <?php
        if (!empty($users)):
            foreach ($users as $uname => $udata):
                ?>
                <tr>
                    <td><?php echo table_val($uname); ?></td>
                    <td><?php echo table_val($udata['FIRST_NAME']); ?></td>
                    <td><?php echo table_val($udata['MIDDLE_NAME']); ?></td>
                    <td><?php echo table_val($udata['LAST_NAME']); ?></td>
                </tr>
                <?php
            endforeach;
        endif;

        ?>
    </table>

    <form method="post" action="<?php echo url_session($_SERVER['PHP_SELF']); ?>" name="usr_user_form">
        <?php
        if ($s_login['user'] != 'SYSDBA') {
            sysdba_pw_textfield($usr_strings['SysdbaPW'], $usr_strings['Required'], $s_sysdba_pw);
        }
        ?>
        <table class="table table-bordered">
            <tr>
                <th align="left"><?php echo $usr_strings['CreateUsr']; ?></th>
                <td><input type="submit" class="btn btn-primary" name="usr_user_create"
                           value="<?php echo $button_strings['Create']; ?>"></td>
            </tr>

            <tr>
                <td>
                    <label for="usr_user_mname"><?php echo $usr_strings['USelMod']; ?></label>
                    <select class="form-control" id="usr_user_mname" name="usr_user_mname">
                        <?php build_user_options(); ?>
                    </select>
                </td>
                <td align="left">
                    <input type="submit" class="btn btn-success" name="usr_user_mod"
                           value="<?php echo $button_strings['Modify']; ?>">
                </td>
            </tr>

            <tr>
                <td>
                    <label for="usr_user_dname"><?php echo $usr_strings['USelDel']; ?></label>
                    <select class="form-control" id="usr_user_dname" name="usr_user_dname">
                        <?php build_user_options($with_sysdba = false); ?>
                    </select>
                </td>
                <td align="left">
                    <input type="submit" class="btn btn-danger" name="usr_user_del"
                           value="<?php echo $button_strings['Delete']; ?>">
                </td>
            </tr>
        </table>
    </form>
    <?php

endif;

function table_val($val)
{
    $val = (empty($val) && $val !== 0) ? '&nbsp;' : $val;

    return $val;
}

?>
