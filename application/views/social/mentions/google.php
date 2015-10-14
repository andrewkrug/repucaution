<div id="ajax-area" class="main-list">


    <?php foreach($mentions as $mention): ?>
        <div class="mentions-block clearfix">
            <div class="photoBox">
                <img width="80px" height="80px" src="<?php echo $mention->creator_image_url?>" class="no-avatar" style="height: 48px">
            </div>
            <div class="data pull-right">
                <div class="info-line clearfix">
                    <div class="author"><?php echo $mention->creator_name; ?></div>
                    <div class="pull-right date-time">
                        <?php echo date(lang('mentions_format'), $mention->created_at);?>
                    </div>
                </div>
                <p>
                    <?php 
					if($mention->other_field('content')){
						echo $mention->other_field('content');
					}else{
						echo $mention->message;
					}
					?>
                </p>
                <div class="clear"></div>
                <?php if (in_array($mention->other_field('type'), array('photo', 'album', 'video', 'article'))): ?>
                    <div class="clear" style="margin-top: 10px"></div>
                    <?php if($picture = $mention->other_field('picture')): ?>
                        <p>
                            <a target="_blank" href="<?php echo $mention->other_field('link', '#');?>">
                                <img class="picture" src="<?php echo $picture;?>"/>
                            </a>
                        </p>
                    <?php else: ?>
                        <div>
                            <?php if($link = $mention->other_field('link')): ?>
                            <p>
                                <a href="<?php echo $link;?>" target="_blank">
                                    <?php echo $link;?>
                                </a>
                            </p>
                            <?php endif;?>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
                <div class="clear"></div>
                <div class="action clearfix social-actions">
				<!-- Place this tag where you want the +1 button to render. 
<div class="g-plusone" data-size="small" recommendations="false" data-width="300" data-href="<?php //echo $mention->other_field('url');?>"></div>

 Place this tag after the last +1 button tag. -->
<g:plusone  size="medium" href="<?php echo $mention->other_field('url');?>" style="padding:5px"></g:plusone>
                   
                    
                    <div class="comment">
                        <?php echo $mention->other_field('comments', 0); ?>
                    </div>
                    <?php $comments_url = site_url('social/activity/google_get_comments/' 
                            . $mention->original_id); 
                    ?>
                    <a href="javascript: void(0)" class="show-comments" data-type="not_loaded" 
                        data-url="<?php echo $comments_url ?>"
                    >
                        Comments
                    </a>
                </div>
                <?php  echo $this->template->block(
                        '_comments', 
                        'social/activity/blocks/_google_comments', 
                        array(
                            '_post' => array('id' => $mention->original_id),
                            
                        )
                    ); 
                ?>
            </div>
            
        </div>            
    <?php endforeach; ?>
</div>
<script type="text/javascript">
  (function() {
    var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
    po.src = 'https://apis.google.com/js/platform.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
  })();
</script>
