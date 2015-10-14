<p>
    <?php foreach ($attachment['thumbnails'] as $thumbnail): ?>
        <a target="_blank" href="<?php echo $thumbnail['url'];?>">
            <img class="picture" src="<?php echo $thumbnail['image']['url'];?>"/>
        </a>
    <?php endforeach; ?>
</p>