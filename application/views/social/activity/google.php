<?php
/**
 * @var array $access_tokens
 * @var Socializer_Google $socializer
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
            <div class="row" style="display: none">
                <div class="col-xs-12">
                    <ul class="nav nav-tabs settings_tab">
                        <?php foreach($access_tokens as $access_token) : ?>
                            <li class="setting_item <?= ($token['id'] == $access_token['id']) ? 'active' : '' ?> auto token_item"
                                data-token-id="<?= $access_token['id'] ?>">
                                <a class="setting_link" href="<?php echo site_url('social/activity/google?token_id='.$access_token['id']); ?>" data-toggle="tab">
                                    <i class="ti-folder"></i>
                                    <?= $access_token['name'] ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
            <div id="ajax-area">

            </div>
            <div class="row">
                <div class="col-xs-12">
                    <ul class="pagination pull-right pginationBlock">
                        <li class="pagination_item">
                            <a href="<?php echo site_url('social/activity/google?token_id='.$token['id']); ?>"
                               class="prev pagination_link"
                               data-url="first"
                                >
                                <?= lang('first') ?>
                            </a>
                        </li>
                        <li class="pagination_item unactive">
                            <a href="<?php echo site_url('social/activity/google'); ?>?token_id=<?= $token['id'] ?>&page=<?php echo isset($nextPageToken) ? urlencode($nextPageToken) : ''; ?>"
                               class="next pagination_link"
                               data-url="<?php echo isset($paging['next']) ? $paging['next'] : ''; ?>"
                                ><?= lang('next') ?></a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>