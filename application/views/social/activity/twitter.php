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
        <div class="tab-pane active"  id="twitter">
            <div class="row" style="display: none;">
                <div class="col-xs-12">
                    <ul class="nav nav-tabs settings_tab">
                        <?php foreach($access_tokens as $access_token) : ?>
                            <li class="setting_item <?= ($token['id'] == $access_token['id']) ? 'active' : '' ?> auto token_item"
                                data-token-id="<?= $access_token['id'] ?>">
                                <a class="setting_link" href="<?php echo site_url('social/activity/twitter?token_id='.$access_token['id']); ?>">
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
                            <ul class="nav nav-tabs settings_tab">
                                <li class="setting_item active auto">
                                    <a class="setting_link" href="<?php echo site_url('social/activity/load_tweets'); ?>" id="twitter-feed" data-toggle="tab">
                                        <i class="ti-folder"></i>
                                        <?= lang('main_feed') ?>
                                    </a>
                                </li>
                                <li class="setting_item auto">
                                    <a class="setting_link" href="<?php echo site_url('social/activity/load_tweets'); ?>" id="mentions" data-toggle="tab">
                                        <i class="ti-book"></i>
                                        <?= lang('mentions') ?>
                                    </a>
                                </li>
                                <li class="setting_item auto">
                                    <a class="setting_link" href="<?php echo site_url('social/activity/load_tweets'); ?>" id="my-feed" data-toggle="tab">
                                        <i class="ti-new-window"></i>
                                        <?= lang('sent_tweets') ?>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="tab-content settings_content">
                        <div class="tab-pane active">
                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="web_radar m-t20 pull_border" id="ajax-area">

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <ul class="pagination pull-right">
                                <li class="pagination_item">
                                    <a href="<?php echo site_url('social/activity/load_tweets'); ?>" class="prev pagination_link" data-url="<?php echo isset($paging['previous']) ? $paging['previous'] : ''; ?>">
                                        <?= lang('previous') ?>
                                    </a>
                                </li>
                                <li class="pagination_item active">
                                    <a class="pagination_link" id="pages-counter">1</a>
                                </li>
                                <li class="pagination_item">
                                    <a href="<?php echo site_url('social/activity/load_tweets'); ?>" class="next pagination_link"  data-url="<?php echo isset($paging['next']) ? $paging['next'] : ''; ?>">
                                        <?= lang('next') ?>
                                    </a>
                                </li>
                                <input type="hidden" id="page_number" value="1">
                            </ul>
                        </div>
                    </div>
<!--                </div>-->
<!--            </div>-->
        </div>
    </div>
</div>

<!-- Modal -->
<div id="reply-window" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body">
                <h4 class="head_tab"><?= lang('enter_reply_text') ?></h4>
                <textarea class="form-control" rows="5" cols="10" class="twitter_reply_textarea"></textarea>
            </div>
            <div class="modal-footer clearfix">
                <div class="pull-right">
                    <a class="link m-r10" data-dismiss="modal" aria-hidden="true" href=""><?= lang('cancel') ?></a>
                    <button type="button" id="reply" class="btn btn-save"><?= lang('send') ?></button>
                </div>
            </div>
        </div>
    </div>
</div>
