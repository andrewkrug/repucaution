<div class="p-rl30 p-tb20">
    <div class="row">
        <div class="col-xs-12">
            <h1 class="head_title"><?= lang('google_rank') ?></h1>
        </div>
    </div>

</div>
<div class="main_block">
    <div class="row">
        <div class="col-xs-12">
            <p class="large-size text_color">
                <?php if (isset($configured['no_address'])): ?>
                    <?= lang('company_address_not_set') ?>
                <br/><br/>
                <a href="<?php echo site_url('settings/keywords'); ?>">
                    <?= lang('go_to_keywords_settings') ?>
                </a>
                <?php elseif(isset($configured['no_keywords'])): ?>
                    <?= lang('no_keywords') ?>
                <br/><br/>
                <a href="<?php echo site_url('settings/keywords'); ?>">
                    <?= lang('go_to_keywords_settings') ?>
                </a>
                <?php elseif(isset($configured['no_results'])): ?>
                    <?= lang('no_results') ?>
                <?php endif; ?>
            </p>

        </div>
    </div>
</div>