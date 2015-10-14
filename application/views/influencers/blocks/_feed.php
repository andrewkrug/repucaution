<?php /*$social = $mention->social;*/?><!--
<div class="mentions-block clearfix <?php /*echo $social;*/?>">
    <div class="photoBox">
        <img width="80px" height="80px" src="<?php /*echo $mention->creator_image_url;*/?>" class="no-avatar" style="height: 48px">
    </div>
    <div class="data pull-right">
        <div class="info-line clearfix">
            <?php
/*            $profileUrl = ($social == 'instagram') ? $mention->profileUrl.$mention->creator_name : $mention->profileUrl.$mention->creator_id;
            */?>
            <div class="author"><a href="<?php /*echo $profileUrl;*/?>"><?php /*echo $mention->creator_name; */?></a></div>
                <div class="pull-right date-time">
                    <?php /*echo $mention->created_at;*/?>
                </div>
        </div>
        <?php /*echo $content;*/?>

   </div>
</div>-->
<?php $social = $mention->social;?>

<div class="web_radar_content dTable">
    <div class="dRow">
        <div class="dCell cellImg">
            <a href="<?php echo $mention->profileUrl.$mention->creator_id;?>">
                <img class="web_radar_picture author" src="<?php echo $mention->creator_image_url;?>" alt="<?php echo $mention->creator_name; ?>">
            </a>
        </div>
        <div class="dCell">
            <p class="web_radar_date"><?php echo $mention->created_at;?></p>
            <?php echo $content;?>
        </div>
    </div>
</div>