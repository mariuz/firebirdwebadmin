<?php
// File           usr_role.php / FirebirdWebAdmin
// Purpose        html sequence for the roles-panel in user.php
// Author         Lutz Brueckner <irie@gmx.de>
// Copyright      (c) 2000, 2001, 2002, 2003, 2004 by Lutz Brueckner,
//                published under the terms of the GNU General Public Licence v.2,
//                see file LICENCE for details

if (isset($s_confirmations['role'])) {
    $subject = 'role';
    include 'panels/confirm.php';
} elseif ($s_connected) {
    ?>
    <form method="post" action="<?php echo url_session($_SERVER['PHP_SELF']);
    ?>" name="usr_role_form">
        <?php

        if (!empty($roles)) {
            ?>
        <table class="table table-bordered">
            <tr>
                <th><?php echo $usr_strings['Name'];
            ?></th>
                <th><?php echo $usr_strings['Owner'];
            ?></th>
                <th><?php echo $usr_strings['Members'];
            ?></th>
            </tr>
            <?php

            foreach ($roles as $name => $role) {
                $members_str = (count($role['members']) > 0) ? implode(', ', $role['members']) : '<i>none</i>';
                ?>
                <tr>
                    <td><strong><?php echo $name;
                ?></strong></td>
                    <td><?php echo $role['owner'];
                ?></td>
                    <td><?php echo $members_str;
                ?></td>
                </tr>
            <?php

            }
            echo '</table>';
        }

    ?>
            <p>
            <table class="table table-bordered">
                <tr>
                    <th align="left"><strong><?php echo $usr_strings['CreateRole'];
    ?></strong></th>
                    <td><strong><?php echo $usr_strings['Name'];
    ?></strong><br>
                        <input type="text" class="form-control" size="32" maxlength="31" name="usr_role_name">
                    </td>
                    <td>
                        <input type="submit" class="btn btn-success" name="usr_role_create" value="<?php echo $button_strings['Create'];
    ?>">
                    </td>
                </tr>
                <tr>
                    <th align="left"><strong><?php echo $usr_strings['RoleSelDel'];
    ?></strong></th>
                    <td>
                        <strong><?php echo $usr_strings['Name'];
    ?></strong><br>
                        <select class="form-control" name="usr_role_dname">
                            <?php

                            $selected = (isset($_POST['usr_role_dname'])) ? $_POST['usr_role_dname'] : '';
    build_roles_options($roles, $selected);

    ?>
                        </select>
                    </td>
                    <td>
                        <input type="submit" class="btn btn-danger" name="usr_role_del" value="<?php echo $button_strings['Delete'];
    ?>">
                    </td>
                </tr>
            </table>
            <p>
            <table class="table table-bordered">
                <tr>
                    <th align="left"><strong><?php echo $usr_strings['RoleAdd'];
    ?></strong></th>
                    <td>
                        <strong><?php echo $usr_strings['Role'];
    ?></strong><br>
                        <select class="form-control" name="usr_role_addname">
                            <?php

                            $selected = (isset($_POST['usr_role_addname'])) ? $_POST['usr_role_addname'] : '';
    build_roles_options($roles, $selected);
    ?>
                        </select>
                    </td>
                    <td>
                        <strong><?php echo $usr_strings['User'];
    ?></strong><br>
                        <?php

                        $pre = (isset($_POST['usr_role_adduser'])) ? $_POST['usr_role_adduser'] : null;
    if (!empty($users)) {
        $user_options = array_keys($users);
        array_push($user_options, 'PUBLIC');
        echo get_selectlist('usr_role_adduser', $user_options, $pre, true);
    } else {
        echo get_textfield('usr_role_adduser', 20, 80, $pre);
    }
    ?>
                    </td>
                    <td>
                        <input type="submit" class="btn btn-success" name="usr_role_add" value="<?php echo $button_strings['Add'];
    ?>">
                    </td>
                </tr>

                <tr>
                    <th align="left"><strong><?php echo $usr_strings['RoleRem'];
    ?></strong></th>
                    <td>
                        <strong><?php echo $usr_strings['Role'];
    ?></strong><br>
                        <select class="form-control" name="usr_role_removename">
                            <?php

                            $selected = (isset($_POST['usr_role_removename'])) ? $_POST['usr_role_removename'] : '';
    build_roles_options($roles, $selected);
    ?>
                        </select>
                    </td>
                    <td>
                        <strong><?php echo $usr_strings['User'];
    ?></strong><br>
                        <?php

                        $pre = (!empty($_POST['usr_role_removeuser'])) ? $_POST['usr_role_removeuser'] : null;
    if (!empty($users)) {
        echo get_selectlist('usr_role_removeuser', $user_options, $pre, true);
    } else {
        echo get_textfield('usr_role_removeuser', 20, 80, $pre);
    }
    ?>
                    </td>
                    <td>
                        <input type="submit" class="btn btn-danger" name="usr_role_remove" value="<?php echo $button_strings['Remove'];
    ?>">
                    </td>
                </tr>
            </table>
    </form>
<?php

}
?>
