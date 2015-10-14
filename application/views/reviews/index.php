<div class="p-rl30 p-tb20">
    <div class="row">
        <div class="col-xs-12">
            <h1 class="head_title"><?= lang('reviews') ?></h1>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <?php echo $this->template->block('app_breadcrumbs', 'layouts/block/application/breadcrumbs', array('menu' => 'customer.main')); ?>
        </div>
    </div>
</div>
<div class="main_block">
<div class="row">
    <div class="col-sm-5">
        <p class="strong-size text_color p-t10"><?= lang('all_reviews') ?></p>
    </div>
    <div class="col-sm-3">
        <div class="form-group date_calendar">
            <input type="text" class="form-control input_date" id="date_from">
        </div>
    </div>
    <div class="col-sm-3">
        <div class="form-group date_calendar">
            <input type="text" class="form-control input_date" id="date_to">
        </div>
    </div>
    <div class="col-sm-1">
        <div class="pull-sm-right">
            <button class="btn btn-save m-b20" id="apply_button"><?= lang('apply') ?></button>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xs-12">
        <div class="pull_border">
            <div class="row">
                <div class="col-sm-3">
                    <div class="review review_total">
                        <p class="review_text"><span><?php echo $reviewsTotal; ?></span> <?= lang('total_reviews') ?></p>
                    </div>
                </div>
                <?php if($type == 'Foursquare'):?>

                    <div class="col-sm-3">
                        <div class="review review_total">
                            <p class="review_text"><span><?php echo $visitors;?></span> <?= lang('visitors') ?></p>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="review review_positive">
                            <p class="review_text"><span><?php echo $checkins;?></span> <?= lang('checkins') ?></p>
                        </div>
                    </div>
                <?php else:?>
                    <div class="col-sm-3">
                        <div class="review review_positive">
                            <p class="review_text"><span><?php echo $rank['positive'];?></span> <?= lang('positive') ?></p>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="review review_negative">
                            <p class="review_text"><span><?php echo $rank['negative'];?></span> <?= lang('negative') ?></p>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="review review_neutral">
                            <p class="review_text"><span><?php echo $rank['neutral'];?></span> <?= lang('neutral') ?></p>
                        </div>
                    </div>
                <?php endif;?>

            </div>
        </div>
    </div>
</div>

<div class="row m-t20">
    <div class="col-xs-12">
        <div class="block_content">
            <div class="block_content_body text-center">
                <div class="row">
                    <div class="col-sm-10 col-sm-offset-1">
                        <div class="row">
                            <div class="col-xs-12">
                                <p class="text_color large-size m-b20"><?= lang('overall_sentiment') ?>: <span class="bold black strong-size"><?php echo $rate.' / '.$stars;?></span></p>
                                <div class="rating" data-value="<?php echo $rate;?>">
                                    <div class="rating_bar"></div>
                                </div>
                            </div>
                        </div>
                        <div class="row rating_block">
                            <?php $scaleLimit = 5;?>
                            <?php $blockLens = array(1, 3, 2, 2, 3, 1);?>
                            <?php for($i=0; $i<=$scaleLimit; $i++):?>
                                <div class="col-xs-<?php echo $blockLens[$i];?>">
                                    <p class="text-left rating_text"><?php echo ($type == 'Foursquare') ? 2*$i : $i;?></p>
                                </div>
                            <?php endfor;?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row m-t20">
    <div class="col-xs-12" id="review-list">
        <?php foreach ($reviews as $review):?>
            <?php echo $this->template->block('review', 'reviews/blocks/review', array('review' => $review));?>
        <?php endforeach;?>
        <div class="col-xs-12 text-center hidden p-tb10" id="loading">
            <img src="<?php echo site_url('/public/theme/images/loading/loading.gif');?>" class="m-tb20" alt="">
        </div>
    </div>
</div>

</div>