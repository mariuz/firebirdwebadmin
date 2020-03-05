<?php
// File           lang/check_languages.inc.php / FirebirdWebAdmin
// Purpose        tool for finding inconsistencies in the language definition files
// Author         Lutz Brueckner <irie@gmx.de>
// Copyright      (c) 2000, 2001, 2002, 2003, 2004 by Lutz Brueckner,
//                published under the terms of the GNU General Public Licence v.2,
//                see file LICENCE for details

include '../inc/functions.inc.php';
include '../inc/debug_funcs.inc.php';

define('PROTOTYPE', 'english');

foreach (get_customize_languages() as $language) {
    require './'.$language.'.inc.php';

    foreach (get_array_names() as $aname) {
        $string_keys[$language][$aname] = array_keys($$aname);
    }
}

foreach (get_customize_languages() as $language) {
    if ($language == PROTOTYPE) {
        continue;
    }

    echo '<strong>'.$language."</strong><br>\n";

    foreach (get_array_names() as $aname) {
        $diff = array_diff($string_keys[PROTOTYPE][$aname], $string_keys[$language][$aname]);
        if (!empty($diff)) {
            foreach ($diff as $key) {
                echo 'missing: '.$key."<br>\n";
            }
        }

        $diff = array_diff($string_keys[$language][$aname], $string_keys[PROTOTYPE][$aname]);
        if (!empty($diff)) {
            foreach ($diff as $key) {
                echo 'obsolete: '.$key."<br>\n";
            }
        }
    }
    echo "<br>\n";
}

function get_array_names()
{
    return array('menu_strings',
                 'menu_coords',
                 'ptitle_strings',
                 'button_strings',
                 'db_strings',
                 'tb_strings',
                 'acc_strings',
                 'sql_strings',
                 'dt_strings',
                 'usr_strings',
                 'adm_strings',
                 'info_strings',
                 'MESSAGES',
                 'WARNINGS',
                 'ERRORS',
                  );
}
