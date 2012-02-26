// File           js/markableTable.js / ibWebAdmin
// Purpose        mark rows of a table with mouse clicks
// Author         Lutz Brueckner <irie@gmx.de>
// Copyright      (c) 2000-2006 by Lutz Brueckner,
//                published under the terms of the GNU General Public Licence v.2,
//                see file LICENCE for details
// Created        <19/02/061 22:44:30 lb>
//
// $Id: markableTable.js,v 1.2 2006/07/08 17:13:44 lbrueckner Exp $


/*
 * mark rows of a table with mouse clicks
 * use shift- and ctrl-key to mark multiple rows or ranges of rows
 */
function markableTable(id, leaveColumns) {
    var table = $(id);
    if (!table) return;

    if (typeof leaveColumns == 'undefined') leaveColumns = [];

    this.rows         = table.getElementsByTagName('tr');   /* holding the table row objects */
    this.marked       = [];            /* indices of the marked rows in the rows array */
    this.leaveColumns = leaveColumns;  /* indices of columns which shoudn't get marked */
    this.spanStart    = false;         /* index for the row used as the start row in shift-clicks */

    var i=0, k=0;
    var ref = this;
    var f = function(e) { ref.onCellClicked(e) }
    for (i=0; i<this.rows.length; i++) {
        if (this.markableRow(this.rows[i])) {
            var cells = this.rows[i].getElementsByTagName('td');
            for (k=0; k<cells.length; k++) {
                if (this.markableCell(cells[k])) {
                    cells[k].addEventListener('click', f, false);
                }
            }
        }
    }
    return;
}

/* overwrite them in derived classes */
markableTable.prototype.markableRow  = function(row) { return true; };   /* indicates whether a row is markable or not */
markableTable.prototype.markableCell = function(cell) { return true; };  /* indicates whether a cell is markable or not */

markableTable.prototype.doMark       = function(cell) { cell.bgColor='yellow'};  /* mark a cell */
markableTable.prototype.doUnmark     = function(cell) { cell.bgColor='white'};   /* unmark a cell */

markableTable.prototype.rowMarked    = function(row) {};   /* gets called after a row was marked */
markableTable.prototype.rowUnmarked  = function(row) {};   /* gets called after a row was unmarked */

/*
  check whether cell is marked
*/
markableTable.prototype.isMarked = function(idx) {
    for (var i=0; i<this.marked.length; i++) {
        if (this.marked[i] == idx) {
            return true;
        }
    }
    return false;
}

/*
  mark a row
*/
markableTable.prototype.markRow = function(row) {
    var cells = row.getElementsByTagName('td');
    for (var i=0; i<cells.length; i++) {
        if (this.markableCell(cells[i])) {
            this.doMark(cells[i]);
        }
    }
    this.rowMarked(row);
}

/*
  unmark a row
*/
markableTable.prototype.unmarkRow = function(row) {
    if (typeof row == 'undefined') return;
    var cells = row.getElementsByTagName('td');
    for (var i=0; i<cells.length; i++) {
        if (this.markableCell(cells[i])) {
            this.doUnmark(cells[i]);
        }
    }
    this.rowUnmarked();
}

/*
 * Eventhandler for mouseclicks on the markable table cells
 */
