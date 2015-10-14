<?php
/**
 * @var bool $isSupportScheduledPosts
 */
?>
<div class="row">
    <div class="col-xs-12 m-t5">
        <p class="text_color strong-size"><?= lang('when_to_post') ?></p>
    </div>
</div>
<div class="row custom-form">
    <div class="col-xs-12">
        <?php if($isSupportScheduledPosts):?>
            <label class="cb-checkbox regRoboto" data-toggle="#schedule-settings">
                <input type="checkbox" id="schedule-type" name="posting_type" value="schedule" <?php echo isset($social_post) ? $social_post->schedule_date == null ? '' : 'checked' : '';?>>
                <?= lang('schedule_post') ?>
            </label>
        <?php endif;?>
    </div>
</div>