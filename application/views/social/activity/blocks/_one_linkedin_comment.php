<li>
    <div class="photo-comments">
        <img src="<?php echo $comment['author']['picture']; ?>" alt="">
    </div>
    <div class="ln_comment_body">
        <a class="author" href="<?php echo $comment['author']['profile'];?>">
        <?php echo $comment['author']['name']; ?></a>
        <span>
            <?php echo $comment['comment']; ?>
        </span>
       
         <p class="date">
            <?php echo date('M d, Y h:i a', substr($comment['created'], 0, strlen($comment['created'])-3)); ?>
        </p>
    </div>
</li>