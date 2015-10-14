<div class="row">
    <div class="col-xs-12">
        <ul class="nav nav-tabs settings_tab">
            <li class="setting_item <?php if($section == 'personal'):?>active<?php endif;?>">
                <a class="setting_link" href="<?php echo site_url('settings/personal');?>">
                    <i class="ti-user"></i>
                    Personal Settings
                </a>
            </li>
            <li class="setting_item <?php if($section == 'directories'):?>active<?php endif;?>">
                <a class="setting_link" href="<?php echo site_url('settings/directories');?>">
                    <i class="ti-settings"></i>
                    Directory Settings
                </a>
            </li>
            <li class="setting_item <?php if($section == 'keywords'):?>active<?php endif;?>">
                <a class="setting_link" href="<?php echo site_url('settings/keywords');?>">
                    <i class="ti-google"></i>
                    Google Places Keywords
                </a>
            </li>
            <li class="setting_item <?php if($section == 'socialmedia'):?>active<?php endif;?>">
                <a class="setting_link" href="<?php echo site_url('settings/socialmedia');?>">
                    <i class="ti-instagram"></i>
                    Social Media
                </a>
            </li>
            <li class="setting_item <?php if($section == 'mention_keywords'):?>active<?php endif;?>">
                <a class="setting_link" href="<?php echo site_url('settings/mention_keywords');?>">
                    <i class="ti-layers-alt"></i>
                    Social Keywords
                </a>
            </li>
            <li class="setting_item <?php if($section == 'analytics'):?>active<?php endif;?>">
                <a class="setting_link" href="<?php echo site_url('settings/analytics');?>">
                    <i class="ti-pulse"></i>
                    Analytics
                </a>
            </li>
            <li class="setting_item <?php if($section == 'rss'):?>active<?php endif;?>">
                <a class="setting_link" href="<?php echo site_url('settings/rss');?>">
                    <i class="ti-rss-alt"></i>
                    Rss
                </a>
            </li>
            <li class="setting_item <?php if($section == 'collaboration'):?>active<?php endif;?>">
                <a class="setting_link" href="<?php echo site_url('settings/collaboration');?>">
                    <i class="ti-id-badge"></i>
                    Collaboration Team
                </a>
            </li>
        </ul>
    </div>
</div>