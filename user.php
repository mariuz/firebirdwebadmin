<?php
// File           user.php / FirebirdWebAdmin
// Purpose        panels for user administration, roles, granting permissions
// Author         Lutz Brueckner <irie@gmx.de>
// Copyright      (c) 2000-2006 by Lutz Brueckner,
//                published under the terms of the GNU General Public Licence v.2,
//                see file LICENCE for details
// Created        <02/05/26 10:07:22 lb>
//
// $Id: user.php,v 1.17 2006/03/22 21:26:43 lbrueckner Exp $


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
    ||  isset($_POST['usr_user_create'])
    ||  isset($_POST['usr_user_mod'])) {
        if (!isset($s_sysdba_pw)  ||  empty($s_sysdba_pw)) {
            $warning = $WARNINGS['NEED_SYSDBA_PW'];
        }
    }

    if (empty($warning)) {

        // remove the selected user
        if (isset($_POST['usr_user_del'])  &&
            trim($_POST['usr_user_dname']) != '') {

            $duser =  get_request_data('usr_user_dname');
            if ($s_cust['askdel'] == TRUE) {
                $s_confirmations['user'] = 
                     array('msg' => sprintf($MESSAGES['CONFIRM_USER_DELETE'], $duser),
                           'obj' => $duser);
            }

            elseif (drop_user($duser, $s_sysdba_pw)) {
                unset($users[$duser]);
            }
        }
        
        // The Create button on the User panel was pushed
        if (isset($_POST['usr_user_create'])){
            $user_add_flag = TRUE;
        }
        
        // create the user from the form values
        if (isset($_POST['usr_user_create_doit'])) {
            $udata = get_posted_user_data();
            if (!create_user($udata, $s_sysdba_pw)) {

                // on error show the create user form again
                $user_add_flag = TRUE;
            }
            else {
                $users = get_user();
            }
        }
        
        // the Modify button on the User panel was pushed
        if (isset($_POST['usr_user_mod'])  
        &&  !empty($_POST['usr_user_mname'])) {
            $s_user_name = get_request_data('usr_user_mname');
            $udata = array('uname'    => $s_user_name,
                           'fname'    => $users[$s_user_name]['FIRST_NAME'],
                           'mname'    => $users[$s_user_name]['MIDDLE_NAME'],
                           'lname'    => $users[$s_user_name]['LAST_NAME']
                           );
        }
        
        // modify the user from the form values
        if (isset($_POST['usr_user_mod_doit'])) {
            $udata =  get_posted_user_data();
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
if (have_panel_permissions($s_login['user'], 'usr_role', TRUE)) {

    include('./inc/roles.inc.php');

    $roles = get_roles();

    // create a role
    if (isset($_POST['usr_role_create'])
    &&  $_POST['usr_role_name'] != '') {

        create_role($_POST['usr_role_name']);
    }

    // drop a role
    if (isset($_POST['usr_role_del'])
    &&  $_POST['usr_role_dname'] != '') {

        drop_role($_POST['usr_role_dname']);
    }

    // add user to role
    if (isset($_POST['usr_role_add'])
    &&  $_POST['usr_role_addname'] != ''
    &&  $_POST['usr_role_adduser'] != '') {

        grant_role_to_user($_POST['usr_role_addname'], $_POST['usr_role_adduser']);
    }

    // remove user from role
    if (isset($_POST['usr_role_remove'])
    &&  $_POST['usr_role_removename'] != ''
    &&  $_POST['usr_role_removeuser'] != '') {

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
// customizing
//
if (have_panel_permissions($s_login['user'], 'usr_cust')) {

    if (isset($_POST['usr_cust_save'])) {

        $old_settings = $s_cust;

        // color settings
        foreach (get_colornames() as $cname) {
            $s_cust['color'][$cname] = strtoupper(get_request_data('usr_cust_'.$cname));
            if (!preg_match('/^[0-9A-F#]{7}$/i', $s_cust['color'][$cname])) {
                $error = 'Bad Color!';
                $s_cust['color'] = $old_settings['color'];
                break;
            }
        }
        
        $s_cust['language']     = get_request_data('usr_cust_language');
        $s_cust['fontsize']     = (int)get_request_data('usr_cust_fontsize');
        $s_cust['textarea']     = array('cols' => (int)get_request_data('usr_cust_tacols'),
                                        'rows' => (int)get_request_data('usr_cust_tarows'));
        $s_cust['iframeheight'] = (int)get_request_data('usr_cust_ifheight');
        $s_cust['askdel']       = get_request_data('usr_cust_askdel') == $usr_strings['Yes'] ? 1 : 0;

        $settings_changed = TRUE;
    }

    // reset the customizing values to the configuration defaults
    if (isset($_POST['usr_cust_defaults'])) {

        $old_settings = $s_cust;
        $s_cust = get_customize_defaults($s_useragent);

        $settings_changed = TRUE;
    }

    if ($settings_changed = TRUE  &&  isset($old_settings)) {

        if ($old_settings['language'] != $s_cust['language']) {

            include('./lang/' . $s_cust['language'] . '.inc.php');
            fix_language($s_cust['language']);
        }

        set_customize_cookie($s_cust);

        // force reloading of the stylesheet
        $s_stylesheet_etag = '';
    }
}


//
// print out all the panels
//
$s_page = 'Users';
$panels = $s_users_panels;

require('./inc/script_end.inc.php');

?>
