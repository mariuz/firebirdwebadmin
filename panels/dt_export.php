<?php
// File           dt_export.php / FirebirdWebAdmin
// Purpose        html sequence for the export-panel in data.php
// Author         Lutz Brueckner <irie@gmx.de>
// Copyright      (c) 2000-2006 by Lutz Brueckner,
//                published under the terms of the GNU General Public Licence v.2,
//                see file LICENCE for details
// Created        <05/10/02 12:24:39 lb>
//
// $Id: dt_export.php,v 1.4 2006/07/08 17:19:13 lbrueckner Exp $


if ($s_connected):

?>
<form method="post" action="<?php echo url_session($_SERVER['PHP_SELF']).'#dt_export'; ?>" name="dt_csv_form" enctype="multipart/form-data">
<table cellpadding="3" cellspacing="0" style="margin-bottom:0px;">
  <tr>
    <td valign="top">
      <table border="1" cellpadding="3" cellspacing="0">
        <tr>
          <th align="left">Format</th>
        </tr>
        <tr>
          <td>
            <?php echo get_indexed_selectlist('dt_export_format', get_export_formats(), $s_export['format'], FALSE, array('onChange' => 'replaceExportFormatOptions(this.value);')); ?>
          </td>
        </tr>
      </table>
    </td>
    <td valign="top">
      <table border cellpadding="3" cellspacing="0">
        <tr>
          <th align="left" colspan="2">Source</th>
        </tr>
        <tr>
          <td valign="top">
            <?php echo get_indexed_selectlist('dt_export_source', get_export_sources(), $s_export['source']['option'], FALSE, array('onChange' => 'setExportSource(this.value);')); ?>
          </td>
          <td>
            <span id="dt_export_source_table_span" style="display:<?php $s_export['source']['option'] == 'table' ? print 'block' : print 'none'; ?>;">
              <?php echo get_table_selectlist('dt_export_source_table', array('select'), $s_export['source']['table'], TRUE); ?>
            </span>
            <span id="dt_export_source_dbtables_span" style="display:<?php $s_export['source']['option'] == 'db' ? print 'block' : print 'none'; ?>;">
              <?php echo get_table_selectlist('dt_export_source_dbtables[]', array('select'), $s_export['source']['dbtables'], FALSE, array('multiple' =>'multiple'), 4); ?>
            </span>
          </td>
        </tr>
      </table>
    </td>
    <td valign="top">
      <table border="1" cellpadding="3" cellspacing="0">
        <tr>
          <th align="left" colspan="2">Target</th>
        </tr>
        <tr>
          <td>
            <?php echo get_indexed_selectlist('dt_export_target', get_export_targets(), $s_export['target']['option'], FALSE, array('onChange' => "if (this.value=='screen') hide('dt_export_filename_span'); else display('dt_export_filename_span'); setExportTarget(this.value);")); ?>
          </td>
          <td>
            <span id="dt_export_filename_span" style="display:<?php $s_export['target']['option'] == 'file' ? print 'block' : print 'none'; ?>;">
              <?php echo get_textfield('dt_export_target_filename', 30, 255, $s_export['target']['filename'], 'text', array('id' => 'dt_export_target_filename')); ?>&nbsp;&nbsp;<?php echo $dt_strings['EntName']; ?>
            </span>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
<div  id="dt_export_query_div" style="display:<?php $s_export['source']['option'] == 'query' ? print 'block' : print 'none'; ?>;">
<table border="1" cellpadding="3" cellspacing="0">
  <tr>
    <th align="left" colspan="2">Query</th>
  </tr>
  <tr>
    <td>
      <textarea name="dt_export_query" rows="<?php echo $s_cust['textarea']['rows']; ?>" cols="<?php echo $s_cust['textarea']['cols']; ?>" wrap="virtual"><?php echo htmlspecialchars($s_export['source']['query']); ?></textarea>
    </td>
  </tr>
</table>
</div>
<table border="0" cellpadding="3" cellspacing="0" style="margin-top:0px;">
  <tr>
    <td valign="top" id="dt_export_format_options">
      <?php echo export_format_options_table($s_export); ?> 
    </td>
    <td valign="top">
      <table border="1" cellpadding="3" cellspacing="0">
        <tr>
          <th align="left" colspan="2"><?php echo $dt_strings['GenOpts']; ?></th>
        </tr>
        <tr>
          <td>
            <table border="0" cellpadding="3" cellspacing="0">
              <tr>
                <td>
                  <?php echo $dt_strings['ReplNull']."\n"; ?>
                </td>
                <td>
                  <?php echo get_textfield('dt_export_replnull', 8, 255, $s_export['general']['replnull']); ?>
                </td>
              </tr>
              <tr>
                <td>
                  <?php echo $dt_strings['DFormat']."\n"; ?>
                </td>
                <td>
                  <?php echo get_textfield('dt_export_date', 10, 255, $s_export['general']['date']); ?>
                </td>
              </tr>
              <tr>
                <td>
                  <?php echo $dt_strings['TFormat']."\n"; ?>
                </td>
                <td>
                  <?php echo get_textfield('dt_export_time', 10, 255, $s_export['general']['time']); ?>
                </td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td>
      <input type="submit" name="dt_export_doit" value="<?php echo $button_strings['Export']; ?>">
    </td>
    <td align="right">
      <input type="submit" name="dt_export_defaults" value="<?php echo $button_strings['Defaults']; ?>">
    </td>
  </tr>
</table>


</form>
<?php

endif;

if (isset($iframekey_export)):

?>
<div class="if" id="dt_export_iframe">
  <iframe src="<?php echo url_session('./iframe_content.php?key='.$iframekey_export); ?>" width="98%" height="<?php echo $s_cust['iframeheight']; ?>" name="adm_export_iframe"></iframe>
</div>
<br />
<?php

endif;

?>