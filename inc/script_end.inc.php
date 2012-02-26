<?php
// File           script_end.inc.php / ibWebAdmin
// Purpose        output the whole html source for the page
// Author         Lutz Brueckner <irie@gmx.de>
// Copyright      (c) 2000-2006 by Lutz Brueckner,
//                published under the terms of the GNU General Public Licence v.2,
//                see file LICENCE for details
// Created        <00/10/18 09:12:24 lb>
//
// $Id: script_end.inc.php,v 1.29 2006/03/22 21:11:22 lbrueckner Exp $


// Varibles:   $title    title string for the page
//             $panels   panel array for the page

$title = build_title($menu_strings[$s_page]);

echo html_head($title)
   . "<body>\n"
   . js_global_variables()
   . js_xml_http_request_client()
   . js_request_close_panel()
   . $js_stack

   . get_tabmenu($s_page);

// display the panels on the active page
foreach ($panels as $nr => $panel) {

    // take respect for the $HIDE_PANELS configuration
    if (in_array($panel[0], $HIDE_PANELS)
    &&  ($s_login['user'] != 'SYSDBA'  ||  SYSDBA_GET_ALL == FALSE  ||  $s_connected == FALSE)) {

        continue;
    }

    echo '<div id="p'.$nr."\">\n"
       . '<a name="'.$panel[0].'"></a>'."\n";

    if ($panel[2] == 'open'  ||
        ($panel[0] == 'info'  &&  critical_error())) {

        echo get_open_panel_start($panel[1], $nr);

        // there may be different instances of the data edit/delete panel,
        // which carrying the instance nr in the panel name
        if (preg_match('/dt_(edit|delete)([0-9]+)/', $panel[0], $matches)) {
            $instance = $matches[2];
            if ($matches[1] == 'edit') {
                include('./panels/dt_edit.php');
            }
            else {
                $subject = 'row';
                include('./panels/confirm.php');
            }
        }
        else {
            include('./panels/' . $panel[0] . '.php');
        }

        echo get_open_panel_end();
    }

    else {
        $open_icon = get_icon_path(DATAPATH, ICON_SIZE) . 'open.png';
        echo get_closed_panel($panel[1], $nr, $open_icon);
    }

    echo "</div>\n";
}

// close the db connection
if (isset($dbhandle)  &&  is_resource($dbhandle)) {
    // fbird_close() chrashes the apache-process,
    // this was a bug in some revisions of the ibase-module
    //    fbird_close($dbhandle);
}



if (DEBUG === TRUE) {
    echo "<div align=\"left\">\n";

    show_time_consumption($start_time, microtime());
    
//     echo 'cookie size: '.strlen($_COOKIE[get_customize_cookie_name()])."<br>\n";
//     debug_var($_COOKIE[get_customize_cookie_name()]);

    // see http://xdebug.derickrethans.nl/
    if (function_exists('xdebug_memory_usage')) {
        echo 'memory usage: '.xdebug_memory_usage()."<br>\n";
    }

    // display links to display the session, post or get variables
    $session_url = url_session('./inc/display_variable.php?var=SESSION');
    echo '<a href="'.$session_url.'" target="_blank">[ Session ]</a>'."\n";

    $post_url = url_session('./inc/display_variable.php?var=POST');
    echo '<a href="'.$post_url.'" target="_blank">[ POST ]</a>'."\n";

    $get_url = url_session('./inc/display_variable.php?var=GET');
    echo '<a href="'.$get_url.'" target="_blank">[ GET ]</a>'."\n";

    echo '<a href="./inc/phpinfo.php" target="_blank">[ phpinfo ]</a>'."\n";

    $kill_url = url_session('./inc/kill_session.php');
    echo '<a href="'.$kill_url.'">[ kill session ]</a>'."\n";

    // Inhalt von $_POST und $_GET in der Session hinterlegen
    $s_POST = $_POST;
    $s_GET  = $_GET;

    echo "</div>\n";
}


if (DEBUG_HTML) {
    $fname = TMPPATH.substr_replace(basename($_SERVER['PHP_SELF']), 'html', -3);
    write_output_buffer($fname);

//     if (in_array('tidy', get_loaded_extensions())) {
//         $tidy = tidy_parse_file($fname);
//         debug_var(tidy_get_error_buffer($tidy));
//     }
}

echo "</body>\n"
   . "</html>\n";

globalize_session_vars();


//
// check the global error-variables
//
function critical_error() {

    return !empty($GLOBALS['error'])  ||
           !empty($GLOBALS['ib_error'])  ||
           !empty($GLOBALS['php_error'])  ||
           !empty($GLOBALS['externcmd']);
}

?>
