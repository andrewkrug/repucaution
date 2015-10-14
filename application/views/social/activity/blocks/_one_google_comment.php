<?php $picture =  $_comment['actor']['image']['url'] ; ?>
<li>
    <div class="photo-comments">
        <img src="<?php echo $picture; ?>" alt="">
    </div>
    <div class="exposition">
        <div class="caption"><?php echo $_comment['actor']['displayName']; ?></div>
        <p>
            <?php echo $_comment['object']['content']; ?>
        </p>
        <div class="comment-panel">
            <div class="scoring">

                <div class="like inactive"><?php echo isset($_comment['plusoners']['totalItems']) ?  $_comment['plusoners']['totalItems'] : 0; ?></div>
                
                <div class="time">
                    <?php echo $socializer->convert_google_time( $_comment['published'] ); ?>
                </div>
            </div>
            
        </div><!-- /comment-panel-->
    </div>
</li>