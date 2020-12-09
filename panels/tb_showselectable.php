<?php
// File           tb_show.php / FirebirdWebAdmin
// Purpose        html sequence for the tb_show-panel in tables.php
// Author         Lutz Brueckner <irie@gmx.de>
// Copyright      (c) 2000-2006 by Lutz Brueckner,
//                published under the terms of the GNU General Public Licence v.2,
//                see file LICENCE for details


$tcnt = 0;
if ($s_connected == true && is_array($s_tables)):
   
    $title = $ptitle_strings['tb_selector'];
    $url = url_session($_SERVER['PHP_SELF'] . "?default");

?>
   
   <a href="<?=$url?>" class="dtitlex" title="${ptitle_strings['Close']}"><span class="glyphicon glyphicon-chevron-up" aria-hidden="true" alt="${ptitle_strings['Close']}" title="${ptitle_strings['Close']}" ></span> <?=$title?></a>

   <div class="container">

   <?php require 'panels/tb_droptables.php'; ?>

   <?php if ( !isset($message) || strlen($message)<=0 ) { ?>   

   <form method="post" action="" name="tb_droptables_form">

   <input type="hidden" name="drop_tables" value="drop_tables">
   <div class="panel panel-default">
      <div class="panel-heading"><?=$tb_strings['FormTableSelector']?></div>
      <div class="panel-body">
      
      <?php if (!isset($_POST['drop_tables'])){ ?>
            <h4><?=$tb_strings['TablesActionsTitle']?></h4>
          <input type="hidden" name="btn_viewselectable" />
          <p><?=$tb_strings['WarningManyTables']?></p>
          <p><input type="submit" class="btn btn-danger" name="tb_droptables" value="<?=$button_strings['DropSelectedTables']?>"></row></p>
      <?php } ?>
    <div class="row">
        <div class="col-sm">
    <table class="table table-bordered table-hover">
    <thead>
        
    <tr>
            <th></th>
            <th><?=$tb_strings['TbName']?></th>
            <th><?=$tb_strings['Records']?></th>
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

    <?php } ?>

</div>


    <?php 
endif;
