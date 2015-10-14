<?php $picture = $socializer->get_profile_picture( $_comment['from']['id'] ); ?>
<div class="dRow">
    <div class="dCell cellImg">
        <a href="">
            <img class="comment_avatar" src="<?php echo $picture; ?>" alt="">
        </a>
    </div>
    <div class="dCell">
        <a class="blue_color comment_title" href="<?php echo $radar->getProfileUrl('facebook').$_comment['from']['id'];?>" target="_blank">
            <?php echo $_comment['from']['name']?>
        </a>
        <p class="comment_text">
            <?php echo $_comment['message']; ?>
        </p>
        <p class="comment_date">
            <cite class="gray-color fa fa-clock-o "></cite><?php echo $socializer->convert_facebook_time( $_comment['created_time'] );?>
        </p>
    </div>
</div>