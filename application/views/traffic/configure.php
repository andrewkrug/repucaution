<div class="p-rl30 p-tb20">
    <div class="row">
        <div class="col-xs-12">
            <h1 class="head_title"><?= lang('analytics') ?></h1>
        </div>
    </div>
</div>
<div class="main_block">
    <div class="row">
        <div class="col-xs-12">
            <p class="large-size text_color">
                <?php if ( ! $access_token->token2): ?>
                    <?= lang('analytics_not_connected') ?>
                <?php elseif( ! $access_token->instance_id): ?>
                    <?= lang('analytics_not_selected') ?>
                <?php endif; ?>
                <br/><br/>
                <a href="<?php echo site_url('settings/analytics'); ?>">
                    <?= lang('go_to_google_analytics_settings') ?>
                </a>
            </p>

        </div>
    </div>
</div>