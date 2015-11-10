<?php
// File           db_delete.php / FirebirdWebAdmin
// Purpose        html sequence for the db_delete-panel in database.php
// Author         Lutz Brueckner <irie@gmx.de>
// Copyright      (c) 2000, 2001, 2002, 2003, 2004 by Lutz Brueckner,
//                published under the terms of the GNU General Public Licence v.2,
//                see file LICENCE for details


if (isset($s_confirmations['database'])):
    $subject = 'database';
    include_once('./panels/confirm.php');

else:

    ?>
    <form method="post" action="<?php echo url_session($_SERVER['PHP_SELF']); ?>" name="db_delete_form">
        <table class="table table-bordered">
            <tr>
                <td><label for="db_delete_database"><?php echo $db_strings['DelDB']; ?></label><br>
                    <?php

                    if (count($dbfiles) == 0):
                        ?>
                        <input type="text" class="form-control" size="35" maxlength="255" id="db_delete_database" name="db_delete_database" value="<?php echo $s_delete_db['database']; ?>">
                    <?php

                    else:
                        echo get_selectlist('db_delete_database', $dbfiles, $s_delete_db['database'], TRUE);
                    endif;
                    ?>
                </td>
                <td>
                    <label for="db_delete_host"><?php echo $db_strings['Host']; ?></label><br>
                    <input type="text" class="form-control" size="35" maxlength="255" id="db_delete_host" name="db_delete_host" value="<?php echo $s_delete_db['host']; ?>">
                </td>

            </tr>
            <tr>
                <td>
                    <label for="db_delete_user"><?php echo $db_strings['Username']; ?></label><br>
                    <input type="text" class="form-control" size="35" maxlength="32" id="db_delete_user" name="db_delete_user" value="<?php echo $s_delete_db['user']; ?>">
                </td>
                <td>
                    <label for="db_delete_password"><?php echo $db_strings['Password']; ?></label><br>
                    <input type="password" class="form-control" size="35" maxlength="32" id="db_delete_password" name="db_delete_password" value="<?php echo password_stars($s_delete_db['password']); ?>">
                </td>
            </tr>
        </table>
        <input type="submit" class="btn btn-danger" name="db_delete_doit" value="<?php echo $button_strings['Delete']; ?>">
    </form>
<?php

endif;

?>
