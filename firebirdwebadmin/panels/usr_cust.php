<?php
// File           panels/usr_cust.php / ibWebAdmin
// Purpose        html for the customizing-panel in user.php
// Author         Lutz Brueckner <irie@gmx.de>
// Copyright      (c) 2000, 2001, 2002, 2003, 2004, 2005 by Lutz Brueckner,
//                published under the terms of the GNU General Public Licence v.2,
//                see file LICENCE for details
// Created        <03/09/28 18:14:41 lb>
//
// $Id: usr_cust.php,v 1.8 2005/09/25 17:01:07 lbrueckner Exp $

if (empty($_COOKIE)):

    echo '<div style="padding: 20px 0px 20px 15px">'.$MESSAGES['COOKIES_NEEDED']."</div>\n";

else:

?>
<form method="post" action="<?php echo url_session($_SERVER['PHP_SELF']); ?>" name="usr_role_form">
<table border="0" cellpadding="3" cellspacing="0">
  <tr>
    <td>
      <table border cellpadding="3" cellspacing="0">
        <tr>
          <th colspan="4" align="left"><?php echo $usr_strings['ColSet']; ?></th>
        </tr>
        <tr>
          <td><?php echo $usr_strings['CBg']; ?></td>
          <td><?php echo get_textfield('usr_cust_background', 7, 7, ifsetor($_POST['usr_cust_background'], $s_cust['color']['background'])); ?></td>
          <td width="45" style="background-color: <?php echo $s_cust['color']['background']; ?>; border-style: solid; border-width: 1px; margin: 5px:">&nbsp;</td>
        </tr>
        <tr>
          <td><?php echo $usr_strings['CPanel']; ?></td>
          <td><?php echo get_textfield('usr_cust_panel', 7, 7, ifsetor($_POST['usr_cust_panel'], $s_cust['color']['panel'])); ?></td>
          <td width="45" style="background-color: <?php echo $s_cust['color']['panel']; ?>; border-style: solid; border-width: 1px;">&nbsp;</td>
        </tr>
        <tr>
          <td><?php echo $usr_strings['CArea']; ?></td>
          <td><?php echo get_textfield('usr_cust_area', 7, 7, ifsetor($_POST['usr_cust_area'], $s_cust['color']['area'])); ?></td>
          <td width="45" style="background-color: <?php echo $s_cust['color']['area']; ?>; border-style: solid; border-width: 1px;">&nbsp;</td>
        </tr>
        <tr>
          <td><?php echo $usr_strings['CHeadline']; ?></td>
          <td><?php echo get_textfield('usr_cust_headline', 7, 7, ifsetor($_POST['usr_cust_headline'], $s_cust['color']['headline'])); ?></td>
          <td width="45" style="background-color: <?php echo $s_cust['color']['headline'] ;?>; border-style: solid; border-width: 1px;">&nbsp;</td>
        </tr>
        <tr>
          <td><?php echo $usr_strings['CMenubrd']; ?></td>
          <td><?php echo get_textfield('usr_cust_menuborder', 7, 7, ifsetor($_POST['usr_cust_menuborder'], $s_cust['color']['menuborder'])); ?></td>
          <td width="45" style="background-color: <?php echo $s_cust['color']['menuborder']; ?>; border-style: solid; border-width: 1px;">&nbsp;</td>
        </tr>
        <tr>
          <td><?php echo $usr_strings['CIfBorder']; ?></td>
          <td><?php echo get_textfield('usr_cust_iframeborder', 7, 7, ifsetor($_POST['usr_cust_iframeborder'], $s_cust['color']['iframeborder'])); ?></td>
          <td width="45" style="background-color: <?php echo $s_cust['color']['iframeborder']; ?>; border-style: solid; border-width: 1px;">&nbsp;</td>
        </tr>
        <tr>
          <td><?php echo $usr_strings['CIfBg']; ?></td>
          <td><?php echo get_textfield('usr_cust_iframebackground', 7, 7, ifsetor($_POST['usr_cust_iframebackground'], $s_cust['color']['iframebackground'])); ?></td>
          <td width="45" style="background-color: <?php echo $s_cust['color']['iframebackground']; ?>; border-style: solid; border-width: 1px;">&nbsp;</td>
        </tr>
        <tr>
          <td><?php echo $usr_strings['CLink']; ?></td>
          <td><?php echo get_textfield('usr_cust_link', 7, 7, ifsetor($_POST['usr_cust_link'], $s_cust['color']['link'])); ?></td>
          <td width="45" style="background-color: <?php echo $s_cust['color']['link']; ?>; border-style: solid; border-width: 1px;">&nbsp;</td>
        </tr>
        <tr>
          <td><?php echo $usr_strings['CHover']; ?></td>
          <td><?php echo get_textfield('usr_cust_linkhover', 7, 7, ifsetor($_POST['usr_cust_linkhover'], $s_cust['color']['linkhover'])); ?></td>
          <td width="45" style="background-color: <?php echo $s_cust['color']['linkhover']; ?>; border-style: solid; border-width: 1px;">&nbsp;</td>
        </tr>
        <tr>
          <td><?php echo $usr_strings['CSelRow']; ?></td>
          <td><?php echo get_textfield('usr_cust_selectedrow', 7, 7, ifsetor($_POST['usr_cust_selectedrow'], $s_cust['color']['selectedrow'])); ?></td>
          <td width="45" style="background-color: <?php echo $s_cust['color']['selectedrow']; ?>; border-style: solid; border-width: 1px;">&nbsp;</td>
        </tr>
        <tr>
          <td><?php echo $usr_strings['CSelInput']; ?></td>
          <td><?php echo get_textfield('usr_cust_selectedinput', 7, 7, ifsetor($_POST['usr_cust_selectedinput'], $s_cust['color']['selectedinput'])); ?></td>
          <td width="45" style="background-color: <?php echo $s_cust['color']['selectedinput']; ?>; border-style: solid; border-width: 1px;">&nbsp;</td>
        </tr>
        <tr>
          <td><?php echo $usr_strings['CFirstRow']; ?></td>
          <td><?php echo get_textfield('usr_cust_firstrow', 7, 7, ifsetor($_POST['usr_cust_firstrow'], $s_cust['color']['firstrow'])); ?></td>
          <td width="45" style="background-color: <?php echo $s_cust['color']['firstrow']; ?>; border-style: solid; border-width: 1px;">&nbsp;</td>
        </tr>
        <tr>
          <td><?php echo $usr_strings['CSecRow']; ?></td>
          <td><?php echo get_textfield('usr_cust_secondrow', 7, 7, ifsetor($_POST['usr_cust_secondrow'], $s_cust['color']['secondrow'])); ?></td>
          <td width="45" style="background-color: <?php echo $s_cust['color']['secondrow']; ?>; border-style: solid; border-width: 1px;">&nbsp;</td>
        </tr>
      </table>
    </td>
    <td>&nbsp;</td>
    <td valign="top">
      <table border cellpadding="3" cellspacing="0">
        <tr>
          <th colspan="2" align="left"><?php echo $usr_strings['Appearance']; ?></th>
        </tr>
        <tr>
          <td><?php echo $usr_strings['Fontsize']; ?></td>
          <td align="right"><?php echo get_textfield('usr_cust_fontsize', 2, 2, $s_cust['fontsize']); ?></td>
        </tr>
        <tr>
          <td><?php echo $usr_strings['Language']; ?></td>
          <td align="right"><?php echo get_selectlist('usr_cust_language', get_customize_languages(), $s_cust['language']); ?></td>
        </tr>
        <tr>
          <td><?php echo $usr_strings['TACols']; ?></td>
          <td align="right"><?php echo get_textfield('usr_cust_tacols', 3, 3, $s_cust['textarea']['cols']); ?></td>
        </tr>
        <tr>
          <td><?php echo $usr_strings['TARows']; ?></td>
          <td align="right"><?php echo get_textfield('usr_cust_tarows', 3, 3, $s_cust['textarea']['rows']); ?></td>
        </tr>
        <tr>
          <td><?php echo $usr_strings['IFHeight']; ?></td>
          <td align="right"><?php echo get_textfield('usr_cust_ifheight', 4, 4, $s_cust['iframeheight']); ?></td>
        </tr>
      </table>
      <table border cellpadding="3" cellspacing="0">
        <tr>
          <th colspan="2" align="left"><?php echo $usr_strings['Attitude']; ?></th>
        </tr>
        <tr>
          <td><?php echo $usr_strings['AskDel']; ?></td>
          <td align="right"><?php echo get_selectlist('usr_cust_askdel', array($usr_strings['Yes'], $usr_strings['No']), ($s_cust['askdel'] == 1 ? $usr_strings['Yes'] : $usr_strings['No'])); ?></td>
        </tr>
      </table>
    </td>
  </tr>
</table>
<table border="0" cellpadding="3" cellspacing="0">
  <tr>
    <td>
      <input type="submit" name="usr_cust_save" value="<?php echo $button_strings['Save']; ?>">&nbsp;&nbsp;&nbsp;
    </td>
    <td width="350">&nbsp;</td>
    <td>
      <input type="submit" name="usr_cust_defaults" value="<?php echo $button_strings['Defaults']; ?>">
    </td>
  </tr>
</table>
</form>
<?php

endif;

?>