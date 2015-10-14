<div class="row-fluid">
    <div class="span12 box">
        <div class="header span12">
            <span><?= lang('all_reviews') ?></span>
        </div>
        <div class="row-fluid">
            <div class="body span12 clearfix">
                <div class="reiews-block" style="padding: 10px;">
                   <a class="reviews-dir-site" href="<?php echo site_url('reviews/'.$directory_id); ?>">
                        &lt;&lt; <?= lang('back') ?>
                    </a> 
                </div>
                <div>
                    <?php foreach($reviews as $_review):?>
                        <div class="reiews-block">
                            <div class="reviews-inner">
                                <div class="clearfix"></div>
                                <div class="author pull-left"><?php echo $_review->author;?></div> 
                                <div class="date pull-right">
                                    <?php echo date(Review::POSTEDFORMAT, $_review->posted);?>
                                </div> 
                                <div class="clear"></div>
                                
								<?php if($type == 'Foursquare'):?>
										<div  style="clear:left"></div>
									<?php else:?>
										<div class="rating-box reviews-list-star clearfix" 
                                    data-rank="<?php echo $_review->rank;?>">
                                </div>
                                    <?php endif;?>
                                <div class="clear"></div>
                                <p><?php echo $_review->text;?></p>
                            </div>
                        </div>
                    <?php endforeach;?>
                </div>
                <div class="reiews-block" style="padding: 10px; text-align: center; min-height: 20px;">   
                    <?php if($reviews->paged->has_previous):?>
                        <a class="reviews-dir-site" 
                            href="<?php echo current_url();?>?page=<?php echo $reviews->paged->previous_page;?>" 
                            style="float: left"
                        >
                            &lt;&lt; <?= lang('previous') ?>
                        </a>
                    <?php endif;?>
                    <span>
                        <?php echo $reviews->paged->current_page;?>
                        /
                        <?php echo $reviews->paged->total_pages;?>
                    </span>
                    <?php if($reviews->paged->has_next):?>
                    <a class="pull-right reviews-dir-site"
                        href="<?php echo current_url();?>?page=<?php echo $reviews->paged->next_page;?>"
                    >
                        <?= lang('next') ?> &gt;&gt;
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>