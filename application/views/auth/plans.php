<?php
/**
 * @var Plan[] $plans
 */
?>

    <div class="container">
        <div class="row">
            <?php $i=1;?>
            <?php $f=2;?>
            <?php foreach ($plans as $plan): ?>
                <div class="col-md-4">
                    <?php
                        if($f == $i){
                            $add = ' featured';
                            $f+=3;

                        } else {
                            $add = '';
                        };
                    ?>
                    <div class="tarif_plan <?php echo $add;?>">
                        <h4 class="plan-name"><?php echo $plan->name; ?></h4>
                        <div class="plan-price">
                            <ul class="plan-price_list">
                                <?php $periods = $plan->getPeriods();?>
                                <?php foreach ($periods as $period) :?>
                                    <?php $price = explode('.', $period->viewPrice());?>
                                    <li class="plan-price_list_item">
                                        <h4 class="plan-price_title">
                                            $<?php echo $price[0];?>  <span class="price-cents"><?php echo $price[1];?></span><span class="price-month"> / <?php if($period->period > 1) echo $period->period;?><?php echo strtolower($options[$period->qualifier]);?></span>
                                        </h4>
                                    </li>
                                <?php endforeach;?>
                            </ul>
                        </div>
                        <ul class="tarif_plan_list">
                            <?php $planFeatures = $plan->getAttachedFeatures()->all_to_single_array('feature_id');?>

                            <?php foreach ($features as $_feature): ?>
                            <?php $inPlan = (in_array($_feature->id, $planFeatures)) ? 'fa-plus green-color' : 'fa-minus attention';?>


                                <li class="tarif_plan_list_item">
                                <span>
                                    <i class="fa <?php echo $inPlan;?>"></i>
                                    <?php echo $_feature->name; ?>
                                </span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                        <div class="row">
                            <div class="col-xs-12 text-center p-tb20">
                                <a href="<?php echo site_url('auth/register/' . $plan->id); ?>" class="btn btn-save">Subscribe</a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php $i++;?>
            <?php endforeach;?>
        </div>
    </div>
