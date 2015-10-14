<div class="p-rl30 p-tb20">
    <div class="row">
        <div class="col-xs-12">
            <h1 class="head_title"><?= lang('api_keys') ?></h1>
        </div>
    </div>
</div>
<div class="main_block">
    <div class="row">
        <div class="col-xs-12 m-t10">
            <form action="<?php echo site_url('admin/admin_api'); ?>" method="POST">
                <table class="responsive-table">
                    <thead class="table_head">
                    <tr>
                        <th><?= lang('api') ?></th>
                        <th><?= lang('name') ?></th>
                        <th><?= lang('value') ?></th>
                    </tr>
                    </thead>
                    <?php foreach($api_keys as $api_key): ?>
                        <tr>
                            <td data-th="<?= lang('api') ?>"><?php echo ucfirst($api_key->social); ?></td>
                            <td data-th="<?= lang('name') ?>"><?php echo $api_key->name; ?></td>
                            <td data-th="<?= lang('value') ?>">
                                <input type="text" class="form-control"
                                       name="<?php echo $api_key->social; ?>/<?php echo $api_key->key; ?>"
                                       value="<?php echo $api_key->value; ?>"
                                    />
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
                <input type="submit" class="btn btn-save" value="<?= lang('save') ?>" />
            </form>
        </div>
    </div>
</div>