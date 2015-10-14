<?php
/**
 * @var array $subscriptions
 * @var Subscription $active_subscription
 */
?>
<div class="p-rl30 p-tb20">
    <div class="row">
        <div class="col-xs-12">
            <h1 class="head_title"><?= lang('settings_subscriptions') ?></h1>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <ul class="breadcrumbs">
                <li class="breadcrumbs_item">
                    <a href="" class="breadcrumbs_link"><?= lang('settings') ?></a>
                </li>
                <li class="breadcrumbs_item active">
                    <?= lang('settings_subscriptions') ?>
                </li>
            </ul>
        </div>
    </div>
</div>
<div class="main_block">
    <div class="row">
        <div class="col-xs-12">
            <?php echo $this->template->block('_info_block', 'blocks/settings/info_block'); ?>
            <div class="well">
                <?php
                $plan = $active_subscription->plan->get();
                $transaction = $active_subscription->payment_transaction->get();
                ?>
                <b><?= lang('active_subscription') ?>:</b> <?php echo $plan->name; ?><br/>
                <b><?= lang('from') ?>:</b> <?php echo $active_subscription->start_date; ?>
                <b><?= lang('to') ?>:</b> <?php echo $active_subscription->end_date; ?><br/>
                <b><?= lang('cost') ?>:</b> <?php echo $transaction->getFormatedAmount() . ' ' . strtoupper($transaction->currency); ?> <br/>
                <?php if($active_subscription->is_stripe_active) : ?>
                <button class="btn btn-remove" id="cancel_subscription"
                   data-href="<?php echo site_url('settings/subscriptions/cancel_subscription'); ?>">
                    <?= lang('cancel') ?>
                </button>
                <?php endif; ?>
            </div>
            <?php if(!empty($subscriptions)): ?>
            <h2><?= lang('history') ?></h2>
            <table class="responsive-table m-t20">
                <thead class="table_head">
                <tr>
                    <th><?= lang('subscription') ?></th>
                    <th><?= lang('start') ?></th>
                    <th><?= lang('end') ?></th>
                    <th><?= lang('cost') ?></th>
                    <th><?= lang('currency') ?></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach($subscriptions as $subscription): ?>
                    <?php
                    $end = DateTime::createFromFormat('Y-m-d', $subscription->end_date);
                    $start = DateTime::createFromFormat('Y-m-d', $subscription->start_date);
                    $now = new DateTime();
                    $status = ($end >= $now) && ($subscription->status == Subscription::STATUS_ACTIVE) ? 'success'  : 'error';
                    $plan = $subscription->plan->get();
                    $transaction = $subscription->payment_transaction->get();
                    ?>
                    <tr id="<?php echo $subscription->id;?>" class="row-<?php echo $status;?>">
                        <td data-th="<?= lang('subscription') ?>"><?php echo $plan->name; ?></td>
                        <td data-th="<?= lang('start') ?>"><?php echo $subscription->start_date; ?></td>
                        <td data-th="<?= lang('end') ?>"><?php echo $subscription->end_date; ?></td>
                        <td data-th="<?= lang('cost') ?>"><?php echo $transaction->getFormatedAmount(); ?></td>
                        <td data-th="<?= lang('currency') ?>"><?php echo strtoupper($transaction->currency); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>
    </div>
</div>