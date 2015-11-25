<?php
// Purpose        html sequence for the enter-data-panel in data.php
// Author         Lutz Brueckner <irie@gmx.de>
// Copyright      (c) 2000, 2001, 2002, 2003, 2004, 2005 by Lutz Brueckner,
//                published under the terms of the GNU General Public Licence v.2,
//                see file LICENCE for details

if ($s_connected == true && $s_enter_name == ''):

    ?>
    <form method="post" action="<?php echo url_session($_SERVER['PHP_SELF']); ?>" name="dt_enter_form">
        <table class="table table-bordered">
            <tr>
                <td colspan="2"><b><?php echo $dt_strings['SelTable']; ?></b><br>
                    <?php echo get_table_selectlist('dt_enter_name', array('noviews', 'insert'), null, true); ?>
                </td>
                <td valign="bottom">
                    <input type="submit" class="btn btn-default" name="dt_enter_select" value="<?php echo $button_strings['Select']; ?>">
                </td>
            </tr>
        </table>
    </form>
<?php

elseif ($s_connected == true):
    $js_stack .= js_giveFocus('dt_enter_form', 'dt_enter_field_0');

    $df = new DataFormEnter($s_enter_name, $s_fields[$s_enter_name], $s_enter_values);

    echo $df->renderHTML();

endif;

?>
