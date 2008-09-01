<?php
// File           acc_gen.php / ibWebAdmin
// Purpose        html sequence for the generators-panel in accessories.php
// Author         Lutz Brueckner <irie@gmx.de>
// Copyright      (c) 2000, 2001, 2002, 2003, 2004 by Lutz Brueckner,
//                published under the terms of the GNU General Public Licence v.2,
//                see file LICENCE for details
// Created        <00/10/03 13:51:00 lb>
//
// $Id: acc_gen.php,v 1.7 2004/10/08 20:36:55 lbrueckner Exp $


if (isset($s_confirmations['generator'])) {
    $subject = 'generator';
    include_once('./panels/confirm.php');
}

elseif($s_connected) {

?>
<form method="post" action="<?php echo url_session($_SERVER['PHP_SELF']); ?>" name="acc_gen_form">
<?php

if (!empty($generators)) {

?>
<table border cellpadding="3" cellspacing="0">
<tr>
   <th><?php echo $acc_strings['Name']; ?></th>
   <th><?php echo $acc_strings['Value']; ?></th>
   <th><?php echo $acc_strings['SetValue']; ?></th>
   <th><?php echo $acc_strings['DropGen']; ?></th>
</tr>

<?php

    foreach ($generators as $idx => $gen) {

?>
<tr>
   <td><b><?php echo $gen['name']; ?></b></td>
   <td align="right"><?php echo $gen['value']; ?></td>
   <td>
      <input type="text" size="8" maxlength="24" name="acc_gen_val_<?php echo $idx; ?>">&nbsp;&nbsp;
      <input type="submit" name="acc_gen_set_<?php echo $idx; ?>" value="<?php echo $button_strings['Set']; ?>">
   </td>
   <td align="center">
      <input type="submit" name="acc_gen_drop_<?php echo $idx; ?>" value="<?php echo $button_strings['Drop']; ?>">
   </td>
</tr>
<?php

    }
    echo "</table>";
}

?>
<p>
<table border cellpadding="3" cellspacing="0">
<tr>
   <th colspan="3" align="left"><b><?php echo $acc_strings['CreateGen']; ?></b></th>
</tr>
<tr>
   <td><b><?php echo $acc_strings['Name']; ?></b><br>
      <input type="text" size="15" maxlength="31" name="acc_gen_name">
   </td>
   <td><b><?php echo $acc_strings['StartVal']; ?></b><br>
      <input type="text" size="8" maxlength="24" name="acc_gen_start">
   </td>
   <td>
      <input type="submit" name="acc_gen_create" value="<?php echo $button_strings['Create']; ?>">
   </td>
</tr>
</table>
</form>
<?php

}

?>
