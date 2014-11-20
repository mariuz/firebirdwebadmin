<?php
// Purpose        form for editing and entering a dataset
// Author         Lutz Brueckner <irie@gmx.de>
// Copyright      (c) 2000-2006 by Lutz Brueckner,
//                published under the terms of the GNU General Public Licence v.2,
//                see file LICENCE for details


/*
   DataForm Class for editing and entering a dataset in a html form

   DataForm gets extended by DataFormEdit and DataFormEnter which are used
   to display the form on the dt_edit- and dt_enter-panels
*/
class DataForm {

    /*
     * string, name of the table
     */
    protected $table = '';

    /*
     * array holding the field properties as returned from get_tables() in inc/get_tables.inc.php
     */
    protected $fields = array();

    /*
     * array holding the field values to display in the form elements
     */
    protected $values = array();

    /*
     * array holding the foreign key properties as returned from get_fk_lookups_data() in inc/foreign_keys.inc.php
     */
    protected $fk_data = array();

    /*
     * string used as a prefix for element names and ids
     */

    protected $postfix = '';

    /*
     * string used as a postfix for element names and ids
     */
    protected $prefix = '';

    /*
     * string, either 'edit' or 'enter'
     */
    protected $job = '';

    /*
     * string, action value for the form tag
     */
    protected $formaction = '';

    /*
     * string that is displayed at top of the form
     */
    protected $headline = '';

    /*
     * array holding the properties for the form buttons
     */
    protected $buttons = array();

    /*
      array holding the properties fpr checkbox elements displayed at the bottom of the form
    */
    protected $checkboxes = array();


    /*
     * Constructor, inits the class properties with the equal named parameters
     */
    public function __construct($table, $fields, $values, $prefix, $postfix='') {

        $this->table   = $table;
        $this->fields  = $fields;
        $this->values  = $values;
        $this->prefix  = $prefix;
        $this->postfix = $postfix;
        if ($GLOBALS['s_cust']['enter']['fk_lookup'] == TRUE  &&
            isset($GLOBALS['s_cust']['fk_lookups'][$table])) {

            $this->fk_data = get_fk_lookups_data($table, $GLOBALS['s_cust']['fk_lookups'][$table]);
        }
        $this->formaction = $_SERVER['PHP_SELF'];
    }

    /*
     * return the whole form element as a string
     */
    public function renderHtml() {
        global $dt_strings;

        $blobs_flag = have_blob($this->table);

        $html = '<form method="post" action="'.url_session($this->formaction).'" name="'.$this->_name('form').'"'.($blobs_flag ? ' enctype="multipart/form-data"' : '').">\n"
              . '<table border="0" cellpadding="0" cellspacing="0">'."\n"
              . "  <tr>\n"
              . "    <td>\n"
              . '      <table border="1" cellpadding="3" cellspacing="0">'."\n";
        if (!empty($this->headline)) {
            $html .= "        <tr>\n"
                   . '          <th colspan="2" align="left">'.$this->headline."</th>\n"
                   . "        </tr>\n";
        }

        $idx = 0;
        foreach($this->fields as $field) {
            $html .= $this->_renderField($field, $idx, $this->values[$idx]);
            $idx++;
        }

        $html .= "      </table>\n"
               . "    </td>\n"
               . '    <td valign="top" style="padding-left:5px">'."\n"
               . '      <span id="'.$this->_name('config')."\"></span>\n"
               . "    </td>\n"
               . "  </tr>\n"
               . "</table>"

               . $this->_renderCheckboxes()
               . $this->_renderButtons() 
 
               . "</form>\n";

        return $html;
    }

    /*
     * return the html for a captioned form element inside of a tables row as a string
     */
    protected function _renderField($field, $idx, $value) {
        global $sql_strings, $dt_strings;

        if (is_string($value)) {
            $value = htmlentities($value);
        }

        $element_name = $this->_name('field') . '_' . $idx;

        $html = "        <tr>\n"
              . '          <td valign="top"><b>'.$field['name']."</b></td>\n"
              . "          <td>\n";

        if ($field['type'] == 'BLOB') {
            if ($this->job == 'edit'  &&  $value !== NULL) {
                $size  = 32;
                $cname = $this->_name('drop_blob').'_'.$idx;
                $url   = url_session('showblob.php?where='.urlencode('WHERE '.$this->condition).'&table='.$this->table.'&col='.$field['name']);

                $blobdrop_str = '            <i><a href="'.$url.'" target="_blank"><b>BLOB</b></a>&nbsp;</i>'."\n"
                              . '            &nbsp;|&nbsp;<input type="checkbox" name="'.$cname.'" id="'.$cname.'">&nbsp;<label for="'.$cname.'">'.$dt_strings['Drop']."</label><br>\n";
            }
            else {
                $size  = 50;
                $blobdrop_str = '';
            }
            $html .= $blobdrop_str 
                   . '            <input type="file" size="50" name="'.$this->_name('file').'_'.$idx."\">\n";
            if ($field['stype'] == 1  ||  $GLOBALS['s_wt']['blob_as'][$field['name']] == 'text') {
                $html .= "            <br>\n"
                       . '            <textarea name="'.$element_name.'" cols="42" rows="3">'.$value."</textarea>\n";
            }
        }

        elseif (isset($field['comp'])) {
            $html .= '            '.$value."\n";
        }

        elseif (isset($this->fk_data[$field['name']])) {
            $html .= "            <table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">\n"
                   . "              <tr>\n"
                   . "                <td>\n"
                   . '                  '.get_indexed_selectlist($element_name, $this->fk_data[$field['name']]['data'], $value, TRUE)
                   . "                </td>\n"
                   . "                <td align=\"right\">\n"
                   . "                  <a href=\"javascript:requestColumnConfigForm('".$this->fk_data[$field['name']]['table']."', '".$this->table."', '".$field['name']."', '".$this->_name('config')."');\" class=\"act\">[".$sql_strings['Config']."]</a>\n"
                   . "                </td>\n"
                   . "              </tr>\n"
                   . "            </table>\n";
        }

        else {
            if (!isset($field['size'])) {
                $size = $maxlen = 20;
            } else {
                $maxlen = $field['size'];
                $size   = ($field['size'] + 1  > DATA_MAXWIDTH) ? DATA_MAXWIDTH : $field['size'] + 1;
            }

            $html .= '            <input type="text" size="'.$size.'" maxlength="'.$maxlen.'" name="'.$element_name.'" value="'.$value."\">\n";
        }

        $html .= "          </td>\n"
               . "        </tr>\n";

        return $html;
    }

