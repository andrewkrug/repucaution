<div class="p-rl30 p-tb20">
    <div class="row">
        <div class="col-xs-12">
            <h1 class="head_title"><?= lang('admin_dashboard') ?></h1>
        </div>
    </div>
</div>
<div class="main_block">
    <div class="row">
        <div class="col-xs-12">
            <ul>
                <?php foreach($dashboard_links as $link => $title):?>
                    <li class="list-unstyled"><a href="<?php echo site_url($link); ?>"><?php echo $title;?></a></li>
                <?php endforeach;?>
            </ul>
        </div>
    </div>
</div>