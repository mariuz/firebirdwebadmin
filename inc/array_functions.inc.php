<?php
// File           array_functions.inc.php / FirebirdWebAdmin
// Purpose        functions for juggling with arrays
// Author         Lutz Brueckner <irie@gmx.de>
// Copyright      (c) 2000-2006 by Lutz Brueckner,
//                published under the terms of the GNU General Public Licence v.2,
//                see file LICENCE for details

//
// move the element from $pos to the top of the array, return the array
//
function array_moveto_top($arr, $pos)
{
    $newarr[] = $arr[$pos];
    for ($i = 0; $i < count($arr); ++$i) {
        if ($i != $pos) {
            $newarr[] = $arr[$i];
        }
    }

    return $newarr;
}

//
// determine the maximum index from an numeric indexed array
//
function get_max_key($arr)
{
    end($arr);
    $key = key($arr);

    return $key;
}
