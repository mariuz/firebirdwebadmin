<?php
// File           adm_server.php / FirebirdWebAdmin
// Purpose        displays the gds_lock_print / iblockpr output for the selected database
// Author         Lutz Brueckner <irie@gmx.de>
// Copyright      (c) 2000, 2001, 2002, 2003, 2004, 2005 by Lutz Brueckner,
//                published under the terms of the GNU General Public Licence v.2,
//                see file LICENCE for details

if ($s_connected  &&  count($iblockpr_output) > 0) {

?>
<form method="post" action="<?php echo url_session($_SERVER['PHP_SELF']); ?>" name="adm_server_form">
<div style="padding:3px 0px 6px;">
<?php echo $server_info; ?>
</div>
<table class="table table-bordered">
<tr>
  <th>LOCK HEADER BLOCK</th>
</tr>
<tr>
  <td><?php echo $lock_header; ?></td>
</tr>
</table>

<table class="table table-bordered">
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
