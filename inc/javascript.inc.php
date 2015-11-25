<?php
// File           javascript.inc.php / FirebirdWebAdmin
// Purpose        inline JavaScript functions
// Author         Lutz Brueckner <irie@gmx.de>
// Copyright      (c) 2000-2006 by Lutz Brueckner,
//                published under the terms of the GNU General Public Licence v.2,
//                see file LICENCE for details


//
// print a JavaScript function that checks the settings for 'Not Null',
// 'Unique' and 'Primary' in a col_def_defination field
//
// -> only one of 'Unique' and 'Primary' can be selected
// -> if 'Unique' or 'Primary' is selected, autoselect 'Not Null'
//
// opt   : name of the selected checkbox
// index : index of the col_def_definition
// form  : form object
//
function js_checkColConstraint()
{
    static $done = false;

    if ($done == true) {
        return '';
    }

    echo <<<EOT
<script language="JavaScript" type="text/javascript">
<!--
function checkColConstraint(form, opt, index) {

    with (form) {

        if ((eval("cd_def_unique" + index).checked == true)
        ||  (eval("cd_def_primary" + index).checked == true)) {
            eval("cd_def_notnull" + index).checked = true;
        }

        if (("cd_def_unique" + index) == opt) {
            if  ((eval("cd_def_primary" + index).checked == false)
            &&   (eval("cd_def_unique" + index).checked == true)) {
                eval("cd_def_unique" + index).checked = true;
            }
            else {
                eval("cd_def_unique" + index).checked = false;
            }
        }

        if (("cd_def_primary" + index) == opt) {
            if ((eval("cd_def_unique" + index).checked == false)
            &&  (eval("cd_def_primary" + index).checked == true)) {
                eval("cd_def_primary" + index).checked = true;
            }
            else {
                  eval("cd_def_primary" + index).checked = false;
            }
        }
    }
}
//-->
</script>

EOT;

    $done = true;
}

//
// return a string with a javascript to give the focus to $field in $form
// (because ns4.7 fails on js inside of a table, this is written to
//  a string $js_stack, which is printed out in script_end.inc.php)
//
function js_giveFocus($form, $field)
{
    $js = "<script language=\"JavaScript\" type=\"text/javascript\">\n<!--\n";
    $js .= "    window.document.$form.$field.focus();\n";
    $js .= "//-->\n</script>\n";

    return $js;
}

//
// set width and height of the window
//
function js_window_resize($width, $height)
{
    $js = "<script language=\"JavaScript\" type=\"text/javascript\">\n<!--\n"
           ."   window.resizeTo($width, $height);\n"
          ."//-->\n</script>\n";

    return $js;
}

//
// builds a javascript array with the collation definitions
// and a function to restrict the collation selectlist according to the selected charset
//
// Parameter: charsets   charset definitions, $_SESSION['s_charsets']
//
//            source     charsets selectlist object
//            target     collations selectlist object
//
function js_collations($charsets)
{
    static $done = false;

    if ($done == true) {
        return '';
    }

    $js = "<script language=\"JavaScript\" type=\"text/javascript\">\n<!--\n"
          ."    var collations = new Array();\n";

    foreach ($charsets as $cs) {
        $js .= '    collations["'.$cs['name']."\"] = new Array();\n";
        $n = 0;
        foreach ($cs['collations'] as $coll) {
            $js .= '    collations["'.$cs['name'].'"]['.$n.'] = "'.$coll."\";\n";
            ++$n;
        }
    }
    $js .= "\n";

    $js .= <<<EOT
    function adjustCollation(source, target) {
        var i, charset;
        for(i=0; i<source.length; i++) {
            if(source.options[i].selected == true) {
                charset = source.options[i].value;
            }
        }
        cnt = target.options.length;
        for (i=0; i<cnt; i++){
            target.options[0] = null;
        }
        target.options[0] = new Option("", "");
        if (typeof(collations[charset]) == "object") {
            for (i=0; i<collations[charset].length; i++){
                target.options[i+1] = new Option(collations[charset][i], collations[charset][i]);
            }
        }
    }
//-->
</script>

EOT;

    $done = true;

    return $js;
}

//
// include the XMLHttpRequestClient library
//
function js_xml_http_request_client()
{
    static $done = false;

    if ($done == true) {
        return '';
    }
    $done = true;

    return js_javascript_file('js/XMLHttpRequestClient.js');
}

