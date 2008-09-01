<?php
// File           adm_server.php / ibWebAdmin
// Purpose        displays the gds_lock_print / iblockpr output for the selected database
// Author         Lutz Brueckner <irie@gmx.de>
// Copyright      (c) 2000, 2001, 2002, 2003, 2004, 2005 by Lutz Brueckner,
//                published under the terms of the GNU General Public Licence v.2,
//                see file LICENCE for details
// Created        <02/05/26 19:27:38 lb>
//
// $Id: adm_server.php,v 1.5 2005/07/09 15:49:16 lbrueckner Exp $

if ($s_connected  &&  count($iblockpr_output) > 0) {

?>
<form method="post" action="<?php echo url_session($_SERVER['PHP_SELF']); ?>" name="adm_server_form">
<div style="padding:3px 0px 6px;">
<?php echo $server_info; ?>
</div>
<table cellpadding="3" border>
<tr>
  <th>LOCK HEADER BLOCK</th>
</tr>
<tr>
  <td><?php echo $lock_header; ?></td>
</tr>
</table>

<table cellpadding="3" border>
<tr>
  <th>&nbsp;</th><th>last sec</th><th>average</th>
</tr>
<?php

    for ($i=1; $i<count($iblock['names']); $i++) {
        echo '<tr><td>'.$iblock['names'][$i].'</td><td align="right">'
                       .$iblock['last'][$i].'</td><td align="right">'
                       .$iblock['avg'][$i]."</td></tr>\n";
    }
?>
</table>
</form>
<?php

}

?>
