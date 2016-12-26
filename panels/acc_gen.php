<?php
// File           acc_gen.php / FirebirdWebAdmin
// Purpose        html sequence for the generators-panel in accessories.php
// Author         Lutz Brueckner <irie@gmx.de>
// Copyright      (c) 2000, 2001, 2002, 2003, 2004 by Lutz Brueckner,
//                published under the terms of the GNU General Public Licence v.2,
//                see file LICENCE for details


if (isset($s_confirmations['generator'])) {
    $subject = 'generator';
    include_once './panels/confirm.php';
} elseif ($s_connected) {
    ?>
<form method="post" action="<?php echo url_session($_SERVER['PHP_SELF']);
    ?>" name="acc_gen_form">
<?php

if (!empty($generators)) {
    ?>
<table class="table table-bordered table-hover">
<tr>
   <th><?php echo $acc_strings['Name'];
    ?></th>
   <th><?php echo $acc_strings['Value'];
    ?></th>
   <th><?php echo $acc_strings['SetValue'];
    ?></th>
   <th><?php echo $acc_strings['DropGen'];
    ?></th>
</tr>

<?php

    foreach ($generators as $idx => $gen) {
        ?>
<tr>
   <td><b><?php echo $gen['name'];
        ?></b></td>
   <td align="right"><?php echo $gen['value'];
        ?></td>
   <td>
   <div class="form-inline">
      <input type="text" class="form-control" size="8" maxlength="24" name="acc_gen_val_<?php echo $idx; ?>">
      <input type="submit" class="btn btn-success" name="acc_gen_set_<?php echo $idx; ?>" value="<?php echo $button_strings['Set']; ?>">
	  </div>
   </td>
   <td align="center">
      <input type="submit" class="btn btn-danger" name="acc_gen_drop_<?php echo $idx; ?>" value="<?php echo $button_strings['Drop']; ?>">
   </td>
</tr>
<?php

    }
    echo '</table>';
}

    ?>
<p>
<table>
<tr>
   <th colspan="3" align="left"><b><?php echo $acc_strings['CreateGen'];
    ?></b></th>
</tr>
<tr>
   <td>
   <div class="form-inline">
    <label for="acc_gen_name"><?php echo $acc_strings['Name'];?></label>
	<input type="text" size="15" maxlength="31" id="acc_gen_name" name="acc_gen_name" class="form-control">
   <label for="acc_gen_name"><?php echo $acc_strings['StartVal'];?></label>
   <input type="text" size="8" maxlength="24" name="acc_gen_start" class="form-control">
   <input type="submit" name="acc_gen_create" class="btn btn-success" value="<?php echo $button_strings['Create']; ?>">
   </div>
   
   
      
   </td>
</tr>
</table>
</form>
<?php

}

?>