    /*
     * return the html for the checkbocxes inside of a table as a string
     */
    protected function _renderCheckboxes() {

        $html = "<table>\n"
              . "  <tr>\n";
        foreach ($this->checkboxes as $checkbox) {
            $ename = 'dt_config_'.$checkbox['name'];
            $checked_str = $checkbox['checked'] ? ' checked' : '';

            $html .= "    <td>\n"
                   . '      <input type="checkbox" id="'.$ename.'" name="'.$ename.'"'.$checked_str.">\n"
                   . "    </td>\n"
                   . "    <td>\n"
                   . '      <label for="'.$ename.'">'.$checkbox['label']."</label>\n"
                   . "    </td>\n"
                   . "    <td>&nbsp;</td>\n";
        }
         
        $html .= "  </tr>\n"
               . "</table>\n";

        return $html;
    }

    /*
     * return the html for the buttons as a string
     */
    protected function _renderButtons() {

        $html = '';
        foreach ($this->buttons as $button) {
            $html .= sprintf('<input type="%s" name="%s" value="%s" class="bgrp">'."\n", $button['type'], $this->_name($button['name']), $button['value']);
        }

        return $html;
    }

    /*
     * return the completed name of an element as a string
     */
    protected function _name($name) {

        $str = $this->prefix . '_' . $name . (!empty($this->postfix) ? '_' . $this->postfix : '');
        
        return $str;
    }
}


/*
   DataForm Class as displayed on the dt_enter-panels
*/
class DataFormEnter extends DataForm {

    /*
     * initialize the extended DataForm to be suitable for the dt_enter-panel
     */
    public function __construct($table, $fields, $values) {
        global $dt_strings, $button_strings;

        parent::__construct($table, $fields, $values, 'dt_enter');

        $this->job = 'enter';

        $this->headline = $dt_strings['Table'].': '.$this->table;

        $this->buttons = array(array('type' => 'submit', 'name' => 'insert', 'value' => $button_strings['Insert']),
                               array('type' => 'reset',  'name' => 'reset',  'value' => $button_strings['Reset']),
                               array('type' => 'submit', 'name' => 'ready',  'value' => $button_strings['Ready'])
                               );

        $this->checkboxes = array(array('name' => 'more',      'checked' => $GLOBALS['s_cust']['enter']['another_row'], 'label' => $dt_strings['IARow']),
                                  array('name' => 'fk_lookup', 'checked' => $GLOBALS['s_cust']['enter']['fk_lookup'],   'label' => $dt_strings['FKLookup'])
                                  );
    }
}


/*
   DataForm Class as displayed on the dt_edit-panels
*/
class DataFormEdit extends DataForm {

    /*
     * string with the sql constraint needed for fetching the concerning dataset from the database
     */
    protected $condition = '';


    /*
     * initialize the extended DataForm to be suitable for the dt_enter-panel
     */
    public function __construct($table, $fields, $values, $condition, $instance) {
        global $dt_strings, $button_strings;

        parent::__construct($table, $fields, $values, 'dt_edit', $instance);

        $this->condition = $condition;

        $this->job = 'edit';

        $this->headline = 'WHERE '.htmlentities($condition);

        $this->buttons = array(array('type' => 'submit', 'name' => 'save',   'value' => $button_strings['Save']),
                               array('type' => 'reset',  'name' => 'reset',  'value' => $button_strings['Reset']),
                               array('type' => 'submit', 'name' => 'cancel', 'value' => $button_strings['Cancel'])
                               );

        $this->checkboxes = array(array('name' => 'as_new',    'checked' => $GLOBALS['s_cust']['enter']['as_new'],    'label' => $dt_strings['INRow']),
                                  array('name' => 'fk_lookup', 'checked' => $GLOBALS['s_cust']['enter']['fk_lookup'], 'label' => $dt_strings['FKLookup'])
                                  );
    }
}

?>
