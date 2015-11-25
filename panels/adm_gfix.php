<?php
// File           adm_gfix.php / FirebirdWebAdmin
// Purpose        interface for the gfix command
// Author         Lutz Brueckner <irie@gmx.de>
// Copyright      (c) 2000, 2001, 2002, 2003, 2004, 2005 by Lutz Brueckner,
//                published under the terms of the GNU General Public Licence v.2,
//                see file LICENCE for details

if ($s_connected == true):

    ?>
    <form method="post" action="<?php echo url_session($_SERVER['PHP_SELF']); ?>" name="db_gfix_form"
          class="form-inline">
        <?php

        echo hidden_field('gfix_doit', '1');

        if ($s_login['user'] != 'SYSDBA') {
            sysdba_pw_textfield($adm_strings['SysdbaPW'], $adm_strings['Required'], $s_sysdba_pw);
        }
        ?>
        <table class="table table-bordered">
            <tr>
                <td>

                    <div class="form-group">
                        <label for="adm_buffers"><?php echo $adm_strings['Buffers']; ?></label>
                        <?php echo get_textfield('adm_buffers', 6, 5, $s_gfix['buffers']); ?>
                    </div>
                    <input type="submit" class="btn btn-default" name="adm_gfix_buffers"
                           value="<?php echo $button_strings['Set']; ?>">
                </td>
                <td>

                    <div class="form-group">
                        <label for="adm_sql_dialect"><?php echo $adm_strings['DBDialect']; ?></label>
                        <?php echo get_selectlist('adm_sql_dialect', array(1, 3), $s_gfix['dialect'], true); ?>
                    </div>
                    <input type="submit" class="btn btn-default" name="adm_gfix_dialect"
                           value="<?php echo $button_strings['Set']; ?>">
                </td>
                <td>

                    <div class="form-group">
                        <label for="adm_access_mode"><?php echo $adm_strings['AccessMode']; ?></label>
                        <?php echo get_selectlist('adm_access_mode', array($adm_strings['ReadWrite'], $adm_strings['ReadOnly']), $s_gfix['access_mode'], true); ?>
                    </div>
                    <input type="submit" class="btn btn-default" name="adm_gfix_access_mode"
                           value="<?php echo $button_strings['Set']; ?>">
                </td>
                <td>

                    <div class="form-group">
                        <label for="adm_write_mode"><?php echo $adm_strings['WriteMode']; ?></label>
                        <?php echo get_selectlist('adm_write_mode', array($adm_strings['Sync'], $adm_strings['Async']), $s_gfix['write_mode'], true); ?>
                    </div>
                    <input type="submit" class="btn btn-default" name="adm_gfix_write_mode"
                           value="<?php echo $button_strings['Set']; ?>">
                </td>
                <td>

                    <div class="form-group">
                        <label for="adm_use_space"><?php echo $adm_strings['UseSpace']; ?></label>
                        <?php echo get_selectlist('adm_use_space', array($adm_strings['SmallFull'], $adm_strings['Reserve']), $s_gfix['use_space'], true); ?>
                    </div>
                    <input type="submit" class="btn btn-default" name="adm_gfix_use_space"
                           value="<?php echo $button_strings['Set']; ?>">
                </td>
            </tr>
        </table>
        <br>

        <table class="table table-bordered">
            <tr>
                <th align="left" colspan="2"><?php echo $adm_strings['Sweeping']; ?></th>
            </tr>
            <tr>
                <td width="50%">
                    <div class="form-group">
                        <label for="adm_housekeeping"><?php echo $adm_strings['SetInterv']; ?></label>
                        <input type="text" class="form-control" size="9" maxlength="8" id="adm_housekeeping"
                               name="adm_housekeeping" value="<?php echo $s_gfix['sweep_interval']; ?>">
                    </div>
                    <input type="submit" class="btn btn-default" name="adm_gfix_housekeeping"
                           value="<?php echo $button_strings['Set']; ?>">
                </td>
                <td width="50%">
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" name="adm_sweep_ignore"
                                   value="1"<?php if ($s_gfix['sweep_ignore']) {
    echo ' checked';
} ?>> <?php echo $adm_strings['IgnoreChk']; ?>
                        </label>
                    </div>
                    <input type="submit" class="btn btn-default margin-left-10px" name="adm_gfix_sweep"
                           value="<?php echo $button_strings['SweepNow']; ?>">
                </td>
            </tr>
        </table>

        <table class="table table-bordered">
            <tr>
                <th align="left"><?php echo $adm_strings['DataRepair']; ?></th>
            </tr>
            <tr>
                <td>
                    <label class="radio-inline">
                        <input type="radio" name="adm_repair"
                               value="mend"<?php if ($s_gfix['repair'] == 'mend') {
    echo ' checked';
} ?>> <?php echo $adm_strings['Mend']; ?>
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="adm_repair"
                               value="validate"<?php if ($s_gfix['repair'] == 'validate') {
    echo ' checked';
} ?>> <?php echo $adm_strings['Validate']; ?>
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="adm_repair"
                               value="full"<?php if ($s_gfix['repair'] == 'full') {
    echo ' checked';
} ?>> <?php echo $adm_strings['Full']; ?>
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="adm_repair"
                               value="no_update"<?php if ($s_gfix['repair'] == 'no_update') {
    echo ' checked';
} ?>> <?php echo $adm_strings['NoUpdate']; ?>
                    </label>
                    <label class="radio-inline">
                        <input type="checkbox" name="adm_repair_ignore"
                               value="1"<?php if ($s_gfix['repair_ignore']) {
    echo ' checked';
} ?>> <?php echo $adm_strings['IgnoreChk']; ?>
                    </label>
                    <input type="submit" class="btn btn-default margin-left-10px" name="adm_gfix_repair"
                           value="<?php echo $button_strings['Execute']; ?>">
                </td>
            </tr>
        </table>

        <table class="table table-bordered">
            <tr>
                <th align="left" colspan="7"><?php echo $adm_strings['Shutdown']; ?></th>
            </tr>
            <tr>
                <td>
                    <label class="radio-inline">
                        <input type="radio" name="adm_shutdown"
                               value="noconns"<?php if ($s_gfix['_shutdown'] == 'noconns') {
    echo ' checked';
} ?>>&nbsp;
                        <?php echo $adm_strings['NoConns']; ?>
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="adm_shutdown"
                               value="notrans"<?php if ($s_gfix['shutdown'] == 'notrans') {
    echo ' checked';
} ?>>&nbsp;
                        <?php echo $adm_strings['NoTrans']; ?>
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="adm_shutdown"
                               value="force"<?php if ($s_gfix['shutdown'] == 'force') {
    echo ' checked';
} ?>>&nbsp;
                        <?php echo $adm_strings['Force']; ?>
                    </label>
                    <div class="form-group">
                        <label for="adm_shut_secs"
                               class="margin-left-10px"><?php echo $adm_strings['ForSeconds']; ?></label>
                        <?php echo get_textfield('adm_shut_secs', 5, 4, $s_gfix['shutdown_seconds']) ?>
                    </div>
                    <input type="submit" class="btn btn-default margin-left-10px" name="adm_gfix_shutdown"
                           value="<?php echo $button_strings['Execute']; ?>">
                    <input type="submit" class="btn btn-default" name="adm_gfix_rescind"
                           value="<?php echo $adm_strings['Rescind']; ?>">
                </td>

            </tr>
            <tr>
                <td>
                    <label class="radio-inline">
                        <input type="checkbox" name="adm_shut_reconnect"
                               value="1"<?php if ($s_gfix['reconnect']) {
    echo ' checked';
} ?>>
                        <?php echo $adm_strings['Reconnect']; ?>&nbsp;
                    </label>
                </td>
            </tr>
        </table>
    </form>
    <?php
endif;
?>
