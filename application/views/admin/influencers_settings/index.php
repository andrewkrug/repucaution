<div class="p-rl30 p-tb20">
    <div class="row">
        <div class="col-xs-12">
            <a href="<?php echo site_url('admin/payment_settings'); ?>">< <?= lang('back') ?></a>

            <h1 class="head_title"><?= lang('influencers_settings') ?>: <?php echo $gateway->name;?></h1>
        </div>
    </div>
</div>
<div class="main_block">
    <div class="row">
        <div class="col-xs-12">
            <table class="responsive-table">
                <thead class="table_head">
                <tr>
                    <th><?= lang('name') ?></th>
                    <th><?= lang('value') ?></th>
                    <th><?= lang('actions') ?></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($influencers_conditions as $condition): ?>
                    <tr>
                        <td data-th="<?= lang('name') ?>"><?php echo $condition->option_name; ?></td>
                        <td data-th="<?= lang('value') ?>"><?php echo $condition->value; ?></td>
                        <td data-th="<?= lang('actions') ?>"><a href="<?php echo site_url('admin/influencers_settings/edit/'.$condition->id); ?>"><?= lang('edit') ?></a></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
