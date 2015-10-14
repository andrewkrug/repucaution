<?php $feeds_counter = 0;?>
<?php foreach( $rss_feed as $_rss ): ?>
    <?php $feeds_counter++;?>

    <div class="feed_block">
        <div class="clearfix">
            <label class="cb-radio regRoboto w-100">

                <input type="radio" value="1" name="rss_feed_item">

                <?php echo $_rss->get_title();?>
            </label>
            <?php if ($enclosure = $_rss->get_enclosure()): ?>
                <img class="feed_picture" src="<?php echo $enclosure->get_thumbnail(); ?>"/>
            <?php endif; ?>
            <a href="<?php echo $_rss->get_link();?>" class="link">
                <?php $link = $_rss->get_link(); ?>
                <?php echo strlen($link) > 50 ? substr($link, 0, 50) . '...' : $link; ?>
            </a>
            <p class="m-t10">
                <?php echo trim(strip_tags($_rss->get_description()));?>
            </p>
            <?php if($author = $_rss->get_author()): ?>
                <a href="<?php echo $author->get_link();?>" class="link"><?php echo $author->get_name();?></a>
            <?php endif; ?>
        </div>

    </div>
<?php endforeach; ?>
<?php if($feeds_counter == 0): ?>
    <?php echo $this->template->block('_alert', 'blocks/alert', array('alert' => array('error' => 'No data'))); ?>
<?php endif; ?>
    
