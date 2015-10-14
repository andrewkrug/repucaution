
<div class="p-rl30 p-tb20">
    <div class="row">
        <div class="col-xs-12">
            <a href="<?php echo site_url('admin/payment_settings'); ?>">< <?= lang('back') ?></a>

            <h1 class="head_title"><?= lang('payment_transactions') ?>: <?php echo $gateway->name;?></h1>
        </div>
    </div>
</div>
<div class="main_block">
    <div class="row">
        <div class="col-xs-4">
            <form id="transactions-filter" action="<?php echo site_url('admin/transactions');?>" method="POST">
                <select class="chosen-select" name="filter">
                    <option value=""><?= lang('all') ?></option>
                    <option value="0"><?= lang('live_mode') ?></option>
                    <option value="1"><?= lang('test_mode') ?></option>
                </select>
            </form>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <?php if ($transactions->exists()) :?>
                <table id="transactions" class="responsive-table">
                    <thead class="table_head">
                    <tr>
                        <th><?= lang('user') ?></th>
                        <th><?= lang('amount') ?></th>
                        <th><?= lang('currency') ?></th>
                        <th><?= lang('description') ?></th>
                        <th><?= lang('test_mode') ?></th>
                        <th><?= lang('date') ?></th>
                    </tr>
                    </thead>

                    <tbody>
                    <?php foreach($transactions as $transaction) :?>
                        <tr>
                            <td data-th="<?= lang('user') ?>">
                                <?php
                                $userInfo = explode(' ', $transaction->user_info);
                                echo (!empty($transaction->user_username)) ? $transaction->user_username : $userInfo[2].' '.$userInfo[3];
                                ?>
                            </td>
                            <td data-th="<?= lang('amount') ?>"><?php echo $transaction->getFormatedAmount();?></td>
                            <td data-th="<?= lang('currency') ?>"><?php echo strtoupper($transaction->currency);?></td>
                            <td data-th="<?= lang('description') ?>"><?php echo $transaction->description;?></td>
                            <td data-th="<?= lang('test_mode') ?>"><?php echo ($transaction->test_mode) ? lang('yes') : lang('no');?></td>
                            <td data-th="<?= lang('date') ?>"><?php echo date('Y-m-d H:i:s', $transaction->updated);?></td>
                        </tr>
                    <?php endforeach;?>
                    </tbody>

                </table>
                <?php echo $this->template->block('pagination', 'admin/transactions/blocks/pagination', array(
                    'page' => $page,
                    'filter' =>$filter
                ));
                ?>
            <?php else :?>
                <p class="large-size m-t20 p-b10 b-Bottom text_color">
                    <?= lang('no_transactions') ?>
                </p>
            <?php endif;?>
        </div>
    </div>
</div>