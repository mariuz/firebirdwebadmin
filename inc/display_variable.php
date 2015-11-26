<?php
// File           inc/display_variable.php / FirebirdWebAdmin
// Purpose        print variable content for debugging purpose
// Author         Lutz Brueckner <irie@gmx.de>
// Copyright      (c) 2000, 2001, 2002, 2003, 2004 by Lutz Brueckner,
//                published under the terms of the GNU General Public Licence v.2,
//                see file LICENCE for details

require './configuration.inc.php';
require './debug_funcs.inc.php';

session_start();

switch ($_GET['var']) {
    case 'SESSION':
        $display = $_SESSION;
        break;
    case 'POST':
     case 'GET':
        $display = $_SESSION['s_'.$_GET['var']];
        break;
    default:
        $display = null;
}

debug_var($display);
