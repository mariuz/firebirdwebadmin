<?php
/**
 * Created by PhpStorm.
 * User: gilcier
 * Date: 14/05/15
 * Time: 08:20
 */ ?>
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
        </div>
        <!--/.nav-collapse -->
    </div>
</nav>