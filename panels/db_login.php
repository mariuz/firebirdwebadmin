<?php
// Purpose        html sequence for the db_login-panel in database.php
// Author         Lutz Brueckner <irie@gmx.de>
// Copyright      (c) 2000, 2001, 2002, 2003, 2004, 2005 by Lutz Brueckner,
//                published under the terms of the GNU General Public Licence v.2,
//                see file LICENCE for details

?>
<form method="post" action="<?php echo url_session($_SERVER['PHP_SELF']); ?>" name="db_login_form">
    <table class="table table-bordered">
        <tr>
            <td colspan="2"><label for="db_login_database"><?php echo $db_strings['Database']; ?></label><br>
                <?php
                if (count($dbfiles) == 0) {
                    echo get_textfield('db_login_database', '35', '128', $s_login['database']);
                } else {
                    echo get_selectlist('db_login_database', $dbfiles, $s_login['database'], true);
                }
                ?>
            </td>
            <td colspan="3"><label for="db_login_host"><?php echo $db_strings['Host']; ?></label><br>
                <?php echo get_textfield('db_login_host', '35', '128', $s_login['host']); ?>
            </td>

        </tr>
        <tr>
            <td colspan="2"><label for="db_login_user"><?php echo $db_strings['Username']; ?></label><br>
                <?php echo get_textfield('db_login_user', 35, 31, $s_login['user']); ?>
            </td>
            <td colspan="3"><label for="db_login_password"><?php echo $db_strings['Password']; ?></label><br>
                <?php echo get_textfield('db_login_password', 35, 32, $s_login['password'], 'password'); ?>
            </td>
        </tr>
        <tr>
            <td><label for="db_login_role"><?php echo $db_strings['Role']; ?></label><br>
                <?php echo get_textfield('db_login_role', 28, 32, $s_login['role']); ?>
            </td>
            <td><label for="db_login_cache"><?php echo $db_strings['Cache']; ?></label><br>
                <?php echo get_textfield('db_login_cache', 5, 5, $s_login['cache']); ?>
            </td>
            <td><label for="db_login_charset"><?php echo $db_strings['Charset']; ?></label><br>
                <?php echo get_charset_select('db_login_charset', $s_login['charset']); ?>
            </td>
            <td><label for="db_login_dialect"><?php echo $db_strings['Dialect']; ?></label><br>
                <?php echo get_selectlist('db_login_dialect', array(1, 3), $s_login['dialect'], true); ?>
            </td>
            <td><label for="db_login_server"><?php echo $db_strings['Server']; ?></label><br>
                <?php echo get_selectlist('db_login_server', $server_types, $s_login['server'], true); ?>
            </td>
        </tr>
    </table>
	<input class="btn btn-success" type="submit" name="db_login_doit" value="<?php echo $button_strings['Login']; ?>">
	<?php
    if ($s_connected == true) {
        echo '<input class="btn btn-warning" type="submit" name="db_logout_doit" value="'.$button_strings['Logout']."\">\n";
    }
    ?>
</form>
