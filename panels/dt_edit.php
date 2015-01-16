<?php
// File           dt_edit.php / FirebirdWebAdmin
// Purpose        html sequence for the edit-data-panel
// Author         Lutz Brueckner <irie@gmx.de>
// Copyright      (c) 2000, 2001, 2002, 2003, 2004, 2005 by Lutz Brueckner,
//                published under the terms of the GNU General Public Licence v.2,
//                see file LICENCE for details

$df = new DataFormEdit($s_edit_where[$instance]['table'],
                       $s_fields[$s_edit_where[$instance]['table']],
                       $s_edit_values[$instance],
                       substr($s_edit_where[$instance]['where'], 6),
                       $instance);

echo $df->renderHTML();

?>
