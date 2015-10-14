<?php $emptyImg = !isset($attachment['image']['url']); ?>
<p>
    <a target="_blank" href="<?php echo $attachment['url'];?>">
        <?php if ($emptyImg): ?>
            <?php echo $attachment['displayName'];?>
        <?php else: ?>
            <img class="picture" src="<?php echo $attachment['image']['url'];?>"/>
        <?php endif; ?>
    </a>
    <?php if (!$emptyImg): ?>
        <br/>
        <?php echo $attachment['displayName'];?>
    <?php endif; ?>
</p>