function js_markable_table()
{
    static $done = false;

    if ($done == true) {
        return '';
    }
    $done = true;

    return js_javascript_file('js/markableTable.js');
}

//
// return the URL of the server-script for the XMLHttpRequests
//
function xml_http_request_server_url()
{
    static $url;

    if (!isset($url)) {
        $script = !empty($_SERVER['PHP_SELF']) ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME'];
        $script = substr($script, 0, strrpos($script, '/')).'/inc/xml_http_request_server.php';
        $script = url_session($script);

        $url = PROTOCOL.'://'.$_SERVER['HTTP_HOST'].$script;
    }

    return $url;
}

//
// request and display the form for a column configuration from the dt_enter- or dt_edit-panel
//
function js_request_column_config_form()
{
    $server_url = xml_http_request_server_url();

    $js = <<<EOT
    <script language="javascript"  type="text/javascript">
    function requestColumnConfigForm(fk_table, table, column, divId) {
        var req = new XMLHttpRequestClient("$server_url");
        req.Request("column_config_form", new Array(fk_table, table, column), "setInnerHtml", new Array(divId));
    }
    </script>

EOT;

    return $js;
}

//
// function to request and display a closed panel
//
function js_request_close_panel()
{
    $server_url = xml_http_request_server_url();

    $js = <<<EOT
    <script language="javascript" type="text/javascript">
    function requestClosedPanel(idx, active) {
        var req = new XMLHttpRequestClient("$server_url");
        req.Request("closed_panel", new Array(idx, active), "setInnerHtml", new Array("p" + idx));
    }
    </script>

EOT;

    return $js;
}

//
// functions to request, display and hide the details for a database object
//
function js_request_details()
{
    static $done = false;

    if ($done == true) {
        return '';
    }

    $server_url = xml_http_request_server_url();

    $js = <<<EOT
    <script language="javascript" type="text/javascript">
    function requestDetail(type, name, title) {
        var req = new XMLHttpRequestClient("$server_url");
        req.Request("detail_view", new Array(type, name, title), "setInnerHtml", new Array(detailPrefix(type) + '_' + name));
    }
    function closeDetail(type, id, name, title) {
        var req = new XMLHttpRequestClient("$server_url");
        req.Request("detail_close", new Array(type, name, title), "setInnerHtml", new Array(id));
    }
    </script>

EOT;

    $done = true;

    return $js;
}

//
// functions to request the values for a foreign key on the tb_watch panel
//
function js_request_fk()
{
    $server_url = xml_http_request_server_url();

    $js = <<<EOT
    <script language="javascript" type="text/javascript">
    function requestFKValues(table, column, value) {
        var req = new XMLHttpRequestClient("$server_url");
        req.Request("fk_values", new Array(table, column, value), "setInnerHtml", new Array("fk"));
    }
    </script>

EOT;

    return $js;
}

//
// functions used for the system table filters
//
function js_request_filter_fields()
{
    $server_url = xml_http_request_server_url();

    $js = <<<EOT
    <script language="javascript" type="text/javascript">
    function getFilterFields(table) {
        var req = new XMLHttpRequestClient("$server_url");
        req.Request("systable_filter_fields", new Array(table), "setInnerHtml", new Array("systable_field"));
    }
    function getFilterValues(table, field) {
        var req = new XMLHttpRequestClient("$server_url");
        req.Request("systable_filter_values", new Array(table, field), "setInnerHtml", new Array("systable_value"));
    }
    </script>

EOT;

    return $js;
}

//
// request a selectlist filled with the columns of a table
//
function js_request_table_columns()
{
    $server_url = xml_http_request_server_url();

    $js = <<<EOT
    <script language="javascript" type="text/javascript">
    function requestTableColumns(table, id, restriction) {
        var req = new XMLHttpRequestClient("$server_url");
        req.Request("table_columns_selectlist", new Array(table, id, restriction), "setInnerHtml", new Array(id));
    }
    </script>

EOT;

    return $js;
}

//
// functions to get the content of a sql buffer and to put it into the textarea on the sql-enter panel
//
function js_request_sql_buffer()
{
    $server_url = xml_http_request_server_url();
    $history_size = SQL_HISTORY_SIZE;

    $js = <<<EOT
    <script language="javascript" type="text/javascript">
    function requestSqlBuffer(idx) {
        var req = new XMLHttpRequestClient("$server_url");
        req.Request("sql_buffer", new Array(idx), "putSqlBuffer", new Array(idx));
    }

    function putSqlBuffer(sql, idx) {
        $("sql_script").value = sql;
    }
    </script>

EOT;

    return $js;
}

