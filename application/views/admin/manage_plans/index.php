
<div class="p-rl30 p-tb20">
    <div class="row">
        <div class="col-xs-12">
            <h1 class="head_title"><?lang('plans_management') ?></h1>
        </div>
    </div>
</div>
<div class="main_block">
    <div class="row">
        <div class="col-xs-12">
            <a href="<?php echo site_url('admin/manage_plans/add');?>" class="link"><?= lang('add_plan') ?></a>
        </div>
    </div>
    <?php if( !$plans->count()): ?>
        <div class="row">
            <div class="col-xs-12">
                <p class="large-size m-t20 p-b10 b-Bottom text_color">
                    <?= lang('no_plans') ?>
                </p>
            </div>
        </div>

    <?php else: ?>
    <div class="row">
        <div class="col-xs-12 m-t10">
            <table id="sortable-table" class="responsive-table manage-plans">
                <thead class="table_head">
                <tr>
                    <th><?= lang('name') ?></th>
                    <th><?= lang('features') ?></th>
                    <th><?= lang('period_price') ?></th>
                    <th><?= lang('actions') ?></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($plans as $plan) :?>
                    <tr id="<?php echo $plan->id;?>"class="plan">
                        <td data-th="<?= lang('name') ?>">
                            <?php echo $plan->name;?>
                        </td>
                        <td data-th="<?= lang('features') ?>">
                            <?php $features = $plan->getAttachedFeatures();
                            foreach ($features as $feature) {
                                echo lang($feature->getFeature()->slug).'<br/>';
                            }
                            ?>
                        </td>
                        <td data-th="<?= lang('period_price') ?>">
                            <?php $periods = $plan->plans_period->get();
                            foreach ($periods as $period) {
                                echo $period->period.' '.$options[$period->qualifier].' / '.$period->viewPrice().'<br/>';
                            }
                            ?>
                        </td>
                        <td data-th="<?= lang('actions') ?>">
                            <?php if ($plan->special):?>
                                <a class="invite-action" data-id="<?php echo $plan->id;?>" href="<?php echo site_url('admin/manage_plans/specialinvite');?>"><?= lang('invite') ?></a>
                            <?php endif; ?>
                            <a href="<?php echo site_url('admin/manage_plans/edit?plan='.$plan->id);?>"><?= lang('edit') ?></a>
                            <a href="<?php echo site_url('admin/manage_plans/delete?plan='.$plan->id);?>"><?= lang('delete') ?></a>
                        </td>
                    </tr>
                <?php endforeach;?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif;?>
</div>
<div id="invite-block" class="modal fade" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <h4 id="myModalLabel" class="head_tab"><?= lang('enter_email') ?></h4>
                <div class="form-group col-xs-12">
                    <input type="text" name="email" class="form-control invite-email">
                    <input type="hidden" name="plan_id" class="invite-plan" value="">
                </div>
            </div>
            <div class="modal-footer clearfix">
                <div class="pull-right">
                    <a class="link m-r10" data-dismiss="modal" aria-hidden="true" href=""><?= lang('cancel') ?></a>
                    <button type="button" id="invite-btn" class="btn btn-save"><?= lang('invite') ?></button>
                </div>
            </div>
        </div>
    </div>
</div>