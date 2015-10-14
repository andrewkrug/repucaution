<p style="color: #727070; font: normal 12px/1.5 Helvetica, arial, sans-serif;" >
    <?php foreach($reviews as $review) :?>
        <br/><b><?php echo $review->review->directory->get()->name;?>:</b><?php echo $review->review->text;?> <br/>
    <?php endforeach;?>
</p>
<a style="display: inline-block; background-color: #4069B0; background: linear-gradient(#416AB2, #395F9D); padding: 9px 11px; color: #fff;  text-align: center; text-decoration: none; font: bold 12px/100% Helvetica, arial, sans-serif;" href="<?php echo site_url('reviews')?>">View Reviews</a>


