<?php
// File           db_meta.php / FirebirdWebAdmin
// Purpose        displays the metadata for the selected database
// Author         Lutz Brueckner <irie@gmx.de>
// Copyright      (c) 2000, 2001, 2002, 2003, 2004, 2005 by Lutz Brueckner,
//                published under the terms of the GNU General Public Licence v.2,
//                see file LICENCE for details

if ($s_connected):

?>
<div class="if">
    <iframe src="<?php echo url_session('./iframe_content.php?key='.$iframekey_meta); ?>" width="98%" height="<?php echo $s_cust['iframeheight']; ?>" name="db_meta_iframe"></iframe>
</div>
<form method="post" action="<?php echo url_session($_SERVER['PHP_SELF']); ?>" name="db_meta_form">
	<input type="submit" class="btn btn-success" name="db_meta_save" value="<?php echo $button_strings['Save']; ?>">
</form>
<?php
endif;
?>
