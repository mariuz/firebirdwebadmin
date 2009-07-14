<?php
// File           stylesheet.php / ibWebAdmin
// Purpose        dynamic stylesheet generation
// Author         Lutz Brueckner <irie@gmx.de>
// Copyright      (c) 2000-2006 by Lutz Brueckner,
//                published under the terms of the GNU General Public Licence v.2,
//                see file LICENCE for details
// Created        <03/09/28 19:25:07 lb>
//
// $Id: stylesheet.php,v 1.17 2006/07/08 17:26:05 lbrueckner Exp $

require('./inc/configuration.inc.php');
require('./inc/functions.inc.php');

// make sure output-compression is turned off,
// oldish browsers like ns4.7 have problems with compressed stylesheet files
ini_set('zlib.output_compression', 'Off');

session_start();

// don't send the stylesheet if 
//   a) it's not the first request
//   b) $s_stylesheet_etag was not deleted after the customizing form was submitted
//   c) stylesheet chaching is not disabled in the configuration
if (isset($_SESSION['s_stylesheet_etag'])  &&  !empty($_SESSION['s_stylesheet_etag'])  &&
    request_header_check($_SESSION['s_stylesheet_etag'])  &&
    CACHE_STYLESHEET === TRUE) {

    header("HTTP/1.1 304 Not Modified");
    send_stylesheet_headers($_SESSION['s_stylesheet_etag']);
}

// send the stylesheet
else {
    $_SESSION['s_stylesheet_etag'] = md5(get_stylesheet($_SESSION['s_cust']));

    send_stylesheet_headers($_SESSION['s_stylesheet_etag']);
    echo get_stylesheet($_SESSION['s_cust']);
}


//
// send http headers to enable caching
//
function send_stylesheet_headers($etag) {

    header('Expires: ' . gmdate('D, d M Y H:i:s', (time() + 20000000)) . ' GMT');
    header('Cache-Control: public');
    header('Pragma: public');
    header('Etag: "' . $etag . '"');
    header('Content-Type: text/css');
}


//
// return TRUE if the the client is sending an If-None-Match or an If-Modified-Since header
// or if the server isn't ab apache 1.3
//
function request_header_check($etag='') {

    if (!function_exists('apache_request_headers')) {
        return TRUE;
    }

    foreach (apache_request_headers() as $header => $value) {
        if (strcasecmp($header, 'If-None-Match') == 0  ||  strcasecmp($header, 'If-Modified-Since') == 0) {

            // the header values should be checked ... later

            return TRUE;
        }
    }

    return FALSE;
}


//
// return a string with the css definitions
//
function get_stylesheet($customize) {

    foreach (get_colornames() as $colorname) {
        ${'color_'.$colorname} = $customize['color'][$colorname];
    }

    $fontsize = $customize['fontsize'].'pt';

return <<<EOT

body { 
    background-color: $color_background;
    font-family: Verdana, Arial, Helvetica, sans-serif;
    font-size: $fontsize;
}

td, th, input, select {
    font-family: Verdana, Arial, Helvetica, sans-serif;
    font-size: $fontsize;
}

pre {
    margin: 0px;
}

textarea {
    font-family: Courier,monospace;
    font-size: $fontsize;
}

input:focus, textarea:focus {
    background-color : $color_selectedinput;
}

input.bgrp {
    margin-right: 10px;
}

table { 
    margin: 2px 0px 2px 0px;
    border-collapse: collapse;
}

table.tsep {
    margin : 0px;
    border-collapse: separate;
}

th {
    background-color: $color_headline;
}

a:link, a:active, a:visited {
    color: $color_link;
    text-decoration: none;
}

a:hover {
    color: $color_linkhover;
    text-decoration: none;
}

a.dtitle, a.ptitle {
    color: black;
    font-weight: bold;
    white-space: nowrap;
}

a.act {
    font-weight: bold;
    padding: 0px 5px 0px 5px;
}

.panel { 
    background-color: $color_panel;
}

.area { 
    background-color:  $color_area;
}

.wttr {
    vertical-align: top;
 }

.wttr1  {
    background-color: $color_firstrow;
}

.wttr2  {
    background-color: $color_secondrow;
}

.detail {
    padding:  0px 3px 0px 3px;
}

.hex {
    font-family: monospace;
    font-size: $fontsize;
    padding: 0px 5px 0px 5px;
}

.err {
    font-weight: bold;
    color : red;
}

div.fk {
    position: static;
    left: 10px;
    width: auto;
    height: auto;
    overflow: auto;
    padding: 5px;
}

.selected {
    background: $color_selectedrow;
    color: HighlightText;
}

td.menu-left {
    padding-left: 15px;
    border-right: 2px solid $color_menuborder;
    border-bottom: 2px solid $color_menuborder;
}

td.menu-right {
    padding-left: 15px;
    border-bottom: 2px solid $color_menuborder;
    width: 100%;
}

td.menu-entry {
    border-right: 2px solid $color_menuborder;
    border-top: 2px solid $color_menuborder;
    padding: 2px 8px 2px 8px;
    white-space: nowrap;
}

td.menu-passive {
    background: $color_panel;
    border-bottom: 2px solid $color_menuborder;
}

a.menu-link {
    color: black;
    font-weight: bold;
}

iframe {
    border: 1px solid $color_iframeborder;
}

div.det {
    border-bottom: 1px solid black;
    margin: 1px;
}

div.cmt {
    margin-left: 28px;
}

div.if {
    text-align: center;
}

body.if { 
    background-color: $color_iframebackground;
}

EOT;
}

?>
