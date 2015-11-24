<?php
// Purpose        html sequence for the info panel
// Author         Lutz Brueckner <irie@gmx.de>
// Copyright      (c) 2000-2006 by Lutz Brueckner,
//                published under the terms of the GNU General Public Licence v.2,
//                see file LICENCE for details

if  (isset($binary_output)  &&  count($binary_output) > 0  &&  $s_page != 'SQL'
&&   strstr('Use CONNECT or CREATE DATABASE to specify a database', $binary_output[0]) === FALSE) {
    echo '<div class="alert alert-info alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><table><tr><td><strong>'.$info_strings['ExtResult'].":</strong></td></tr>";
    foreach ($binary_output as $line) {
        echo "<tr><td>".$line."</td></tr>";
    }
    echo "</table></div>";
}

if ($ib_error != '') {
	echo '<div class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><table>';
    echo '<tr><td><strong>'.$info_strings['FBError'].":</strong></td></tr>\n";
    echo "<tr><td>".$ib_error."</td></tr>\n";
	echo "</table></div>";
}

if (isset($binary_error)  && $binary_error != '') {
	echo '<div class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><table>';
    echo '<tr><td><strong>'.$info_strings['ExtError'].":</strong></td></tr>\n";
    echo "<tr><td>".nl2br($binary_error)."</td>\n</tr>\n";
	echo "</table></div>";
}

if ($error != '') {
	echo '<div class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><table>';
    echo '<tr><td><strong>'.$info_strings['Error'].":</strong></td></tr>\n";
    echo "<tr><td>".$error."</td>\n</tr>\n";
	echo "</table></div>";
}

if ($php_error != '') {
	echo '<div class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><table>';
    echo '<tr><td><strong>'.$info_strings['PHPError'].":</strong></td></tr>\n";
    echo "<tr><td>".$php_error."</td>\n</tr>\n";
	echo "</table></div>";
}

if ($warning != '') {
	echo '<div class="alert alert-warning alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><table>';
    echo '<tr><td><strong>'.$info_strings['Warning'].":</strong></td></tr>\n";
    echo "<tr><td>\n".$warning."</td>\n</tr>\n";
	echo "</table></div>";
}

if ($message != '') {
	echo '<div class="alert alert-info alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><table>';
    echo '<tr><td><strong>'.$info_strings['Message'].":</strong></td></tr>\n";
    echo "<tr><td>\n".$message."</td>\n</tr>\n";
	echo "</table></div>";
}

if ($externcmd != '') {
	echo '<div class="alert alert-info alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><table>';
    echo '<tr><td><strong>'.$info_strings['ComCall'].":</strong></td></tr>\n";
    echo "<tr><td>\n".$externcmd."</td>\n</tr>\n";
	echo "</table></div>";
}

if (DEBUG  &&  count($debug) > 0) {
	echo '<div class="alert alert-info alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><table>';
    echo '<tr><td><strong>'.$info_strings['Debug'].":</strong></td>\n</tr>\n";
    foreach($debug as $str) {
        echo "<tr><td>\n".$str."</td>\n</tr>\n";
    }
	echo "</table></div>";
}
?>
