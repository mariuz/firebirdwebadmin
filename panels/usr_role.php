<?php
// File           usr_role.php / FirebirdWebAdmin
// Purpose        html sequence for the roles-panel in user.php
// Author         Lutz Brueckner <irie@gmx.de>
// Copyright      (c) 2000, 2001, 2002, 2003, 2004 by Lutz Brueckner,
//                published under the terms of the GNU General Public Licence v.2,
//                see file LICENCE for details
// Created        <02/05/26 11:10:48 lb>
//
// $Id: usr_role.php,v 1.9 2004/10/08 20:36:55 lbrueckner Exp $


if (isset($s_confirmations['role'])) {
    $subject = 'role';
    include('panels/confirm.php');
}

elseif($s_connected) {

?>
<form method="post" action="<?php echo url_session($_SERVER['PHP_SELF']); ?>" name="usr_role_form">
<?php

if (!empty($roles)) {

?>
<table border cellpadding="3" cellspacing="0">
<tr>
    <th><?php echo $usr_strings['Name']; ?></th>
    <th><?php echo $usr_strings['Owner']; ?></th>
    <th><?php echo $usr_strings['Members']; ?></th>
</tr>
<?php

    foreach ($roles as $name => $role) {
        $members_str = (count($role['members']) > 0) ? implode(', ', $role['members']) : '<i>none</i>';
?>
<tr>
   <td><b><?php echo $name; ?></b></td>
   <td><?php echo $role['owner']; ?></td>
   <td><?php echo $members_str; ?></td>
</tr>
<?php

    }
    echo "</table>";
}

?>
<p>
<table border cellpadding="3" cellspacing="0">
<tr>
   <th align="left"><b><?php echo $usr_strings['CreateRole']; ?></b></th>
   <td><b><?php echo $usr_strings['Name']; ?></b><br>
      <input type="text" size="32" maxlength="31" name="usr_role_name">
   </td>
   <td>
      <input type="submit" name="usr_role_create" value="<?php echo $button_strings['Create']; ?>">
   </td>
</tr>
<tr>
   <th align="left"><b><?php echo $usr_strings['RoleSelDel']; ?></b></th>
   <td><b><?php echo $usr_strings['Name']; ?></b><br>
     <select name="usr_role_dname">
     <?php 

         $selected = (isset($_POST['usr_role_dname'])) ? $_POST['usr_role_dname'] : '';
         build_roles_options($roles, $selected); 

     ?>
     </select>
   </td>
   <td>
      <input type="submit" name="usr_role_del" value="<?php echo $button_strings['Delete']; ?>">
   </td>
</tr>
</table>
<p>
<table border cellpadding="3" cellspacing="0">
<tr>
   <th align="left"><b><?php echo $usr_strings['RoleAdd']; ?></b></th>
   <td>
      <b><?php echo $usr_strings['Role']; ?></b><br>
      <select name="usr_role_addname">
      <?php 

         $selected = (isset($_POST['usr_role_addname'])) ? $_POST['usr_role_addname'] : '';
         build_roles_options($roles, $selected); 
      ?>
      </select>
   </td>
   <td>
      <b><?php echo $usr_strings['User']; ?></b><br>
      <?php 

          $pre = (isset($_POST['usr_role_adduser'])) ? $_POST['usr_role_adduser'] : NULL;         
          if (!empty($users)) {
              $user_options = array_keys($users);
              array_push($user_options, 'PUBLIC');
              echo get_selectlist('usr_role_adduser', $user_options, $pre, TRUE);
          }
          else {
              echo get_textfield('usr_role_adduser', 20, 80, $pre);
          }
      ?>
   </td>
   <td>
      <input type="submit" name="usr_role_add" value="<?php echo $button_strings['Add']; ?>">
   </td>
</tr>

<tr>
   <th align="left"><b><?php echo $usr_strings['RoleRem']; ?></b></th>
   <td>
      <b><?php echo $usr_strings['Role']; ?></b><br>
      <select name="usr_role_removename">
      <?php 

         $selected = (isset($_POST['usr_role_removename'])) ? $_POST['usr_role_removename'] : '';
         build_roles_options($roles, $selected); 
      ?>
      </select>
   </td>
   <td>
      <b><?php echo $usr_strings['User']; ?></b><br>
      <?php 

          $pre = (!empty($_POST['usr_role_removeuser'])) ? $_POST['usr_role_removeuser'] : NULL;         
          if (!empty($users)) {
              echo get_selectlist('usr_role_removeuser', $user_options, $pre, TRUE);
          }
          else {
              echo get_textfield('usr_role_removeuser', 20, 80, $pre);
          }
      ?>
   </td>
   <td>
      <input type="submit" name="usr_role_remove" value="<?php echo $button_strings['Remove']; ?>">
   </td>
</tr>
</table>
</form>
<?php

}

?>
