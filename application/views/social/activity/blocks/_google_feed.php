<div class="row">
    <div class="col-xs-12">
        <div class="web_radar m-t20 pull_border">
            <?php foreach( $activities as $activity ): ?>
            <div class="web_radar_content dTable">
                <div class="dRow">
                    <div class="dCell cellImg">
                        <a href="<?php echo $radar->getProfileUrl('google').$activity['actor']['id']; ?>">
                            <img class="web_radar_picture" src="<?php echo $activity['actor']['image']['url'];?>" alt="">
                        </a>
                    </div>
                    <div class="dCell">
                        <p class="web_radar_date">
                            <?php $dateObject = $activity['published'];?>
                            <?php echo $radar->formatRadarDate($dateObject->getTimestamp());?>
                        </p>
                        <i class="fa fa-google-plus-square i-google"></i>
                        <p class="web_radar_text">
                            <?php if( isset($activity['object']['content']) ):?>
                                <?php echo $activity['object']['content']; ?>
                            <?php endif; ?>
                        </p>
                        <?php if (!empty($activity['object']['attachments'])): ?>
                            <div class="clear" style="margin-top: 10px"></div>
                            <?php foreach ($activity['object']['attachments'] as $attachment): ?>
                                <?php echo $this->template->block(
                                    'google_attachment_'.$attachment['objectType'],
                                    'social/activity/blocks/google_attachments/'.$attachment['objectType'],
                                    array(
                                        'attachment' => $attachment
                                    )
                                ); ?>
                            <?php endforeach;?>
                        <?php endif;?>
                        <a href="https://plus.google.com/share?url=<?php echo $activity['object']['plusoners']['selfLink'];?>" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;">
                            +1
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
