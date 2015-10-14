<?php
/**
 * @var array $available_configs
 */
?>


<div class="p-rl30 p-tb20">
    <div class="row">
        <div class="col-xs-12">
            <h1 class="head_title"><?= lang('social_settings') ?></h1>
        </div>
    </div>
</div>
<div class="main_block">
    <div class="row">
        <div class="col-xs-8">
            <form class="custom-form" action="<?php echo site_url('admin/social_settings');?>" method="POST">
                <?php foreach ($available_configs as $type => $configs) : ?>
                    <p class="text_color strong-size m-t20"><?= ucfirst($type) ?></p>
                    <ul>
                        <?php foreach($configs as $config) : ?>
                            <li class="list-unstyled">
                                <label class="cb-checkbox">
                                    <input
                                        type="checkbox"
                                        name="<?= $type ?>[<?= $config['id']; ?>]"
                                        <?= ($config['is_enable']) ? 'checked="checked"' : ''; ?>
                                    />
                                    <?= (lang($config['key'])) ? lang($config['key']) : $config['name']; ?>
                                </label>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endforeach; ?>
                <div class="form-group">
                    <input type="submit" class="btn btn-save" value="<?= lang('save') ?>" />
                </div>
            </form>
        </div>
    </div>
</div>