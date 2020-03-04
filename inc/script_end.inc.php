<?php
// File           script_end.inc.php / FirebirdWebAdmin
// Purpose        output the whole html source for the page
// Author         Lutz Brueckner <irie@gmx.de>
// Copyright      (c) 2000-2006 by Lutz Brueckner,
//                published under the terms of the GNU General Public Licence v.2,
//                see file LICENCE for details


// Varibles:   $title    title string for the page
//             $panels   panel array for the page

$title = build_title($menu_strings[$s_page]);
require_once './views/header.php';
//echo html_head($title).
//    ""
//   . js_global_variables()
//   . js_xml_http_request_client()
//   . js_request_close_panel()
//   . $js_stack
//
//   . get_tabmenu($s_page)
//    ;

// display the panels on the active page
foreach ($panels as $nr => $panel) {

    // take respect for the $HIDE_PANELS configuration
    if (in_array($panel[0], $HIDE_PANELS)
    &&  ($s_login['user'] != 'SYSDBA'  ||  SYSDBA_GET_ALL == false  ||  $s_connected == false)) {
        continue;
    }

    echo '<div id="p'.$nr."\">\n"
       .'<a name="'.$panel[0].'"></a>'."\n";

    if ($panel[2] == 'open'  ||
        ($panel[0] == 'info'  &&  critical_error())) {
        echo get_open_panel_start($panel[1], $nr);

        // there may be different instances of the data edit/delete panel,
        // which carrying the instance nr in the panel name
        if (preg_match('/dt_(edit|delete)([0-9]+)/', $panel[0], $matches)) {
            $instance = $matches[2];
            if ($matches[1] == 'edit') {
                include './panels/dt_edit.php';
            } else {
                $subject = 'row';
                include './panels/confirm.php';
            }
        } else {
            include './panels/'.$panel[0].'.php';
        }

        echo get_open_panel_end();
    } else {
        echo get_closed_panel($panel[1], $nr);
    }

    echo "</div>\n";
}

// close the db connection
if (isset($dbhandle)  &&  is_resource($dbhandle)) {
    // fbird_close() chrashes the apache-process,
    // this was a bug in some revisions of the ibase-module
    //    fbird_close($dbhandle);
}

if (DEBUG_HTML) {
    $fname = TMPPATH.substr_replace(basename($_SERVER['PHP_SELF']), 'html', -3);
    write_output_buffer($fname);

//     if (in_array('tidy', get_loaded_extensions())) {
//         $tidy = tidy_parse_file($fname);
//         debug_var(tidy_get_error_buffer($tidy));
//     }
}

require_once './views/footer.php';
globalize_session_vars();

//
// check the global error-variables
//
function critical_error()
{
    return !empty($GLOBALS['error'])  ||
           !empty($GLOBALS['fb_error'])  ||
           !empty($GLOBALS['php_error'])  ||
           !empty($GLOBALS['externcmd']);
}
