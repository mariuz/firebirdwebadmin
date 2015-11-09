<?php
// File           db_create.php / FirebirdWebAdmin
// Purpose        html sequence for the db_create-panel in database.php
// Author         Lutz Brueckner <irie@gmx.de>
// Copyright      (c) 2000, 2001, 2002, 2003, 2004 by Lutz Brueckner,
//                published under the terms of the GNU General Public Licence v.2,
//                see file LICENCE for details

?>
<form method="post" action="<?php echo url_session($_SERVER['PHP_SELF']); ?>" name="db_create_form">
    <table class="table table-bordered">
        <tr>
            <td><label for="db_create_database"><?php echo $db_strings['NewDB']; ?></label><br>
                <input type="text" class="form-control" size="35" maxlength="255" id="db_create_database" name="db_create_database" value="<?php echo $s_create_db; ?>">
            </td>
            <td><label for="db_create_host"><?php echo $db_strings['Host']; ?></label><br>
                <input type="text" class="form-control" size="35" maxlength="255" id="db_create_host" name="db_create_host" value="<?php echo $s_create_host; ?>">
            </td>
        </tr>
        <tr>
            <td>
                <label for="db_create_user"><?php echo $db_strings['Username']; ?></label><br>
                <input type="text" class="form-control" size="35" maxlength="32" id="db_create_user" name="db_create_user" value="<?php echo $s_create_user; ?>">
            </td>
            <td>
                <label for="db_create_password"><?php echo $db_strings['Password']; ?></label><br>
                <input type="password" class="form-control" size="35" maxlength="32" id="db_create_password" name="db_create_password" value="<?php echo password_stars($s_create_pw); ?>">
            </td>
        </tr>
        <tr>
            <td>
                <label for="db_create_pagesize"><?php echo $db_strings['PageSize']; ?></label><br>
                <?php
                echo get_selectlist('db_create_pagesize', $pagesizes, $s_create_pagesize, TRUE);
                ?>
            </td>
            <td>
                <label for="db_create_charset"><?php echo $db_strings['Charset']; ?></label><br>
                <?php echo get_charset_select('db_create_charset', $s_create_charset); ?>
            </td>
        </tr>
    </table>
    <input type="submit" class="btn btn-primary" name="db_create_doit" value="<?php echo $button_strings['Create']; ?>">
</form>
