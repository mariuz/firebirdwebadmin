<?php
// File           tb_watch.php / FirebirdWebAdmin
// Purpose        html sequence for the table-watch-panel in sql.php and data.php
// Author         Lutz Brueckner <irie@gmx.de>
// Copyright      (c) 2000, 2001, 2002, 2003, 2004, 2005 by Lutz Brueckner,
//                published under the terms of the GNU General Public Licence v.2,
//                see file LICENCE for details

if (!isset($tb_watch_cfg_flag) && $s_connected):

    ?>
    <form method="post" action="<?php echo url_session($_SERVER['PHP_SELF']); ?>" name="tb_watch_form" class="form-inline">
		<div class="form-group">
		<label for="tb_watch_table"><?php echo $sql_strings['SelTable'];?></label>
		<?php echo get_table_selectlist('tb_watch_table', array('select'), $s_wt['table'], true); ?>
		<input type="submit" class="btn btn-default" name="tb_watch_select" value="<?php echo $button_strings['Select']; ?>">

		<?php
        if (isset($s_wt['table']) && $s_wt['table'] != '') {
            $url = url_session($_SERVER['PHP_SELF'].'?wcfg=true');
            echo '<a class="btn btn-link" href="'.$url.'">['.$sql_strings['Config'].']</a>';
        }
        ?>
		</div>
    </form>
	<?php
    display_table($s_wt);
    ?>
    <div id="fk" class="fk"></div>
<?php

//
// Configuration panel
//
elseif ($s_connected):

    ?>
    <form method="post" action="<?php echo url_session($_SERVER['PHP_SELF']); ?>" name="tb_watch_form">
 
		<?php watchtable_column_options($s_wt['table'],
            $s_wt['columns'],
            $s_wt['order'],
            $s_wt['blob_links'],
            $s_wt['blob_as']
        );
        ?>

        <table class="table table-bordered">
			<thead>
				<tr>
					<th><label for="tb_watch_rows"><?php echo $sql_strings['Rows']; ?></label></th>
					<th><label for="tb_watch_start"><?php echo $sql_strings['Start']; ?></label></th>
					<th><label for="tb_watch_direction"><?php echo $sql_strings['Dir']; ?></label></th>
					<th><label for="tb_watch_edit"><?php echo $sql_strings['ELinks']; ?></label></th>
					<th><label for="tb_watch_del"><?php echo $sql_strings['DLinks']; ?></label></th>
				</tr>
			</thead>
            <tr>
                <td align="center">
                    <input type="text" size="4" maxlength="4" class="form-control" id="tb_watch_rows" name="tb_watch_rows" value="<?php echo $s_wt['rows']; ?>">
                </td>
                <td align="center">
                    <input type="text" size="8" maxlength="8" class="form-control" id="tb_watch_start" name="tb_watch_start" value="<?php echo $s_wt['start']; ?>">
                </td>
                <td align="center">
                    <?php echo get_selectlist('tb_watch_direction',
                        array($sql_strings['Asc'], $sql_strings['Desc']),
                        $s_wt['direction'] == 'ASC' ? $sql_strings['Asc'] : $sql_strings['Desc']);
                    ?>
                </td>
                <td align="center">
                    <?php echo get_yesno_selectlist('tb_watch_edit', $s_wt['edit'] ? 'Yes' : 'No'); ?>
                </td>
                <td align="center">
                    <?php echo get_yesno_selectlist('tb_watch_del', $s_wt['delete'] ? 'Yes' : 'No'); ?>
                </td>
            </tr>
        </table>
        <table class="table table-bordered">
			<thead>
				<tr>
					<th><label for="tb_watch_tblob_inline"><?php echo $sql_strings['TBInline']; ?></label></th>
					<th><label for="tb_watch_tblob_chars"><?php echo $sql_strings['TBChars']; ?></label></th>
				</tr>
			</thead>
            <tr>
                <td align="center">
                    <?php echo get_yesno_selectlist('tb_watch_tblob_inline', $s_wt['delete'] ? 'Yes' : 'No'); ?>
                </td>
                <td align="center">
                    <input type="text" class="form-control" size="4" maxlength="4" id="tb_watch_tblob_chars" name="tb_watch_tblob_chars" value="<?php echo $s_wt['tblob_chars']; ?>">
                </td>
            </tr>
        </table>
        <table class="table table-bordered">
			<thead>
				<tr>
					<th align="left"><label for="tb_watch_condition"><?php echo $sql_strings['Restrict']; ?></label></th>
				</tr>
			</thead>
            <tr>
                <td>
                    <input type="text" class="form-control" size="60" maxlength="256" id="tb_watch_condition" name="tb_watch_condition" value="<?php echo $s_wt['condition']; ?>">
                <td>
            </tr>
        </table>
        <input type="submit" class="btn btn-success" name="tb_watch_cfg_doit" value="<?php echo $button_strings['Ready']; ?>" class="bgrp">
        <input type="submit" class="btn btn-default"  name="tb_watch_cfg_cancel" value="<?php echo $button_strings['Cancel']; ?>" class="bgrp">
    </form>
<?php
endif;
?>
