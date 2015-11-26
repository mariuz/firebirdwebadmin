<?php
// File           sql_output.php / FirebirdWebAdmin
// Purpose        html sequence for the sql_output-panel in sql.php
// Author         Lutz Brueckner <irie@gmx.de>
// Copyright      (c) 2000-2006 by Lutz Brueckner,
//                published under the terms of the GNU General Public Licence v.2,
//                see file LICENCE for details

// Variables      $results   :   array holding the results from the ibase_query calls
//                $isql_flag :   TRUE if the query was done by isql, FALSE if by php
//                $binary_output : output lines from isql

?>
<form method="post" action="<?php echo url_session($_SERVER['PHP_SELF']); ?>" name="sql_output_form">
<table cellpadding="3" cellspacing="0">
  <tr>
    <td>
<?php

// display the output from isql
if (isset($isql_flag)  ||  isset($plan_flag)) {
    array_shift($binary_output);      // discard the first line
    foreach ($binary_output as $line) {
        $line = str_replace(' ', '&nbsp;', $line);
        $line = nl2br($line);
        echo $line."<br>\n";
    }
} elseif (isset($results)) {
    ob_start();

    foreach ($results as $idx => $result) {
        if (!is_array($result)  ||  empty($result)) {
            continue;
        }
        echo get_result_table($result, $fieldinfo[$idx], $idx);
        echo sql_export_button($idx);
    }

    $s_sql['buffer'] = ob_get_contents();
    ob_end_flush();
} elseif ($s_sql['buffer'] != '') {
    echo $s_sql['buffer'];
    echo '('.$sql_strings['DisplBuf'].')';
}

echo "    </td>\n  </tr>\n";

if ($s_sql['more'] === true) {
    ?>
  <tr>
    <td>
      <input type="submit" name="sql_display_all" value="<?php echo $button_strings['DisplAll'];
    ?>">
    </td>
  </tr>
<?php

}

?>
</table>
</form>
