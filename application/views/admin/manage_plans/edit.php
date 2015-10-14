<div class="p-rl30 p-tb20">
    <div class="row">
        <div class="col-xs-12">
            <h1 class="head_title"><?= lang('add_new_plan') ?></h1>
        </div>
    </div>
</div>
<div class="main_block">
    <form id="form-edit-plan" method="POST" action="<?php echo site_url('admin/manage_plans/edit')?>">
        <div class="row custom-form">
            <div class="col-xs-6">
                <input name="name" class="form-control" value="<?php echo (!empty($recent['name'])) ? $recent['name'] : $plan->name;?>" placeholder="<?= lang('name') ?>">
            </div>
            <div class="col-xs-12 m-t10">
                <label class="cb-checkbox"><?= lang('trial_free_plan_info') ?>
                <input name="trial" type="checkbox" <?php if(!empty($recent['trial']) || $plan->trial) echo 'checked="checked"'?>>
                </label>
            </div>
            <div class="col-xs-12 m-t10">
                <label class="cb-checkbox"><?= lang('private_plan_info') ?>
                <input name="special" type="checkbox" <?php if(!empty($recent['special']) || $plan->special) echo 'checked="checked"'?>></label>
            </div>
        </div>
        <input name="id" type="hidden" value="<?php echo $plan->id;?>">
        <input name="plan_weight" type="hidden" value="<?php echo $plan->weight;?>">
        <div class="row">
            <div class="col-xs-4">
                <h4><?= lang('features') ?>:</h4>
                <ul class="used-features connected sortable list" style="background-color:#ccc;height:400px;overflow:auto;" id="sortable1">
                    <?php if ($plansFeatures->exists()) :?>
                        <?php foreach ($plansFeatures as $plansFeature) :?>
                            <li draggable="true" class="list-unstyled" style="border:1px solid #ccc;background-color:#fff;padding:10px 20px;cursor:move" data-id="<?php echo $plansFeature->feature_id;?>">

                            <span>
                                <?php echo lang($plansFeature->feature->slug);?>
                            </span>
                            <span class="inputs-feature" style="display: none">
                                <?php if ($plansFeature->getFeature()->type != 'bool') :?>
                                    <input type="text" name="feature_<?php echo $plansFeature->feature_id;?>_value"
                                           value="<?php echo (isset($recent) && !empty($recent['feature_'.$plansFeature->feature_id.'_value'])) ?
                                               $recent['feature_'.$plansFeature->feature_id.'_value'] :
                                               $plansFeature->value;
                                           ?>"
                                        >
                                <?php endif;?>
                                <input type="hidden" name="feature_<?php echo $plansFeature->feature_id;?>_plansfeatureid" value="<?php echo $plansFeature->id;?>">
                                <input name="feature[]" type="hidden"  value="">
                            </span>
                            </li>
                        <?php endforeach;?>
                    <?php endif; ?>
                    <?php if (isset($recent)) :?>
                        <?php if ($features && $features->exists()) :?>
                            <?php foreach($features as $feature) :?>
                                <?php if (!in_array($feature->id, $recent['feature'])) continue;?>
                                <?php $bool = ($feature->type == 'bool') ? true : false;?>
                                <li draggable="true" style="border:1px solid #ccc;background-color:#fff;padding:10px 20px;cursor:move" class="list-unstyled" data-id="<?php echo $feature->id;?>">
                            <span>
                                <?php echo lang($feature->slug);?>
                            </span>
                            <span class="inputs-feature" style="display: none">
                                <?php if (!$bool) :?>
                                    <input type="text" name="feature_<?php echo $feature->id;?>_value" value="<?php echo $recent['feature_'.$feature->id.'_value'];?>">
                                <?php endif;?>
                                <input type="hidden" name="feature_<?php echo $feature->id;?>_plansfeatureid" value="">
                                <input name="feature[]" type="hidden" value="">
                            </span>
                                </li>
                            <?php endforeach; ?>
                        <?php endif;?>
                    <?php endif;?>
                </ul>
            </div>
            <div class="col-xs-4">
                <h4><?= lang('features_list') ?>:</h4>
                <ul class="unused-features connected sortable list" style="background-color:#ccc;height:400px;overflow:auto;" id="sortable2">
                    <?php if ($features && $features->exists()) :?>
                        <?php foreach($features as $feature) :?>
                            <?php if (isset($recent) && in_array($feature->id, $recent['feature'])) continue;?>
                            <?php $bool = ($feature->type == 'bool') ? true : false;?>
                            <li draggable="true" style="border:1px solid #ccc;background-color:#fff;padding:10px 20px;cursor:move" class="list-unstyled" data-id="<?php echo $feature->id;?>">
                            <span>
                                <?php echo lang($feature->slug);?>
                            </span>
                            <span class="inputs-feature" style="display: none">
                                <?php if (!$bool) :?>
                                    <input type="text" name="feature_<?php echo $feature->id;?>_value" value="">
                                <?php endif;?>
                                <input type="hidden" name="feature_<?php echo $feature->id;?>_plansfeatureid" value="">
                                <input name="feature[]" type="hidden" value="">
                            </span>
                            </li>
                        <?php endforeach; ?>
                    <?php endif;?>
                </ul>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <table class="responsive-table">
                    <thead class="table_head">
                        <tr>
                            <th><?= lang('period') ?></th>
                            <th></th>
                            <th><?= lang('price') ?></th>
                        </tr>
                    </thead>
                    <tbody>

                    <?php $row = 1; ?>
                    <?php if(isset($recent)) :?>
                    <?php foreach ($recent['period'] as $k => $v) :?>
                    <tr class="period-row">
                        <td data-th="<?= lang('period') ?>">
                            <input name="period_id[]" value="<?php echo $recent['period_id'][$k];?>" type="hidden">
                            <input name="period[]" value="<?php echo $v; ?>" placeholder="<?= lang('period') ?>">
                        </td>
                        <td data-th="">
                            <select name="qualifier[]">
                                <?php foreach($options as $key => $value) :?>
                                <option value="<?php echo $key;?>" <?php echo ($key == $recent['qualifier'][$k]) ? 'selected' : '' ; ?>><?php echo $value;?></option>
                                <?php endforeach;?>
                            </select>
                        </td>
                        <td data-th="<?= lang('price') ?>">
                            <input name="price[]" value="<?php echo $recent['price'][$k]; ?>" placeholder="<?= lang('price') ?>">
                            &nbsp;&nbsp;
                            <input  type="button" class="period-remove" style="<?php echo ($row > 1) ? '' : 'display:none';?>" value="X">
                        </td>
                    </tr>
                    <?php $row++; ?>
                    <?php endforeach;?>
                    <?php else :?>
                    <?php if ($plansPeriod->exists()) :?>
                    <?php foreach ($plansPeriod as $period) :?>
                    <tr class="period-row">
                        <td  data-th="<?= lang('period') ?>">
                            <input name="period_id[]" value="<?php echo $period->id; ?>" type="hidden">
                            <input name="period[]" value="<?php echo $period->period; ?>" placeholder="period">
                        </td>
                        <td data-th="">
                            <select name="qualifier[]">
                                <?php foreach($options as $key => $value) :?>
                                <option value="<?php echo $key;?>" <?php echo ($key == $period->qualifer) ? 'selected' : '' ; ?>><?php echo $value;?></option>
                                <?php endforeach;?>
                            </select>
                        </td>
                        <td data-th="<?= lang('price') ?>">
                            <input name="price[]" value="<?php echo $period->viewPrice(); ?>" placeholder="price">
                            &nbsp;&nbsp;
                            <input type="button" class="period-remove" style="<?php echo ($row > 1) ? '' : 'display:none';?>" value="X">
                        </td>
                    </tr>
                    <?php $row++; ?>
                    <?php endforeach;?>
                    <?php else :?>
                    <tr class="period-row">
                        <td data-th="<?= lang('period') ?>">
                            <input name="period_id[]" value="" type="hidden">
                            <input name="period[]" value="" placeholder="period">
                        </td>
                        <td data-th="">
                            <select name="qualifier[]">
                                <?php foreach($options as $key => $value) :?>
                                <option value="<?php echo $key;?>" ><?php echo $value;?></option>
                                <?php endforeach;?>
                            </select>
                        </td>
                        <td data-th="<?= lang('price') ?>">
                            <input name="price[]" value="" placeholder="price">
                            &nbsp;&nbsp;
                            <input type="button" class="period-remove" style="display:none" value="X">
                        </td>
                    </tr>
                    <?php $row++; ?>
                    <?php endif;?>
                    <?php endif;?>
                </tbody>
                </table>
                <div class="add-period">
                    <a><?= lang('add_period') ?></a>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12 m-t10">
                <input type="submit" class="btn btn-save" value="<?= lang('save') ?>" />
            </div>
        </div>
    </form>
</div>