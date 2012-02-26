// File           js/XMLHttpRequestClient.js / ibWebAdmin
// Purpose        javascript implementation of a client class for XMLHttpRequests
// Author         Lutz Brueckner <irie@gmx.de>
// Copyright      (c) 2000-2006 by Lutz Brueckner,
//                published under the terms of the GNU General Public Licence v.2,
//                see file LICENCE for details
// Created        <05/02/01 24:14:51 lb>
//
// $Id: XMLHttpRequestClient.js,v 1.4 2006/07/08 17:12:25 lbrueckner Exp $


/*
   by defining the request object in the global scope
   it is reusable for multible calls on mozilla browsers
*/
var xmlreq   = false;

function XMLHttpRequestClient(server_url) {

    var method = 'GET';
    var serverUrl = server_url;
    var response  = null;
    var jsCallback = null;
    var jsCallbackParameters = null;
    var debug = false;

    this.Request = doRequest;

    xmlreq = new XMLHttpRequest();
    return;

    function doRequest(handler, handler_parameters, callback, callback_parameters) {

        jsCallback = callback;
        jsCallbackParameters = callback_parameters;

        var sep = serverUrl.search(/\?/) == -1 ? '?' : '&';
        xmlreq.onreadystatechange = ProcessReqChange;
        xmlreq.open(method, serverUrl + sep + 'f=' + handler + _getUrlParameters(handler_parameters), true);
        xmlreq.setRequestHeader('Content-Type', 'text/xml; charset=' + php_charset);
        xmlreq.send(null);
    }

    function ProcessReqChange() {
	
	if (xmlreq.readyState == 4) {
            if (jsCallback != null) {
                response = xmlreq.responseText;
                eval(jsCallback + "(response" + _getParametersList(jsCallbackParameters) + ")");
            }
	}
    }

    function _getUrlParameters(parameters) {

        var str = '';
        for (var i=0; i<parameters.length; i++) {
            str = str + "&p" + i + "=" + encodeURIComponent(parameters[i]);
        }

        return str;
    }

    function _getParametersList(parameters) {

        var str = '';
        for (var i=0; i<parameters.length; i++) {
            str = str + ",'" + parameters[i] + "'";
        }

        return str;
    }
}
