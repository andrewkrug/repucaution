<?php
/**
 * @var array $access_tokens
 * @var array $token
 */
?>
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
            <div class="row" style="display: none;">
                <div class="col-xs-12">
                    <ul class="nav nav-tabs settings_tab">
                        <?php foreach($access_tokens as $access_token) : ?>
                            <li class="setting_item <?= ($token['id'] == $access_token['id']) ? 'active' : '' ?> auto token_item"
                                data-token-id="<?= $access_token['id'] ?>">
                                <a class="setting_link" href="<?php echo site_url('social/activity/facebook?token_id='.$access_token['id']); ?>">
                                    <i class="ti-folder"></i>
                                    <?= $access_token['name'] ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
<!--            <div class="tab-pane active">-->
<!--                <div class="tab-content">-->
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="web_radar m-t20 pull_border" id="ajax-area">

                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <ul class="pagination pull-right">
                                <li class="pagination_item unactive">
                                    <a href="<?php echo site_url('social/activity/load_facebook_feed'); ?>" class="prev pagination_link" data-url="<?php echo isset($paging['previous']) ? $paging['previous'] : ''; ?>">
                                        <?= lang('previous') ?>
                                    </a>
                                </li>
                                <li class="pagination_item active">
                                    <a href="javascript: void(0)" class="pagination_link" id="pages-counter">1</a>
                                </li>
                                <li class="pagination_item">
                                    <a href="<?php echo site_url('social/activity/load_facebook_feed'); ?>" class="next pagination_link"  data-url="<?php echo isset($paging['next']) ? $paging['next'] : ''; ?>">
                                        <?= lang('next') ?>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
<!--                </div>-->
<!--            </div>-->
        </div>
    </div>
</div>
