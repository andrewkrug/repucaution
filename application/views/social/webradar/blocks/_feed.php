<?php
/**
 * @var Mention $mention
 */
$social = $mention->social;
$profileUrl = $mention->profileUrl;
if($social == 'twitter' || $social == 'instagram') {
    $profileUrl.=$mention->creator_name;
} else {
    $profileUrl.=$mention->creator_id;
}
?>
<style>
    .highlighted {
        background-color: #ffff00;
    }
</style>
<div class="web_radar_content dTable">
    <div class="dRow">
        <div class="dCell cellImg">
            <a href="<?php echo $profileUrl;?>" target="_blank">
                <img class="web_radar_picture author" src="<?php echo $mention->creator_image_url;?>" alt="<?php echo $mention->creator_name; ?>">
            </a>
        </div>
        <div class="dCell">
            <p class="web_radar_date"><?php echo $mention->created_at;?></p>
            <?php echo $content;?>
        </div>
    </div>
</div>
