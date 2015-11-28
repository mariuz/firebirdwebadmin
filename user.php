<?php
// Purpose        panels for user administration, roles, granting permissions
// Author         Lutz Brueckner <irie@gmx.de>
// Copyright      (c) 2000-2006 by Lutz Brueckner,
//                published under the terms of the GNU General Public Licence v.2,
//                see file LICENCE for details


require('./inc/script_start.inc.php');

//
// user stuff
//
if (have_panel_permissions($s_login['user'], 'usr_user', TRUE)) {

    include('./inc/users.inc.php');

    // init the array users[]
    $users = get_user();

    $s_sysdba_pw = get_sysdba_pw();

    if (isset($_POST['usr_user_del'])
        || isset($_POST['usr_user_create'])
        || isset($_POST['usr_user_mod'])
    ) {
        if (!isset($s_sysdba_pw) || empty($s_sysdba_pw)) {
            $warning = $WARNINGS['NEED_SYSDBA_PW'];
        }
    }

    if (empty($warning)) {

        // remove the selected user
        if (isset($_POST['usr_user_del']) &&
            trim($_POST['usr_user_dname']) != ''
        ) {

            $duser = get_request_data('usr_user_dname');
            if ($s_cust['askdel'] == true) {
                $s_confirmations['user'] =
                    array('msg' => sprintf($MESSAGES['CONFIRM_USER_DELETE'], $duser),
                        'obj' => $duser);
            } elseif (drop_user($duser, $s_sysdba_pw)) {
                unset($users[$duser]);
            }
        }

        // The Create button on the User panel was pushed
        if (isset($_POST['usr_user_create'])) {
            $user_add_flag = true;
        }

        // create the user from the form values
        if (isset($_POST['usr_user_create_doit'])) {
            $udata = get_posted_user_data();
            if (!create_user($udata, $s_sysdba_pw)) {

                // on error show the create user form again
                $user_add_flag = true;
            } else {
                $users = get_user();
            }
        }

        // the Modify button on the User panel was pushed
        if (isset($_POST['usr_user_mod'])
            && !empty($_POST['usr_user_mname'])
        ) {
            $s_user_name = get_request_data('usr_user_mname');
            $udata = array('uname' => $s_user_name,
                'fname' => $users[$s_user_name]['FIRST_NAME'],
                'mname' => $users[$s_user_name]['MIDDLE_NAME'],
                'lname' => $users[$s_user_name]['LAST_NAME']
            );
        }

        // modify the user from the form values
        if (isset($_POST['usr_user_mod_doit'])) {
            $udata = get_posted_user_data();
            if (modify_user($udata, $s_sysdba_pw)) {
                // on success don't show the modify user form again
                unset($s_user_name);
                $users = get_user();
            }
        }

        // modifying an index was canceled
        if (isset($_POST['usr_user_mod_cancel'])) {
            unset($s_user_name);
        }
    }
}


//
// roles initialisations and form handling
//
if (have_panel_permissions($s_login['user'], 'usr_role', true)) {

    include('./inc/roles.inc.php');

    $roles = get_roles();

    // create a role
    if (isset($_POST['usr_role_create'])
        && $_POST['usr_role_name'] != ''
    ) {
        create_role($_POST['usr_role_name']);
    }

    //print_r($_POST);

    // drop a role
    if (isset($_POST['usr_role_del'])
        && $_POST['usr_role_dname'] != ''
    ) {

        drop_role($_POST['usr_role_dname']);
    }

    // add user to role
    if (isset($_POST['usr_role_add'])
        && $_POST['usr_role_addname'] != ''
        && $_POST['usr_role_adduser'] != ''
    ) {

        grant_role_to_user($_POST['usr_role_addname'], $_POST['usr_role_adduser']);
    }

    // remove user from role
    if (isset($_POST['usr_role_remove'])
        && $_POST['usr_role_removename'] != ''
        && $_POST['usr_role_removeuser'] != ''
    ) {

        revoke_role_from_user($_POST['usr_role_removename'], $_POST['usr_role_removeuser']);
    }
}


// deleting a subject is confirmed
if (isset($_POST['confirm_yes'])) {
    switch ($_POST['confirm_subject']) {
        case 'user':
            if (drop_user($s_confirmations['user']['obj'], $s_sysdba_pw)) {
                unset($users[$s_confirmations['user']['obj']]);
            }
            unset($s_confirmations['user']);
            break;
    }
}

// deleting a subject is canceled
if (isset($_POST['confirm_no'])) {
    unset($s_confirmations[$_POST['confirm_subject']]);
}

//
// print out all the panels
//
$s_page = 'Users';
$panels = $s_users_panels;

require('./inc/script_end.inc.php');

?>
