<?php
// File           debug_funcs.inc.php / FirebirdWebAdmin
// Purpose        provides functions for debuging purpose
// Author         Lutz Brueckner <irie@gmx.de>
// Copyright      (c) 2000, 2001, 2002, 2003, 2004 by Lutz Brueckner,
//                published under the terms of the GNU General Public Licence v.2,
//                see file LICENCE for details

//
// write the content of php's output_buffer to $fname
//
function write_output_buffer($fname)
{
    $fp = fopen($fname, 'w')
        or die('Error opening file '.$fname);
    fwrite($fp, ob_get_contents())
        or die('Error writing to file '.$fname);
    ob_end_flush();
}

//
// output the distance between $start and $end,
// which are resultstrings from microtime()
//
function show_time_consumption($start, $end)
{
    list($sm, $ss) = explode(' ', $start);
    list($em, $es) = explode(' ', $end);
    $elapsed = $es - $ss + $em - $sm;
    echo 'time consumption: '.$elapsed."<br>\n";
}

// add a string to array $debug[], $debug[] is printed on the info-panel if DEBUG == TRUE
// call this function with one or three parameters:
//
// $str         : if $$str is a variable print its name and value, else just print $str
// $file, $line : are thought to be __FILE__ and __LINE__ at the place the function is called
//
function add_debug($str, $file = null, $line = null)
{
    if ($file == null || $line == null) {
        $dstr = "<tr>\n<td colspan=\"2\">";
        if (isset($GLOBALS[$str])) {
            $dstr .= add_var_debug($str, "<br>\n");
        } else {
            $dstr .= "$str<br>\n";
        }
    } else {
        $dstr = "<tr>\n<td>$file, $line:</td>\n";
        if (isset($GLOBALS[$str])) {
            $dstr .= '<td>'.add_var_debug($str, "<br>\n");
        } else {
            $dstr .= "<td>$str<br>\n";
        }
    }
    $dstr .= "</td>\n</tr>\n";
    $GLOBALS['debug'][] = $dstr;
}

function add_var_debug($var, $separator)
{
    if (!is_array($GLOBALS[$var])) {
        return($var.' = '.$GLOBALS[$var]);
    } else {
        $str = $var.' = array('.$separator;
        $arr = $GLOBALS[$var];
        foreach ($arr as $key => $val) {
            $str .= $key.' => '.$val.$separator;
        }
        $str .= ')'.$separator;

        return $str;
    }
}

//
// append debugging output $str to the file debug.txt in the temporay directory
//
function file_debug($str)
{
    include_once 'inc/configuration.inc.php';

    $fp = fopen(TMPPATH.'debug.txt', 'a') or die('Error: cannot open file for debug output');
    fwrite($fp, $str);
    fclose($fp);
}

//
// pop up a javascript window displaying $string
//
function js_alert($string)
{
    ?>
<script language="JavaScript">
  <!--
   alert("<?php echo $string;
    ?>");
  //-->
  </script>
<?php

}

//
// display all session variables
//
function show_session()
{
    debug_var($GLOBALS['HTTP_SESSION_VAR']);
}

// display content and structure of $var and die()
function debug_die($var)
{
    debug_var($var);
    die();
}

// display content and structure of $var
function debug_var($var)
{
    @include_once 'Var_Dump.php';
    if (class_exists('Var_Dump')) {
        Var_Dump::displayInit(array('display_mode' => 'HTML4_Text'),
                              array('mode' => 'normal',
                                    'offset' => 3,
                                    'before_type' => '<font color="#006600">',
                                    'after_type' => '</font>',
                                    'before_value' => '<font color="#000088">',
                                    'after_value' => '</font>',
                                    )
                              );
        Var_Dump::display($var);
    } else {
        echo "<pre>\n";
        print_r($var);
        echo "</pre>\n";
    }
}

?>
