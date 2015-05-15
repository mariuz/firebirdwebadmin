<?php
// File           tb_show.php / FirebirdWebAdmin
// Purpose        html sequence for the tb_show-panel in tables.php
// Author         Lutz Brueckner <irie@gmx.de>
// Copyright      (c) 2000-2006 by Lutz Brueckner,
//                published under the terms of the GNU General Public Licence v.2,
//                see file LICENCE for details


$tcnt = 0;
if ($s_connected == TRUE && is_array($s_tables)):
    foreach($s_tables as $tablename => $properties) {
        if ($properties['is_view'] == TRUE) {
            continue;
        }
        $tcnt++;

        $title    = $tablename;
        if ($s_tables_counts == TRUE  &&  isset($properties['count'])) {
            $title .= '&nbsp;['.$properties['count'].']';
        }

        $fold_url    = fold_detail_url('table', $properties['status'], $tablename, $title);
        $comment_url = "javascript:requestCommentArea('table', '".$tablename."');";

        echo '      <div id="'.'t_'.$tablename."\" class=\"det\">\n";

        if ($properties['status'] == 'open') {
            echo get_opened_table($tablename, $title, $fold_url, $comment_url, 'tc_'.$tablename);
        }
        else {   // $properties['status'] == 'close'
            echo get_closed_detail($title, $fold_url, $comment_url, 'tc_'.$tablename);
        }

        echo "      </div>\n";

    }    // foreach $s_tables

    echo '<form method="post" action="'.url_session($_SERVER['PHP_SELF'])."#tb_show\" name=\"tb_show_form\">\n"
       . get_checkbox('tb_show_counts', '1', $s_tables_counts).' '.$tb_strings['DispCounts']."&nbsp;&nbsp;&nbsp;\n"
       . get_checkbox('tb_show_cnames', '1', $s_tables_cnames).' '.$tb_strings['DispCNames']."&nbsp;&nbsp;&nbsp;\n"
       . get_checkbox('tb_show_def', '1', $s_tables_def).' '.$tb_strings['DispDef']."&nbsp;&nbsp;&nbsp;\n"
       . get_checkbox('tb_show_comp', '1', $s_tables_comp).' '.$tb_strings['DispComp']."&nbsp;&nbsp;&nbsp;\n"
       . get_checkbox('tb_show_comments', '1', $s_tables_comment).' '.$tb_strings['DispComm']."<br />\n"
       . '  <input type="submit" name="tb_show_reload" value="'.$button_strings['Reload']."\" class=\"bgrp\">\n";
        if ($tcnt > 1) {
            echo '  <input type="submit" name="tb_table_open" value="'.$button_strings['OpenAll']."\" class=\"bgrp\">\n"
               . '  <input type="submit" name="tb_table_close" value="'.$button_strings['CloseAll']."\" class=\"bgrp\">\n";
        }
    echo "</form>\n";

endif;

?>
