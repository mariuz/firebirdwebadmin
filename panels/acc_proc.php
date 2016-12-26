<?php
// File           acc_procr.php / FirebirdWebAdmin
// Purpose        html sequence for the procedures panel on the accessories page
// Author         Lutz Brueckner <irie@gmx.de>
// Copyright      (c) 2000-2006 by Lutz Brueckner,
//                published under the terms of the GNU General Public Licence v.2,
//                see file LICENCE for details

if (isset($s_confirmations['procedure'])):
    $subject = 'procedure';
    include_once './panels/confirm.php';

elseif (isset($proc_add_flag)):

?>
<form method="post" action="<?php echo url_session($_SERVER['PHP_SELF']); ?>" name="create_proc_form">
<?php
      echo get_procedure_definition($acc_strings['CreateProc'], $s_proceduredefs['source']);
 ?>
<input type="submit" name="acc_proc_create_doit" value="<?php echo $button_strings['Create']; ?>" class="btn btn-success">
<input type="reset" name="acc_proc_create_clear" value="<?php echo $button_strings['Reset']; ?>" class="btn btn-default">
<input type="submit" name="acc_proc_create_cancel" value="<?php echo $button_strings['Cancel']; ?>" class="btn btn-default">
</form>
<?php

elseif (isset($proc_mod_flag)):

?>
<form method="post" action="<?php echo url_session($_SERVER['PHP_SELF']); ?>" name="modify_proc_form">
<?php
      echo get_procedure_definition(sprintf($acc_strings['ModProc'], $s_proceduredefs['name']),
                                    $s_proceduredefs['source']
                                    );
?>
<input type="submit" name="acc_proc_mod_doit" value="<?php echo $button_strings['Save']; ?>" class="btn btn-success">
<input type="reset" name="acc_proc_mod_clear" value="<?php echo $button_strings['Reset']; ?>" class="btn btn-default">
<input type="submit" name="acc_proc_mod_cancel" value="<?php echo $button_strings['Cancel']; ?>" class="btn btn-default">
</form>
<?php

elseif ($s_connected == true):

    if (count($s_procedures) > 0) {
        foreach ($s_procedures as $pname => $properties) {
            $fold_url = fold_detail_url('procedure', $properties['status'], $pname, $pname);

            echo '<div id="'.'p_'.$pname."\" class=\"det\">\n";

            if ($properties['status'] == 'open') {
                echo get_opened_procedure($pname, $properties, $fold_url);
            } else {
                echo get_closed_detail($pname, $fold_url);
            }

            echo "</div>\n";
        }
    }

    echo '<form method="post" action="'.url_session($_SERVER['PHP_SELF'])."\" name=\"acc_proc_form\">\n";

    if (count($s_procedures) > 0) {
        echo '<input type="submit" name="acc_proc_reload" value="'.$button_strings['Reload']."\" class=\"btn btn-default btn-xs\">\n";

        if (count($s_procedures) > 1) {
            echo '<input type="submit" name="acc_proc_open" value="'.$button_strings['OpenAll']."\" class=\"btn btn-default btn-xs\">\n";
            echo '<input type="submit" name="acc_proc_close" value="'.$button_strings['CloseAll']."\" class=\"btn btn-default btn-xs\">\n";
        }
        echo "<br><br>\n";
    }
?>
<table>
<tr>
  <th  colspan="2" align="left"><?php echo $acc_strings['CreateProc']; ?></th>
  <td><input type="submit" name="acc_proc_create" class="btn btn-primary" value="<?php echo $button_strings['Create']; ?>"></td>
</tr>
<tr>
  <td>
    <b><?php echo $acc_strings['SelProcMod']; ?></b>
  </td>
  <td>
    <?php echo get_selectlist('acc_proc_mod_name', array_keys($s_procedures), null, true); ?>
  </td>
  <td align="left">
    <input type="submit" name="acc_proc_mod" class="btn btn-success" value="<?php echo $button_strings['Modify']; ?>">
  </td>
</tr>
<tr>
  <td>
    <b><?php echo $acc_strings['SelProcDel']; ?></b>
  </td>
  <td>
    <?php echo get_selectlist('acc_proc_del_name', array_keys($s_procedures), null, true); ?>
  </td>
  <td align="left">
    <input type="submit" name="acc_proc_del" class="btn btn-danger" value="<?php echo $button_strings['Delete']; ?>">
  </td>
</tr>
</table>
</form>
<?php

endif;

?>
