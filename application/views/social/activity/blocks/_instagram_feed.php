<?php foreach($data as $_image): ?>
    <div class="mentions-block clearfix">
        <?php  $time = $_image->created_at; ?>
		<?php $likes = $_image->likes;?>
		<?php $comment = $_image->message;?>
		<?php $other = json_decode($_image->other_fields);?>
		<?php $thumbnail = $other->thumbnail;?>
        <div class="instagram_image">
            <img width="150px" height="150px" src="<?php echo $thumbnail;?>" />
			
        </div>
        <span >
            <ul>
				
				<li>Created: <?php echo date("Y-m-d H:i:s", $time);?></li>
				<li>Likes: <?php echo $likes;?></li>
			</ul>
			<span><?php echo $comment;?></span>
        </span>
    </div>
<?php endforeach; ?>