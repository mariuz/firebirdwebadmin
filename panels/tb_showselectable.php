<?php
// File           tb_show.php / FirebirdWebAdmin
// Purpose        html sequence for the tb_show-panel in tables.php
// Author         Lutz Brueckner <irie@gmx.de>
// Copyright      (c) 2000-2006 by Lutz Brueckner,
//                published under the terms of the GNU General Public Licence v.2,
//                see file LICENCE for details


$tcnt = 0;
if ($s_connected == true && is_array($s_tables)):
   
    $title = 'Tables selector';
    $url = url_session($_SERVER['PHP_SELF'] . "?default");

?>
   
   <a href="<?=$url?>" class="dtitlex" title="${ptitle_strings['Close']}"><span class="glyphicon glyphicon-chevron-up" aria-hidden="true" alt="${ptitle_strings['Close']}" title="${ptitle_strings['Close']}" ></span> <?=$title?></a>

   <div class="container">

   <?php require 'panels/tb_droptables.php'; ?>

   <form method="post" action="" name="tb_droptables_form">

   <input type="hidden" name="drop_tables" value="drop_tables">
   <div class="panel panel-default">
      <div class="panel-heading">Panel with selectable table list</div>
      <div class="panel-body">
      
      <?php if (!isset($_POST['drop_tables'])){ ?>
            <h4>Functions selector</h4>
          <input type="hidden" name="btn_viewselectable" />
          <p>The selected data and tables will be deleted. Make a backup before running this feature.</p>
          <p><input type="submit" class="btn btn-danger" name="tb_droptables" value="Drop selected tables"></row></p>
      <?php } ?>
    <div class="row">
        <div class="col-sm">
    <table class="table table-bordered table-hover">
    <thead>
        
    <tr>
            <th></th>
            <th>Table name</th>
            <th>Records</th>
        </tr>
</thead>
<tbody>
    <?php
    foreach ($s_tables as $tablename => $properties) {
        if ($properties['is_view'] == true) {
            continue;
        }
        ++$tcnt;

        $title = $tablename;
        if ($s_tables_counts == true  &&  isset($properties['count'])) {
            $title .= '&nbsp;['.$properties['count'].']';
        }

        $fold_url = fold_detail_url('table', $properties['status'], $tablename, $title);
        $comment_url = "javascript:requestCommentArea('table', '".$tablename."');";

        ?>        
        <tr>
        <td><?php echo get_checkbox('tb_selected_tables[]', "${tablename}", ''); ?></td>
        <td><?=$tablename?></td>
        <td></td>
        </tr>
        <?php 

    }    // foreach $s_tables
    ?>
    </tbody>
    </table>
    </div>
    </div>
    </div>
    </div>
    </form>
</div>


    <?php 
endif;