markableTable.prototype.onCellClicked = function(e) {
    var row = e.target.parentNode;
    var i = 0, cnt = 0, idx = 0;;

    if (!this.spanStart) {
        this.spanStart = row.rowIndex;
    }
    
    // mark a single row
    if (!e.shiftKey  &&  !e.ctrlKey) {
        for (i=0; i<this.marked.length; i++) {
            this.unmarkRow(this.rows[this.marked[i]]);
        }
        this.markRow(row);
        this.marked = []; 
        this.marked.push(row.rowIndex);
        this.spanStart = row.rowIndex;
    }

    // mark span of rows
    else if (e.shiftKey) {
        for (i=0; i<this.marked.length; i++) {
            this.unmarkRow(this.rows[this.marked[i]]);
        }
        this.marked = [];

        if (row.rowIndex >= this.spanStart) {
            cnt = row.rowIndex - this.spanStart + 1;
            idx = this.spanStart;
        }
        else {
            cnt = this.spanStart - row.rowIndex + 1;
            idx = row.rowIndex;
        }
        for (i=0; i<cnt; i++) {
            if (this.markableRow(this.rows[idx])) {
                this.markRow(this.rows[idx]);
                this.marked.push(idx);
                idx++;
            }
        }
        window.getSelection().removeAllRanges();
    }

    // mark/unmark one ore more distinct rows
    else {  // ctrl == true
        if (this.isMarked(row.rowIndex)) {
            this.unmarkRow(row);
            for (i=0; i<this.marked.length; i++) {
                if (this.marked[i] == row.rowIndex) {
                    this.marked.splice(i, 1);
                    break;
                }
            }
        }
        else {
            this.markRow(row);
            this.marked.push(row.rowIndex);
        }
    }
}


/*
 * markableTable implementation used for sql results and system tables
 */
function markableIbwaTable(id, leaveColumns) {
    markableTable.call(this, id, leaveColumns);
}
markableIbwaTable.prototype = new markableTable();

markableIbwaTable.prototype.markableRow = function(row) {
    return row.rowIndex != 0 ? true : false;
}

markableIbwaTable.prototype.doMark = function(cell) {
    cell.className = 'selected';
}

markableIbwaTable.prototype.doUnmark = function (cell) {
    cell.className = '';
}


/*
 * markableTable implementation for the watchtable on the tb_watch-panel
 * inherits from markableIbwaTable which inherits from marableTable
 */
function  markableWatchtable(id, leaveColumns) {
    markableIbwaTable.call(this, id, leaveColumns);
    this.cnt = 0;
    this.report = true;
}
markableWatchtable.prototype = new markableIbwaTable();

markableWatchtable.prototype.markableCell = function(cell) {
    for (var i=0; i<this.leaveColumns.length; i++) {
        if (this.leaveColumns[i] == cell.cellIndex) {
            return false;
        }
    }
    return true;
}

markableWatchtable.prototype.rowMarked = function(row) {
    this.cnt++;
    if (this.cnt == 1) {
        $('tb_watch_mark_buttons').style.display = '';
    }
    var button = $('tb_watch_export');
    button.value = button.value.match(/\S+\s/) + '(' + this.cnt + ')';

//     if (this.report) {
//         alert('foo: ' + row.links.length[0]);
//         var req = new XMLHttpRequestClient(php_xml_http_request_server_url);
//         req.Request('markable_watchtable_report', new Array('mark', '1'), '', new Array());
//     }
};

markableWatchtable.prototype.rowUnmarked = function(row) {
    this.cnt--;
    var span = $('tb_watch_mark_buttons');
    if (this.cnt > 0) {
        var button = $('tb_watch_export');
        button.value = button.value.match(/\S+\s/) + '(' + this.cnt + ')';
    }
    else 
        $('tb_watch_mark_buttons').style.display = 'none';

//     if (this.report) {
//         var req = new XMLHttpRequestClient(php_xml_http_request_server_url);
//         req.Request('markable_watchtable_report', new Array('unmark', '1'), '', new Array());
//     }
};

markableWatchtable.prototype.unmarkAll = function() {
    this.report = false;
    for (var i=0; i<this.marked.length; i++) {
        this.unmarkRow(this.rows[this.marked[i]]);
    }
    this.marked = [];
    this.cnt = 0;
    $('tb_watch_mark_buttons').style.display = 'none';

    var req = new XMLHttpRequestClient(php_xml_http_request_server_url);
    req.Request('markable_watchtable_report', new Array('unmark', 'all'), '', new Array());
    this.report = true;
}
    
