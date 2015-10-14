<?php
/**
 * @var Access_token $token
 */
?>
<div class="p-rl30 p-tb20">
    <div class="row">
        <div class="col-xs-12">
            <h1 class="head_title"><?= lang('settings_social_media_analytics', [ucfirst($token->type)]); ?></h1>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <ul class="breadcrumbs">
                <li class="breadcrumbs_item">
                    <a href="<?php echo site_url('settings');?>" class="breadcrumbs_link"><?= lang('settings') ?></a>
                </li>
                <li class="breadcrumbs_item">
                    <a href="<?php echo site_url('settings/socialmedia');?>" class="breadcrumbs_link"><?= lang('settings_social_media') ?></a>
                </li>
                <li class="breadcrumbs_item active">
                    <a href="" class="breadcrumbs_link"><?= lang('settings_social_media_analytics', [ucfirst($token->type)]); ?></a>
                </li>
            </ul>
        </div>
    </div>
</div>
<div class="main_block">
    <div class="row">
        <div class="col-xs-12">
            <h4 class="edit-profile-title"><i class="fa fa-<?= $token->type ?>"></i> <?= $token->name ?></h4>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <input id="access_token_id" type="hidden" value="<?= $token->id ?>"/>
            <select name="period" id="analytics_period" class="chosen-select">
                <option value="1 months"><?= lang('month') ?></option>
                <option value="3 months"><?= lang('three_months') ?></option>
                <option value="1 years"><?= lang('year') ?></option>
            </select>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <div class="block_content m-b20">
                <div class="row">
                    <div class="col-xs-12 clearfix">
                        <h2 class="block_content_title pull-sm-left w-100">
                            <?= lang('analytics_followers') ?>
                        </h2>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12">
                        <div id="chartbox-followers">

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <div class="block_content m-b20">
                <div class="row">
                    <div class="col-xs-12 clearfix">
                        <h2 class="block_content_title pull-sm-left w-100">
                            <?= lang('analytics_favourites') ?>
                        </h2>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12">
                        <div id="chartbox-favourites_count">

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <div class="block_content m-b20">
                <div class="row">
                    <div class="col-xs-12 clearfix">
                        <h2 class="block_content_title pull-sm-left w-100">
                            <?= lang('analytics_retweets') ?>
                        </h2>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12">
                        <div id="chartbox-retweets_count">

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <div class="block_content m-b20">
                <div class="row">
                    <div class="col-xs-12 clearfix">
                        <h2 class="block_content_title pull-sm-left w-100">
                            <?= lang('analytics_new_following_by_search') ?>
                        </h2>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12">
                        <div id="chartbox-new_following_by_search_count">

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <div class="block_content m-b20">
                <div class="row">
                    <div class="col-xs-12 clearfix">
                        <h2 class="block_content_title pull-sm-left w-100">
                            <?= lang('analytics_new_following') ?>
                        </h2>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12">
                        <div id="chartbox-new_following_count">

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <div class="block_content m-b20">
                <div class="row">
                    <div class="col-xs-12 clearfix">
                        <h2 class="block_content_title pull-sm-left w-100">
                            <?= lang('analytics_new_unfollowers') ?>
                        </h2>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12">
                        <div id="chartbox-new_unfollowers_count">

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <div class="block_content m-b20">
                <div class="row">
                    <div class="col-xs-12 clearfix">
                        <h2 class="block_content_title pull-sm-left w-100">
                            <?= lang('analytics_new_unfollowing') ?>
                        </h2>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12">
                        <div id="chartbox-new_unfollowing_count">

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>