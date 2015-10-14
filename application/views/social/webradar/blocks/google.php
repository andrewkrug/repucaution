<!--<p class="ment-text">
    <?php /*
    if($mention->other_field('content')){
        echo $mention->other_field('content');
    }else{
        echo $mention->message;
    }
    */?>
</p>
<div class="clear"></div>
<?php /*if (in_array($mention->other_field('type'), array('photo', 'album', 'video', 'article'))): */?>
<div class="clear" style="margin-top: 10px"></div>
<?php /*if($picture = $mention->other_field('picture')): */?>
    <p>
        <a target="_blank" href="<?php /*echo $mention->other_field('link', '#');*/?>">
            <img class="picture" src="<?php /*echo $picture;*/?>"/>
        </a>
    </p>
<?php /*else: */?>
    <div>
        <?php /*if($link = $mention->other_field('link')): */?>
        <p>
            <a href="<?php /*echo $link;*/?>" target="_blank">
                <?php /*echo $link;*/?>
            </a>
        </p>
        <?php /*endif;*/?>
    </div>
<?php /*endif; */?>
<?php /*endif; */?>
<div class="clear"></div>
<?php /*if ($mention->actions) :*/?>
<div class="action clearfix social-actions radar">
    <a href="https://plus.google.com/share?url=<?php /*echo $mention->other_field('url');*/?>" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;">
      +1
    </a>
    <?php /*if (!$mention->influencer) :*/?>
        <a href="javascript: void(0)" class="add-influencer"
           data-creator_id="<?php /*echo $mention->creator_id; */?>"
           data-social="<?php /*echo $mention->social; */?>"
           style="display:none"
            >
            Add to influencers
        </a>
    <?php /*endif;*/?>
</div>
--><?php /*endif;*/?>
<i class="fa fa-google-plus-square i-google"></i>
    <?php if($picture = $mention->other_field('picture')): ?>
    <a target="_blank" href="<?php echo $mention->other_field('link', '#');?>">
        <img src="<?php echo $picture;?>" class="web_radar_image" alt="">
    </a>
    <?php else: ?>
        <?php if($link = $mention->other_field('link')): ?>

            <a href="<?php echo $link;?>" target="_blank">
                <?php echo $link;?>
            </a>

        <?php endif;?>
    <?php endif;?>
<p class="web_radar_text">
    <?php
        if($mention->other_field('content')){
            echo $mention->other_field('content');
        }else{
            echo $mention->message;
        }
    ?>
</p>
<?php if ($mention->actions) :?>

    <a href="https://plus.google.com/share?url=<?php echo $mention->other_field('url');?>" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;">
        +1
    </a>
    <?php if (!$mention->influencer) :?>
    <a href="javascript: void(0)" class="add-influencer"
       data-creator_id="<?php echo $mention->creator_id; ?>"
       data-social="<?php echo $mention->social; ?>"
       style="display:none"
        >
        <?= lang('add_to_influencers') ?>
    </a>
    <?php endif;?>

<?php endif;?>