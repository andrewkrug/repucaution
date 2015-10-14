<?php if ($review) :?>

    <div class="block_content">
        <div class="block_content_body">
            <div class="recent_review">
                <div class="clearfix">
                    <p class="recent_review_title pull-sm-left"><?php echo $review->author;?></p>
                    <p class="recent_review_date pull-sm-right"><?php echo $review->posted_date;?></p>
                </div>
                <?php if($type == 'Foursquare'):?>
                    <div  style="clear:left"></div>
                <?php else:?>
                    <?php for ($i=1; $i<=5; $i++):?>
                        <i class="icon-star<?php if($i<=$review->rank):?> active-rating<?php endif;?>"></i>
                    <?php endfor;?>
                <?php endif;?>
                <p class="recent_review_text">
                    <?php echo $review->text;?>
                </p>
            </div>
        </div>
    </div>
<?php endif; ?>