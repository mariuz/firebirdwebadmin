// Purpose        collection of javascript functions
// Author         Lutz Brueckner <irie@gmx.de>
// Copyright      (c) 2000-2006 by Lutz Brueckner,
//                published under the terms of the GNU General Public Licence v.2,
//                see file LICENCE for details


function hide(id) {
    $('#'+id).hide();
}

function display(id) {
    $('#'+id).show();
}

// put the given html string into the specified div
function setInnerHtml(html, id) {
  if (!id) return;
  $('#'+id).html(html);
}

// find and return the value of the selected element in a selectlist
function selectedElement(source) {
    return source.options[source.selectedIndex].value;
}


// request and display the accociated values for a foreign key from the watchtable panel
function requestFKValues(table, column, value) {
    var req = new XMLHttpRequestClient(php_xml_http_request_server_url);
    req.Request("fk_values", new Array(table, column, value), "setInnerHtml", new Array());
}
function displayFKValues(html) {
    setInnerHtml(html, 'fk');
    display('fk');
}


function detailPrefix(type) {
    switch (type) {
    case 'table':
        return 't';
        break;
    case 'view':
        return 'v';
        break;
    case 'trigger':
        return 'r';
        break;
    case 'procedure':
        return 'p';
        break;
    default:
        return '';
    }
}
