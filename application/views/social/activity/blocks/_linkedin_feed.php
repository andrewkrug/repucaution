<?php foreach( $updates as $_post ): ?><?php
$creator_image = (isset($_post->creator_image_url)) ? $_post->creator_image_url : '';
$key = $_post->original_id;
?>
    <div class="web_radar_content dTable">
        <div class="dRow">
            <div class="dCell cellImg">
                <a href="">
                    <img class="web_radar_picture" src="<?php echo $creator_image?>" alt="">
                </a>
            </div>
            <div class="dCell">
                <p class="web_radar_date">
                    <?php  $time = $_post->created_at ; ?>
                    <?php echo $radar->formatRadarDate($time);?>
                </p>
                <i class="fa fa-linkedin-square i-linkedin"></i>
                <?php if( $author = $_post->other_field('author')): ?>
                    <div class="author">
                        <a href="<?php echo $_post->other_field('author_profile');?>">
                            <?php echo $author;?>
                        </a>
                    </div>
                <?php endif; ?>
                <?php if (in_array(
                    $_post->other_field('type'),
                    array(
                        'CONN',
                        'SHAR',
                        'MSFC',
                        'JGRP',
                        'PICU',
                        'PFOL',
                        'PROF',
                        'PREC'
                    )
                )): ?>
                    <div class="clear"></div>

                    <?php if($photo = $_post->other_field('image')): ?>
                        <img class="web_radar_image" src="<?php echo $photo;?>"/>
                    <?php else: ?>
                        <div>
                            <?php if($link = $_post->other_field('link')): ?>
                                <p>
                                    <a target="_blank" href="<?php echo $link;?>" target="_blank">
                                        <?php echo $link;?>
                                    </a>
                                </p>
                            <?php endif;?>
                        </div>
                    <?php endif; ?>
                    <?php if($follow = $_post->other_field('follow')): ?>
                        <div class="fllw">
                            <div class="fllw_img">
                                <img class="web_radar_image" src="<?php echo $follow['picture'];?>"/>
                            </div>
                            <div class = "fllw_props">
                                <a target="_blank" href="<?php echo $follow['profile']?>"><?php echo $follow['name']?></a>
                                <p><?php echo $follow['headline']?></p>
                            </div>
                        </div>
                    <?php endif; ?>
                    <?php if($share = $_post->other_field('share')): ?>
                        <div class="shr">
                            <div class="shr_img">
                                <img class="web_radar_image" src="<?php echo $share['thumbnail'];?>"/>
                            </div>
                            <div class = "shr_props">
                                <a target="_blank" href="<?php echo $share['url']?>"><?php echo $share['title']?></a>
                                <p><?php echo $share['description']?></p>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
                <div class="clear"></div>
                <p class="web_radar_text">
                    <?php if( isset($_post->message) && !$_post->other_field('link') && !($_post->message == 'blank')): ?>
                        <?php echo $_post->message; ?>
                    <?php endif; ?>
                </p>
                <div class="action clearfix m-t10 social-actions radar">
                    <?php if( $_post->other_field('likable')=='true'):?>

                        <?php   $is_liked = $_post->other_field('liked');
                        $like_url = site_url('/social/activity/linkedin_');
                        $action = $is_liked =='true'
                            ? 'unlike'
                            : 'like';

                        ?>
                        <a href="javascript: void(0)" class="like-button"
                           data-url="<?php echo $like_url; ?>"
                           data-id="<?php echo $key; ?>"
                           data-action="<?php echo $action; ?>"
                            >
                            <?php echo ($is_liked) ? 'Unlike' : 'Like';?>
                        </a>
                        <?php if($_post->other_field('comment')=='true'):?>

                        <?php endif;?>
                        <?php $comments_url = site_url('social/activity/linkedin_get_comments?key='
                            . $key);
                        ?>
                        <a href="javascript: void(0)" class="show-comments" data-type="not_loaded"
                           data-url="<?php echo $comments_url ?>"
                            >
                            Comments
                        </a>
                        <?php echo $this->template->block('_comments', 'social/activity/blocks/_linkedin_comments', array('_post' => $_post)); ?>
                    <?php endif;?>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; ?>

