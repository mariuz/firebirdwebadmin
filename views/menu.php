<?php
	require_once('./inc/script_start.inc.php');
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
					<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><?php echo $s_login['user']; ?> <span class="caret"></span></a>
					<ul class="dropdown-menu">
					
					<?php if (!empty($s_login['role'])) { ?>
				    <li class="dropdown-header"><?php echo $db_strings['Role']; ?>:</li>
					<li><a href="#"><?php echo $s_login['role']; ?></a></li>
					<?php } ?>
				    
					<li class="dropdown-header"><?php echo $db_strings['Host']; ?>:</li>
					<li><a href="#"><?php echo $s_login['host']; ?></a></li>

					<li class="dropdown-header"><?php echo $db_strings['Database']; ?>:</li>
					<li><a href="#"><?php echo $s_login['database']; ?></a></li>
					
					<li role="separator" class="divider"></li>
					<li><a href="logout.php"><?= $button_strings['Logout']; ?></a></li>
				  </ul>
				</li>
			</ul>
			<?php } ?>
        </div>
        <!--/.nav-collapse -->
    </div>
</nav>