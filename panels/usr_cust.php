<?php
// File           panels/usr_cust.php / FirebirdWebAdmin
// Purpose        html for the customizing-panel in user.php
// Author         Lutz Brueckner <irie@gmx.de>
// Copyright      (c) 2000, 2001, 2002, 2003, 2004, 2005 by Lutz Brueckner,
//                published under the terms of the GNU General Public Licence v.2,
//                see file LICENCE for details

if (empty($_COOKIE)):

    echo '<div class="alert alert-danger" role="alert">' . $MESSAGES['COOKIES_NEEDED'] . "</div>";

else: ?>
    <form method="post" action="<?php echo url_session($_SERVER['PHP_SELF']); ?>" name="usr_role_form"
          class="form-horizontal">
        <div class="form-group">
            <h4><?php echo $usr_strings['Appearance']; ?></h4>
        </div>
        <div class="form-group">
            <label for="usr_cust_language"
                   class="col-sm-2 control-label"><?php echo $usr_strings['Language']; ?></label>

            <div class="col-sm-2">
                <?php echo get_selectlist('usr_cust_language', get_customize_languages(), $s_cust['language']); ?>
            </div>
        </div>
        <div class="form-group">
            <h4><?php echo $usr_strings['Attitude']; ?></h4>
        </div>
        <div class="form-group">
            <label for="usr_cust_askdel" class="col-sm-2 control-label"><?php echo $usr_strings['AskDel']; ?></label>

            <div class="col-sm-2">
                <?php echo get_selectlist('usr_cust_askdel', array($usr_strings['Yes'], $usr_strings['No']), ($s_cust['askdel'] == 1 ? $usr_strings['Yes'] : $usr_strings['No'])); ?>
            </div>
        </div>


        <input type="submit" class="btn btn-success" name="usr_cust_save"
               value="<?php echo $button_strings['Save']; ?>">&nbsp;&nbsp;&nbsp;
        <input type="submit" class="btn btn-default" name="usr_cust_defaults"
               value="<?php echo $button_strings['Defaults']; ?>">
    </form>
<?php endif; ?>
