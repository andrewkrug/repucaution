<div class="p-rl30 p-tb20">
    <div class="row">
        <div class="col-xs-12">
            <h1 class="head_title"><?= lang('social_activity') ?></h1>
        </div>
    </div>
</div>
<div class="main_block">
    <?php echo $this->template->block('tabs', 'social/activity/blocks/tabs'); ?>
    <div class="tab-content settings_content">
        <div class="tab-pane active">
            <div class="row">
                <div class="col-xs-12">
                    <div class="web_radar m-t20 pull_border" id="ajax-area">
                        <?php if(isset($linkedin_html)):?>
                            <?php echo $linkedin_html; ?>
                        <?php endif;?>
                    </div>
                </div>
            </div>
            <?php if(!empty($linkedin_html)):?>
            <div class="row">
                <div class="col-xs-12">
                    <ul class="pagination pull-right">
                        <li class="pagination_item active">
                            <a href="<?php echo site_url('social/activity/load_updates'); ?>" class="prev pagination_link" data-url="<?php echo $paging['previous']; ?>">
                                <?= lang('previous') ?>
                            </a>
                        </li>
                        <li class="pagination_item active">
                            <a class="pagination_link" id="pages-counter">1</a>
                        </li>
                        <li class="pagination_item active">
                            <a href="<?php echo site_url('social/activity/load_updates'); ?>" class="next pagination_link"  data-url="<?php echo $paging['next']; ?>">
                                <?= lang('next') ?>
                            </a>
                        </li>
                        <input type="hidden" id="page_number" value="1">
                    </ul>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

