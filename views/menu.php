<?php
/**
 * Created by PhpStorm.
 * User: gilcierweb
 * Date: 14/05/15
 * Time: 08:20
 */ 
 
	require_once('./inc/script_start.inc.php');
	
	if ($s_connected == TRUE) {
		$dstr = (!empty($s_login['host'])) ? $s_login['host'] . ':' . $s_login['database'] : $s_login['database'];
		$rstr = !empty($s_login['role']) ? '&nbsp;(' . $s_login['role'] . ')' : '';
		$ustr = $s_login['user'] . $rstr;
	} else {
		$dstr = '&lt;none&gt;';
		$ustr = '';
	}
	
 ?>
<!-- Fixed navbar -->
<nav class="navbar navbar-default navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="#">FirebirdWebAdmin 3.0</a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
            <?= get_tabmenu_top_fixed($s_page) ?>
			
			<?php if ($s_connected == TRUE) { ?>
			<ul class="nav navbar-nav navbar-right">
				<li class="dropdown">
				  <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><?php echo $ustr; ?> <span class="caret"></span></a>
				  <ul class="dropdown-menu">
				    <li class="dropdown-header"><?php echo $info_strings['Connected']; ?>:</li>
					<li><a href="#"><?php echo $dstr; ?></a></li>
					<li role="separator" class="divider"></li>
					<li><a href="logout.php"><?php echo $button_strings['Logout']; ?></a></li>
				  </ul>
				</li>
			</ul>
			<?php } ?>
        </div>
        <!--/.nav-collapse -->
    </div>
</nav>