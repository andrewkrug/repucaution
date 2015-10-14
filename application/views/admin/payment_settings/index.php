<div class="p-rl30 p-tb20">
    <div class="row">
        <div class="col-xs-12">
            <h1 class="head_title"><?= lang('payment_settings') ?></h1>
        </div>
    </div>
</div>
<div class="main_block">
    <div class="row">
        <div class="col-xs-12">
            <form action="" method="post">
                <table class="responsive-table">
                    <thead class="table_head">
                    <tr>
                        <th><?= lang('option') ?></th>
                        <th><?= lang('action') ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach($paymentData as $key=>$data):?>
                        <tr>
                            <td data-th="<?= lang('option') ?>"><?php echo $data['label'];?></td>
                            <td data-th="<?= lang('action') ?>">
                                <button class="btn-save btn" name="settings[<?php echo $key;?>]" value="<?php echo $data['value'];?>">
                                    <?php echo $data['text_status'];?>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach;?>
                    </tbody>
                </table>
            </form>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12 m-t10">
            <ul class="nav nav-list">
                <li class="nav-header"><?= lang('payment_gateways') ?></li>
                <?php foreach($geteways as $gateway): ?>
                    <li>
                        <a href="<?php echo site_url('admin/payment_settings/gateways/'.$gateway->slug);?>">
                            <?php echo $gateway->name;?> (<?php echo $gateway->status ? lang('enabled') : lang('disabled'); ?>)
                        </a>
                    </li>
                <?php endforeach;?>
            </ul>
        </div>
    </div>
</div>