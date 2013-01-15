<?php
// File           inc/display_variable.php / FirebirdWebAdmin
// Purpose        print variable content for debugging purpose
// Author         Lutz Brueckner <irie@gmx.de>
// Copyright      (c) 2000, 2001, 2002, 2003, 2004 by Lutz Brueckner,
//                published under the terms of the GNU General Public Licence v.2,
//                see file LICENCE for details
// Created        <02/10/14 17:47:02 lb>
//
// $Id: display_variable.php,v 1.3 2004/10/24 16:33:43 lbrueckner Exp $

require('./configuration.inc.php');
require('./debug_funcs.inc.php');

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
        $display = NULL;
}

debug_var($display);

?>
