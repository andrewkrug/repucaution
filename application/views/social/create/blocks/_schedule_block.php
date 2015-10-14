<?php
/**
 * @var bool $is_user_set_timezone
 */
$schedule_style = isset($social_post) ? $social_post->schedule_date == null ? 'is-hidden' : '' : 'is-hidden';
?>

<div class="row m-t10 <?php echo $schedule_style; ?>" id="schedule-settings">
    <?php if($is_user_set_timezone): ?>
        <div class="col-sm-12">
            <div class="form-group date_calendar">
                <?php
                    $tomorrow = new DateTime('tomorrow 11:00 am');
                ?>
                <input type="text" class="form-control time_date" value="<?php echo $tomorrow->format(lang('date_time_format')); ?>" name="schedule_date">
            </div>
        </div>
    <?php else:?>
        <div class="message-error alert-error">
            <div class="message"> <i class="icon"></i> <span><?= lang('select_timezone_error', [site_url('settings/socialmedia/')]); ?></span></div>
        </div>
    <?php endif;?>
</div>
