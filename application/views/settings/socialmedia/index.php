<?php
/**
 * @var array $type_tokens
 * @var array $timezones
 * @var string $currentTimezone
 * @var Social_group[] $groups
 * @var bool $has_twitter_marketing_tools
 */
?>
<div class="p-rl30 p-tb20">
    <div class="row">
        <div class="col-xs-12">
            <h1 class="head_title"><?= lang('settings_social_media') ?></h1>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <ul class="breadcrumbs">
                <li class="breadcrumbs_item">
                    <a href="<?= site_url('settings');?>" class="breadcrumbs_link"><?= lang('settings') ?></a>
                </li>
                <li class="breadcrumbs_item active">
                    <a href="" class="breadcrumbs_link"><?= lang('settings_social_media') ?></a>
                </li>
            </ul>
        </div>
    </div>
</div>
<div class="main_block">
    <div class="row">
        <div class="col-xs-12">
            <?php echo $this->template->block('_info_block', 'blocks/settings/info_block'); ?>
            <div class="row account_block">
                <?php foreach($type_tokens as $type => $tokens) : ?>
                    <div class="col-sm-6 col-lg-4 m-b20 account_item">
                        <div class="row">
                            <div class="col-sm-6">
                                <p class="text_color large-size"><?= lang('social_gateway', [ucfirst($type)]) ?></p>
                                <?php if(!count($tokens)): ?>
                                    <p class="black smallText"><?= lang('setup_your_social_account', [ucfirst($type)]) ?></p>
                                <?php endif; ?>
                            </div>
                            <?php if(!count($tokens)): ?>
                            <div class="col-sm-6 sm-right">
                                <a
                                    href="<?php echo site_url('settings/socialmedia/'.$type);?>"
                                    class="btn btn-<?= $type ?>"
                                    >
                                    <?= lang('add_your_account') ?>
                                </a>
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php if($type == 'twitter' && !count($tokens)): ?>
                            <div class="row">
                                <div class="col-xs-12">
                                    <a class="link twitter-btn enter-pin" data-toggle="modal" data-target="#EnterCode" href="#EnterCode">
                                        <?= lang('enter_pin_code') ?>
                                    </a>
                                </div>
                            </div>
                        <?php endif; ?>
                        <?php foreach($tokens as $token) : ?>
                            <div class="row">
                                <div class="col-xs-12 m-t20">
                                    <div class="user_account account_<?= $type ?> dTable">
                                        <div class="dRow">
                                            <div class="dCell cellImg">
                                                <img class="user_account_image" src="<?= $token['image'] ?>" alt="">
                                            </div>
                                            <div class="dCell">
                                                <h3 class="user_account_name pull-md-left"><?= $token['name'] ?></h3>
                                                <div class="pull-md-right m-t10">
                                                    <?php if($type == 'twitter' && $has_twitter_marketing_tools): ?>
                                                        <a href="<?= site_url('settings/socialmedia/account_analytics/'.$token['id']);?>"
                                                           class="link ">
                                                            <i class="fa fa-bar-chart-o"></i> <?= lang('social_account_analytics') ?>
                                                        </a>
                                                    <?php endif; ?>
                                                    <?php if($token['has_configs']) : ?>
                                                        <a href="<?= site_url('settings/socialmedia/edit_account/'.$token['id']);?>"
                                                           class="link m-l10">
                                                            <i class="fa fa-pencil-square-o"></i> <?= lang('edit') ?>
                                                        </a>
                                                    <?php endif; ?>
                                                    <a href="<?= site_url('settings/socialmedia/social_logout/'.$token['id']);?>"
                                                       class="remove_link m-l10">
                                                        <i class="fa fa-remove"></i> <?= lang('remove') ?>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
<?php echo $this->template->block('twitter_pin_modal', 'blocks/modal/settings/socialmedia/twitter'); ?>
