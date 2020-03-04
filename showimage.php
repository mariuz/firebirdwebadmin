<?php
// Purpose        called by showblob to load the given blob and display the data as an image
// Author         Lutz Brueckner <irie@gmx.de>
// Copyright      (c) 2000, 2001, 2002, 2003, 2004 by Lutz Brueckner,
//                published under the terms of the GNU General Public Licence v.2,
//                see file LICENCE for details


// GET-Variables specifying the blob
//
//       $table: table containing the blob
//       $col  : column containing the blob
//       $where: sql-where-clause specifying the primary keys to fetch the blob

require './inc/configuration.inc.php';
require './inc/session.inc.php';

session_start();
localize_session_vars();

require './lang/'.(isset($s_cust) ? $s_cust['language'] : LANGUAGE).'.inc.php';
require './inc/functions.inc.php';

$dbhandle = db_connect()
     or fb_error();

$table = $_GET['table'];
$col = $_GET['col'];
$where = $_GET['where'];
$sql = sprintf('SELECT %s FROM %s %s', $col, $table, $where);
$blob = get_blob_content($sql);

switch ($s_wt['blob_as'][$col]) {
    case 'png':
        header('Content-Type: image/png');
        break;
    case 'jpg':
        header('Content-Type: image/jpg');
        break;
    case 'gif':
        header('Content-Type: image/gif');
        break;
}

echo $blob;
