<?php
// File           acc_view.php / FirebirdWebAdmin
// Purpose        html sequence for the views-panel in accessories.php
// Author         Lutz Brueckner <irie@gmx.de>
// Copyright      (c) 2000-2006 by Lutz Brueckner,
//                published under the terms of the GNU General Public Licence v.2,
//                see file LICENCE for details
// Created        <03/01/13 17:47:05 lb>
//
// $Id: acc_view.php,v 1.12 2006/03/14 21:07:04 lbrueckner Exp $

if (isset($s_confirmations['view'])) {
    $subject = 'view';
    include('./panels/confirm.php');
}

elseif (isset($view_add_flag)) {

?>
<form method="post" action="<?php echo url_session($_SERVER['PHP_SELF']); ?>" name="acc_view_form">
<?php echo view_definition($acc_strings['CreateView'], $s_viewdefs); ?>
<input type="submit" name="acc_view_create_doit" value="<?php echo $button_strings['Create']; ?>" class="bgrp">
<input type="reset" name="acc_view_create_clear" value="<?php echo $button_strings['Reset']; ?>" class="bgrp">
<input type="submit" name="acc_view_create_cancel" value="<?php echo $button_strings['Cancel']; ?>" class="bgrp">
</form>
<?php

} elseif (isset($view_mod_flag)) {

?>
<form method="post" action="<?php echo url_session($_SERVER['PHP_SELF']); ?>" name="acc_modview_form">
<?php echo view_definition(sprintf($acc_strings['ModView'], $s_viewdefs['name']), $viewdefs); ?>
<input type="submit" name="acc_modview_doit" value="<?php echo $button_strings['Modify']; ?>" class="bgrp">
<input type="reset" name="acc_view_mod_clear" value="<?php echo $button_strings['Reset']; ?>" class="bgrp">
<input type="submit" name="acc_modview_cancel" value="<?php echo $button_strings['Cancel']; ?>" class="bgrp">
</form>

<?php

} elseif ($s_connected == TRUE) {
    if (is_array($s_tables)) {
        $vcnt = 0;
        foreach($s_tables as $viewname => $properties) {
            if ($properties['is_view'] == FALSE) {
                continue;
            }
            $vcnt++;

            $title    = ($s_views_counts == TRUE  &&  isset($properties['count'])) ? $viewname.'&nbsp;['.$properties['count'].']' : $viewname;
            $fold_url = fold_detail_url('view', $properties['status'], $viewname, $title);

            echo '<div id="'.'v_'.$viewname."\" class=\"det\">\n";

            if ($properties['status'] == 'open') {

                echo get_opened_view($viewname, $title, $fold_url);
            }
            else {   // $properties['status'] == 'close'

                echo get_closed_detail($title, $fold_url);
            }

            echo "</div>\n";

        }    // foreach $s_tables
    }
    echo '<form method="post" action="'.url_session($_SERVER['PHP_SELF'])."\" name=\"acc_view_form\">\n";

    if ($vcnt > 0) {
?>
<input type="checkbox" name="acc_show_counts" value="yes"<?php if ($s_views_counts == TRUE) echo ' checked'; ?>>
<?php echo $tb_strings['DispCounts']; ?><br />
<input type="submit" name="acc_show_reload" value="<?php echo $button_strings['Reload']; ?>">&nbsp;&nbsp;&nbsp;
<?php 
     if ($vcnt > 1) {
         echo '<input type="submit" name="acc_view_open" value="'.$button_strings['OpenAll']."\" class=\"bgrp\">\n";
         echo '<input type="submit" name="acc_view_close" value="'.$button_strings['CloseAll']."\" class=\"bgrp\">\n";
         
        }
        echo "<br><br>\n";
    }
?>
<table border="1" cellpadding="3" cellspacing="0">
<tr>
  <th colspan="2" align="left"><?php echo $acc_strings['CreateView']; ?></th>
  <td><input type="submit" name="acc_view_create" value="<?php echo $button_strings['Create']; ?>"></td>
</tr>
<tr>
  <td>
    <b><?php echo $acc_strings['SelViewMod']; ?></b>
  </td>
  <td>
    <?php echo get_table_selectlist('acc_modview_name', array('owner', 'views'), NULL, TRUE); ?>
  </td>
  <td align="left">
    <input type="submit" name="acc_view_mod" value="<?php echo $button_strings['Modify']; ?>">
  </td>
</tr>
<tr>
  <td>
    <b><?php echo $acc_strings['SelViewDel']; ?></b>
  </td>
  <td>
    <?php echo get_table_selectlist('acc_delview_name', array('owner', 'views'), NULL, TRUE); ?>
  </td>
  <td align="left">
    <input type="submit" name="acc_view_del" value="<?php echo $button_strings['Delete']; ?>">
  </td>
</tr>
</table>
</form>
<?php

}

?>