//
// functions used for the export-options form
//
function js_data_export()
{
    $server_url = xml_http_request_server_url();

    $js = <<<EOT
    <script language="javascript" type="text/javascript">
    function replaceExportFormatOptions(format) {
        var req = new XMLHttpRequestClient("$server_url");
        req.Request("data_export_format_options", new Array(format), "setInnerHtml", new Array("dt_export_format_options"));

        hide("dt_export_iframe");

        var ele =  $("dt_export_target_filename");
        if (ele) {
            var filename= ele.value;
            if (filename.lastIndexOf(".") + 4 == filename.length) {
                ele.value = filename.substring(0, filename.lastIndexOf(".") + 1) + format;
            }
        }
    }

    function setExportTarget(target) {
        var req = new XMLHttpRequestClient("$server_url");
        req.Request("set_export_target", new Array(target), "", new Array());
    }

    function setExportSource(source) {
        var req = new XMLHttpRequestClient("$server_url");
        req.Request("set_export_source", new Array(source), "", new Array());

        hide("dt_export_iframe");

        if (source == 'table') {
            hide("dt_export_source_dbtables_span");
            hide("dt_export_query_div");
            display("dt_export_source_table_span")
        }
        else if (source == 'db') {
            hide("dt_export_source_table_span");
            hide("dt_export_query_div");
            display("dt_export_source_dbtables_span");
        }
        else if (source == "query") {
            hide("dt_export_source_table_span");
            hide("dt_export_source_dbtables_span");
            display("dt_export_query_div");
        }
    }
    </script>

EOT;

    return $js;
}

//
// request a textarea for editing the comment for a table, sp, trigger, view, ...
//
function js_request_comment_area()
{
    $server_url = xml_http_request_server_url();

    $js = <<<EOT
    <script language="javascript" type="text/javascript">
    function requestCommentArea(type, name) {
        var req = new XMLHttpRequestClient("$server_url");
        req.Request("comment_area", new Array(type, name), "setInnerHtml", new Array(detailPrefix(type) + 'c_' + name));
    }
    </script>

EOT;

    return $js;
}

//
// auto-refresh feature on thy systables panel for Firebird temporary system tables
//
// TODO: this is left to change for using XMLHttpRequests
function js_refresh_systable()
{
    $js = <<<EOT
    <script language="javascript" type="text/javascript">
    var sttimer;
    function refresh_systable(seconds) {
        if (sttimer) {
            window.clearInterval(sttimer);
        }
        if (seconds != 0) {
            sttimer = window.setInterval('requestSystable()', seconds*1000);
        }
        else {
            requestSystable(0);
        }
    }

    function requestSystable() {
        jsrsPOST = true;
        jsrsExecute("%1\$s", displaySystable, "systable", Array(document.db_systable_form.db_refresh.value));
    }

    function displaySystable(returnstring) {
        var result = jsrsArrayFromString(returnstring, "~");
        if (result[0].length > 0) {
            var target = $("st");
            target.innerHTML = result[0];
            if (str) delete str;
            str = new SelectableTableRows($("systable"), true)
        }
    }
    </script>

EOT;

    return sprintf($js, url_session('jsrs/systable_request.php'));
}

//
// return inline javascript as a string
//
function js_javascript($js)
{
    return '<script language="JavaScript" type="text/javascript">'.$js."</script>\n";
}

function js_javascript_file($file)
{
    return '<script src="'.$file."\" type=\"text/javascript\"></script>\n";
}

function js_javascript_variable($type, $name, $value)
{
    switch ($type) {
    case 'string':
        $value_str = "'".$value."'";
        break;
    default:
        $value_str = "''";
    }

    return 'var '.$name.'='.$value_str.";\n";
}

function js_global_variables()
{
    return "<script language=\"javascript\" type=\"text/javascript\">\n"
         .js_javascript_variable('string', 'php_session_name', session_name())
         .js_javascript_variable('string', 'php_session_id', session_id())
         .js_javascript_variable('string', 'php_xml_http_request_server_url', xml_http_request_server_url())
         .js_javascript_variable('string', 'php_charset', $GLOBALS['charset'])
         ."</script>\n";
}
