<?php
// File           adm_gfix.php / FirebirdWebAdmin
// Purpose        interface for the gfix command
// Author         Lutz Brueckner <irie@gmx.de>
// Copyright      (c) 2000, 2001, 2002, 2003, 2004, 2005 by Lutz Brueckner,
//                published under the terms of the GNU General Public Licence v.2,
//                see file LICENCE for details

if ($s_connected == TRUE):

    ?>
    <form method="post" action="<?php echo url_session($_SERVER['PHP_SELF']); ?>" name="db_gfix_form">
        <?php

        echo hidden_field('gfix_doit', '1');

        if ($s_login['user'] != 'SYSDBA') {
            sysdba_pw_textfield($adm_strings['SysdbaPW'], $adm_strings['Required'], $s_sysdba_pw);
        }
        ?>
        <table>
            <tr>
                <td>
                    <table class="table table-bordered">
                        <tr>
                            <th align="left" colspan="3"><?php echo $adm_strings['Buffers']; ?></th>
                        <tr>
                            <td>
                                <?php echo get_textfield('adm_buffers', 6, 5, $s_gfix['buffers']); ?>
                                <input type="submit" class="btn btn-default" name="adm_gfix_buffers" value="<?php echo $button_strings['Set']; ?>">
                            </td>
                        </tr>
                    </table>
                </td>
                <td>
                    <table class="table table-bordered">
                        <tr>
                            <th align="left" colspan="3"><?php echo $adm_strings['DBDialect']; ?></th>
                        </tr>
                        <tr>
                            <td>
                                <?php echo get_selectlist('adm_sql_dialect', array(1, 3), $s_gfix['dialect'], TRUE); ?>
                                <input type="submit" class="btn btn-default" name="adm_gfix_dialect" value="<?php echo $button_strings['Set']; ?>">
                            </td>
                        </tr>
                    </table>
                </td>
                <td>
                    <table class="table table-bordered">
                        <tr>
                            <th align="left" colspan="3"><?php echo $adm_strings['AccessMode']; ?></th>
                        </tr>
                        <tr>
                            <td>
                                <?php echo get_selectlist('adm_access_mode', array($adm_strings['ReadWrite'], $adm_strings['ReadOnly']), $s_gfix['access_mode'], TRUE); ?>
                                <input type="submit" class="btn btn-default" name="adm_gfix_access_mode" value="<?php echo $button_strings['Set']; ?>">
                            </td>
                        </tr>
                    </table>
                </td>
                <td>
                    <table class="table table-bordered">
                        <tr>
                            <th align="left" colspan="3"><?php echo $adm_strings['WriteMode']; ?></th>
                        </tr>
                        <tr>
                            <td>
                                <?php echo get_selectlist('adm_write_mode', array($adm_strings['Sync'], $adm_strings['Async']), $s_gfix['write_mode'], TRUE); ?>
                                <input type="submit" class="btn btn-default" name="adm_gfix_write_mode" value="<?php echo $button_strings['Set']; ?>">
                            </td>
                        </tr>
                    </table>
                </td>
                <td>
                    <table class="table table-bordered">
                        <tr>
                            <th align="left" colspan="3"><?php echo $adm_strings['UseSpace']; ?></th>
                        </tr>
                        <tr>
                            <td>
                                <?php echo get_selectlist('adm_use_space', array($adm_strings['SmallFull'], $adm_strings['Reserve']), $s_gfix['use_space'], TRUE); ?>
                                <input type="submit" class="btn btn-default" name="adm_gfix_use_space" value="<?php echo $button_strings['Set']; ?>">
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <br>

        <table class="table table-bordered">
            <tr>
                <th align="left" colspan="5"><?php echo $adm_strings['Sweeping']; ?></th>
            </tr>
            <tr>
                <td><?php echo $adm_strings['SetInterv']; ?>&nbsp;
                    <input type="text" class="form-control" size="9" maxlength="8" name="adm_housekeeping" value="<?php echo $s_gfix['sweep_interval']; ?>">
                    <input type="submit" class="btn btn-default" name="adm_gfix_housekeeping" value="<?php echo $button_strings['Set']; ?>">
                </td>
                <td width="20">&nbsp;</td>
                <td>
                    <input type="checkbox" name="adm_sweep_ignore" value="1"<?php if ($s_gfix['sweep_ignore']) echo ' checked'; ?>>&nbsp;
                    <?php echo $adm_strings['IgnoreChk']; ?>
                </td>
                <td width="20">&nbsp;</td>
                <td>
                    <input type="submit" class="btn btn-default" name="adm_gfix_sweep" value="<?php echo $button_strings['SweepNow']; ?>">
                </td>
            </tr>
        </table>
        <br>

        <table class="table table-bordered">
            <tr>
                <th align="left" colspan="9"><?php echo $adm_strings['DataRepair']; ?></th>
            </tr>
            <tr>
                <td>
                    <input type="radio" name="adm_repair" value="mend"<?php if ($s_gfix['repair'] == 'mend') echo ' checked'; ?>>&nbsp;
                    <?php echo $adm_strings['Mend']; ?>
                </td>
                <td>
                    <input type="radio" name="adm_repair" value="validate"<?php if ($s_gfix['repair'] == 'validate') echo ' checked'; ?>>&nbsp;
                    <?php echo $adm_strings['Validate']; ?>
                </td>
                <td>
                    <input type="radio" name="adm_repair" value="full"<?php if ($s_gfix['repair'] == 'full') echo ' checked'; ?>>&nbsp;
                    <?php echo $adm_strings['Full']; ?>
                </td>
                <td>
                    <input type="radio" name="adm_repair" value="no_update"<?php if ($s_gfix['repair'] == 'no_update') echo ' checked'; ?>>&nbsp;
                    <?php echo $adm_strings['NoUpdate']; ?>
                </td>
                <td width="20">&nbsp;</td>
                <td>
                    <input type="checkbox" name="adm_repair_ignore" value="1"<?php if ($s_gfix['repair_ignore']) echo ' checked'; ?>>&nbsp;
                    <?php echo $adm_strings['IgnoreChk']; ?>
                </td>
                <td width="20">&nbsp;</td>
                <td>
                    <input type="submit" class="btn btn-default" name="adm_gfix_repair" value="<?php echo $button_strings['Execute']; ?>">
                </td>
            </tr>
        </table>
        <br>

        <table class="table table-bordered">
            <tr>
                <th align="left" colspan="7"><?php echo $adm_strings['Shutdown']; ?></th>
            </tr>
            <tr>
                <td>
                    <input type="radio" name="adm_shutdown" value="noconns"<?php if ($s_gfix['_shutdown'] == 'noconns') echo ' checked'; ?>>&nbsp;
                    <?php echo $adm_strings['NoConns']; ?>
                </td>
                <td>
                    <input type="radio" name="adm_shutdown" value="notrans"<?php if ($s_gfix['shutdown'] == 'notrans') echo ' checked'; ?>>&nbsp;
                    <?php echo $adm_strings['NoTrans']; ?>
                </td>
                <td>
                    <input type="radio" name="adm_shutdown" value="force"<?php if ($s_gfix['shutdown'] == 'force') echo ' checked'; ?>>&nbsp;
                    <?php echo $adm_strings['Force']; ?>
                </td>
                <td width="20">&nbsp;</td>
                <td>
                    <?php echo $adm_strings['ForSeconds'] . '&nbsp;' . get_textfield('adm_shut_secs', 5, 4, $s_gfix['shutdown_seconds']) ?>
                <td width="20">&nbsp;</td>
                <td>
                    <input type="submit" class="btn btn-default" name="adm_gfix_shutdown" value="<?php echo $button_strings['Execute']; ?>">
                </td>
            </tr>
            <tr>
                <td colspan="5" align="right">
                    <?php echo $adm_strings['Rescind']; ?>&nbsp;
                </td>
                <td width="20">&nbsp;</td>
                <td>
                    <input type="submit" class="btn btn-default" name="adm_gfix_rescind" value="<?php echo $button_strings['Execute']; ?>">
                </td>
            </tr>
            <tr>
                <td colspan="7">
                    <input type="checkbox" name="adm_shut_reconnect" value="1"<?php if ($s_gfix['reconnect']) echo ' checked'; ?>>
                    <?php echo $adm_strings['Reconnect']; ?>&nbsp;
                </td>
            </tr>
        </table>
    </form>
<?php
endif;
?>
