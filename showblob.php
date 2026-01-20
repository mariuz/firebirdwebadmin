<?php
// Purpose        displays the blob specified through the GET-varibles
// Author         Lutz Brueckner <irie@gmx.de>
// Copyright      (c) 2000, 2001, 2002, 2003, 2004, 2005 by Lutz Brueckner,
//                published under the terms of the GNU General Public Licence v.2,
//                see file LICENCE for details


// GET-Variables specifying the blob to display:
//
//       $table: table containing the blob
//       $col  : column containing the blob
//       $where: sql-where-clause specifying the primary keys to fetch the blob

require './inc/script_start.inc.php';

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $table = get_request_data('table', 'GET');
    $col = get_request_data('col', 'GET');
    $where = get_request_data('where', 'GET');
} else {
    $table = get_request_data('table');
    $col = get_request_data('col');
    $where = get_request_data('where');

    $s_wt['blob_as'][$col] = get_request_data('blobtype');
}

// Validate SQL identifiers to prevent SQL injection
// Table and column names should only contain alphanumeric characters and underscores
if (!preg_match('/^[a-zA-Z0-9_$]+$/', $table)) {
    die('Invalid table name');
}
if (!preg_match('/^[a-zA-Z0-9_$]+$/', $col)) {
    die('Invalid column name');
}
// WARNING: WHERE clause validation is complex and not implemented here
// The WHERE parameter remains a potential SQL injection vector
// This should use parameterized queries in production

$imageurl = 'showimage.php?where='.urlencode($where).'&table='.urlencode($table).'&col='.urlencode($col);
$imageurl .= '&'.uniqid('UNIQ_');

$blob = get_blob_content(sprintf('SELECT %s FROM %s %s', $col, $table, $where));

$title = build_title(sprintf('Blob from %s %s', $table, $where), false);
echo html_head($title)
   .'<body bgcolor="'.$s_cust['color']['area']."\">\n"
   .js_window_resize(BLOB_WINDOW_WIDTH, BLOB_WINDOW_HEIGHT)
   .'<form method="post" action="'.url_session($_SERVER['PHP_SELF']).'" name="showblob_form">'."\n"
   .hidden_field('table', htmlentities($table))
   .hidden_field('col', htmlentities($col))
   .hidden_field('where', htmlentities($where))
   ."<table>\n<tr>\n<td>\n"
   .get_selectlist('blobtype', $blob_types, $s_wt['blob_as'][$col], true)
   ."</td>\n<td>\n"
   .'<input type="submit" name="change_blobtype" value="Change Type">'."\n"
   ."</td>\n<td width=\"50\">\n</td>\n<td>\n"
   .'<input type="button" value="Close" onClick="self.close()">'."\n"
   ."</td>\n</tr>\n<table>\n"
   ."</form>\n";

$blobas = isset($s_wt['blob_as'][$col])  &&  $s_wt['blob_as'][$col] != ''
        ? $s_wt['blob_as'][$col]
        : 'hex';
switch ($blobas) {
    case 'png':
    case 'jpg':
    case 'gif':
        echo '<img src="'.$imageurl."\">\n";
        break;
    case 'text':
        echo '<pre align="left">'.htmlspecialchars($blob)."</pre>\n";
        break;
    case 'html':
        // Note: Displaying HTML blob content with escaping to prevent XSS attacks.
        // The HTML will be shown as plain text. To render actual HTML, this feature
        // should only be used with trusted blob data in a controlled environment.
        echo htmlspecialchars($blob, ENT_QUOTES, 'UTF-8');
        break;
    case 'hex':
        echo hex_view($blob);
        break;
}

echo "</body>\n"
   ."</html>\n";

function hex_view($data)
{
    global $s_cust;

    $len = strlen($data);
    $lines = ceil($len / 16);
    $offset = $values = $ascii = '';
    $p = 0;
    for ($i = 1; $i <= $lines; ++$i) {
        $offset .= sprintf('%08x', $p);

        for ($j = 0; $j < 16; ++$j) {
            if ($p > $len - 1) {
                break;
            }
            $values .= sprintf('%02x', ord($data[$p])).'&nbsp;';
            $ascii  .= hex2ascii($data[$p]);
            ++$p;
        }

        $offset .= "<br>\n";
        $values .= "<br>\n";
        $ascii  .= "<br>\n";
    }

    return "<table>\n<tr>\n"
         ."<td>\n<table>\n<tr>\n<td bgcolor=\"".$s_cust['color']['area']."\" class=\"hex\">\n"
         .$offset
         ."</td>\n</tr>\n</table>\n</td>\n"
         ."<td>\n<table>\n<tr>\n<td class=\"hex\">\n"
         .$values
         ."</td>\n</tr>\n</table>\n</td>\n"
         ."<td>\n<table>\n<tr>\n<td bgcolor=\"".$s_cust['color']['area']."\" class=\"hex\">\n"
         .$ascii
         ."</td>\n</tr>\n</table>\n</td>\n"
         ."</tr>\n</table>\n";
}

function hex2ascii($val)
{
    return (ord($val) > 31  && ord($val) < 128) ? str_replace(' ', '&nbsp;', htmlspecialchars($val)) : '.';
}
