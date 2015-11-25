<?php
// File           users.inc.php / FirebirdWebAdmin
// Purpose        functions working with users, included from user.php
// Author         Lutz Brueckner <irie@gmx.de>
// Copyright      (c) 2000-2006 by Lutz Brueckner,
//                published under the terms of the GNU General Public Licence v.2,
//                see file LICENCE for details


//
// get an array with the users information from the security database
//
function get_user()
{
    global $dbhandle, $s_login;

    $users = array();

    if (($service = fbird_service_attach($s_login['host'], $s_login['user'], $s_login['password'])) != false) {
        $users_info = fbird_server_info($service, IBASE_SVC_GET_USERS);
        fbird_service_detach($service);
        foreach ($users_info as $user) {
            $users[$user['user_name']] = array('FIRST_NAME' => $user['first_name'],
                'MIDDLE_NAME' => $user['middle_name'],
                'LAST_NAME' => $user['last_name'],
            );
        }
    } else {
        $GLOBALS['ib_error'] = fbird_errmsg();

        return false;
    }

    return $users;
}

//
// create the user from the values posted by the user-form
//
function create_user($udata, $s_sysdba_pw)
{
    global $s_login, $ib_error, $warning, $WARNINGS;

    if (empty($udata['uname'])) {
        $warning = $WARNINGS['UN_REQUIRED'];

        return false;
    }

    if (empty($udata['password'])) {
        $warning = $WARNINGS['PW_REQUIRED'];

        return false;
    }

    if (empty($udata['pw_repeat'])
        || $udata['pw_repeat'] != $udata['password']
    ) {
        $warning = $WARNINGS['PW_WRONG_REPEAT'];

        return false;
    }

    if (($service = fbird_service_attach($s_login['host'], 'SYSDBA', $s_sysdba_pw)) == false) {
        $ib_error = fbird_errmsg();
    } elseif (false == fbird_add_user($service, $udata['uname'], $udata['password'], $udata['fname'], $udata['mname'], $udata['lname'])) {
        $ib_error = 'Creating user failed!';
    } else {
        fbird_service_detach($service);
    }

    return empty($ib_error);
}

//
// modify the user $uname according the values posted by the user-form
//
function modify_user($udata, $s_sysdba_pw)
{
    global $s_login, $ib_error, $WARNINGS, $warning;

    if (!empty($udata['password'])) {
        if (empty($udata['pw_repeat'])
            || $udata['pw_repeat'] != $udata['password']
        ) {
            $warning = $WARNINGS['PW_WRONG_REPEAT'];

            return false;
        } else {
            $change_pw = true;
        }
    }

    if (($service = fbird_service_attach($s_login['host'], 'SYSDBA', $s_sysdba_pw)) == false) {
        $ib_error = fbird_errmsg();
    } elseif (false == fbird_modify_user($service, $udata['uname'], $udata['password'], $udata['fname'], $udata['mname'], $udata['lname'])) {
        $ib_error = 'Modifying user failed!';
    } else {
        fbird_service_detach($service);
    }

    return empty($ib_error);
}

//
// remove the user $uname
//
function drop_user($uname, $s_sysdba_pw)
{
    global $s_login, $ib_error;

    if (($service = fbird_service_attach($s_login['host'], 'SYSDBA', $s_sysdba_pw)) == false) {
        $ib_error = fbird_errmsg();
    } elseif (fbird_delete_user($service, $uname) == false) {
        $ib_error = fbird_errmsg();
    } else {
        fbird_service_detach($service);
    }

    return empty($ib_error);
}

//
// output a html-table with a form to define/modify an user
//
// Variables:  $uname  name of the user to modify or NULL to create a new one
//             $title  headline-string for the table
function user_definition($udata, $title, $modify = false)
{
    global $usr_strings;

    $readonly_str = $modify ? 'readonly="readonly"' : '';

    $html = <<<EOT
<table class="table table-bordered">
  <tr>
    <th colspan="3" align="left">$title</th>
  </tr>
  <tr>
    <td>
      <b>${usr_strings['UName']}</b><br>
      <input type="text" size="20" maxlength="128" name="def_user_name" value="${udata['uname']}"$readonly_str>
    </td>
    <td>
      <b>${usr_strings['Password']}</b><br>
      <input type="password" size="20" maxlength="31" name="def_user_pw" value="">
    </td>
    <td>
      <b>${usr_strings['RepeatPW']}</b><br>
      <input type="password" size="20" maxlength="31" name="def_user_pwa" value="">
    </td>
  </tr>
  <tr>
    <td>
      <b>${usr_strings['FName']}</b><br>
      <input type="text" size="20" maxlength="128" name="def_user_fname" value="${udata['fname']}">
    </td>
    <td>
      <b>${usr_strings['MName']}</b><br>
      <input type="text" size="20" maxlength="128" name="def_user_mname" value="${udata['mname']}">
    </td>
    <td>
      <b>${usr_strings['LName']}</b><br>
      <input type="text" size="20" maxlength="128" name="def_user_lname" value="${udata['lname']}">
    </td>
  </tr>
</table>

EOT;

    return $html;
}

//
// return an array filled with the data posted by the userdefinition_form
//
function get_posted_user_data()
{
    return array('uname' => get_request_data('def_user_name'),
        'password' => get_request_data('def_user_pw'),
        'pw_repeat' => get_request_data('def_user_pwa'),
        'fname' => get_request_data('def_user_fname'),
        'mname' => get_request_data('def_user_mname'),
        'lname' => get_request_data('def_user_lname'),
    );
}
