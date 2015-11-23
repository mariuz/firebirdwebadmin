<?php
// File           adm_restore.php / FirebirdWebAdmin
// Purpose        restore a database from a backup file
// Author         Lutz Brueckner <irie@gmx.de>
// Copyright      (c) 2000, 2001, 2002, 2003, 2004, 2005 by Lutz Brueckner,
//                published under the terms of the GNU General Public Licence v.2,
//                see file LICENCE for details

if ($s_connected == TRUE):

    ?>
    <form method="post" action="<?php echo url_session($_SERVER['PHP_SELF']); ?>" name="adm_restore"
          class="form-inline">
        <table class="table table-bordered">
            <tr>
                <th align="left">
                    <?php echo $adm_strings['RSource']; ?>
                </th>
            </tr>
            <tr>
                <td>
                    <div class="form-group">
                        <label for="adm_re_source"><?php echo $adm_strings['FDName']; ?></label>

                        <div class="input-group">
                            <?php if (defined('BACKUP_DIR') && BACKUP_DIR !== '') echo '<div class="input-group-addon">' . BACKUP_DIR . '</div>'; ?>
                            <?php echo get_textfield('adm_re_source', 50, 256, $s_restore['source']); ?>
                        </div>
                    </div>
                </td>
            </tr>
        </table>

        <table class="table table-bordered">
            <tr>
                <th align="left"><?php echo $adm_strings['RTarget']; ?></th>
            </tr>
            <tr>
                <td>
                    <div class="form-group">
                        <label for="adm_re_target"><?php echo $adm_strings['TargetDB']; ?></label>
                        <?php echo get_textfield('adm_re_target', 50, 256, $s_restore['target']); ?>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <label class="radio-inline">
                        <input type="radio" name="adm_re_overwrite"
                               value="0"<?php if ($s_restore['overwrite'] == FALSE) echo ' checked'; ?>>&nbsp;
                        <?php echo $adm_strings['NewFile']; ?>
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="adm_re_overwrite"
                               value="1"<?php if ($s_restore['overwrite'] == TRUE) echo ' checked'; ?>>&nbsp;
                        <?php echo $adm_strings['RestFile']; ?>
                    </label>
                </td>
            </tr>
        </table>

        <table class="table table-bordered">
            <tr>
                <th colspan="2" align="left"><?php echo $adm_strings['Options']; ?></th>
            </tr>
            <tr>
                <td>
                    <label class="radio-inline">
                        <input type="checkbox" name="adm_re_inactive"
                               value="1"<?php if ($s_restore['inactive'] == TRUE) echo ' checked'; ?>>
                        <?php echo $adm_strings['IdxInact']; ?>
                    </label>
                </td>
                <td>
                    <label class="radio-inline">
                        <input type="checkbox" name="adm_re_novalidity"
                               value="1"<?php if ($s_restore['novalidity'] == TRUE) echo ' checked'; ?>>
                        <?php echo $adm_strings['NoValidity']; ?>
                    </label>
                </td>
            </tr>
            <tr>
                <td>
                    <label class="radio-inline">
                        <input type="checkbox" name="adm_re_oneattime"
                               value="1"<?php if ($s_restore['oneattime'] == TRUE) echo ' checked'; ?>>
                        <?php echo $adm_strings['OneAtTime']; ?>
                    </label>
                </td>
                <td>
                    <label class="radio-inline">
                        <input type="checkbox" name="adm_re_kill"
                               value="1"<?php if ($s_restore['kill'] == TRUE) echo ' checked'; ?>>
                        <?php echo $adm_strings['KillShad']; ?>
                    </label>
                </td>
            </tr>
            <tr>
                <td>
                    <label class="radio-inline">
                        <input type="checkbox" name="adm_re_useall"
                               value="1"<?php if ($s_restore['useall'] == TRUE) echo ' checked'; ?>>
                        <?php echo $adm_strings['UseAll']; ?>
                    </label>
                </td>
                <td>
                    <label class="radio-inline">
                        <input type="checkbox" name="adm_re_verbose"
                               value="1"<?php if ($s_restore['verbose'] == TRUE) echo ' checked'; ?>>
                        <?php echo $adm_strings['Verbose']; ?>
                    </label>
                </td>
            </tr>
        </table>
        <label class="radio-inline">
            <input type="checkbox" name="adm_re_connect"
                   value="1"<?php if ($s_restore['connect'] == TRUE) echo ' checked'; ?>>
            <?php echo $adm_strings['ConnAfter']; ?></label>
        <?php

        if (isset($iframekey_restore)):
            ?>

            <br/>
            <div class="if">
                <iframe src="<?php echo url_session('./iframe_content.php?key=' . $iframekey_restore); ?>" width="98%"
                        height="<?php echo $s_cust['iframeheight']; ?>" name="adm_restore_iframe"></iframe>
            </div>
            <br/>
            <?php

        endif;
        ?>
        <br/>
        <br/>
        <input type="submit" class="btn btn-default" name="adm_restore_doit"
               value="<?php echo $button_strings['Restore']; ?>">
    </form>
    <?php

endif;

?>
