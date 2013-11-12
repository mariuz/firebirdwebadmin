<?php
// File           toggle_fold_panel.php / FirebirdWebAdmin
// Purpose        set s_xyz_panels[][2]["open"|"close"] and redirect to $HTTP_REFERER
// Author         Lutz Brueckner <irie@gmx.de>
// Copyright      (c) 2000-2006 by Lutz Brueckner,
//                published under the terms of the GNU General Public Licence v.2,
//                see file LICENCE for details
// Created        <00/09/07 23:33:47 lb>
//
// $Id: toggle_fold_panel.php,v 1.14 2006/03/22 21:12:29 lbrueckner Exp $

// GET-Parameter:  $p       index of panel to open/close
//                 $d       the string "open" or "close"


include('./inc/configuration.inc.php');
include('./inc/session.inc.php');

session_start();
localize_session_vars();

include('./lang/english.inc.php');
include('./inc/functions.inc.php');

// some browsers may fail with the dynamically inserted html
if (!isset($_GET['p'])) {
    redirect(url_session($s_referer));
}

$p = $_GET['p'];
$d = $_GET['d'];

//calculate the panel name
$pvar = 's_'.strtolower($_SESSION['s_page']).'_panels';

if ($d == 'open' || $d == 'close') {
    ${$pvar}[$p][2] = $d;
}

set_customize_cookie($s_cust);

globalize_session_vars();

redirect(url_session($s_referer));

?>
