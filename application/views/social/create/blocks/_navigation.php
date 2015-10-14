
<div class="row">
    <div class="col-xs-12">
        <ul class="nav nav-tabs settings_tab">
            <li class="setting_item <?php echo $active_method == 'update' ? 'active' : ''; ?> auto">
                <a class="setting_link" href="<?php echo site_url('social/create/update');?>">
                    <i class="ti-export"></i>
                    Post updates
                </a>
            </li>
            <li class="setting_item <?php echo $active_method == 'post_rss' ? 'active' : ''; ?> auto">
                <a class="setting_link" href="<?php echo site_url('social/create/post_rss');?>">
                    <i class="ti-rss-alt"></i>
                    Post RSS
                </a>
            </li>
        </ul>
    </div>
</div>