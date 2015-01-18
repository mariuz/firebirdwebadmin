<?php
// Purpose        html sequence for the info panel
// Author         Lutz Brueckner <irie@gmx.de>
// Copyright      (c) 2000-2006 by Lutz Brueckner,
//                published under the terms of the GNU General Public Licence v.2,
//                see file LICENCE for details

if ($s_connected == TRUE) {
    $dstr = (!empty($s_login['host'])) ? $s_login['host'] . ':' . $s_login['database'] : $s_login['database'];
    $rstr = !empty($s_login['role']) ? '&nbsp;(' . $s_login['role'] . ')' : '';
    $ustr = '     <td>' . $s_login['user'] . $rstr . "</td>\n";
} else {
    $dstr = '&lt;none&gt;';
    $ustr = '';
}

?>
<table width="100%">
  <tr>
    <td>
      <table cellpadding="3" cellspacing="0">
        <tr>
          <?php echo $ustr; ?>
          <td><b><?php echo $info_strings['Connected'].':'; ?></b></td>
          <td><?php echo $dstr; ?></td>
        </tr>
      </table>
     </td>
  </tr>
</table>
<table>
<?php

if  (isset($binary_output)  &&  count($binary_output) > 0  &&  $s_page != 'SQL'
&&   strstr('Use CONNECT or CREATE DATABASE to specify a database', $binary_output[0]) === FALSE) {
    echo '<tr><td colspan="2"><b>'.$info_strings['ExtResult'].":</b><br>\n";
    foreach ($binary_output as $line) {
        echo $line."<br>\n";
    }
    echo "</td>\n</tr>\n";
}

if ($ib_error != '') {
    echo '<tr><td class="err">'.$info_strings['FBError'].":</td></tr>\n";
    echo "<tr><td>\n";
    echo $ib_error;
    echo "</td></tr>\n";
}

if (isset($binary_error)  && $binary_error != '') {
    echo '<tr><td class="err">'.$info_strings['ExtError'].":</td></tr>\n";
    echo "<tr><td>\n";
    echo nl2br($binary_error);
    echo "</td>\n</tr>\n";
}

if ($error != '') {
    echo '<tr><td class="err">'.$info_strings['Error'].":</td></tr>\n";
    echo "<tr><td>\n";
    echo $error;
    echo "</td>\n</tr>\n";
}

if ($php_error != '') {
    echo '<tr><td class="err">'.$info_strings['PHPError'].":</td></tr>\n";
    echo "<tr><td>\n";
    echo $php_error;
    echo "</td>\n</tr>\n";
}

if ($warning != '') {
    echo '<tr><td class="err">'.$info_strings['Warning'].":</td></tr>\n";
    echo "<tr><td>\n";
    echo $warning;
    echo "</td>\n</tr>\n";
}

if ($message != '') {
    echo '<tr><td><b>'.$info_strings['Message'].":</b></td></tr>\n";
    echo "<tr><td>\n";
    echo $message;
    echo "</td>\n</tr>\n";
}

if ($externcmd != '') {
    echo '<tr><td><b>'.$info_strings['ComCall'].":</b></td></tr>\n";
    echo "<tr><td>\n";
    echo $externcmd;
    echo "</td>\n</tr>\n";
}

if (DEBUG  &&  count($debug) > 0) {
    echo '<tr><td><b>'.$info_strings['Debug'].":</b></td>\n</tr>\n";
    echo "<tr><td>\n";
    foreach($debug as $str) {
        echo $str;
    }
    echo "</td>\n</tr>\n";
}

echo "</table>\n";

?>
