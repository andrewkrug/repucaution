<div class="row">
    <div class="col-xs-12 m-t5 custom-form">
        <label class="cb-checkbox regRoboto m-r10" data-toggle="#cron">
            <input type="checkbox" id="need_cron" name="is_cron">
            <?= lang('make_it_a_recurring_post') ?>
        </label>
    </div>
</div>
<div class="row is-hidden" id="cron">
    <div class="col-xs-12">
        <div class="row">
            <div class="col-xs-12">
                <div class="btn-group" data-toggle="buttons">
                    <label class="btn btn-primary">
                        <input name="cron_day[]"
                            value="Monday"
                            type="checkbox"
                            autocomplete="off"> <?= lang('monday') ?>
                    </label>
                    <label class="btn btn-primary">
                        <input name="cron_day[]"
                            value="Tuesday"
                            type="checkbox"
                            autocomplete="off"> <?= lang('tuesday') ?>
                    </label>
                    <label class="btn btn-primary">
                        <input name="cron_day[]"
                            value="Wednesday"
                            type="checkbox"
                            autocomplete="off"> <?= lang('wednesday') ?>
                    </label>
                    <label class="btn btn-primary">
                        <input name="cron_day[]"
                            value="Thursday"
                            type="checkbox"
                            autocomplete="off"> <?= lang('thursday') ?>
                    </label>
                    <label class="btn btn-primary">
                        <input name="cron_day[]"
                            value="Friday"
                            type="checkbox"
                            autocomplete="off"> <?= lang('friday') ?>
                    </label>
                    <label class="btn btn-primary">
                        <input name="cron_day[]"
                            value="Saturday"
                            type="checkbox"
                            autocomplete="off"> <?= lang('saturday') ?>
                    </label>
                    <label class="btn btn-primary">
                        <input name="cron_day[]"
                            value="Sunday"
                            type="checkbox"
                            autocomplete="off"> <?= lang('sunday') ?>
                    </label>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <p id="cron_text"></p>
                <div id="cron-scheduled-block">
                </div>
                <div class="row">
                    <div class="col-xs-12">
                        <div class="pull-sm-left">
                            <a class="btn btn-add m-tb20 m-r20" id="cron-time-add-btn"><?= lang('add_time') ?></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script id="cron-time-template" type="text/x-handlebars-template">
    <div class="cron-time">
        <div class="form-group date_calendar">
            <input type="text" class="form-control time m-r20" value="<?= lang('default_time') ?>" name="cron_schedule_time[]">
            <i class="cb-remove cron-time-delete" style="right: 40px;"></i>
        </div>
    </div>
</script>