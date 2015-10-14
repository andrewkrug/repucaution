<div class="contentBlock-section" id="block_3">
    <fieldset  class="formBox-3">
        <div class="row-fluid">
            <div class="span4">
                <ul class="menuCategory">
                    <?php foreach($rss as $_rss): ?>
                        <li class="custom-rss-link-parent">
                            <a href="javascript: void(0)" class="custom-rss-link" data-id="<?php echo $_rss->id; ?>" data-url="<?php echo $_rss->link; ?>">
                                <?php echo $_rss->title; ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="span8" id="rss-feed-container"></div>
        </div>
    </fieldset>
    <fieldset class="buttBox"><input class="black-btn" id="post-custom-rss-link" style="margin-bottom: 20px;" type="submit" value="Post Link"></fieldset>
</div>