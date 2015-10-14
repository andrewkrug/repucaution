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
    <a href="javascript: void(0)" class="remove-influencer"
       data-creator_id="<?php echo $mention->creator_id; ?>"
       data-social="<?php echo $mention->social; ?>"
       style="display:none"
        >
        Delete from influencers
    </a>
<?php endif;?>
